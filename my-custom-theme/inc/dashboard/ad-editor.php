<?php
/**
 * ad-editor.php
 * Форма создания и редактирования объявления в ЛК
 * Вызывается из page-account.php при section=ads & action=new|edit
 */
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

// URL страницы аккаунта (передан через set_query_var)
$account_page_url = get_query_var( 'account_page_url' );

// Определяем режим: new или edit
$action  = sanitize_text_field( wp_unslash( $_GET['action'] ?? '' ) );
$is_edit = 'edit' === $action && ! empty( $_GET['ad_id'] );
$post_id = $is_edit ? intval( $_GET['ad_id'] ) : 0;
$post    = $is_edit ? get_post( $post_id ) : null;

// Подготовка данных для полей
$title = $is_edit ? $post->post_title : '';
$desc  = $is_edit ? $post->post_content : '';
$price = $is_edit ? get_post_meta( $post_id, '_price', true ) : '';
$cond  = $is_edit ? get_post_meta( $post_id, '_condition', true ) : 'new';
$cat_terms = $is_edit
  ? wp_get_post_terms( $post_id, 'product_cat', [ 'fields' => 'ids' ] )
  : [];
$cat_id     = ! empty( $cat_terms ) ? $cat_terms[0] : 0;
$gallery_ids = $is_edit
  ? array_filter( array_map( 'absint', explode( ',', get_post_meta( $post_id, '_product_image_gallery', true ) ) ) )
  : [];
$thumb_id    = $is_edit ? get_post_thumbnail_id( $post_id ) : 0;
?>

<h2 class="ad-editor-title">
  <?php echo $is_edit
    ? esc_html__( 'Редактировать объявление', 'my-custom-theme' )
    : esc_html__( 'Новое объявление',    'my-custom-theme' ); ?>
</h2>

<form method="post" enctype="multipart/form-data" class="ad-form">
  <?php wp_nonce_field( 'mytheme_save_ad', 'mytheme_ad_nonce' ); ?>
  <input type="hidden" name="account_url" value="<?php echo esc_attr( $account_page_url ); ?>">
  <?php if ( $is_edit ) : ?>
    <input type="hidden" name="ad_id" value="<?php echo esc_attr( $post_id ); ?>">
  <?php endif; ?>

  <!-- Название -->
  <p class="form-row">
    <label for="ad_title"><?php esc_html_e( 'Название объявления', 'my-custom-theme' ); ?></label><br>
    <input
      type="text"
      id="ad_title"
      name="ad_title"
      value="<?php echo esc_attr( $title ); ?>"
      required
      class="widefat"
    >
  </p>

  <!-- Категория -->
  <p class="form-row">
    <label for="ad_cat"><?php esc_html_e( 'Категория', 'my-custom-theme' ); ?></label><br>
    <?php
    wp_dropdown_categories( [
      'taxonomy'         => 'product_cat',
      'hide_empty'       => false,
      'name'             => 'ad_cat',
      'id'               => 'ad_cat',
      'hierarchical'     => true,
      'show_option_none' => __( 'Выберите…', 'my-custom-theme' ),
      'selected'         => $cat_id,
      'class'            => 'widefat',
    ] );
    ?>
  </p>

  <!-- Состояние -->
  <p class="form-row">
    <label><?php esc_html_e( 'Состояние', 'my-custom-theme' ); ?></label><br>
    <label>
      <input type="radio" name="ad_cond" value="new" <?php checked( $cond, 'new' ); ?>>
      <?php esc_html_e( 'Новое', 'my-custom-theme' ); ?>
    </label>
    <label style="margin-left:1em;">
      <input type="radio" name="ad_cond" value="used" <?php checked( $cond, 'used' ); ?>>
      <?php esc_html_e( 'Б/у', 'my-custom-theme' ); ?>
    </label>
  </p>

  <!-- Цена -->
  <p class="form-row">
    <label for="ad_price"><?php esc_html_e( 'Цена', 'my-custom-theme' ); ?></label><br>
    <input
      type="number"
      id="ad_price"
      name="ad_price"
      value="<?php echo esc_attr( $price ); ?>"
      step="0.01"
      min="0"
      required
      class="widefat"
    >
  </p>

  <!-- Описание -->
  <p class="form-row">
    <label for="ad_desc"><?php esc_html_e( 'Описание', 'my-custom-theme' ); ?></label><br>
    <textarea
      id="ad_desc"
      name="ad_desc"
      rows="6"
      class="widefat"
    ><?php echo esc_textarea( $desc ); ?></textarea>
  </p>

  <!-- Загрузка новых фотографий -->
  <p class="form-row">
    <label><?php esc_html_e( 'Фотографии (до 10 шт.)', 'my-custom-theme' ); ?></label><br>
    <input
      type="file"
      id="ad-images-input"
      name="ad_images[]"
      accept="image/*"
      multiple
    >
    <div id="ad-image-preview" class="ad-image-preview"></div>
  </p>

  <!-- Превью существующих фотографий -->
  <?php if ( $is_edit && ( $thumb_id || $gallery_ids ) ) : ?>
    <div class="ad-existing-preview ad-image-preview">
      <strong><?php esc_html_e( 'Существующие изображения:', 'my-custom-theme' ); ?></strong><br>
      <?php if ( $thumb_id ) : ?>
        <div class="image-thumb" data-existing-id="<?php echo esc_attr( $thumb_id ); ?>">
          <?php echo wp_get_attachment_image( $thumb_id, 'thumbnail' ); ?>
          <button type="button" class="remove-existing">&times;</button>
        </div>
      <?php endif; ?>
      <?php foreach ( $gallery_ids as $gid ) : ?>
        <div class="image-thumb" data-existing-id="<?php echo esc_attr( $gid ); ?>">
          <?php echo wp_get_attachment_image( $gid, 'thumbnail' ); ?>
          <button type="button" class="remove-existing">&times;</button>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- Кнопки действий -->
  <p class="form-row ad-actions">
    <button type="submit" class="button button-primary">
      <?php echo $is_edit
        ? esc_html__( 'Сохранить изменения', 'my-custom-theme' )
        : esc_html__( 'Опубликовать',       'my-custom-theme' ); ?>
    </button>

    <?php if ( $is_edit ) : ?>
      <button
        type="submit"
        name="ad_delete"
        value="1"
        class="button button-secondary"
        onclick="return confirm('<?php echo esc_js( __( 'Удалить объявление безвозвратно?', 'my-custom-theme' ) ); ?>');"
      >
        <?php esc_html_e( 'Удалить', 'my-custom-theme' ); ?>
      </button>
    <?php endif; ?>
  </p>
</form>
