<?php
/**
 * Template Name: Products Page
 * Обычный каталог товаров
 * @package my-custom-theme
 */

get_header();
mytheme_breadcrumbs();

// Секция каталога из query-аргумента
$section = isset( $_GET['section'] ) ? sanitize_key( $_GET['section'] ) : 'dashboard';
?>

<div class="page-wrapper account-wrapper">

  <!-- Сайдбар каталога -->
  <aside class="account-sidebar">
    <h3 class="catalog-title">PRODUCT</h3>
    <?php
    // slug => [иконка, название]
    $menu = [
      'dashboard' => ['dashboard.svg', 'Dashboard'],
      'audio'     => ['audio.svg',     'AUDIO'],
      'lighting'  => ['lighting.svg',  'LIGHTING'],
      'visual'    => ['visual.svg',    'VISUAL'],
      'rigging'   => ['rigging.svg',   'RIGGING – POWER DISTRIBUTION'],
      'staging'   => ['staging.svg',   'STAGING – TRUSSING'],
    ];
    ?>
    <nav class="account-menu" aria-label="Меню каталога">
      <ul>
        <?php foreach ( $menu as $slug => $item ) : ?>
          <li class="<?php echo $slug === $section ? 'is-active' : ''; ?>">
            <a href="<?php echo esc_url( add_query_arg( 'section', $slug, get_permalink() ) ); ?>">
              <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/<?php echo esc_attr( $item[0] ); ?>" alt="">
              <?php echo esc_html( $item[1] ); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </aside>

  <!-- Контент каталога -->
  <main class="account-content">
    <?php
      // Подключаем часть из inc/catalog/{section}.php
      $part = get_template_directory() . "/inc/catalog/{$section}.php";
      if ( file_exists( $part ) ) {
        include $part;
      } else {
        echo '<p>Секция ещё в разработке…</p>';
      }
    ?>
  </main>

</div><!-- .page-wrapper -->

<?php get_footer(); ?>
