<?php

/**
 * Custom Bricks element: Post Meta.
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('\Bricks\Element')) {
  error_log('ETP Post Meta: Bricks\\Element not available.');
  return;
}

class ETP_Post_Meta extends \Bricks\Element
{
  public $category = 'etp';
  public $name     = 'etp-post-meta';
  public $icon     = 'ti-info-alt';

  public function get_label()
  {
    return esc_html__('ETP Post Meta', 'etp');
  }

  public function set_controls()
  {
    $this->controls['show_tags'] = [
      'tab'     => 'content',
      'label'   => esc_html__('Tags anzeigen', 'etp'),
      'type'    => 'checkbox',
      'default' => true,
    ];

    $this->controls['reading_time'] = [
      'tab'         => 'content',
      'label'       => esc_html__('Lesezeit (Text)', 'etp'),
      'type'        => 'text',
      'placeholder' => esc_html__('25 min read', 'etp'),
      'default'     => esc_html__('25 min read', 'etp'),
    ];

    $this->controls['reading_time_icon'] = [
      'tab'   => 'content',
      'label' => esc_html__('Lesezeit Icon', 'etp'),
      'type'  => 'icon',
    ];

    $this->controls['share_label'] = [
      'tab'         => 'content',
      'label'       => esc_html__('Share-Text', 'etp'),
      'type'        => 'text',
      'placeholder' => esc_html__('Share', 'etp'),
      'default'     => esc_html__('Share', 'etp'),
    ];

    $this->controls['share_icon'] = [
      'tab'   => 'content',
      'label' => esc_html__('Share-Icon', 'etp'),
      'type'  => 'icon',
    ];

    $this->controls['share_link'] = [
      'tab'   => 'content',
      'label' => esc_html__('Share-Link', 'etp'),
      'type'  => 'link',
    ];
  }

  private function build_link_attributes($link)
  {
    if (!is_array($link) || empty($link['url'])) {
      return '';
    }

    $attrs   = [];
    $attrs[] = 'href="' . esc_url($link['url']) . '"';

    if (!empty($link['target'])) {
      $attrs[] = 'target="' . esc_attr($link['target']) . '"';
    }

    if (!empty($link['nofollow'])) {
      $attrs[] = 'rel="nofollow"';
    }

    return implode(' ', $attrs);
  }

  public function render()
  {
    $settings = $this->settings ?? [];
    $post_id  = get_the_ID() ?: get_queried_object_id();

    $show_tags    = !empty($settings['show_tags']);
    $reading_time = $settings['reading_time'] ?? '';
    $reading_icon = $settings['reading_time_icon'] ?? [];
    $share_label  = $settings['share_label'] ?? '';
    $share_icon   = $settings['share_icon'] ?? [];
    $share_link   = $settings['share_link'] ?? [];

    $tags_output = '';
    if ($show_tags && $post_id) {
      $tags = get_the_tags($post_id);
      if (!empty($tags) && is_array($tags)) {
        $first = $tags[0];
        $tags_output = '<span class="etp-post-meta__tag">' . esc_html($first->name) . '</span>';
      }
    }

    $updated = $post_id ? get_the_modified_time('jS M Y', $post_id) : '';

    $this->set_attribute('_root', 'class', 'etp-post-meta');

    $share_attrs = $this->build_link_attributes($share_link);
    $has_share   = $share_label || ($share_icon && !empty($share_icon['name']));

    if (!$tags_output && !$updated && !$reading_time && !$has_share) {
      return;
    }

    ?>
    <div <?php echo $this->render_attributes('_root'); ?>>
      <?php if ($tags_output) : ?>
        <div class="etp-post-meta__item etp-post-meta__item--tag">
          <?php echo $tags_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
      <?php endif; ?>

      <?php if ($updated) : ?>
        <div class="etp-post-meta__item">
          <?php echo esc_html(sprintf(__('Updated %s', 'etp'), $updated)); ?>
        </div>
      <?php endif; ?>

      <?php if ($reading_time) : ?>
        <div class="etp-post-meta__item etp-post-meta__item--time">
          <span class="etp-post-meta__time-icon">
            <?php if (!empty($reading_icon['library']) && !empty($reading_icon['icon'])) : ?>
              <?php echo $this->render_icon($reading_icon); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php endif; ?>
          </span>
          <span><?php echo esc_html($reading_time); ?></span>
        </div>
      <?php endif; ?>

      <?php if ($has_share) : ?>
        <div class="etp-post-meta__item etp-post-meta__item--share">
          <?php if ($share_attrs) : ?>
            <a class="etp-post-meta__share-link" <?php echo $share_attrs; ?>>
              <?php if ($share_label) : ?>
                <span><?php echo esc_html($share_label); ?></span>
              <?php endif; ?>
              <?php if (!empty($share_icon['library']) && !empty($share_icon['icon'])) : ?>
                <span class="etp-post-meta__share-icon">
                  <?php echo $this->render_icon($share_icon); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </span>
              <?php endif; ?>
            </a>
          <?php else : ?>
            <span class="etp-post-meta__share-link">
              <?php if ($share_label) : ?>
                <span><?php echo esc_html($share_label); ?></span>
              <?php endif; ?>
              <?php if (!empty($share_icon['library']) && !empty($share_icon['name'])) : ?>
                <span class="etp-post-meta__share-icon">
                  <?php echo $this->render_icon($share_icon); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </span>
              <?php endif; ?>
            </span>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
    <?php
  }
}

add_action('bricks/elements/register', function ($elements_manager) {
  error_log('ETP Post Meta: register callback fired.');
  $elements_manager->register_element(ETP_Post_Meta::class);
});
