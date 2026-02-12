<?php
namespace HHBooksReviews;

/**
 * Registers and manages the “Book Type” taxonomy for Books.
 *
 * What this does:
 * - Adds a hierarchical taxonomy (like categories) called `hh_book_type`.
 * - Hooks it in after the Book CPT so everything is registered in the right order.
 * - Optionally seeds a few starter terms so you’re not starting from zero.
 *
 * @package HHBooksReviews
 * @since   0.1.0
 */
class BookTypeTaxonomy extends Singleton
{
    /**
     * The taxonomy slug we’ll use everywhere in WP.
     *
     * Example usage: get_terms( BookTypeTaxonomy::TAXONOMY ).
     *
     * @var string
     */
    const TAXONOMY = 'hh_book_type';

    /**
     * Singleton instance holder (so we only register once).
     *
     * @var static
     */
    protected static $instance;

    /**
     * Wire things up.
     *
     * Note: use priority 1 on `init` to ensure the Book CPT is already registered
     * before the taxonomy attaches itself to it.
     *
     * @since 0.1.0
     */
    protected function __construct()
    {
        // register after CPT
        add_action('init', [$this, 'registerBookType'], 1);
    }

    /**
     * Register the “Book Type” taxonomy and attach it to the Book CPT.
     *
     * Details:
     * - Hierarchical (so you can nest types).
     * - Visible in admin, nav menus, REST (block editor).
     * - Pretty permalinks at /book-type/{term}.
     *
     * @since  0.1.0
     * @return void
     */
    public function registerBookType(): void
    {
        $labels = array(
            'name'                       => _x('Book Types', 'taxonomy general name', 'hh-books-reviews'),
            'singular_name'              => _x('Book Type', 'taxonomy singular name', 'hh-books-reviews'),
            'menu_name'                  => __('Book Types', 'hh-books-reviews'),
            'all_items'                  => __('All Book Types', 'hh-books-reviews'),
            'parent_item'                => __('Parent Book Type', 'hh-books-reviews'),
            'parent_item_colon'          => __('Parent Book Type:', 'hh-books-reviews'),
            'new_item_name'              => __('New Book Type Name', 'hh-books-reviews'),
            'add_new_item'               => __('Add New Book Type', 'hh-books-reviews'),
            'edit_item'                  => __('Edit Book Type', 'hh-books-reviews'),
            'update_item'                => __('Update Book Type', 'hh-books-reviews'),
            'view_item'                  => __('View Book Type', 'hh-books-reviews'),
            'separate_items_with_commas' => __('Separate items with commas', 'hh-books-reviews'),
            'add_or_remove_items'        => __('Add or remove book types', 'hh-books-reviews'),
            'choose_from_most_used'      => __('Choose from the most used', 'hh-books-reviews'),
            'popular_items'              => __('Popular Book Types', 'hh-books-reviews'),
            'search_items'               => __('Search Book Types', 'hh-books-reviews'),
            'not_found'                  => __('Not Found', 'hh-books-reviews'),
            'no_terms'                   => __('No book types', 'hh-books-reviews'),
            'items_list'                 => __('Book Types list', 'hh-books-reviews'),
            'items_list_navigation'      => __('Book Types list navigation', 'hh-books-reviews'),
        );

        $args = array(
            'labels'            => $labels,
            'hierarchical'      => true,                   // like categories
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud'     => false,
            'show_in_rest'      => true,                   // Gutenberg / REST
            'rewrite'           => ['slug' => 'book-type', 'with_front' => true],
            'query_var'         => true,
        );

        register_taxonomy(self::TAXONOMY, [BookPostType::POST_TYPE], $args);
    }

    /**
     * Seed a few starter terms so there is something to pick from.
     *
     * Safe to run more than once — it only adds what’s missing (no duplicates).
     *
     * @since  0.1.0
     * @return void
     */
    public static function seed_defaults() : void
    {
        $defaults = ['Treasury', 'Box Set', 'Exhibition Catalogue', 'Textbook'];

        foreach ($defaults as $name) {
            if (! term_exists($name, self::TAXONOMY)) {
                wp_insert_term($name, self::TAXONOMY, ['slug' => sanitize_title($name)]);
            }
        }
    }
}
