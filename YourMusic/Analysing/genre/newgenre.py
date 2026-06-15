import sys
import numpy as np
import librosa
import laion_clap



GENRE_PROMPTS = {
    "ambient": "ambient atmospheric soundscape, no drums, slow evolving pads",
    "dark ambient": "dark ambient drone soundscape, eerie, minimal rhythm",
    "techno": "techno music, repetitive kick drum, club electronic dance music",
    "minimal techno": "minimal techno, sparse elements, repetitive groove",
    "house": "house music, 4/4 beat, groovy bassline, dancefloor",
    "deep house": "deep house, smooth chords, chill groove",
    "lofi": "lofi hip hop, dusty sound, vinyl crackle, relaxed beats",
    "chillout": "chillout electronic, relaxing, soft beats",
    "downtempo": "downtempo electronic music, slow groove",
    "synthwave": "synthwave, 80s retro electronic, analog synths",
    "chillsynth": "chill synthwave, dreamy pads, melodic, relaxing",
    "retrowave": "retrowave 80s inspired synth music",
    "idm": "intelligent electronic music, complex rhythms",
    "glitch": "glitch electronic, digital artifacts, broken beats",
    "drum and bass": "drum and bass, fast breakbeats, heavy bass",
    "liquid dnb": "liquid drum and bass, melodic, atmospheric",
    "dubstep": "dubstep, heavy bass drops, wobble bass",
    "future garage": "future garage, soft beats, chopped vocals",
    "trance": "trance, uplifting melodies, emotional builds",
    "progressive house": "progressive house, evolving structure",
    "electro": "electro electronic, punchy synthetic beats",
    "breakbeat": "breakbeat electronic, broken drum patterns",
    "industrial": "industrial electronic, harsh mechanical sounds",
    "noise": "noise music, chaotic, distorted sound"
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
    texts = list(GENRE_PROMPTS.values())

    text_embs = model.get_text_embedding(texts, use_tensor=False)
    text_embs = np.array(text_embs)

    text_embs = text_embs / (np.linalg.norm(text_embs, axis=1, keepdims=True) + 1e-9)

    return text_embs



def classify(audio_emb, text_embs):
    scores = {}

    for i, genre in enumerate(GENRE_PROMPTS.keys()):
        score = float(np.dot(audio_emb, text_embs[i]))
        scores[genre] = score

    best = max(scores, key=scores.get)

    return best, scores



def main():
    if len(sys.argv) < 2:
        print("Usage: python3 newgenre.py <audiofile>")
        return

    path = sys.argv[1]
    print("File:", path)

    audio_emb = get_audio_embedding(path)
    text_embs = get_text_embeddings()

    genre, scores = classify(audio_emb, text_embs)

    print("\nGENRE:", genre)

    print("\nTOP SCORES:")
    for k, v in sorted(scores.items(), key=lambda x: -x[1])[:10]:
        print(k, round(v, 3))


if __name__ == "__main__":
    main()
