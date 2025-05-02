<?php

/**
 * Remove empty gaconnector values from the $_POST array before submission.
 */
add_action('gform_pre_submission', 'remove_empty_gaconnector_values');
function remove_empty_gaconnector_values($form)
{
    foreach ($_POST as $key => &$value) {
        if (strpos($value, 'gaconnector_') !== false) {
            // Check if the value contains 'gaconnector_' and set it to an empty string
            $_POST[$key] = '';
        }
    }
};