<?php

/**
 * Bricks setup: categories and element loading.
 */

if (!defined('ABSPATH')) {
  exit;
}

// Register custom Bricks element category.
add_filter('bricks/elements/categories', function ($categories) {
  foreach ($categories as $category) {
    if (!empty($category['name']) && $category['name'] === 'etp') {
      return $categories;
    }
  }

  $categories[] = [
    'name' => 'etp',
    'label' => esc_html__('Emerging Tech Policy', 'etp'),
    'icon' => 'ti-star',
  ];

  return $categories;
});

// Load all custom Bricks elements once Bricks is ready.
add_action('init', function () {
  $elements_dir = trailingslashit(ETP_THEME_INC) . 'bricks-elements/';

  if (!is_dir($elements_dir)) {
    error_log('Bricks setup: elements dir missing.');
    return;
  }

  foreach (glob($elements_dir . '*.php') as $element_file) {
    \Bricks\Elements::register_element($element_file);
  }
});
