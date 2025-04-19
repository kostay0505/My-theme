<?php
/**
 * Template Name: Account Page
 * Пользовательский личный кабинет
 * @package my-custom-theme
 */

if ( ! is_user_logged_in() ) {
  wp_safe_redirect( home_url( '/user_login/' ) );
  exit;
}

get_header();
mytheme_breadcrumbs();

/* --------------------------------------------------
 *  Данные пользователя
 * -------------------------------------------------- */
$current_user = wp_get_current_user();
$section      = isset( $_GET['section'] ) ? sanitize_key( $_GET['section'] ) : 'dashboard';
?>

<div class="page-wrapper account-wrapper">

  <!-- ==== Боковая панель ================================================= -->
  <aside class="account-sidebar">
    <div class="account-profile">
      <?php echo get_avatar( $current_user->ID, 64, '', '', array( 'class' => 'avatar' ) ); ?>
      <div class="account-name"><?php echo esc_html( $current_user->display_name ?: $current_user->user_login ); ?></div>
      <div class="account-mail"><?php echo esc_html( $current_user->user_email ); ?></div>
    </div>

    <?php
    // массив пунктов меню: slug → [иконка, подпись]
    $menu = array(
      'dashboard'       => array( 'dashboard.svg',      'Основная информация' ),
      'personal'        => array( 'user.svg',           'Личная информация'   ),
      'billing'         => array( 'billing.svg',        'Платежные реквизиты' ),
      'cart'            => array( 'cart.svg',           'Корзина'             ),
      'orders'          => array( 'orders.svg',         'Заказы'              ),
      'favourites'      => array( 'heart.svg',          'Избранное'           ),
      'listing-create'  => array( 'add.svg',            'Создать объявление'  ),
      'listing-my'      => array( 'list.svg',           'Мои объявления'      ),
      'stats'           => array( 'stats.svg',          'Статистика'          ),
    );
    ?>
    <nav class="account-menu" aria-label="Меню аккаунта">
      <ul>
        <?php foreach ( $menu as $slug => $item ) : ?>
          <li class="<?php echo $slug === $section ? 'is-active' : ''; ?>">
            <a href="<?php echo esc_url( add_query_arg( 'section', $slug, get_permalink() ) ); ?>">
              <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/<?php echo esc_attr( $item[0] ); ?>" alt="">
              <?php echo esc_html( $item[1] ); ?>
            </a>
          </li>
        <?php endforeach; ?>

        <!-- Выход -->
        <li class="logout">
          <a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/logout.svg" alt="">
            Выйти из аккаунта
          </a>
        </li>
      </ul>
    </nav>
  </aside>

  <!-- ==== Контентная область ============================================ -->
  <main class="account-content">

    <?php if ( $section === 'dashboard' ) : ?>

      <!-- --- Секция «Основное» --- -->
      <h2>Основное</h2>
      <div class="dash-cards">
        <a class="dash-card" href="#"><span class="number">4</span> <span class="label">Заказы</span></a>
        <a class="dash-card" href="#"><span class="number">3</span> <span class="label">Избранное</span></a>
        <a class="dash-card" href="#"><span class="number">1</span> <span class="label">Объявления</span></a>
        <a class="dash-card" href="#"><span class="number">—</span> <span class="label">Личные данные</span></a>
        <a class="dash-card" href="#"><span class="number">—</span> <span class="label">Платежные реквизиты</span></a>
      </div>

      <!-- --- Секция «Подписки» --- -->
      <h2>Подписки</h2>
      <div class="subs-cards">
        <div class="subs-card">
          <p>Подписка на&nbsp;новости по&nbsp;email</p>
          <button class="btn">Подписаться</button>
        </div>
        <div class="subs-card">
          <p>Подписка на&nbsp;новости в&nbsp;telegram</p>
          <button class="btn">Отписаться</button>
        </div>
      </div>

    <?php else : ?>

      <p>Страница <strong><?php echo esc_html( $section ); ?></strong> ещё в&nbsp;разработке…</p>

    <?php endif; ?>

  </main>

</div><!-- .page-wrapper -->

<?php get_footer(); ?>
