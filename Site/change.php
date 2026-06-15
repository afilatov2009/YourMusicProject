<?php
require_once __DIR__ . '/main.php';
require_once __DIR__ . '/base.php';
$id = $_SESSION["id"];
$cr = $_SESSION['crplaylist'];
$file = "/files/user_$id/$cr.json"
?>
<!doctype html>
<html lang="<?= $_SESSION['l']?>">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>YourMusic</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
    <section class="back">
      <img src = "./images/back.png" class = "back-image" img>
      <button class="progress-btn active invis">
        <img src="./images/left-arrow.svg" class = "progress-btn-icon">
        <div class="progress-btn-content">
          <span class="progress-btn-text">.</span>
          <div class="progress-btn-progress">
            <div class="progress-btn-bar" style="width: 60%;"></div>
          </div>
        </div>
      </button>
      <div class = "playlist-win">
        <div class = "playlist-head" ><?= t('uploaded'); ?></div>
        <div class = "close" >
          <button close button >
            <img src = "./images/close.svg" class = "close-icon" img>
          </button>
        </div>
        <div class="playlists-panel">
        </div>
        <div class = "tracks" >
          <div class="track-header">
            <div class="th-num">№</div><div class="th-name"><?= t('headername'); ?></div><div class="th-weight"><?= t('weight'); ?></div>
          </div>
          <div class = "tracklist" >
          </div>
        </div>
        <div class = "add-files" >
          <div class="ok-btn">
            <button ok-btn button >
              <img src = "./images/tick.svg" class = "tick" img>
            </button>
          </div>
          <input class = "playlist-name" type="text" placeholder="ААААА" id = "name">
          <form method='post' action="/files.php" enctype="multipart/form-data" id="uploadMoreFiles">
            <input type="file" name='addfile[]'  accept="audio/mp3, audio/flac, audio/wav, audio/ogg" id="Input" multiple hidden>
          </form>
          <button add-files button onclick = "document.getElementById('Input').click();"><?= t('addfiles'); ?></button>
        </div>
      </div>
      <div class = "aut">
        <img src = "./images/icon.svg" class = "aut-icon" img>
      </div>
      <div class = "lang" >
         <div class = "lang-choose" ></div>
         <button lang button value = "ru">RU</button>
         <button lang button value = "en">EN</button>
      </div>
<script>
  let canPlay = "<?= $_SESSION['canPlay']?>";
  let filepath = "<?=$file?>";
  let id = "<?= $_SESSION['id']?>";
  let isGenerating = "<?= $_SESSION['isgen']?>";
