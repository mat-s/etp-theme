<?php

/**
 * Helper functions for the ETP theme.
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Returns breadcrumbs HTML in the form "Home / Parent / Current".
 *
 * @param string $separator Separator between crumbs.
 * @return string
 */
function etp_get_breadcrumbs($separator = ' / ')
{
  if (is_front_page()) {
    return esc_html__('Home', 'etp');
  }

  $crumbs = [];
  $separator_html = '<span class="etp-breadcrumb__separator">' . esc_html($separator) . '</span>';

  // Home link.
  $crumbs[] = sprintf(
    '<a href="%s" class="etp-breadcrumb__link">%s</a>',
    esc_url(home_url('/')),
    esc_html__('Home', 'etp')
  );

  $post = get_post();
  if (!$post) {
    return implode($separator_html, $crumbs);
  }

  $ancestors = array_reverse(get_post_ancestors($post));

  foreach ($ancestors as $ancestor_id) {
    $crumbs[] = sprintf(
      '<a href="%s" class="etp-breadcrumb__link">%s</a>',
      esc_url(get_permalink($ancestor_id)),
      esc_html(get_the_title($ancestor_id))
    );
  }

  // Current item (no link).
  $crumbs[] = sprintf(
    '<span class="etp-breadcrumb__current">%s</span>',
    esc_html(get_the_title($post))
  );

  return implode($separator_html, $crumbs);
}
