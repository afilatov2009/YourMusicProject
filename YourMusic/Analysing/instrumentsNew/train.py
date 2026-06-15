import os
import json
import numpy as np
import librosa
import openl3
from tqdm import tqdm



AUDIO_DIR = "audio"
OUT_DIR = "embeddings"

EMBEDDING_SIZE = 512
CONTENT_TYPE = "music"
INPUT_REPR = "mel256"



def get_instrument_from_filename(filename: str) -> str:
    """
    NSynth format:
    instrument_source_pitch_velocity.wav

    Example:
    bass_electronic_018-064.wav
    """
    return filename.split("_")[0]


def ensure_dirs(instruments):
    os.makedirs(OUT_DIR, exist_ok=True)
    for inst in instruments:
        os.makedirs(os.path.join(OUT_DIR, inst), exist_ok=True)


def collect_instruments(audio_dir):
    instruments = set()
    for f in os.listdir(audio_dir):
        if f.lower().endswith(".wav"):
            instruments.add(get_instrument_from_filename(f))
    return sorted(list(instruments))



def main():
    if not os.path.exists(AUDIO_DIR):
        print(f"ERROR: folder '{AUDIO_DIR}' not found")
        return

    print("Scanning instruments...")
    instruments = collect_instruments(AUDIO_DIR)

    print("\nDetected instrument classes:")
    for inst in instruments:
        print(" ", inst)

    ensure_dirs(instruments)

    classes_path = os.path.join(OUT_DIR, "instrument_classes.json")
    with open(classes_path, "w") as f:
        json.dump(instruments, f, indent=2)

    print("\nLoading OpenL3 model...")
    model = openl3.models.load_audio_embedding_model(
        input_repr=INPUT_REPR,
        content_type=CONTENT_TYPE,
        embedding_size=EMBEDDING_SIZE
    )

    files = [f for f in os.listdir(AUDIO_DIR) if f.lower().endswith(".wav")]

    print(f"\nProcessing {len(files)} files...\n")

    for filename in tqdm(files):
        instrument = get_instrument_from_filename(filename)

        out_path = os.path.join(
            OUT_DIR,
            instrument,
            filename.replace(".wav", ".npz")
        )

        if os.path.exists(out_path):
            continue

        audio_path = os.path.join(AUDIO_DIR, filename)

        try:
            audio, sr = librosa.load(audio_path, sr=None, mono=True)

            emb, _ = openl3.get_audio_embedding(
                audio,
                sr,
                model=model,
                input_repr=INPUT_REPR,
                content_type=CONTENT_TYPE,
                embedding_size=EMBEDDING_SIZE
            )

            emb_mean = emb.mean(axis=0)

            np.savez_compressed(
                out_path,
                embedding=emb_mean,
                instrument=instrument,
                source_file=filename
            )

        except Exception as e:
            print(f"\nERROR processing {filename}: {e}")

    print("\nDone.")


if __name__ == "__main__":
    main()
