import sys
import json
import numpy as np
import librosa
import laion_clap
import soundfile as sf
import openl3
import joblib
from http.server import BaseHTTPRequestHandler, HTTPServer
import cgi
import tempfile
import os
import torch
import urllib.request
import ssl
_ssl_ctx = ssl.create_default_context()
_ssl_ctx.check_hostname = False
_ssl_ctx.verify_mode = ssl.CERT_NONE
from socketserver import ThreadingMixIn
from threading import Semaphore
import threading
import time
import random
import gc

TEST_MODE = os.environ.get("TEST_MODE", "0") == "1"

def log(msg):
    print('[' + time.strftime('%H:%M:%S') + '] ' + str(msg), flush=True)

SERVER_START_TIME = time.time()
currently_processing = []

MAX_CONCURRENT_ANALYSIS = 1
analysis_lock = Semaphore(MAX_CONCURRENT_ANALYSIS)

device = "cuda" if torch.cuda.is_available() else "cpu"
EMBEDDINGS_CACHE_PATH = "/app/models_cache/text_embeddings_cache.npz"

clap_model = laion_clap.CLAP_Module(enable_fusion=False).to(device)
clap_model.load_ckpt()

mood_model = joblib.load("models/mood_model.joblib")
mood_scaler = joblib.load("models/scaler.joblib")

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
    "future funk": "future funk, funky disco bass, 80s japanese funk, upbeat electronic groove",
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
    "noise": "noise music, chaotic, distorted sound",

    "hyperpop": "hyperpop, high-pitched vocals, distorted synths, extreme pop",
    "hardstyle": "hardstyle, heavy distorted kick drum, high energy, fast tempo",
    "psytrance": "psytrance, rolling bassline, psychedelic acid synths, fast",
    "phonk": "phonk music, cowbell melody, distorted bass, memphis rap samples",
    "vaporwave": "vaporwave, slowed down aesthetic, 80s corporate music samples, reverb",
    "breakcore": "breakcore, chaotic fast drum breaks, glitchy melodic elements",
    "garage": "uk garage, 2-step rhythm, swing beats, chopped soul vocals",

    "rock": "rock music, electric guitars, acoustic drums, energetic vocals",
    "indie rock": "indie rock, jangle guitars, alternative melodic sound",
    "heavy metal": "heavy metal music, distorted overdriven guitars, powerful drums",
    "shoegaze": "shoegaze, wall of sound, dreamy distorted guitars, ethereal vocals",
    "punk": "punk rock, fast aggressive guitars, raw energy, simple structure",
    "post-rock": "post-rock, cinematic build-up, instrumental, crescendos",
    "psychedelic rock": "psychedelic rock, distorted swirling guitars, trippy analog synths, dreamy vocals, 60s 70s vibe",
    "funk rock": "funk rock, groovy rhythmic bass, electric guitar riffs, upbeat drum pocket",

    "hip hop": "hip hop music, boom bap, rap vocals, sampled beat",
    "trap": "trap music, 808 bass, fast hi-hats, modern rap production",
    "pop": "pop music, catchy vocal melody, polished production, radio hits",
    "rnb": "r'n'b music, soulful vocals, smooth grooves, rhythmic beats",
    "jazz": "jazz music, saxophone, piano improvisation, swing rhythm",
    "classical": "classical orchestral music, strings, symphony, no electronics",
    "reggae": "reggae music, off-beat guitar skank, heavy bass, relaxed rhythm",
    "dream pop": "dream pop, ethereal textures, washed out guitars, reverb heavy, slow tempo"

}

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

VIBE_PROMPTS = {
    "vintage": "vintage retro analog sound",
    "modern": "modern high-fidelity digital production",
    "atmospheric": "atmospheric ambient spacious reverb",
    "energetic": "high energy fast powerful",
    "dreamy": "dreamy ethereal hazy",
    "dark": "dark moody mysterious",
    "bright": "bright happy upbeat sunny",
    "gritty": "gritty distorted raw rough",
    "minimalist": "minimalist simple sparse",
    "futuristic": "futuristic electronic digital"
}

MOOD_MATRIX = [
    ["melancholic", "depressing",  "pensive",      "peaceful",    "serene"],
    ["gloomy",      "sad",         "chill",        "relaxed",     "joyful"],
    ["dark",        "wistful",     "neutral",      "pleasant",    "bright"],
    ["disturbing",  "agitated",    "dynamic",      "cheerful",    "happy"],
    ["aggressive",  "frantic",     "intense",      "epic",        "euphoric"]
]

