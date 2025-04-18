<?php
/**
 * dashboard-functions.php
 *
 * Функции для личного кабинета, которые помогут обрабатывать формы, обновлять посты и т.д.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Пример функции для обработки отправки нового объявления с фронтенда.
 * Используйте эту функцию в своем обработчике формы, например, через AJAX или как часть page template.
 */
function mytheme_handle_new_listing() {
    // Проверка nonce, прав, sanitize, и т.д.
    if ( isset( $_POST['listing_title'] ) && is_user_logged_in() ) {
        $post_data = array(
            'post_title'   => sanitize_text_field( $_POST['listing_title'] ),
            'post_content' => wp_kses_post( $_POST['listing_description'] ),
            'post_type'    => 'post', // или 'listings'
            'post_status'  => 'pending', // создаём со статусом pending
            'post_author'  => get_current_user_id(),
        );
        $new_post_id = wp_insert_post( $post_data );
        if ( $new_post_id ) {
            wp_send_json_success( array( 'message' => 'Объявление отправлено на модерацию.' ) );
        } else {
            wp_send_json_error( array( 'message' => 'Ошибка отправки.' ) );
        }
    }
    wp_die();
}
add_action( 'wp_ajax_mytheme_new_listing', 'mytheme_handle_new_listing' );
add_action( 'wp_ajax_nopriv_mytheme_new_listing', 'mytheme_handle_new_listing' );
