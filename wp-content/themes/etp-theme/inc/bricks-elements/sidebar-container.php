<?php

/**
 * Custom Bricks element: Sidebar Container.
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('\Bricks\Element_Container')) {
  error_log('ETP Sidebar Container: Bricks\\Element_Container not available.');
  return;
}

class ETP_Sidebar_Container extends \Bricks\Element_Container
{
  public $category      = 'etp';
  public $name          = 'etp-sidebar-container';
  public $icon          = 'ti-layout-sidebar-left';
  public $vue_component = 'bricks-nestable';
  public $nestable      = true;

  public function get_label()
  {
    return esc_html__('ETP Sidebar Container', 'etp');
  }

  public function get_keywords()
  {
    return ['sidebar', 'grid', 'layout', 'container', 'nestable'];
  }

  public function render()
  {
    $element = $this->element ?? [];

    // Default grid class and display to enable CSS grid layouts.
    $this->set_attribute('_root', 'class', 'etp-sidebar-container brxe-container');

    parent::render();
  }
}

add_action('bricks/elements/register', function ($elements_manager) {
  error_log('ETP Sidebar Container: register callback fired.');
  $elements_manager->register_element(ETP_Sidebar_Container::class);
});
