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
      <div class = "aut-head"><?= t('reg'); ?></div>
      <input class = "reg-block" type="text" placeholder=<?= t('newlog'); ?> id = "username">
      <input class = "reg-block" type="password" placeholder=<?= t('newpass'); ?> id = "pass">
      <input class = "reg-block" type="password" placeholder=<?= t('newpass'); ?> id = "confpass">
      <div class = aut-btn>
        <button aut-btn button ><?= t('signup'); ?></button>
      </div>
      <a href="aut.php" class = "to-reg"><?= t('toaut'); ?></a>
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
  const closeBtn = document.querySelector(".close button");
  const langLine = document.querySelector(".lang-choose");
  const usr = document.getElementById("username");
  const newPass = document.getElementById("pass");
  const conPass = document.getElementById("confpass");
  const regBtn = document.querySelector(".aut-btn button");
  const massage = document.querySelector(".massage-win");

  let currentLang = "<?= $_SESSION['l']?>";
  let isGenerating = "<?= $_SESSION['isgen']?>";

  if (currentLang == 'ru') langLine.classList.add("ru");
  function mas(){massage.classList.remove("vis")}

  regBtn.onclick = () => {
    if (newPass.value == conPass.value){
    fetch('registration.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
       username: usr.value,
       password: newPass.value
       })
    }).then(r => r.json()).then(data=> {
      if (data.status === "Yes") {
        window.location.href = 'aut.php';
      }
      if (data.status === "alrCreated") {
        massage.classList.add("vis")
        massage.textContent = "<?= t('already')?>";
        setInterval(mas,5000);
      }
      if (data.status === "shortPass") {
        massage.classList.add("vis")
        massage.textContent = "<?= t('shortPass')?>";
        setInterval(mas,5000);
      }
      if (data.status === "badPass") {
        massage.classList.add("vis")
        massage.textContent = "<?= t('badPass')?>";
        setInterval(mas,5000);
      }
    })
    }
  }

  closeBtn.onclick = () => {
     window.location.href = 'index.php';
  }

</script>
<script src = 'cod.js'></script>