<?php
/**
 * Template Name: Account Page
 */
get_header();

// Если не залогинен — на страницу входа
if ( ! is_user_logged_in() ) {
  wp_redirect( home_url('/login/') );
  exit;
}

$current_user = wp_get_current_user();
mytheme_breadcrumbs();
?>

<div class="page-wrapper account-container container">

  <!-- Левая колонка -->
  <aside class="account-sidebar">
    <div class="avatar">
      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/account.png" alt="Avatar">
    </div>
    <div class="user-info">
      <p class="user-name">
        <?php echo esc_html( $current_user->first_name . ' ' . $current_user->last_name ); ?>
      </p>
      <p class="user-email">
        <?php echo esc_html( $current_user->user_email ); ?>
      </p>
    </div>
    <nav class="account-nav">
      <ul>
        <li><a href="#"><span class="dashicons dashicons-admin-users"></span> Основная информация</a></li>
        <li><a href="#"><span class="dashicons dashicons-admin-site-alt3"></span> Личная информация</a></li>
        <li><a href="#"><span class="dashicons dashicons-format-status"></span> Платёжные реквизиты</a></li>
        <li><a href="#"><span class="dashicons dashicons-cart"></span> Корзина</a></li>
        <li><a href="#"><span class="dashicons dashicons-list-view"></span> Заказы</a></li>
        <li><a href="#"><span class="dashicons dashicons-heart"></span> Избранное</a></li>
        <li><a href="#"><span class="dashicons dashicons-plus-alt2"></span> Создать объявление</a></li>
        <li><a href="#"><span class="dashicons dashicons-media-document"></span> Мои объявления</a></li>
        <li><a href="#"><span class="dashicons dashicons-chart-bar"></span> Статистика</a></li>
        <li><a href="<?php echo wp_logout_url( home_url() ); ?>">
            <span class="dashicons dashicons-dismiss"></span> Выйти из аккаунта
        </a></li>
      </ul>
    </nav>
  </aside>

  <!-- Правая рабочая зона -->
  <main class="account-main">
    <section class="account-hero">
      <h2>Основное</h2>
      <div class="account-cards">
        <div class="card">
          <p class="card-title">Заказы</p>
          <p class="card-number">4</p>
        </div>
        <div class="card">
          <p class="card-title">Добавлено в избранное</p>
          <p class="card-number">3</p>
        </div>
        <div class="card">
          <p class="card-title">Объявления</p>
          <p class="card-number">1</p>
        </div>
        <div class="card">
          <p class="card-title">Личные данные</p>
          <a href="#">Заполните личные данные</a>
        </div>
        <div class="card">
          <p class="card-title">Платёжные реквизиты</p>
          <a href="#">Добавьте реквизиты</a>
        </div>
      </div>
    </section>

    <section class="account-subscriptions">
      <h2>Подписки</h2>
      <div class="subs-cards">
        <div class="sub-card">
          <p>Подписка на новости по email</p>
          <button>Подписаться</button>
        </div>
        <div class="sub-card">
          <p>Подписка на новости в Telegram</p>
          <button>Отписаться</button>
        </div>
      </div>
    </section>
  </main>
</div>

<?php get_footer(); ?>
