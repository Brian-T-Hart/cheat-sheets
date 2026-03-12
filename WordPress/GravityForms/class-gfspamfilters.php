<?php

#-----------------------------------------------------------------#
#   GRAVITY FORMS
#-----------------------------------------------------------------#

class GFSpamFilters
{
    private $referrer_check_field_labels = array( // Referrer field labels to look for
        'First Click Referrer',
        'Last Click Referrer'
    );
    private $spam_check_field_label = 'spam check'; // Spam check field label
    
    /**
     * Initialize the spam filters
     */
    public function __construct() {
        // runs for all forms

        // check/update post submission data for spam before entry is created
        add_filter( 'gform_pre_submission', [$this, 'check_submission_is_spam'], 10, 3);
        add_action( 'gform_pre_submission', [$this, 'remove_empty_gaconnector_values'] ); 
        add_action( 'gform_pre_submission', [$this, 'ip_pre_submission_handler'] );
        
        // custom validation filters
        add_filter( 'gform_field_validation', [$this, 'filter_words'], 10, 4 );
        add_filter( 'gform_field_validation', [$this, 'cyrillic_greek_validation'], 10, 4 );
        add_filter( 'gform_field_validation', [$this, 'filter_email_results'], 10, 4 );
        add_filter( 'gform_field_validation', [$this, 'validate_comment_field'], 10, 4);
        add_filter( 'gform_field_validation', [$this, 'validate_phone_number'], 10, 4);
        add_filter( 'gform_entry_is_spam', [$this, 'check_submission_is_duplicate'], 10, 3);
    }

    public function check_submission_is_spam($form) {
        // Make sure Gravity Forms is available
        if ( ! class_exists( 'GFAPI' ) ) {
            return null;
        }
        $form_id = $form['id'];
        // Get all forms
        $form = GFAPI::get_form( $form_id );
        $spam_check_field = $this->get_spam_check_field_id($form);
        if (!$spam_check_field) {
            return $form; // No spam check field found, exit early
        }
        $spam_check_set = false;
        $referrer_check_response = $this->check_referrer_is_spam($form);
        if (!empty($referrer_check_response)) {
            $_POST['input_'.$spam_check_field] = $referrer_check_response;
            $spam_check_set = true;
        }
        if (!$spam_check_set) { // dont override if already set by referrer check
            $comment_check_response = $this->check_comment_is_spam($form);
            if (!empty($comment_check_response)) {
                $_POST['input_'.$spam_check_field] = $comment_check_response;
                !$spam_check_set = true;
            }
        }
        return $form;
    }

    /* REFERRER AND COMMENT FIELD SPAM CHECK FUNCTIONS */
    private function check_referrer_is_spam ( $form ) {
        $response = '';
        // Loop through each field in the form
        foreach ( $form['fields'] as $field ) {
            if ( $field instanceof GF_Field ) {
                $label = trim( strtolower( $field->label ) );
                if ( in_array( $label, array_map( 'strtolower', $this->referrer_check_field_labels ) ) ) {
                    $value = rgar( $_POST, 'input_' . $field->id );
                    $blocked_referrers = array(
                        'syndicatedsearch.goog'
                    );
                    foreach ( $blocked_referrers as $blocked_referrer ) {
                        if ( strpos( strtolower( $value ), $blocked_referrer ) !== false ) {
                            $response = "Blocked referrer: {$blocked_referrer}";
                            break; // No need to check further1
                        }
                    }
                }
            }
            if ( !empty( $response) ) {
                break; // Break loop if already found an error
            }
        }
        return $response;
    }

    private function check_comment_is_spam ( $form ) {
        $response = '';
        // Loop through each field in the form
        foreach ( $form['fields'] as $field ) {
            if ( $field instanceof GF_Field ) {
                if ( $field->type == 'textarea' ) {
                    $value = rgar( $_POST, 'input_' . $field->id );
                    $message_check = $this->check_message($value);
                    if ($message_check !== false) {
                        $response = $message_check;
                        break; // No need to check further
                    }
                }
            }
        }
        return $response;
    }

    /**
     * Remove empty gaconnector values from the $_POST array before submission.
     */
    public function remove_empty_gaconnector_values($form) {
        foreach ($_POST as $key => &$value) {
            // Check if the value contains 'gaconnector_' and set it to an empty string
            if (strpos($value, 'gaconnector_') !== false) {
                $_POST[$key] = '';
            }
        }
    }

