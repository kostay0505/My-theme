<?php
/**
 * Template Name: Account Page
 * Личный кабинет пользователя
 * @package my-custom-theme
 */

get_header();
mytheme_breadcrumbs();

// Определяем активный раздел из GET, по умолчанию — dashboard
$section = isset( $_GET['section'] ) ? sanitize_key( wp_unslash( $_GET['section'] ) ) : 'dashboard';

// Формируем меню ЛК
$menu = [
  'dashboard'      => ['dashboard.svg', 'Основная информация'],
  'profile'        => ['profile.svg',   'Личная информация'],
  'payment-method'=> ['payment.svg',    'Платёжные реквизиты'],
  'cart'           => ['cart.svg',       'Корзина'],
  'orders'         => ['orders.svg',     'Заказы'],
  'favorites'      => ['heart.svg',      'Избранное'],
];

?>
<div class="page-wrapper account-wrapper">
  <aside class="account-sidebar">
    <h3 class="catalog-title"><?php esc_html_e( 'ACCOUNT', 'my-custom-theme' ); ?></h3>
    <nav class="account-menu">
      <ul>
        <?php foreach ( $menu as $slug => $item ) :
          // Для «orders» добавляем счётчик
          $count_html = '';
          if ( 'orders' === $slug && is_user_logged_in() ) {
            $cnt = wc_get_customer_order_count( get_current_user_id() );
            $count_html = '<span class="menu-count">' . intval( $cnt ) . '</span>';
          }
          $active_class = $slug === $section ? 'is-active' : '';
          $url = esc_url( add_query_arg( 'section', $slug, get_permalink() ) );
        ?>
        <li class="<?php echo $active_class; ?>">
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
    switch ( $section ) :

      // 1) Основная информация (Dashboard)
      case 'dashboard':
        // Подключаем оригинальный файл из репозитория:
        get_template_part( 'inc/dashboard/dashboard-content' );
        break;

      // 2) Личная информация
      case 'profile':
        get_template_part( 'inc/dashboard/profile' );
        break;

      // 3) Платёжные реквизиты
      case 'payment-method':
        get_template_part( 'inc/dashboard/payment-methods' );
        break;

      // 4) Корзина
      case 'cart':
        // Если корзина пуста, можно редирект на /cart/ или вывести кол-во
        echo '<p>' . esc_html__( 'Ваша корзина', 'my-custom-theme' ) . '</p>';
        echo do_shortcode( '[woocommerce_cart]' );
        break;

      // 5) Заказы
      case 'orders':

        if ( ! is_user_logged_in() ) {
          echo '<p>' . esc_html__( 'Пожалуйста, авторизуйтесь для просмотра заказов.', 'my-custom-theme' ) . '</p>';
          break;
        }

        // Конкретный заказ?
        if ( ! empty( $_GET['order_id'] ) ) {
          $order_id = intval( $_GET['order_id'] );
          $order    = wc_get_order( $order_id );
          if ( $order && $order->get_user_id() === get_current_user_id() ) {
            // Смотрим детали заказа
            wc_get_template( 'checkout/thankyou.php', [ 'order' => $order ] );
          } else {
            echo '<p>' . esc_html__( 'Заказ не найден.', 'my-custom-theme' ) . '</p>';
          }
        } else {
          // Список заказов
          $paged  = max( 1, get_query_var( 'paged', 1 ) );
          $per    = 10;
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
              printf(
                '<li class="order-item"><a href="%1$s">%2$s #%3$s &mdash; %4$s</a></li>',
                $link,
                esc_html__( 'Заказ', 'my-custom-theme' ),
                esc_html( $o->get_order_number() ),
                wp_kses_post( wc_price( $o->get_total() ) )
              );
            }
            echo '</ul>';
            // Пагинация
            echo paginate_links( [
              'current' => $paged,
              'total'   => $orders->max_num_pages,
              'format'  => '?section=orders&paged=%#%',
            ] );
          } else {
            echo '<p>' . esc_html__( 'У вас ещё нет заказов.', 'my-custom-theme' ) . '</p>';
          }
        }
        break;

      // 6) Избранное
      case 'favorites':
        get_template_part( 'inc/dashboard/favorites' );
        break;

      default:
        echo '<p>' . esc_html__( 'Раздел не найден.', 'my-custom-theme' ) . '</p>';
        break;

    endswitch;
    ?>

  </main>
</div>

<?php get_footer(); ?>
