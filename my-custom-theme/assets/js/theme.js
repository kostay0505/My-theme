// assets/js/theme.js
// …существующий код…

document.addEventListener('DOMContentLoaded', function(){
  const mainImg = document.querySelector('.product-main-img');
  const thumbs  = document.querySelectorAll('.product-thumb-img');

  if ( mainImg && thumbs.length ) {
    thumbs.forEach(thumb => {
      thumb.addEventListener('click', function(){
        mainImg.src = this.dataset.full;
        thumbs.forEach(t => t.classList.remove('active'));
        this.classList.add('active');
      });
    });
  }
});

// …возможно, дальше ещё ваш код…
