<?php

// Add a timestamp to the form submission to detect fast submissions
add_action('gform_enqueue_scripts', 'custom_enqueue_submission_timer_script', 10, 2);
function custom_enqueue_submission_timer_script($form, $is_ajax) {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form#gform_<?php echo $form['id']; ?>');
            if (!form) return;
            
            // Prevent duplicate input
            if (form.querySelector('input[name="gf_submission_start"]')) return;

            // Create a hidden input to store the submission start time
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'gf_submission_start';
            input.className = 'gform_hidden';
            input.value = <?php echo time(); ?>; // Set the current timestamp
            form.appendChild(input);
        });
    </script>
    <?php
}

// Add a filter to check the submission time and mark it as spam if too fast
add_filter('gform_entry_is_spam', 'mark_fast_submissions_as_spam', 10, 3);
function mark_fast_submissions_as_spam($is_spam, $form, $entry) {
    if (isset($_POST['gf_submission_start'])) {
        $start_time = intval($_POST['gf_submission_start']);
        $now = time();
        $delta = $now - $start_time;

        if ($delta < 3) {
            GFCommon::log_debug(__METHOD__ . "(): Submission marked as spam (Submitted in {$delta}s)");
            $_POST['gf_marked_spam_reason'] = "Marked as spam: Submitted in {$delta} seconds.";
            return true; // Mark as spam
        }
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
