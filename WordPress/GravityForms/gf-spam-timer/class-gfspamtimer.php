<?php

class GFSpamTimer
{
    private static $minimum_time = 5; // Minimum time in seconds to consider a valid submission
    private static $secret_key = 'ny3vNfD6tU0J'; // Secret key to validate the submission

    /**
     * Initialize the spam timer.
     */
    public static function init()
    {
        add_action('gform_register_init_scripts', [__CLASS__, 'add_custom_fields_to_form'], 1);
        add_filter('gform_entry_is_spam', [__CLASS__, 'mark_fast_submissions_as_spam'], 11, 3);
        add_action('gform_entry_post_save', [__CLASS__, 'run_post_save_checks'], 12, 2);
    }

    /**
     * Add custom fields to the form.
     */
    public static function add_custom_fields_to_form($form)
    {
        $form_id = $form['id'];
        $secret_key = self::$secret_key;

        if (version_compare(GFForms::$version, '2.9.0', '>=')) {
            $script = <<<EOD
                window.gf_st_custom_1 = new Date().getTime();
                console.log('gf_st_custom_1 set to ' + window.gf_st_custom_1);
				gform.utils.addAsyncFilter('gform/submission/pre_submission', async (data) => {
                    const currentTime = new Date().getTime();
                    console.log('Current time: ' + currentTime);
                    
				    const input1 = document.createElement('input');
				    input1.type = 'hidden';
				    input1.name = 'gf_st_custom_1';
				    input1.value = JSON.stringify({time: window.gf_st_custom_1, secret: '{$secret_key}'});
				    input1.setAttribute('autocomplete', 'new-password');
				    data.form.appendChild(input1);

                    const input2 = document.createElement('input');
				    input2.type = 'hidden';
				    input2.name = 'gf_st_custom_2';
				    input2.value = JSON.stringify({time: currentTime, secret: '{$secret_key}'});
				    input2.setAttribute('autocomplete', 'new-password');
				    data.form.appendChild(input2);
				
				    return data;
				});
EOD;
        } else {
            $autocomplete = RGFormsModel::is_html5_enabled() ? ".attr( 'autocomplete', 'new-password' )\n\t\t" : '';

            $script = <<<EOD
                window.gf_st_custom_1 = new Date().getTime();
                console.log('using jQuerygf_st_custom_1 set to ' + window.gf_st_custom_1);
                
				jQuery( "#gform_{$form_id}" ).on( 'submit', function( event ) {
                    const currentTime = new Date().getTime();
                    console.log('Current time: ' + currentTime);
					jQuery( '<input>' )
						.attr( 'type', 'hidden' )
						.attr( 'name', 'gf_st_custom_1' )
						.attr( 'value', JSON.stringify({time: window.gf_st_custom_1, secret: '{$secret_key}'}) )
						$autocomplete.appendTo( jQuery( this ) );

                    jQuery( '<input>' )
                        .attr( 'type', 'hidden' )
                        .attr( 'name', 'gf_st_custom_2' )
                        .attr( 'value', JSON.stringify({time: currentTime, secret: '{$secret_key}'}) )
                        $autocomplete.appendTo( jQuery( this ) );
				} );
EOD;
        }

        GFFormDisplay::add_init_script($form_id, 'gf-spam-timer', GFFormDisplay::ON_PAGE_RENDER, $script);
    }

    /**
     * Check the submission time and mark it as spam if submitted too quickly
     */
    public static function mark_fast_submissions_as_spam($is_spam, $form, $entry)
    {
        // Mark as spam if the custom fields are not set
        if (!isset($_POST['gf_st_custom_1']) || !isset($_POST['gf_st_custom_2'])) {
            GFCommon::log_debug(__METHOD__ . "(): Form ID {$form['id']} - Custom fields are missing");
            $_POST['gf_marked_spam_reason'] = "Marked as spam - Custom fields are missing";
            return true;
        }

        $gf_st_1 = json_decode(stripslashes($_POST['gf_st_custom_1']), true);
        $gf_st_2 = json_decode(stripslashes($_POST['gf_st_custom_2']), true);

        // Mark as spam if the secret key values are not valid
        if (!isset($gf_st_1['secret']) || !isset($gf_st_2['secret']) || $gf_st_1['secret'] !== self::$secret_key || $gf_st_2['secret'] !== self::$secret_key) {
            GFCommon::log_debug(__METHOD__ . "(): Form ID {$form['id']} - Secret key mismatch.");
            $_POST['gf_marked_spam_reason'] = "Marked as spam - Secret key mismatch.";
            return true;
        }

        // Mark as spam if the time values are not valid
        if (!isset($gf_st_1['time']) || !isset($gf_st_2['time']) || !is_numeric($gf_st_1['time']) || !is_numeric($gf_st_2['time'])) {
            GFCommon::log_debug(__METHOD__ . "(): Form ID {$form['id']} - Invalid time values.");
            $_POST['gf_marked_spam_reason'] = "Marked as spam - Invalid time values.";
            return true;
        }

        // Calculate the time taken to submit the form in seconds
        $time_to_submit = round(($gf_st_2['time'] - $gf_st_1['time']) / 1000);

        // Save it in $_POST
        $_POST['gf_submission_duration'] = $time_to_submit;

        // Store the submission duration in entry metadata
        // gform_update_meta( $entry['id'], 'submission_duration', $time_to_submit );

        // Mark as spam if submitted too quickly
        if ($time_to_submit < self::$minimum_time) {
            GFCommon::log_debug(__METHOD__ . "(): Form ID {$form['id']} - Submission marked as spam for invalid time ({$time_to_submit} seconds).");
            $_POST['gf_marked_spam_reason'] = "Marked as spam: Submitted in {$time_to_submit} seconds.";
            return true;
        }

        return $is_spam;
    }

    // Add a note to the entry when marked as spam
    public static function run_post_save_checks($entry, $form)
    {
        // Store the submission duration in entry metadata
        if (!empty($_POST['gf_submission_duration']) && is_numeric($_POST['gf_submission_duration'])) {
            gform_update_meta($entry['id'], 'gf_submission_duration', intval($_POST['gf_submission_duration']));
        }

        // Add a note if marked as spam
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
