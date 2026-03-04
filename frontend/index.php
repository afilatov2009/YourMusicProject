<?php
require_once __DIR__ . '/main.php';
require_once __DIR__ . '/base.php';
$id = $_SESSION["id"];
$file = "./files/user_$id/player/track.wav";
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
    <div class = "aut">
        <img src = "./images/icon.svg" class = "aut-icon" img>
    </div>
    <div class = "main-btn" >
      <button main-btn button value = 0 ></button>
    </div>
    <div class = "playlist">
        <button playlist button ><?= t('upload')?></button>
    </div>
    <div class = "lang" >
        <div class = "lang-choose" ></div>
        <button lang button value = "ru">RU</button>
        <button lang button value = "en">EN</button>
    </div>
  </section>
</body>
<script>
  const generateBtn = document.querySelector(".main-btn button");
  const backgraund = document.querySelector(".back");
  const bacImage = document.querySelector(".back-image");
  const playlistBtn = document.querySelector(".playlist button");
  const autBtn = document.querySelector(".aut");
  const autIcon = document.querySelector(".aut-icon");
  const langBack = document.querySelector(".lang");
  const langBtn = document.querySelectorAll(".lang button");
  const langLine = document.querySelector(".lang-choose");
  let playlistUploaded = "<?= $_SESSION['playlistUploaded']?>";
  let isGenerating = "<?= $_SESSION['isgen']?>";
  let isLogged = "<?= $_SESSION['logged']?>";
  let currentLang = "<?= $_SESSION['l']?>";
  let id = "<?= $_SESSION['id']?>";

  if (currentLang == 'ru') langLine.classList.add("ru");
  if (playlistUploaded == true) playlistBtn.textContent = '<?= t('change'); ?>';
  if (isGenerating == true) {
    generateBtn.textContent = '<?= t('stop'); ?>';
  }
  else {
    generateBtn.textContent = '<?= t('gen'); ?>';
  }

  if (isGenerating == true){
    generateBtn.classList.add("active");
    backgraund.classList.add("gen");
    bacImage.classList.add("gen");
    autBtn.classList.add("gen");
    autIcon.classList.add("gen");
    playlistBtn.classList.add("active");
    langBtn.forEach(btn => btn.classList.add("gen"))
    langLine.classList.add("gen");
    langBack.classList.add("gen");
    playlistBtn.textContent = '';
  }

  autBtn.onclick = () => {
    if (isGenerating == false){
      if (isLogged == false) window.location.href = 'aut.php';
      else window.location.href = 'profile.php'
    }
  }

  playlistBtn.onclick = () => {
    if (isGenerating == false){
      if (playlistUploaded == false) window.location.href = 'upload.php';
      else window.location.href = 'change.php';
    }
  }
</script>
<script src="generating.js"></script>
<?php if ($_SESSION['isgen'] === false): ?>
  <script src="cod.js"></script>
<?php endif; ?>
