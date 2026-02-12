<?php

namespace HHBooksReviews;

/**
 * Random review shortcode.
 *
 * Pulls one random post from the Reviews CPT (`hhreviews`) and prints:
 * - the review post title (as an H4 inside the quote),
 * - the review text,
 * - a cite line with stars, reviewer name, location, and a link to the book.
 *
 * I’m keeping this lightweight on purpose.
 */
class RandomReviewShortcode extends Singleton
{
    /**
     * Singleton stash — just how we keep one instance around.
     * (Nothing fancy; same pattern as the rest of the plugin.)
     *
     * @var static
     */
    protected static $instance;

    /**
     * The shortcode tag you drop in the editor: [hh_random_review]
     */
    const SHORTCODE = 'hh_random_review';

    /**
     * On construct, register the shortcode with WordPress.
     * WP will call $this->render() whenever it sees [hh_random_review].
     */
    protected function __construct()
    {
        add_shortcode(self::SHORTCODE, [$this, 'render']);
    }

    /**
     * Build the HTML for one random Review and hand it back to WP.
     * Shortcodes must RETURN a string (not echo), so we assemble $html and return it.
     */
    public function render($atts = [])
    {
        // Ask WordPress for exactly one random, published Review.
        $q = new \WP_Query([
            'post_type'           => 'hhreviews', // our Reviews CPT
            'post_status'         => 'publish',
            'posts_per_page'      => 1,
            'orderby'             => 'rand',      // true random each call
            'no_found_rows'       => true,        // performance: no pagination math
            'ignore_sticky_posts' => true,
        ]);

        // If there are no reviews yet, quietly return nothing.
        if (!$q->have_posts()) {
            return '';
        }

        // Move the global post pointer so template tags work (get_the_ID, etc.).
        $q->the_post();
        $review_id = get_the_ID();

        // Pull the review post title (shown inside the quote as an H4).
        $title = get_the_title($review_id);

        // Grab the review content, strip HTML, and trim it so we have a clean paragraph.
        $content_raw = (string) get_post_field('post_content', $review_id);
        $content_txt = trim(wp_strip_all_tags($content_raw));

        // Reviewer name: use the meta if set; otherwise fall back to the post author display name.
        $name = get_post_meta($review_id, ReviewMeta::REVIEWER_NAME, true);
        if ($name === '' || $name === null) {
            $author_id = (int) get_post_field('post_author', $review_id);
            if ($author_id) {
                $name = get_the_author_meta('display_name', $author_id);
            }
        }

        // Reviewer location (totally optional).
        $location = get_post_meta($review_id, ReviewMeta::REVIEWER_LOCATION, true);

        // Rating (1–5). If valid, build a little star string like ★★★★☆ with an aria label.
        $rating = (int) get_post_meta($review_id, ReviewMeta::REVIEW_RATING, true);
        $stars_html = '';
        if ($rating >= 1 && $rating <= 5) {
            $stars_str  = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
            $aria_label = $rating . ' out of 5 stars';
            $stars_html = '<span class="hh-review-stars" aria-label="' . esc_attr($aria_label) . '">' . esc_html($stars_str) . '</span>';
        }

        // Linked Book: look up the post ID from meta; if it’s a published Book, grab its title+permalink.
        $book_id    = (int) get_post_meta($review_id, ReviewMeta::REVIEW_BOOK_ID, true);
        $book_link  = '';
        $book_title = '';
        if ($book_id) {
            $book = get_post($book_id);
            if ($book && $book->post_status === 'publish') {
                $book_title = get_the_title($book);
                $book_link  = get_permalink($book);
            }
        }

        // Build the byline bits in the order we want: Stars • Name • Location • Book link.
        $parts = [];
        if ($stars_html)       { $parts[] = $stars_html; }
        if (!empty($name))     { $parts[] = esc_html($name); }
        if (!empty($location)) { $parts[] = esc_html($location); }
        if (!empty($book_link)) {
            $label   = $book_title ? $book_title : 'View book';
            $parts[] = '<a href="' . esc_url($book_link) . '">' . esc_html($label) . '</a>';
        }
        $byline = implode(' • ', $parts);

        // Build the final HTML string. Keeping it simple and readable.
        $html  = '<blockquote class="wp-block-quote">';

        // Title sits inside the blockquote as an H4 (theme will style .wp-block-heading).
        if (!empty($title)) {
            $html .= '<h4 class="wp-block-heading">' . esc_html($title) . '</h4>';
        }

        // The review text itself.
        if ($content_txt !== '') {
            $html .= '<p>' . esc_html($content_txt) . '</p>';
        }

        // The “— Name • Location • Book” line (only if we have at least one part).
        if ($byline) {
            $html .= '<cite>— ' . $byline . '</cite>';
        }

        $html .= '</blockquote>';

        // Always clean up after a custom query so global $post is restored.
        wp_reset_postdata();

        // Shortcodes must return their HTML, not echo it.
        return $html;
    }
}
