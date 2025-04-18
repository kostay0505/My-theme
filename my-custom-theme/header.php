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

    <!-- Иконки: корзина и аккаунт -->
    <div class="header-icons">
      <a href="<?php echo wc_get_cart_url(); ?>" class="cart-link">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/cart.png" alt="Cart">
      </a>
      <a href="<?php echo home_url('/register/'); ?>" class="account-link">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/account.png" alt="Account">
      </a>
    </div>

  </div>
</header>
