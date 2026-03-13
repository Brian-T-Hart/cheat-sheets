<?php
/** 
 * How to create custom elements in Bricks
 * https://academy.bricksbuilder.io/article/create-your-own-elements
 */

if (! defined('ABSPATH')) exit; // Exit if accessed directly
if (! class_exists('\Bricks\Elements')) exit; // Exit if Bricks is not active

class Element_Gravity_Form extends \Bricks\Element
{
  public $category     = 'custom';
  public $name         = 'ypm-gravity-form';
  public $icon         = 'fa-solid fa-file-pen'; // FontAwesome 5 icon in builder (https://fontawesome.com/icons)
  public $css_selector = '.gf-form-wrapper'; // Default CSS selector for all controls with 'css' properties
  public $nestable     = false; // true || @since 1.5

  public function get_label()
  {
    return esc_html__('Gravity Form', 'bricks');
  }

  public function set_control_groups()
  {
    /*
    $this->control_groups['custom'] = [
      'title' => esc_html__( 'Custom', 'bricks' ),
      'tab'   => 'content', // Accepts: 'content' or 'style'
    ];
    */
  }

  public function set_controls()
  {
    $forms = GFAPI::get_forms();
    $list = array();

    foreach ($forms as $form) {
      $id = $form['id'];
      $title = $form['title'] . ' [' . $id . ']';
      $list[$id] = esc_html__($title, 'bricks');
    }

    $this->controls['gravityForm'] = [
      'tab'         => 'content',
      'label'       => esc_html__('Gravity Form', 'bricks'),
      'type'        => 'select',
      'options'     => $list,
      'default'     => '',
      'placeholder' => esc_html__('Choose a Gravity Form', 'bricks')
    ];

    $this->controls['fieldValues'] = [
      'tab'            => 'content',
      'label'          => esc_html__('Field Values', 'bricks'),
      'type'           => 'text',
      'default'        => esc_html__('', 'bricks'),
      'placeholder'    => esc_html__('add additional field values here', 'bricks')
    ];

    $this->controls['addClass'] = [
      'tab'            => 'content',
      'label'          => esc_html__('Add a CSS Class', 'bricks'),
      'type'           => 'text',
      'default'        => esc_html__('', 'bricks'),
      'placeholder'    => esc_html__('separate by spaces, class name(s) only', 'bricks')
    ];
  }

  /** 
   * Render element HTML on frontend
   * If no 'render_builder' function is defined then this code is used to render element HTML in builder, too.
   */
  public function render()
  {
    $settings = $this->settings;
    $gravityForm   = !empty($settings['gravityForm']) ? $settings['gravityForm'] : false;
    $fieldValues    = !empty($settings['fieldValues']) ? $settings['fieldValues'] : false;
    $addClass    = !empty($settings['addClass']) ? $settings['addClass'] : false;

    if (!$gravityForm) {
      $output = '<h5>Please select a Gravity Form</h5>';
    } else {
      $shortcode = '[gravityform id="' . $gravityForm . '" title="false" description="false" ajax="true"';
      if ($fieldValues) $shortcode .= ' field_values="' . $fieldValues . '"';
      $shortcode .= ' /]';

      $output = do_shortcode($shortcode);
    }

    // Set element class attributes
    $root_classes[] = 'form-common full-container';
    if ($addClass) $root_classes[] = $addClass;

    // Add 'class' attribute to element root tag
    $this->set_attribute('_root', 'class', $root_classes);

    // Render element HTML
    // '_root' attribute is required since Bricks 1.4 (contains element ID, class, etc.)
    echo "<div {$this->render_attributes('_root')}>"; // Element root attributes
    echo $output;
    echo '</div>';
  }
}
