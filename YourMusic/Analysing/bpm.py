import librosa
import numpy as np

def get_bpm(file_path):
    try:
        y, sr = librosa.load(file_path, mono=True)
        if len(y) == 0:
            return None
        tempo, _ = librosa.beat.beat_track(y=y, sr=sr, start_bpm=120.0, units='time')
        if isinstance(tempo, np.ndarray):
            if tempo.size == 1:
                tempo = tempo.item()
            else:
                tempo = float(np.nanmean(tempo))
        if np.isnan(tempo):
            print("NaN")
            return None
        return round(float(tempo), 2)
    except Exception as e:
        print(f"error reading file {file_path}: {e}")
        return None

if __name__ == "__main__":
    file_path = input("enter path: ").strip()
    bpm = get_bpm(file_path)
    if bpm is not None:
        print(bpm)
    else:
        print("failed")
