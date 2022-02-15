<?php
/**
 * Attaches the correct WP_Query modifier to a WP_Query depending on its nature.
 *
 * @since   TBD
 *
 * @package TEC\Events\Custom_Tables\V1\WP_Query\Monitors
 */

namespace TEC\Events\Custom_Tables\V1\WP_Query\Monitors;

use Countable;
use TEC\Events\Custom_Tables\V1\WP_Query\Custom_Tables_Query;
use TEC\Events\Custom_Tables\V1\WP_Query\Modifiers\Events_Only_Modifier;
use TEC\Events\Custom_Tables\V1\WP_Query\Modifiers\Events_Series_Relationship_Modifier;
use WP_Query;

/**
 * Class Monitor
 *
 * @since   TBD
 *
 * @package TEC\Events\Custom_Tables\V1\WP_Query\Monitors
 */
class WP_Query_Monitor implements Countable {
	use Query_Monitor;

	/**
	 * A list of possible modifiers implementations.
	 *
	 * @since TBD
	 *
	 * @var array<string>
	 */
	private $implementations = [
		Events_Only_Modifier::class,
		Events_Series_Relationship_Modifier::class,
	];

	/**
	 * Returns the flag property that will be set on a `WP_Query` instance to indicate it should
	 * be ignored by the Monitor.
	 *
	 * @since TBD
	 *
	 * @return string The name of the flag property that will be set on a `WP_Query` object to indicate it
	 *                should be ignored by the Monitor.
	 */
	public static function ignore_flag() {
		return 'icaltec_ignore';
	}

	/**
	 * Whether the monitor applies to the Query or not.
	 *
	 * This monitor will only apply to non-custom tables Queries.
	 *
	 * @since TBD
	 *
	 * @param WP_Query|null $query A reference to the WP Query object
	 *                             to check.
	 *
	 * @return bool Whether this Monitor should apply to the query or not.
	 */
	private function applies_to_query( $query = null ) {
		return $query instanceof WP_Query && ! $query instanceof Custom_Tables_Query;
	}
}
