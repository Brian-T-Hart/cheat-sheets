<?php

add_filter('gform_pre_validation_2', 'custom_gf_email_throttle_submissions');
function custom_gf_email_throttle_submissions($form)
{
    $throttle_minutes = 1;

    // $ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
    $email = rgpost('input_3');

    if (!$email) {
        return $form;
    }

    $transient_key = 'gf_throttle_' . md5($form['id'] . $email);
    $last_submission_time = get_transient($transient_key);

    if ($last_submission_time) {
        $validation_message = apply_filters(
            'custom_gf_throttle_message',
            'You’ve submitted this form recently. Please wait a few minutes and try again.'
        );

        foreach ($form['fields'] as &$field) {
            if ($field->type != 'hidden') {
                $field->failed_validation = true;
                $field->validation_message = $validation_message;
                break;
            }
        }
    } else {
        $success = set_transient($transient_key, time(), $throttle_minutes * MINUTE_IN_SECONDS);

        if (!$success) {
            error_log('Failed to set transient for form throttling.');
        }
    }

    return $form;
}

add_filter('gform_pre_validation_2', 'custom_gf_time_throttle_submissions');
function custom_gf_time_throttle_submissions($form)
{
    $form_id = $form['id'];
    $limit = 10;
    $period_seconds = DAY_IN_SECONDS;

    $ip = $_SERVER['REMOTE_ADDR'];
    $transient_key = 'gf_throttle_counter_' . md5($form_id . '_' . $ip);
    $submission_data = get_transient($transient_key);

    if (!$submission_data) {
        // No record yet — initialize
        $submission_data = [
            'count' => 0,
            'start_time' => time()
        ];
    }

    // If the week has passed, reset counter
    if ((time() - $submission_data['start_time']) > $period_seconds) {
        $submission_data['count'] = 0;
        $submission_data['start_time'] = time();
    }

    if ($submission_data['count'] >= $limit) {
        // Block submission
        foreach ($form['fields'] as &$field) {
            if ($field->type != 'hidden') {
                $field->failed_validation = true;
                $field->validation_message = 'You have reached the submission limit. Please try again later.';
                break;
            }
        }
    } else {
        // Allow and increment
        $submission_data['count'] += 1;
        // Save back to transient with time remaining in the week
        $time_left = $period_seconds - (time() - $submission_data['start_time']);
        set_transient($transient_key, $submission_data, $time_left);
    }

    return $form;
}
