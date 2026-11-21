<?php
/**
 * Manual itinerary API integration tests.
 *
 * @package DealZ
 */

class DealZ_Itinerary_API_Test extends WP_UnitTestCase {

	/**
	 * Public IDs for trips created by these tests.
	 *
	 * @var array<int,string>
	 */
	private $trip_public_ids = array();

	/**
	 * Set up schema and REST routes.
	 */
	public function set_up(): void {
		parent::set_up();
		\DealZ\Database\Schema::maybe_upgrade();
		do_action( 'rest_api_init' );
	}

	/**
	 * Dispatch an API request.
	 *
	 * @param string              $method HTTP method.
	 * @param string              $route Route path.
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
	 * Create an owned trip and itinerary.
	 *
	 * @return array<string,mixed>
	 */
	private function create_itinerary_fixture(): array {
		$user_id = self::factory()->user->create();
		wp_set_current_user( $user_id );

		$trip = $this->request(
			'POST',
			'/trips',
			array(
				'title'      => 'Chuyến đi kiểm thử',
				'start_date' => '2027-04-01',
				'end_date'   => '2027-04-03',
			)
		);
		$this->assertSame( 201, $trip->get_status() );
		$trip_id = $trip->get_data()->public_id;
		$this->trip_public_ids[] = $trip_id;

		$itinerary = $this->request( 'POST', '/trips/' . $trip_id . '/itineraries' );
		$this->assertSame( 201, $itinerary->get_status() );

		return array(
			'user_id'      => $user_id,
			'trip_id'      => $trip_id,
			'itinerary_id' => $itinerary->get_data()['public_id'],
		);
	}

	/**
	 * Verify a day must fall inside the trip's date range.
	 */
	public function test_day_must_be_inside_trip_date_range(): void {
		$fixture = $this->create_itinerary_fixture();

		$response = $this->request(
			'POST',
			'/itineraries/' . $fixture['itinerary_id'] . '/days',
			array( 'local_date' => '2027-04-05' )
		);

		$this->assertSame( 400, $response->get_status() );
	}

	/**
	 * Verify four main stops are allowed and the fifth is rejected.
	 */
	public function test_day_rejects_fifth_main_stop(): void {
		$fixture = $this->create_itinerary_fixture();

		$day = $this->request(
			'POST',
			'/itineraries/' . $fixture['itinerary_id'] . '/days',
			array( 'local_date' => '2027-04-01' )
		);
		$this->assertSame( 201, $day->get_status() );
		$day_id = $day->get_data()['public_id'];

		for ( $index = 1; $index <= 4; $index++ ) {
			$stop = $this->request(
				'POST',
				'/days/' . $day_id . '/items',
				array( 'title_snapshot' => 'Điểm dừng ' . $index )
			);
			$this->assertSame( 201, $stop->get_status() );
		}

		$fifth = $this->request(
			'POST',
			'/days/' . $day_id . '/items',
			array( 'title_snapshot' => 'Điểm dừng thứ năm' )
		);
		$this->assertSame( 400, $fifth->get_status() );
	}

	/**
	 * Verify a different user cannot list days from an itinerary they do not own.
	 */
	public function test_other_user_cannot_access_itinerary_days(): void {
		$fixture = $this->create_itinerary_fixture();
		$other_user = self::factory()->user->create();
		wp_set_current_user( $other_user );

		$response = $this->request( 'GET', '/itineraries/' . $fixture['itinerary_id'] . '/days' );

		$this->assertSame( 404, $response->get_status() );
	}


	/**
	 * Verify explicit overlapping time windows are reported.
	 */
	public function test_schedule_validator_reports_overlapping_items(): void {
		$fixture = $this->create_itinerary_fixture();

		$day = $this->request(
			'POST',
			'/itineraries/' . $fixture['itinerary_id'] . '/days',
			array( 'local_date' => '2027-04-01' )
		);
		$this->assertSame( 201, $day->get_status() );
		$day_id = $day->get_data()['public_id'];

		$first = $this->request(
			'POST',
			'/days/' . $day_id . '/items',
			array(
				'title_snapshot' => 'Điểm A',
				'start_local'    => '09:00',
				'end_local'      => '10:00',
			)
		);
		$second = $this->request(
			'POST',
			'/days/' . $day_id . '/items',
			array(
				'title_snapshot' => 'Điểm B',
				'start_local'    => '09:30',
				'end_local'      => '10:30',
			)
		);
		$this->assertSame( 201, $first->get_status() );
		$this->assertSame( 201, $second->get_status() );

		$validation = $this->request( 'GET', '/days/' . $day_id . '/validation' );
		$this->assertSame( 200, $validation->get_status() );
		$this->assertFalse( $validation->get_data()['is_valid'] );
		$this->assertSame( 'time_overlap', $validation->get_data()['issues'][0]['code'] );

		$second_id = $second->get_data()['public_id'];
		$updated   = $this->request(
			'PATCH',
			'/items/' . $second_id,
			array(
				'start_local' => '10:00',
				'end_local'   => '11:00',
			)
		);
		$this->assertSame( 200, $updated->get_status() );

		$revalidated = $this->request( 'GET', '/days/' . $day_id . '/validation' );
		$this->assertSame( 200, $revalidated->get_status() );
		$this->assertTrue( $revalidated->get_data()['is_valid'] );
	}

	/**
	 * Remove test itinerary data and trip rows.
	 */
	public function tear_down(): void {
		global $wpdb;

		$trips_table      = $wpdb->prefix . 'dealz_trips';
		$itineraries_table = $wpdb->prefix . 'dealz_itineraries';
		$days_table       = $wpdb->prefix . 'dealz_itinerary_days';
		$items_table      = $wpdb->prefix . 'dealz_itinerary_items';
		$routes_table     = $wpdb->prefix . 'dealz_route_legs';

		foreach ( $this->trip_public_ids as $trip_public_id ) {
			$trip = $wpdb->get_row(
				$wpdb->prepare(
					'SELECT id FROM ' . $trips_table . ' WHERE public_id = %s',
					$trip_public_id
				)
			);

			if ( $trip ) {
				$itinerary_ids = $wpdb->get_col(
					$wpdb->prepare(
						'SELECT id FROM ' . $itineraries_table . ' WHERE trip_id = %d',
						$trip->id
					)
				);

				foreach ( $itinerary_ids as $itinerary_id ) {
					$day_ids = $wpdb->get_col(
						$wpdb->prepare(
							'SELECT id FROM ' . $days_table . ' WHERE itinerary_id = %d',
							$itinerary_id
						)
					);

					foreach ( $day_ids as $day_id ) {
						$wpdb->delete( $items_table, array( 'day_id' => $day_id ), array( '%d' ) );
					}

					$wpdb->delete( $days_table, array( 'itinerary_id' => $itinerary_id ), array( '%d' ) );
					$wpdb->delete( $routes_table, array( 'itinerary_id' => $itinerary_id ), array( '%d' ) );
				}
				$wpdb->delete( $itineraries_table, array( 'trip_id' => $trip->id ), array( '%d' ) );
				$wpdb->delete( $trips_table, array( 'id' => $trip->id ), array( '%d' ) );
			}
		}

		parent::tear_down();
	}
}
