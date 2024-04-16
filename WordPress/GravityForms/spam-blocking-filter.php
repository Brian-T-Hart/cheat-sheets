add_filter( 'gform_field_validation_1', 'filter_words', 10, 4 ); // applies to all fields for form id 1
add_filter( 'gform_field_validation_1', 'cyrillic_greek_validation', 10, 4 ); // applies to all fields for form id 1
add_filter( 'gform_field_validation_1_2', 'filter_email_results', 10, 4 ); // applies to form 1 field ID 2 only

function filter_words( $result, $value, $form, $field ) {
	GFCommon::log_debug( __METHOD__ . '(): Running...' );
	// Only for Single Line Text and Paragraph fields.
	if ( $field->type == 'text' || $field->type == 'textarea' ) {
  
		if ( $result['is_valid'] ) {
			$stop_words = array( // List of words to not allow in lowercase.
				'viagra',
				'porn',
				'sidenafil',
				'cialis',
				'seo',
				'sex',
				'rank',
				'rankings',
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
				'robertwheme'
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

function cyrillic_greek_validation( $result, $value, $form, $field ) {
	GFCommon::log_debug( __METHOD__ . '(): running for field type ' . $field->type );
	if ( 'text' !== $field->type && 'textarea' !== $field->type ) {
		GFCommon::log_debug( __METHOD__ . '(): No text or paragraph field.' );
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
  
function filter_email_results( $result, $value, $form, $field ) {
	if ( $result['is_valid'] ) {
		$stop_words = array( // List of words to not allow in lowercase.
			'.ru',
			'.store',
			'godaddy'
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

add_action( 'gform_pre_submission', 'ip_pre_submission_handler'); // blocks submission of all forms if a matching IP
function ip_pre_submission_handler( $form ) {
	$blocked_ips = array('85.209.11.20', '85.209.11.117');
	$ip_address = $_SERVER['REMOTE_ADDR'];
	if (in_array($ip_address, $blocked_ips)) {
		die( "ERROR: Please contact the webmaster." );
	}
}