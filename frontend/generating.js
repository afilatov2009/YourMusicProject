const dictionary = {
    synonims: {
        "relaxed": ["serene", "tranquil", "dreamy", "mellow", "placid"],
        "energetic": ["pulsating", "dynamic", "vibrant", "driving", "fierce"],
        "mysterious": ["shadowy", "enigmatic", "noir", "veiled", "obscure"], 
        "uplifting": ["radiant", "euphoric", "cheerful", "bright", "joyful"],  
        "melancholic": ["haunting", "somber", "wistful", "gloomy", "pensive"], 
        "aggressive": ["gritty", "intense", "raw", "rugged", "heavy"],         
        "romantic": ["intimate", "velvety", "tender", "sensual", "soft"],      
        "cinematic": ["epic", "grand", "majestic", "sweeping", "heroic"], 
        "nostalgic": ["vintage", "retro", "sepia-toned", "old-school", "dusty"], 
        "futuristic": ["cybernetic", "alien", "glitchy", "high-tech", "neon"] 
    },

    genreAtributes: {
    "Lo-Fi": ["rainy window background", "warm coffee shop ambience", "soft bedroom hum", "distant city night sounds", "crinkling paper ATHMs"],
    "Jazz": ["smoky bar ATRIBUTES", "clinking glasses and plates", "intimate lounge reverb", "warm wooden stage", "soft evening rain"],
    "Ambient": ["endless space void", "deep ocean silence", "morning mountain fog", "ethereal spirit world", "drifting clouds"],
    "Folk": ["crackling campfire", "dry autumn leaves", "old porch wooden creak", "mountain river flow", "handcrafted feel"],
    "Classical": ["grand orchestra hall", "silent library air", "royal palace acoustics", "wooden violin resonance", "breathtaking height"],
    "Soul": ["warm vinyl glow", "candlelit room", "soft velvet curtains", "deep emotional resonance", "smooth golden era sound"],
    "Techno": ["cold industrial warehouse", "throbbing strobe lights", "mechanical factory hum", "underground club reverb", "digital pulse static"],
    "Synthwave": ["1980s neon city", "retro arcade buzz", "analog laser hum", "driving fast on highway", "purple sunset glow"],
    "House": ["sunny beach club", "glimmering disco ball", "pumping dancefloor energy", "chic rooftop party", "summer breeze rhythm"],
    "Dubstep": ["gritty urban wasteland", "heavy electric distortion", "deep subterranean bass", "shattering glass ATHMs", "robotic machinery"],
    "Cyberpunk": ["rain-slicked neon streets", "high-tech low-life buzz", "hacked satellite signal", "dark artificial intelligence", "flying car engine hum"],
    "IDM": ["complex glitch patterns", "abstract digital landscape", "microscopic sound clicks", "brain-stimulating ATHMs", "unfolding geometric shapes"],
    "Rock": ["raw garage acoustics", "loud stadium echo", "gritty basement feel", "crowd cheering energy", "distorted air vibration"],
    "Metal": ["dark thunderstorm power", "heavy iron foundry", "aggressive mosh pit energy", "hellish fire crackle", "sharp metallic serration"],
    "Punk": ["rebellious basement grit", "DIY underground club", "raw distorted energy", "chaotic street protest", "lo-fi protest spirit"],
    "Hip-Hop": ["street basketball court", "heavy sub-bass rumble", "urban traffic noise", "vinyl scratching surface", "boombox power"],
    "Trap": ["dark luxury mansion", "heavy 808 sub rumble", "icy diamond shimmer", "street corner midnight", "polished dark chrome"],
    "Reggae": ["sunny tropical beach", "slow island breeze", "sweet herbal haze", "wooden percussion reverb", "relaxed seaside shack"],
    "World": ["distant desert dunes", "ancient tribal drums", "exotic jungle canopy", "bustling spice market", "mystic ritual chants"],
    "Blues": ["dusty crossroads wind", "old Mississippi porch", "weary traveler spirit", "worn-out slide guitar echo", "midnight railroad hum"],
    "Funk": ["70s disco dancefloor", "bright brass sections", "grooveline basement party", "shimmering sequins", "saturated rhythmic energy"],
    "Country": ["wide open prairie", "dusty horseback trail", "old wooden barn dance", "southern sunset glow", "rustic outdoor picnic"],
    },

    genreGroups: {
    "Lo-Fi": "atmospheric", "Ambient": "atmospheric", "Jazz": "atmospheric", "Soul": "atmospheric",
    "Techno": "electronic", "House": "electronic", "IDM": "electronic", "Synthwave": "electronic", "Cyberpunk": "electronic",
    "Hip-Hop": "ritmy", "Trap": "ritmy", "Dubstep": "ritmy", "Funk": "ritmy",
    "Rock": "heavy", "Metal": "heavy", "Punk": "heavy", "Blues": "heavy",
    "Classical": "organic", "Folk": "organic", "World": "organic"
    },

    patterns: {
    "atmospheric": "A deeply [MOOD] [GENRE] soundscape. Focus on [INSTRUMENTS] with [ATRIBUTES]. [TEMPO] BPM.",
    "electronic": "Professional [MOOD] [GENRE] track for a club. Dominant [BEAT] beat in [TEMPO] BPM, [INSTRUMENTS] with [ATRIBUTES].",
    "ritmy": "Gritty [MOOD] [GENRE] with a heavy [BEAT]. Featuring [INSTRUMENTS]. [ATRIBUTES], [TEMPO] BPM. High-end bass production.",
    "heavy": "Raw and powerful [MOOD] [GENRE] music. [INSTRUMENTS] play with energy. Elements of [ATRIBUTES], [TEMPO] BPM. Recorded in a concert style.",
    "organic": "A peaceful [MOOD] [GENRE] composition with [ATRIBUTES]. Arrangement featuring [INSTRUMENTS] in [TEMPO] BPM.",
    "other": "High-quality [GENRE] music in [MOOD] mood with [INSTRUMENTS]. Recorded with [BEAT] in [TEMPO] BPM. Professional studio sound.",
    "between": "Evolving musical bridge between [GENRE_A] and [GENRE_B]. Start with [MOOD_A] atmosphere, then introduce [MOOD_B] patterns. Blending [INSTRUMENTS_A], [BEAT_A] in the beginning and [INSTRUMENTS_B], [BEAT_B] in the end.In [TEMPO] BPM tempo with some [ATRIBUTES_A] and some [ATRIBUTES_B]."
   }
};

