document.addEventListener('DOMContentLoaded', () => {
  const langBtn = document.querySelectorAll(".lang button");
  const langLine = document.querySelector(".lang-choose");
  let currentLang = document.documentElement.lang;

  langBtn.forEach(btn => {
    btn.addEventListener('click', () => {
      if (btn.value != currentLang) {
        currentLang = btn.value;
        langLine.classList.toggle('ru');
        fetch('main.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: new URLSearchParams({ lan: currentLang }).toString()
      }).then(() => location.reload())
    }
    });
  });
});

