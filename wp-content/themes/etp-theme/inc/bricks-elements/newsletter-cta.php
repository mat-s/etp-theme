<?php

/**
 * Custom Bricks element: Newsletter CTA.
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('\Bricks\Element')) {
  error_log('ETP Newsletter CTA: Bricks\\Element not available.');
  return;
}

class ETP_Newsletter_CTA extends \Bricks\Element
{
  public $category = 'etp';
  public $name     = 'etp-newsletter-cta';
  public $icon     = 'ti-email';

  public function get_label()
  {
    return esc_html__('ETP Newsletter CTA', 'etp');
  }

  public function set_controls()
  {
    $this->controls['headline'] = [
      'tab'         => 'content',
      'label'       => esc_html__('Headline', 'etp'),
      'type'        => 'text',
      'placeholder' => esc_html__('Stay in the loop', 'etp'),
    ];

    $this->controls['text'] = [
      'tab'         => 'content',
      'label'       => esc_html__('Text', 'etp'),
      'type'        => 'textarea',
      'placeholder' => esc_html__('Short supporting copy', 'etp'),
    ];

    $this->controls['form_html'] = [
      'tab'         => 'content',
      'label'       => esc_html__('Mailchimp form HTML', 'etp'),
      'type'        => 'code',
      'placeholder' => '<form>…</form>',
    ];
  }

  public function render()
  {
    $settings = $this->settings ?? [];

    $headline       = $settings['headline'] ?? '';
    $text           = $settings['text'] ?? '';
    $form_html      = $settings['form_html'] ?? '';

    $this->set_attribute('_root', 'class', 'etp-newsletter');

    ob_start();
?>
    <section <?php echo $this->render_attributes('_root'); ?>>
      <div class="etp-newsletter__content">
        <div class="etp-newsletter__copy">
          <?php if ($headline) : ?>
            <h2 class="etp-newsletter__headline"><?php echo esc_html($headline); ?></h2>
          <?php endif; ?>

          <?php if ($text) : ?>
            <div class="etp-newsletter__text"><?php echo wp_kses_post(wpautop($text)); ?></div>
          <?php endif; ?>
        </div>

        <?php if ($form_html) : ?>
          <div class="etp-newsletter__form">
            <?php echo $form_html; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>
<?php
    echo ob_get_clean();
  }
}

add_action('bricks/elements/register', function ($elements_manager) {
  error_log('ETP Newsletter CTA: register callback fired.');
  $elements_manager->register_element(ETP_Newsletter_CTA::class);
});
