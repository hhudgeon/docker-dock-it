<?php
/**
 * Archive: Books (CPT: hh-book)
 * Layout: each book is a comic-style panel (thumb left, excerpt right)
 * Pagination: numeric (5 per page), styled via CSS
 */

get_header(); ?>

    <header class="archive-header">
        <h1 class="archive-title">Books:</h1>

        <?php
        // --- Book Type filter panel (UNDER the H1; separate small panel) ---
        $taxonomy        = \HHBooksReviews\BookTypeTaxonomy::TAXONOMY; // 'hh_book_type'
        $terms           = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,   // show terms even if not assigned yet
            'orderby'    => 'name',
            'order'      => 'ASC',
        ]);
        $current_term_id = is_tax($taxonomy) ? get_queried_object_id() : 0;
        $archive_link    = get_post_type_archive_link(\HHBooksReviews\BookPostType::POST_TYPE);
        ?>

        <?php if ( ! is_wp_error($terms) && ! empty($terms) ) : ?>
            <section class="hh-panel book-type-panel" aria-label="Filter by Book Type">
                <h2 class="book-type-panel__title">Book Type</h2>
                <ul class="book-type-filter">
                    <li>
                        <a class="book-type-filter__link <?php echo $current_term_id ? '' : 'is-active'; ?>"
                           href="<?php echo esc_url($archive_link); ?>">
                            <span class="book-type-filter__box" aria-hidden="true"></span>
                            <span class="book-type-filter__label">All</span>
                        </a>
                    </li>
                    <?php foreach ( $terms as $t ) : ?>
                        <li>
                            <a class="book-type-filter__link <?php echo ( $current_term_id === $t->term_id ) ? 'is-active' : ''; ?>"
                               href="<?php echo esc_url( get_term_link( $t ) ); ?>">
                                <span class="book-type-filter__box" aria-hidden="true"></span>
                                <span class="book-type-filter__label"><?php echo esc_html( $t->name ); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

    </header>

<?php if ( have_posts() ) : ?>
    <div class="books-archive">
        <?php while ( have_posts() ) : the_post(); ?>
            <article <?php post_class('hh-panel book-archive__item'); ?>>
                <div class="book-archive__grid">
                    <a class="book-archive__coverlink" href="<?php the_permalink(); ?>">
                        <?php
                        if ( has_post_thumbnail() ) {
                            the_post_thumbnail( 'large', ['class' => 'book-archive__cover book-cover'] );
                        }
                        ?>
                    </a>

                    <div class="book-archive__content">
                        <h2 class="book-archive__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>

                        <div class="book-archive__excerpt">
                            <?php
                            // 50-word excerpt (fallback to trimmed content if the excerpt is empty)
                            $excerpt = get_the_excerpt();
                            if ( ! $excerpt ) {
                                $excerpt = wp_strip_all_tags( get_the_content() );
                            }
                            echo wp_kses_post( wpautop( wp_trim_words( $excerpt, 50 ) ) );
                            ?>
                        </div>

                        <p class="book-archive__readmore-wrap">
                            <a class="ast-button book-archive__readmore" href="<?php the_permalink(); ?>">
                                Read more
                            </a>
                        </p>
                    </div>
                </div>
            </article>
        <?php endwhile; ?>
    </div>

    <?php
    // ---------- Pagination (robust) ----------
    global $wp_query;

    // Build a correct base and format for your site structure
    $big    = 999999999; // need an unlikely integer
    $base   = str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) );

    // If pretty permalinks are on, WP expects /page/2/, otherwise ?paged=2
    $format = get_option('permalink_structure') ? 'page/%#%/' : '&paged=%#%';

    $current_page = (int) max( 1, get_query_var('paged'), get_query_var('page') );

    $links = paginate_links([
        'total'     => max( 1, (int) $wp_query->max_num_pages ),
        'current'   => max( 1, get_query_var('paged'), get_query_var('page') ),
        'type'      => 'array',
        'mid_size'  => 1,
        'end_size'  => 1,
        'prev_text' => '«',
        'next_text' => '»',
    ] );

    if ( $links ) : ?>
        <nav class="books-pagination" aria-label="Books pagination">
            <ul class="page-numbers">
                <?php foreach ( $links as $link ) : ?>
                    <li><?php echo $link; ?></li>
                <?php endforeach; ?>
            </ul>
        </nav>
    <?php endif; ?>
    <!-- Debug (optional): remove after confirming -->
    <?php  echo '<!-- found_posts=' . esc_html($wp_query->found_posts) . ' max=' . esc_html($wp_query->max_num_pages) . ' paged=' . esc_html($current_page) . ' -->';  ?>

<?php else : ?>
    <p>No books found.</p>
<?php endif; ?>

<?php get_footer();
