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

    <!-- Бургер-кнопка: ведёт на /products/ -->
    <a href="<?php echo esc_url( home_url('/products/') ); ?>" class="burger-products" title="Products">
      <span class="line"></span>
      <span class="line"></span>
      <span class="line"></span>
      <span class="btn-text">Products</span>
    </a>

    <!-- Логотип -->
    <div class="logo">
      <a href="<?php echo esc_url( home_url() ); ?>">
        <img
          src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png"
          alt="<?php bloginfo('name'); ?>"
        >
      </a>
    </div>

    <!-- Основное меню (статичные ссылки) -->
    <nav class="main-nav">
      <a href="<?php echo esc_url( home_url('/equipment/') ); ?>">Оборудование</a>
      <a href="<?php echo esc_url( home_url('/installations/') ); ?>">Инсталляции</a>
      <a href="<?php echo esc_url( home_url('/news/') ); ?>">Новости</a>
      <a href="<?php echo esc_url( home_url('/questions/') ); ?>">Вопросы</a>
      <a href="<?php echo esc_url( home_url('/contacts/') ); ?>">Контакты</a>
      <a href="<?php echo esc_url( home_url('/about/') ); ?>">О нас</a>
    </nav>

    <!-- Иконки: корзина и аккаунт -->
    <div class="header-icons">
      <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cart-link" title="Корзина">
        <img
          src="<?php echo get_template_directory_uri(); ?>/assets/images/cart.png"
          alt="Cart"
        >
      </a>
      <a
        href="<?php echo is_user_logged_in()
          ? esc_url( home_url('/account/') )
          : esc_url( home_url('/user_login/') );
        ?>"
        class="account-link"
        title="<?php echo is_user_logged_in() ? 'Мой аккаунт' : 'Войти'; ?>"
      >
        <img
          src="<?php echo get_template_directory_uri(); ?>/assets/images/account.png"
          alt="Account"
        >
      </a>
    </div>

  </div>
</header>
