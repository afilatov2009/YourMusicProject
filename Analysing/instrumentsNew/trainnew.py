import os
import random
import numpy as np
import joblib

from sklearn.preprocessing import StandardScaler
from sklearn.neural_network import MLPClassifier
from sklearn.model_selection import train_test_split


EMB_PATH = "embeddings"
MAX_SOURCES_PER_SAMPLE = 3
SAMPLES = 50000


classes = sorted([
    d for d in os.listdir(EMB_PATH)
    if os.path.isdir(os.path.join(EMB_PATH, d))
])

class_to_idx = {c: i for i, c in enumerate(classes)}
n_classes = len(classes)

print("Classes:", classes)


file_index = {}

for cls in classes:
    folder = os.path.join(EMB_PATH, cls)

    files = []
    for f in os.listdir(folder):
        path = os.path.join(folder, f)

        if os.path.isfile(path) and f.endswith(".npz"):
            files.append(path)

    file_index[cls] = files

    if len(files) == 0:
        print(f"[WARNING] Empty class: {cls}")


def load_random_embedding(cls):
    if len(file_index[cls]) == 0:
        raise ValueError(f"No embeddings for class: {cls}")

    path = random.choice(file_index[cls])
    data = np.load(path,allow_pickle=True)

    if "embedding" in data:
        emb = data["embedding"]
    else:
        emb = data["arr_0"]

    return emb


def make_sample():
    n_sources = random.randint(1, MAX_SOURCES_PER_SAMPLE)
    chosen_classes = random.sample(classes, n_sources)

    embs = []
    label = np.zeros(n_classes)

    for cls in chosen_classes:
        emb = load_random_embedding(cls)
        embs.append(emb)
        label[class_to_idx[cls]] = 1

    mix = np.mean(embs, axis=0)

    return mix, label


total_files = sum(len(v) for v in file_index.values())
print("Total embedding files:", total_files)

if total_files == 0:
    raise RuntimeError("No embeddings found. Check dataset path or file format.")


print("Generating synthetic dataset...")

X = []
Y = []

for i in range(SAMPLES):
    x, y = make_sample()
    X.append(x)
    Y.append(y)

X = np.array(X)
Y = np.array(Y)

print("X shape:", X.shape)
print("Y shape:", Y.shape)


scaler = StandardScaler()
X = scaler.fit_transform(X)


X_train, X_test, y_train, y_test = train_test_split(
    X, Y, test_size=0.1, random_state=42
)


print("Training model...")

model = MLPClassifier(
    hidden_layer_sizes=(512, 256),
    activation="relu",
    solver="adam",
    max_iter=30,
    verbose=True
)

model.fit(X_train, y_train)


print("Evaluating...")

score = model.score(X_test, y_test)
print("Accuracy:", score)


joblib.dump(model, "instrument_model.joblib")
joblib.dump(scaler, "instrument_scaler.joblib")
np.save("instrument_classes.npy", np.array(classes))

print("Saved: model + scaler + classes")
