<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="site-header">
  <div class="header-inner container">

    <!-- Бургер‑кнопка -->
    <button class="burger-products">
      <span class="line"></span>
      <span class="line"></span>
      <span class="line"></span>
      <span class="btn-text">Products</span>
    </button>

    <!-- Логотип -->
    <div class="logo">
      <a href="<?php echo home_url(); ?>">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" alt="Touring Expert">
      </a>
    </div>

    <!-- Главное меню -->
    <nav class="main-nav">
      <a href="#">Оборудование</a>
      <a href="#">Инсталляции</a>
      <a href="#">Новости</a>
      <a href="#">Вопросы</a>
      <a href="#">Контакты</a>
      <a href="#">О нас</a>
    </nav>

    <!-- Иконки: корзина и аккаунт/выход -->
    <div class="header-icons">
      <!-- Корзина оставляем без изменений -->
      <a href="<?php echo wc_get_cart_url(); ?>" class="cart-link">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/cart.png" alt="Cart">
      </a>

      <!-- Аккаунт: если вошёл, — выход, иначе — форма входа -->
      <?php if ( is_user_logged_in() ) : ?>
        <a href="<?php echo wp_logout_url( home_url() ); ?>" class="account-link" title="Выйти">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logout.png" alt="Logout">
        </a>
      <?php else : ?>
        <a href="<?php echo home_url('/user_login/'); ?>" class="account-link" title="Войти">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/images/account.png" alt="Account">
        </a>
      <?php endif; ?>
    </div>

  </div>
</header>
