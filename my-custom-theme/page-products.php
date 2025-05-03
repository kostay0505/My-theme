<?php
/**
 * Template Name: Products Page
 * Обычный каталог товаров, включая Dashboard и Заказы
 * @package my-custom-theme
 */

get_header();
mytheme_breadcrumbs();

// 1) Секция из GET
$section = isset( $_GET['section'] ) ? sanitize_key( wp_unslash( $_GET['section'] ) ) : 'dashboard';
// Term только если не dashboard или orders
$term = ( 'dashboard' !== $section && 'orders' !== $section )
  ? get_term_by( 'slug', $section, 'product_cat' )
  : false;

// 2) Меню
$menu = [
  'dashboard' => ['dashboard.svg','Dashboard'],
  'audio'     => ['audio.svg','AUDIO'],
  'lighting'  => ['lighting.svg','LIGHTING'],
  'visual'    => ['visual.svg','VISUAL'],
  'rigging'   => ['rigging.svg','RIGGING – POWER DISTRIBUTION'],
  'staging'   => ['staging.svg','STAGING – TRUSSING'],
  'orders'    => ['orders.svg','Заказы'],
];
?>
<div class="page-wrapper account-wrapper">
  <aside class="account-sidebar">
    <h3 class="catalog-title"><?php esc_html_e( 'PRODUCT', 'my-custom-theme' ); ?></h3>
    <nav class="account-menu">
      <ul>
        <?php foreach ( $menu as $slug_key => $item ) :
          $is_active = ( $slug_key === $section ) ? 'is-active' : '';
          $url       = esc_url( add_query_arg( 'section', $slug_key, get_permalink() ) );
          // Для заказов добавляем счётчик
          $count_html = '';
          if ( 'orders' === $slug_key && is_user_logged_in() ) {
            $cnt = wc_get_customer_order_count( get_current_user_id() );
            $count_html = '<span class="menu-count">'. intval( $cnt ) .'</span>';
          }
        ?>
        <li class="<?php echo $is_active; ?>">
          <a href="<?php echo $url; ?>">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/' . $item[0] ); ?>" alt="">
            <?php echo esc_html( $item[1] ); ?><?php echo $count_html; ?>
          </a>
        </li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </aside>

  <main class="account-content">

  <?php
  // 3) Обработка секции «orders»
  if ( 'orders' === $section ) :

    if ( ! is_user_logged_in() ) {
      echo '<p>'. esc_html__( 'Пожалуйста, авторизуйтесь, чтобы просматривать ваши заказы.', 'my-custom-theme' ) .'</p>';
      return;
    }

    // Конкретный заказ?
    if ( ! empty( $_GET['order_id'] ) ) {
      $order_id = intval( $_GET['order_id'] );
      $order    = wc_get_order( $order_id );
      if ( $order && (int)$order->get_user_id() === get_current_user_id() ) {
        // Выводим стандартный шаблон деталей заказа
        wc_get_template( 'checkout/thankyou.php', [ 'order' => $order ] );
      } else {
        echo '<p>'. esc_html__( 'Заказ не найден.', 'my-custom-theme' ) .'</p>';
      }
      return;
    }

    // Список заказов
    $paged  = max( 1, get_query_var( 'paged', 1 ) );
    $per    = intval( $_GET['per_page'] ?? 10 );
    $orders = wc_get_orders( [
      'customer_id' => get_current_user_id(),
      'limit'       => $per,
      'page'        => $paged,
      'paginate'    => true,
      'orderby'     => 'date',
      'order'       => 'DESC',
    ] );

    if ( $orders->orders ) {
      echo '<ul class="orders-list">';
      foreach ( $orders->orders as $o ) {
        $link = esc_url( add_query_arg( [
          'section'  => 'orders',
          'order_id' => $o->get_id(),
        ], get_permalink() ) );
        echo '<li class="order-item">';
        echo '<a href="'. $link .'">';
        printf(
          '%1$s #%2$s — %3$s — %4$s',
          esc_html__( 'Заказ', 'my-custom-theme' ),
          esc_html( $o->get_order_number() ),
          esc_html( $o->get_date_created()->date_i18n( 'd.m.Y' ) ),
          wp_kses_post( wc_price( $o->get_total() ) )
        );
        echo '</a></li>';
      }
      echo '</ul>';
      echo '<div class="orders-pagination">';
      echo paginate_links( [
        'current'  => $paged,
        'total'    => $orders->max_num_pages,
        'format'   => '?section=orders&paged=%#%',
        'add_args' => [ 'per_page' => $per ],
      ] );
      echo '</div>';
    } else {
      echo '<p>'. esc_html__( 'У вас ещё нет заказов.', 'my-custom-theme' ) .'</p>';
    }

    return;

  // 4) Секция Dashboard
  elseif ( 'dashboard' === $section ) :
    // ... здесь ваш блок Dashboard (все товары) ...
    // (копируйте ваш существующий код вывода всех товаров)
    return;

  // 5) Секция таксономии
  elseif ( $term && ! is_wp_error( $term ) ) :
    // ... тут ваш код для подкатегорий и товаров внутри ...
    return;

  else :
    echo '<p>'. esc_html__( 'Раздел не найден.', 'my-custom-theme' ) .'</p>';
  endif;
  ?>

  </main>
</div>

<?php get_footer(); ?>
