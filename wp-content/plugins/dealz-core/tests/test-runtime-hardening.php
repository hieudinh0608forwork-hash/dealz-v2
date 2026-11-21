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
