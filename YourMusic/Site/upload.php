<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/main.php';
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
      <dialog id="confirmDialog">
        <div class = "big-block">
          <div class = "win-text">Name:</div>
          <input class = "new-name" type="text" placeholder="Playlist's name" id = "name">
        </div>
        <div class = "small-block">
          <div class = "yes-btn">
            <button yes-btn id = 'ok'>Ok</button>
          </div>
          <div class = "skip-btn">
            <button skip-btn id = 'skip'>Skip</button>
          </div>
        </div>
      </dialog>
      <div class = "playlist-win">
        <div class = "playlist-head" ><?= t('uploading'); ?></div>
        <div class = "close" >
        <button close button >
          <img src = "./images/close.svg" class = "close-icon" img>
        </button>
        </div>
        <div class = "upload" >
          <form method='post' action="/files.php" enctype="multipart/form-data" id="uploadForm">
            <input type="file" name='file[]'  accept="audio/mp3, audio/flac, audio/wav, audio/ogg" id="fileInput" multiple hidden>
          </form>
          <button upload button onclick = "document.getElementById('fileInput').click();" >
            <img src = "./images/upload.png" class = "upload-icon" img>
          </button>
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
  const closeBtn = document.querySelector(".close button");
  const input = document.getElementById('fileInput');
  const langLine = document.querySelector(".lang-choose");
  const okBtn = document.getElementById('ok');
  const skipBtn = document.getElementById('skip');
  const namer = document.getElementById('name');
  const autBtn = document.querySelector(".aut");
  let isLogged = "<?= $_SESSION['logged']?>";
  
  let currentLang = "<?= $_SESSION['l']?>";

  if (currentLang == 'ru') langLine.classList.add("ru");

  closeBtn.onclick = () => {
     window.location.href = 'index.php';
  }
  okBtn.onclick = () => {
    if (namer.value.length > 0){
      fetch('main.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: new URLSearchParams({ nextName: namer.value}).toString()
      })
      document.getElementById('uploadForm').submit();
    }
  }
  skipBtn.onclick = () => {
    document.getElementById('uploadForm').submit();
  }

  input.addEventListener('change', () => {
    if (input.files.length > 0) {
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
      confirmDialog.showModal();
    }
  });

  autBtn.onclick = () => {
    if (isLogged == false) window.location.href = 'aut.php';
    else window.location.href = 'profile.php'
  }

</script>
<script src = 'cod.js'></script>