<?php
/**
 * functions.php
 * Основные настройки темы: меню, стили, WooCommerce, хлебные крошки,
 * а также подключение ролей и функционала личного кабинета.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Защита от прямого доступа
}

// Подключаем кастомные роли
require_once get_template_directory() . '/inc/roles.php';

// Подключаем логику личного кабинета (шорткоды, проверки и т.п.)
require_once get_template_directory() . '/inc/dashboard/dashboard-functions.php';

/**
 * Настройка темы: title-tag, миниатюры, меню.
 */
function mytheme_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    register_nav_menus( array(
        'header_menu' => 'Main Header Menu',
    ) );
}
add_action( 'after_setup_theme', 'mytheme_setup' );

/**
 * Подключение стилей и шрифтов.
 */
function mytheme_enqueue_scripts() {
    // Основной стиль
    wp_enqueue_style( 'mytheme-style', get_stylesheet_uri(), array(), '2.1' );
    // Google Font Inter
    wp_enqueue_style(
        'inter-font',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap',
        array(),
        null
    );
}
add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_scripts' );

/**
 * Поддержка WooCommerce.
 */
function mytheme_add_woocommerce_support() {
    add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );

/**
 * Вывод хлебных крошек.
 */
function mytheme_breadcrumbs() {
    echo '<nav class="breadcrumbs">';
    if ( ! is_front_page() ) {
        echo '<a href="' . esc_url( home_url() ) . '">Home</a> / ';
        if ( is_page() ) {
            global $post;
            if ( $post->post_parent ) {
                $ancestors = array_reverse( get_post_ancestors( $post->ID ) );
                foreach ( $ancestors as $ancestor ) {
                    echo '<a href="' . esc_url( get_permalink( $ancestor ) ) . '">'
                         . esc_html( get_the_title( $ancestor ) )
                         . '</a> / ';
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
