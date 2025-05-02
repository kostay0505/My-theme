<?php
/**
 * Template Name: Products Page
 * Обычный каталог товаров, включая Dashboard
 * @package my-custom-theme
 */

get_header();
mytheme_breadcrumbs();

// Секция каталога из query-аргумента
$section = isset( $_GET['section'] ) ? sanitize_key( wp_unslash( $_GET['section'] ) ) : 'dashboard';
// Получаем объект терма по slug (для всех, кроме dashboard)
$term = ( 'dashboard' !== $section ) ? get_term_by( 'slug', $section, 'product_cat' ) : false;
?>

<div class="page-wrapper account-wrapper">
  <!-- Сайдбар каталога -->
  <aside class="account-sidebar">
    <h3 class="catalog-title"><?php esc_html_e( 'PRODUCT', 'my-custom-theme' ); ?></h3>
    <?php
    // Меню разделов
    $menu = [
      'dashboard' => ['dashboard.svg','Dashboard'],
      'audio'     => ['audio.svg','AUDIO'],
      'lighting'  => ['lighting.svg','LIGHTING'],
      'visual'    => ['visual.svg','VISUAL'],
      'rigging'   => ['rigging.svg','RIGGING – POWER DISTRIBUTION'],
      'staging'   => ['staging.svg','STAGING – TRUSSING'],
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

    <?php if ( 'dashboard' === $section ) : ?>
      <?php
      // Dashboard: показываем все товары
      $current_orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ?? 'date' ) );
      $current_per     = intval( wp_unslash( $_GET['per_page'] ?? 10 ) );
      $paged           = max( 1, get_query_var( 'paged', 1 ) );
      $order           = ( 'date' === $current_orderby ) ? 'DESC' : 'ASC';
      $args = [
        'post_type'      => 'product',
        'posts_per_page' => $current_per,
        'orderby'        => $current_orderby,
        'order'          => $order,
        'paged'          => $paged,
      ];
      if ( isset( $_GET['used_only'] ) ) {
        $args['meta_query'][] = [ 'key'=>'_condition','value'=>'used','compare'=>'=' ];
      }
      $products = new WP_Query( $args );
      ?>

      <h1 class="subcategory-title"><?php esc_html_e( 'All Listings', 'my-custom-theme' ); ?></h1>

      <!-- Форма фильтра -->
      <div class="filter-box">
          <!-- Поисковая строка -->
          <div class="filter-search">
            <label for="product-search-input"><?php esc_html_e( 'Search', 'my-custom-theme' ); ?></label>
            <input type="text" id="product-search-input" placeholder="<?php esc_attr_e( 'Search by title...', 'my-custom-theme' ); ?>">
            <div id="search-suggestions" class="search-suggestions"></div>
          </div>
        <form class="subcategory-filter" method="get">
          <input type="hidden" name="section" value="dashboard">
          <div class="filter-grid">
            <label>
              <?php esc_html_e( 'Sort by', 'my-custom-theme' ); ?>
              <select name="orderby">
                <option value="date" <?php selected( $current_orderby, 'date' ); ?>><?php esc_html_e( 'Date Descending', 'my-custom-theme' ); ?></option>
                <option value="title" <?php selected( $current_orderby, 'title' ); ?>><?php esc_html_e( 'Title', 'my-custom-theme' ); ?></option>
              </select>
            </label>
            <label>
              <?php esc_html_e( 'Ads per Page', 'my-custom-theme' ); ?>
              <select name="per_page">
                <?php foreach ( [10,20,40,80] as $n ) : ?>
                  <?php if ( $n <= $products->found_posts ) : ?>
                    <option value="<?php echo esc_attr( $n ); ?>" <?php selected( $current_per, $n ); ?>><?php echo esc_html( $n ); ?></option>
                  <?php endif; endforeach; ?>
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

      <?php if ( $products->have_posts() ) : ?>
        <ul class="products-list">
          <?php while ( $products->have_posts() ) : $products->the_post(); ?>
            <?php wc_get_template_part( 'content', 'product' ); ?>
          <?php endwhile; ?>
        </ul>
        <?php wp_reset_postdata(); ?>

        <div class="products-pagination">
          <?php the_posts_pagination([ 'mid_size'=>1,'prev_text'=>'«','next_text'=>'»' ]); ?>
        </div>

        <div class="bottom-switcher">
          <span><?php esc_html_e( 'Show per page:', 'my-custom-theme' ); ?></span>
          <?php foreach ( [10,20,40,80] as $n ) : if ( $n <= $products->found_posts ) :
            $query = wp_parse_args( $_SERVER['QUERY_STRING'] );
            $query['per_page'] = $n;
            $query['paged']    = 1;
            $url = esc_url( add_query_arg( $query, get_permalink() ) );
            $active = ( $current_per === $n ) ? 'active' : '';
          ?>
            <a href="<?php echo $url; ?>" class="<?php echo esc_attr( $active ); ?>"><?php echo esc_html( $n ); ?></a>
          <?php endif; endforeach; ?>
        </div>

      <?php else: ?>
        <p><?php esc_html_e( 'No listings found.', 'my-custom-theme' ); ?></p>
      <?php endif; ?>

    <?php elseif ( $term && ! is_wp_error( $term ) ): ?>

      <?php
      // Существующие подкатегории / товары без дочерних категорий
      $children = get_terms([ 'taxonomy'=>'product_cat','parent'=>$term->term_id,'hide_empty'=>false ]);
      if ( ! empty( $children ) && ! is_wp_error( $children ) ) :
        echo '<div class="catalog-grid">';
        foreach ( $children as $child ) {
          echo '<a href="'.esc_url(add_query_arg(['section'=>$child->slug,'paged'=>1],get_permalink())).'" class="catalog-item">'.esc_html($child->name).'</a>';
        }
        echo '</div>';
      else:
        // подключаем единый шаблон для списка
        set_query_var('section',$section);
        set_query_var('term',$term);
        get_template_part('inc/catalog/catalog-section');
      endif;
      ?>

    <?php else: ?>
      <p><?php esc_html_e( 'Section not found.', 'my-custom-theme' ); ?></p>
    <?php endif; ?>
  </main>
</div>
<?php get_footer(); ?>
