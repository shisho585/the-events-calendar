<?php
/**
 * View: Day View
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/day.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version 4.9.11
 *
 * @var array    $events               The array containing the events.
 * @var string   $rest_url             The REST URL.
 * @var string   $rest_nonce           The REST nonce.
 * @var bool     $disable_event_search Boolean on whether to disable the event search.
 * @var string[] $container_classes    Classes used for the container of the view.
 */

$header_classes = [ 'tribe-events-header' ];
if ( empty( $disable_event_search ) ) {
	$header_classes[] = 'tribe-events-header--has-event-search';
}

?>
<div
	<?php tribe_classes( $container_classes ); ?>
	data-js="tribe-events-view"
	data-view-rest-nonce="<?php echo esc_attr( $rest_nonce ); ?>"
	data-view-rest-url="<?php echo esc_url( $rest_url ); ?>"
	data-view-manage-url="<?php echo esc_attr( $should_manage_url ); ?>"
>
	<div class="tribe-common-l-container tribe-events-l-container">
		<?php $this->template( 'components/loader', [ 'text' => __( 'Loading...', 'the-events-calendar' ) ] ); ?>

		<?php $this->template( 'components/data' ); ?>

		<?php $this->template( 'components/before' ); ?>

		<header <?php tribe_classes( $header_classes ); ?>>
			<?php $this->template( 'components/messages' ); ?>

			<?php $this->template( 'components/breadcrumbs' ); ?>

			<?php $this->template( 'components/events-bar' ); ?>

			<?php $this->template( 'day/top-bar' ); ?>
		</header>

		<?php $this->template( 'components/filter-bar' ); ?>

		<div class="tribe-events-calendar-day">

			<?php foreach ( $events as $event ) : ?>
				<?php $this->setup_postdata( $event ); ?>

				<?php $this->template( 'day/type-separator', [ 'event' => $event ] ); ?>
				<?php $this->template( 'day/time-separator', [ 'event' => $event ] ); ?>
				<?php $this->template( 'day/event', [ 'event' => $event ] ); ?>

			<?php endforeach; ?>

		</div>

		<?php $this->template( 'day/nav' ); ?>

		<?php $this->template( 'components/ical-link' ); ?>

		<?php $this->template( 'components/after' ); ?>

	</div>

</div>

<?php $this->template( 'components/breakpoints' ); ?>
