const dictionary = {
    synonims: {
        "melancholic":  ["haunting",     "somber",        "wistful",       "longing",      "plaintive"],
        "depressing":   ["bleak",        "desolate",      "hollow",        "mournful",     "heavy"],
        "pensive":      ["thoughtful",   "reflective",    "contemplative", "meditative",   "quiet"],
        "peaceful":     ["calm",         "gentle",        "still",         "serene",       "soothing"],
        "serene":       ["tranquil",     "placid",        "hushed",        "glassy",       "luminous"],
        "gloomy":       ["overcast",     "dim",           "murky",         "sullen",       "brooding"],
        "sad":          ["sorrowful",    "mournful",      "heavy-hearted", "wistful",      "plaintive"],
        "chill":        ["laid-back",    "easy",          "smooth",        "hazy",         "low-key"],
        "relaxed":      ["dreamy",       "mellow",        "unhurried",     "languid",      "gentle"],
        "joyful":       ["elated",       "exuberant",     "gleeful",       "buoyant",      "radiant"],
        "dark":         ["shadowy",      "sinister",      "ominous",       "murky",        "foreboding"],
        "wistful":      ["nostalgic",    "bittersweet",   "longing",       "reflective",   "tender"],
        "neutral":      ["balanced",     "even",          "steady",        "composed",     "moderate"],
        "pleasant":     ["warm",         "inviting",      "agreeable",     "soft",         "gentle"],
        "bright":       ["sunny",        "vivid",         "sparkling",     "airy",         "luminous"],
        "disturbing":   ["unsettling",   "eerie",         "tense",         "restless",     "uneasy"],
        "agitated":     ["restless",     "nervous",       "anxious",       "turbulent",    "fitful"],
        "dynamic":      ["driving",      "propulsive",    "surging",       "forceful",     "pressing"],
        "cheerful":     ["merry",        "upbeat",        "lively",        "spirited",     "breezy"],
        "happy":        ["joyful",       "buoyant",       "gleeful",       "warm",         "elated"],
        "aggressive":   ["fierce",       "raw",           "relentless",    "brutal",       "gritty"],
        "frantic":      ["frenetic",     "hectic",        "frenzied",      "wild",         "chaotic"],
        "intense":      ["powerful",     "charged",       "sharp",         "searing",      "fierce"],
        "epic":         ["grand",        "sweeping",      "majestic",      "monumental",   "soaring"],
        "euphoric":     ["ecstatic",     "blissful",      "transcendent",  "rapturous",    "elevated"]
    },

    genreAtributes: {
        "ambient":            ["endless space void",         "deep ocean silence",         "morning mountain fog",        "ethereal spirit world",       "drifting clouds"],
        "dark ambient":       ["ancient cave drip",          "fog over still water",        "desolate winter field",       "distant thunder static",      "flickering candle darkness"],
        "lofi":               ["rainy window pane",          "warm coffee shop hum",        "soft tape hiss",              "distant city night sounds",   "vinyl crackle warmth"],
        "chillout":           ["hammock in warm breeze",     "sundowner horizon glow",      "poolside evening echo",       "slow ceiling fan",            "long road at dusk"],
        "downtempo":          ["half-lit hotel lobby",       "slow rain on glass",          "late night highway",          "dimmed dancefloor aftermath", "blurry city reflections"],
        "chillsynth":         ["pastel sunset haze",         "slow motion neon",            "dreamy analog warmth",        "gentle oscillator hum",       "retro bedroom glow"],
        "vaporwave":          ["empty shopping mall echo",   "slowed corporate muzak",      "80s elevator reverb",         "marble floors and palms",     "glitching VHS color bleed"],
        "cloud rap":          ["foggy midnight drive",       "blurred streetlight haze",    "icy breath in dark air",      "whispered concrete echoes",   "drifting smoke ceiling"],
        "dream pop":          ["washed-out summer haze",     "reverb-soaked bedroom",       "soft lens flare warmth",      "hazy distant vocals",         "slow motion film grain"],
        "shoegaze":           ["wall of distortion fog",     "submerged guitar wash",       "cathedral drone reverb",      "muffled euphoric haze",       "sunlight through dirty glass"],
        "liquid dnb":         ["underwater neon glow",       "smooth rain-slicked city",    "floating in deep water",      "silk and bass warmth",        "blurred rooftop lights"],
        "future garage":      ["twilight at the port",       "soft rain on pavement",       "ghostly vocal echo",          "2am train station hum",       "cold glass warm breath"],
        "jazz":               ["smoky bar interior",         "clinking glasses and plates", "intimate lounge reverb",      "warm wooden stage",           "soft evening rain"],
        "techno":             ["cold industrial warehouse",  "throbbing strobe lights",     "mechanical factory hum",      "underground club reverb",     "digital pulse static"],
        "minimal techno":     ["sparse concrete room",       "single bulb flickering",      "distant kick in fog",         "hypnotic repetition",         "empty tunnel resonance"],
        "house":              ["glimmering disco ball",      "pumping dancefloor energy",   "chic rooftop party",          "summer breeze rhythm",        "warm bassline groove"],
        "deep house":         ["dimmed basement lounge",     "soul-soaked vinyl crackle",   "late night body heat",        "smooth chord reverb",         "candle in a dark club"],
        "synthwave":          ["1980s neon city highway",    "retro arcade buzz",           "analog laser hum",            "driving fast at dusk",        "purple sunset glow"],
        "retrowave":          ["cassette tape warmth",       "80s TV static glow",          "chrome and neon reflection",  "analog sunset pulse",         "synth-drenched nostalgia"],
        "idm":                ["complex glitch patterns",    "abstract digital landscape",  "microscopic sound clicks",    "unfolding geometric shapes",  "brain-stimulating noise"],
        "glitch":             ["broken digital signal",      "corrupted data stream",       "fractured rhythm artifacts",  "circuit error pulse",         "pixelated sonic decay"],
        "trance":             ["hands-raised open sky",      "euphoric melodic build",      "four-on-the-floor eternity",  "transcendent energy peak",    "shimmering air plateau"],
        "progressive house":  ["evolving dancefloor journey","deep bassline progression",   "sunrise rooftop crowd",       "layered chord atmosphere",    "long melodic arc"],
        "electro":            ["punchy robot groove",        "synthetic drum machine kick", "metallic synth stab",         "cold funk of machines",       "grid-locked beat precision"],
        "hardstyle":          ["massive distorted kick wall","stadium crowd frenzy",        "hard floor vibration",        "pounding concrete energy",    "raw festival power"],
        "psytrance":          ["alien jungle canopy",        "psychedelic acid spiral",     "fast rolling bassline",       "cosmic tribal pulse",         "fractal sound tunnel"],
        "ebm":                ["military synth march",       "aggressive sequenced pulse",  "cold steel machinery",        "body music in darkness",      "industrial dancefloor tension"],
        "hyperpop":           ["shattered pop excess",       "distorted candy rush",        "extreme pitch glitch",        "hyper-colored digital world", "broken speaker sweetness"],
        "garage":             ["late night UK basement",     "swinging 2-step shuffle",     "chopped vocal chop",          "subwoofer rumble underfoot",  "dark dancefloor swing"],
        "hip hop":            ["street basketball court",    "heavy sub-bass rumble",       "urban traffic noise",         "vinyl scratching surface",    "boombox power"],
        "trap":               ["dark luxury mansion night",  "heavy 808 sub rumble",        "icy diamond shimmer",         "street corner midnight",      "polished dark chrome"],
        "phonk":              ["dusty Memphis night",        "cowbell through a tunnel",    "low car ride vibration",      "dark southern static",        "chopped vocal haze"],
        "dubstep":            ["gritty urban wasteland",     "heavy electric distortion",   "deep subterranean bass",      "shattering glass impact",     "robotic machinery grind"],
        "drum and bass":      ["speeding underground train", "fast urban jungle rush",      "concrete bass impact",        "frantic city night energy",   "overpass vibration"],
        "breakbeat":          ["broken groove loop",         "funky drum scatter",          "urban sample cut",            "head-nodding rhythm shift",   "raw vinyl crack"],
        "future funk":        ["disco ball at noon",         "funky city pop shimmer",      "groovy upbeat city energy",   "bright synth bass bounce",    "feel-good movement"],
        "funk rock":          ["sweat on the stage floor",   "tight pocket groove",         "wah guitar squeal",           "crowd moving as one body",    "bass and drum locked in"],
        "rnb":                ["candlelit velvet room",       "smooth late night groove",    "soulful voice over bass",     "warm studio glow",            "silk sheets low light"],
        "rock":               ["raw garage acoustics",       "loud stadium echo",           "crowd cheering energy",       "distorted air vibration",     "gritty basement feel"],
        "indie rock":         ["jangling college radio echo","rainy practice room",          "cardboard-box reverb",        "intimate small venue warmth", "afternoon light through blinds"],
        "heavy metal":        ["dark thunderstorm power",    "heavy iron foundry",          "aggressive mosh pit energy",  "hellish fire crackle",        "sharp metallic serration"],
        "black metal":        ["frozen Norwegian mountain",  "winter forest darkness",      "raw lo-fi cave recording",    "snowstorm in open field",     "ancient ruin cold"],
        "punk":               ["rebellious basement grit",   "DIY underground club",        "raw distorted energy",        "chaotic street protest",      "lo-fi protest spirit"],
        "industrial":         ["grinding factory machinery", "harsh metal collision",       "cold warehouse darkness",     "mechanical torture rhythm",   "desolate post-industrial ruin"],
        "noise":              ["wall of chaotic static",     "overdriven circuit scream",   "pure sonic destruction",      "feedback loop collapse",      "white noise cathedral"],
        "post-rock":          ["cinematic crescendo build",  "empty highway at dawn",       "vast open landscape",         "slow-motion emotional wave",  "gentle then overwhelming"],
        "psychedelic rock":   ["swirling fuzz guitar haze",  "trippy analog warmth",        "60s summer daydream",         "droning feedback spiral",     "color-soaked sonic trip"],
        "breakcore":          ["shredded drum break chaos",  "violent glitch explosion",    "frantic digital panic",       "everything collapsing at once","gabber meets jungle"],
        "classical":          ["grand orchestra hall",       "silent library air",          "royal palace acoustics",      "wooden violin resonance",     "breathtaking concert height"],
        "reggae":             ["sunny tropical beach",       "slow island breeze",          "sweet herbal haze",           "wooden percussion reverb",    "relaxed seaside shack"],
        "pop":                ["bright radio-ready sheen",   "catchy hook in open air",     "polished studio sparkle",     "crowd singalong energy",      "sunlit chorus moment"],
        "indie pop":          ["bedroom window afternoon",   "soft chorus shimmer",         "gentle hook on a sunny day",  "light reverb warmth",         "intimate lo-fi brightness"]
    },

    genreGroups: {
        "ambient": "atmospheric",       "dark ambient": "atmospheric",  "lofi": "atmospheric",
        "chillout": "atmospheric",      "downtempo": "atmospheric",     "chillsynth": "atmospheric",
        "vaporwave": "atmospheric",     "cloud rap": "atmospheric",     "dream pop": "atmospheric",
        "shoegaze": "atmospheric",      "liquid dnb": "atmospheric",    "future garage": "atmospheric",
        "jazz": "atmospheric",          "techno": "electronic",         "minimal techno": "electronic", 
        "house": "electronic",          "deep house": "electronic",     "synthwave": "electronic",      
        "retrowave": "electronic",      "idm": "electronic",            "glitch": "electronic",         
        "trance": "electronic",         "progressive house": "electronic", "electro": "electronic",     
        "hardstyle": "electronic",      "psytrance": "electronic",      "ebm": "electronic",            
        "hyperpop": "electronic",       "garage": "electronic",         "hip hop": "ritmy",             
        "trap": "ritmy",                "phonk": "ritmy",               "dubstep": "ritmy",             
        "future funk": "ritmy",         "funk rock": "ritmy",           "rnb": "ritmy",
        "rock": "heavy",                "indie rock": "heavy",          "heavy metal": "heavy",
        "black metal": "heavy",         "punk": "heavy",                "industrial": "heavy",
        "noise": "heavy",               "post-rock": "heavy",           "psychedelic rock": "heavy",
        "breakcore": "heavy",           "classical": "organic",         "reggae": "organic",
        "pop": "other",                 "indie pop": "other",           "drum and bass": "ritmy",       
        "breakbeat": "ritmy",
    },

    patterns: {
        "atmospheric": "A deeply [MOOD] [GENRE] soundscape. Focus on [INSTRUMENTS] with [ATRIBUTES]. [TEMPO] BPM.",
        "electronic":  "Professional [MOOD] [GENRE] track for a club. In [TEMPO] BPM, [INSTRUMENTS] with [ATRIBUTES].",
        "ritmy":       "Gritty [MOOD] [GENRE]. Featuring [INSTRUMENTS]. [ATRIBUTES], [TEMPO] BPM. High-end bass production.",
        "heavy":       "Raw and powerful [MOOD] [GENRE] music. [INSTRUMENTS] play with energy. Elements of [ATRIBUTES], [TEMPO] BPM. Recorded in a concert style.",
        "organic":     "A peaceful [MOOD] [GENRE] composition with [ATRIBUTES]. Arrangement featuring [INSTRUMENTS] in [TEMPO] BPM.",
        "other":       "High-quality [GENRE] music in [MOOD] mood with [INSTRUMENTS]. Recorded in [TEMPO] BPM. Professional studio sound.",
        "between":     "Evolving musical bridge between [GENRE_A] and [GENRE_B]. Start with [MOOD_A] atmosphere, then introduce [MOOD_B] patterns. Blending [INSTRUMENTS_A] in the beginning and [INSTRUMENTS_B] in the end.In [TEMPO] BPM tempo with some [ATRIBUTES_A] and some [ATRIBUTES_B]."
    }
};
const moodPositions = {
    "melancholic": [0, 0], "depressing": [0, 1], "pensive":   [0, 2], "peaceful": [0, 3], "serene":   [0, 4],
    "gloomy":      [1, 0], "sad":        [1, 1], "chill":     [1, 2], "relaxed":  [1, 3], "joyful":   [1, 4],
    "dark":        [2, 0], "wistful":    [2, 1], "neutral":   [2, 2], "pleasant": [2, 3], "bright":   [2, 4],
    "disturbing":  [3, 0], "agitated":   [3, 1], "dynamic":   [3, 2], "cheerful": [3, 3], "happy":    [3, 4],
    "aggressive":  [4, 0], "frantic":    [4, 1], "intense":   [4, 2], "epic":     [4, 3], "euphoric": [4, 4]
};

