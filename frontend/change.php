<?php
require_once __DIR__ . '/main.php';
require_once __DIR__ . '/base.php';
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
      <div class = "playlist-win">
        <div class = "playlist-head" ><?= t('uploaded'); ?></div>
        <div class = "close" >
        <button close button >
          <img src = "./images/close.svg" class = "close-icon" img>
        </button>
        </div>
        <div class = "tracks" >
          <div class = "tracklist" >
          </div>
        </div>
        <div class = "add-files" >
          <form method='post' action="/files.php" enctype="multipart/form-data" id="uploadMoreFiles">
            <input type="file" name='file[]'  accept="audio/mp3, audio/flac, audio/wav, audio/ogg" id="Input" multiple hidden>
          </form>
          <button add-files button onclick = "document.getElementById('Input').click();">Add files</button>
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
      fetch('files.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: new URLSearchParams({ name: this.title, weight: this.weight}).toString()
      })
    });
    delBtn.addEventListener("click", () => {
      fetch('files.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: new URLSearchParams({ del: this.title}).toString()
      }).then(() => location.reload())
    })
    }
  }
  let lastUpdate = 0;
  function loadTracks(){
  fetch("updating.php")
  .then(r => r.json())
  .then(({ updated, tracks }) => {
  if (tracks.length == 0) window.location.href = 'upload.php';
  if (updated !== lastUpdate) {
    lastUpdate = updated;
    const trackList = [];
    const container = document.querySelector('.tracklist');
    let i = 1;
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
  setInterval(loadTracks(), 3000);

  const closeBtn = document.querySelector(".close button");
  const input = document.getElementById('fileInput');
  const langLine = document.querySelector(".lang-choose");
  const formInput = document.getElementById('Input');
  let currentLang = "<?= $_SESSION['l']?>";
  let id = "<?= $_SESSION['id']?>"

  if (currentLang == 'ru') langLine.classList.add("ru");

  closeBtn.onclick = () => {
     window.location.href = 'index.php';
  }

  formInput.addEventListener('change', () => {
    if (formInput.files.length > 0) {
      document.getElementById('uploadMoreFiles').submit();
    }
  });

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