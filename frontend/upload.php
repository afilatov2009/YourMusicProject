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
  const closeBtn = document.querySelector(".close button");
  const input = document.getElementById('fileInput');
  const langLine = document.querySelector(".lang-choose");
  let isGenerating = "<?= $_SESSION['isgen']?>";
  
  let currentLang = "<?= $_SESSION['l']?>";

  if (currentLang == 'ru') langLine.classList.add("ru");

  closeBtn.onclick = () => {
     window.location.href = 'index.php';
  }

  input.addEventListener('change', () => {
    if (input.files.length > 0) {
      document.getElementById('uploadForm').submit();
    }
  });

</script>
<script src = 'cod.js'></script>