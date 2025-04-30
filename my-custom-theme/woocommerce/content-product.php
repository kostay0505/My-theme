<?php
/**
 * The template for displaying product entries in a custom subcategory list
 * @package my-custom-theme
 */
defined( 'ABSPATH' ) || exit;
global $product;
?>

<li <?php wc_product_class( 'subcategory-item', $product ); ?>>

  <!-- 3. Thumbnail -->
  <div class="item-thumbnail">
    <a href="<?php the_permalink(); ?>">
      <?php echo wp_get_attachment_image( $product->get_image_id(), 'thumbnail' ); ?>
    </a>
  </div>

  <!-- 4. Title and excerpt -->
  <div class="item-details">
    <h2 class="item-title">
      <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </h2>
    <div class="item-excerpt">
      <?php echo wp_trim_words( get_the_excerpt(), 20, '&hellip;' ); ?>
    </div>
  </div>

  <!-- 5. Price, date, location -->
  <div class="item-meta">
    <span class="item-price"><?php echo $product->get_price_html(); ?></span>
    <span class="item-date"><?php echo get_the_date( 'd.m.Y' ); ?></span>
    <?php if ( $zip = get_post_meta( get_the_ID(), '_zip_code', true ) ) : ?>
      <span class="item-location"><?php echo esc_html( $zip ); ?></span>
    <?php endif; ?>
  </div>

</li>
