import sys
import numpy as np
import soundfile as sf
import openl3
import joblib

MODEL_PATH = "instruments_model.joblib"
SCALER_PATH = "instruments_scaler.joblib"
INSTRUMENT_LIST_PATH = "instrument_list.joblib"

THRESHOLD = 0.20
TOP_N = 10

if len(sys.argv) != 2:
    print("usage: python3 instruments.py audio_file")
    sys.exit(1)

audio_path = sys.argv[1]

print("Loading audio...")
audio, sr = sf.read(audio_path)

if audio.ndim > 1:
    audio = np.mean(audio, axis=1)

print("Extracting embedding...")

emb, _ = openl3.get_audio_embedding(
    audio,
    sr,
    content_type="music",
    embedding_size=512
)

emb = emb.mean(axis=0).reshape(1, -1)

print("Loading model...")

model = joblib.load(MODEL_PATH)
scaler = joblib.load(SCALER_PATH)
instrument_list = joblib.load(INSTRUMENT_LIST_PATH)

emb = scaler.transform(emb)
pred = model.predict(emb)[0]

pairs = list(zip(instrument_list, pred))
pairs_sorted = sorted(pairs, key=lambda x: x[1], reverse=True)

print()
print("Detected instruments (probability > %.2f):" % THRESHOLD)
print("-" * 40)

found = False
for name, prob in pairs_sorted:
    if prob >= THRESHOLD:
        print(f"{name:20s} {prob:.3f}")
        found = True

if not found:
    print("No instruments above threshold.")

print()
print(f"Top {TOP_N} instruments:")
print("-" * 40)

for name, prob in pairs_sorted[:TOP_N]:
    print(f"{name:20s} {prob:.3f}")

