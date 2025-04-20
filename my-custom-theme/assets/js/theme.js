/**
 *  theme.js
 *  ▸  анимация бургер‑кнопки
 *  ▸  (добавляйте свои скрипты здесь)
 */
document.addEventListener('DOMContentLoaded', () => {

  /* ── burger toggle ───────────────── */
  const burger = document.querySelector('.burger-products');
  if (burger){
    burger.addEventListener('click', () => {
      burger.classList.toggle('is-open');
      // здесь можно раскрывать мобильное off‑canvas меню,
      // например: document.querySelector('.mobile-nav').classList.toggle('open');
    });
  }

});
