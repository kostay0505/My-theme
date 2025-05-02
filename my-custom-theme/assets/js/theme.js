// assets/js/theme.js
// Главный скрипт темы

document.addEventListener('DOMContentLoaded', function() {
  // 1. Галерея товара — переключение главного изображения
  const mainImg = document.querySelector('.product-main-img');
  const thumbs = document.querySelectorAll('.product-thumb-img');
  if ( mainImg && thumbs.length ) {
    thumbs.forEach(thumb => {
      thumb.addEventListener('click', function() {
        mainImg.src = this.dataset.full;
        thumbs.forEach(t => t.classList.remove('active'));
        this.classList.add('active');
      });
    });
  }

  // 2. Инициализация каруселей с помощью Splide.js
  const splides = document.querySelectorAll('.carousel-splide');
  if ( splides.length && typeof Splide !== 'undefined' ) {
    splides.forEach(el => {
      new Splide(el, {
        type       : 'loop',
        perPage    : 4,
        gap        : '1rem',
        arrows     : true,
        pagination : true,
        breakpoints: {
          1024: { perPage: 3 },
          768 : { perPage: 2 },
          480 : { perPage: 1 },
        },
      }).mount();
    });
  }

  // 3. Live-поиск по товарам в фильтрационном блоке
  const searchInput = document.getElementById('product-search-input');
  const suggestionsBox = document.getElementById('search-suggestions');
  if ( searchInput && suggestionsBox ) {
    let timer;
    searchInput.addEventListener('input', () => {
      clearTimeout(timer);
      const term = searchInput.value.trim();
      if ( !term ) {
        suggestionsBox.innerHTML = '';
        return;
      }
      timer = setTimeout(() => {
        fetch(
          `${window.location.origin}/wp-json/wp/v2/product?search=${encodeURIComponent(term)}&per_page=5`
        )
          .then(res => res.json())
          .then(data => {
            suggestionsBox.innerHTML = data
              .map(item =>
                `<div class="suggestion-item" data-url="${item.link}">${item.title.rendered}</div>`
              )
              .join('');
          });
      }, 300);
    });

    // Переход по клику на подсказку
    suggestionsBox.addEventListener('click', e => {
      const item = e.target.closest('.suggestion-item');
      if ( item ) {
        window.location.href = item.getAttribute('data-url');
      }
    });

    // Скрывать подсказки при клике вне
    document.addEventListener('click', e => {
      if ( !searchInput.contains(e.target) && !suggestionsBox.contains(e.target) ) {
        suggestionsBox.innerHTML = '';
      }
    });
  }
});
