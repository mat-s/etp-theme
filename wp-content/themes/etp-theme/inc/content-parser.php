<?php

/**
 * Content parsing helpers (plain text, word count, reading time).
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Return plain text of a post/page.
 *
 * @param int|null $post_id
 * @param bool $apply_filters Whether to run the_content filters.
 * @return string
 */
function etp_content_get_plaintext($post_id = null, $apply_filters = false)
{
  $resolved_id = $post_id ?: get_the_ID() ?: get_queried_object_id();
  if (!$resolved_id) {
    return '';
  }

  $post = get_post($resolved_id);
  if (!$post) {
    return '';
  }

  $content = $post->post_content ?? '';

  if ($apply_filters) {
    $content = apply_filters('the_content', $content);
  }

  $content = strip_shortcodes($content);
  $content = wp_strip_all_tags($content);
  $content = html_entity_decode($content, ENT_QUOTES, get_bloginfo('charset'));
  $content = trim(preg_replace('/\s+/', ' ', $content));

  return $content;
}

/**
 * Count words of a post/page.
 *
 * @param int|null $post_id
 * @param bool $apply_filters Whether to run the_content filters.
 * @return int
 */
function etp_content_count_words($post_id = null, $apply_filters = false)
{
  static $cache = [];

  $key = ($post_id ?: 'current') . '|' . (int)$apply_filters;
  if (isset($cache[$key])) {
    return $cache[$key];
  }

  $text  = etp_content_get_plaintext($post_id, $apply_filters);
  $count = $text === '' ? 0 : str_word_count($text);

  $cache[$key] = $count;

  return $count;
}

/**
 * Estimate reading time in whole minutes (min 1).
 *
 * @param int|null $post_id
 * @param int $words_per_minute
 * @param bool $apply_filters
 * @return int
 */
function etp_content_reading_time($post_id = null, $words_per_minute = 220, $apply_filters = false)
{
  $words = etp_content_count_words($post_id, $apply_filters);
  if ($words === 0) {
    return 0;
  }

  $wpm     = max(1, (int)$words_per_minute);
  $minutes = (int)ceil($words / $wpm);

  return max(1, $minutes);
}
