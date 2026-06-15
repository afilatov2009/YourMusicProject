import os
import json
import numpy as np
from tqdm import tqdm

from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler
from sklearn.neural_network import MLPClassifier
from sklearn.metrics import classification_report



DATA_DIR = "embeddings"

MODEL_PATH = "instrument_model.joblib"
SCALER_PATH = "instrument_scaler.joblib"
CLASSES_PATH = "instrument_classes.json"



with open(os.path.join(DATA_DIR, "instrument_classes.json")) as f:
    classes = json.load(f)

class_to_idx = {c: i for i, c in enumerate(classes)}

print("Classes:", classes)



X = []
y = []

print("\nLoading dataset...")

for instrument in classes:
    folder = os.path.join(DATA_DIR, instrument)

    if not os.path.exists(folder):
        continue

    files = [f for f in os.listdir(folder) if f.endswith(".npz")]

    for file in tqdm(files, desc=instrument):
        path = os.path.join(folder, file)

        try:
            data = np.load(path)

            emb = data["embedding"]

            X.append(emb)
            y.append(class_to_idx[instrument])

        except Exception as e:
            print("ERROR:", path, e)

X = np.array(X)
y = np.array(y)

print("\nDataset loaded:")
print("X:", X.shape)
print("y:", y.shape)



X_train, X_test, y_train, y_test = train_test_split(
    X, y,
    test_size=0.2,
    random_state=42,
    stratify=y
)



print("\nScaling...")

scaler = StandardScaler()
X_train = scaler.fit_transform(X_train)
X_test = scaler.transform(X_test)



print("\nTraining model...")

model = MLPClassifier(
    hidden_layer_sizes=(512, 256),
    activation="relu",
    solver="adam",
    batch_size=128,
    learning_rate_init=0.001,
    max_iter=50,
    verbose=True
)

model.fit(X_train, y_train)



print("\nEvaluating...")

y_pred = model.predict(X_test)

print(classification_report(y_test, y_pred, target_names=classes))



import joblib

joblib.dump(model, MODEL_PATH)
joblib.dump(scaler, SCALER_PATH)

print("\nSaved:")
print("Model ->", MODEL_PATH)
print("Scaler ->", SCALER_PATH)
