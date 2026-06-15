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
    <div class = "aut-win">
      <div class = "close" >
        <button close button >
          <img src = "./images/close.svg" class = "close-icon" img>
        </button>
      </div>
      <div class = "aut-head"><?= t('aut'); ?></div>
      <input class = "aut-block" type="text" placeholder=<?= t('log'); ?> id = "username">
      <input class = "aut-block"  type="password" placeholder=<?= t('pass'); ?> id = "pass">
      <div class = aut-btn>
        <button aut-btn button ><?= t('login'); ?></button>
      </div>
      <a href="reg.php" class = "to-reg"><?= t('toreg'); ?></a>
    </div>
    <img src = "./images/back.png" class = "back-image" img>
    <div class = "massage" >
      <div class = "massage-win warning"></div>
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
  let canPlay = "<?= $_SESSION['canPlay']?>";
  let filepath = "<?=$file?>";
  let id = "<?= $_SESSION['id']?>";
  let isGenerating = "<?= $_SESSION['isgen']?>";
</script>
<script src="ratingSystem.js"></script>
<script src ='generating.js'></script>
<script>
  const closeBtn = document.querySelector(".close button");
  const langLine = document.querySelector(".lang-choose");
  const autBtn = document.querySelector(".aut-btn button");
  const usr = document.getElementById("username");
  const pass = document.getElementById("pass");
  const massage = document.querySelector(".massage-win");
  let currentLang = "<?= $_SESSION['l']?>";

  if (currentLang == 'ru') langLine.classList.add("ru");
  function mas(){massage.classList.remove("vis")}

  autBtn.onclick = () => {
    fetch('login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
       username: usr.value,
       password: pass.value
       })
    }).then(r => r.json()).then(data=> {
      if (data.status === "Yes") {
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
        window.location.href = 'index.php';
      }
      if (data.status === "Wrong") {
        massage.classList.add("vis")
        massage.textContent = "<?= t('wrong')?>";
        setInterval(mas,5000);
      }
    })
  }

  closeBtn.onclick = () => {
     window.location.href = 'index.php';
  }

</script>
<script src = 'cod.js'></script>