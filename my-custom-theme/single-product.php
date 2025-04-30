<?php
/**
 * Template Name: Product Detail
 * Шаблон страницы товара
 * @package my-custom-theme
 */

get_header();
if ( function_exists( 'mytheme_breadcrumbs' ) ) {
    echo '<div class="breadcrumbs-wrap">';
    mytheme_breadcrumbs();
    echo '</div>';
}

global $product;
?>

<div class="page-wrapper product-page">
  <main class="product-detail container">

    <!-- Левый блок: изображения товара -->
    <div class="product-images">
        <?php
        $gallery_ids = $product->get_gallery_image_ids();
        if ( ! empty( $gallery_ids ) ) {
            // Главное изображение
            $main_id = array_shift( $gallery_ids );
            echo wp_get_attachment_image( $main_id, 'large', false, array( 'class' => 'product-main-img' ) );
            // Превью изображений
            echo '<div class="product-thumbs">';
            echo wp_get_attachment_image( $main_id, 'thumbnail', false, array(
                'class'    => 'product-thumb-img',
                'data-full'=> wp_get_attachment_image_url( $main_id, 'large' ),
            ));
            foreach ( $gallery_ids as $img_id ) {
                echo wp_get_attachment_image( $img_id, 'thumbnail', false, array(
                    'class'    => 'product-thumb-img',
                    'data-full'=> wp_get_attachment_image_url( $img_id, 'large' ),
                ));
            }
            echo '</div>';
        } else {
            // Стандартное изображение товара
            echo woocommerce_get_product_thumbnail( 'large', array( 'class' => 'product-main-img' ) );
        }
        ?>
    </div>

    <!-- Центральный блок: информация о товаре -->
    <div class="product-info">

      <!-- Название товара -->
      <h1 class="product-title"><?php the_title(); ?></h1>

      <!-- Цена товара -->
      <div class="product-price">
        <?php echo $product->get_price_html(); ?>
      </div>

      <!-- Блок покупки -->
      <div class="product-purchase">
        <form class="cart" action="<?php echo esc_url( get_permalink() ); ?>" method="post">
          <?php woocommerce_quantity_input( array( 'min_value' => 1, 'input_value' => 1 ) ); ?>
          <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="btn-buy">
            <?php esc_html_e( 'Купить', 'my-custom-theme' ); ?>
          </button>
        </form>
      </div>

      <!-- Продавец товара -->
      <div class="product-seller">
        <?php esc_html_e( 'Продавец:', 'my-custom-theme' ); ?>
        <strong><?php echo esc_html( get_post_meta( get_the_ID(), 'seller_name', true ) ?: 'Touring Expert' ); ?></strong>
      </div>

      <!-- Описание товара -->
      <div class="product-description">
        <h2><?php esc_html_e( 'Описание', 'my-custom-theme' ); ?></h2>
        <?php the_content(); ?>
      </div>

    </div><!-- /.product-info -->

    <!-- Правый блок: логотип бренда -->
    <aside class="product-brand">
      <?php
      $logo_id = get_post_meta( get_the_ID(), 'brand_logo', true );
      if ( $logo_id ) {
          echo wp_get_attachment_image( $logo_id, 'medium', false, array( 'class' => 'brand-logo' ) );
      } else {
          echo '<div class="brand-logo-placeholder">';
          esc_html_e( 'Логотип бренда', 'my-custom-theme' );
          echo '</div>';
      }
      ?>
    </aside>

  </main><!-- /.product-detail -->
</div><!-- /.page-wrapper -->

<?php get_footer(); ?>
