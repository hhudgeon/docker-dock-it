<?php
/**
 * Single template for Books (CPT: hh-book)
 * My layout:
 *   Panel 1 (white): two columns
 *     - LEFT  => Featured image, then Book meta details
 *     - RIGHT => Right-aligned title, then the main content (Synopsis)
 *   Panel 2 (white): Related Reviews for this book
 */

get_header();

while ( have_posts() ) :
    the_post();

    // --- Grab all my meta once (clean + simple) ---
    $book_id   = get_the_ID();
    $pub_date  = get_post_meta($book_id, 'hh_book_pub_date',   true);
    $isbn      = get_post_meta($book_id, 'hh_book_isbn',       true);
    $format    = get_post_meta($book_id, 'hh_book_format',     true);
    $publisher = get_post_meta($book_id, 'hh_book_publisher',  true);
    $start     = get_post_meta($book_id, 'hh_book_strip_start',true);
    $end       = get_post_meta($book_id, 'hh_book_strip_end',  true);
    $price     = get_post_meta($book_id, 'hh_book_price',      true);
    $notes     = get_post_meta($book_id, 'hh_book_strip_notes',true);

    // Build a quick array of labeled meta so I can loop without repeating markup
    $meta_rows = [];

    if ( !empty($publisher) ) { $meta_rows[] = ['label' => 'Publisher',       'value' => $publisher]; }
    if ( !empty($pub_date)  ) { $meta_rows[] = ['label' => 'Publication date','value' => $pub_date]; }
    if ( !empty($format)    ) { $meta_rows[] = ['label' => 'Format',          'value' => $format]; }
    if ( !empty($price)     ) { $meta_rows[] = ['label' => 'Price',           'value' => '$' . $price]; }

    // Strip range row (only show what's available)
    if ( $start || $end ) {
        $range = trim( $start . ( ($start && $end) ? ' — ' : '' ) . $end );
        $meta_rows[] = ['label' => 'Strip range', 'value' => $range];
    }

    if ( !empty($isbn)  ) { $meta_rows[] = ['label' => 'ISBN', 'value' => $isbn]; }
    if ( !empty($notes) ) { $meta_rows[] = ['label' => 'Strip notes', 'value' => $notes]; }
    ?>

    <!-- ===================== PANEL 1: BOOK ===================== -->
    <section class="hh-panel hh-panel--white book-hero">
        <div class="book-hero__grid">
            <!-- LEFT column: cover + details -->
            <aside class="book-hero__left">
                <?php
                // Featured image first; if none, I just don't render the element.
                if ( has_post_thumbnail() ) {
                    the_post_thumbnail( 'large', ['class' => 'book-cover'] );
                }
                ?>

                <?php if ( !empty($meta_rows) ) : ?>
                    <div class="book-meta">
                        <!-- My labeled meta list lives here -->
                        <ul class="book-meta__list">
                            <?php foreach ( $meta_rows as $row ) : ?>
                                <li class="book-meta__item">
                                    <span class="book-meta__label"><?php echo esc_html($row['label']); ?>:</span>
                                    <span class="book-meta__value">
                                        <?php echo esc_html($row['value']); ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </aside>

            <!-- RIGHT column: title (right-aligned) + content -->
            <main class="book-hero__right">
                <h1 class="book-title align-right"><?php the_title(); ?></h1>
                <?php
                // Build a safe link back to the Books archive
                $archive_url = get_post_type_archive_link('hh-book');
                if ( ! $archive_url ) {
                    // Fallback in case permalinks aren’t ready for some reason
                    $archive_url = home_url('/books/');
                }
                ?>
                <p class="book-backlink-wrap">
                    <a class="ast-button book-backlink" href="<?php echo esc_url($archive_url); ?>">
                        ← Return to Books
                    </a>
                </p>


                <div class="book-content">
                    <?php
                    // This prints my "Synopsis" (main content) with theme filters.
                    the_content();
                    ?>
                </div>
                <!-- Reviews live under the synopsis, same panel -->
                <section class="book-reviews-inline">
                    <h2 class="reviews-title">Reviews</h2>

                    <?php
                    $reviews_q = new WP_Query([
                        'post_type'      => 'hhreviews',
                        'posts_per_page' => 10,
                        'meta_query'     => [
                            [
                                'key'     => 'hh_review_book_id',
                                'value'   => $book_id,
                                'compare' => '=',
                            ],
                        ],
                    ]);

                    if ( $reviews_q->have_posts() ) : ?>
                        <div class="reviews-list">
                            <?php while ( $reviews_q->have_posts() ) : $reviews_q->the_post();
                                $rid    = get_the_ID();
                                $r_name = get_post_meta($rid, 'hh_review_name', true);
                                $r_loc  = get_post_meta($rid, 'hh_review_location', true);
                                $r_rate = (int) get_post_meta($rid, 'hh_review_rating', true);
                                $stars  = ($r_rate >=1 && $r_rate <=5) ? str_repeat('★',$r_rate) . str_repeat('☆', 5-$r_rate) : '';
                                ?>
                                <article class="review-item">
                                    <header class="review-item__header">
                                        <h3 class="review-item__title"><?php the_title(); ?></h3>
                                        <div class="review-item__meta">
                                            <?php if ($r_name): ?><span class="review-item__author"><?php echo esc_html($r_name); ?></span><?php endif; ?>
                                            <?php if ($r_loc): ?><span class="review-item__location"> · <?php echo esc_html($r_loc); ?></span><?php endif; ?>
                                            <?php if ($stars): ?><span class="review-item__rating"> · <?php echo esc_html($stars); ?></span><?php endif; ?>
                                        </div>
                                    </header>
                                    <div class="review-item__content">
                                        <?php the_content(); ?>
                                    </div>
                                </article>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </div>
                    <?php else : ?>
                        <p class="reviews-empty">No reviews yet.</p>
                    <?php endif; ?>
                </section>

            </main>
        </div>
    </section>



<?php
endwhile;

get_footer();