const groupCompatibility = {
    "atmospheric": { "atmospheric": 1, "electronic": 0.8, "ritmy": 0.3, "heavy": 0.15, "organic": 0.9, "other": 0.8 },
    "electronic":  { "atmospheric": 0.8, "electronic": 1, "ritmy": 0.8, "heavy": 0.3, "organic": 0.7, "other": 0.6 },
    "ritmy":       { "atmospheric": 0.3, "electronic": 0.8, "ritmy": 1, "heavy": 0.9, "organic": 0.4, "other": 0.7 },
    "heavy":       { "atmospheric": 0.15, "electronic": 0.3, "ritmy": 0.9, "heavy": 1, "organic": 0.1, "other": 0.4 },
    "organic":     { "atmospheric": 0.9, "electronic": 0.7, "ritmy": 0.4, "heavy": 0.1, "organic": 1, "other": 0.9 },
    "other":       { "atmospheric": 0.8, "electronic": 0.6, "ritmy": 0.7, "heavy": 0.4, "organic": 0.9, "other": 1 }
};
const wsUrl = 'wss://209-142-100-23.sslip.io:8443/ws';
let audio;
let allBuffer = [];
let nextStart = 0;
let ws;
let isPlaying = false;
let tracks = [];
let track = null;
let next = null;
let phase = 'first'; 
let liking = false;
let disliking = false;
let allTime = parseFloat(localStorage.getItem('allTime') || '-1');

