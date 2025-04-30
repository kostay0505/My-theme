<?php
/**
 * The template for displaying single product pages
 *
 * Template Name: Product Detail
 * @package my-custom-theme
 */

get_header();

// Хлебные крошки
if ( function_exists( 'mytheme_breadcrumbs' ) ) {
    mytheme_breadcrumbs();
}

// Предположим, что у вас есть ACF-поля или подобные для:
// – Галерея изображений товара: 'product_gallery' (array из attachment ID)
// – Название: стандартный заголовок — the_title()
// – Цена наличными: ACF‑поле 'price_cash'
// – Цена безналом: ACF‑поле 'price_noncash'
// – Продавец: ACF‑поле 'seller_name'
// – Описание: the_content()
// – Логотип бренда: ACF‑поле 'brand_logo' (attachment ID)
?>

<div class="page-wrapper product-page">

  <main class="product-detail">

    <!-- 1. Левый блок: изображения -->
    <div class="product-images">
      <?php
      $gallery = get_field( 'product_gallery' ) ?: [];
      // Первое фото — главное
      if ( count( $gallery ) ) {
        $main_id = array_shift( $gallery );
        echo wp_get_attachment_image( $main_id, 'large', false, [ 'class'=>'product-main-img' ] );
      }
      ?>
      <?php if ( count( $gallery ) ): ?>
        <div class="product-thumbs">
          <?php
          // Выводим превьюшки
          array_unshift( $gallery, $main_id );
          foreach ( $gallery as $img_id ) {
            echo wp_get_attachment_image( $img_id, 'thumbnail', false, [ 'class'=>'product-thumb-img', 'data-full'=>wp_get_attachment_image_url( $img_id, 'large' ) ] );
          }
          ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- 2. Правый блок: информация -->
    <div class="product-info">

      <!-- 2.1 Название товара -->
      <h1 class="product-title"><?php the_title(); ?></h1>

      <!-- 2.2 Цены -->
      <div class="product-prices">
        <?php if ( $cash = get_field('price_cash') ): ?>
          <div class="price-cash"><?php echo esc_html( $cash ); ?> Наличными</div>
        <?php endif; ?>
        <?php if ( $noncash = get_field('price_noncash') ): ?>
          <div class="price-noncash"><?php echo esc_html( $noncash ); ?> Безналичный расчёт</div>
        <?php endif; ?>
      </div>

      <!-- 2.3 Кнопка покупки + кол-во -->
      <div class="product-purchase">
        <button class="btn btn-buy">Купить</button>
        <div class="qty-selector">
          <button class="qty-minus">–</button>
          <input type="number" min="1" value="1" class="qty-input">
          <button class="qty-plus">+</button>
          <span>компл.</span>
        </div>
      </div>

      <!-- 2.4 Продавец -->
      <?php if ( $seller = get_field('seller_name') ): ?>
        <div class="product-seller">
          Продавец: <strong><?php echo esc_html( $seller ); ?></strong>
        </div>
      <?php endif; ?>

      <!-- 2.5 Описание -->
      <div class="product-description">
        <h2>Описание</h2>
        <?php the_content(); ?>
      </div>

      <!-- 2.6 Логотип бренда -->
      <?php if ( $logo_id = get_field('brand_logo') ): ?>
        <div class="product-brand-logo">
          <?php echo wp_get_attachment_image( $logo_id, 'medium', false, [ 'alt'=>'Логотип бренда' ] ); ?>
        </div>
      <?php endif; ?>

    </div><!-- .product-info -->

  </main><!-- .product-detail -->

</div><!-- .page-wrapper -->

<?php get_footer(); ?>
