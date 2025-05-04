// assets/js/theme.js
// Главный скрипт темы

document.addEventListener('DOMContentLoaded', function() {
  // 1. Галерея товара — переключение главного изображения
  const mainImg = document.querySelector('.product-main-img');
  const thumbs  = document.querySelectorAll('.product-thumb-img');
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
           768: { perPage: 2 },
           480: { perPage: 1 },
        },
      }).mount();
    });
  }

  // 3. Live-поиск по товарам в фильтрационном блоке
  const searchInput    = document.getElementById('product-search-input');
  const suggestionsBox = document.getElementById('search-suggestions');
  if ( searchInput && suggestionsBox ) {
    let timer;
    searchInput.addEventListener('input', () => {
      clearTimeout(timer);
      const term = searchInput.value.trim();
      if ( ! term ) {
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

    // переход по клику на подсказку
    suggestionsBox.addEventListener('click', e => {
      const item = e.target.closest('.suggestion-item');
      if ( item ) {
        window.location.href = item.getAttribute('data-url');
      }
    });

    // скрывать подсказки при клике вне
    document.addEventListener('click', e => {
      if ( ! searchInput.contains(e.target) && ! suggestionsBox.contains(e.target) ) {
        suggestionsBox.innerHTML = '';
      }
    });
  }

  // 4. Редактор объявлений: превью новых фото
  const adInput   = document.getElementById('ad-images-input');
  const adPreview = document.getElementById('ad-image-preview');
  if ( adInput && adPreview ) {
    const dt = new DataTransfer();

    adInput.addEventListener('change', () => {
      Array.from(adInput.files).forEach(file => {
        dt.items.add(file);

        const reader = new FileReader();
        reader.onload = ev => {
          const wrapper = document.createElement('div');
          wrapper.className = 'image-thumb';

          const img = document.createElement('img');
          img.src = ev.target.result;
          wrapper.appendChild(img);

          const btn = document.createElement('button');
          btn.type       = 'button';
          btn.className  = 'remove-img';
          btn.innerHTML  = '&times;';
          btn.addEventListener('click', () => {
            const thumbs = Array.from(adPreview.children);
            const idx    = thumbs.indexOf(wrapper);
            dt.items.remove(idx);
            adInput.files = dt.files;
            wrapper.remove();
          });
          wrapper.appendChild(btn);

          adPreview.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
      });
      adInput.files = dt.files;
    });
  }

  // 5. Редактор объявлений: удаление уже существующих фото
  const existingBox = document.querySelector('.ad-existing-preview');
  if ( existingBox ) {
    existingBox.addEventListener('click', function(e) {
      if ( e.target.matches('.remove-existing') ) {
        const wrapper = e.target.closest('.image-thumb');
        const id      = wrapper.getAttribute('data-existing-id');

        // создаём скрытое поле для удаления на сервере
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'remove_images[]';
        input.value = id;
        wrapper.insertAdjacentElement('afterend', input);

        wrapper.remove();
      }
    });
  }
});