function getIndex() {
    let currentIndex = Math.floor(allTime / 5);
    return currentIndex % tracksAndPrompts.length;
}

function resetTimer() {
    allTime = 0;
    localStorage.removeItem('allTime');
}

function moodDif(moodA, moodB) {
    const posA = moodPositions[moodA];
    const posB = moodPositions[moodB];
    if (!posA || !posB) return 0.5;
    const betw = Math.abs(posA[0] - posB[0]) + Math.abs(posA[1] - posB[1]);
    return 1 - (betw / 16);
}

function getWeight(trackA = null, trackB) {
    const rating = trackB.rating || 0;
    if (trackA == null) return Math.max(0.01, trackB.weight * Math.exp(rating * 0.2))
    const groupA = dictionary.genreGroups[trackA.genre] || 'other';
    const groupB = dictionary.genreGroups[trackB.genre] || 'other';
    const groupScore = (groupCompatibility[groupA])[groupB];
    const moodScore  = moodDif(trackA.mood, trackB.mood);
    return Math.max(0.01, trackB.weight * (2 ** (rating * 0.4))) * groupScore * moodScore;
}

function getTrack (prevTrack = null) {
    if (tracks.length > 1){
        let min = 100
        for (let track of tracks) {
            if (track.inPlay < min) min = track.inPlay;
        }
        let modTracks = tracks.filter(track => track !== prevTrack && track.inPlay - min <= 3); 
        console.log(modTracks.length)
        let totalWeight = 0
        for (let track of modTracks) {
            totalWeight += getWeight(prevTrack, track);
        }
        let random = Math.random() * totalWeight;
        let sum = 0;
        for (let i = modTracks.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [modTracks[i], modTracks[j]] = [modTracks[j], modTracks[i]];
        }
        for (let track of modTracks) {
            sum += getWeight(prevTrack, track);
            if (random <= sum){
                let ind = tracks.indexOf(track)
                if (ind != -1) tracks[ind].inPlay +=1
                fetch('files.php', {
                    method: 'POST',
                    headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: new URLSearchParams({ playCount: track.title}).toString()
                })
                trackSelected()
                console.log(track.title)
                return track;
            }
        }
        console.log("Ничего не выбрано, уходим в рекурсию:", prevTrack);
        return getTrack(prevTrack);
    }
    else return tracks[0]
};
function instruments(insts){
    let ins = '';
    for (const i in tasteProfile.instsList){
        if (tasteProfile.instsList[i] >= 6 && Math.random() > 0.5) ins = i;
    }
    if (!insts || insts.length === 0) {
        if (ins == '') return 'various instruments';
        else return ins;
    }
    if (insts.length == 1){
        if (ins == '') return insts[0];
        else return ins + " and " + insts[0];
    }
    else {
        for (let i = 0; i < insts.length; i++){
            if ((tasteProfile.instsList[insts[i]] ?? 0) < 6){
                if (i == insts.length - 2) ins += insts[i] + " and ";
                else if (i == 0 && ins != '') ins += ", " + insts[i] + ", ";
                else if (i == insts.length - 1) ins += insts[i];
                else ins += insts[i] + ", "
            }
        }
        if (ins == '') ins = insts[0]
        return ins;
    }

}

