<?php
/**
 * Budget and expense REST integration tests.
 *
 * @package DealZ
 */

class DealZ_Finance_API_Test extends WP_UnitTestCase {

	/**
	 * Trip UUIDs created by the tests.
	 *
	 * @var array<int,string>
	 */
	private $trip_public_ids = array();

	/**
	 * Register schema and REST routes.
	 */
	public function set_up(): void {
		parent::set_up();
		\DealZ\Database\Schema::maybe_upgrade();
		do_action( 'rest_api_init' );
	}

	/**
	 * Dispatch a REST request.
	 *
	 * @param string              $method HTTP method.
	 * @param string              $route Route.
	 * @param array<string,mixed> $params Request body.
	 * @return WP_REST_Response
	 */
	private function request( string $method, string $route, array $params = array() ): WP_REST_Response {
		$request = new WP_REST_Request( $method, '/dealz/v1' . $route );
		if ( ! empty( $params ) ) {
			$request->set_body_params( $params );
		}
		return rest_get_server()->dispatch( $request );
	}

	/**
	 * Create a trip owned by the current test user.
	 *
	 * @return string
	 */
	private function create_owned_trip(): string {
		$user_id = self::factory()->user->create();
		wp_set_current_user( $user_id );

		$response = $this->request(
			'POST',
			'/trips',
			array(
				'title'      => 'Chuyến đi tài chính kiểm thử',
				'start_date' => '2027-05-01',
				'end_date'   => '2027-05-03',
			)
		);

		$this->assertSame( 201, $response->get_status() );
		$public_id = $response->get_data()->public_id;
		$this->trip_public_ids[] = $public_id;

		return $public_id;
	}

	/**
	 * Budget totals are calculated server-side with integer minor units.
	 */
	public function test_budget_line_total_and_summary_are_calculated(): void {
		$trip_id = $this->create_owned_trip();

		$created = $this->request(
			'POST',
			'/trips/' . $trip_id . '/budget/lines',
			array(
				'category'          => 'transport',
				'description'       => 'Thuê xe',
				'quantity'          => '2.50',
				'unit_amount_minor' => 100000,
				'currency'          => 'VND',
				'cost_type'         => 'estimate',
			)
		);

		$this->assertSame( 201, $created->get_status() );
		$this->assertSame( 250000, (int) $created->get_data()['total_amount_minor'] );

		$summary = $this->request( 'GET', '/trips/' . $trip_id . '/budget' );
		$this->assertSame( 200, $summary->get_status() );
		$this->assertCount( 1, $summary->get_data()['lines'] );
		$this->assertSame( 250000, (int) $summary->get_data()['summary'][0]['total_amount_minor'] );
	}

	/**
	 * A budget line cannot be read or changed by another user.
	 */
	public function test_budget_line_is_scoped_to_trip_owner(): void {
		$trip_id = $this->create_owned_trip();
		$created = $this->request(
			'POST',
			'/trips/' . $trip_id . '/budget/lines',
			array( 'category' => 'food', 'unit_amount_minor' => 50000 )
		);
		$this->assertSame( 201, $created->get_status() );
		$line_id = $created->get_data()['public_id'];

		wp_set_current_user( self::factory()->user->create() );
		$updated = $this->request( 'PATCH', '/budget/lines/' . $line_id, array( 'description' => 'Không được phép' ) );
		$this->assertSame( 404, $updated->get_status() );
	}

	/**
	 * Expense timestamps with offsets are normalized to UTC.
	 */
	public function test_expense_datetime_is_normalized_to_utc(): void {
		$trip_id = $this->create_owned_trip();

		$created = $this->request(
			'POST',
			'/trips/' . $trip_id . '/expenses',
			array(
				'category'    => 'food',
				'amount_minor' => 125000,
				'currency'    => 'VND',
				'occurred_at' => '2027-05-01T10:30:00+07:00',
				'note'        => 'Bữa trưa',
			)
		);

		$this->assertSame( 201, $created->get_status() );
		$this->assertSame( '2027-05-01 03:30:00', $created->get_data()['occurred_at'] );
	}

	/**
	 * Invalid calendar timestamps are rejected, not silently normalized.
	 */
	public function test_expense_rejects_invalid_calendar_datetime(): void {
		$trip_id = $this->create_owned_trip();

		$response = $this->request(
			'POST',
			'/trips/' . $trip_id . '/expenses',
			array(
				'category'     => 'food',
				'amount_minor' => 125000,
				'currency'     => 'VND',
				'occurred_at'  => '2027-02-30T10:30:00+07:00',
			)
		);

		$this->assertSame( 400, $response->get_status() );
	}

	/**
	 * Remove finance and trip rows created by these tests.
	 */
	public function tear_down(): void {
		global $wpdb;
		$tables = \DealZ\Database\Schema::table_names();

		foreach ( $this->trip_public_ids as $public_id ) {
			$trip = $wpdb->get_row(
				$wpdb->prepare(
					'SELECT id FROM ' . $tables['trips'] . ' WHERE public_id = %s',
					$public_id
				)
			);

			if ( ! $trip ) {
				continue;
			}

			$wpdb->delete( $tables['budget_lines'], array( 'trip_id' => $trip->id ), array( '%d' ) );
			$wpdb->delete( $tables['expenses'], array( 'trip_id' => $trip->id ), array( '%d' ) );
			$wpdb->delete( $tables['collaborators'], array( 'trip_id' => $trip->id ), array( '%d' ) );

			$itinerary_ids = $wpdb->get_col(
				$wpdb->prepare( 'SELECT id FROM ' . $tables['itineraries'] . ' WHERE trip_id = %d', $trip->id )
			);
			foreach ( $itinerary_ids as $itinerary_id ) {
				$day_ids = $wpdb->get_col(
					$wpdb->prepare( 'SELECT id FROM ' . $tables['days'] . ' WHERE itinerary_id = %d', $itinerary_id )
				);
				foreach ( $day_ids as $day_id ) {
					$wpdb->delete( $tables['items'], array( 'day_id' => $day_id ), array( '%d' ) );
				}
				$wpdb->delete( $tables['days'], array( 'itinerary_id' => $itinerary_id ), array( '%d' ) );
				$wpdb->delete( $tables['route_legs'], array( 'itinerary_id' => $itinerary_id ), array( '%d' ) );
			}
			$wpdb->delete( $tables['itineraries'], array( 'trip_id' => $trip->id ), array( '%d' ) );
			$wpdb->delete( $tables['trips'], array( 'id' => $trip->id ), array( '%d' ) );
		}

		parent::tear_down();
	}
}