     /* Block submissions from specific IP addresses */
    public function ip_pre_submission_handler( $form ) {
        $blocked_ips = array('85.209.11.20', '85.209.11.117');
        $ip_address = $_SERVER['REMOTE_ADDR'];
        if (in_array($ip_address, $blocked_ips)) {
            die( "ERROR: Please contact the webmaster." );
        }
    }

    /**
     * Validate text fields against a list of stop words.
     */
    public function filter_words( $result, $value, $form, $field ) {
        // Only for Single Line Text and Paragraph fields.
        if ( $field->type == 'text' || $field->type == 'textarea' ) {
            if ( $result['is_valid'] ) {
                $stop_words = array( // List of words to not allow in lowercase. Can this keyword list be moved to an external file or backend settings field?
                    'viagra',
                    'porn',
                    'fuck',
                    'sidenafil',
                    'cialis',
                    'seo',
                    'sex',
                    'rank',
                    'ranking',
                    'leads',
                    'trial',
                    'software',
                    'virus',
                    'viruses',
                    'malware',
                    'robertked',
                    'venture capital',
                    'skype',
                    'whatsapp',
                    'feedback forms',
                    'feedbackform',
                    'file sharing',
                    'online security',
                    'spam',
                    'funding',
                    'href=',
                    'url=',
                    'instagram',
                    'crypto',
                    'telegram',
                    'youtube',
                    'yandex',
                    'godaddy',
                    'mtskheta',
                    'robertwheme',
                    'xxx',
                    'services directory',
                    'online presence',
                    'brand presence',
                    'professional website',
                    'current website',
                    'new website',
                    'business website',
                    'website service',
                    'janitorial',
                    'broker',
                    'wallet',
                    'financing',
                    'freeaireports',
                    'adcreative',
                    'guest blog',
                    'workhomelife',
                    'dog harness',
                    'share an article',
                    'tinyurl',
                    'blessings',
                    'johnwick',
                    'visser',
                    'fdg',
                    'fsdg',
                    'looking forward to your response',
                    'virtual assistant',
                    'http',
                    'debts',
                    'collection',
                    'desperate',
                    'waiting for your response',
                    'local cleaning company',
                    'cleaning quote',
                    'optout',
                    'sdf',
                    'screenshot',
                    'your business website',
                    'your website',
                    'earliest convenience',
                    'earliest opportunity',
                    'please contact me',
                    'would like to hear',
                    "your advertisement",
                    'saw your ad',
                    'noticed your ad',
                    'like to learn more',
                    "optout",
                    'unsubscribe',
                    'want to know more',
                    'get in touch with me',
                    'like to get in touch',
                    'please contact me by email',
                    'send some information',
                    'please send me an email when you get the chance.',
                    'prompt response',
                    'want to know more'
                );
            
                // Stop Words Counter.
                $stop_words_detected = 0;
    
                // Check field value for Stop Words.
                foreach ( $stop_words as $stop_word ) {
                    if ( strpos( strtolower( $value ), $stop_word ) !== false ) {
                        $stop_words_detected++;
                        GFCommon::log_debug( __METHOD__ . "(): Increased Stop Words counter for field id {$field->id}. Stop Word: {$stop_word}" );
                    }
                }
    
                if ( $stop_words_detected > 0 ) {
                    GFCommon::log_debug( __METHOD__ . "(): {$stop_words_detected} Stop words detected." );
                    $result['is_valid'] = false;
                    $result['message']  = 'Sorry, there is a problem with your message. Please try again.';
                }
            }
    
        }
    
        return $result;
    }

    /**
     * Validate text fields against Cyrillic and Greek characters.
     */
    public function cyrillic_greek_validation( $result, $value, $form, $field ) {
        GFCommon::log_debug( __METHOD__ . '(): running for field type ' . $field->type );
        if ( 'text' !== $field->type && 'textarea' !== $field->type ) {
            return $result;
        }

        // Cyrillic & Greek check.
        $cyrillic = preg_match( '/[\p{Cyrillic}]/u', $value);
        $greek = preg_match( '/[\p{Greek}]/u', $value);

        if ( $result['is_valid'] && ( $cyrillic || $greek ) ) {
            GFCommon::log_debug( __METHOD__ . '(): Cyrillic or Greek detected!' );
            $result['is_valid'] = false;
            $result['message'] = 'Sorry, there is a problem with your message. Please try again.';
        }

        return $result;
    }
  
