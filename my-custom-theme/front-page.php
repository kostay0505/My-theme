<?php
/**
 * Template Name: Front Page
 * Description: Главная страница с hero, 3 каруселями, секцией вопросов, брендами и хлебными крошками
 * @package my-custom-theme
 */

get_header();

// Хлебные крошки (если не на главной)
if ( function_exists( 'mytheme_breadcrumbs' ) ) {
    mytheme_breadcrumbs();
}
?>

<div class="front-page-wrapper">

  <!-- HERO-SECTION -->
  <section class="hero">
    <div class="hero-block hero-left"
         style="background-image:url('<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/hero-left.jpg');">
      <div class="hero-content">
        <h2><?php esc_html_e( 'Не нашли что искали?', 'my-custom-theme' ); ?></h2>
        <p><?php esc_html_e( 'Сделайте запрос на нужное вам оборудование в личном кабинете...', 'my-custom-theme' ); ?></p>
      </div>
    </div>
    <div class="hero-block hero-right"
         style="background-image:url('<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/hero-right.jpg');">
      <div class="hero-content">
        <h2><?php esc_html_e( 'Есть ненужное оборудование?', 'my-custom-theme' ); ?></h2>
        <p><?php esc_html_e( 'Мы всегда ищем б/у оборудование. Отправьте нам список...', 'my-custom-theme' ); ?></p>
      </div>
    </div>
  </section>

  <!-- HOT DEALS -->
  <?php
  $hot_deals = new WP_Query( array(
    'post_type'      => 'product',
    'posts_per_page' => 8,
    'meta_query'     => array(
      array(
        'key'   => 'hot_deal',
        'value' => '1',
      ),
    ),
  ) );
  if ( $hot_deals->have_posts() ) : ?>
    <section class="carousel-section hot-deals">
      <div class="container">
        <h2><?php esc_html_e( 'Hot Deals', 'my-custom-theme' ); ?></h2>
        <div id="hot-deals-splide" class="splide carousel-splide">
          <div class="splide__track">
            <ul class="splide__list">
              <?php while ( $hot_deals->have_posts() ) : $hot_deals->the_post(); global $product; ?>
                <li class="splide__slide">
                  <a href="<?php the_permalink(); ?>" class="carousel-item">
                    <?php echo wp_get_attachment_image( $product->get_image_id(), 'medium' ); ?>
                    <h3><?php the_title(); ?></h3>
                    <div class="price"><?php echo $product->get_price_html(); ?></div>
                  </a>
                </li>
              <?php endwhile; wp_reset_postdata(); ?>
            </ul>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- NEW IN -->
  <?php
  $new_in = new WP_Query( array(
    'post_type'      => 'product',
    'posts_per_page' => 8,
    'orderby'        => 'date',
    'order'          => 'DESC',
  ) );
  if ( $new_in->have_posts() ) : ?>
    <section class="carousel-section new-in">
      <div class="container">
        <h2><?php esc_html_e( 'New In', 'my-custom-theme' ); ?></h2>
        <div id="new-in-splide" class="splide carousel-splide">
          <div class="splide__track">
            <ul class="splide__list">
              <?php while ( $new_in->have_posts() ) : $new_in->the_post(); global $product; ?>
                <li class="splide__slide">
                  <a href="<?php the_permalink(); ?>" class="carousel-item">
                    <?php echo wp_get_attachment_image( $product->get_image_id(), 'medium' ); ?>
                    <h3><?php the_title(); ?></h3>
                    <div class="price"><?php echo $product->get_price_html(); ?></div>
                  </a>
                </li>
              <?php endwhile; wp_reset_postdata(); ?>
            </ul>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- MOST VIEWED -->
  <?php
  $most_viewed = new WP_Query( array(
    'post_type'      => 'product',
    'posts_per_page' => 8,
    'meta_key'       => 'views',
    'orderby'        => 'meta_value_num',
    'order'          => 'DESC',
  ) );
  if ( $most_viewed->have_posts() ) : ?>
    <section class="carousel-section most-viewed">
      <div class="container">
        <h2><?php esc_html_e( 'Most Viewed', 'my-custom-theme' ); ?></h2>
        <div id="most-viewed-splide" class="splide carousel-splide">
          <div class="splide__track">
            <ul class="splide__list">
              <?php while ( $most_viewed->have_posts() ) : $most_viewed->the_post(); global $product; ?>
                <li class="splide__slide">
                  <a href="<?php the_permalink(); ?>" class="carousel-item">
                    <?php echo wp_get_attachment_image( $product->get_image_id(), 'medium' ); ?>
                    <h3><?php the_title(); ?></h3>
                    <div class="price"><?php echo $product->get_price_html(); ?></div>
                  </a>
                </li>
              <?php endwhile; wp_reset_postdata(); ?>
            </ul>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- ОСТАЛИСЬ ВОПРОСЫ? -->
  <section class="questions">
    <div class="questions-inner">
      <div class="questions-text">
        <h2><?php esc_html_e( 'Остались вопросы', 'my-custom-theme' ); ?></h2>
        <p><?php esc_html_e( 'Заполните форму обратной связи с интересующими вас вопросами и мы свяжемся с вами удобным для вас способом', 'my-custom-theme' ); ?></p>
      </div>
      <div class="questions-action">
        <a href="#contact" class="questions-button"><?php esc_html_e( 'Напишите нам', 'my-custom-theme' ); ?></a>
      </div>
    </div>
  </section>

  <!-- PRODUCT BRAND -->
  <section class="brands-carousel">
    <div class="container">
      <h2><?php esc_html_e( 'PRODUCT BRAND', 'my-custom-theme' ); ?></h2>
      <div class="carousel-container">
        <?php
        $brand_dir = get_template_directory() . '/assets/images/brands';
        $images    = glob( $brand_dir . '/*.{png,jpg,jpeg,svg}', GLOB_BRACE );
        if ( $images ) {
          foreach ( $images as $img_path ) {
            $filename = basename( $img_path );
            $img_url  = get_template_directory_uri() . '/assets/images/brands/' . $filename;
            $alt      = pathinfo( $filename, PATHINFO_FILENAME );
            echo '<div class="brand-item">';
            echo '<img src="' . esc_url( $img_url ) . '" alt="' . esc_attr( $alt ) . '">';
            echo '</div>';
          }
        } else {
          echo '<p>' . esc_html__( 'Логотипы брендов отсутствуют.', 'my-custom-theme' ) . '</p>';
        }
        ?>
      </div>
    </div>
  </section>

</div><!-- .front-page-wrapper -->

<?php
get_footer();
