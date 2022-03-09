<?php

namespace TEC\Events\Custom_Tables\V1\Tables;

class Custom_TablesTest extends \CT1_Migration_Test_Case {
	/**
	 * @after each test make sure the custom tables will be there for the following ones.
	 */
	public function recreate_custom_tables() {
		$events_updated = ( new Events )->update();
		if ( ! $events_updated ) {
			throw new \RuntimeException( 'Failed to create Events custom table.' );
		}
		$occurrences_updated = ( new Occurrences() )->update();
		if ( ! $occurrences_updated ) {
			throw new \RuntimeException( 'Failed to create Events custom table.' );
		}
	}

	/**
	 * Should successfully drop custom tables.
	 *
	 * @skip
	 * @test
	 */
	public function should_drop_custom_tables() {
		global $wpdb;
		// Should have our custom tables.
		$q      = 'show tables';
		$tables = $wpdb->get_col( $q );
		$this->assertContains( Occurrences::table_name( true ), $tables );
		$this->assertContains( Events::table_name( true ), $tables );

		// Should drop successfully.
		$occurrence_table = tribe( Occurrences::class );
		$event_table      = tribe( Events::class );
		$this->assertTrue( $occurrence_table->drop_table() );
		$this->assertTrue( $event_table->drop_table() );

		// Tables should be gone.
		$q      = 'show tables';
		$tables = $wpdb->get_col( $q );
		$this->assertNotContains( Occurrences::table_name( true ), $tables );
		$this->assertNotContains( Events::table_name( true ), $tables );
	}

	/**
	 * Should filter the tables being dropped.
	 *
	 * @test
	 * @skip
	 */
	public function should_filter_custom_table_drop() {
		global $wpdb;
		// Should have our custom tables.
		$q      = 'show tables';
		$tables = $wpdb->get_col( $q );

		$this->assertContains( Occurrences::table_name( true ), $tables );
		$this->assertContains( Events::table_name( true ), $tables );

		// Should filter to only drop Occurrences.
		add_filter( 'tec_events_custom_tables_v1_tables_to_drop',
			function ( array $ct1_tables ) {
				return [ Occurrences::class ];
			}
		);
		tribe( Provider::class )->drop_tables();

		// One table should be gone.
		$q      = 'show tables';
		$tables = $wpdb->get_col( $q );
		$this->assertNotContains( Occurrences::table_name( true ), $tables );
		$this->assertContains( Events::table_name( true ), $tables );
	}
}