def precompute_text_embeddings():
    if os.path.exists(EMBEDDINGS_CACHE_PATH):
        print("\U0001f4e6 loading cached text embeddings ...")
        cache = np.load(EMBEDDINGS_CACHE_PATH, allow_pickle=True)
        return cache["g_embs"], cache["i_embs"], cache["v_embs"], list(cache["inst_keys"])

    print("\U0001f9e0 precomputing text embeddings on", device, "...")

    genre_list = list(GENRE_PROMPTS.values())
    g_embs = clap_model.get_text_embedding(genre_list, use_tensor=False)
    g_embs = np.array(g_embs)
    g_embs /= np.linalg.norm(g_embs, axis=1, keepdims=True) + 1e-9

    inst_texts, inst_keys = [], []
    for inst, prompts in INSTRUMENT_PROMPTS.items():
        for p in prompts:
            inst_texts.append(p)
            inst_keys.append(inst)

    i_embs = clap_model.get_text_embedding(inst_texts, use_tensor=False)
    i_embs = np.array(i_embs)
    i_embs /= np.linalg.norm(i_embs, axis=1, keepdims=True) + 1e-9

    vibe_list = list(VIBE_PROMPTS.values())
    v_embs = clap_model.get_text_embedding(vibe_list, use_tensor=False)
    v_embs = np.array(v_embs)
    v_embs /= np.linalg.norm(v_embs, axis=1, keepdims=True) + 1e-9

    np.savez(EMBEDDINGS_CACHE_PATH, g_embs=g_embs, i_embs=i_embs, v_embs=v_embs, inst_keys=np.array(inst_keys))
    print("\U0001f4be text embeddings cached to disk")

    return g_embs, i_embs, v_embs, inst_keys

CACHED_GENRE_EMBS, CACHED_INST_EMBS, CACHED_VIBE_EMBS, INST_KEYS = precompute_text_embeddings()

def get_audio_emb(audio, sr):
    segment_length = sr * 10
    embs = []
    for i in range(0, len(audio), segment_length):
        seg = audio[i:i + segment_length]
        if len(seg) < segment_length: continue
        seg = np.expand_dims(np.asarray(seg, dtype=np.float32), axis=0)
        emb = clap_model.get_audio_embedding_from_data(x=seg, use_tensor=False)
        emb = np.array(emb).flatten()
        emb = emb / (np.linalg.norm(emb) + 1e-9)
        embs.append(emb)
    return None if not embs else np.mean(embs, axis=0)

def predict_genre(audio_emb):
    scores = {
        genre: float(np.dot(audio_emb, CACHED_GENRE_EMBS[i]))
        for i, genre in enumerate(GENRE_PROMPTS.keys())
    }
    return max(scores, key=scores.get), scores

def predict_instruments(audio_emb):
    scores = {}
    for i, inst in enumerate(INST_KEYS):
        score = float(np.dot(audio_emb, CACHED_INST_EMBS[i]))
        scores.setdefault(inst, []).append(score)

    final_scores = {k: float(np.mean(v)) for k, v in scores.items()}
    detected = [k for k, v in final_scores.items() if v > 0.20]
    if not detected: detected = [max(final_scores, key=final_scores.get)]
    return detected, final_scores

def predict_vibes(audio_emb):
    scores = {
        vibe: float(np.dot(audio_emb, CACHED_VIBE_EMBS[i]))
        for i, vibe in enumerate(VIBE_PROMPTS.keys())
    }
    sorted_vibes = sorted(scores.items(), key=lambda x: x[1], reverse=True)
    top_vibes = [vibe for vibe, score in sorted_vibes if score > 0.15][:2]

    return top_vibes, scores


def load_audio(path):
    return librosa.load(path, sr=48000, mono=True,duration=180)

def get_bpm(audio, sr):
    tempo, _ = librosa.beat.beat_track(y=audio, sr=sr)
    return int(round(tempo)) if tempo else None

def predict_mood(path):
    audio, sr = librosa.load(path, sr=None, mono=True, duration=180)

    emb, _ = openl3.get_audio_embedding(audio, sr, content_type="music", embedding_size=512, hop_size=0.5)
    emb = mood_scaler.transform(emb.mean(axis=0).reshape(1, -1))
    valence, arousal = mood_model.predict(emb)[0]

    log(f"DEBUG MOOD -> Valence: {valence:.2f}, Arousal: {arousal:.2f}")

    if valence < 3.0: v_idx = 0
    elif valence < 4.5: v_idx = 1
    elif valence < 5.8: v_idx = 2
    elif valence < 7.2: v_idx = 3
    else: v_idx = 4

    if arousal < 3.0: a_idx = 0
    elif arousal < 4.5: a_idx = 1
    elif arousal < 5.8: a_idx = 2
    elif arousal < 7.2: a_idx = 3
    else: a_idx = 4

    mood_tag = MOOD_MATRIX[a_idx][v_idx]

    return mood_tag

def get_uptime_string():
    uptime_seconds = int(time.time() - SERVER_START_TIME)

    hours = uptime_seconds // 3600
    minutes = (uptime_seconds % 3600) // 60
    seconds = uptime_seconds % 60

    return f"{hours}h {minutes}m {seconds}s"

