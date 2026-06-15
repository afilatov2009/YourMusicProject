import sys
import numpy as np
import librosa
import laion_clap



INSTRUMENT_PROMPTS = {
    "drums": [
        "drum kit playing, rhythmic percussion, kick snare hi-hat",
        "electronic drums, beat, rhythmic percussion",
        "strong drum beat in music"
    ],

    "kick drum": [
        "deep kick drum, bass drum hit, electronic kick",
        "four on the floor kick drum",
        "punchy bass drum sound"
    ],

    "snare": [
        "snare drum hit, sharp percussion sound",
        "drum snare backbeat",
        "crisp snare in rhythm"
    ],

    "hi-hats": [
        "hi-hat cymbals, closed and open hats",
        "shimmering hi-hat rhythm",
        "electronic hi-hats pattern"
    ],

    "bass": [
        "bass guitar or bass synth, low frequency sound",
        "deep bassline in electronic music",
        "sub bass presence"
    ],

    "synthesizer": [
        "synthesizer melody, electronic synth pads",
        "analog synth sound, electronic music synth",
        "synth lead or synth chords"
    ],

    "piano": [
        "acoustic piano playing melody or chords",
        "soft piano in music",
        "grand piano sound"
    ],

    "electric guitar": [
        "electric guitar playing, distorted or clean guitar",
        "rock guitar riff",
        "electric guitar melody"
    ],

    "acoustic guitar": [
        "acoustic guitar strumming",
        "fingerpicked acoustic guitar",
        "guitar acoustic warm sound"
    ],

    "vocals": [
        "human singing voice in music",
        "male or female vocals in a song",
        "lyrical vocal performance"
    ],

    "strings": [
        "string section, violins violas cellos",
        "orchestral strings playing melody",
        "cinematic string ensemble"
    ],

    "brass": [
        "trumpet trombone saxophone brass section",
        "orchestral brass instruments",
        "loud brass hits in music"
    ],

    "pads": [
        "ambient pad sounds, soft synth background",
        "atmospheric sustained chords",
        "warm background textures"
    ],

    "noise / fx": [
        "sound effects, noise textures, glitch sounds",
        "electronic noise, distortion effects",
        "ambient effects and transitions"
    ]
}



print("Loading CLAP...")
model = laion_clap.CLAP_Module(enable_fusion=False)
model.load_ckpt()



def get_audio_embedding(path):
    audio, sr = librosa.load(path, sr=48000, mono=True)

    segment_length = sr * 10
    embeddings = []

    for i in range(0, len(audio), segment_length):
        seg = audio[i:i + segment_length]

        if len(seg) < segment_length:
            continue

        seg = np.asarray(seg, dtype=np.float32)
        seg = np.expand_dims(seg, axis=0)

        emb = model.get_audio_embedding_from_data(
            x=seg,
            use_tensor=False
        )

        emb = np.array(emb).flatten()
        emb = emb / (np.linalg.norm(emb) + 1e-9)

        embeddings.append(emb)

    if len(embeddings) == 0:
        raise Exception("Audio too short")

    return np.mean(embeddings, axis=0)



def get_text_embeddings():
    texts = []
    keys = []

    for instrument, prompts in INSTRUMENT_PROMPTS.items():
        for p in prompts:
            texts.append(p)
            keys.append(instrument)

    text_embs = model.get_text_embedding(texts, use_tensor=False)
    text_embs = np.array(text_embs)

    text_embs = text_embs / (np.linalg.norm(text_embs, axis=1, keepdims=True) + 1e-9)

    return text_embs, keys



def classify(audio_emb, text_embs, keys):
    scores = {}

    for i, instrument in enumerate(keys):
        score = float(np.dot(audio_emb, text_embs[i]))

        if instrument not in scores:
            scores[instrument] = []

        scores[instrument].append(score)

    final_scores = {
        k: float(np.mean(v)) for k, v in scores.items()
    }

    best = max(final_scores, key=final_scores.get)

    return best, final_scores



def main():
    if len(sys.argv) < 2:
        print("Usage: python3 newinstruments.py <audiofile>")
        return

    path = sys.argv[1]
    print("File:", path)

    audio_emb = get_audio_embedding(path)
    text_embs, keys = get_text_embeddings()

    instrument, scores = classify(audio_emb, text_embs, keys)

    print("\nMAIN INSTRUMENT:", instrument)

    print("\nTOP INSTRUMENTS:")
    for k, v in sorted(scores.items(), key=lambda x: -x[1])[:10]:
        print(k, round(v, 3))


if __name__ == "__main__":
    main()
