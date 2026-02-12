<?php

namespace HHBooksReviews;

/**
 * Registers the “Review” custom post type.
 *
 * In simple terms:
 * - Adds a Reviews section to the WordPress admin.
 * - Lets you create/edit Review posts with a title and content.
 * - Enables pretty URLs and REST support so it plays nice with the editor.
 */
class ReviewPostType extends Singleton
{
    /**
     * Singleton instance holder.
     * (Just means we only ever make one of these.)
     *
     * @var static
     */
    protected static $instance;

    /**
     * Hook into WordPress to register the post type.
     * Runs on `init` with a low priority so it fires early.
     */
    protected function __construct()
    {
        add_action('init', [$this, 'registerPostType'], 0);
    }

    /**
     * Register the Review custom post type.
     *
     * What this sets up:
     * - Labels (what you see in the admin UI).
     * - Supports title + editor.
     * - Public, has an archive at /reviews.
     * - REST enabled (works with the block editor).
     *
     * @return void
     */
    public function registerPostType(): void
    {
        $labels = array(
            'name'                  => _x('Reviews', 'Post Type General Name', 'hh-books-reviews'),
            'singular_name'         => _x('Review', 'Post Type Singular Name', 'hh-books-reviews'),
            'menu_name'             => __('Reviews', 'hh-books-reviews'),
            'name_admin_bar'        => __('Review', 'hh-books-reviews'),
            'archives'              => __('Review Archives', 'hh-books-reviews'),
            'attributes'            => __('Review Attributes', 'hh-books-reviews'),
            'parent_item_colon'     => __('Parent Review:', 'hh-books-reviews'),
            'all_items'             => __('All Reviews', 'hh-books-reviews'),
            'add_new_item'          => __('Add New Review', 'hh-books-reviews'),
            'add_new'               => __('Add New', 'hh-books-reviews'),
            'new_item'              => __('NewReview', 'hh-books-reviews'),
            'edit_item'             => __('Edit Review', 'hh-books-reviews'),
            'update_item'           => __('Update Review', 'hh-books-reviews'),
            'view_item'             => __('View Review', 'hh-books-reviews'),
            'view_items'            => __('View Reviews', 'hh-books-reviews'),
            'search_items'          => __('Search Review', 'hh-books-reviews'),
            'not_found'             => __('Not found', 'hh-books-reviews'),
            'not_found_in_trash'    => __('Not found in Trash', 'hh-books-reviews'),
            'featured_image'        => __('Featured Image', 'hh-books-reviews'),
            'set_featured_image'    => __('Set featured image', 'hh-books-reviews'),
            'remove_featured_image' => __('Remove featured image', 'hh-books-reviews'),
            'use_featured_image'    => __('Use as featured image', 'hh-books-reviews'),
            'insert_into_item'      => __('Insert into review', 'hh-books-reviews'),
            'uploaded_to_this_item' => __('Uploaded to this review', 'hh-books-reviews'),
            'items_list'            => __('Reviews list', 'hh-books-reviews'),
            'items_list_navigation' => __('Reviews list navigation', 'hh-books-reviews'),
            'filter_items_list'     => __('Filter reviews list', 'hh-books-reviews'),
        );

        $rewrite = array(
            'slug'       => 'reviews',
            'with_front' => true,
            'pages'      => true,
            'feeds'      => true,
        );

        $args = array(
            'label'               => __('Review', 'hh-books-reviews'),
            'description'         => __('Reviews of Bill Watterson\'s books', 'hh-books-reviews'),
            'labels'              => $labels,
            'supports'            => array('title', 'editor'), // title + content editor
            'hierarchical'        => false,                    // behaves like Posts (not Pages)
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-star-half',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => 'reviews',                // archive at /reviews
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'rewrite'             => $rewrite,
            'capability_type'     => 'page',
            'show_in_rest'        => true,                     // block editor / REST API
        );

        // Register the CPT with the slug 'hhreviews'.
        register_post_type('hhreviews', $args);
    }
}
