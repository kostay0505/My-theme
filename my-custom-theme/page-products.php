<?php
/**
 * Template Name: Products Page
 * Обычный каталог товаров
 * @package my-custom-theme
 */
get_header();
mytheme_breadcrumbs();

// Секция каталога из query-аргумента
$section = isset( $_GET['section'] ) ? sanitize_key( wp_unslash( $_GET['section'] ) ) : 'dashboard';
// Получаем объект терма по slug
$term = $section ? get_term_by( 'slug', $section, 'product_cat' ) : false;
?>

<div class="page-wrapper account-wrapper">
  <!-- Сайдбар каталога -->
  <aside class="account-sidebar">
    <h3 class="catalog-title"><?php esc_html_e( 'PRODUCT', 'my-custom-theme' ); ?></h3>
    <?php
    $menu = [
      'dashboard' => ['dashboard.svg', 'Dashboard'],
      'audio'     => ['audio.svg', 'AUDIO'],
      'lighting'  => ['lighting.svg', 'LIGHTING'],
      'visual'    => ['visual.svg', 'VISUAL'],
      'rigging'   => ['rigging.svg', 'RIGGING – POWER DISTRIBUTION'],
      'staging'   => ['staging.svg', 'STAGING – TRUSSING'],
    ];
    ?>
    <nav class="account-menu" aria-label="<?php esc_attr_e( 'Catalog Menu', 'my-custom-theme' ); ?>">
      <ul>
        <?php foreach ( $menu as $slug_key => $item ) : ?>
          <li class="<?php echo $slug_key === $section ? 'is-active' : ''; ?>">
            <a href="<?php echo esc_url( add_query_arg( 'section', $slug_key, get_permalink() ) ); ?>">
              <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/' . $item[0] ); ?>" alt="">
              <?php echo esc_html( $item[1] ); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </aside>

  <!-- Контент каталога -->
  <main class="account-content">
    <?php if ( $term && ! is_wp_error( $term ) ) : ?>

      <?php
      // Получаем дочерние термины (подкатегории)
      $children = get_terms( array(
        'taxonomy'   => 'product_cat',
        'parent'     => $term->term_id,
        'hide_empty' => false,
      ) );
      ?>

      <?php if ( ! empty( $children ) && ! is_wp_error( $children ) ) : ?>
        <div class="catalog-grid">
          <?php foreach ( $children as $child ) : ?>
            <a href="<?php echo esc_url( add_query_arg( 'section', $child->slug, get_permalink() ) ); ?>" class="catalog-item">
              <?php echo esc_html( $child->name ); ?>
            </a>
          <?php endforeach; ?>
        </div>

      <?php else : ?>

        <h1 class="subcategory-title"><?php echo esc_html( $term->name ); ?></h1>

        <div class="filter-box">
          <form class="subcategory-filter" method="get">
            <input type="hidden" name="section" value="<?php echo esc_attr( $section ); ?>">
            <div class="filter-grid">
              <label>
                <?php esc_html_e( 'Sort by', 'my-custom-theme' ); ?>
                <select name="orderby">
                  <?php $current_orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ?? 'date' ) ); ?>
                  <option value="date" <?php selected( $current_orderby, 'date' ); ?>><?php esc_html_e( 'Date Descending', 'my-custom-theme' ); ?></option>
                  <option value="title" <?php selected( $current_orderby, 'title' ); ?>><?php esc_html_e( 'Title', 'my-custom-theme' ); ?></option>
                </select>
              </label>
              <label>
                <?php esc_html_e( 'Ads per Page', 'my-custom-theme' ); ?>
                <select name="per_page">
                  <?php $current_per = intval( wp_unslash( $_GET['per_page'] ?? 10 ) ); ?>
                  <?php foreach ( array( 10, 20, 40, 80 ) as $n ) : ?>
                    <option value="<?php echo esc_attr( $n ); ?>" <?php selected( $current_per, $n ); ?>><?php echo esc_html( $n ); ?></option>
                  <?php endforeach; ?>
                </select>
              </label>
            </div>
            <div class="filter-grid">
              <label>
                <?php esc_html_e( 'ZIP', 'my-custom-theme' ); ?>
                <input type="text" name="zip" value="<?php echo esc_attr( wp_unslash( $_GET['zip'] ?? '' ) ); ?>" placeholder="<?php esc_attr_e( 'ZIP', 'my-custom-theme' ); ?>">
              </label>
              <label>
                <?php esc_html_e( 'Radius', 'my-custom-theme' ); ?>
                <select name="radius">
                  <?php $current_radius = sanitize_text_field( wp_unslash( $_GET['radius'] ?? '5 km' ) ); ?>
                  <?php foreach ( array( '5 km', '10 km', '20 km', '50 km' ) as $r ) : ?>
                    <option value="<?php echo esc_attr( $r ); ?>" <?php selected( $current_radius, $r ); ?>><?php echo esc_html( $r ); ?></option>
                  <?php endforeach; ?>
                </select>
              </label>
            </div>
            <div class="filter-actions">
              <label class="filter-checkbox">
                <input type="checkbox" name="used_only" <?php checked( isset( $_GET['used_only'] ) ); ?>>
                <?php esc_html_e( 'Show only used items', 'my-custom-theme' ); ?>
              </label>
              <button type="submit" class="button"><?php esc_html_e( 'Filter', 'my-custom-theme' ); ?></button>
            </div>
          </form>
        </div>

        <?php
        $paged    = max( 1, get_query_var( 'paged', 1 ) );
        $orderby  = $current_orderby;
        $order    = ( 'date' === $orderby ) ? 'DESC' : 'ASC';
        $per_page = $current_per;
        $args = array(
          'post_type'      => 'product',
          'posts_per_page' => $per_page,
          'orderby'        => $orderby,
          'order'          => $order,
          'paged'          => $paged,
          'tax_query'      => array(
            array(
              'taxonomy' => 'product_cat',
              'field'    => 'slug',
              'terms'    => $section,
            ),
          ),
        );
        if ( isset( $_GET['used_only'] ) ) {
          $args['meta_query'][] = array(
            'key'     => '_condition',
            'value'   => 'used',
            'compare' => '=',
          );
        }
        $products = new WP_Query( $args );
        if ( $products->have_posts() ) : ?>

          <ul class="products-list">
            <?php while ( $products->have_posts() ) : $products->the_post(); ?>
              <?php wc_get_template_part( 'content', 'product' ); ?>
            <?php endwhile; ?>
          </ul>

          <div class="products-pagination">
            <?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => '&laquo;', 'next_text' => '&raquo;' ) ); ?>
          </div>

          <?php if ( $products->found_posts > 0 ) : ?>
            <div class="bottom-switcher">
              <span><?php esc_html_e( 'Show per page:', 'my-custom-theme' ); ?></span>
              <?php foreach ( array( 10, 20, 40, 80 ) as $n ) : ?>
                <?php if ( $n <= $products->found_posts ) : ?>
                  <?php
                  $query_args = wp_parse_args( $_SERVER['QUERY_STRING'] );
                  $query_args['per_page'] = $n;
                  $query_args['paged']    = 1;
                  $url = esc_url( add_query_arg( $query_args, get_permalink() ) );
                  $active = ( $per_page === $n ) ? 'active' : '';
                  ?>
                  <a href="<?php echo $url; ?>" class="<?php echo $active; ?>"><?php echo esc_html( $n ); ?></a>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php wp_reset_postdata(); ?>

        <?php else : ?>

          <p><?php esc_html_e( 'В этой категории товаров нет.', 'my-custom-theme' ); ?></p>

        <?php endif; // end have_posts ?>

      <?php endif; // end have_children ?>

    <?php else : ?>

      <p><?php esc_html_e( 'Секция не указана или категория не найдена.', 'my-custom-theme' ); ?></p>

    <?php endif; ?>
  </main>
</div>
<?php get_footer(); ?>
