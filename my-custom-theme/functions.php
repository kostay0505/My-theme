<?php
/**
 * functions.php
 *
 * Основной файл настроек темы
 * @package my-custom-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/* ──────────────────────────
 1. Подключаем вспом-файлы
────────────────────────── */
require_once get_template_directory() . '/inc/roles.php';
require_once get_template_directory() . '/inc/dashboard/dashboard-functions.php';

/* ──────────────────────────
 2. Запуск темы
────────────────────────── */
add_action( 'after_setup_theme', 'mytheme_setup' );
function mytheme_setup() {
  add_theme_support( 'title-tag' );
  add_theme_support( 'post-thumbnails' );
  register_nav_menus( [
    'header_menu' => 'Main Header Menu',
  ] );
  add_theme_support( 'woocommerce' );
}

/* ──────────────────────────
 3. Стили и скрипты
────────────────────────── */
add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_assets' );
function mytheme_enqueue_assets() {
  // главный CSS
  wp_enqueue_style(
    'mytheme-style',
    get_stylesheet_uri(),
    [],
    '2.7'
  );
  // Splide CSS
  wp_enqueue_style(
    'splide-css',
    get_template_directory_uri() . '/assets/libs/splide/splide.min.css',
    [],
    '4.1.3'
  );
  // Интер-шрифт
  wp_enqueue_style(
    'inter-font',
    'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap',
    [],
    null
  );
  // Splide JS
  wp_enqueue_script(
    'splide-js',
    get_template_directory_uri() . '/assets/libs/splide/splide.min.js',
    [],
    '4.1.3',
    true
  );
  // главный JS, зависит от Splide
  wp_enqueue_script(
    'mytheme-js',
    get_template_directory_uri() . '/assets/js/theme.js',
    [ 'splide-js' ],
    '1.1',
    true
  );
}

/* ──────────────────────────
 4. Хлебные крошки
────────────────────────── */
function mytheme_breadcrumbs() {
  echo '<nav class="breadcrumbs" aria-label="breadcrumbs">';
  if ( ! is_front_page() ) {
    echo '<a href="' . esc_url( home_url() ) . '">Home</a> / ';
    if ( is_page() ) {
      global $post;
      if ( $post->post_parent ) {
        $ancestors = array_reverse( get_post_ancestors( $post->ID ) );
        foreach ( $ancestors as $ancestor ) {
          echo '<a href="' . esc_url( get_permalink( $ancestor ) ) . '">'
             . esc_html( get_the_title( $ancestor ) ) . '</a> / ';
        }
      }
      echo esc_html( get_the_title() );
    } elseif ( is_single() ) {
      the_title();
    } elseif ( is_archive() ) {
      the_archive_title();
    }
  } else {
    echo 'Home';
  }
  echo '</nav>';
}

/* ──────────────────────────
 5. Добавляем роль «user»
────────────────────────── */
add_action( 'after_switch_theme', 'mytheme_add_custom_user_role' );

/* ──────────────────────────
 6. Фильтрация товаров WooCommerce
────────────────────────── */
add_action( 'woocommerce_product_query', 'mytheme_filter_subcategory_query' );
function mytheme_filter_subcategory_query( $q ) {
  if ( ! is_tax( 'product_cat' ) ) {
    return;
  }
  // сортировка
  if ( ! empty( $_GET['orderby'] ) ) {
    $orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
    if ( in_array( $orderby, [ 'date', 'title' ], true ) ) {
      $q->set( 'orderby', $orderby );
      $q->set( 'order', $orderby === 'date' ? 'DESC' : 'ASC' );
    }
  }
  // кол-во на странице
  if ( ! empty( $_GET['per_page'] ) ) {
    $q->set( 'posts_per_page', intval( $_GET['per_page'] ) );
  }
  // только б/у
  if ( isset( $_GET['used_only'] ) ) {
    $meta = $q->get( 'meta_query' ) ?: [];
    $meta[] = [
      'key'     => '_condition',
      'value'   => 'used',
      'compare' => '=',
    ];
    $q->set( 'meta_query', $meta );
  }
  // TODO: ZIP/Radius
}

