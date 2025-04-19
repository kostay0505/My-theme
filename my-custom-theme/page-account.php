<?php
/**
 * Template Name: Account Page
 * Description: Личный кабинет пользователя (sidebar + статистика + объявления)
 */

get_header();

// Если не авторизован — перенаправляем на страницу логина
if ( ! is_user_logged_in() ) {
    wp_redirect( home_url( '/user-login/' ) );
    exit;
}

// Данные текущего пользователя
$current_id   = get_current_user_id();
$current_user = wp_get_current_user();

// Хлебные крошки (если есть функция)
if ( function_exists( 'mytheme_breadcrumbs' ) ) {
    mytheme_breadcrumbs();
}
?>

<div class="page-wrapper">
  <main class="site-main">

    <div class="account-layout"><!-- FLEX-контейнер -->

      <!-- SIDEBAR -->
      <aside class="account-sidebar">
        <div class="user-card">
          <div class="avatar">
            <?php echo get_avatar( $current_id, 80 ); ?>
          </div>
          <p class="user-name"><?php echo esc_html( $current_user->display_name ); ?></p>
          <p class="user-email"><?php echo esc_html( $current_user->user_email ); ?></p>
        </div>

        <ul class="nav">
          <li class="is-active"><span class="ico">🏠</span>Обзор</li>
          <li><span class="ico">📄</span>Мои объявления</li>
          <li><span class="ico">❤️</span>Избранное</li>
          <li><span class="ico">⚙️</span>Настройки</li>
          <li>
            <span class="ico">🚪</span>
            <a href="<?php echo wp_logout_url( home_url() ); ?>">Выход</a>
          </li>
        </ul>
      </aside>

      <!-- CONTENT -->
      <div class="account-content">

        <!-- СТАТИСТИКА -->
        <div class="account-stats">
          <?php
            $stats = te_get_user_stats( $current_id );
          ?>
          <div class="stat">
            <p class="stat-label">Объявления</p>
            <p class="stat-num"><?php echo intval( $stats['listings'] ); ?></p>
          </div>
          <div class="stat">
            <p class="stat-label">Избранное</p>
            <p class="stat-num"><?php echo intval( $stats['favourites'] ); ?></p>
          </div>
          <div class="stat">
            <p class="stat-label">Заказы</p>
            <p class="stat-num"><?php echo intval( $stats['orders'] ); ?></p>
          </div>
        </div>

        <!-- МОИ ОБЪЯВЛЕНИЯ -->
        <h3 class="block-title">Мои объявления</h3>
        <div class="account-listings">
          <?php te_render_user_listings( $current_id ); ?>
        </div>

        <!-- ПОДПИСКИ -->
        <div class="account-subscriptions">
          <button class="btn-accent">Продлить подписку</button>
          <button class="btn-outline">Отказаться</button>
        </div>

      </div><!-- /.account-content -->

    </div><!-- /.account-layout -->

  </main>
</div><!-- /.page-wrapper -->

<?php get_footer(); ?>
