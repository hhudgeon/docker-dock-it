<?php
/**
 * Render file for the dynamic Books List block.
 *
 * This file runs on the frontend and pulls all books from the hh-book
 * custom post type and displays them in a compact card layout.
 */

// Set up the query arguments
$args = array(
	'post_type'      => 'hh-book',
	'posts_per_page' => -1,
);

// Run the query
$books = new WP_Query( $args );

// Start the block wrapper
echo '<div ' . get_block_wrapper_attributes() . '>';
echo '<div class="hh-books-block__list">';

// Check if any books exist
if ( $books->have_posts() ) {

	// Loop through each book
	while ( $books->have_posts() ) {
		$books->the_post();

		$publisher   = get_post_meta( get_the_ID(), 'hh_book_publisher', true );
		$pub_date    = get_post_meta( get_the_ID(), 'hh_book_pub_date', true );
		$format      = get_post_meta( get_the_ID(), 'hh_book_format', true );
		$price       = get_post_meta( get_the_ID(), 'hh_book_price', true );
		$isbn        = get_post_meta( get_the_ID(), 'hh_book_isbn', true );
		$strip_notes = get_post_meta( get_the_ID(), 'hh_book_strip_notes', true );

		echo '<article class="hh-books-block__card hh-panel">';
		echo '<div class="hh-books-block__grid">';

		// Left column: featured image
		echo '<div class="hh-books-block__media">';

		if ( has_post_thumbnail() ) {
			echo '<a class="hh-books-block__coverlink" href="' . esc_url( get_permalink() ) . '">';
			echo get_the_post_thumbnail(
				get_the_ID(),
				'medium',
				array(
					'class' => 'hh-books-block__cover book-cover',
					'alt'   => esc_attr( get_the_title() ),
				)
			);
			echo '</a>';
		}

		echo '</div>';

		// Right column: title, excerpt, meta
		echo '<div class="hh-books-block__content">';

		echo '<h3 class="hh-books-block__title">';
		echo '<a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a>';
		echo '</h3>';

		echo '<div class="hh-books-block__excerpt">';
		echo '<p>' . esc_html( get_the_excerpt() ) . '</p>';
		echo '</div>';

		echo '<div class="hh-books-block__meta book-meta">';
		echo '<ul class="book-meta__list">';

		if ( ! empty( $publisher ) ) {
			echo '<li class="book-meta__item"><span class="book-meta__label">Publisher:</span> <span class="book-meta__value">' . esc_html( $publisher ) . '</span></li>';
		}

		if ( ! empty( $pub_date ) ) {
			echo '<li class="book-meta__item"><span class="book-meta__label">Publication date:</span> <span class="book-meta__value">' . esc_html( $pub_date ) . '</span></li>';
		}

		if ( ! empty( $format ) ) {
			echo '<li class="book-meta__item"><span class="book-meta__label">Format:</span> <span class="book-meta__value">' . esc_html( $format ) . '</span></li>';
		}

		if ( ! empty( $price ) ) {
			echo '<li class="book-meta__item"><span class="book-meta__label">Price:</span> <span class="book-meta__value">$' . esc_html( $price ) . '</span></li>';
		}

		if ( ! empty( $isbn ) ) {
			echo '<li class="book-meta__item"><span class="book-meta__label">ISBN:</span> <span class="book-meta__value">' . esc_html( $isbn ) . '</span></li>';
		}

		if ( ! empty( $strip_notes ) ) {
			echo '<li class="book-meta__item"><span class="book-meta__label">Strip notes:</span> <span class="book-meta__value">' . esc_html( $strip_notes ) . '</span></li>';
		}

		echo '</ul>';
		echo '</div>'; // .hh-books-block__meta

		echo '</div>'; // .hh-books-block__content
		echo '</div>'; // .hh-books-block__grid
		echo '</article>';
	}

	// Reset the global post data
	wp_reset_postdata();

} else {
	echo '<p>No books found.</p>';
}

echo '</div>'; // .hh-books-block__list
echo '</div>'; // block wrapper
