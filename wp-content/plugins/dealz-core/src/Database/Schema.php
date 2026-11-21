<?php
/**
 * Versioned database schema for the trip-planning MVP.
 *
 * @package DealZ
 */

namespace DealZ\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates and upgrades DealZ custom tables.
 *
 * WordPress dbDelta is used for additive schema changes. Destructive changes
 * require a separate reviewed migration and must never run on plugin activation.
 */
final class Schema {

	/**
	 * Current database schema version.
	 */
	public const VERSION = '1.0.0';

	/**
	 * Option storing the installed schema version.
	 */
	private const OPTION_NAME = 'dealz_db_version';

	/**
	 * Run an additive upgrade when required.
	 *
	 * @return void
	 */
	public static function maybe_upgrade(): void {
		$installed = get_option( self::OPTION_NAME, '' );

		if ( version_compare( (string) $installed, self::VERSION, '>=' ) ) {
			return;
		}

		self::create_tables();
		update_option( self::OPTION_NAME, self::VERSION, false );
	}

	/**
	 * Create or update the initial MVP tables.
	 *
	 * @return void
	 */
	private static function create_tables(): void {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$tables          = self::table_names();

		$sql = array();

		$sql[] = "CREATE TABLE {$tables['trips']} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			public_id char(36) NOT NULL,
			owner_user_id bigint(20) unsigned NOT NULL,
			title varchar(190) NOT NULL,
			origin_text varchar(255) NOT NULL DEFAULT '',
			destination_name varchar(190) NOT NULL DEFAULT '',
			start_date date DEFAULT NULL,
			end_date date DEFAULT NULL,
			destination_timezone varchar(64) NOT NULL DEFAULT 'Asia/Ho_Chi_Minh',
			traveler_count smallint(5) unsigned NOT NULL DEFAULT 1,
			budget_amount_minor bigint(20) unsigned DEFAULT NULL,
			currency char(3) NOT NULL DEFAULT 'VND',
			status varchar(24) NOT NULL DEFAULT 'draft',
			visibility varchar(16) NOT NULL DEFAULT 'private',
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY public_id (public_id),
			KEY owner_status (owner_user_id,status),
			KEY owner_dates (owner_user_id,start_date,end_date)
		) $charset_collate;";

