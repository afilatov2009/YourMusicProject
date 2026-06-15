import sys
import numpy as np
import soundfile as sf
import openl3
import joblib

if len(sys.argv) != 2:
    print("usage: python3 predict.py file.mp3")
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

model = joblib.load("mood_model.joblib")
scaler = joblib.load("scaler.joblib")
emb = scaler.transform(emb)

valence, arousal = model.predict(emb)[0]

def describe_emotion(valence, arousal):
    if valence < 2:
        mood = "very sad"
    elif valence < 4:
        mood = "sad"
    elif valence < 6:
        mood = "neutral"
    elif valence < 8:
        mood = "happy"
    else:
        mood = "very happy"

    if arousal < 2:
        energy = "very calm"
    elif arousal < 4:
        energy = "calm"
    elif arousal < 6:
        energy = "moderate"
    elif arousal < 8:
        energy = "active"
    else:
        energy = "energetic"

    return (
        f"The track has a {mood} emotional character with {energy} energy. "
        f"(Valence: {valence:.2f}, Arousal: {arousal:.2f})"
    )

print(describe_emotion(valence, arousal))
