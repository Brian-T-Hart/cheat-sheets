<?php

GFForms::include_addon_framework();

class GFFiltersAddOn extends GFAddOn
{

    protected $_version = GF_FILTERS_ADDON_VERSION;
    protected $_min_gravityforms_version = '1.9';
    protected $_slug = 'filtersaddon';
    protected $_path = 'gf-filters-addon/filtersaddon.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Gravity Forms Filters Add-On';
    protected $_short_title = 'Spam Filters';

    private static $_instance = null;

    public static function get_instance()
    {
        if (self::$_instance == null) {
            self::$_instance = new GFFiltersAddOn();
        }

        return self::$_instance;
    }

    public function init()
    {
        parent::init();

        $is_enabled = $this->get_plugin_setting('enabled');

        if ($is_enabled) {
            add_filter('gform_field_validation', array($this, 'custom_field_validation'), 10, 4);
        }
    }

    /**
     * * This function is called to initialize the add-on and set up the filters for each form.
     * * It checks if the add-on is enabled and if filters are not disabled for each form.
     */
    // public function init()
    // {
    //     parent::init();

    //     $is_enabled = $this->get_plugin_setting('enabled');

    //     if ($is_enabled) {
    //         $forms = GFAPI::get_forms();

    //         foreach ($forms as $form) {
    //             $settings = $this->get_form_settings($form);
    //             $filters_disabled = isset($settings['filters_disabled']) ? $settings['filters_disabled'] : false;

    //             if (!$filters_disabled) {
    //                 $form_id = $form['id'];
    //                 add_filter("gform_field_validation_$form_id", array($this, 'custom_field_validation'), 10, 4);
    //             }
    //         }
    //     }
    // }

    /**
     * Replace default icon with custom SVG icon.
     */
    public function get_menu_icon()
    {
        return '<svg style="height: 28px; width: 37px; max-width: 37px" width="1358" height="1056" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M3.9 54.9C10.5 40.9 24.5 32 40 32l432 0c15.5 0 29.5 8.9 36.1 22.9s4.6 30.5-5.2 42.5L320 320.9 320 448c0 12.1-6.8 23.2-17.7 28.6s-23.8 4.3-33.5-3l-64-48c-8.1-6-12.8-15.5-12.8-25.6l0-79.1L9 97.3C-.7 85.4-2.8 68.8 3.9 54.9z"/></svg>';
    }

    /**
     * * Returns the settings fields for the plugin settings page.
     * * Forms -> Settings -> Filters
     */
    public function plugin_settings_fields()
    {
        return array(
            array(
                'title'  => esc_html__('GF Filters Settings', 'filtersaddon'),
                'fields' => array(
                    array(
                        'name'    => 'enabled',
                        'type'    => 'checkbox',
                        'label'   => esc_html__('Activate GF Filters', 'filtersaddon'),
                        'tooltip' => esc_html__('Check the box to activate the filters.', 'filtersaddon'),
                        'choices' => array(
                            array(
                                'name'  => 'enabled',
                                'label' => esc_html__('Enabled', 'filtersaddon'),
                            ),
                        ),
                    ),
                    array(
                        'name'    => 'block_urls',
                        'type'    => 'checkbox',
                        'label'   => esc_html__('Block URLs', 'filtersaddon'),
                        'tooltip' => esc_html__('Check the box to add http, https, and www to the block list. Leave unchecked to allow users to include URLs in form submissions.', 'filtersaddon'),
                        'choices' => array(
                            array(
                                'name'  => 'block_urls',
                                'label' => esc_html__('Add http, https, and www to the block list', 'filtersaddon'),
                            ),
                        ),
                    ),
                    array(
                        'name'    => 'blocked_text',
                        'type'    => 'textarea',
                        'label'   => esc_html__('Blocked Text', 'filtersaddon'),
                        'description' => esc_html__('Enter words or phrases below (one per line)', 'filtersaddon'),
                        'tooltip' => esc_html__('Enter words or phrases below (one per line)', 'filtersaddon'),
                        'class'   => 'medium',
                    ),
                    array(
                        'name'    => 'blocked_email',
                        'type'    => 'textarea',
                        'label'   => esc_html__('Blocked Email', 'filtersaddon'),
                        'description' => esc_html__('Enter full or partial email addresses below (One per line)', 'filtersaddon'),
                        'tooltip' => esc_html__('Enter full or partial email addresses below (One per line)', 'filtersaddon'),
                        'class'   => 'medium',
                    ),
                ),
            ),
        );
    }

