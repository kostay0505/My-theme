<?php
/**
 * functions.php
 *
 * Основной файл настроек темы
 * @package my-custom-theme
 */

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
  register_nav_menus( array(
    'header_menu' => __( 'Main Header Menu', 'my-custom-theme' ),
  ) );
  add_theme_support( 'woocommerce' );
}

/* ──────────────────────────
 3. Стили и скрипты
────────────────────────── */
add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_assets' );
function mytheme_enqueue_assets() {
  // Главный CSS
  wp_enqueue_style( 'mytheme-style', get_stylesheet_uri(), array(), '2.7' );

  // Интер шрифт
  wp_enqueue_style( 'inter-font', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap', array(), null );

  // Splide.js
  wp_enqueue_style( 'splide-css', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@latest/dist/css/splide.min.css', array(), null );
  wp_enqueue_script( 'splide-js', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@latest/dist/js/splide.min.js', array(), null, true );

  // Тема JS
  wp_enqueue_script( 'mytheme-js', get_template_directory_uri() . '/assets/js/theme.js', array( 'splide-js' ), '1.0', true );
}

/* ──────────────────────────
 4. Хлебные крошки
────────────────────────── */
function mytheme_breadcrumbs() {
  echo '<nav class="breadcrumbs" aria-label="breadcrumbs">';
  if ( ! is_front_page() ) {
    echo '<a href="' . esc_url( home_url() ) . '">' . esc_html__( 'Home', 'my-custom-theme' ) . '</a>';
    $sep = ' / ';

    if ( is_page_template( 'page-products.php' ) ) {
      echo $sep . '<a href="' . esc_url( get_permalink() ) . '">' . esc_html__( 'Catalog', 'my-custom-theme' ) . '</a>';
      $section = isset( $_GET['section'] ) ? sanitize_key( wp_unslash( $_GET['section'] ) ) : '';
      if ( $section && 'dashboard' !== $section ) {
        $term = get_term_by( 'slug', $section, 'product_cat' );
        if ( $term ) {
          echo $sep . '<a href="' . esc_url( add_query_arg( 'section', $section, get_permalink() ) ) . '">' . esc_html( $term->name ) . '</a>';
        }
      }

    } elseif ( is_singular( 'product' ) ) {
      // Найти страницу каталога
      $pages = get_posts( array(
        'post_type'   => 'page',
        'meta_key'    => '_wp_page_template',
        'meta_value'  => 'page-products.php',
        'numberposts' => 1,
      ) );
      $catalog_url = $pages ? get_permalink( $pages[0]->ID ) : home_url();

      echo $sep . '<a href="' . esc_url( add_query_arg( 'section', 'dashboard', $catalog_url ) ) . '">' . esc_html__( 'Catalog', 'my-custom-theme' ) . '</a>';
      $terms = wp_get_post_terms( get_the_ID(), 'product_cat' );
      if ( $terms ) {
        $cat = $terms[0];
        if ( $cat->parent ) {
          $ancestors = array_reverse( get_ancestors( $cat->term_id, 'product_cat' ) );
          foreach ( $ancestors as $anc_id ) {
            $anc = get_term( $anc_id, 'product_cat' );
            echo $sep . '<a href="' . esc_url( add_query_arg( 'section', $anc->slug, $catalog_url ) ) . '">' . esc_html( $anc->name ) . '</a>';
          }
        }
        echo $sep . '<a href="' . esc_url( add_query_arg( 'section', $cat->slug, $catalog_url ) ) . '">' . esc_html( $cat->name ) . '</a>';
      }
      echo $sep . esc_html( get_the_title() );

    } elseif ( is_page() ) {
      global $post;
      if ( $post->post_parent ) {
        $parents = array_reverse( get_post_ancestors( $post->ID ) );
        foreach ( $parents as $pid ) {
          echo $sep . '<a href="' . esc_url( get_permalink( $pid ) ) . '">' . esc_html( get_the_title( $pid ) ) . '</a>';
        }
      }
      echo $sep . esc_html( get_the_title() );

    } elseif ( is_archive() ) {
      echo $sep . get_the_archive_title();
    } elseif ( is_search() ) {
      echo $sep . esc_html__( 'Search results', 'my-custom-theme' );
    }
  } else {
    echo esc_html__( 'Home', 'my-custom-theme' );
  }
  echo '</nav>';
}

/* ──────────────────────────
 5. Счётчик просмотров
────────────────────────── */
/* ──────────────────────────
 5. Счётчик просмотров + инициализация метаполя
────────────────────────── */
// Инкремент при просмотре товара
add_action( 'template_redirect', function() {
  if ( is_singular( 'product' ) ) {
    $id    = get_the_ID();
    $views = (int) get_post_meta( $id, 'views', true );
    update_post_meta( $id, 'views', $views + 1 );
  }
}, 20 );

// Разовая инициализация views = 0 для всех товаров
add_action( 'init', 'mytheme_initialize_views_once', 11 );
function mytheme_initialize_views_once() {
  if ( get_option( 'mytheme_views_initialized' ) ) {
    return;
  }
  $all = get_posts([
    'post_type'      => 'product',
    'posts_per_page' => -1,
    'fields'         => 'ids',
  ]);
  if ( ! empty( $all ) ) {
    foreach ( $all as $id ) {
      // Если ещё нет мета-поля — создаём с 0
      if ( '' === get_post_meta( $id, 'views', true ) ) {
        update_post_meta( $id, 'views', 0 );
      }
    }
  }
  update_option( 'mytheme_views_initialized', 1 );
}


/* ──────────────────────────
 6. Фильтрация товаров в каталогах
────────────────────────── */
add_action( 'woocommerce_product_query', 'mytheme_filter_subcategory_query' );
function mytheme_filter_subcategory_query( $q ) {
  if ( ! is_tax( 'product_cat' ) ) {
    return;
  }
  if ( ! empty( $_GET['orderby'] ) ) {
    $o = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
    $q->set( 'orderby', $o );
    $q->set( 'order', $o === 'date' ? 'DESC' : 'ASC' );
  }
  if ( ! empty( $_GET['per_page'] ) ) {
    $q->set( 'posts_per_page', intval( wp_unslash( $_GET['per_page'] ) ) );
  }
  if ( isset( $_GET['used_only'] ) ) {
    $mq = $q->get( 'meta_query' ) ?: array();
    $mq[] = array( 'key'=>'_condition','value'=>'used','compare'=>'=' );
    $q->set( 'meta_query', $mq );
  }
}

/* ──────────────────────────
 7. Установка случайных Hot Deals
────────────────────────── */
function mytheme_assign_random_hot_deals() {
  $all = get_posts([ 'post_type'=>'product','posts_per_page'=>-1,'fields'=>'ids' ]);
  if ( ! $all ) {
    return;
  }
  shuffle( $all );
  $pick = array_slice( $all, 0, 15 );
  foreach ( $all as $id ) {
    update_post_meta( $id, 'hot_deal', in_array( $id, $pick, true ) ? '1' : '0' );
  }
}
add_action( 'init', 'mytheme_assign_random_hot_deals_once', 10 );
function mytheme_assign_random_hot_deals_once() {
  if ( get_option( 'mytheme_hot_assigned' ) ) {
    return;
  }
  mytheme_assign_random_hot_deals();
  update_option( 'mytheme_hot_assigned', 1 );
}

/* ──────────────────────────
 8. Роль user
────────────────────────── */
add_action( 'after_switch_theme', 'mytheme_add_custom_user_role' );

/**
 * Редирект на логин, если пытаются открыть /checkout/ неавторизованные
 */
add_action( 'template_redirect', 'mytheme_require_login_for_checkout' );
function mytheme_require_login_for_checkout() {
    if ( function_exists( 'is_checkout' )
      && is_checkout()
      && ! is_user_logged_in()
      && ! is_wc_endpoint_url( 'order-received' )  // чтобы после подтверждения сразу не редиректить
    ) {
        // отправляем на страницу логина, после логина вернём на checkout
        wp_redirect( wp_login_url( wc_get_checkout_url() ) );
        exit;
    }
}

