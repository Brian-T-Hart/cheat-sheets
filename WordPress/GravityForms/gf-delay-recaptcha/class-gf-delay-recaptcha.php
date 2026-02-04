<?php

/**
 * Gravity Forms – Custom reCAPTCHA Delay
 * Requires Gravity Forms 2.9+
 */

class GFDelayReCaptcha
{
    private static $src = '';

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
        $timestamp = time();

        add_filter('script_loader_src', [__CLASS__, 'remove_recaptcha_src'], 10, 2);

        wp_enqueue_script(
            'gf-delay-recaptcha',
            get_stylesheet_directory_uri() . '/functions/gf-delay-recaptcha/gf-delay-recaptcha.js?v=' . $timestamp,
            array(),
            null,
            true
        );

        // Pass data to the enqueued script
        $localized_data = [
            'formId' => $form_id,
            'ajaxEnabled' => $is_ajax,
            'recaptchaSrc' => self::$src,
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
            self::$src = $src;
            
            GFCommon::log_debug(__METHOD__ . "(): Script src = {self::$src}.");

            $src = "";
        }

        return $src;
    }
}

GFDelayReCaptcha::init();