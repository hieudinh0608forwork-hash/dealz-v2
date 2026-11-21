<?php
/**
 * Trip REST API integration tests.
 *
 * Run inside the official WordPress PHPUnit test suite.
 *
 * @package DealZ
 */

class DealZ_Trip_API_Test extends WP_UnitTestCase {

	/**
	 * Public trip IDs created by this test class.
	 *
	 * @var array<int,string>
	 */
	private $created_trip_ids = array();

	/**
	 * Ensure schema and routes are available.
	 */
	public function set_up(): void {
		parent::set_up();
		\DealZ\Database\Schema::maybe_upgrade();
		do_action( 'rest_api_init' );
	}

	/**
	 * Dispatch a REST request.
	 *
	 * @param string               $method HTTP method.
	 * @param string               $route REST route.
	 * @param array<string,mixed>  $params Request parameters.
	 * @return WP_REST_Response
	 */
	private function dispatch_request( string $method, string $route, array $params = array() ): WP_REST_Response {
		$request = new WP_REST_Request( $method, '/dealz/v1' . $route );

		if ( ! empty( $params ) ) {
			$request->set_body_params( $params );
		}

		return rest_get_server()->dispatch( $request );
	}

	/**
	 * Create a trip and return its public ID.
	 *
	 * @param int $user_id Owner user ID.
	 * @return string
	 */
	private function create_trip_for_user( int $user_id ): string {
		wp_set_current_user( $user_id );
		$response = $this->dispatch_request(
			'POST',
			'/trips',
			array(
				'title'            => 'Kiểm thử DealZ',
				'destination_name' => 'Hội An',
				'traveler_count'   => 2,
			)
		);

		$this->assertSame( 201, $response->get_status() );
		$data = $response->get_data();
		$this->created_trip_ids[] = $data['public_id'];

		return $data['public_id'];
	}

	/**
	 * Verify authenticated owner can create and read their trip.
	 */
	public function test_owner_can_create_and_read_trip(): void {
		$user_id  = self::factory()->user->create();
		$public_id = $this->create_trip_for_user( $user_id );

		$response = $this->dispatch_request( 'GET', '/trips/' . $public_id );

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( 'Kiểm thử DealZ', $response->get_data()->title );
	}

	/**
	 * Verify another authenticated user cannot read a trip by guessing its UUID.
	 */
	public function test_other_user_cannot_read_trip(): void {
		$owner_id  = self::factory()->user->create();
		$other_id  = self::factory()->user->create();
		$public_id = $this->create_trip_for_user( $owner_id );

		wp_set_current_user( $other_id );
		$response = $this->dispatch_request( 'GET', '/trips/' . $public_id );

		$this->assertSame( 404, $response->get_status() );
	}

	/**
	 * Verify reversed date ranges are rejected.
	 */
	public function test_reversed_trip_date_range_is_rejected(): void {
		$user_id = self::factory()->user->create();
		wp_set_current_user( $user_id );

		$response = $this->dispatch_request(
			'POST',
			'/trips',
			array(
				'title'      => 'Ngày không hợp lệ',
				'start_date' => '2027-02-12',
				'end_date'   => '2027-02-10',
			)
		);

		$this->assertSame( 400, $response->get_status() );
	}

	/**
	 * Verify delete archives without making the trip readable.
	 */
	public function test_delete_archives_trip(): void {
		$user_id  = self::factory()->user->create();
		$public_id = $this->create_trip_for_user( $user_id );

		$archive = $this->dispatch_request( 'DELETE', '/trips/' . $public_id );
		$this->assertSame( 200, $archive->get_status() );
		$this->assertSame( 'archived', $archive->get_data()['status'] );

		$read = $this->dispatch_request( 'GET', '/trips/' . $public_id );
		$this->assertSame( 404, $read->get_status() );
	}


	/**
	 * Verify a different user cannot mutate or archive an owned Trip.
	 */
	public function test_other_user_cannot_update_or_archive_trip(): void {
		$owner_id  = self::factory()->user->create();
		$other_id  = self::factory()->user->create();
		$public_id = $this->create_trip_for_user( $owner_id );

		wp_set_current_user( $other_id );
		$update = $this->dispatch_request( 'PATCH', '/trips/' . $public_id, array( 'title' => 'Không được phép' ) );
		$delete = $this->dispatch_request( 'DELETE', '/trips/' . $public_id );

		$this->assertSame( 404, $update->get_status() );
		$this->assertSame( 404, $delete->get_status() );
	}

	/**
	 * Verify a partial update cannot make the date range invalid.
	 */
	public function test_partial_date_update_preserves_trip_date_order(): void {
		$user_id = self::factory()->user->create();
		wp_set_current_user( $user_id );

		$create = $this->dispatch_request(
			'POST',
			'/trips',
			array(
				'title'      => 'Chuyến đi có ngày',
				'start_date' => '2027-06-10',
				'end_date'   => '2027-06-15',
			)
		);
		$this->assertSame( 201, $create->get_status() );
		$public_id = $create->get_data()->public_id;
		$this->created_trip_ids[] = $public_id;

		$update = $this->dispatch_request(
			'PATCH',
			'/trips/' . $public_id,
			array( 'end_date' => '2027-06-09' )
		);

		$this->assertSame( 400, $update->get_status() );
	}

	/**
	 * Remove test records from the custom trip table.
	 */
	public function tear_down(): void {
		global $wpdb;

		$table = $wpdb->prefix . 'dealz_trips';
		foreach ( $this->created_trip_ids as $public_id ) {
			$wpdb->delete( $table, array( 'public_id' => $public_id ), array( '%s' ) );
		}

		parent::tear_down();
	}
}
