<?php
/**
 * Header template
 * @package my-custom-theme
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="site-header">
  <div class="header-inner container">

    <!-- Бургер‑меню «Products» -->
    <button class="burger-products" aria-label="Product menu">
      <span class="line"></span><span class="line"></span><span class="line"></span>
      <span class="btn-text">Products</span>
    </button>

    <!-- Логотип -->
    <div class="logo">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" alt="Touring Expert">
      </a>
    </div>

    <!-- Главное меню (при желании замените на wp_nav_menu) -->
    <nav class="main-nav" aria-label="Основное меню">
      <a href="#">Оборудование</a>
      <a href="#">Инсталляции</a>
      <a href="#">Новости</a>
      <a href="#">Вопросы</a>
      <a href="#">Контакты</a>
      <a href="#">О нас</a>
    </nav>

    <!-- Иконки корзины и аккаунта -->
    <div class="header-icons">
      <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cart-link" title="Корзина">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/cart.png" alt="Cart">
      </a>

      <?php
        // если залогинен – ведём в /account/, иначе – на /user_login/
        $account_url = is_user_logged_in() ? home_url( '/account/' ) : home_url( '/user_login/' );
      ?>
      <a href="<?php echo esc_url( $account_url ); ?>" class="account-link" title="Аккаунт">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/account.png" alt="Account">
      </a>
    </div>

  </div><!-- .header-inner -->
</header>
