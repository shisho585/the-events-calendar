<?php
use Tribe\Events\Admin\Settings as Plugin_Settings;

/** @var \Tribe__Events__Aggregator__Settings $settings */
$settings          = tribe( 'events-aggregator.settings' );
$global_limit_type = tribe_get_option( 'tribe_aggregator_default_import_limit_type', 'count' );

if ( 'no_limit' === $global_limit_type ) {
	return;
}

$global_limit_strings = $settings->get_import_limit_count_options();
$global_limit_option  = tribe_get_option( 'tribe_aggregator_default_import_limit_number', $settings->get_import_limit_count_default() );
$global_limit_message = esc_html(
	sprintf(
		__(
			'Event Aggregator will try to fetch %s events starting from the current date or the specified date;',
			'the-events-calendar'
		),
		$global_limit_strings[ $global_limit_option ]
	)
);

if ( 'range' === $global_limit_type ) {
	$global_limit_strings = $settings->get_import_range_options( false );
	$global_limit_option  = tribe_get_option( 'tribe_aggregator_default_import_limit_range', $settings->get_import_range_default() );
	$global_limit_message = esc_html(
		sprintf(
			__(
				'Event Aggregator will try to fetch events starting within the next %s from the current date or the specified date;',
				'the-events-calendar'
			),
			$global_limit_strings[ $global_limit_option ]
		)
	);
}

$import_limit_link    = esc_url( tribe( Plugin_Settings::class )->get_url( [ 'tab' => 'imports#tribe-field-tribe_aggregator_default_import_limit_type' ] ) );
$import_limit_message = $global_limit_message . ' ' . sprintf( '<a href="%s" target="_blank">%s</a> ', $import_limit_link, esc_html__( 'you can modify this setting here.', 'the-events-calendar' ) );
$main = Tribe__Events__Main::instance();
?>

<div class="tribe-dependent" data-depends="#tribe-ea-field-origin" data-condition-not-empty data-condition-relation="and" data-condition-not='["url","eventbrite"]'>
	<p><?php echo $import_limit_message; ?></p>
	
	<div class="tec-admin-ea-help-message">
		<?php esc_html_e( 'Need assistance? You can find more information here:', 'the-events-calendar' ); ?>

		<img
			class="tec-admin-ea-help-message__icon"
			src="<?php echo esc_url( tribe_resource_url( 'images/icons/tec-horns.svg', false, null, $main ) ); ?>"
			alt="<?php esc_attr_e( 'The Events Calendar logo', 'the-events-calendar' ); ?>"
		/>
		<a href="https://evnt.is/1bam" target="_blank" rel="noopener noreferrer">
			<?php esc_html_e( 'Importing Calendar Data From a CSV File', 'the-events-calendar' ); ?>
		</a>

		<img
			class="tec-admin-ea-help-message__icon"
			src="<?php echo esc_url( tribe_resource_url( 'images/icons/tec-horns.svg', false, null, $main ) ); ?>"
			alt="<?php esc_attr_e( 'The Events Calendar logo', 'the-events-calendar' ); ?>"
		/>
		<a href="https://evnt.is/1ban" target="_blank" rel="noopener noreferrer">
			<?php esc_html_e( 'CSV File Formatting and Examples ', 'the-events-calendar' ); ?>
		</a>
	</div>
</div>
