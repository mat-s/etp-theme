<?php

/**
 * Custom Bricks element: Content Cards.
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('\Bricks\Element')) {
  error_log('ETP Content Cards: Bricks\\Element not available.');
  return;
}

class ETP_Content_Cards extends \Bricks\Element
{
  public $category = 'etp';
  public $name     = 'etp-content-cards';
  public $icon     = 'ti-layout-grid2';

  public function get_label()
  {
    return esc_html__('ETP Content Cards', 'etp');
  }

  public function set_controls()
  {
    $this->controls['columns'] = [
      'tab'     => 'content',
      'label'   => esc_html__('Columns', 'etp'),
      'type'    => 'select',
      'options' => [
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
      ],
      'inline'      => true,
      'placeholder' => '3',
    ];

    $this->controls['items'] = [
      'tab'        => 'content',
      'label'      => esc_html__('Cards', 'etp'),
      'type'       => 'repeater',
      'titleField' => 'title',
      'fields'     => [
        'source' => [
          'label'       => esc_html__('Quelle', 'etp'),
          'type'        => 'select',
          'options'     => [
            'manual' => esc_html__('Manuell', 'etp'),
            'post'   => esc_html__('Post/Seite', 'etp'),
          ],
          'placeholder' => 'manual',
          'inline'      => true,
        ],
        'post' => [
          'label'       => esc_html__('Post/Seite auswählen', 'etp'),
          'type'        => 'post',
          'placeholder' => esc_html__('Post auswählen', 'etp'),
          'required'    => ['source', '=', 'post'],
        ],
        'image' => [
          'label'    => esc_html__('Bild', 'etp'),
          'type'     => 'image',
          'size'     => 'large',
          'required' => ['source', '=', 'manual'],
        ],
        'title' => [
          'label'       => esc_html__('Überschrift', 'etp'),
          'type'        => 'text',
          'placeholder' => esc_html__('Card title', 'etp'),
          'required'    => ['source', '=', 'manual'],
        ],
        'text' => [
          'label'       => esc_html__('Text', 'etp'),
          'type'        => 'textarea',
          'placeholder' => esc_html__('Kurzbeschreibung', 'etp'),
          'required'    => ['source', '=', 'manual'],
        ],
        'link' => [
          'label'    => esc_html__('Link', 'etp'),
          'type'     => 'link',
          'required' => ['source', '=', 'manual'],
        ],
      ],
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

  private function get_post_data($post_id)
  {
    $post = get_post($post_id);
    if (!$post) {
      return null;
    }

    $image_id  = get_post_thumbnail_id($post);
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : '';
    $image_alt = $image_id ? get_post_meta($image_id, '_wp_attachment_image_alt', true) : '';

    if (!$image_alt) {
      $image_alt = get_the_title($post);
    }

    $text = get_the_excerpt($post);
    if (!$text) {
      $text = wp_trim_words(wp_strip_all_tags($post->post_content), 28);
    }

    return [
      'title' => get_the_title($post),
      'text'  => $text,
      'link'  => [
        'url' => get_permalink($post),
      ],
      'image' => [
        'url' => $image_url,
        'alt' => $image_alt,
      ],
    ];
  }

  public function render()
  {
    $settings = $this->settings ?? [];
    $items    = $settings['items'] ?? [];
    if (empty($items) || !is_array($items)) {
      $items = [
        [
          'source' => 'manual',
          'title'  => esc_html__('Sample card one', 'etp'),
          'text'   => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor.', 'etp'),
          'link'   => ['url' => '#'],
          'image'  => ['url' => '', 'alt' => ''],
        ],
        [
          'source' => 'manual',
          'title'  => esc_html__('Sample card two', 'etp'),
          'text'   => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor.', 'etp'),
          'link'   => ['url' => '#'],
          'image'  => ['url' => '', 'alt' => ''],
        ],
        [
          'source' => 'manual',
          'title'  => esc_html__('Sample card three', 'etp'),
          'text'   => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor.', 'etp'),
          'link'   => ['url' => '#'],
          'image'  => ['url' => '', 'alt' => ''],
        ],
      ];
    }

    $columns = isset($settings['columns']) && in_array((string)$settings['columns'], ['1', '2', '3', '4'], true)
      ? (int)$settings['columns']
      : 3;

    $this->set_attribute('_root', 'class', 'etp-cards');
    $this->set_attribute('_root', 'style', '--cards-columns:' . $columns);

    ?>
    <div <?php echo $this->render_attributes('_root'); ?>>
      <div class="etp-cards__list">
        <?php foreach ($items as $item) :
          $source = $item['source'] ?? 'manual';
          $card   = [];

          if ($source === 'post' && !empty($item['post'])) {
            $card = $this->get_post_data((int)$item['post']);
          } else {
            $card = [
              'title' => $item['title'] ?? '',
              'text'  => $item['text'] ?? '',
              'link'  => $item['link'] ?? [],
              'image' => [
                'url' => $item['image']['url'] ?? '',
                'alt' => $item['image']['alt'] ?? ($item['title'] ?? ''),
              ],
            ];
          }

          if (empty($card['title']) && empty($card['text']) && empty($card['image']['url'])) {
            continue;
          }

          $link_attrs = $this->build_link_attributes($card['link'] ?? []);
        ?>
          <article class="etp-card">
            <?php if (!empty($card['image']['url'])) : ?>
              <div class="etp-card__image">
                <img src="<?php echo esc_url($card['image']['url']); ?>" alt="<?php echo esc_attr($card['image']['alt'] ?? ''); ?>" loading="lazy" decoding="async" />
              </div>
            <?php endif; ?>

            <div class="etp-card__body">
              <?php if (!empty($card['title'])) : ?>
                <?php if ($link_attrs) : ?>
                  <a class="etp-card__title" <?php echo $link_attrs; ?>>
                    <?php echo esc_html($card['title']); ?>
                  </a>
                <?php else : ?>
                  <div class="etp-card__title"><?php echo esc_html($card['title']); ?></div>
                <?php endif; ?>
              <?php endif; ?>

              <?php if (!empty($card['text'])) : ?>
                <div class="etp-card__text"><?php echo esc_html($card['text']); ?></div>
              <?php endif; ?>

              <?php if ($link_attrs) : ?>
                <a class="etp-card__cta" <?php echo $link_attrs; ?>>
                  <?php echo esc_html__('Learn more', 'etp'); ?> →
                </a>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
    <?php
  }
}

add_action('bricks/elements/register', function ($elements_manager) {
  error_log('ETP Content Cards: register callback fired.');
  $elements_manager->register_element(ETP_Content_Cards::class);
});