function nextPrompt() {
    if (!track) track = getTrack();
    let prompt = '';
    const pick = (arr) => arr[Math.floor(Math.random() * arr.length)];
    console.log(track.mood);
    const mood = pickSynonym(track.mood, dictionary.synonims[track.mood]) || track.mood;
    const atributes = pickAttr(track.genre, dictionary.genreAtributes[track.genre] || ['']);
    let inst = instruments(track.instruments);
    if (phase === 'first') {
        const group = dictionary.genreGroups[track.genre] || "other";
        prompt = dictionary.patterns[group];
        prompt = prompt.replace("[MOOD]", mood)
        .replace("[GENRE]", track.genre)
        .replace("[INSTRUMENTS]", inst)
        .replace("[ATRIBUTES]", atributes)
        .replace("[TEMPO]", String(track.tempo));
        phase = 'second';
    } 
    else {
        next = getTrack(track);
        const mood2 = pickSynonym(next.mood, dictionary.synonims[next.mood]) || next.mood;
        const atributes2 = pickAttr(next.genre, dictionary.genreAtributes[next.genre] || ['']);
        prompt = dictionary.patterns["between"];
        let inst2 = '';
        inst2 =  instruments(next.instruments);      
        const bpm = String(Math.round((Number(track.tempo) + Number(next.tempo)) / 2));
        prompt = prompt.replace("[MOOD_A]", mood)
        .replace("[MOOD_B]", mood2)
        .replace("[GENRE_A]", track.genre)
        .replace("[GENRE_B]", next.genre)
        .replace("[INSTRUMENTS_A]", inst)
        .replace("[INSTRUMENTS_B]", inst2)
        .replace("[ATRIBUTES_A]", atributes)
        .replace("[ATRIBUTES_B]", atributes2)
        .replace("[TEMPO]", bpm);
        track = next;
        phase = 'first';
    }
    let trPr = [track, prompt, atributes, mood]
    tracksAndPrompts.push(trPr)
    localStorage.setItem('tracksAndPrompts', JSON.stringify(tracksAndPrompts));
    console.log(tracksAndPrompts)
    return prompt
}

