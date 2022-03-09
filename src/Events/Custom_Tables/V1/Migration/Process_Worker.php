<?php
/**
 * Does the migration and undo operations.
 *
 * @since   TBD
 * @package TEC\Events\Custom_Tables\V1\Migration;
 */

namespace TEC\Events\Custom_Tables\V1\Migration;

use TEC\Events\Custom_Tables\V1\Activation;
use TEC\Events\Custom_Tables\V1\Migration\Reports\Event_Report;
use TEC\Events\Custom_Tables\V1\Migration\Strategies\Single_Event_Migration_Strategy;
use TEC\Events\Custom_Tables\V1\Migration\Strategies\Strategy_Interface;
use TEC\Events\Custom_Tables\V1\Tables\Provider;

/**
 * Class Process_Worker. Handles the migration and undo operations.
 *
 * @since   TBD
 * @package TEC\Events\Custom_Tables\V1\Migration;
 */
class Process_Worker {

	/**
	 * The full name of the action that will be fired to signal one
	 * Event should be migrated, or have its migration previewed.
	 */
	const ACTION_PROCESS = 'tec_events_custom_tables_v1_migration_process';
	/**
	 * The full name of the action that will be fired to signal one
	 * Event should be undone.
	 */
	const ACTION_UNDO = 'tec_events_custom_tables_v1_migration_cancel';
	/**
	 * A reference to the current Events' migration repository.
	 *
	 * @since TBD
	 * @var Events
	 */
	private $events;

	/**
	 * A reference to the migration state object.
	 *
	 * @var State;
	 */
	private $state;

	/**
	 * Process_Worker constructor.
	 *
	 * @since TBD
	 *
	 * @param Events $events A reference to the current Events' migration repository.
	 * @param State $state A reference to the migration state data.
	 */
	public function __construct( Events $events, State $state ) {
		$this->events = $events;
		$this->state = $state;
	}

	/**
	 * Processes an Event migration.
	 *
	 * @since TBD
	 *
	 * @param int  $post_id The post ID of the Evente to migrate.
	 * @param bool $dry_run Whether the migration should commit or just preview
	 *                      the changes.
	 *
	 * @return Event_Report A reference to the migration report object produced by the
	 *                      migration.
	 */
	public function migrate_event( $post_id, $dry_run = false ) {
		// Get our Event_Report ready for the strategy.
		// This is also used in our error catching, so needs to be defined outside that block.
		$event_report = new Event_Report( get_post( $post_id ) );

		// Watch for any errors that may occur and store in our Event_Report.
		set_error_handler(
			function ( $errno, $errstr, $errfile, $errline ) {
				// Delegate to our try/catch handler.
				throw new Migration_Exception( $errstr, $errno );
			}
		);

		// Catch unexpected shutdown.
		$shutdown_function = function () use ( $event_report ) {
			$event_report->migration_failed( 'Unknown error occurred, shutting down.' );
		};
		add_action( 'shutdown', $shutdown_function );

		try {
			// Check if we are still in migration phase.
			if ( ! in_array( $this->state->get_phase(), [
				State::PHASE_PREVIEW_IN_PROGRESS,
				State::PHASE_MIGRATION_IN_PROGRESS
			], true ) ) {
				$event_report->migration_failed( 'Canceled.' );

				return $event_report;
			}

			/**
			 * Filters the migration strategy that should be used to migrate an Event.
			 * Returning an object implementing the TEC\Events\Custom_Tables\V1\Migration\Strategy_Interface
			 * here will prevent TEC from using the default one.
			 *
			 * @since TBD
			 *
			 * @param Strategy_Interface A reference to the migration strategy that should be used.
			 *                          Initially `null`.
			 * @param int  $post_id     The post ID of the Event to migrate.
			 * @param bool $dry_run     Whether the strategy should be provided for a real migration
			 *                          or its preview.
			 */
			$strategy = apply_filters( 'tec_events_custom_tables_v1_migration_strategy', null, $post_id, $dry_run );

			if ( ! $strategy instanceof Strategy_Interface ) {
				$strategy = new Single_Event_Migration_Strategy( $post_id, $dry_run );
			}

			$event_report->start_event_migration();

			// Apply strategy, use Event_Report to flag any pertinent details or any failure events.
			$strategy->apply( $event_report );
			// If no error, mark successful.
			if ( ! $event_report->error ) {
				$event_report->migration_success();
			}

			$post_id = $this->events->get_id_to_process();

			if ( $post_id ) {
				// Enqueue a new (Action Scheduler) action to import another Event.
				$action_id = as_enqueue_async_action( self::ACTION_PROCESS, [ $post_id, $dry_run ] );

				//@todo check action ID here and log on failure.
			}
		} catch ( \Throwable $e ) {
			$event_report->migration_failed( $e->getMessage() );
		} catch ( \Exception $e ) {
			$event_report->migration_failed( $e->getMessage() );
		}
		// Restore error handling.
		restore_error_handler();
		// Remove shutdown hook.
		remove_action( 'shutdown', $shutdown_function );

		// Transition phase.
		if ( $this->events->get_total_events_remaining() === 0
		     && $this->state->is_running()
		     && in_array( $this->state->get_phase(), [
				State::PHASE_MIGRATION_IN_PROGRESS,
				State::PHASE_PREVIEW_IN_PROGRESS
			] ) ) {
			$this->state->set( 'phase', $dry_run ? State::PHASE_MIGRATION_PROMPT : State::PHASE_MIGRATION_COMPLETE );
			$this->state->set( 'migration', 'estimated_time_in_seconds', $this->events->calculate_time_to_completion() );
			$this->state->set( 'complete_timestamp', time() );
			$this->state->save();
		}

		return $event_report;
	}

