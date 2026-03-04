<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/main.php';
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
      <bitton logout button id = "lgout">Logout</bitton>
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
  const lgoutBtn = document.getElementById("lgout");

  let currentLang = "<?= $_SESSION['l']?>";
  if (currentLang == 'ru') langLine.classList.add("ru");

  lgoutBtn.onclick = () => {
    fetch('logout.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
      },
      body: new URLSearchParams({ logout: "1"}).toString()
    }).then(window.location.href = 'index.php')
  }

  closeBtn.onclick = () => {
    window.location.href = 'index.php';
  }

</script>
<script src = 'cod.js'></script>