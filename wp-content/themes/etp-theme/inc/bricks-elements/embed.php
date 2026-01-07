<?php

/**
 * Custom Bricks element: Embed.
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('\Bricks\Element')) {
  error_log('ETP Embed: Bricks\\Element not available.');
  return;
}

class ETP_Embed extends \Bricks\Element
{
  public $category = 'etp';
  public $name     = 'etp-embed';
  public $icon     = 'ti-world';

  public function get_label()
  {
    return esc_html__('ETP Embed', 'etp');
  }

  public function set_controls()
  {
    $this->controls['headline'] = [
      'tab'         => 'content',
      'label'       => esc_html__('Headline', 'etp'),
      'type'        => 'text',
      'placeholder' => esc_html__('Optional heading', 'etp'),
    ];

    $this->controls['headline_tag'] = [
      'tab'         => 'content',
      'label'       => esc_html__('Heading tag', 'etp'),
      'type'        => 'select',
      'options'     => [
        'h1' => 'H1',
        'h2' => 'H2',
        'h3' => 'H3',
        'h4' => 'H4',
        'h5' => 'H5',
        'h6' => 'H6',
        'p'  => 'p',
        'div'=> 'div',
      ],
      'placeholder' => 'h3',
      'inline'      => true,
    ];

    $this->controls['html'] = [
      'tab'   => 'content',
      'label' => esc_html__('Embed HTML', 'etp'),
      'type'  => 'code',
      'mode'  => 'text/html',
    ];
  }

  public function render()
  {
    $headline = $this->settings['headline'] ?? '';
    $html     = $this->settings['html'] ?? '';
    $tag      = $this->settings['headline_tag'] ?? 'h3';
    $allowed_tags = ['h1','h2','h3','h4','h5','h6','p','div'];
    if (!in_array($tag, $allowed_tags, true)) {
      $tag = 'h3';
    }

    if ($html === '') {
      return;
    }

    $this->set_attribute('_root', 'class', 'etp-embed');

    ?>
    <div <?php echo $this->render_attributes('_root'); ?>>
      <?php if ($headline) : ?>
        <<?php echo esc_attr($tag); ?> class="etp-embed__headline"><?php echo esc_html($headline); ?></<?php echo esc_attr($tag); ?>>
      <?php endif; ?>
      <?php echo $html; ?>
    </div>
    <?php
  }
}

add_action('bricks/elements/register', function ($elements_manager) {
  error_log('ETP Embed: register callback fired.');
  $elements_manager->register_element(ETP_Embed::class);
});
