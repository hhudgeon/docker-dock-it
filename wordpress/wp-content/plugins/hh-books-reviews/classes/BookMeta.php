<?php

namespace HHBooksReviews;

/**
 * Book meta fields helper.
 *
 * What is does:
 * - Adds a small “Book Details” box on the Book editor screen.
 * - Saves those fields safely when you update/publish.
 * - Gives you easy helper functions (getters) to read the saved values.
 */
class BookMeta extends Singleton
{
    // === Meta keys (these are the names we use to save/read values) ===

    /** Publication date, like 2024-05-01. */
    const PUB_DATE     = 'hh_book_pub_date';

    /** ISBN as plain text (numbers, X, spaces, hyphens are fine). */
    const ISBN         = 'hh_book_isbn';

    /** Any notes about the strips included. */
    const STRIP_NOTES  = 'hh_book_strip_notes';

    /** Publisher name. */
    const PUBLISHER    = 'hh_book_publisher';

    /** Start date for the strip range (YYYY-MM-DD). */
    const STRIP_START  = 'hh_book_strip_start';

    /** End date for the strip range (YYYY-MM-DD). */
    const STRIP_END    = 'hh_book_strip_end';

    /** Price as a string like "12.99". */
    const PRICE        = 'hh_book_price';

    /** Format label, e.g. "B/W", "Color Sundays", "Mixed". */
    const FORMAT       = 'hh_book_format';

    // Nonce keys used when saving (basic security check)
    /** Hidden input name in the form (nonce). */
    const NONCE        = 'hh_book_meta_nonce';

    /** Nonce action value we verify against. */
    const NONCE_ACTION = 'hh_book_meta_save';

    /**
     * Singleton instance holder.
     * (Just means we only ever make one of these.)
     */
    protected static $instance;

    /**
     * Set up WordPress hooks for the Book post type only.
     * - Adds the meta box on the Book editor screen.
     * - Saves fields when a Book is saved.
     */
    protected function __construct()
    {
        // Add the “Book Details” meta box on Book edit screens.
        add_action('add_meta_boxes_' . BookPostType::POST_TYPE, [$this, 'registerMetaBox']);

        // Save fields when a Book gets saved.
        add_action('save_post_' . BookPostType::POST_TYPE, [$this, 'saveMeta'], 10, 2);
    }

    /**
     * Add the "Book Details" meta box in the right sidebar of the editor.
     */
    public function registerMetaBox(): void
    {
        add_meta_box(
            'hh-book-meta',
            'Book Details',
            [$this, 'metaForm'],
            BookPostType::POST_TYPE,
            'side',
            'core'
        );
    }

    /**
     * Show the actual fields in the editor.
     * We keep the HTML in a small template file so this class stays clean.
     * Template path: /templates/book-meta-form.php
     */
    public function metaForm(): void
    {
        $template = plugin_dir_path(__FILE__) . '../templates/book-meta-form.php';
        if (file_exists($template)) {
            include $template;
        } else {
            echo '<p style="color:#cc0000;">Template not found: templates/book-meta-form.php</p>';
        }
    }

    /**
     * Save the fields safely when a Book is saved.
     *
     * What we check first:
     * - Nonce is valid (security).
     * - Not an autosave or revision.
     * - Current user can edit this post.
     *
     * What we save:
     * - Pub date, ISBN, notes, publisher, strip start/end, price, format.
     * - Dates are validated (YYYY-MM-DD). If end < start, we keep the old values.
     */
    public function saveMeta(int $post_id, \WP_Post $post): void
    {
        // Nonce required
        if (empty($_POST[self::NONCE]) || !wp_verify_nonce($_POST[self::NONCE], self::NONCE_ACTION)) {
            return;
        }

        // Autosave/revisions/capability checks
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return; }
        if (wp_is_post_revision($post_id)) { return; }
        if (!current_user_can('edit_post', $post_id)) { return; }

        // --- Save each field if present in $_POST ---

        // Publication Date (YYYY-MM-DD)
        if (isset($_POST[self::PUB_DATE])) {
            $val = $this->sanitizeDate($_POST[self::PUB_DATE]);
            update_post_meta($post_id, self::PUB_DATE, $val);
        }

        // ISBN (stored as plain text)
        if (isset($_POST[self::ISBN])) {
            $val = sanitize_text_field($_POST[self::ISBN]);
            update_post_meta($post_id, self::ISBN, $val);
        }

        // Strip Notes (textarea)
        if (isset($_POST[self::STRIP_NOTES])) {
            $val = sanitize_textarea_field($_POST[self::STRIP_NOTES]);
            update_post_meta($post_id, self::STRIP_NOTES, $val);
        }

