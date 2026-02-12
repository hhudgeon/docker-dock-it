<?php

namespace HHBooksReviews;

/**
 * Registers the “Book” custom post type and wires it up on init.
 *
 * What this does:
 * - Creates the `hh-book` CPT with titles, editor, featured image, and ordering.
 * - Makes it hierarchical (so you can nest if you ever want).
 * - Enables REST support so blocks/editor work cleanly.
 *
 * @package HHBooksReviews
 * @since   0.1.0
 */
class BookPostType extends Singleton
{
    /**
     * Post type slug for Books (used everywhere in WP).
     *
     * @var string
     */
    const POST_TYPE = 'hh-book';

    /**
     * Singleton instance holder (so we only register once).
     *
     * @var static
     */
    protected static $instance;

    /**
     * Hook into WordPress lifecycle.
     *
     * Registers our CPT on `init` (priority 0 so it’s early).
     *
     * @since 0.1.0
     */
    protected function __construct()
    {
        // add hooks here
        add_action('init', [$this, 'registerPostType'], 0);
    }

    /**
     * Register the Book custom post type.
     *
     * Labels: friendly admin UI strings.
     * Args: hierarchical, public, has archive at /books, REST enabled, etc.
     *
     * @since  0.1.0
     * @return void
     */
    public function registerPostType()
    {
        $labels = array(
            'name'                  => _x('Books', 'Post Type General Name', 'hh-books-reviews'),
            'singular_name'         => _x('Book', 'Post Type Singular Name', 'hh-books-reviews'),
            'menu_name'             => __('Books', 'hh-books-reviews'),
            'name_admin_bar'        => __('Book', 'hh-books-reviews'),
            'archives'              => __('Book Archives', 'hh-books-reviews'),
            'attributes'            => __('Book Attributes', 'hh-books-reviews'),
            'parent_item_colon'     => __('Parent Book:', 'hh-books-reviews'),
            'all_items'             => __('All Books', 'hh-books-reviews'),
            'add_new_item'          => __('Add New Book', 'hh-books-reviews'),
            'add_new'               => __('Add New', 'hh-books-reviews'),
            'new_item'              => __('New Book', 'hh-books-reviews'),
            'edit_item'             => __('Edit Book', 'hh-books-reviews'),
            'update_item'           => __('Update Book', 'hh-books-reviews'),
            'view_item'             => __('View Book', 'hh-books-reviews'),
            'view_items'            => __('View Books', 'hh-books-reviews'),
            'search_items'          => __('Search Book', 'hh-books-reviews'),
            'not_found'             => __('Not found', 'hh-books-reviews'),
            'not_found_in_trash'    => __('Not found in Trash', 'hh-books-reviews'),
            'featured_image'        => __('Featured Image', 'hh-books-reviews'),
            'set_featured_image'    => __('Set featured image', 'hh-books-reviews'),
            'remove_featured_image' => __('Remove featured image', 'hh-books-reviews'),
            'use_featured_image'    => __('Use as featured image', 'hh-books-reviews'),
            'insert_into_item'      => __('Insert into book', 'hh-books-reviews'),
            'uploaded_to_this_item' => __('Uploaded to this book', 'hh-books-reviews'),
            'items_list'            => __('Books list', 'hh-books-reviews'),
            'items_list_navigation' => __('Books list navigation', 'hh-books-reviews'),
            'filter_items_list'     => __('Filter books list', 'hh-books-reviews'),
        );

        $rewrite = array(
            'slug'       => 'books',
            'with_front' => true,
            'pages'      => true,
            'feeds'      => true,
        );

        $args = array(
            'label'               => __('Book', 'hh-books-reviews'),
            'description'         => __('Books by Bill Watterson', 'hh-books-reviews'),
            'labels'              => $labels,
            'supports'            => array('title', 'editor', 'thumbnail', 'page-attributes'),
            'hierarchical'        => true,  // can have parents/children if you need
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-book-alt',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => 'books',
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'rewrite'             => array('slug' => 'books', 'with_front' => false),
            'capability_type'     => 'page',
            'show_in_rest'        => true,  // block editor / REST API support
        );

        register_post_type(self::POST_TYPE, $args);
    } // end of registerPostType
} // end of class
