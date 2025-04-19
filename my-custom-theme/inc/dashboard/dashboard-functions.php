<?php
/**
 * Набор вспомогательных функций для личного кабинета
 * @package my-custom-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Возвращает статистику пользователя.
 */
function te_get_user_stats( $user_id ) {

    // кол-во объявлений (пример: CPT 'listing')
    $listings = (int) wp_count_posts( 'listing' )->publish;
    // избранное (допустим, храним в user_meta)
    $favs     = (int) get_user_meta( $user_id, 'te_favourites_count', true );
    // заказы (если WooCommerce)
    $orders   = function_exists('wc_get_customer_order_count')
        ? wc_get_customer_order_count( $user_id )
        : 0;

    return [
        'listings'   => $listings,
        'favourites' => $favs,
        'orders'     => $orders,
    ];
}

/**
 * Рендерит список объявлений пользователя (простая версия).
 */
function te_render_user_listings( $user_id ) {

    $args = [
        'post_type'      => 'listing',
        'author'         => $user_id,
        'posts_per_page' => 8,
    ];
    $q = new WP_Query( $args );

    if ( $q->have_posts() ) {
        echo '<ul class="listing-grid">';
        while ( $q->have_posts() ) {
            $q->the_post();
            echo '<li>';
            if ( has_post_thumbnail() ) {
                the_post_thumbnail( 'thumbnail' );
            }
            echo '<p>' . get_the_title() . '</p>';
            echo '</li>';
        }
        echo '</ul>';
        wp_reset_postdata();
    } else {
        echo '<p>У вас пока нет объявлений.</p>';
    }
}
