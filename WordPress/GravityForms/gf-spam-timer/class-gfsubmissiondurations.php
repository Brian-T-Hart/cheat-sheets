<?php
/**
 * Class GF_Submission_Durations
 * Retrieves Gravity Forms submission durations via AJAX (GET or POST).
 * Examples:
 *   https://form-security.local/wp-admin/admin-ajax.php?action=get_submission_durations&form_id=1&format=json
 *   https://form-security.local/wp-admin/admin-ajax.php?action=get_submission_durations&form_id=1&format=html
 */

if ( ! class_exists( 'GF_Submission_Durations' ) ) {

	class GF_Submission_Durations {

		protected $form_id;
		protected $require_login;

		public function __construct( $form_id = 1, $require_login = true ) {
			$this->form_id = absint( $form_id );
			$this->require_login = $require_login;

			add_action( 'wp_ajax_get_submission_durations', [ $this, 'ajax_get_submission_durations' ] );
			add_action( 'wp_ajax_nopriv_get_submission_durations', [ $this, 'ajax_get_submission_durations' ] );
		}

		/**
		 * AJAX handler to get submission durations
		 */
		public function ajax_get_submission_durations() {

			// Use $_REQUEST so GET or POST both work
			$params = wp_unslash( $_REQUEST );

			// Security check
			if ( $this->require_login && ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( [ 'message' => 'Unauthorized' ], 403 );
			}

			// Get form ID from URL or use default
			$form_id = isset( $params['form_id'] ) ? absint( $params['form_id'] ) : $this->form_id;

			// Fetch entries from the database
			$search_criteria = ['status' => 'active'];
			$sorting = [ 'key' => 'id', 'direction' => 'DESC' ];
			$paging = [ 'offset' => 0, 'page_size' => 200 ];

			$entries = GFAPI::get_entries( $form_id, $search_criteria, $sorting, $paging );

			$durations = [];
			foreach ( $entries as $entry ) {
				$duration = gform_get_meta( $entry['id'], 'gf_submission_duration' );
				if ( $duration ) {
					$durations[] = [
						'entry_id'     => $entry['id'],
						'duration_seconds'  => intval( $duration ),
						'date_created' => $entry['date_created'],
					];
				}
			}

			// Calculate average duration in seconds
			$average = $durations ? array_sum( wp_list_pluck( $durations, 'duration_seconds' ) ) / count( $durations ) : 0;

			// Optional HTML table view
			$format = isset( $params['format'] ) && $params['format'] === 'html' ? 'html' : 'json';

			if ( $format === 'html' ) {
				$this->render_html( $form_id, $durations, $average );
				exit;
			}

			// JSON output
			wp_send_json_success( [
				'form_id'     => $form_id,
				'count'       => count( $durations ),
				'average_seconds'  => round( $average ),
				'durations'   => $durations,
			] );
		}

		/**
		 * Render submission durations as an HTML table
		 */
		private function render_html( $form_id, $durations, $average ) {
			header( 'Content-Type: text/html; charset=utf-8' );

			$html = '<h2>Submission Durations for Form ID ' . esc_html( $form_id ) . '</h2>';
			
			if ( empty( $durations ) ) {
				$html .= '<p>No duration data found.</p>';
				echo $html;
				return;
			}
			
			$html .= '<p><strong>Average:</strong> ' . round( $average ) . ' seconds</p>';
			$html .= '<table border="1" cellpadding="5" cellspacing="0">';
			$html .= '<tr><th>Entry ID</th><th>Duration (ms)</th><th>Date Created</th></tr>';

			foreach ( $durations as $row ) {
				$html .= '<tr>';
				$html .= '<td>' . esc_html( $row['entry_id'] ) . '</td>';
				$html .= '<td>' . esc_html( $row['duration_seconds'] ) . '</td>';
				$html .= '<td>' . esc_html( $row['date_created'] ) . '</td>';
				$html .= '</tr>';
			}
			$html .= '</table>';
			echo $html;
		}
	}
}

if ( class_exists( 'GFAPI' ) ) {
    $submission_durations = new GF_Submission_Durations( 1, true );
}