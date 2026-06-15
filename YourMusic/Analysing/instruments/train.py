import os
import glob
import numpy as np
import joblib
from tqdm import tqdm

from sklearn.preprocessing import StandardScaler
from sklearn.neural_network import MLPRegressor
from sklearn.model_selection import train_test_split
from sklearn.metrics import mean_squared_error



FEATURE_DIR = "features"
MODEL_PATH = "instruments_model.joblib"
SCALER_PATH = "instruments_scaler.joblib"
INSTRUMENT_LIST_PATH = "instrument_list.joblib"



def load_instruments(data):
    """
    Читает инструменты из npz.
    Возвращает dict {instrument: relevance}
    Поддерживает:
      - instruments + probabilities (основной формат)
      - dict
      - список инструментов
    """

    if "instruments" in data and "probabilities" in data:
        inst = data["instruments"]
        probs = data["probabilities"]

        result = {}
        for name, p in zip(inst, probs):
            result[str(name)] = float(p)

        return result

    if "instruments" in data:
        inst = data["instruments"]

        if isinstance(inst, np.ndarray) and inst.dtype == object:
            if inst.size == 1:
                inst = inst.item()

        if isinstance(inst, dict):
            return inst

        if isinstance(inst, (list, tuple, np.ndarray)):
            return {str(i): 1.0 for i in inst}

    return {}



print("Collecting feature files...")
feature_files = sorted(glob.glob(os.path.join(FEATURE_DIR, "*.npz")))
print("Feature files:", len(feature_files))

if len(feature_files) == 0:
    raise RuntimeError("No feature files found")



print("Scanning instruments...")
instrument_set = set()

for path in tqdm(feature_files):
    data = np.load(path, allow_pickle=True)
    inst_dict = load_instruments(data)
    instrument_set.update(inst_dict.keys())

instrument_list = sorted(list(instrument_set))
instrument_index = {name: i for i, name in enumerate(instrument_list)}

print("Unique instruments:", len(instrument_list))

if len(instrument_list) == 0:
    raise RuntimeError("No instruments found in dataset")



print("Building dataset...")

X = []
Y = []

for path in tqdm(feature_files):
    data = np.load(path, allow_pickle=True)

    emb = data["embedding"]

    if emb.ndim != 1:
        emb = emb.reshape(-1)

    inst_dict = load_instruments(data)

    y = np.zeros(len(instrument_list), dtype=np.float32)

    for name, prob in inst_dict.items():
        if name in instrument_index:
            y[instrument_index[name]] = prob

    X.append(emb)
    Y.append(y)

X = np.array(X, dtype=np.float32)
Y = np.array(Y, dtype=np.float32)

print("X shape:", X.shape)
print("Y shape:", Y.shape)



X_train, X_test, Y_train, Y_test = train_test_split(
    X, Y,
    test_size=0.2,
    random_state=42
)



print("Fitting scaler...")
scaler = StandardScaler()
X_train = scaler.fit_transform(X_train)
X_test = scaler.transform(X_test)



print("Training model...")
model = MLPRegressor(
    hidden_layer_sizes=(1024, 512),
    activation="relu",
    solver="adam",
    learning_rate="adaptive",
    max_iter=500,
    verbose=True,
    random_state=42
)

model.fit(X_train, Y_train)



print("Evaluating...")
pred = model.predict(X_test)
mse = mean_squared_error(Y_test, pred)
print("Test MSE:", mse)



print("Saving model...")
joblib.dump(model, MODEL_PATH)

print("Saving scaler...")
joblib.dump(scaler, SCALER_PATH)

print("Saving instrument list...")
joblib.dump(instrument_list, INSTRUMENT_LIST_PATH)

print("Done.")
