<?php

// Add a timestamp to the form submission to detect fast submissions
add_action('gform_enqueue_scripts', 'custom_enqueue_submission_timer_script', 10, 2);
function custom_enqueue_submission_timer_script($form, $is_ajax) {
    $form_id = $form['id'];
    $timestamp = time();

    wp_enqueue_script('gf-spam-timer', get_stylesheet_directory_uri() . '/functions/gf-spam-timer/gf-spam-timer.js', array(), null, true);
    wp_localize_script('gf-spam-timer', 'gfSpamTimerData', array(
        'formId' => $form_id,
        'timestamp' => $timestamp,
    ));
}

// Check the submission time and mark it as spam if submitted too quickly
add_filter('gform_entry_is_spam', 'mark_fast_submissions_as_spam', 10, 3);
function mark_fast_submissions_as_spam($is_spam, $form, $entry) {
    // Check if the form has the submission start time input
    if (!isset($_POST['gf_submission_start'])) {
        GFCommon::log_debug(__METHOD__ . "(): Form ID {$form['id']} - No submission start time found.");
        return $is_spam;
    }

    // Check if the time to submit is valid
    $start_time = intval($_POST['gf_submission_start']);
    $time_to_submit = time() - $start_time;
    $minimum_time = 3; // Minimum time in seconds to consider a valid submission
    $maximum_time = 86400; // Maximum time in seconds (86400 = 24 hours)
    $is_valid_time = ($time_to_submit >= $minimum_time && $time_to_submit <= $maximum_time);

    // If the time to submit is invalid, mark as spam
    if (!$is_valid_time) {
        GFCommon::log_debug(__METHOD__ . "(): Form ID {$form['id']} - Submission marked as spam for invalid time_passed ({$time_to_submit})");
        $_POST['gf_marked_spam_reason'] = "Marked as spam for invalid time: Submitted in {$time_to_submit} seconds.";
        return true; // Mark as spam
    }

    return $is_spam;
}

// Add a note to the entry when marked as spam
add_action('gform_entry_post_save', 'mark_spam_reason_too_fast', 10, 2);
function mark_spam_reason_too_fast($entry, $form) {
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
