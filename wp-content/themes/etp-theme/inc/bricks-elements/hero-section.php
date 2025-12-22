<?php

/**
 * Custom Bricks element: Hero Section.
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('\Bricks\Element')) {
  error_log('ETP Hero Section: Bricks\\Element not available.');
  return;
}

class ETP_Hero_Section extends \Bricks\Element
{
  public $category = 'etp';
  public $name = 'etp-hero-section';
  public $icon = 'ti-layout-media-overlay';

  public function get_label()
  {
    return esc_html__('ETP Hero Section', 'etp');
  }

  public function set_controls()
  {
    $this->controls['title'] = [
      'tab' => 'content',
      'label' => esc_html__('Title', 'etp'),
      'type' => 'text',
      'placeholder' => esc_html__('Defaults to the page title', 'etp'),
    ];

    $this->controls['image'] = [
      'tab' => 'content',
      'label' => esc_html__('Bild', 'etp'),
      'type' => 'image',
      'size' => 'full',
    ];

    $this->controls['text'] = [
      'tab' => 'content',
      'label' => esc_html__('Text', 'etp'),
      'type' => 'textarea',
      'placeholder' => esc_html__('Optional supporting copy', 'etp'),
    ];
  }

  public function render()
  {
    $settings = $this->settings ?? [];
    $post_id = get_the_ID() ?: get_queried_object_id();

    $title = isset($settings['title']) && $settings['title'] !== '' ? $settings['title'] : ($post_id ? get_the_title($post_id) : '');
    $text = $settings['text'] ?? '';

    $image_id = $settings['image']['id'] ?? null;
    if (!$image_id && $post_id) {
      $image_id = get_post_thumbnail_id($post_id);
    }

    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : '';
    $image_alt = '';

    if ($image_id) {
      $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
    }

    if (!$image_alt) {
      $image_alt = $title;
    }

    $this->set_attribute('_root', 'class', 'etp-hero');

    if ($image_url) {
      $this->set_attribute('_root', 'style', '--hero-image:url(' . esc_url($image_url) . ')');
    }

    ob_start();
    ?>
    <section <?php echo $this->render_attributes('_root'); ?>>
      <div class="etp-hero__content">
        <?php if ($title) : ?>
          <h1 class="etp-hero__title"><?php echo esc_html($title); ?></h1>
        <?php endif; ?>

        <?php if ($text) : ?>
          <div class="etp-hero__text"><?php echo wp_kses_post(wpautop($text)); ?></div>
        <?php endif; ?>
      </div>

      <?php if ($image_url) : ?>
        <div class="etp-hero__image">
          <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" loading="lazy" />
        </div>
      <?php endif; ?>
    </section>
    <?php
    echo ob_get_clean();
  }
}

add_action('bricks/elements/register', function ($elements_manager) {
  error_log('ETP Hero Section: register callback fired.');
  $elements_manager->register_element(ETP_Hero_Section::class);
});
