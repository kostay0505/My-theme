<?php
/**
 * Template Name: Account Page
 * Личный кабинет пользователя
 * @package my-custom-theme
 */

get_header();
mytheme_breadcrumbs();

// ID и URL страницы аккаунта
$account_page_id  = get_queried_object_id();
$account_page_url = get_permalink( $account_page_id );

// Определяем активный раздел
$section = isset( $_GET['section'] )
  ? sanitize_key( wp_unslash( $_GET['section'] ) )
  : 'dashboard';

// Меню ЛК
$menu = [
  'dashboard' => ['dashboard.svg', 'Основная информация'],
  'profile'   => ['profile.svg',   'Личная информация'],
  'payment'   => ['payment.svg',   'Платёжные реквизиты'],
  'cart'      => ['cart.svg',      'Корзина'],
  'orders'    => ['orders.svg',    'Заказы'],
  'ads'       => ['ads.svg',       'Объявления'],
  'favorites' => ['heart.svg',     'Избранное'],
  'logout'    => ['logout.svg',    'Выйти'],    // ← добавили пункт «Выйти»
];
?>
<div class="page-wrapper account-wrapper">
  <aside class="account-sidebar">
    <h3 class="catalog-title"><?php esc_html_e( 'ACCOUNT', 'my-custom-theme' ); ?></h3>
    <nav class="account-menu">
      <ul>
        <?php foreach ( $menu as $slug => $item ) :
          $count_html = '';
          $url        = '';

          // Счётчик для Заказы
          if ( 'orders' === $slug && is_user_logged_in() ) {
            $cnt = wc_get_customer_order_count( get_current_user_id() );
            $count_html = '<span class="menu-count">' . intval( $cnt ) . '</span>';
            $url = add_query_arg( 'section', 'orders', $account_page_url );

          // Счётчик для Объявления
          } elseif ( 'ads' === $slug && is_user_logged_in() ) {
            $ads_q = new WP_Query([
              'post_type'      => 'product',
              'author'         => get_current_user_id(),
              'post_status'    => ['publish','pending','draft'],
              'posts_per_page' => 1,
              'fields'         => 'ids',
            ]);
            $count_html = '<span class="menu-count">' . intval( $ads_q->found_posts ) . '</span>';
            $url = add_query_arg( 'section', 'ads', $account_page_url );

          // Пункт «Выйти»
          } elseif ( 'logout' === $slug ) {
            $url = wp_logout_url( $account_page_url );

          // Все остальные пункты
          } else {
            $url = add_query_arg( 'section', $slug, $account_page_url );
          }

          printf(
            '<li class="%1$s"><a href="%2$s"><img src="%3$s/assets/icons/%4$s" alt=""><span>%5$s</span>%6$s</a></li>',
            ( $slug === $section ) ? 'is-active' : '',
            esc_url( $url ),
            esc_url( get_template_directory_uri() ),
            esc_attr( $item[0] ),
            esc_html( $item[1] ),
            $count_html
          );
        endforeach; ?>
      </ul>
    </nav>
  </aside>

  <main class="account-content">
    <?php
    switch ( $section ) :

      case 'dashboard':
        get_template_part( 'inc/dashboard/dashboard-content' );
        break;

      case 'profile':
        get_template_part( 'inc/dashboard/profile' );
        break;

      case 'payment':
        get_template_part( 'inc/dashboard/payment-methods' );
        break;

      case 'cart':
        echo '<h2>' . esc_html__( 'Ваша корзина', 'my-custom-theme' ) . '</h2>';
        echo do_shortcode( '[woocommerce_cart]' );
        break;

      case 'orders':
        if ( ! is_user_logged_in() ) {
          echo '<p>' . esc_html__( 'Пожалуйста, авторизуйтесь для просмотра заказов.', 'my-custom-theme' ) . '</p>';
          break;
        }
        if ( ! empty( $_GET['order_id'] ) ) {
          $order = wc_get_order( intval( $_GET['order_id'] ) );
          if ( $order && $order->get_user_id() === get_current_user_id() ) {
            wc_get_template( 'checkout/thankyou.php', [ 'order' => $order ] );
          } else {
            echo '<p>' . esc_html__( 'Заказ не найден.', 'my-custom-theme' ) . '</p>';
          }
        } else {
          $paged  = max( 1, get_query_var( 'paged', 1 ) );
          $orders = wc_get_orders([
            'customer_id' => get_current_user_id(),
            'limit'       => 10,
            'page'        => $paged,
            'paginate'    => true,
            'orderby'     => 'date',
            'order'       => 'DESC',
          ]);
          echo '<h2>' . esc_html__( 'Мои заказы', 'my-custom-theme' ) . '</h2>';
          if ( $orders->orders ) {
            echo '<ul class="orders-list">';
            foreach ( $orders->orders as $o ) {
              $link = esc_url( add_query_arg([
                'section'=>'orders',
                'order_id'=> $o->get_id(),
              ], $account_page_url ) );
              printf(
                '<li><a href="%1$s">%2$s #%3$s — %4$s</a></li>',
                $link,
                esc_html__( 'Заказ', 'my-custom-theme' ),
                esc_html( $o->get_order_number() ),
                wc_price( $o->get_total() )
              );
            }
            echo '</ul>';
            echo paginate_links([
              'current' => $paged,
              'total'   => $orders->max_num_pages,
              'format'  => '?section=orders&paged=%#%',
            ]);
          } else {
            echo '<p>' . esc_html__( 'У вас ещё нет заказов.', 'my-custom-theme' ) . '</p>';
          }
        }
        break;

      case 'ads':
        if ( ! is_user_logged_in() ) {
          echo '<p>' . esc_html__( 'Авторизуйтесь, чтобы управлять объявлениями.', 'my-custom-theme' ) . '</p>';
          break;
        }
        // обработка new/edit handled in template part...
        $action = sanitize_text_field( wp_unslash( $_GET['action'] ?? '' ) );
        if ( in_array( $action, [ 'new', 'edit' ], true ) ) {
          set_query_var( 'account_page_url', $account_page_url );
          get_template_part( 'inc/dashboard/ad-editor' );
          break;
        }
        echo '<p><a class="button" href="'
             . esc_url( add_query_arg( [ 'section'=>'ads','action'=>'new' ], $account_page_url ) )
             . '">'
             . esc_html__( 'Новое объявление', 'my-custom-theme' )
             . '</a></p>';
        $paged = max( 1, get_query_var( 'paged', 1 ) );
        $ads_q = new WP_Query([
          'post_type'      => 'product',
          'author'         => get_current_user_id(),
          'post_status'    => ['publish','pending','draft'],
          'posts_per_page' => 10,
          'paged'          => $paged,
        ]);
        echo '<h2>' . esc_html__( 'Мои объявления', 'my-custom-theme' ) . '</h2>';
        if ( $ads_q->have_posts() ) {
          echo '<ul class="ads-list">';
          while ( $ads_q->have_posts() ) : $ads_q->the_post();
            $status = get_post_status_object( get_post_status() )->label;
            $edit_link = esc_url( add_query_arg([
              'section'=>'ads','action'=>'edit','ad_id'=>get_the_ID(),
            ], $account_page_url ) );
            printf(
              '<li><a href="%1$s">%2$s</a> — %3$s</li>',
              $edit_link,
              esc_html( get_the_title() ),
              esc_html( $status )
            );
          endwhile;
          echo '</ul>';
          echo paginate_links([
            'current' => $paged,
            'total'   => $ads_q->max_num_pages,
            'format'  => '?section=ads&paged=%#%',
          ]);
          wp_reset_postdata();
        } else {
          echo '<p>' . esc_html__( 'У вас пока нет объявлений.', 'my-custom-theme' ) . '</p>';
        }
        break;

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
