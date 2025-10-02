<?php

class GFSpamTimer
{
    private static $minimum_time = 5; // Minimum time in seconds to consider a valid submission
    private static $secret_key = 'ny3vNfD6tU0J';

    /**
     * Initialize the spam timer.
     */
    public static function init()
    {
        add_action('gform_enqueue_scripts', [__CLASS__, 'custom_enqueue_submission_timer_script']);
        add_filter('gform_entry_is_spam', [__CLASS__, 'mark_fast_submissions_as_spam'], 11, 3);
        add_action('gform_entry_post_save', [__CLASS__, 'mark_spam_reason_too_fast'], 12, 2);
    }

    // Add a timestamp to the form submission to detect fast submissions
    public static function custom_enqueue_submission_timer_script($form)
    {
        $form_id = $form['id'];
        $timestamp = time();

        wp_enqueue_script('gf-spamtimer', get_stylesheet_directory_uri() . '/functions/gf-spam-timer-frontend/gf-spamtimer.js?v=' . $timestamp, array(), null, true);

        GFCommon::log_debug(__METHOD__ . "(): Enqueued script for form ID {$form_id}.");
    }

    // Check the submission time and mark it as spam if submitted too quickly
    public static function mark_fast_submissions_as_spam($is_spam, $form, $entry)
    {
        // Check if the form has the required hidden field and value
        if (!isset($_POST['gf_st_custom_1'])) {
            GFCommon::log_debug(__METHOD__ . "(): Form ID {$form['id']} - Secret key is missing");
            $_POST['gf_marked_spam_reason'] = "Marked as spam - Secret key is missing";
            return true;
        }

        if ($_POST['gf_st_custom_1'] !== self::$secret_key) {
            GFCommon::log_debug(__METHOD__ . "(): Form ID {$form['id']} - Secret key mismatch.");
            $_POST['gf_marked_spam_reason'] = "Marked as spam - Secret key mismatch.";
            return true;
        }

        return $is_spam;
    }

    // Add a note to the entry when marked as spam
    public static function mark_spam_reason_too_fast($entry, $form)
    {
        if (!empty($_POST['gf_marked_spam_reason'])) {
            GFFormsModel::add_note(
                $entry['id'],
                get_current_user_id(), // Or 0 for system
                'GF Spam Timer',
                sanitize_text_field($_POST['gf_marked_spam_reason'])
            );
        }

        return $entry;
    }
}

//if gravity forms is active, initialize the spam timer
if (class_exists('GFForms')) {
    GFSpamTimer::init();
}
