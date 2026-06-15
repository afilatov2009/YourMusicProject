import sys
import numpy as np
import soundfile as sf
import openl3
import joblib

if len(sys.argv) != 2:
    print("usage: python3 genre.py file.mp3")
    sys.exit(1)

audio_path = sys.argv[1]

audio, sr = sf.read(audio_path)
emb, _ = openl3.get_audio_embedding(
    audio,
    sr,
    content_type="music",
    embedding_size=512
)

emb = emb.mean(axis=0).reshape(1, -1)

scaler = joblib.load("genre_scaler.joblib")
model = joblib.load("genre_model.joblib")
encoder = joblib.load("genre_encoder.joblib")

emb = scaler.transform(emb)
pred = model.predict(emb)[0]

genre = encoder.inverse_transform([pred])[0]
print(genre)