/* ──────────────────────────
 7. Сохранение/редактирование объявлений из ЛК
────────────────────────── */
add_action( 'template_redirect', 'mytheme_handle_save_ad' );
function mytheme_handle_save_ad() {
  if ( empty( $_POST['mytheme_ad_nonce'] ) ) {
    return;
  }
  if ( ! wp_verify_nonce( wp_unslash( $_POST['mytheme_ad_nonce'] ), 'mytheme_save_ad' ) ) {
    return;
  }
  if ( empty( $_POST['account_url'] ) || ! is_user_logged_in() ) {
    return;
  }

  // подготовка
  $account_url = esc_url_raw( wp_unslash( $_POST['account_url'] ) );
  $is_edit     = ! empty( $_POST['ad_id'] );
  $post_id     = $is_edit ? intval( wp_unslash( $_POST['ad_id'] ) ) : 0;

  // кнопка удаления объявления
  if ( $is_edit && ! empty( $_POST['ad_delete'] ) ) {
    wp_delete_post( $post_id, true );
    wp_safe_redirect( $account_url );
    exit;
  }

  // WP-media
  require_once ABSPATH . 'wp-admin/includes/file.php';
  require_once ABSPATH . 'wp-admin/includes/media.php';
  require_once ABSPATH . 'wp-admin/includes/image.php';

  // 1) удаляем отмеченные старые картинки
  if ( $is_edit && ! empty( $_POST['remove_images'] ) ) {
    $to_remove = array_map( 'absint', (array) $_POST['remove_images'] );
    // миниатюра
    $thumb = get_post_thumbnail_id( $post_id );
    if ( in_array( $thumb, $to_remove, true ) ) {
      delete_post_thumbnail( $post_id );
    }
    // галерея
    $gal = get_post_meta( $post_id, '_product_image_gallery', true );
    $ids = $gal ? explode( ',', $gal ) : [];
    $ids = array_diff( $ids, $to_remove );
    update_post_meta( $post_id, '_product_image_gallery', implode( ',', $ids ) );
  }

  // 2) читаем поля
  $title   = sanitize_text_field( wp_unslash( $_POST['ad_title']   ?? '' ) );
  $content = sanitize_textarea_field( wp_unslash( $_POST['ad_desc']   ?? '' ) );
  $cat     = isset( $_POST['ad_cat'] )
    ? intval( wp_unslash( $_POST['ad_cat'] ) )
    : 0;
  $cond    = isset( $_POST['ad_cond'] )
    ? sanitize_text_field( wp_unslash( $_POST['ad_cond'] ) )
    : '';
  $price   = isset( $_POST['ad_price'] )
    ? floatval( wp_unslash( $_POST['ad_price'] ) )
    : 0;

  // создаём/обновляем пост
  if ( $is_edit ) {
    wp_update_post( [
      'ID'           => $post_id,
      'post_title'   => $title,
      'post_content' => $content,
    ] );
  } else {
    $post_id = wp_insert_post( [
      'post_type'    => 'product',
      'post_status'  => 'publish',  // сразу публикуем
      'post_title'   => $title,
      'post_content' => $content,
    ] );
  }
  if ( is_wp_error( $post_id ) ) {
    wp_safe_redirect( $account_url );
    exit;
  }

  // таксономия + мета
  wp_set_post_terms( $post_id, [ $cat ], 'product_cat' );
  update_post_meta( $post_id, '_condition', $cond );
  update_post_meta( $post_id, '_price',     $price );

  // 3) загрузка новых картинок через sideload
  if ( ! empty( $_FILES['ad_images']['tmp_name'][0] ) ) {
    $gal = get_post_meta( $post_id, '_product_image_gallery', true );
    $ids = $gal ? explode( ',', $gal ) : [];

    foreach ( $_FILES['ad_images']['tmp_name'] as $i => $tmp ) {
      if ( empty( $tmp ) ) {
        continue;
      }
      $file_array = [
        'name'     => sanitize_file_name( $_FILES['ad_images']['name'][ $i ] ),
        'tmp_name' => $tmp,
      ];
      $attach_id = media_handle_sideload( $file_array, $post_id );
      if ( ! is_wp_error( $attach_id ) ) {
        $ids[] = $attach_id;
      }
    }

    update_post_meta( $post_id, '_product_image_gallery', implode( ',', $ids ) );
    // миниатюра
    if ( ! has_post_thumbnail( $post_id ) && ! empty( $ids[0] ) ) {
      set_post_thumbnail( $post_id, $ids[0] );
    }
  }

  // 4) редирект назад
  wp_safe_redirect( $account_url );
  exit;
}
