<?php

/**
 * Table of contents helper functions.
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Create a slug suitable for use as an HTML id.
 */
function etp_toc_slugify($text)
{
  $text = strtolower(wp_strip_all_tags($text));
  $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
  $text = preg_replace('/[\s-]+/', '-', $text);
  return trim($text, '-');
}

/**
 * Parse headings from HTML, ensure IDs, and return headings + updated HTML.
 *
 * @param string $html
 * @param array $levels Allowed heading tags (e.g. ['h2','h3','h4'])
 * @return array ['headings' => array, 'html' => string]
 */
function etp_toc_parse_headings_from_html($html, $levels = ['h2', 'h3', 'h4'])
{
  if (!$html) {
    return ['headings' => [], 'html' => ''];
  }

  $headings = [];
  $allowed  = array_map('strtolower', $levels);
  libxml_use_internal_errors(true);
  $dom = new DOMDocument();
  $loaded = $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_NOERROR | LIBXML_NOWARNING);
  libxml_clear_errors();

  if (!$loaded) {
    return ['headings' => [], 'html' => ''];
  }

  $seen_ids = [];

  $xpath = new DOMXPath($dom);
  $query = [];
  foreach ($allowed as $tag) {
    $query[] = "//{$tag}";
  }
  $nodes = $xpath->query(implode(' | ', $query));

  if ($nodes instanceof DOMNodeList) {
    foreach ($nodes as $node) {
      $tag   = strtolower($node->nodeName);
      $text  = trim($node->textContent);
      $id    = $node->getAttribute('id');
      $level = (int)filter_var($tag, FILTER_SANITIZE_NUMBER_INT);

      if ($text === '') {
        continue;
      }

      if (!$id) {
        $id = etp_toc_slugify($text);
      }

      // Ensure unique IDs in the TOC output.
      if (isset($seen_ids[$id])) {
        $suffix = ++$seen_ids[$id];
        $id     = "{$id}-{$suffix}";
      } else {
        $seen_ids[$id] = 0;
      }

      // Apply ID to the node so anchors can target it.
      $node->setAttribute('id', $id);

      $headings[] = [
        'text'  => $text,
        'id'    => $id,
        'level' => $level,
      ];
    }
  }

  // Extract body inner HTML (avoid full HTML wrapper)
  $body    = $dom->getElementsByTagName('body')->item(0);
  $newHtml = '';
  if ($body) {
    foreach ($body->childNodes as $child) {
      $newHtml .= $dom->saveHTML($child);
    }
  }

  return [
    'headings' => $headings,
    'html'     => $newHtml,
  ];
}

/**
 * Get headings from a post/page.
 *
 * @param int|null $post_id
 * @param array $levels
 * @param bool $apply_filters Apply the_content filters before parsing.
 * @return array
 */
function etp_toc_get_headings($post_id = null, $levels = ['h2', 'h3', 'h4'], $apply_filters = false)
{
  $resolved_id = $post_id ?: get_the_ID() ?: get_queried_object_id();
  if (!$resolved_id) {
    return [];
  }

  $post = get_post($resolved_id);
  if (!$post) {
    return [];
  }

  $html = $post->post_content ?? '';

  // Bricks content is stored in builder data; render it if post_content is empty.
  if ($html === '' && class_exists('\Bricks\Helpers') && class_exists('\Bricks\Frontend')) {
    static $rendering_bricks = false;

    // Avoid recursion if this helper is triggered during a Bricks render.
    if ($rendering_bricks === false) {
      $bricks_data = \Bricks\Helpers::get_bricks_data($resolved_id, 'content');

      if (!empty($bricks_data)) {
        $rendering_bricks = true;
        $html = \Bricks\Frontend::render_data($bricks_data, $resolved_id);
        $rendering_bricks = false;
      }
    }
  }

  if ($apply_filters) {
    $html = apply_filters('the_content', $html);
  }

  $parsed = etp_toc_parse_headings_from_html($html, $levels);

  return $parsed['headings'];
}

/**
 * Build TOC list HTML from heading array.
 *
 * @param array $headings
 * @param string $class Wrapper class.
 * @return string
 */
function etp_toc_render_list(array $headings)
{
  if (empty($headings)) {
    return '';
  }

  // Ensure sorted by appearance; we can't rely on level sort alone.
  // Keep as-is since parse function returns in DOM order per tag.
  $html    = '<nav class="etp-toc__menu"><ul>';
  $current = $headings[0]['level'];

  foreach ($headings as $heading) {
    $level = $heading['level'];

    while ($level > $current) {
      $html   .= '<ul>';
      $current = $level;
    }

    while ($level < $current) {
      $html   .= '</ul>';
      $current = $level;
    }

    $html .= '<li class="etp-toc__menu-item"><a href="#' . esc_attr($heading['id']) . '">' . esc_html($heading['text']) . '</a></li>';
  }

  // Close any remaining lists
  while ($current > $headings[0]['level']) {
    $html   .= '</ul>';
    $current--;
  }

  $html .= '</ul></nav>';

  return $html;
}

/**
 * Generate and cache TOC HTML for a post.
 *
 * @param int $post_id
 * @param array $levels
 * @param bool $apply_filters
 * @return string Cached HTML
 */
function etp_toc_generate_for_post($post_id, $levels = ['h2', 'h3', 'h4'], $apply_filters = true)
{
  if (!$post_id || get_post_status($post_id) !== 'publish') {
    return '';
  }

  $post = get_post($post_id);
  if (!$post) {
    return '';
  }

  $html_content = $post->post_content ?? '';

  if ($html_content === '' && class_exists('\Bricks\Helpers') && class_exists('\Bricks\Frontend')) {
    static $rendering_bricks = false;

    if ($rendering_bricks === false) {
      $bricks_data = \Bricks\Helpers::get_bricks_data($post_id, 'content');

      if (!empty($bricks_data)) {
        $rendering_bricks = true;
        $html_content     = \Bricks\Frontend::render_data($bricks_data, $post_id);
        $rendering_bricks = false;
      }
    }
  }

  if ($apply_filters) {
    $html_content = apply_filters('the_content', $html_content);
  }

  $parsed   = etp_toc_parse_headings_from_html($html_content, $levels);
  $headings = $parsed['headings'];
  $html     = etp_toc_render_list($headings);

  update_post_meta($post_id, '_etp_toc_html', $html);
  update_post_meta($post_id, '_etp_toc_levels', $levels);

  return $html;
}

/**
 * On save/update, regenerate TOC.
 */
add_action('save_post', function ($post_id, $post, $update) {
  // Skip autosave, revisions, or invalid post types.
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  if (wp_is_post_revision($post_id)) {
    return;
  }

  if (!in_array($post->post_type, ['post', 'page'], true)) {
    return;
  }

  etp_toc_generate_for_post($post_id);
}, 10, 3);