    /**
     * * Returns settings tab and fields for individual forms.
     */
    // public function form_settings_fields($form)
    // {
    //     return array(
    //         array(
    //             'title'  => esc_html__('Form Filter Settings', 'filtersaddon'),
    //             'fields' => array(
    //                 array(
    //                     'name'    => 'filters_disabled',
    //                     'type'    => 'checkbox',
    //                     'label'   => esc_html__('Disable GF Filters for this form', 'filtersaddon'),
    //                     'tooltip' => esc_html__('Check the box to deactivate the filters on this form.', 'filtersaddon'),
    //                     'choices' => array(
    //                         array(
    //                             'name'  => 'filters_disabled',
    //                             'label' => esc_html__('Disable Filters', 'filtersaddon'),
    //                         ),
    //                     ),
    //                 ),
    //             ),
    //         ),
    //     );
    // }

    /**
     * * This function is called to validate the settings for the plugin.
     */
    public function is_valid_setting($value)
    {
        return strlen($value) > 5;
    }

    /**
     * * This function is called to validate the field values when the form is submitted.
     * * It checks for Cyrillic and Greek characters, as well as custom stop words.
     */
    public function custom_field_validation($result, $value, $form, $field)
    {
        GFCommon::log_debug(__METHOD__ . '(): Running...');

        if (!$result['is_valid']) {
            return $result;
        }

        switch ($field->type) {
            case 'text':
            case 'textarea':

                // Run Cyrillic & Greek check.
                $cyrillic = preg_match('/[\p{Cyrillic}]/u', $value);
                $greek = preg_match('/[\p{Greek}]/u', $value);

                if ($cyrillic || $greek) {
                    GFCommon::log_debug(__METHOD__ . '(): Cyrillic or Greek detected!');
                    $result['is_valid'] = false;
                    $result['message'] = 'Sorry, there is a problem with your message. Please try again.';
                    break;
                }

                // Run Stop Words check.
                $custom_stop_words = $this->get_plugin_setting('blocked_text', '');

                if (!empty($custom_stop_words)) {
                    $custom_stop_words_array = array_map('trim', preg_split('/\r\n|\r|\n/', $custom_stop_words));
                } else {
                    $custom_stop_words_array = array();
                }

                if ($this->get_plugin_setting('block_urls')) {
                    $custom_stop_words_array[] = 'http';
                    $custom_stop_words_array[] = 'www';
                }

                $lower_value = strtolower(trim($value));
                $stop_words_detected = 0;

                foreach ($custom_stop_words_array as $stop_word) {
                    if (strpos($lower_value, strtolower($stop_word)) !== false) {
                        $stop_words_detected++;
                        GFCommon::log_debug(__METHOD__ . "(): Increased Stop Words counter for field id {$field->id}. Stop Word: {$stop_word}");
                    }
                }

                if ($stop_words_detected > 0) {
                    GFCommon::log_debug(__METHOD__ . "(): {$stop_words_detected} Stop words detected.");
                    $result['is_valid'] = false;
                    $result['message']  = 'Sorry, there is a problem with your entry. Please try again.';
                }
                break;

            case 'email':
                $custom_stop_words = $this->get_plugin_setting('blocked_email', '');

                if (!empty($custom_stop_words)) {
                    $custom_stop_words_array = array_map('trim', preg_split('/\r\n|\r|\n/', $custom_stop_words));
                } else {
                    $custom_stop_words_array = array();
                }

                $lower_value = strtolower(trim($value));
                $stop_words_detected = 0;

                // Check field value for Stop Words.
                foreach ($custom_stop_words_array as $stop_word) {
                    if (strpos($lower_value, strtolower($stop_word)) !== false) {
                        $stop_words_detected++;
                        GFCommon::log_debug(__METHOD__ . "(): Increased Stop Words counter for field id {$field->id}. Stop Word: {$stop_word}");
                    }
                }

                if ($stop_words_detected > 0) {
                    GFCommon::log_debug(__METHOD__ . "(): {$stop_words_detected} Stop words detected.");
                    $result['is_valid'] = false;
                    $result['message']  = 'Sorry, there is a problem with your email. Please try again.';
                }
                break;

            default:
                GFCommon::log_debug(__METHOD__ . "(): No validation occurred for field id {$field->id}.");
                break;
        }

        return $result;
    }
}
