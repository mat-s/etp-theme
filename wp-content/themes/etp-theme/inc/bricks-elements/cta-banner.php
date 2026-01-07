<?php

/**
 * Custom Bricks element: CTA Banner.
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('\Bricks\Element')) {
  error_log('ETP CTA Banner: Bricks\\Element not available.');
  return;
}

class ETP_CTA_Banner extends \Bricks\Element
{
  public $category = 'etp';
  public $name     = 'etp-cta-banner';
  public $icon     = 'ti-layout-cta-left';

  public function get_label()
  {
    return esc_html__('ETP CTA Banner', 'etp');
  }

  public function set_controls()
  {
    $this->controls['image'] = [
      'tab'   => 'content',
      'label' => esc_html__('Bild', 'etp'),
      'type'  => 'image',
      'size'  => 'large',
      'default' => [
        'id'  => 0,
        'url' => method_exists('\Bricks\Helpers', 'get_image_placeholder_url') ? \Bricks\Helpers::get_image_placeholder_url() : '',
      ],
    ];

    $this->controls['headline'] = [
      'tab'         => 'content',
      'label'       => esc_html__('Überschrift', 'etp'),
      'type'        => 'text',
      'placeholder' => esc_html__('Your CTA headline', 'etp'),
      'default'     => esc_html__('Your CTA headline', 'etp'),
    ];

    $this->controls['text'] = [
      'tab'         => 'content',
      'label'       => esc_html__('Text', 'etp'),
      'type'        => 'textarea',
      'placeholder' => esc_html__('Optional supporting text', 'etp'),
      'default'     => esc_html__('Optional supporting text', 'etp'),
    ];

    $this->controls['button_text'] = [
      'tab'         => 'content',
      'label'       => esc_html__('Button-Text', 'etp'),
      'type'        => 'text',
      'placeholder' => esc_html__('Call to action', 'etp'),
      'default'     => esc_html__('Call to action', 'etp'),
    ];

    $this->controls['button_icon'] = [
      'tab'   => 'content',
      'label' => esc_html__('Button-Icon', 'etp'),
      'type'  => 'icon',
    ];

    $this->controls['link'] = [
      'tab'   => 'content',
      'label' => esc_html__('Link', 'etp'),
      'type'  => 'link',
    ];

    $this->controls['style_variant'] = [
      'tab'         => 'content',
      'label'       => esc_html__('Style', 'etp'),
      'type'        => 'select',
      'options'     => [
        'light' => esc_html__('Light', 'etp'),
        'dark'  => esc_html__('Dark', 'etp'),
      ],
      'placeholder' => 'light',
      'default'     => 'light',
      'inline'      => true,
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

    $headline = $settings['headline'] ?? '';
    $text     = $settings['text'] ?? '';
    $button   = $settings['button_text'] ?? '';
    $icon     = $settings['button_icon'] ?? '';
    $link     = $settings['link'] ?? [];
    $image    = $settings['image'] ?? [];
    $style    = $settings['style_variant'] ?? 'light';

    if ($headline === '' && $button === '' && empty($image['url'])) {
      return;
    }

    if (empty($image['url']) && method_exists('\Bricks\Builder', 'get_template_placeholder_image')) {
      $image = [
        'url' => \Bricks\Builder::get_template_placeholder_image(),
        'alt' => '',
      ];
    }

    $style_class = $style === 'dark' ? 'is-dark' : 'is-light';
    $link_attrs  = $this->build_link_attributes($link);

    $this->set_attribute('_root', 'class', 'etp-cta-banner sidebar-element ' . $style_class);

?>
    <div <?php echo $this->render_attributes('_root'); ?>>
      <div class="etp-cta-banner__inner">
        <?php if (!empty($image['url'])) : ?>
          <div class="etp-cta-banner__image">
            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt'] ?? ''); ?>" loading="lazy" decoding="async" />
          </div>
        <?php endif; ?>

        <div class="etp-cta-banner__body">
          <?php if ($headline) : ?>
            <h3 class="etp-cta-banner__headline"><?php echo esc_html($headline); ?></h3>
          <?php endif; ?>

          <?php if ($text) : ?>
            <div class="etp-cta-banner__text">
              <?php echo wp_kses_post(wpautop($text)); ?>
            </div>
          <?php endif; ?>

          <?php if ($button && $link_attrs) : ?>
            <a class="etp-cta-banner__button" <?php echo $link_attrs; ?>>
              <span class="etp-cta-banner__button-text"><?php echo esc_html($button); ?></span>
              <?php if (!empty($icon['library']) && !empty($icon['icon'])) : ?>
                <span class="etp-cta-banner__button-icon">
                  <?php echo $this->render_icon($icon); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </span>
              <?php endif; ?>
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
<?php
  }
}

add_action('bricks/elements/register', function ($elements_manager) {
  error_log('ETP CTA Banner: register callback fired.');
  $elements_manager->register_element(ETP_CTA_Banner::class);
});
