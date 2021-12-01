<?php
/**
 * The Events Calendar Template Tags
 *
 * Display functions (template-tags) for use in WordPress templates.
 *
 * @deprecated 5.0.0 ViewsV1Removal: v1 only.
 * @todo ViewsV1Removal: Remove 6.0.0.
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( class_exists( 'Tribe__Events__Main' ) ) {

	/**
	 * Retrieves the posts used in the List Widget loop.
	 *
	 * @return array WP_Posts of the retrieved events.
	 **/
	function tribe_get_list_widget_events() {
		return apply_filters( 'tribe_get_list_widget_events', Tribe__Events__List_Widget::$posts );
	}
}