		$sql[] = "CREATE TABLE {$tables['collaborators']} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			trip_id bigint(20) unsigned NOT NULL,
			user_id bigint(20) unsigned DEFAULT NULL,
			invite_email varchar(190) NOT NULL DEFAULT '',
			permission varchar(16) NOT NULL DEFAULT 'view',
			invitation_status varchar(20) NOT NULL DEFAULT 'pending',
			token_hash char(64) DEFAULT NULL,
			expires_at datetime DEFAULT NULL,
			revoked_at datetime DEFAULT NULL,
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY trip_user (trip_id,user_id),
			KEY invite_email (invite_email),
			KEY token_hash (token_hash),
			KEY user_status (user_id,invitation_status)
		) $charset_collate;";

		$sql[] = "CREATE TABLE {$tables['itineraries']} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			public_id char(36) NOT NULL,
			trip_id bigint(20) unsigned NOT NULL,
			version int(10) unsigned NOT NULL DEFAULT 1,
			source varchar(16) NOT NULL DEFAULT 'manual',
			status varchar(20) NOT NULL DEFAULT 'draft',
			input_snapshot longtext DEFAULT NULL,
			created_by bigint(20) unsigned NOT NULL,
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY public_id (public_id),
			UNIQUE KEY trip_version (trip_id,version),
			KEY trip_status (trip_id,status)
		) $charset_collate;";

		$sql[] = "CREATE TABLE {$tables['days']} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			public_id char(36) NOT NULL,
			itinerary_id bigint(20) unsigned NOT NULL,
			local_date date NOT NULL,
			position smallint(5) unsigned NOT NULL DEFAULT 0,
			notes text DEFAULT NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY public_id (public_id),
			UNIQUE KEY itinerary_date (itinerary_id,local_date),
			KEY itinerary_position (itinerary_id,position)
		) $charset_collate;";

		$sql[] = "CREATE TABLE {$tables['items']} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			public_id char(36) NOT NULL,
			day_id bigint(20) unsigned NOT NULL,
			place_provider varchar(32) NOT NULL DEFAULT '',
			place_provider_id varchar(190) NOT NULL DEFAULT '',
			title_snapshot varchar(190) NOT NULL,
			description text DEFAULT NULL,
			address_snapshot varchar(255) NOT NULL DEFAULT '',
			start_local time DEFAULT NULL,
			end_local time DEFAULT NULL,
			duration_minutes smallint(5) unsigned DEFAULT NULL,
			position smallint(5) unsigned NOT NULL DEFAULT 0,
			transport_mode varchar(24) NOT NULL DEFAULT '',
			latitude decimal(10,7) DEFAULT NULL,
			longitude decimal(10,7) DEFAULT NULL,
			estimated_cost_minor bigint(20) unsigned DEFAULT NULL,
			currency char(3) NOT NULL DEFAULT 'VND',
			data_status varchar(20) NOT NULL DEFAULT 'unverified',
			metadata longtext DEFAULT NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY public_id (public_id),
			KEY day_position (day_id,position),
			KEY place_lookup (place_provider,place_provider_id)
		) $charset_collate;";

		$sql[] = "CREATE TABLE {$tables['budget_lines']} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			public_id char(36) NOT NULL,
			trip_id bigint(20) unsigned NOT NULL,
			category varchar(40) NOT NULL,
			description varchar(255) NOT NULL DEFAULT '',
			quantity decimal(10,2) NOT NULL DEFAULT 1.00,
			unit_amount_minor bigint(20) unsigned DEFAULT NULL,
			total_amount_minor bigint(20) unsigned DEFAULT NULL,
			currency char(3) NOT NULL DEFAULT 'VND',
			cost_type varchar(16) NOT NULL DEFAULT 'estimate',
			source_type varchar(32) NOT NULL DEFAULT '',
			source_id varchar(64) NOT NULL DEFAULT '',
			status varchar(20) NOT NULL DEFAULT 'active',
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY public_id (public_id),
			KEY trip_type (trip_id,cost_type),
			KEY trip_category (trip_id,category)
		) $charset_collate;";

		$sql[] = "CREATE TABLE {$tables['expenses']} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			public_id char(36) NOT NULL,
			trip_id bigint(20) unsigned NOT NULL,
			payer_user_id bigint(20) unsigned NOT NULL,
			category varchar(40) NOT NULL,
			amount_minor bigint(20) unsigned NOT NULL,
			currency char(3) NOT NULL DEFAULT 'VND',
			occurred_at datetime NOT NULL,
			note text DEFAULT NULL,
			split_method varchar(20) NOT NULL DEFAULT 'none',
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY public_id (public_id),
			KEY trip_occurred (trip_id,occurred_at),
			KEY payer_user (payer_user_id)
		) $charset_collate;";

		foreach ( $sql as $statement ) {
			dbDelta( $statement );
		}
	}

	/**
	 * Resolve table names with the active WordPress prefix.
	 *
	 * @return array<string,string>
	 */
	public static function table_names(): array {
		global $wpdb;

		return array(
			'trips'         => $wpdb->prefix . 'dealz_trips',
			'collaborators' => $wpdb->prefix . 'dealz_trip_collaborators',
			'itineraries'   => $wpdb->prefix . 'dealz_itineraries',
			'days'          => $wpdb->prefix . 'dealz_itinerary_days',
			'items'         => $wpdb->prefix . 'dealz_itinerary_items',
			'budget_lines'  => $wpdb->prefix . 'dealz_budget_lines',
			'expenses'      => $wpdb->prefix . 'dealz_expenses',
		);
	}
}
