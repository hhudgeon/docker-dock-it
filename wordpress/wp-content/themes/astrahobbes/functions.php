<?php
/**
 * Astrahobbes Child Theme functions
 */

/**
 * Enqueue child styles + Google Fonts (after Astra)
 */
add_action('wp_enqueue_scripts', function () {
    // Cache-bust whenever style.css changes
    $ver = filemtime( get_stylesheet_directory() . '/style.css' );

    // Child stylesheet (loads after Astra core CSS)
    wp_enqueue_style(
        'astrahobbes',
        get_stylesheet_uri(),
        array('astra-theme-css'),
        $ver
    );

    // Google Fonts for Barriecito + Comic Neue
    wp_enqueue_style(
        'child-google-fonts',
        'https://fonts.googleapis.com/css2?family=Barriecito&family=Comic+Neue:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap',
        array(),
        null
    );

    wp_style_add_data('astrahobbes', 'rtl', 'replace');
}, 99);

// Books archive: show 5 per page (enables pagination)
add_action('pre_get_posts', function ($q) {
    if ( is_admin() || ! $q->is_main_query() ) {
        return;
    }
    if ( $q->is_post_type_archive('hh-book') ) {
        $q->set('posts_per_page', 5);
        // do NOT set 'paged' here; WP handles it
        // do NOT set 'no_found_rows' either
    }
});

/**
 * (Optional) Force taxonomy template if you want; otherwise WP will find
 * taxonomy-hh_book_type.php automatically via template hierarchy.
 */
add_filter('template_include', function ($template) {
    if ( is_tax('hh_book_type') ) {
        $child = get_stylesheet_directory() . '/taxonomy-hh_book_type.php';
        if ( file_exists($child) ) {
            return $child;
        }
    }
    return $template;
});

/**
 * Fallback for old-style pagination URLs like /books/page-2/
 * (WP’s native pattern is /books/page/2/)
 */
add_action('init', function () {
    add_rewrite_rule(
        '^books/page-([0-9]+)/?$',
        'index.php?post_type=hh-book&paged=$matches[1]',
        'top'
    );
});