	/**
	 * Undoes an Event migration.
	 *
	 * @since TBD
	 *
	 * @param array<string, mixed> The metadata we pass to ourselves.
	 *
	 */
	public function undo_event_migration( $meta ) {

		if ( ! isset( $meta['started_timestamp'] ) ) {
			$meta['started_timestamp'] = time();
		}

		$seconds_to_wait  = 60 * 5; // 5 minutes
		$max_time_reached = ( time() - $meta['started_timestamp'] ) > $seconds_to_wait;

		// Are we still processing some events? If so, recurse and wait to do the undo operation.
		if ( ! $max_time_reached && $this->events->get_total_events_in_progress() ) {
			as_enqueue_async_action( self::ACTION_UNDO, [ $meta ] );

			return;
		}
		// @todo Move this to a centralized rollback (in the schema objects, with hooks?)
		// @todo Review - missing anything? Better way?
		do_action( 'tec_events_custom_tables_v1_migration_before_cancel' );

		tribe( Provider::class )->drop_tables();

		// Clear meta values.
		$meta_keys = [
			Event_Report::META_KEY_MIGRATION_LOCK_HASH,
			Event_Report::META_KEY_REPORT_DATA,
			Event_Report::META_KEY_MIGRATION_PHASE,
		];

		/**
		 * Filters the list of post meta keys to be removed during a migration cancel.
		 *
		 * @since TBD
		 *
		 * @param array<string> $meta_keys List of keys to delete.
		 */
		$meta_keys = apply_filters( 'tec_events_custom_tables_v1_delete_meta_keys', $meta_keys );
		foreach ( $meta_keys as $meta_key ) {
			delete_metadata( 'post', 0, $meta_key, '', true );
		}

		// @todo This will just cause the tables to be recreated - we need something to handle create/destroy operations properly...
		delete_transient( Activation::ACTIVATION_TRANSIENT );

		$this->state->set( 'phase', State::PHASE_MIGRATION_PROMPT );
		$this->state->save();

		do_action( 'tec_events_custom_tables_v1_migration_after_cancel' );
	}


}