    /**
     * Validate email field against specific domains and keywords.
     */
    public function filter_email_results( $result, $value, $form, $field ) {
        if ($field->type !== 'email') {
            return $result; // Only apply to email fields
        }
        if ( $result['is_valid'] ) {
            $stop_words = array( // List of words to not allow in lowercase.
                '.ru',
                '.store',
                'godaddy',
                'jourrapide',
                'fdg.com',
                'clubemp',
                'jordan.visser',
                'clicki.ai'
            );

            // Stop Words Counter.
            $stop_words_detected = 0;

            // Check field value for Stop Words.
            foreach ( $stop_words as $stop_word ) {
                if ( strpos( strtolower( $value ), $stop_word ) !== false ) {
                    $stop_words_detected++;
                    GFCommon::log_debug( __METHOD__ . "(): Increased Stop Words counter for field id {$field->id}. Stop Word: {$stop_word}" );
                }
            }

            if ( $stop_words_detected > 0 ) {
                GFCommon::log_debug( __METHOD__ . "(): {$stop_words_detected} Stop words detected." );
                $result['is_valid'] = false;
                $result['message']  = 'Sorry, there is a problem with your email. Please try again.';
            }
        }
        return $result;
    }
    
    /* COMMENT FIELD SPAM CHECK FUNCTIONS */
    public function validate_comment_field ($result, $value, $form, $field) {
        // Skip check if value is empty
        if (empty($value)) {
            return $result;
        }
        // Only apply to textarea fields
        if ($field->type !== 'textarea') {
            return $result;
        }

        // Example: Reject submissions with too short comments
        $word_count = str_word_count(strip_tags($value));
        if ((strlen($value) < 6) || ($word_count < 2)) {
            $result['is_valid'] = false;
            $result['message']  =  "Please describe your issue in more detail.";
        }
        return $result;
    }

    /* PHONE NUMBER VALIDATION */
    public function validate_phone_number ($result, $value, $form, $field) {
        // Skip check if value is empty
        if (empty($value)) {
            return $result;
        }
        // Only apply this validation to phone fields
        if ($field->type !== 'phone') {
            return $result;
        }
        // Validate US phone number format

        // Strip non-digit characters
        $digits = preg_replace('/\D/', '', $value);

        // Remove leading '1' if present (country code)
        if (strlen($digits) === 11 && $digits[0] === '1') {
            $digits = substr($digits, 1);
        }

        // Should be exactly 10 digits
        if (strlen($digits) !== 10) {
            $result['is_valid'] = false;
            $result['message'] = 'Please enter a valid 10-digit US phone number.';
            return $result;
        }

        // Extract parts
        $area_code   = substr($digits, 0, 3);
        $prefix = substr($digits, 3, 3);
        $line   = substr($digits, 6, 4);

        // Validate area code and prefix: must not start with 0 or 1
        if ($area_code[0] < '2' || $prefix[0] < '2') {
            $result['is_valid'] = false;
            $result['message'] = 'Please enter a valid US phone number with a valid area code and prefix.';
            return $result;
        }

        // Optional: Reject known fake numbers
        $invalidNumbers = [
            '0000000000',
            '1234567890',
            '9999999999',
        ];
        if (in_array($digits, $invalidNumbers)) {
            $result['is_valid'] = false;
            $result['message'] = 'That phone number appears invalid. Please try again.';
            return $result;
        }

        return $result;
    }

    /* Check if the submission is spam based on custom checks */
    public function check_submission_is_duplicate($is_spam, $form, $entry) {
        $duplicate_check_result = $this->check_duplicates_last_24h_dynamic_spam($form, $entry);
        if ( !$duplicate_check_result['is_valid'] ) {
            $is_spam = true;
            GFCommon::log_debug(__METHOD__ . "(): Form ID {$form['id']} - Entry ID {$entry['id']} - Failed Duplicate Check - " . $duplicate_check_result['message']);
            GFCommon::set_spam_filter( rgar( $form, 'id' ), 'Duplicate Check', $duplicate_check_result['message'] );
            $spam_check_field_id = $this->get_spam_check_field_id($form);
            if ($spam_check_field_id && $spam_check_field_id > 0) GFAPI::update_entry_field( $entry['id'], $spam_check_field_id, "Duplicate submission detected" );
        }
        return $is_spam;
    }

    /* DUPLICATE SUBMISSION CHECK FUNCTIONS */

