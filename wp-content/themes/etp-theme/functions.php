<?php

/**
 * ETP Bricks child theme bootstrap.
 */

define('ETP_THEME_PATH', get_stylesheet_directory());
define('ETP_THEME_INC', ETP_THEME_PATH . '/inc');

// Enqueue child theme stylesheet compiled by Gulp.
add_action('wp_enqueue_scripts', function () {
  $theme = wp_get_theme();
  wp_enqueue_style(
    'etp-theme',
    get_stylesheet_directory_uri() . '/style.css',
    [],
    $theme->get('Version')
  );

  wp_enqueue_script(
    'etp-theme',
    get_stylesheet_directory_uri() . '/assets/js/main.js',
    [],
    $theme->get('Version'),
    true
  );
});

// Theme supports and basics.
add_action('after_setup_theme', function () {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  
  // Load modular theme functionality.
  $etp_includes = [
    'helpers.php',
    'content-parser.php',
    'toc-helper.php',
    'disable-comments.php',
    'bricks-setup.php',
  ];

  foreach ($etp_includes as $file) {
    $path = trailingslashit(ETP_THEME_INC) . $file;
    if (file_exists($path)) {
      require_once $path;
    }
  }
});
