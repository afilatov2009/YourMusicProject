from fastapi import FastAPI, WebSocket, WebSocketDisconnect
import torch
from audiocraft.models import MusicGen
import numpy as np
import io
import wave
import asyncio
import gc

app = FastAPI()
sessions = {}
device = "cuda" if torch.cuda.is_available() else "cpu"
print(f"Device: {device.upper()}")

duration = 30 if device == "cuda" else 5
crossfade = 5 if device == "cuda" else 2
timeout = 300

model = MusicGen.get_pretrained("facebook/musicgen-small", device=device)
model.set_generation_params(duration=duration, use_sampling=True)
print("Model loaded")

def wavBytes(wav):
    buffer = io.BytesIO()
    with wave.open(buffer, "wb") as f:
        f.setnchannels(1)
        f.setsampwidth(2)
        f.setframerate(model.sample_rate)
        f.writeframes(np.clip(wav.detach().cpu().numpy() * 32767, -32768, 32767).astype("int16").tobytes())
    buffer.seek(0)
    return buffer.read()

class Session:
    def __init__(self):
        self.clients    = set()
        self.prompts    = asyncio.Queue()
        self.started    = False
        self.buffer     = torch.empty((1, 0), device=device)
        self.lastThirty = torch.empty((1, 0), device=device)
        self.tasks      = set()

    def addTask(self, task: asyncio.Task):
        self.tasks.add(task)
        task.add_done_callback(self.tasks.discard)

    async def close(self):
        for task in list(self.tasks):
            if not task.done():
                task.cancel()
        if self.tasks:
            await asyncio.gather(*self.tasks, return_exceptions=True)
        gc.collect()
        if torch.cuda.is_available():
            torch.cuda.empty_cache()

    async def send(self):
        dead = []
        for ws in self.clients:
            try:
                await ws.send_text("next")
            except (WebSocketDisconnect, RuntimeError):
                dead.append(ws)
        for ws in dead:
            self.clients.discard(ws)

    async def generation(self):
        print("Generation started")
        try:
            await self.send()
            try:
                prompt = await asyncio.wait_for(self.prompts.get(), timeout=timeout)
            except asyncio.TimeoutError:
                print("Timeout")
                return
            wavfile = None
            while wavfile is None:
                try:
                    wavfile = await asyncio.to_thread(lambda: model.generate([prompt], model.sample_rate))
                except Exception as e:
                    print(f"Generate error: {e}, retrying")
                    await asyncio.sleep(1)
            while True:
                if self.buffer.shape[-1] <= 150 * model.sample_rate:
                    await self.send()
                    try:
                        prompt = await asyncio.wait_for(self.prompts.get(), timeout=timeout)
                    except asyncio.TimeoutError:
                        print("Timeout")
                        return
                    try:
                        nextFile = await asyncio.to_thread(lambda: model.generate([prompt], model.sample_rate))
                    except Exception as e:
                        print(f"Generate error: {e}, retrying")
                        await asyncio.sleep(1)
                        continue
                    first  = wavfile.squeeze(0)
                    second = nextFile.squeeze(0)
                    t = min(int(crossfade * model.sample_rate), first.shape[-1], second.shape[-1])
                    overlap = (first[..., -t:] * torch.linspace(1, 0, t, device=device) +
                               second[..., :t]  * torch.linspace(0, 1, t, device=device))
                    finalV = torch.cat([first[..., :-t], overlap], dim=-1)
                    self.buffer = torch.cat([self.buffer, finalV], dim=-1)
                    print(self.buffer.numel())
                    wavfile = nextFile[..., t:]
                else:
                    await asyncio.sleep(1)

        except asyncio.CancelledError:
            raise
        finally:
            self.buffer     = torch.empty((1, 0), device=device)
            self.lastThirty = torch.empty((1, 0), device=device)
            self.started    = False
            gc.collect()
            print("Generation task ended")

    async def play(self, ws):
        sec = 32000
        print("playing")
        while True:
            if self.buffer.numel() == 0:
                await asyncio.sleep(0.1)
                continue
            chunkSize = min(sec, self.buffer.numel())
            chunk         = self.buffer[..., :chunkSize]
            self.buffer   = self.buffer[..., chunkSize:]
            if self.lastThirty.shape[-1] < duration * model.sample_rate:
                self.lastThirty = torch.cat([self.lastThirty, chunk], dim=-1)
            else:
                self.lastThirty = self.lastThirty[..., chunkSize:]
                self.lastThirty = torch.cat([self.lastThirty, chunk], dim=-1)
            try:
                await ws.send_bytes(bytes([1]) + wavBytes(chunk.unsqueeze(0)))
            except (WebSocketDisconnect, RuntimeError):
                break
            await asyncio.sleep(0.98)

    async def downloading(self, ws):
        await ws.send_bytes(bytes([2]) + wavBytes(self.lastThirty))


def newSession(id: str) -> Session:
    if id not in sessions:
        sessions[id] = Session()
    return sessions[id]

async def clean(session_id: str):
    await asyncio.sleep(timeout)
    if session_id in sessions and not sessions[session_id].clients:
        print(f"Removing session {session_id}")
        await sessions[session_id].close()
        sessions.pop(session_id, None)

@app.websocket("/ws")
async def websocket_music(ws: WebSocket):
    await ws.accept()
    id = ws.query_params.get("session", "none")
    session = newSession(id)
    session.clients.add(ws)
    print("Connected new client on id:" + id)

    async def listen():
        while True:
            if ws.client_state.name == "CONNECTED":
                try:
                    command = await ws.receive_text()
                    if command == "play":
                        print("Start playing")
                        task = asyncio.create_task(session.play(ws))
                        session.addTask(task)
                    elif command == "download":
                        asyncio.create_task(session.downloading(ws))
                    elif command == "stopSession":
                        await session.close()
                        sessions.pop(id, None)
                        break
                    else:
                        await session.prompts.put(command)
                        print(f"New prompt: {command}")
                except WebSocketDisconnect:
                    print("bad connection")
                    session.clients.discard(ws)
                    break
            else:
                break

    if not session.started:
        session.started = True
        task = asyncio.create_task(session.generation())
        session.addTask(task)
    await listen()
    session.clients.discard(ws)
    if not session.clients and id in sessions:
        asyncio.create_task(clean(id))