def analyse(path, track):
    global currently_processing
    with analysis_lock:
        currently_processing.append(track)
        try:
            st = time.time()
            audio, sr = load_audio(path)
            emb = get_audio_emb(audio, sr)
            bpm = get_bpm(audio, sr)
            del audio
            gc.collect()
            if emb is None: return {"error": "Audio too short"}
            genre, genre_scores = predict_genre(emb)
            instruments, _ = predict_instruments(emb)
            vibes, _ = predict_vibes(emb)
            del emb
            gc.collect()
            mood = predict_mood(path)
            duration = round(time.time() - st, 2)
            return {
                "genre": genre, "vibes": vibes, "bpm": bpm, "mood": mood,
                "instruments": instruments, "confidence": float(max(genre_scores.values())),
                "seconds": duration
            }
        finally:
            if track in currently_processing:
                currently_processing.remove(track)

class Handler(BaseHTTPRequestHandler):
    def log_message(self, format, *args):
        return

    def do_POST(self):
        ip = self.client_address[0]
        form = cgi.FieldStorage(
            fp=self.rfile, headers=self.headers,
            environ={'REQUEST_METHOD': 'POST'}
        )

        if "file" not in form or "sessionId" not in form or "id" not in form or "playlist" not in form or "track" not in form or "url" not in form:
            self.send_response(400); self.end_headers(); return

        file_item = form["file"]
        track_name = form["track"].value
        session_id = form["sessionId"].value
        user_id = form["id"].value
        playlist_name = form["playlist"].value
        url = form["url"].value

        log(f"\U0001f913 analysing '{track_name}' for {ip}")

        with tempfile.NamedTemporaryFile(delete=False) as tmp:
            tmp.write(file_item.file.read())
            tmp_path = tmp.name
            tmp.flush()
            os.fsync(tmp.fileno())

        def run_analysis_and_webhook():
            try:
                if TEST_MODE:

                    import time as _t; _t.sleep(2)
                    result = {
                        "genre": random.choice(list(GENRE_PROMPTS.keys())),
                        "mood": random.choice([m for row in MOOD_MATRIX for m in row]),
                        "bpm": random.randint(70, 180),
                        "instruments": random.sample(list(INSTRUMENT_PROMPTS.keys()), k=random.randint(2,4)),
                        "seconds": 2.0
                    }
                    log(f"\U0001f9ea TEST analysed '{track_name}' for {ip}")
                else:
                    result = analyse(tmp_path, track_name)
                    log(f"\U00002705 analysed '{track_name}' for {ip} ({result.get('seconds', 0)}s)")

                data = {
                    "sessionId": session_id,
                    "id": user_id,
                    "playlist": playlist_name,
                    "track": track_name,
                    "genre": result.get("genre", "Unknown"),
                    "mood": result.get("mood", "Unknown"),
                    "bpm": result.get("bpm", 80),
                    "instruments": result.get("instruments", "Unknown")
                }
                req = urllib.request.Request(
                    url,
                    data=json.dumps(data).encode('utf-8'),
                    headers={'Content-Type': 'application/json'},
                    method='POST'
                )

                with urllib.request.urlopen(req, timeout=10, context=_ssl_ctx) as response:
                    log(f"Sent to PHP. Status code: {response.status}")
            except Exception as e:
                log(f"\U0000274c Error during background task: {e}")
            finally:
                if os.path.exists(tmp_path):
                    os.remove(tmp_path)

        threading.Thread(target=run_analysis_and_webhook, daemon=True).start()

        self.send_response(200)
        self.send_header("Content-Type", "application/json")
        self.end_headers()
        self.wfile.write(json.dumps({"status": "queued"}).encode())

    def do_GET(self):
        if self.path == '/health':
            self.send_response(200)
            self.send_header("Content-Type", "application/json")
            self.end_headers()

            status = {
                "status": "online",
                "device": device,
                "load": f'{len(currently_processing)}/{MAX_CONCURRENT_ANALYSIS}',
                "analysing": currently_processing,
                "server": "tuff-analyser-v2",
                "uptime": get_uptime_string()
            }
            self.wfile.write(json.dumps(status).encode())
        else:
            self.send_response(404)
            self.end_headers()


class ThreadingSimpleServer(ThreadingMixIn, HTTPServer):
    daemon_threads = True

if __name__ == "__main__":
    port = 6767
    server = ThreadingSimpleServer(("0.0.0.0", port), Handler)
    try:
        print(f"tuff music analyser ser (short for server btw) running on port {port} \U0001f608\U0001f976\U0001f940 (mode: {device})")
        server.serve_forever()
    except KeyboardInterrupt:
        print("\nbye king \U0001f451")
        server.server_close()
