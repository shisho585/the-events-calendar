<?php
/**
 * Handles the migration UI Ajax requests.
 *
 * While, technically, Action Scheduler code will work using AJAX, this
 * handler will concentrate on AJAX requests from the migraiton UI, not
 * from Action Scheduler.
 *
 * @since   TBD
 *
 * @package TEC\Events\Custom_Tables\V1\Migration;
 */

namespace TEC\Events\Custom_Tables\V1\Migration;

use TEC\Events\Custom_Tables\V1\Migration\Admin\Progress_Modal;
use TEC\Events\Custom_Tables\V1\Migration\Admin\Upgrade_Tab;
use TEC\Events\Custom_Tables\V1\Migration\Reports\Site_Report;

/**
 * Class Ajax.
 *
 * @since   TBD
 * @package TEC\Events\Custom_Tables\V1\Migration;
 */
class Ajax {

	/**
	 * The full name of the action that will be fired following a migration UI
	 * request for a report.
	 *
	 * @since TBD
	 */
	const ACTION_REPORT = 'wp_ajax_tec_events_custom_tables_v1_migration_report';

	/**
	 * The full name of the action that will be fired following a request from
	 * the migration UI to start the migration.
	 *
	 * @since TBD
	 */
	const ACTION_START = 'wp_ajax_tec_events_custom_tables_v1_migration_start';

	/**
	 * The full name of the action that will be fired following a request from
	 * the migration UI to cancel the migration.
	 */
	const ACTION_CANCEL = 'wp_ajax_tec_events_custom_tables_v1_migration_cancel';

	/**
	 * The full name of the action that will be fired following a request from
	 * the migration UI to undo the migration.
	 */
	const ACTION_UNDO = 'wp_ajax_tec_events_custom_tables_v1_migration_undo';


	/**
	 * A reference to the current background processing handler.
	 *
	 * @since TBD
	 *
	 * @var Process
	 */
	private $process;
	/**
	 * A reference to the current progress modal handler.
	 *
	 * @since TBD
	 *
	 * @var Progress_Modal
	 */
	private $progress_modal;

	/**
	 * Ajax constructor.
	 *
	 * since TBD
	 *
	 * @param Process $process A reference to the current background processing handler.
	 */
	public function __construct( Process $process, Progress_Modal $progress_modal ) {
		$this->process        = $process;
		$this->progress_modal = $progress_modal;
	}

	/**
	 * Builds and sends the report in the format expected by the Migration UI JS
	 * component.
	 *
	 * @since TBD
	 *
	 * @return string The JSON-encoded data for the front-end.
	 */
	public function get_report( $echo = true ) {
		// @todo Add pagination?
		$page   = 1;
		$count  = 20;
		$report = Site_Report::build( $page, $count );

		$html = tribe( Upgrade_Tab::class )->get_phase_inner_html();
		$response      = [
			// 'has_changes' => $report->has_changes,
			// @todo remove this hard-coded value used for testing.
			'has_changes' => true,
			'report_html' => $html,
		];

		if ( $echo ) {
			wp_send_json( $response );
			die();
		}

		return wp_json_encode( $response );
	}

	/**
	 * Handles the request from the Admin UI to start the migration and returns
	 * a first report about its progress.
	 *
	 * @since TBD
	 *
	 * @return Site_Report A report about the migration start process.
	 */
	public function start_migration( $echo = true ) {
		// @todo This should have state with the process starting?
		$report = Site_Report::build();
		$this->process->start();

		if ( $echo ) {
			wp_send_json( $report );
		}

		return $report;
	}

	/**
	 * Handles the request from the Admin UI to cancel the migration and returns
	 * a first report about its progress.
	 *
	 * @since TBD
	 *
	 * @return Site_Report A report about the migration cancel process.
	 */
	public function cancel_migration( $echo = true ) {
		// @todo This should have state with the process canceling?
		$report = Site_Report::build();
		$this->process->cancel();

		if ( $echo ) {
			wp_send_json( $report );
		}

		return $report;
	}

	/**
	 * Handles the request from the Admin UI to undo the migration and returns
	 * a first report about its progress.
	 *
	 * @since TBD
	 *
	 * @return Site_Report A report about the migration undo process.
	 */
	public function undo_migration( $echo = true ) {
		// @todo This should have state with the process undoing?
		$report = Site_Report::build();
		$this->process->undo();

		if ( $echo ) {
			wp_send_json( $report );
		}

		return $report;
	}
}