function play() {
    console.log("playing")
    if (audio.state === 'suspended') {
        audio.resume();
    }
    const buffer = allBuffer.shift();
    const source = audio.createBufferSource();
    source.buffer = buffer;
    source.connect(audio.destination);
    if (nextStart == 0 || nextStart < audio.currentTime) {
        nextStart = audio.currentTime + 0.1;
    }
    source.start(nextStart);
    nextStart += buffer.duration;
    source.onended = () => {
        if (allBuffer.length > 0) play();
    };
}

function downloadWavFile(audioBytes, fileName) {
    const blob = new Blob([audioBytes], { type: 'audio/wav' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = fileName;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

async function imp(){
    if (isGenerating != true) {
        localStorage.setItem('tracksAndPrompts', '[]');
        localStorage.setItem('allTime', '-1');
    }
    r = await fetch(filepath)
    tracks = await r.json();
    audio = new window.AudioContext({ sampleRate: 32000 });
    //ws = new WebSocket(`ws://localhost:8000/ws?session=${id}`);
    ws = new WebSocket(`${wsUrl}?session=${id}`);
    ws.binaryType = "arraybuffer";
    console.log("good")
    
    ws.onmessage = async (event) => {
        if (typeof event.data === "string") {
            if (event.data == 'next'){
                if (tracksAndPrompts.length == 2){
                    fetch('main.php', {
                        method: 'POST',
                        headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                        },
                        body: new URLSearchParams({ canUseGen: 1}).toString()
                    })
                }
                console.log("next")
                prompt = nextPrompt();
                if (ws.readyState === WebSocket.OPEN) {
                   ws.send(prompt);
                }    
            }
        }
        else{
            const data = new Uint8Array(event.data);
            const what = data[0];
            const audioBytes = event.data.slice(1); 
            switch(what){
                case 1:
                    try {
                        const audioBuffer = await audio.decodeAudioData(audioBytes);
                        allBuffer.push(audioBuffer);
                        allTime += 1;
                        localStorage.setItem('allTime', allTime.toString());
                        play();
                    } catch (e) {
                        console.error("Ошибка декодирования аудио для плеера:", e);
                    }   
                    break;
                case 2:
                    downloadWavFile(audioBytes, 'generated_audio.wav');
                    break;
            }
        }
    };
    ws.onclose = (event) => {
        console.log("Код:", event.code);
    }
    if (isGenerating == true){
      ws.onopen = () => {
        ws.send("play");
      }
    }
}
if (canPlay == true) imp();
