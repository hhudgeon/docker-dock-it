<?php
/**
 * Plugin Name: Books and Reviews
 * Description: A plugin with a custom post type that displays books and reviews
 * Version: 0.1.0
 * Author: Heather Hudgeon
 * Text Domain: hh-books-reviews
 */

namespace HHBooksReviews;

/**
 * Plugin bootstrap. This file is the “on switch.”
 *
 * What it does:
 * - Loads all our classes (CPTs, meta, taxonomy, shortcode).
 * - Spins up each singleton so hooks get registered.
 * - Handles install/uninstall chores like flushing permalinks.
 *
 * @package HHBooksReviews
 * @since   0.1.0
 */

// -----------------------------------------------------------------------------
// Load classes we need
// -----------------------------------------------------------------------------

require_once plugin_dir_path(__FILE__) . 'classes/Singleton.php';
require_once plugin_dir_path(__FILE__) . 'classes/BookPostType.php';
require_once plugin_dir_path(__FILE__) . 'classes/BookMeta.php';
require_once plugin_dir_path(__FILE__) . 'classes/ReviewPostType.php';
require_once plugin_dir_path(__FILE__) . 'classes/ReviewMeta.php';
require_once plugin_dir_path(__FILE__) . 'classes/BookTypeTaxonomy.php';
require_once plugin_dir_path(__FILE__) . 'classes/RandomReviewShortcode.php';

// -----------------------------------------------------------------------------
// Boot everything (each class hooks itself on construct)
// -----------------------------------------------------------------------------

BookPostType::getInstance();
BookMeta::getInstance();
ReviewPostType::getInstance();
ReviewMeta::getInstance();
BookTypeTaxonomy::getInstance();
RandomReviewShortcode::getInstance();

/**
 * Fires one time when the plugin is activated.
 *
 * Game plan:
 * - Register CPTs/tax so WP knows their routes.
 * - Seed a few starter taxonomy terms.
 * - Flush permalinks so pretty URLs work right away.
 *
 * Heads up: flushing is “expensive,” so we only do it here.
 *
 * @since 0.1.0
 * @return void
 */
function activate_plugin()
{
    // Make sure types/tax are registered before flushing.
    BookPostType::getInstance()->registerPostType();
    ReviewPostType::getInstance()->registerPostType();
    BookTypeTaxonomy::getInstance()->registerBookType();
    BookTypeTaxonomy::seed_defaults();

    // Turn on the new routes immediately.
    flush_rewrite_rules();
}

/**
 * Fires when the plugin is deactivated.
 *
 * Cleanup:
 * - Flush permalinks so our custom routes don’t hang around.
 *
 * @since 0.1.0
 * @return void
 */
function deactivate_plugin() : void
{
    flush_rewrite_rules();
}

// -----------------------------------------------------------------------------
// Hook it up
// -----------------------------------------------------------------------------

/**
 * Register the lifecycle hooks for this plugin.
 *
 * @since 0.1.0
 */
register_activation_hook(__FILE__, 'HHBooksReviews\activate_plugin');
register_deactivation_hook(__FILE__, 'HHBooksReviews\deactivate_plugin');
