<?php
/**
 * Provides methods common to any Service Provider dealing in Custom Tables registration.
 *
 * @since   TBD
 *
 * @package TEC\Events\Custom_Tables\V1\Tables
 */

namespace TEC\Events\Custom_Tables\V1\Tables;

use WP_CLI;

/**
 * Trait Custom_Tables_Provider
 *
 * @since   TBD
 *
 * @package TEC\Events\Custom_Tables\V1\Tables
 */
trait Custom_Tables_Provider {
	/**
	 * Removes the table option from the database on deactivation.
	 *
	 * @since TBD
	 */
	public function clean() {
		delete_option( self::VERSION_OPTION );
	}

	/**
	 * Filters the list of tables for a blog adding the ones created by the plugin.
	 *
	 * @since TBD
	 *
	 * @param array $tables An array of table names for the blog.
	 *
	 * @return array<string> A filtered array of table names, including prefix.
	 */
	public function filter_tables_list( $tables ) {
		foreach ( $this->table_classes as $class ) {
			$table_name            = call_user_func( [ $class, 'table_name' ] );
			$tables[ $table_name ] = $table_name;
		}

		return $tables;
	}

	/**
	 * A proxy method to update the tables without forcing
	 * them.
	 *
	 * If the `update_tables` was directly hooked to the blog
	 * switches, then the blog ID, a positive integer, would be
	 * cast to a truthy value and force the table updates when
	 * not really required to.
	 *
	 * @since TBD
	 *
	 * @return array<mixed> A list of each creation or update result.
	 */
	public function update_blog_tables() {
		return $this->update_tables( false );
	}

	/**
	 * Creates or updates the custom tables the plugin will use.
	 *
	 * @since TBD
	 *
	 * @param bool $force Whether to force the creation or update of the tables or not.
	 *
	 * @return array<mixed> A list of each creation or update result.
	 */
	public function update_tables( $force = false ) {
		if ( ! $force && version_compare( get_option( self::VERSION_OPTION ), self::VERSION, '>=' ) ) {
			return [];
		}

		global $wpdb;

		//phpcs:ignore
		$wpdb->get_results( "SELECT 1 FROM {$wpdb->posts} LIMIT 1" );
		$posts_table_exists = '' === $wpdb->last_error;
		// Let's not try to create the tables on a blog that's missing the basic ones.
		if ( ! $posts_table_exists ) {
			return [];
		}

		$results = [];

		foreach ( $this->table_classes as $custom_table_class ) {
			/** @var Custom_Table_Interface $custom_table */
			$custom_table                               = $this->container->make( $custom_table_class );
			$results[ $custom_table->get_table_name() ] = $custom_table->update();
		}

		add_option( self::VERSION_OPTION, self::VERSION );

		return array_merge( ...array_values( $results ) );
	}

	/**
	 * Registers the custom table names as properties on the `wpdb` global.
	 *
	 * @since TBD
	 */
	public function register_custom_tables_names() {
		global $wpdb;

		foreach ( $this->table_classes as $class ) {
			$no_prefix_table_name          = call_user_func( [ $class, 'table_name' ], false );
			$prefixed_tale_name            = call_user_func( [ $class, 'table_name' ] );
			$wpdb->{$no_prefix_table_name} = $prefixed_tale_name;
			if ( ! in_array( $wpdb->{$no_prefix_table_name}, $wpdb->tables, true ) ) {
				$wpdb->tables[] = $wpdb->{$no_prefix_table_name};
			}
		}
	}

	/**
	 * Empties the plugin custom tables.
	 *
	 * @since TBD
	 */
	public function empty_custom_tables() {
		// Due to foreign key constraints, custom tables should be emptied in inverse creation order.
		foreach ( array_reverse( $this->table_classes ) as $class ) {
			/** @var Custom_Table_Interface $custom_table */
			$custom_table = $this->container->make( $class );
			WP_CLI::debug( 'Emptying table ' . $custom_table->get_table_name(), 'TEC' );
			$custom_table->empty_table();
		}
	}

	/**
	 * Registers the service provider functions.
	 *
	 * @since TBD
	 */
	public function register() {
		$this->container->singleton( Provider::class, $this );

		$this->register_custom_tables_names();
		$this->register_wpcli_support();

		if ( is_multisite() ) {
			$this->register_multisite_actions();
		}
	}

	/**
	 * Ensures the tables exist for a blog on activation or switch.
	 *
	 * @since TBD
	 */
	private function register_multisite_actions() {
		add_action( 'activate_blog', [ $this, 'update_blog_tables' ] );
		add_action( 'activate_blog', [ $this, 'register_custom_tables_names' ] );
		add_action( 'switch_blog', [ $this, 'update_blog_tables' ] );
		add_action( 'switch_blog', [ $this, 'register_custom_tables_names' ] );
		add_filter( 'wpmu_drop_tables', [ $this, 'filter_tables_list' ] );
	}

	/**
	 * Hooks into wp-cli actions to perform operations on custom tables.
	 *
	 * @since TBD
	 */
	private function register_wpcli_support() {
		if ( defined( 'WP_CLI' ) && method_exists( '\\WP_CLI', 'add_hook' ) ) {
			WP_CLI::add_hook( 'after_invoke:site empty', [ $this, 'empty_custom_tables' ] );
		}
	}
}
