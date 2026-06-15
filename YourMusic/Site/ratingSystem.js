const tasteProfile = {attrWeights:{},synonymWeights:{}, instsList:{}};
let tracksAndPrompts = JSON.parse(localStorage.getItem('tracksAndPrompts') || '[]');
loadProfile()
let tracksCount = 0;

function sredny(v, lo, hi) {
    return Math.max(lo, Math.min(hi, v));
}

function normalyse(obj, x) {
    for (const key in obj) {
        if (Math.abs(x) < 1) obj[key] *= x;
        else {
            if (obj[key] < 0) obj[key] += x
            else if (obj[key] > 0) obj[key] -= x
        } 
        if (Math.abs(obj[key]) < 0.01) delete obj[key];
    }
};

async function loadProfile() {
    const r = await fetch('files.php?getProfile=1');
    const data = await r.json();
    Object.assign(tasteProfile.attrWeights, data.attrWeights || {});
    Object.assign(tasteProfile.synonymWeights, data.synonymWeights || {});
    Object.assign(tasteProfile.instsList, data.instsList || {});
}


function saveProfile() {
    console.log("Profile is saving")
    fetch('files.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: new URLSearchParams({
            saveProfile: JSON.stringify(tasteProfile)
        }).toString()
    });
}

function pickWeighted(items, funct) {
    if (!items || !Array.isArray(items) || items.length === 0) return undefined;
    const weights = items.map(item => {
        const s = Math.max(-5, Math.min(5, funct(item)));
        return 2 ** (s * 0.4);
    });
    const total = weights.reduce((a, b) => a + b, 0);
    let rand = Math.random() * total;
    for (let i = 0; i < items.length; i++) {
        rand -= weights[i];
        if (rand <= 0) return items[i];
    }
}

function pickAttr(genre, attrs) {
    return pickWeighted(attrs, attr => tasteProfile.attrWeights[attr] || 0) || attrs[0];
}

function pickSynonym(mood, synonyms) {
    return pickWeighted(synonyms, syn => tasteProfile.synonymWeights[syn] || 0) || mood;
}

function trackSelected() {
    tracksCount++;
    if (tracksCount % 4 === 0){
        normalyse(tasteProfile.attrWeights, 0.8);
        normalyse(tasteProfile.synonymWeights, 1);
        normalyse(tasteProfile.instsList, 1);
        saveProfile()
    };
}

function changeProfile(track, thisPrompt, attr, mood, sign) {
    tasteProfile.attrWeights[attr] = sredny((tasteProfile.attrWeights[attr] || 0) + sign * 0.5, -5, 5);
    tasteProfile.synonymWeights[mood] = sredny((tasteProfile.synonymWeights[mood] || 0) + sign * 0.5, -5, 5);
    track.instruments.forEach((i) => {
        if (sign == 1) {
            tasteProfile.instsList[i] = Math.min((tasteProfile.instsList[i] || 0) + 1, 7);
        }
        if (sign == -1) {
            tasteProfile.instsList[i] = Math.max((tasteProfile.instsList[i] || 0) -1, -7);
        }
    });
    saveProfile()
    return true;
}

function like(data) {
    let track = data[0]
    let thisPrompt = data[1]
    let attr = data[2]
    let mood = data[3]
    changeProfile(track, thisPrompt, attr, mood, 1);
    fetch('files.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: new URLSearchParams({ rateTrack: track.title, delta: String(0.5) }).toString()
    });
    return true;
}

function dislike(data) {
    let track = data[0]
    let thisPrompt = data[1]
    let attr = data[2]
    let mood = data[3]
    changeProfile(track, thisPrompt, attr, mood, -1);
    fetch('files.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: new URLSearchParams({ rateTrack: track.title, delta: String(-0.5) }).toString()
    });
    return true;
} 