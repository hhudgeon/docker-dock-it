<?php

namespace HHBooksReviews;

/**
 * Handles extra fields for Review posts.
 *
 * In simple terms:
 * - Adds a small “Review Details” box on the Review editor screen.
 * - Saves those fields safely when you update/publish.
 * - On single Review pages, shows a tiny box with Name / Location / Rating.
 */
class ReviewMeta extends Singleton
{
    // === Meta keys (the names we use to save/read values) ===

    /** Reviewer’s name (text). */
    const REVIEWER_NAME = 'hh_review_name';

    /** Reviewer’s location (text, like “City, State”). */
    const REVIEWER_LOCATION = 'hh_review_location';

    /** Star rating number (1–5). */
    const REVIEW_RATING = 'hh_review_rating';

    /** The related Book post ID (so a Review can link to a Book). */
    const REVIEW_BOOK_ID = 'hh_review_book_id'; // links Review -> Book

    /**
     * Singleton instance holder.
     * (Just means we only ever make one of these.)
     */
    protected static $instance;

    /**
     * Set up the hooks that make this work for the Reviews post type.
     * - Add the meta box in the editor.
     * - Save fields on post save.
     * - Append a small info block on the front end.
     */
    protected function __construct()
    {
        // Add the “Review Details” meta box on the Review editor screen.
        add_action('add_meta_boxes_hhreviews', [$this, 'registerMetaBox']);

        // Save ONLY when a Review (slug = hhreviews) is saved.
        add_action('save_post_hhreviews', [$this, 'saveMeta'], 10, 2);

        // Front end: add Name/Location/Rating under the Review content.
        add_filter('the_content', [$this, 'appendFrontEndBlock']);
    }

    /**
     * Add the “Review Details” meta box in the right sidebar of the editor.
     */
    public function registerMetaBox() : void
    {
        add_meta_box(
            'hh-review-meta',
            'Review Details',
            [$this, 'metaForm'],
            'hhreviews', // our Reviews post type
            'side',
            'core'
        );
    }

    /**
     * Show the fields in the editor.
     * We load a small template so this class stays clean.
     * Template: /templates/review-meta-form.php
     */
    public function metaForm() : void
    {
        // __FILE__ is in /classes; step up to /templates
        include plugin_dir_path(__FILE__) . '../templates/review-meta-form.php';
    }

    /**
     * Save the posted fields safely.
     *
     * What we check first:
     * - Nonce is valid (security).
     * - Not an autosave.
     * - Current user can edit this post.
     *
     * What we save:
     * - Name, Location (text)
     * - Rating (only keep 1–5; anything else is cleared)
     * - Book ID (integer; lets us link to a Book)
     *
     * @param int      $post_id Review post ID.
     * @param \WP_Post $post    Review post object.
     */
    public function saveMeta(int $post_id, \WP_Post $post) : void
    {
        // Nonce check — bail if it’s missing or invalid.
        if (empty($_POST['hh_review_meta_nonce']) ||
            ! wp_verify_nonce($_POST['hh_review_meta_nonce'], 'hh_review_meta_save')) {
            return;
        }

        // Autosave / permissions
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return; }
        if (! current_user_can('edit_post', $post_id)) { return; }

        // NAME — plain text
        if (isset($_POST[self::REVIEWER_NAME])) {
            $name = sanitize_text_field($_POST[self::REVIEWER_NAME]);
            update_post_meta($post_id, self::REVIEWER_NAME, $name);
        }

        // LOCATION — plain text
        if (isset($_POST[self::REVIEWER_LOCATION])) {
            $loc = sanitize_text_field($_POST[self::REVIEWER_LOCATION]);
            update_post_meta($post_id, self::REVIEWER_LOCATION, $loc);
        }

        // RATING — keep 1–5 only; anything else becomes empty
        if (isset($_POST[self::REVIEW_RATING])) {
            $rating = (int) $_POST[self::REVIEW_RATING];
            if ($rating < 1 || $rating > 5) { $rating = ''; }
            update_post_meta($post_id, self::REVIEW_RATING, $rating);
        }

        // BOOK ID — link this Review to a Book post (optional)
        if (isset($_POST[self::REVIEW_BOOK_ID])) {
            $book_id = absint($_POST[self::REVIEW_BOOK_ID]);
            update_post_meta($post_id, self::REVIEW_BOOK_ID, $book_id);
        }
    }

    /**
     * On single Review pages, add a small box under the content
     * showing the reviewer, location, and star rating (if we have them).
     *
     * If all three are empty, we don’t add anything.
     *
     * @param  string $content Original post content.
     * @return string          Original content + our box (or unchanged).
     */
    public function appendFrontEndBlock($content)
    {
        // Only run on single Review pages, main query, in the Loop.
        if (!is_singular('hhreviews') || !in_the_loop() || !is_main_query()) {
            return $content;
        }

        $post_id = get_the_ID();

        $name   = get_post_meta($post_id, self::REVIEWER_NAME, true);
        $loc    = get_post_meta($post_id, self::REVIEWER_LOCATION, true);
        $rating = (int) get_post_meta($post_id, self::REVIEW_RATING, true);

        // If nothing is set, leave the content alone.
        if ($name === '' && $loc === '' && empty($rating)) {
            return $content;
        }

        // Turn 1–5 into stars like ★★★★☆
        $stars = '';
        if ($rating >= 1 && $rating <= 5) {
            $stars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
        }

        // Simple inline block for now (you can move styles to CSS later).
        $html  = '<div class="hh-review-meta-block" style="border:1px solid #eee;padding:12px;border-radius:6px;margin-top:24px;">';
        if ($name !== '') { $html .= '<div><strong>Reviewer:</strong> ' . esc_html($name) . '</div>'; }
        if ($loc !== '')  { $html .= '<div><strong>Location:</strong> ' . esc_html($loc) . '</div>'; }
        if ($stars)       { $html .= '<div><strong>Rating:</strong> ' . esc_html($stars) . ' (' . (int)$rating . '/5)</div>'; }
        $html .= '</div>';

        return $content . $html;
    }

    // === Optional getters (handy in templates) ===

    /** Get the reviewer’s name. Returns '' if not set. */
    public function getName(\WP_Post $post = null) {
        $post = $post ?: get_post();
        return get_post_meta($post->ID, self::REVIEWER_NAME, true);
    }

    /** Get the reviewer’s location. Returns '' if not set. */
    public function getLocation(\WP_Post $post = null) {
        $post = $post ?: get_post();
        return get_post_meta($post->ID, self::REVIEWER_LOCATION, true);
    }

    /** Get the rating number (0 if missing or invalid). */
    public function getRating(\WP_Post $post = null) {
        $post = $post ?: get_post();
        return (int) get_post_meta($post->ID, self::REVIEW_RATING, true);
    }

    /** Get the linked Book post ID (0 if missing). */
    public function getBookId(\WP_Post $post = null) {
        $post = $post ?: get_post();
        return (int) get_post_meta($post->ID, self::REVIEW_BOOK_ID, true);
    }
}
