<?php
/**
 * Taxonomy archive for Book Type (taxonomy: hh_book_type)
 * Mirrors the Books archive layout.
 */

get_header(); ?>

    <header class="archive-header">
        <h1 class="archive-title">
            <span class="archive-title__prefix">Book Type:</span>
            <span class="archive-title__term"><?php single_term_title(); ?></span>
        </h1>
        <?php
        $term_description = term_description();
        if ( $term_description ) : ?>
            <div class="taxonomy-description">
                <?php echo wp_kses_post( $term_description ); ?>
            </div>
        <?php endif; ?>
    </header>


<?php if ( have_posts() ) : ?>
    <div class="books-archive">
        <?php while ( have_posts() ) : the_post(); ?>
            <article <?php post_class('hh-panel book-archive__item'); ?>>
                <div class="book-archive__grid">
                    <a class="book-archive__coverlink" href="<?php the_permalink(); ?>">
                        <?php if ( has_post_thumbnail() ) {
                            the_post_thumbnail('large', ['class' => 'book-archive__cover book-cover']);
                        } ?>
                    </a>

                    <div class="book-archive__content">
                        <h2 class="book-archive__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>

                        <div class="book-archive__excerpt">
                            <?php
                            $excerpt = get_the_excerpt();
                            if ( ! $excerpt ) { $excerpt = wp_strip_all_tags( get_the_content() ); }
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
    // Pagination (numeric)
    $links = paginate_links([
        'total'     => $GLOBALS['wp_query']->max_num_pages,
        'current'   => max( 1, get_query_var('paged') ),
        'type'      => 'array',
        'mid_size'  => 1,
        'end_size'  => 1,
        'prev_text' => '«',
        'next_text' => '»',
    ]);

    if ( $links ) : ?>
        <nav class="books-pagination" aria-label="Books pagination">
            <ul class="page-numbers">
                <?php foreach ( $links as $link ) : ?>
                    <li><?php echo $link; ?></li>
                <?php endforeach; ?>
            </ul>
        </nav>
    <?php endif; ?>

<?php else : ?>
    <?php
    // Nice centered "No Books Found" panel + back button
    $archive_url = get_post_type_archive_link( 'hh-book' );
    if ( ! $archive_url ) { $archive_url = home_url( '/books/' ); }
    ?>
    <section class="hh-panel no-books">
        <h2 class="no-books__title">No books found</h2>
        <p class="no-books__text">
            Try another Book Type </p>
        <p>Or <a class="ast-button" href="<?php echo esc_url( $archive_url ); ?>">Return to Books</a>
        </p>
    </section>
<?php endif; ?>

<?php get_footer();
