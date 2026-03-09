<?php
/**
 * Render file for the dynamic Books List block.
 *
 * This file runs on the frontend and pulls all books from the hh-book
 * custom post type and displays them in the block.
 */

// Set up the query arguments
$args = array(
	'post_type'      => 'hh-book', // Use the Books custom post type from the plugin
	'posts_per_page' => -1         // -1 tells WordPress to return ALL books
);

// Run the query
$books = new WP_Query($args);

// Start the block wrapper (WordPress adds block classes automatically)
echo '<div ' . get_block_wrapper_attributes() . '>';

// Check if any books exist
if ($books->have_posts()) {

	// Loop through each book
	while ($books->have_posts()) {
		$books->the_post();

		echo '<div class="book-item">';

		// Book title that links to the single book page
		echo '<h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';

		// Short description (excerpt) of the book
		echo '<p>' . get_the_excerpt() . '</p>';

		echo '</div>';
	}

	// Reset the global post data so other queries on the page don't break *cross fingers*
	wp_reset_postdata();

} else {

	// Message if no books are found
	echo '<p>No books found.</p>';
}

// Close the block wrapper
echo '</div>';
