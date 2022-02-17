<div class="tec-ct1-upgrade__row">
	<div class="image-container">
		<?php // @todo add the correct screenshot here. ?>
		<img class="screenshot" src="<?php echo esc_url( plugins_url( 'src/resources/images/upgrade-views-screenshot.png', TRIBE_EVENTS_FILE ) ); ?>" alt="<?php esc_attr_e( 'screenshot of updated calendar views', 'the-events-calendar' ); ?>" />
	</div>

	<div class="content-container">
		<h3>
			<?php echo $logo; ?>
			<?php esc_html_e( 'Migration complete!', 'the-events-calendar' ); ?>
		</h3>

		<p>
			<?php // @todo change this code to allow ECP filtering, or change the copy to remove mention of recurring events. ?>
			<?php echo esc_html__( 'Your site is now using the upgraded recurring events system. See the report below to learn how your events may have been adjusted during the migration process.', 'the-events-calendar' ); ?>
		</p>

		<p>
			<?php
			echo sprintf(
				esc_html__( 'Go ahead and %1$scheck out your events%2$s, %3$sview your calendar%2$s, or %4$sread more about the new features of Events Calendar PRO 6.0%2$s.', 'the-events-calendar' ),
				'<a href="' . esc_url( admin_url( 'edit.php?post_type=' . Tribe__Events__Main::POSTTYPE ) ) . '">',
				'</a>',
				'<a href="' . esc_url( tribe_events_get_url() ) . '">',
				'<a href="https://evnt.is/recurrence-2-0" target="_blank" rel="noopener">'
			);
			?>
		</p>

		<?php include_once __DIR__ . '/report-data.php'; ?>
	</div>
</div>

<div class="tec-ct1-upgrade__row">
	<?php
	$datetime_heading = __( 'Migration Date/Time:', 'the-events-calendar' );
	$total_heading    = __( 'Total Events Migrated:', 'the-events-calendar' );
	ob_start();
	?>
	<a href="" class="tec-ct1-upgrade__link-danger"><?php esc_html_e( 'Reverse Migration', 'the-events-calendar' ); ?></a>
	<?php
	$heading_action = ob_get_clean();
	include_once __DIR__ . '/report.php';
	?>
</div>