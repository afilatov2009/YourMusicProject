import sys
import numpy as np
import librosa
import openl3
import joblib
import json


MODEL_PATH = "instrument_model.joblib"
SCALER_PATH = "instrument_scaler.joblib"
CLASSES_PATH = "embeddings/instrument_classes.json"

WINDOW_SIZE = 1.0
HOP_SIZE = 0.5



model = joblib.load(MODEL_PATH)
scaler = joblib.load(SCALER_PATH)

with open(CLASSES_PATH) as f:
    classes = json.load(f)


if len(sys.argv) != 2:
    print("usage: python3 instruments.py file.wav")
    sys.exit(1)

audio_path = sys.argv[1]



audio, sr = librosa.load(audio_path, sr=None, mono=True)

window_samples = int(WINDOW_SIZE * sr)
hop_samples = int(HOP_SIZE * sr)

segments = []

for start in range(0, len(audio) - window_samples, hop_samples):
    segment = audio[start:start + window_samples]
    segments.append(segment)

if not segments:
    print("Audio too short")
    sys.exit(1)


openl3_model = openl3.models.load_audio_embedding_model(
    input_repr="mel256",
    content_type="music",
    embedding_size=512
)


predictions = []

for segment in segments:
    emb, _ = openl3.get_audio_embedding(
        segment,
        sr,
        model=openl3_model,
        input_repr="mel256",
        content_type="music",
        embedding_size=512
    )

    emb = emb.mean(axis=0).reshape(1, -1)

    emb = scaler.transform(emb)

    probs = model.predict_proba(emb)[0]
    predictions.append(probs)


predictions = np.array(predictions)

mean_probs = predictions.mean(axis=0)


print("\nDetected instruments (probability > 0.2):")
print("----------------------------------------")

found = False

for i, p in enumerate(mean_probs):
    if p > 0.2:
        print(f"{classes[i]:<20} {p:.3f}")
        found = True

if not found:
    print("No instruments above threshold.")

print("\nTop 10 instruments:")
print("----------------------------------------")

top_idx = np.argsort(mean_probs)[::-1][:10]

for i in top_idx:
    print(f"{classes[i]:<20} {mean_probs[i]:.3f}")

print("\nStats:")
print("----------------------------------------")
print("min:", mean_probs.min())
print("max:", mean_probs.max())
print("mean:", mean_probs.mean())
