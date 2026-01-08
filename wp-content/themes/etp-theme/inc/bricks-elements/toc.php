<?php

/**
 * Custom Bricks element: Table of Contents.
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('\Bricks\Element')) {
  error_log('ETP TOC: Bricks\\Element not available.');
  return;
}

class ETP_TOC extends \Bricks\Element
{
  public $category = 'etp';
  public $name     = 'etp-toc';
  public $icon     = 'ti-list';

  public function get_label()
  {
    return esc_html__('ETP Table of Contents', 'etp');
  }

  public function set_controls()
  {
    $this->controls['levels'] = [
      'tab'         => 'content',
      'label'       => esc_html__('Überschriften-Level', 'etp'),
      'type'        => 'select',
      'options'     => [
        'h2,h3'       => 'H2–H3',
        'h2,h3,h4'    => 'H2–H4',
        'h2,h3,h4,h5' => 'H2–H5',
        'h3,h4'       => 'H3–H4',
      ],
      'placeholder' => 'h2,h3,h4',
    ];

    $this->controls['apply_filters'] = [
      'tab'     => 'content',
      'label'   => esc_html__('the_content Filter anwenden', 'etp'),
      'type'    => 'checkbox',
      'default' => false,
    ];

    $this->controls['wrapper_class'] = [
      'tab'         => 'style',
      'label'       => esc_html__('Wrapper-Klasse', 'etp'),
      'type'        => 'text',
      'placeholder' => esc_html__('etp-toc', 'etp'),
    ];
  }

  public function render()
  {
    $settings = $this->settings ?? [];
    $levels   = isset($settings['levels']) && $settings['levels'] !== '' ? explode(',', $settings['levels']) : ['h2', 'h3', 'h4'];
    $levels   = array_map('trim', $levels);

    $apply_filters = !empty($settings['apply_filters']);
    $wrapper_class = $settings['wrapper_class'] ?? 'etp-toc';
    $post_id       = get_the_ID() ?: get_queried_object_id();

    // Use cached TOC HTML; fallback: generate on the fly (safe mode).
    $toc_html = '';
    if ($post_id) {
      $toc_html = get_post_meta($post_id, '_etp_toc_html', true);
    }

    if ($toc_html === '' && function_exists('etp_toc_generate_for_post')) {
      $toc_html = etp_toc_generate_for_post($post_id, $levels, $apply_filters);
    }

    if ($toc_html === '') {
      echo $this->render_element_placeholder([
        'title' => esc_html__('Keine Überschriften gefunden.', 'etp'),
      ]);
      return;
    }

    // Wrapper class attribute on root
    $this->set_attribute('_root', 'class', $wrapper_class . ' sidebar-element');

    ?>
    <div <?php echo $this->render_attributes('_root'); ?>>
      <button class="etp-toc__toggle" type="button" aria-expanded="true">
        <span class="etp-toc__toggle-label"><?php echo esc_html__('Contents', 'etp'); ?></span>
        <span class="etp-toc__toggle-icon" aria-hidden="true">▾</span>
      </button>

      <div class="etp-toc__body">
        <?php echo $toc_html; // already escaped in helper ?>
      </div>
    </div>
    <?php
  }
}

add_action('bricks/elements/register', function ($elements_manager) {
  error_log('ETP TOC: register callback fired.');
  $elements_manager->register_element(ETP_TOC::class);
});
