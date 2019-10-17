<?php

namespace Tribe\Events\Views\V2\Views\HTML\Month;

use Tribe\Test\PHPUnit\Traits\With_Post_Remapping;
use Tribe\Test\Products\WPBrowser\Views\V2\HtmlTestCase;

class MonthEventMultidayTest extends HtmlTestCase {
	use With_Post_Remapping;

	/**
	 * @test
	 */
	public function it_should_contain_correct_html_classes() {

		$event = $this->mock_event( 'events/featured/1.json' )->with_thumbnail()->is_multiday( 2 )->get();
		$event->starts_this_week = true;
		$event->ends_this_week   = true;

		$template = $this->template->template(
			'month/calendar-body/day/multiday-events/multiday-event',
			[ 'event' => $event, 'day_date' => '2019-01-01', 'is_start_of_week' => true, 'today_date' => '2019-01-01' ]
		);
		$html     = $this->document->html( $template );

		$this->assertEquals(
			$html->find( '.tribe-events-calendar-month__multiday-event' )->count(),
			1,
			'Multiday HTML needs to contain one ".tribe-events-calendar-month__multiday-event" element'
		);


		$this->assertTrue(
			$html->find( '.tribe-events-calendar-month__multiday-event-bar' )->children()->is(
				'.tribe-events-calendar-month__multiday-event-bar-inner'
			),
			'Multiday HTML needs to contain ".tribe-events-calendar-month__multiday-event-bar-inner" element'
		);


		$this->assertEquals(
			$html->find( '.tribe-events-calendar-month__multiday-event-bar-title' )->count(),
			2,
			'Multiday HTML needs to contain one ".tribe-events-calendar-month__multiday-event-bar-title" element'
		);

	}

	/**
	 * @test
	 */
	public function it_should_contain_a11y_attributes() {

		$event = $this->mock_event( 'events/featured/1.json' )->with_thumbnail()->is_multiday( 2 )->get();
		$event->starts_this_week = true;
		$event->ends_this_week   = true;

		$template = $this->template->template(
			'month/calendar-body/day/multiday-events/multiday-event',
			[ 'event' => $event, 'day_date' => '2019-01-01', 'is_start_of_week' => true, 'today_date' => '2019-01-01' ]
		);
		$html     = $this->document->html( $template );
		$html     = $html->find( '.tribe-events-calendar-month__multiday-event' );
		$icon     = $html->find( '.tribe-events-calendar-month__multiday-event-bar-featured-icon' );


		$this->markTestSkipped( 'The month multi-day event does not receive data yet' );

		$this->assertTrue(
			$icon->is( '[aria-label="Featured"]' ),
			'Month multiday featured icon needs to be aria-label="Featured"'
		);

		$this->assertTrue(
			$icon->is( '[title="Featured"]' ),
			'Month multiday featured icon needs to be title="Featured"'
		);
	}
}
