<?php
/**
 * Security, migration and transaction regression tests.
 *
 * @package DealZ
 */
class DealZ_Runtime_Hardening_Test extends WP_UnitTestCase {
	private $trip_ids = array();

	public function set_up(): void {
		parent::set_up();
		\DealZ\Database\Schema::maybe_upgrade();
		do_action( 'rest_api_init' );
	}

	private function request( string $method, string $route, array $params = array() ): WP_REST_Response {
		$request = new WP_REST_Request( $method, '/dealz/v1' . $route );
		if ( ! empty( $params ) ) {
			$request->set_body_params( $params );
		}
		return rest_get_server()->dispatch( $request );
	}

	private function create_trip(): string {
		$user_id = self::factory()->user->create();
		wp_set_current_user( $user_id );
		$response = $this->request( 'POST', '/trips', array(
			'title' => 'Kiểm thử hardening',
			'start_date' => '2027-04-01',
			'end_date' => '2027-04-03',
		) );
		$this->assertSame( 201, $response->get_status() );
		$public_id = $response->get_data()->public_id;
		$this->trip_ids[] = $public_id;
		return $public_id;
	}

	public function test_trip_itinerary_and_finance_routes_reject_anonymous_access(): void {
		wp_set_current_user( 0 );

		$trip = $this->request( 'GET', '/trips' );
		$budget = $this->request( 'GET', '/trips/00000000-0000-4000-8000-000000000001/budget' );
		$items = $this->request( 'GET', '/days/00000000-0000-4000-8000-000000000002/items' );

		$this->assertSame( 401, $trip->get_status() );
		$this->assertSame( 401, $budget->get_status() );
		$this->assertSame( 401, $items->get_status() );
	}

	public function test_schema_activation_is_repeatable_and_keeps_all_eight_tables(): void {
		\DealZ\Database\Schema::maybe_upgrade();
		do_action( 'activate_dealz-core/dealz-core.php' );
		\DealZ\Database\Schema::maybe_upgrade();

		global $wpdb;
		$tables = \DealZ\Database\Schema::table_names();
		$this->assertCount( 8, $tables );

		foreach ( $tables as $table ) {
			$found = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
			$this->assertSame( $table, $found, 'Missing DealZ table: ' . $table );
		}

		$this->assertSame( \DealZ\Database\Schema::VERSION, get_option( 'dealz_db_version' ) );
	}

	public function test_invalid_reorder_rolls_back_without_changing_existing_positions(): void {
		$trip_id = $this->create_trip();
		$itinerary = $this->request( 'POST', '/trips/' . $trip_id . '/itineraries' );
		$this->assertSame( 201, $itinerary->get_status() );

		$day = $this->request( 'POST', '/itineraries/' . $itinerary->get_data()['public_id'] . '/days', array(
			'local_date' => '2027-04-01',
		) );
		$this->assertSame( 201, $day->get_status() );
		$day_id = $day->get_data()['public_id'];

		$first = $this->request( 'POST', '/days/' . $day_id . '/items', array( 'title_snapshot' => 'Điểm A' ) );
		$second = $this->request( 'POST', '/days/' . $day_id . '/items', array( 'title_snapshot' => 'Điểm B' ) );
		$this->assertSame( 201, $first->get_status() );
		$this->assertSame( 201, $second->get_status() );

		global $wpdb;
		$day_row = $wpdb->get_row( $wpdb->prepare(
			'SELECT id FROM ' . $wpdb->prefix . 'dealz_itinerary_days WHERE public_id = %s',
			$day_id
		) );
		$items_table = $wpdb->prefix . 'dealz_itinerary_items';
		$before = $wpdb->get_results( $wpdb->prepare(
			'SELECT public_id, position FROM ' . $items_table . ' WHERE day_id = %d AND status = %s ORDER BY position ASC',
			$day_row->id,
			'active'
		), OBJECT_K );

		$repository = new \DealZ\Repositories\ItineraryRepository();
		$this->assertFalse( $repository->reorder_items( (int) $day_row->id, array( $first->get_data()['public_id'] ) ) );

		$after = $wpdb->get_results( $wpdb->prepare(
			'SELECT public_id, position FROM ' . $items_table . ' WHERE day_id = %d AND status = %s ORDER BY position ASC',
			$day_row->id,
			'active'
		), OBJECT_K );

		$this->assertEquals( $before, $after, 'A rejected reorder must not persist partial position changes.' );

		// Force the second UPDATE in a valid reorder to fail after the first UPDATE has run.
		// The repository must roll the first write back as part of the same transaction.
		$trigger_name = 'dealz_test_reorder_rollback';
		$wpdb->query( 'DROP TRIGGER IF EXISTS `' . $trigger_name . '`' );
		$trigger_sql = sprintf(
			"CREATE TRIGGER `%s` BEFORE UPDATE ON `%s` FOR EACH ROW BEGIN IF OLD.public_id = '%s' AND NEW.position = 1 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Forced DealZ rollback test'; END IF; END",
			$trigger_name,
			$items_table,
			esc_sql( $first->get_data()['public_id'] )
		);
		$this->assertNotFalse( $wpdb->query( $trigger_sql ), 'The rollback test trigger must be created.' );
		try {
			$this->assertFalse(
				$repository->reorder_items(
					(int) $day_row->id,
					array( $second->get_data()['public_id'], $first->get_data()['public_id'] )
				),
				'A database error after a partial reorder must fail the transaction.'
			);
		} finally {
			$wpdb->query( 'DROP TRIGGER IF EXISTS `' . $trigger_name . '`' );
		}

		$after_failure = $wpdb->get_results( $wpdb->prepare(
			'SELECT public_id, position FROM ' . $items_table . ' WHERE day_id = %d AND status = %s ORDER BY position ASC',
			$day_row->id,
			'active'
		), OBJECT_K );
		$this->assertEquals( $before, $after_failure, 'A failed UPDATE must roll back earlier position changes in the transaction.' );
	}