</script>
<script src="ratingSystem.js"></script>
<script src ='generating.js'></script>
<script>
  let plNum = Number("<?= $_SESSION['crplaylist']?>")
  let isRanging = false;
  let timeout = null;
  let secondTimeout = null;
  class Track {
    constructor({ title, weight}) {
    this.title = title;
    this.weight = weight;
    }
    render(container, val) {
    const el = document.createElement('div');
    el.className = 'track';
    el.innerHTML = `
      <div class = "track-num" >${val}</div>
      <div class = "text-cont" >
        <pre><div class = "track-text" >${this.title}</div></pre>
      </div>
      <div class = "ranger" >
        <input class = "weight" type="range" min="1" max="5" step="1" value="${this.weight}" id="weightRange">
        <span class = "weightValue">${this.weight}</span>
      </div>
      <div class = "del-file" >
        <button del-file button >
          <img src ="./images/del.svg" class = "del-icon" img>
        </button>`;
    container.appendChild(el);
    const range = el.querySelector('input[type="range"]');
    const value = el.querySelector('.weightValue');
    const delBtn = el.querySelector('.del-file button');
    const percent = (range.value - range.min) / (range.max - range.min);
    const offset = percent * range.offsetWidth;
    value.style.left = `${offset}px`;

    range.addEventListener('input', () => {
      this.weight = Number(range.value);
      value.textContent = this.weight;
      const percent = (range.value - range.min) / (range.max - range.min);
      const offset = percent * range.offsetWidth;
      value.style.left = `${offset}px`;
      isRanging = true;
      clearTimeout(timeout);
      clearTimeout(secondTimeout);
      timeout = setTimeout(() => {
        fetch('files.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
          },
          body: new URLSearchParams({ name: this.title, weight: this.weight }).toString()
        });
      }, 300);
      secondTimeout = setTimeout(() => {
        isRanging = false;
      }, 2000);
    });
    delBtn.addEventListener("click", () => {
      fetch('files.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: new URLSearchParams({ del: this.title}).toString()
      }).then(response => response.text()).then(data => {
          const result = data.trim();
          console.log(result);
          if (result === "No playlist") window.location.href = 'index.php';
          else location.reload();
        })
    })
    }
  }
  let lastUpdate = 0;
  let trackList = [];
  function loadTracks(){
    if (isRanging == false){
      fetch("updating.php")
      .then(r => r.json())
      .then(({ updated, tracks }) => {
        if (tracks.length == 0) window.location.href = 'upload.php';
        if (Number(updated) !== Number(lastUpdate)) {
          lastUpdate = updated;
          const container = document.querySelector('.tracklist');
          let i = 1;
          container.innerHTML = '';   
          trackList = [];
          tracks.forEach(item => {
            const track = new Track(item);
            trackList.push(track);
            track.render(container, i);
            i++;
            ScrollingTitle(container.lastElementChild.querySelector('.text-cont'),track.title);
          });
        }
      });
    }
  }
  class Playlist{
    constructor(num, active) {
    this.num = num;
    this.active = active;
    }
    main(cont,list){
      const el = document.createElement('div');
      el.innerHTML = `
        <span>${list[this.num]}</span>
        <div class = "delete-playlist">
          <button delete-playlist button><img src="./images/close1.svg" class="pl-del-icon"></button>
        </div>`;
      if (this.active == true) {
        el.className = 'playlist-item active';
      }
      else{
        el.className = 'playlist-item';
      }
      cont.appendChild(el)
      const del = el.querySelector(".delete-playlist button")
      el.addEventListener('click', () => {
        if (el.className == 'playlist-item' && el.classList.contains("active") == false){
          fetch('main.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: new URLSearchParams({ canUseGen: 0}).toString()
          })
          const stopWs = new WebSocket(`${wsUrl}?session=${id}`);
          stopWs.onopen = () => {
            localStorage.setItem('tracksAndPrompts', '[]');
            resetTimer();
            stopWs.send('stopSession');
          };
          stopWs.onclose = () => {
            fetch('main.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
              },
              body: new URLSearchParams({newCur: this.num}).toString()
            }).then(() => location.reload());
          };
        }
      })
      del.addEventListener('click', () => {
        fetch('main.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
          },
          body: new URLSearchParams({ canUseGen: 0}).toString()
        })
        const stopWs2 = new WebSocket(`${wsUrl}?session=${id}`);
        stopWs2.onopen = () => {
          localStorage.setItem('tracksAndPrompts', '[]');
          resetTimer();
          stopWs2.send('stopSession');
        };
        stopWs2.onclose = () => {
          fetch('files.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
              },
              body: new URLSearchParams({delPlaylist: this.num}).toString()
          }).then(response => response.text()).then(data => {
            const result = data.trim();
            console.log(result);
            if (result === "No playlist") window.location.href = 'index.php';
            else location.reload();
          });
        };
      })
    }
  }
  const closeBtn = document.querySelector(".close button");
  const input = document.getElementById('fileInput');
  const langLine = document.querySelector(".lang-choose");
  const formInput = document.getElementById('Input');
  const plname = document.getElementById("name");
  const tick = document.querySelector(".ok-btn button");
  const plsts = document.querySelector(".playlists-panel");
  const autBtn = document.querySelector(".aut");
  const progressBtn = document.querySelector(".progress-btn");
  const progressIcn = document.querySelector(".progress-btn-icon");
  const progressBtnText = document.querySelector(".progress-btn-text");
  const progressBtnBar = document.querySelector(".progress-btn-bar");
  let isLogged = "<?= $_SESSION['logged']?>";
  let currentLang = "<?= $_SESSION['l']?>";
  let crnum = Number("<?= $_SESSION['crplaylist']?>");
  var listOfPlaylists = <?= json_encode($_SESSION['plList']);?>;
  var listOfNames = [];
  var List = [];
  let isAnalyzing = "<?= $_SESSION['isAnalyzingNow']?>";
  let AnalyzingProgress = "<?= $_SESSION['progress']?>";
  function refreshProgress(){
    fetch('main.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
          },
          body: new URLSearchParams({ getProgress: 1}).toString()
    }).then(r => r.json()).then(data => {
      isAnalyzing = data.anNow;
      AnalyzingProgress = data.progress;
      progressBtnText.textContent = "<?= t('analyzingPr')?>" + isAnalyzing;
      progressBtnBar.style.width = AnalyzingProgress + '%';
      if (isAnalyzing == '') progressBtn.classList.add("invis");
      else progressBtn.classList.remove("invis");
    });
  }
  refreshProgress();
  loadTracks();
  setInterval(refreshProgress, 1000);
  setInterval(loadTracks, 3000);  
  for (i = 0; i<listOfPlaylists.length;i++){
    listOfNames[i] = listOfPlaylists[i][0]
  }
  for (i = 0; i<listOfNames.length;i++){
    if (i == crnum) {
      const playlist = new Playlist(i,true);
      playlist.main(plsts,listOfNames)
    }
    else{
      List.push(i);
    }
  }
  for (i = 0; i<List.length;i++){
    const playlist = new Playlist(List[i],false);
    playlist.main(plsts,listOfNames)
  }
  const adder = document.createElement('div');
  adder.className = 'playlist-item';
  adder.innerHTML = `
      <span><?= t('addnew'); ?></span>
      <img src="./images/plus.svg" class="plus-icon">`;
  plsts.appendChild(adder);
  if (plsts.scrollWidth > plsts.clientWidth){
    const el = document.createElement('div');
    el.className = 'playlist-blur';
    plsts.removeChild(adder)
    plsts.appendChild(el);
    adder.className = 'playlist-item adder'
    plsts.appendChild(adder);
  }

  if (currentLang == 'ru') langLine.classList.add("ru");
  plname.value = listOfNames[crnum];

  closeBtn.onclick = () => {
    window.location.href = 'index.php';
  }

  adder.onclick = () => {
    window.location.href = 'upload.php';
  }

  tick.onclick = () => {
    fetch('main.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: new URLSearchParams({newName: plname.value}).toString()
    }).then(location.reload())
  }

  progressBtn.onclick = () => {
    progressBtn.classList.toggle("active");
    if (progressBtn.classList.contains("active")) progressIcn.src = "./images/left-arrow.svg";
    else progressIcn.src = "./images/right-arrow.svg";
  }

  formInput.addEventListener('change', () => {
    if (formInput.files.length > 0) {
      document.getElementById('uploadMoreFiles').submit();
    }
  });

  autBtn.onclick = () => {
    if (isLogged == false) window.location.href = 'aut.php';
    else window.location.href = 'profile.php'
  }

  function ScrollingTitle(el,text) {
    const title = el.querySelector('.track-text');
    if (title.scrollWidth > el.clientWidth) {
      title.classList.add('scroll');
      title.innerHTML = `${text}    ${text}`;
      const time = title.scrollWidth / 35;
      title.style.animationDuration = `${time}s`;
    }
  }
</script>
<script src = 'cod.js'></script>