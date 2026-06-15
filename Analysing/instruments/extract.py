import os
import numpy as np
import pandas as pd
import soundfile as sf
import openl3
from tqdm import tqdm

AUDIO_ROOT = "openmic-2018/audio"
CSV_PATH = "openmic-2018-aggregated-labels.csv"
OUT_DIR = "features"

os.makedirs(OUT_DIR, exist_ok=True)

df = pd.read_csv(CSV_PATH)

def find_audio(sample_key):
    subdir = sample_key[:3]
    base = os.path.join(AUDIO_ROOT, subdir)
    if not os.path.isdir(base):
        return None
    for f in os.listdir(base):
        if f.startswith(sample_key):
            return os.path.join(base, f)
    return None

for _, row in tqdm(df.iterrows(), total=len(df)):
    sample_key = row["sample_key"]
    instrument = str(row["instrument"])
    probability = float(row["relevance"])

    npz_path = os.path.join(OUT_DIR, f"{sample_key}.npz")

    if os.path.exists(npz_path):
        data = np.load(npz_path, allow_pickle=True)

        instruments = list(data["instruments"])
        probabilities = list(data["probabilities"])
        embedding = data["embedding"]

        if instrument in instruments:
            print(f"[SKIP] {sample_key}: {instrument}")
            continue

        instruments.append(instrument)
        probabilities.append(probability)

        np.savez(
            npz_path,
            embedding=embedding,
            instruments=np.array(instruments, dtype=object),
            probabilities=np.array(probabilities, dtype=np.float32),
        )

        print(f"[ADD] {sample_key}: {instrument} ({probability:.4f})")
        continue

    audio_path = find_audio(sample_key)
    if audio_path is None:
        continue

    audio, sr = sf.read(audio_path)

    embedding, _ = openl3.get_audio_embedding(
        audio,
        sr,
        content_type="music",
        embedding_size=512,
    )

    embedding = embedding.mean(axis=0)

    np.savez(
        npz_path,
        embedding=embedding,
        instruments=np.array([instrument], dtype=object),
        probabilities=np.array([probability], dtype=np.float32),
    )

    print(f"[NEW] {sample_key}: {instrument} ({probability:.4f})")
