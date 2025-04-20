<?php
/**
 * Template Name: Account Page
 * -----------------------------------------------------------------
 * Личный кабинет пользователя
 * -----------------------------------------------------------------
 *  ▸ URL при выходе:  wp_logout_url( home_url( '/' ) )
 *  ▸ Дополнительные «части» шаблонов хранятся в  /inc/account/
 *      ├ dashboard.php          – «Основная информация / Подписки»
 *      ├ personal-info.php      – «Личная информация»
 *      ├ billing-details.php    – «Платёжные реквизиты»
 *      └ cart.php               – «Корзина»
 *
 *  NB!  Если вы добавите новые разделы, просто положите файл‑часть
 *       в /inc/account/  и добавьте пункт в $menu ниже.
 * -----------------------------------------------------------------
 * @package my-custom-theme
 */

if ( ! is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/user_login/' ) );
	exit;
}

get_header();
mytheme_breadcrumbs();

/*------------------------------------------------------------------
| Данные текущего пользователя
*-----------------------------------------------------------------*/
$current_user = wp_get_current_user();

/*------------------------------------------------------------------
| Активный раздел (?section=…)    default = dashboard
*-----------------------------------------------------------------*/
$section       = isset( $_GET['section'] ) ? sanitize_key( $_GET['section'] ) : 'dashboard';
$available_tpl = array(
	'dashboard',
	'personal-info',
	'billing-details',
	'cart',
);

/*------------------------------------------------------------------
| Меню «Сайдбар»   slug => [icon‑file, label]
*-----------------------------------------------------------------*/
$menu = array(
	'dashboard'       => array( 'dashboard.svg',      'Основная информация' ),
	'personal-info'   => array( 'user.svg',           'Личная информация'   ),
	'billing-details' => array( 'billing.svg',        'Платёжные реквизиты' ),
	'cart'            => array( 'cart.svg',           'Корзина'             ),
	'orders'          => array( 'orders.svg',         'Заказы'              ),
	'favourites'      => array( 'heart.svg',          'Избранное'           ),
	'listing-create'  => array( 'add.svg',            'Создать объявление'  ),
	'listing-my'      => array( 'list.svg',           'Мои объявления'      ),
	'stats'           => array( 'stats.svg',          'Статистика'          ),
);
?>
<!-- ============================================================= -->
<div class="page-wrapper account-wrapper">

	<!-- ─────────────── Сайдбар ─────────────── -->
	<aside class="account-sidebar">
		<div class="account-profile">
			<?php echo get_avatar( $current_user->ID, 64, '', '', array( 'class' => 'avatar' ) ); ?>
			<div class="account-name"><?php echo esc_html( $current_user->display_name ?: $current_user->user_login ); ?></div>
			<div class="account-mail"><?php echo esc_html( $current_user->user_email ); ?></div>
		</div>

		<nav class="account-menu" aria-label="Меню аккаунта">
			<ul>
				<?php
				foreach ( $menu as $slug => $item ) :
					$url   = esc_url( add_query_arg( 'section', $slug, get_permalink() ) );
					$icon  = esc_attr( $item[0] );
					$label = esc_html( $item[1] );
					$active = $slug === $section ? 'is-active' : '';
					?>
					<li class="<?php echo $active; ?>">
						<a href="<?php echo $url; ?>">
							<img src="<?php echo get_template_directory_uri() . '/assets/icons/' . $icon; ?>" alt="">
							<?php echo $label; ?>
						</a>
					</li>
				<?php endforeach; ?>

				<li class="logout">
					<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>">
						<img src="<?php echo get_template_directory_uri(); ?>/assets/icons/logout.svg" alt="">
						Выйти&nbsp;из&nbsp;аккаунта
					</a>
				</li>
			</ul>
		</nav>
	</aside><!-- /.account-sidebar -->

	<!-- ─────────── Контентная область ─────────── -->
	<main class="account-content">
		<?php
		/* ---------------------------------------------
		 *  Подключаем «часть» в зависимости от $section
		 * -------------------------------------------*/
		$template_slug = in_array( $section, $available_tpl, true ) ? $section : 'dashboard';
		get_template_part( 'inc/account/' . $template_slug );
		?>
	</main><!-- /.account-content -->

</div><!-- /.page-wrapper -->

<?php get_footer();
