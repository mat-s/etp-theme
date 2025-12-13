<?php
/**
 * ETP Bricks child theme setup.
 */

// Enqueue child theme stylesheet compiled by Gulp.
add_action('wp_enqueue_scripts', function () {
    $theme = wp_get_theme();
    wp_enqueue_style(
        'etp-theme',
        get_stylesheet_directory_uri() . '/style.css',
        [],
        $theme->get('Version')
    );
});

// Ensure Bricks knows about the child theme directory if needed.
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
});
