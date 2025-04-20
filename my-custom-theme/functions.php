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

require_once get_template_directory() . '/inc/roles.php';                       // кастомная роль «user»
require_once get_template_directory() . '/inc/dashboard/dashboard-functions.php'; // (если нужен дополнительный код)

/* ──────────────────────────
   2.  Запуск темы
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
   3.  Стили и скрипты
────────────────────────── */

add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_assets' );
function mytheme_enqueue_assets() {

	// главный CSS
	wp_enqueue_style(
		'mytheme-style',
		get_stylesheet_uri(),      // style.css в корне темы
		array(),
		'2.7'                      // меняйте при каждом обновлении!
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
		array(),                   // зависимости (jQuery не нужен)
		'1.0',
		true                      // подключить в footer
	);
}

/* ──────────────────────────
   4.  Хлебные крошки
────────────────────────── */

function mytheme_breadcrumbs() {

	echo '<nav class="breadcrumbs" aria-label="breadcrumbs">';

	if ( ! is_front_page() ) {

		echo '<a href="' . esc_url( home_url() ) . '">Home</a> / ';

		if   ( is_page() ) {
			global $post;
			if ( $post->post_parent ) {
				$ancestors = array_reverse( get_post_ancestors( $post->ID ) );
				foreach ( $ancestors as $ancestor ) {
					echo '<a href="' . esc_url( get_permalink( $ancestor ) ) . '">' .
					     esc_html( get_the_title( $ancestor ) ) . '</a> / ';
				}
			}
			echo esc_html( get_the_title() );
		}
		elseif ( is_single() ) {
			the_title();
		}
		elseif ( is_archive() ) {
			the_archive_title();
		}

	} else {
		echo 'Home';
	}

	echo '</nav>';
}

/* ──────────────────────────
   5.  Добавляем роль «user»
   (реализация перенесена в inc/roles.php,
   здесь оставляем только вызов во время
   активации темы, чтобы не дублировать.)
────────────────────────── */

add_action( 'after_switch_theme', 'mytheme_add_custom_user_role' );
