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
    <div class = "aut-win">
      <div class = "close" >
        <button close button >
          <img src = "./images/close.svg" class = "close-icon" img>
        </button>
      </div>
      <div class = "aut-head"><?= t('profile'); ?></div>
      <div class = "info" >
        <div class = "info-text" id = "username"></div>
        <div class = "info-text">
          <div class= "in-info" id = "playlist" ></div>
          <a class = "in-info" id = 'plHref'></a>
        </div>
        <div class = "info-text">
          <div class= "in-info" id = "crplaylist" ></div>
          <a class = "in-info" id = 'crPlHref'></a>
        </div>
        <div class = "info-text">
          <div class= "in-info" id = "langInfo" ></div>
        </div>
        <div class = "info-text">
          <button info-text button id = 'logout'><?= t('logOut'); ?></button>
        </div>
      </div>
    </div>
    <img src = "./images/back.png" class = "back-image" img>
    <div class = "massage" >
      <div class = "massage-win"></div>
    </div>
    <div class = "aut">
        <img src = "./images/icon.svg" class = "aut-icon" img>
    </div>
    <div class = "lang" >
        <div class = "lang-choose" ></div>
        <button lang button value = "ru">RU</button>
        <button lang button value = "en">EN</button>
    </div>
  </section>
</body>
<script>
  const massage = document.querySelector(".massage-win");
  const user = document.getElementById("username");
  const langLine = document.querySelector(".lang-choose");
  const playlistInfo = document.getElementById("playlist");
  const crPlaylistName = document.getElementById("crplaylist");
  const playlistLink = document.getElementById("plHref");
  const crPlaylistLink = document.getElementById("crPlHref");
  const langInfo = document.getElementById("langInfo");
  const lgoutBtn = document.getElementById("logout");
  const closeBtn = document.querySelector(".close button");
  let currentLang = "<?= $_SESSION['l']?>";
  let username = "<?= $_SESSION['username']?>";
  let isGenerating = "<?= $_SESSION['isgen']?>";
  let playlist = "<?= $_SESSION['playlistUploaded']?>";
  let canPlay = "<?= $_SESSION['canPlay']?>";
  let filepath = "<?=$file?>";
  let id = "<?= $_SESSION['id']?>";
  if (currentLang == 'ru') {
    langLine.classList.add("ru");
    langInfo.textContent = "Язык: Русский";
  }
  else langInfo.textContent = "Language: English";
  user.textContent = "<?= t('usname'); ?>" + username;
  <?php if ($_SESSION['playlistUploaded'] === true): ?>
    let curPlaylistName = "<?= $_SESSION['plList'][$_SESSION['crplaylist']][0]?>";
    let plCount = "<?= count($_SESSION['plList'])?>";
    playlistInfo.textContent = `<?= t('plUpInfo'); ?> ${plCount}`;
    playlistLink.href = "upload.php";
    playlistLink.textContent = "<?= t('plUpLink'); ?>";
    crPlaylistName.textContent = "<?= t('currPl'); ?>";
    crPlaylistLink.href = "change.php";
    crPlaylistLink.textContent = `${curPlaylistName}`;
  <?php else: ?>
    playlistInfo.textContent = "<?= t('plNUpInfo'); ?>";
    playlistLink.href = "upload.php";
    playlistLink.textContent = "<?= t('plNUpLink'); ?>";
    crPlaylistName.textContent = `<?= t('noCurrPl'); ?>`;
    crPlaylistLink.href = "";
    playlistLink.textContent = ``;
  <?php endif; ?>

  closeBtn.onclick = () => {
    window.location.href = 'index.php';
  }
</script>
<script src="ratingSystem.js"></script>
<script src ='generating.js'></script>
<script>
  lgoutBtn.onclick = () => {
    fetch('main.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
      },
      body: new URLSearchParams({ canUseGen: 1}).toString()
    })
    const stopWs = new WebSocket(`${wsUrl}?session=${id}`);
    stopWs.onopen = () => {
      localStorage.setItem('tracksAndPrompts', '[]');
      resetTimer();
      stopWs.send('stopSession');
    };
    fetch('logout.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
      },
      body: new URLSearchParams({ logout: "1"}).toString()
    }).then(() => window.location.href = 'index.php')
  }
</script>
<script src = 'cod.js'></script>