        // Publisher (text)
        if (isset($_POST[self::PUBLISHER])) {
            $val = sanitize_text_field($_POST[self::PUBLISHER]);
            update_post_meta($post_id, self::PUBLISHER, $val);
        }

        // Strip Range Start (date)
        $start = null;
        if (isset($_POST[self::STRIP_START])) {
            $start = $this->sanitizeDate($_POST[self::STRIP_START]);
            update_post_meta($post_id, self::STRIP_START, $start);
        }

        // Strip Range End (date)
        $end = null;
        if (isset($_POST[self::STRIP_END])) {
            $end = $this->sanitizeDate($_POST[self::STRIP_END]);
            update_post_meta($post_id, self::STRIP_END, $end);
        }

        // If both dates exist and end < start, keep prior values (don’t overwrite with bad data)
        if ($start && $end && strcmp($end, $start) < 0) {
            $prev_start = get_post_meta($post_id, self::STRIP_START, true);
            $prev_end   = get_post_meta($post_id, self::STRIP_END, true);
            update_post_meta($post_id, self::STRIP_START, $prev_start);
            update_post_meta($post_id, self::STRIP_END, $prev_end);
        }

        // Price (normalize to "0.00")
        if (isset($_POST[self::PRICE])) {
            $val = $this->normalizePrice($_POST[self::PRICE]);
            update_post_meta($post_id, self::PRICE, $val);
        }

        // Format (text; e.g., B/W, Color Sundays, Mixed)
        if (isset($_POST[self::FORMAT])) {
            $val = sanitize_text_field($_POST[self::FORMAT]);
            update_post_meta($post_id, self::FORMAT, $val);
        }
    }

    // === Getters for templates (use: BookMeta::getInstance()->getXxx()) ===

    /** Get publication date (YYYY-MM-DD). Returns '' if not set. */
    public function getPubDate(\WP_Post $post = null): string
    {
        $post = $post ?: get_post();
        return (string) get_post_meta($post->ID, self::PUB_DATE, true);
    }

    /** Get ISBN (plain text). Returns '' if not set. */
    public function getIsbn(\WP_Post $post = null): string
    {
        $post = $post ?: get_post();
        return (string) get_post_meta($post->ID, self::ISBN, true);
    }

    /** Get notes about strips. Returns '' if not set. */
    public function getStripNotes(\WP_Post $post = null): string
    {
        $post = $post ?: get_post();
        return (string) get_post_meta($post->ID, self::STRIP_NOTES, true);
    }

    /** Get publisher name. Returns '' if not set. */
    public function getPublisher(\WP_Post $post = null): string
    {
        $post = $post ?: get_post();
        return (string) get_post_meta($post->ID, self::PUBLISHER, true);
    }

    /** Get strip range start date (YYYY-MM-DD). Returns '' if not set. */
    public function getStripStart(\WP_Post $post = null): string
    {
        $post = $post ?: get_post();
        return (string) get_post_meta($post->ID, self::STRIP_START, true);
    }

    /** Get strip range end date (YYYY-MM-DD). Returns '' if not set. */
    public function getStripEnd(\WP_Post $post = null): string
    {
        $post = $post ?: get_post();
        return (string) get_post_meta($post->ID, self::STRIP_END, true);
    }

    /** Get price as a string like "12.99". Returns '' if not set. */
    public function getPrice(\WP_Post $post = null): string
    {
        $post = $post ?: get_post();
        return (string) get_post_meta($post->ID, self::PRICE, true);
    }

    /** Get format label (e.g., "B/W", "Color Sundays"). Returns '' if not set. */
    public function getFormat(\WP_Post $post = null): string
    {
        $post = $post ?: get_post();
        return (string) get_post_meta($post->ID, self::FORMAT, true);
    }

    // === Internal helpers for cleaning up inputs ===

    /**
     * Check that a date looks like YYYY-MM-DD and is a real calendar date.
     * Returns the cleaned date or '' if it’s not valid.
     */
    private function sanitizeDate($raw): string
    {
        $val = trim((string) $raw);
        // Accept only YYYY-MM-DD, and ensure it’s a real date
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) {
            return '';
        }
        [$y, $m, $d] = array_map('intval', explode('-', $val));
        if (!checkdate($m, $d, $y)) {
            return '';
        }
        return $val;
    }

    /**
     * Turn something “price-like” into a clean "0.00" string.
     * Strips weird characters; negatives become 0.00.
     */
    private function normalizePrice($raw): string
    {
        $num = floatval(preg_replace('/[^0-9.\-]/', '', (string) $raw));
        if ($num < 0) { $num = 0.0; }
        // Format as string with two decimals
        return number_format($num, 2, '.', '');
    }
}
