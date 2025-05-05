<?php

/**
 * Gravity Forms Rate Limiter Add-On
 *
 * This class implements a rate limiting mechanism for Gravity Forms submissions.
 * It prevents users from submitting the same form multiple times within a short period.
 *
 * @package GravityForms
 * @subpackage AddOn
 */
class GF_RateLimiter
{
    private static $limit_count = 1; // Number of allowed submissions
    private static $throttle_time = 10; // Time period for the throttle in seconds
    private static $wait_time = 30; // Time period for the limit in seconds
    private static $max_wait_time = 3600; // Maximum wait time in seconds

    /**
     * Initialize the rate limiter.
     */
    public static function init()
    {
        // add_filter('gform_pre_validation', [__CLASS__, 'gf_check_submission_count']);
        add_action('gform_pre_submission', [__CLASS__, 'check_submission_rate']);
        add_filter('gform_entry_is_spam', [__CLASS__, 'maybe_mark_as_spam'], 10, 3);
        add_action('gform_entry_post_save', [__CLASS__, 'add_spam_note'], 10, 2);
    }

    /**
     * Check if the submission count exceeds the limit.
     */
    public static function gf_check_submission_count($form)
    {
        $ip = self::get_user_ip();
        $agent = sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
        $form_id = $form['id'];
        $transient_key = 'gf_throttle_counter_' . md5($form_id . $ip . $agent);
        $submission_data = get_transient($transient_key);

        if (!$submission_data) {
            $submission_data = [
                'count' => 0,
                'start_time' => time(),
                'wait_time' => self::$wait_time
            ];
        }

        // Reset the count if the time period has passed
        if ((time() - $submission_data['start_time']) > self::$wait_time) {
            $submission_data['count'] = 0;
            $submission_data['start_time'] = time();
            // $submission_data['wait_time'] = self::$wait_time;
        }

        if ($submission_data['count'] >= self::$limit_count) {
            // Block submission
            foreach ($form['fields'] as &$field) {
                if ($field->type != 'hidden') {
                    $field->failed_validation = true;
                    $field->validation_message = sprintf(
                        __('You have reached the submission limit. Please try again in %d seconds.', 'text-domain'),
                        $submission_data['wait_time']
                    );
                    break;
                }
            }

            // Increase the wait time for the next failed attempt
            $submission_data['start_time'] = time(); // Increment the count
            $submission_data['wait_time'] = min($submission_data['wait_time'] * 2, self::$max_wait_time); // Add a static $max_wait_time property
        } else {
            $submission_data['count'] += 1;
        }

        // Increment the count and set the transient
        $time_left = max($submission_data['wait_time'] - (time() - $submission_data['start_time']), 1); // Minimum 1 second
        set_transient($transient_key, $submission_data, $time_left);
        GFCommon::log_debug(__METHOD__ . "(): Submission data: " . print_r($submission_data, true));

        return $form;
    }

    /**
     * Check if the form submission is too frequent.
     */
    public static function check_submission_rate($form)
    {
        $ip = self::get_user_ip();
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $form_id = $form['id'];
        $key = 'gf_rate_limit_' . md5($form_id . $ip . $agent);

        if (get_transient($key)) {
            $_POST['gf_rate_limit_flagged'] = true;
        } else {
            set_transient($key, true, self::$throttle_time); // Lockout window in seconds
        }
    }

    /**
     * Mark the entry as spam if flagged.
     */
    public static function maybe_mark_as_spam($is_spam, $form, $entry)
    {
        if (!empty($_POST['gf_rate_limit_flagged'])) {
            $_POST['gf_rate_limit_spam_reason'] = 'Marked as spam: repeated submission from same IP within 60 seconds.';
            return true;
        }
        return $is_spam;
    }

    /**
     * Add a note to the entry if it was marked as spam.
     */
    public static function add_spam_note($entry, $form)
    {
        if (!empty($_POST['gf_rate_limit_spam_reason'])) {
            GFFormsModel::add_note(
                $entry['id'],
                0, // 0 = system note
                'GF Filters Add-On',
                sanitize_text_field($_POST['gf_rate_limit_spam_reason'])
            );
        }
        return $entry;
    }

    /**
     * Utility: Get user IP address.
     */
    private static function get_user_ip()
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'] ?? ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);
        $ip = explode(',', $ip)[0]; // Use the first IP in the list
        return filter_var($ip, FILTER_VALIDATE_IP) ?: 'unknown';
    }
}
