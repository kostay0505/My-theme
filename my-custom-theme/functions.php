<?php

/**
 * functions.php
 *
 * Основной файл настроек темы
 * @package my-custom-theme
 */

/* ──────────────────────────
 1. Подключаем вспом‑файлы
────────────────────────── */
require_once get_template_directory() . '/inc/roles.php'; // кастомная роль «user"
require_once get_template_directory() . '/inc/dashboard/dashboard-functions.php'; // (если нужен дополнительный код)

/* ──────────────────────────
 2. Запуск темы
────────────────────────── */
add_action( 'after_setup_theme', 'mytheme_setup' );
function mytheme_setup() {
  // title‑tag, миниатюры
  add_theme_support( 'title-tag' );
  add_theme_support( 'post-thumbnails' );

  // меню
  register_nav_menus( array(
    'header_menu' => 'Main Header Menu',
  ) );

  // WooCommerce
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
    get_stylesheet_uri(), // style.css в корне темы
    array(),
    '2.7'
  );

  // шрифт Inter
  wp_enqueue_style(
    'inter-font',
    'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap',
    array(),
    null
  );

  // главный JS (⟵ НОВОЕ!)
  wp_enqueue_script(
    'mytheme-js',
    get_template_directory_uri() . '/assets/js/theme.js',
    array(),
    '1.0',
    true // подключить в footer
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
   (реализация в inc/roles.php)
────────────────────────── */
add_action( 'after_switch_theme', 'mytheme_add_custom_user_role' );

/* ──────────────────────────
 6. Фильтрация товаров подкатегорий
────────────────────────── */
add_action( 'woocommerce_product_query', 'mytheme_filter_subcategory_query' );
/**
 * Фильтрует запрос WooCommerce для страниц product_cat
 */
function mytheme_filter_subcategory_query( $q ) {
  if ( ! is_tax( 'product_cat' ) ) {
    return;
  }

  // Сортировка
  if ( ! empty( $_GET['orderby'] ) ) {
    $orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
    if ( in_array( $orderby, array( 'date', 'title' ), true ) ) {
      $q->set( 'orderby', $orderby );
      $q->set( 'order', $orderby === 'date' ? 'DESC' : 'ASC' );
    }
  }

  // Кол-во товаров на странице
  if ( ! empty( $_GET['per_page'] ) ) {
    $per_page = intval( $_GET['per_page'] );
    $q->set( 'posts_per_page', $per_page );
  }

  // Фильтр "только б/у"
  if ( isset( $_GET['used_only'] ) ) {
    $meta_query = $q->get( 'meta_query' ) ?: array();
    $meta_query[] = array(
      'key'     => '_condition',
      'value'   => 'used',
      'compare' => '=',
    );
    $q->set( 'meta_query', $meta_query );
  }

  // TODO: ZIP и Radius — добавить логику гео-фильтрации
}
