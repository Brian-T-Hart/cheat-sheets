<?php

/**
 * Gravity Forms – Custom reCAPTCHA Delay
 * Requires Gravity Forms 2.9+
 */
class GFDelayReCaptcha
{
    private static $version = '1.0.0';
    private static $script_paths = [
        'minjs' => 'gf-delay-recaptcha.min.js',
        'js' => 'gf-delay-recaptcha.js'
    ];

    /**
     * Initialize the class and hooks
     */
    public static function init()
    {
        add_action('gform_enqueue_scripts', [__CLASS__, 'enqueue_recaptcha_delay_script'], 12, 2);
    }

    /**
     * Enqueue the custom reCAPTCHA delay script
     */
    public static function enqueue_recaptcha_delay_script($form, $is_ajax)
    {
        $form_id = $form['id'];
        // $timestamp = time();
        $recaptcha_src = '';

        global $wp_scripts;
        if (isset($wp_scripts->registered['gform_recaptcha'])) {
            $recaptcha_src = $wp_scripts->registered['gform_recaptcha']->src;
        }

        // Get the directory of this file dynamically
        $current_dir = __DIR__;
        $stylesheet_dir = get_stylesheet_directory();

        // Calculate relative path from stylesheet to this directory
        $relative_path = str_replace(wp_normalize_path($stylesheet_dir), '', wp_normalize_path($current_dir));

        // Check for minified version first, then fallback to non-minified
        $script_filename = self::$script_paths['minjs'];
        $script_path = $relative_path . '/' . $script_filename;
        $full_path = $stylesheet_dir . $script_path;

        if (!file_exists($full_path)) {
            $script_filename = self::$script_paths['js'];
            $script_path = $relative_path . '/' . $script_filename;
            $full_path = $stylesheet_dir . $script_path;

            if (!file_exists($full_path)) {
                GFCommon::log_debug(__METHOD__ . "(): Error - Script file not found at " . $full_path);
                return;
            }
        }

        // Stop the reCAPTCHA script from loading by removing its src
        add_filter('script_loader_src', [__CLASS__, 'remove_recaptcha_src'], 10, 2);

        // Enqueue our custom script to handle the delayed loading of reCAPTCHA
        wp_enqueue_script(
            'gf-delay-recaptcha',
            get_stylesheet_directory_uri() . $script_path . '?v=' . self::$version,
            array(),
            null,
            true
        );

        // Pass data to the enqueued script
        $localized_data = [
            'formId' => $form_id,
            'ajaxEnabled' => $is_ajax,
            'recaptchaSrc' => esc_url($recaptcha_src)
        ];

        wp_localize_script('gf-delay-recaptcha', 'GFDelayReCaptchaData', $localized_data);


        GFCommon::log_debug(__METHOD__ . "(): Enqueued script for form ID {$form_id}.");
    } // enqueue_recaptcha_delay_script

    /**
     * Remove the reCAPTCHA script src to delay its loading
     */
    public static function remove_recaptcha_src($src, $handle)
    {
        if ($handle === 'gform_recaptcha') {

            GFCommon::log_debug(__METHOD__ . "(): Removed reCAPTCHA script src {$src}.");

            $src = "";
        }

        return $src;
    }
} // GFDelayReCaptcha

GFDelayReCaptcha::init();
