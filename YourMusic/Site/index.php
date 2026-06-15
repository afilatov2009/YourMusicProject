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
    <div class = "massage" >
      <div class = "massage-win"></div>
    </div>
    <button class="progress-btn active invis">
      <img src="./images/left-arrow.svg" class = "progress-btn-icon">
      <div class="progress-btn-content">
        <span class="progress-btn-text">.</span>
        <div class="progress-btn-progress">
          <div class="progress-btn-bar" style="width: 60%;"></div>
        </div>
      </div>
    </button>
    <div class = "aut">
        <img src = "./images/icon.svg" class = "aut-icon" img>
    </div>
    <div class = "main-btn" >
      <div class = 'dislike-btn'>
        <button dislike-btn button>
            <img src = "./images/dislike.svg" class = "like-icon" img>
        </button>
      </div>
      <button class = 'gen-btn'><?= t('gen'); ?></button>
      <div class = 'like-btn'>
        <button like-btn button>
          <img src = "./images/like.svg" class = "like-icon" img>
        </button>
      </div>
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
  const generateBtn = document.querySelector(".gen-btn");
  const backgraund = document.querySelector(".back");
  const bacImage = document.querySelector(".back-image");
  const playlistBtn = document.querySelector(".playlist button");
  const autBtn = document.querySelector(".aut");
  const autIcon = document.querySelector(".aut-icon");
  const langBack = document.querySelector(".lang");
  const langBtn = document.querySelectorAll(".lang button");
  const langLine = document.querySelector(".lang-choose");
  const progressBtn = document.querySelector(".progress-btn");
  const progressIcn = document.querySelector(".progress-btn-icon");
  const progressBtnText = document.querySelector(".progress-btn-text");
  const progressBtnBar = document.querySelector(".progress-btn-bar");
  const massage = document.querySelector(".massage-win");
  const likeBtn = document.querySelector(".like-btn button");
  const dislikeBtn = document.querySelector(".dislike-btn button");
  const likeIcon = document.querySelectorAll(".like-icon");
  let playlistUploaded = "<?= $_SESSION['playlistUploaded']?>";
  let canPlay = "<?= $_SESSION['canPlay']?>";
  let canUseGen = "<?= $_SESSION['canUseGen']?>";
  let isGenerating = "<?= $_SESSION['isgen']?>";
  let isLogged = "<?= $_SESSION['logged']?>";
  let currentLang = "<?= $_SESSION['l']?>";
  let filepath = "<?=$file?>";
  let id = "<?= $_SESSION['id']?>";
  let isAnalyzing = "<?= $_SESSION['isAnalyzingNow']?>";
  let AnalyzingProgress = "<?= $_SESSION['progress']?>";
  let warning = '';
  function mas(){
    massage.classList.remove("vis")
  }
  function refreshCanPlay(){
    canUseGen = "<?= $_SESSION['canUseGen']?>";
  }
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
      canUseGen = data.canUseGen;
      if (!canUseGen) generateBtn.classList.add("inactive");
      else generateBtn.classList.remove("inactive");
    });
  }
  refreshProgress();
  setInterval(refreshProgress, 1000);
  if (currentLang == 'ru') langLine.classList.add("ru");
  if (playlistUploaded == true) {
    playlistBtn.textContent = '<?= t('change'); ?>';
    if (canPlay == false) warning = '<?= t('waitToPlay'); ?>'
    else warning = '<?= t('waitForGen'); ?>'
  }
  else warning = '<?= t('uploadToPlay'); ?>';
  if (canPlay == false) generateBtn.classList.add("inactive");
  if (isGenerating == true){
    generateBtn.textContent = '<?= t('stop'); ?>';
    massage.classList.add("dark")
    generateBtn.classList.add("active");
    backgraund.classList.add("gen");
    bacImage.classList.add("gen");
    autBtn.classList.add("gen");
    autIcon.classList.add("gen");
    playlistBtn.classList.add("active");
    likeBtn.classList.add("active");
    dislikeBtn.classList.add("active");
    likeIcon.forEach(i => i.classList.add("active"));
    langBtn.forEach(btn => btn.classList.add("gen"))
    langLine.classList.add("gen");
    langBack.classList.add("gen");
    playlistBtn.textContent = '';
    const icon = document.createElement('img');
    icon.className = 'playlist-icon';
    icon.src = "./images/download.svg";
    playlistBtn.appendChild(icon);
    const tip = document.createElement('div');
      tip.className = 'download-tip';
      tip.textContent = '<?= t('download'); ?>';
    playlistBtn.addEventListener('mouseenter', () => {
      playlistBtn.appendChild(tip);
      playlistBtn.classList.add('wide');
    });
    playlistBtn.addEventListener('mouseleave', () => {
      playlistBtn.removeChild(tip);
      playlistBtn.classList.remove('wide');
    });
  }
  else {
    generateBtn.textContent = '<?= t('gen'); ?>';
  }

  autBtn.onclick = () => {
    if (isGenerating == false){
      if (isLogged == false) window.location.href = 'aut.php';
      else window.location.href = 'profile.php'
    }
  }

  progressBtn.onclick = () => {
    progressBtn.classList.toggle("active");
    if (progressBtn.classList.contains("active")) progressIcn.src = "./images/left-arrow.svg";
    else progressIcn.src = "./images/right-arrow.svg";
  }
</script>
<script src="ratingSystem.js"></script>
<script src="generating.js"></script>
<script>
generateBtn.onclick = () => {
  if (canUseGen == true){
      if (isGenerating == true){
          fetch('files.php', {
              method: 'POST',
              headers: {
              'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
              },
              body: new URLSearchParams({ zero: 0}).toString()
          })
          fetch('main.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: new URLSearchParams({ canUseGen: 0}).toString()
          })
          localStorage.setItem('tracksAndPrompts', '[]');
          resetTimer()
          ws.send('stopSession');
      }
      fetch('main.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
          },
          body: new URLSearchParams({ gen: 1}).toString()
      }).then(() => location.reload())
  }
  else{
      massage.textContent = warning;
      massage.classList.add("vis");
      setInterval(mas,5000);
  }
}

playlistBtn.onclick = () => {
    if (isGenerating == false){
      if (playlistUploaded == false) window.location.href = 'upload.php';
      else window.location.href = 'change.php';
    }
    else{
        ws.send('download');
    }
}

likeBtn.onclick = () => {
    console.log('like')
    let index =  getIndex();
    console.log(index, tracksAndPrompts[index])
    like(tracksAndPrompts[index])
    localStorage.setItem('tracksAndPrompts', JSON.stringify(tracksAndPrompts));
    massage.textContent = '<?= t('like'); ?>';
    massage.classList.add("vis");
    setInterval(mas,5000);
}

dislikeBtn.onclick = () => {
    console.log('dislike')
    let index =  getIndex();
    console.log(index, tracksAndPrompts[index])
    dislike(tracksAndPrompts[index])
    localStorage.setItem('tracksAndPrompts', JSON.stringify(tracksAndPrompts));
    massage.textContent = '<?= t('dislike'); ?>';
    massage.classList.add("vis");
    setInterval(mas,5000);
}
</script>
<?php if ($_SESSION['isgen'] === false): ?>
  <script src="cod.js"></script>
<?php endif; ?>