	public function test_migration_from_schema_1_0_preserves_itinerary_data_and_adds_v1_1_v1_2_fields(): void {
		$trip_id = $this->create_trip();
		$itinerary = $this->request( 'POST', '/trips/' . $trip_id . '/itineraries' );
		$this->assertSame( 201, $itinerary->get_status() );
		$day = $this->request( 'POST', '/itineraries/' . $itinerary->get_data()['public_id'] . '/days', array(
			'local_date' => '2027-04-01',
		) );
		$this->assertSame( 201, $day->get_status() );
		$item = $this->request( 'POST', '/days/' . $day->get_data()['public_id'] . '/items', array(
			'title_snapshot' => 'Dữ liệu cũ cần giữ',
		) );
		$this->assertSame( 201, $item->get_status() );

		global $wpdb;
		$tables = \DealZ\Database\Schema::table_names();
		$this->assertNotFalse( $wpdb->query( 'ALTER TABLE `' . $tables['items'] . '` DROP COLUMN `item_type`, DROP COLUMN `status`' ) );
		$this->assertNotFalse( $wpdb->query( 'ALTER TABLE `' . $tables['expenses'] . '` DROP COLUMN `status`' ) );
		$this->assertNotFalse( $wpdb->query( 'DROP TABLE IF EXISTS `' . $tables['route_legs'] . '`' ) );
		update_option( 'dealz_db_version', '1.0.0' );

		\DealZ\Database\Schema::maybe_upgrade();

		$this->assertSame( '1.2.0', get_option( 'dealz_db_version' ) );
		foreach ( array(
			$tables['items'] . '.item_type',
			$tables['items'] . '.status',
			$tables['expenses'] . '.status',
		) as $qualified_column ) {
			list( $table, $column ) = explode( '.', $qualified_column, 2 );
			$this->assertNotFalse( $wpdb->get_var( $wpdb->prepare( 'SHOW COLUMNS FROM `' . $table . '` LIKE %s', $column ) ) );
		}
		$this->assertSame( $tables['route_legs'], $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $tables['route_legs'] ) ) );

		$preserved = $wpdb->get_row( $wpdb->prepare(
			'SELECT title_snapshot, item_type, status FROM ' . $tables['items'] . ' WHERE public_id = %s',
			$item->get_data()['public_id']
		) );
		$this->assertSame( 'Dữ liệu cũ cần giữ', $preserved->title_snapshot );
		$this->assertSame( 'stop', $preserved->item_type );
		$this->assertSame( 'active', $preserved->status );
	}

	public function test_migration_from_schema_1_1_adds_expense_archive_status_without_data_loss(): void {
		$trip_id = $this->create_trip();
		global $wpdb;
		$tables = \DealZ\Database\Schema::table_names();
		$this->assertNotFalse( $wpdb->query( 'ALTER TABLE `' . $tables['expenses'] . '` DROP COLUMN `status`' ) );
		$public_id = wp_generate_uuid4();
		$now = current_time( 'mysql', true );
		$this->assertSame( 1, $wpdb->insert(
			$tables['expenses'],
			array(
				'public_id' => $public_id,
				'trip_id' => (int) $wpdb->get_var( $wpdb->prepare( 'SELECT id FROM ' . $tables['trips'] . ' WHERE public_id = %s', $trip_id ) ),
				'payer_user_id' => get_current_user_id(),
				'category' => 'food',
				'amount_minor' => 275000,
				'currency' => 'VND',
				'occurred_at' => '2027-04-01 12:00:00',
				'note' => 'Khoản chi từ schema 1.1',
				'split_method' => 'none',
				'created_at' => $now,
				'updated_at' => $now,
			)
		) );
		update_option( 'dealz_db_version', '1.1.0' );

		\DealZ\Database\Schema::maybe_upgrade();

		$this->assertSame( '1.2.0', get_option( 'dealz_db_version' ) );
		$preserved = $wpdb->get_row( $wpdb->prepare(
			'SELECT amount_minor, note, status FROM ' . $tables['expenses'] . ' WHERE public_id = %s',
			$public_id
		) );
		$this->assertNotNull( $preserved );
		$this->assertSame( '275000', (string) $preserved->amount_minor );
		$this->assertSame( 'Khoản chi từ schema 1.1', $preserved->note );
		$this->assertSame( 'active', $preserved->status );
	}

	public function test_budget_rejects_zero_quantity_and_integer_overflow(): void {
		$trip_id = $this->create_trip();

		$zero = $this->request( 'POST', '/trips/' . $trip_id . '/budget/lines', array(
			'category' => 'transport',
			'quantity' => '0',
			'unit_amount_minor' => 100,
		) );
		$this->assertSame( 400, $zero->get_status() );

		$overflow = $this->request( 'POST', '/trips/' . $trip_id . '/budget/lines', array(
			'category' => 'transport',
			'quantity' => '99999999.99',
			'unit_amount_minor' => (string) PHP_INT_MAX,
		) );
		$this->assertSame( 400, $overflow->get_status() );
	}

	public function tear_down(): void {
		global $wpdb;
		$tables = \DealZ\Database\Schema::table_names();

		foreach ( $this->trip_ids as $public_id ) {
			$trip = $wpdb->get_row( $wpdb->prepare(
				'SELECT id FROM ' . $tables['trips'] . ' WHERE public_id = %s',
				$public_id
			) );
			if ( ! $trip ) {
				continue;
			}
			$itineraries = $wpdb->get_col( $wpdb->prepare(
				'SELECT id FROM ' . $tables['itineraries'] . ' WHERE trip_id = %d',
				$trip->id
			) );
			foreach ( $itineraries as $itinerary_id ) {
				$days = $wpdb->get_col( $wpdb->prepare(
					'SELECT id FROM ' . $tables['days'] . ' WHERE itinerary_id = %d',
					$itinerary_id
				) );
				foreach ( $days as $day_id ) {
					$wpdb->delete( $tables['items'], array( 'day_id' => $day_id ), array( '%d' ) );
				}
				$wpdb->delete( $tables['days'], array( 'itinerary_id' => $itinerary_id ), array( '%d' ) );
			}
			$wpdb->delete( $tables['itineraries'], array( 'trip_id' => $trip->id ), array( '%d' ) );
			$wpdb->delete( $tables['budget_lines'], array( 'trip_id' => $trip->id ), array( '%d' ) );
			$wpdb->delete( $tables['expenses'], array( 'trip_id' => $trip->id ), array( '%d' ) );
			$wpdb->delete( $tables['collaborators'], array( 'trip_id' => $trip->id ), array( '%d' ) );
			$wpdb->delete( $tables['trips'], array( 'id' => $trip->id ), array( '%d' ) );
		}

		parent::tear_down();
	}
}