let audio;
let allBuffer = [];
let nextStart = 0;
let ws;
let isPlaying = false;
let tracks = [];
let prompt = 'New-age ambient music, slow to mid-tempo (80–100 BPM), evolving and atmospheric,soft synthesizer pads and airy flute melodies, calm and meditative mood,no drums, no beat, no percussion, smooth progression, spacious and warm soundscape.';
let track = null;
let next = null;
let phase = 'first'; 

const getTrack = () => {
    let min = 100
    let totalWeight = 0
    for (let track of tracks) {
        if (track.inPlay < min) min = track.inPlay;
    }
    for (let track of tracks) {
        if (track.inPlay == min) totalWeight += track.weight;
    }
    let random = Math.random() * totalWeight;
    let sum = 0;
    for (let track of tracks) {
        if (track.inPlay == min){
            sum += (track.weight);
            if (random <= sum) {
               track.inPlay +=1
               fetch('files.php', {
                    method: 'POST',
                    headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: new URLSearchParams({ playCount: track.title}).toString()
               })
               return track;
        }
       }
    }
};

function nextPrompt() {
    if (!track) track = getTrack();
    let prompt = '';
    const pick = (arr) => arr[Math.floor(Math.random() * arr.length)];
    const mood = pick(dictionary.synonims[track.mood] || [track.mood]) ;
    const atributes = pick (dictionary.genreAtributes[track.genre] || [""]);
    if (phase === 'first') {
        const group = dictionary.genreGroups[track.genre] || "other";
        prompt = dictionary.patterns[group];
        let inst = "";
        if (track.instruments.length > 1) inst = track.instruments.slice(0, -1).join(", ") + " and " + track.instruments.slice(-1);
        else inst = track.instruments[0];
        prompt = prompt.replace("[MOOD]", mood)
        .replace("[GENRE]", track.genre)
        .replace("[INSTRUMENTS]", inst)
        .replace("[ATRIBUTES]", atributes)
        .replace("[BEAT]", track.beat || "no beat")
        .replace("[TEMPO]", String(track.tempo));
        phase = 'second';
    } 
    else {
        next = getTrack();
        const mood2 = pick(dictionary.synonims[next.mood] || [next.mood]);
        const atributes2 = pick(dictionary.genreAtributes[next.genre] || [""]);
        prompt = dictionary.patterns["between"];
        let inst = '';
        let inst2 = '';
        if (track.instruments.length > 1) inst = track.instruments[0] + " and " + track.instruments[1]; 
        else inst = track.instruments[0];
        if (next.instruments.length > 1) inst2 = next.instruments[0] + " and " + next.instruments[1];
        else inst2 =  next.instruments[0];      
        const bpm = String(Math.round((Number(track.tempo) + Number(next.tempo)) / 2));
        prompt = prompt.replace("[MOOD_A]", mood)
        .replace("[MOOD_B]", mood2)
        .replace("[GENRE_A]", track.genre)
        .replace("[GENRE_B]", next.genre)
        .replace("[INSTRUMENTS_A]", inst)
        .replace("[INSTRUMENTS_B]", inst2)
        .replace("[ATRIBUTES_A]", atributes)
        .replace("[ATRIBUTES_B]", atributes2)
        .replace("[BEAT_A]", track.beat || "no beat")
        .replace("[BEAT_B]", next.beat || "no beat")
        .replace("[TEMPO]", bpm);
        track = next;
        phase = 'first';
    }
    return prompt
}


generateBtn.onclick = () => {
    if (isGenerating == true){
        fetch('files.php', {
            method: 'POST',
            headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: new URLSearchParams({ zero: 0}).toString()
        })
    }
    fetch('main.php', {
       method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: new URLSearchParams({ gen: 1}).toString()
    }).then(() => location.reload())
}


function play() {
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


async function imp(){
    r = await fetch(`files/user_${id}/files.json`)
    tracks = await r.json();
    audio = new window.AudioContext();
    ws = new WebSocket(`ws://46.242.127.90:8000/ws?session=${id}`);
    ws.binaryType = "arraybuffer";
    
    ws.onmessage = async (event) => {
        if (typeof event.data === "string") {
            prompt = nextPrompt();
            if (ws.readyState === WebSocket.OPEN) {
               ws.send(prompt);
            }    
        }
        else{
            const audioBuffer = await audio.decodeAudioData(event.data);
            allBuffer.push(audioBuffer);
            fetch('log.php', {
            method: 'POST',
            headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: new URLSearchParams({ buffer: allBuffer.length}).toString()
            })
            play();
        }
    };
    if (isGenerating == true){
        ws.onopen = () => {
            ws.send("play");
        }
    }
}
if (playlistUploaded == true) imp();
