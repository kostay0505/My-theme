<?php
/**
 * Template Name: Front Page
 * Description: Главная страница с hero, 3 каруселями, секцией вопросов, брендами и хлебными крошками
 */

get_header();

// Хлебные крошки (если не на главной)
if ( function_exists('mytheme_breadcrumbs') ) {
  mytheme_breadcrumbs();
}
?>

<div class="page-wrapper"><!-- центральный белый контейнер -->
  <main class="site-main">

    <!-- HERO-SECTION -->
    <section class="hero">
      <div class="hero-block hero-left"
           style="background-image:url('<?php echo get_template_directory_uri(); ?>/assets/images/hero-left.jpg');">
        <div class="hero-content">
          <h2>Не нашли что искали?</h2>
          <p>Сделайте запрос на нужное вам оборудование в личном кабинете...</p>
        </div>
      </div>
      <div class="hero-block hero-right"
           style="background-image:url('<?php echo get_template_directory_uri(); ?>/assets/images/hero-right.jpg');">
        <div class="hero-content">
          <h2>Есть ненужное оборудование?</h2>
          <p>Мы всегда ищем б/у оборудование. Отправьте нам список...</p>
        </div>
      </div>
    </section>

    <!-- HOT DEALS -->
    <section class="carousel-section hot-deals">
      <div class="container">
        <h2>HOT DEALS</h2>
        <div class="carousel-container">
          <div class="product">Hot Deal #1</div>
          <div class="product">Hot Deal #2</div>
          <div class="product">Hot Deal #3</div>
          <div class="product">Hot Deal #4</div>
        </div>
      </div>
    </section>

    <!-- NEW IN -->
    <section class="carousel-section new-in">
      <div class="container">
        <h2>NEW IN</h2>
        <div class="carousel-container">
          <div class="product">New In #1</div>
          <div class="product">New In #2</div>
          <div class="product">New In #3</div>
          <div class="product">New In #4</div>
        </div>
      </div>
    </section>

    <!-- MOST VIEWED -->
    <section class="carousel-section most-viewed">
      <div class="container">
        <h2>MOST VIEWED</h2>
        <div class="carousel-container">
          <div class="product">Most Viewed #1</div>
          <div class="product">Most Viewed #2</div>
          <div class="product">Most Viewed #3</div>
          <div class="product">Most Viewed #4</div>
        </div>
      </div>
    </section>

    <!-- Секция "Остались вопросы" -->
    <section class="questions">
      <div class="questions-inner">
        <div class="questions-text">
          <h2>Остались вопросы?</h2>
          <p>Свяжитесь с нами, и мы ответим на все ваши вопросы.</p>
        </div>
        <div class="questions-action">
          <a href="#contact" class="btn-contact">Напишите нам</a>
        </div>
      </div>
    </section>

    <!-- PRODUCT BRAND -->
    <section class="brands-carousel">
      <div class="container">
        <h2>PRODUCT BRAND</h2>
        <div class="carousel-container">
          <?php
          // Динамически выводим все логотипы из папки /assets/images/brands/
          $brand_dir = get_template_directory() . '/assets/images/brands';
          $images = glob( $brand_dir . '/*.{png,jpg,jpeg,svg}', GLOB_BRACE );
          if ( $images ) {
            foreach ( $images as $img_path ) {
              $filename = basename( $img_path );
              $img_url  = get_template_directory_uri() . '/assets/images/brands/' . $filename;
              $alt      = pathinfo( $filename, PATHINFO_FILENAME );
              echo '<div class="brand-item">';
              echo '  <img src="' . esc_url( $img_url ) . '" alt="' . esc_attr( $alt ) . '">';
              echo '</div>';
            }
          } else {
            echo '<p>Логотипы брендов отсутствуют.</p>';
          }
          ?>
        </div>
      </div>
    </section>

  </main>
</div><!-- .page-wrapper -->

<?php get_footer(); ?>