    public function check_duplicates_last_24h_dynamic($result, $value, $form, $field, $entry_id) {
        // Skip check if value is empty
        if (empty($value)) {
            return $result;
        }

        $form_id  = (int) $form['id'];
        $field_id = (int) $field->id;

        $field_types_to_check = ['email', 'phone'];

        // Only apply to email and phone fields
        if (!in_array($field->type, $field_types_to_check)) {
            return $result;
        }

        // Time: 24 hours ago
        $cutoff = gmdate('Y-m-d H:i:s', strtotime('-24 hours'));

        // Build search criteria
        $search_criteria = [
            'start_date' => $cutoff,
            'field_filters' => [
                [
                    'key'   => (string)$field_id,
                    'value' => $value,
                ],
                [
                    'key'   => 'status',
                    'value' => 'active', // Only non-spam entries
                ]
            ]
        ];

        // Only need to find 1 match that is not the current entry
        $paging = [
            'offset'    => 0,
            'page_size' => 3
        ];

        $entries = GFAPI::get_entries($form_id, $search_criteria, null, $paging);

        if (!empty($entries) && is_array($entries)) {
            foreach ($entries as $entry_found) {
                if ( (int)$entry_found['id'] !== (int)$entry_id ) {
                    $result['is_valid'] = false;
                    $result['message']  = "Duplicate entry id {$entry_found['id']} found for {$field->type} field ID {$field_id} with value '{$value}' in the last 24 hours.";
                    //$_POST['gf_marked_spam_reason'] = "Marked as spam - Duplicate entry found for field ID {$field_id} with value '{$value}' in the last 24 hours.";
                    GFCommon::log_debug(__METHOD__ . "(): Form ID {$form['id']} - Duplicate entry id {$entry_found['id']} found for {$field->type} field ID {$field_id} with value '{$value}' in the last 24 hours.");
                    break; // No need to check further
                }
            }
            
        }
        return $result;
    }
    /* Check for duplicates in the last 24 hours */
     public function check_duplicates_last_24h_dynamic_spam($form, $entry) {
        $result = array( 'is_valid' => true, 'message' => '', 'field_id' => 0 );
        $form_id  = (int) $form['id'];
        foreach ( $form['fields'] as $field ) {
            if ( $field instanceof GF_Field ) {
                $field_id = (int) $field->id;
                $value = rgar( $entry, ''.$field_id );
                $result = $this->check_duplicates_last_24h_dynamic($result, $value, $form, $field, $entry['id']);
            }
            if ( !$result['is_valid'] ) {
                break; // Break loop if already marked as invalid
            }
        }
        return $result;
    }

    /* PRIVATE HELPERS */

    // Check for invalid messages
    private function check_message($text) {
        $length = strlen($text);
        if ($length < 6) return "Message less than 6 characters.";

        // Reject if too few words
        $word_count = str_word_count(strip_tags($text));
        if ($word_count < 2) return "Message has too few words.";

        // Reject if too many consonants in a row
        $consonant_ratio_fail = $this->has_high_consonant_ratio($text);
        if ($consonant_ratio_fail) return "Failed consonant ratio check.";

        // Reject if it contains long strings of symbols
        if (preg_match('/[^\w\s]{4,}/u', $text)) return "Failed long symbol string check.";

        // Digit ratio too high (e.g., 40% or more of the characters are digits)
        $digit_ratio_fail = $this->has_high_digit_ratio($text);
        if ($digit_ratio_fail) return "Failed digit ratio check.";

        return false;
    }
    
    private function has_high_consonant_ratio($text) {
        $text = strtolower($text); // Remove non-letters
        $text = preg_replace('/[^a-z]/', '', $text);

        if (strlen($text) < 4) return false; // Too short to judge

        $vowels = preg_match_all('/[aeiou]/', $text);
        $consonants = preg_match_all('/[bcdfghjklmnpqrstvwxyz]/', $text);

        // Avoid division by zero
        if ($vowels === 0) $vowels = 1;

        $ratio = $consonants / $vowels;

        return $ratio > 4; // Adjust threshold (4:1 = very unnatural)
    }

    private function has_high_digit_ratio($text) {
        $length = strlen($text);
        if ($length == 0) return false; // Avoid division by zero

        $digitCount = preg_match_all('/\d/', $text);
        $digitRatio = $digitCount / $length;

        return $digitRatio > 0.5; // Adjust threshold (e.g., 50% or more of the characters are digits)
    }
    /* Get the field ID of the spam check field by its label */
    private function get_spam_check_field_id($form) {
        // Loop through each field in the form
        foreach ( $form['fields'] as $field ) {
            if ( $field instanceof GF_Field ) {
                $label = trim( strtolower( $field->label ) );
                if ( $label == $this->spam_check_field_label ) {
                    return $field->id;
                }
            }
        }
        return false;
    }
}

//if gravity forms is active, initialize the spam filters
if (class_exists('GFForms')) $gform_spam_filters = new GFSpamFilters();