<?php
/**
 * Itinerary, day and stop persistence.
 *
 * @package DealZ
 */

namespace DealZ\Repositories;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owner-scoped persistence for manual itinerary editing.
 */
final class ItineraryRepository {

	/**
	 * Resolve a prefixed table name.
	 *
	 * @param string $suffix Table suffix.
	 * @return string
	 */
	private function table( string $suffix ): string {
		global $wpdb;
		return $wpdb->prefix . 'dealz_' . $suffix;
	}

	/**
	 * Find a trip visible to its owner.
	 *
	 * @param string $trip_public_id Trip UUID.
	 * @param int    $owner_id Owner user ID.
	 * @return object|null
	 */
	public function find_trip( string $trip_public_id, int $owner_id ): ?object {
		global $wpdb;

		$sql = $wpdb->prepare(
			'SELECT id, public_id, start_date, end_date, destination_timezone FROM ' . $this->table( 'trips' ) . ' WHERE public_id = %s AND owner_user_id = %d AND status <> %s LIMIT 1',
			$trip_public_id,
			$owner_id,
			'archived'
		);

		$row = $wpdb->get_row( $sql );
		return is_object( $row ) ? $row : null;
	}

	/**
	 * List itineraries belonging to an owned trip.
	 *
	 * @param int $trip_id Internal trip ID.
	 * @return array<int,object>
	 */
	public function list_itineraries( int $trip_id ): array {
		global $wpdb;

		$sql = $wpdb->prepare(
			'SELECT public_id, version, source, status, created_at FROM ' . $this->table( 'itineraries' ) . ' WHERE trip_id = %d ORDER BY version DESC',
			$trip_id
		);

		return $wpdb->get_results( $sql );
	}

	/**
	 * Create a manual itinerary using a locked version counter.
	 *
	 * @param int $trip_id Internal trip ID.
	 * @param int $user_id Creator user ID.
	 * @return string|false
	 */
	public function create_itinerary( int $trip_id, int $user_id ) {
		global $wpdb;

		if ( false === $wpdb->query( 'START TRANSACTION' ) ) {
			return false;
		}

		$locked_trip = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT id FROM ' . $this->table( 'trips' ) . ' WHERE id = %d FOR UPDATE',
				$trip_id
			)
		);

		if ( ! $locked_trip ) {
			$wpdb->query( 'ROLLBACK' );
			return false;
		}

		$version = (int) $wpdb->get_var(
			$wpdb->prepare(
				'SELECT COALESCE(MAX(version), 0) FROM ' . $this->table( 'itineraries' ) . ' WHERE trip_id = %d',
				$trip_id
			)
		) + 1;

		$public_id = wp_generate_uuid4();
		$inserted  = $wpdb->insert(
			$this->table( 'itineraries' ),
			array(
				'public_id'    => $public_id,
				'trip_id'      => $trip_id,
				'version'      => $version,
				'source'       => 'manual',
				'status'       => 'draft',
				'input_snapshot' => null,
				'created_by'   => $user_id,
				'created_at'   => current_time( 'mysql', true ),
			),
			array( '%s', '%d', '%d', '%s', '%s', '%s', '%d', '%s' )
		);

		if ( false === $inserted ) {
			$wpdb->query( 'ROLLBACK' );
			return false;
		}

		$wpdb->query( 'COMMIT' );
		return $public_id;
	}

	/**
	 * Find an itinerary through its owning trip.
	 *
	 * @param string $public_id Itinerary UUID.
	 * @param int    $owner_id Owner user ID.
	 * @return object|null
	 */
	public function find_itinerary_owned( string $public_id, int $owner_id ): ?object {
		global $wpdb;

		$sql = $wpdb->prepare(
			'SELECT i.id, i.public_id, i.trip_id, i.version, i.source, i.status, i.created_at, t.start_date AS trip_start_date, t.end_date AS trip_end_date FROM ' . $this->table( 'itineraries' ) . ' i INNER JOIN ' . $this->table( 'trips' ) . ' t ON t.id = i.trip_id WHERE i.public_id = %s AND t.owner_user_id = %d AND t.status <> %s LIMIT 1',
			$public_id,
			$owner_id,
			'archived'
		);

		$row = $wpdb->get_row( $sql );
		return is_object( $row ) ? $row : null;
	}

	/**
	 * List days for an owned itinerary.
	 *
	 * @param int $itinerary_id Internal itinerary ID.
	 * @return array<int,object>
	 */
	public function list_days( int $itinerary_id ): array {
		global $wpdb;

		$sql = $wpdb->prepare(
			'SELECT public_id, local_date, position, notes FROM ' . $this->table( 'itinerary_days' ) . ' WHERE itinerary_id = %d ORDER BY position ASC, local_date ASC',
			$itinerary_id
		);

		return $wpdb->get_results( $sql );
	}

	/**
	 * Add a day to an itinerary.
	 *
	 * @param int    $itinerary_id Internal itinerary ID.
	 * @param string $local_date Local calendar date.
	 * @param int|null $position Optional explicit position.
	 * @return string|false
	 */
	public function create_day( int $itinerary_id, string $local_date, ?int $position = null ) {
		global $wpdb;

		if ( null === $position ) {
			$position = (int) $wpdb->get_var(
				$wpdb->prepare(
					'SELECT COALESCE(MAX(position), -1) + 1 FROM ' . $this->table( 'itinerary_days' ) . ' WHERE itinerary_id = %d',
					$itinerary_id
				)
			);
		}

		$public_id = wp_generate_uuid4();
		$now       = current_time( 'mysql', true );
		$inserted  = $wpdb->insert(
			$this->table( 'itinerary_days' ),
			array(
				'public_id'     => $public_id,
				'itinerary_id'  => $itinerary_id,
				'local_date'    => $local_date,
				'position'      => $position,
				'notes'         => null,
				'created_at'    => $now,
				'updated_at'    => $now,
			),
			array( '%s', '%d', '%s', '%d', '%s', '%s', '%s' )
		);

		return false === $inserted ? false : $public_id;
	}

	/**
	 * Find a day through an owned itinerary and trip.
	 *
	 * @param string $public_id Day UUID.
	 * @param int    $owner_id Owner user ID.
	 * @return object|null
	 */
	public function find_day_owned( string $public_id, int $owner_id ): ?object {
		global $wpdb;

		$sql = $wpdb->prepare(
			'SELECT d.id, d.public_id, d.itinerary_id, d.local_date, i.trip_id FROM ' . $this->table( 'itinerary_days' ) . ' d INNER JOIN ' . $this->table( 'itineraries' ) . ' i ON i.id = d.itinerary_id INNER JOIN ' . $this->table( 'trips' ) . ' t ON t.id = i.trip_id WHERE d.public_id = %s AND t.owner_user_id = %d AND t.status <> %s LIMIT 1',
			$public_id,
			$owner_id,
			'archived'
		);

		$row = $wpdb->get_row( $sql );
		return is_object( $row ) ? $row : null;
	}

	/**
	 * List active items for an owned day.
	 *
	 * @param int $day_id Internal day ID.
	 * @return array<int,object>
	 */
	public function list_items( int $day_id ): array {
		global $wpdb;

		$sql = $wpdb->prepare(
			'SELECT public_id, item_type, title_snapshot, description, address_snapshot, start_local, end_local, duration_minutes, position, transport_mode, latitude, longitude, estimated_cost_minor, currency, data_status, status FROM ' . $this->table( 'itinerary_items' ) . ' WHERE day_id = %d AND status = %s ORDER BY position ASC, start_local ASC',
			$day_id,
			'active'
		);

		return $wpdb->get_results( $sql );
	}

	/**
	 * Count active main stops in a day.
	 *
	 * @param int         $day_id Day ID.
	 * @param string|null $except_public_id Item UUID to exclude.
	 * @return int
	 */
	private function count_main_stops( int $day_id, ?string $except_public_id = null ): int {
		global $wpdb;

		$sql = 'SELECT COUNT(*) FROM ' . $this->table( 'itinerary_items' ) . ' WHERE day_id = %d AND item_type = %s AND status = %s';
		$args = array( $day_id, 'stop', 'active' );

		if ( $except_public_id ) {
			$sql .= ' AND public_id <> %s';
			$args[] = $except_public_id;
		}

		return (int) $wpdb->get_var( $wpdb->prepare( $sql, $args ) );
	}

	/**
	 * Create an itinerary item, enforcing the four-main-stop daily limit.
	 *
	 * @param int                 $day_id Day ID.
	 * @param array<string,mixed> $data Validated item data.
	 * @return string|WP_Error
	 */
	public function create_item( int $day_id, array $data ) {
		global $wpdb;

		if ( false === $wpdb->query( 'START TRANSACTION' ) ) {
			return false;
		}

		$locked_day = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT id FROM ' . $this->table( 'itinerary_days' ) . ' WHERE id = %d FOR UPDATE',
				$day_id
			)
		);

		if ( ! $locked_day ) {
			$wpdb->query( 'ROLLBACK' );
			return false;
		}

		if ( 'stop' === $data['item_type'] && $this->count_main_stops( $day_id ) >= 4 ) {
			$wpdb->query( 'ROLLBACK' );
			return new \WP_Error( 'dealz_daily_stop_limit', __( 'Mỗi ngày chỉ được có tối đa 4 điểm dừng chính.', 'dealz-core' ), array( 'status' => 400 ) );
		}

		if ( ! isset( $data['position'] ) ) {
			$data['position'] = (int) $wpdb->get_var(
				$wpdb->prepare(
					'SELECT COALESCE(MAX(position), -1) + 1 FROM ' . $this->table( 'itinerary_items' ) . ' WHERE day_id = %d',
					$day_id
				)
			);
		}

		$public_id = wp_generate_uuid4();
		$now       = current_time( 'mysql', true );
		$insert    = array_merge(
			array(
				'public_id'  => $public_id,
				'day_id'     => $day_id,
				'status'     => 'active',
				'created_at' => $now,
				'updated_at' => $now,
			),
			$data
		);

		$inserted = $wpdb->insert( $this->table( 'itinerary_items' ), $insert );

		if ( false === $inserted ) {
			$wpdb->query( 'ROLLBACK' );
			return false;
		}

		$wpdb->query( 'COMMIT' );
		return $public_id;
	}

	/**
	 * Reorder every active item in a day atomically.
	 *
	 * @param int                $day_id Day ID.
	 * @param array<int,string>  $public_ids Complete ordered UUID list.
	 * @return bool
	 */
	public function reorder_items( int $day_id, array $public_ids ): bool {
		global $wpdb;

		if ( false === $wpdb->query( 'START TRANSACTION' ) ) {
			return false;
		}

		$locked_day = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT id FROM ' . $this->table( 'itinerary_days' ) . ' WHERE id = %d FOR UPDATE',
				$day_id
			)
		);

		if ( ! $locked_day ) {
			$wpdb->query( 'ROLLBACK' );
			return false;
		}

		$current_ids = $wpdb->get_col(
			$wpdb->prepare(
				'SELECT public_id FROM ' . $this->table( 'itinerary_items' ) . ' WHERE day_id = %d AND status = %s',
				$day_id,
				'active'
			)
		);
		$expected = array_map( 'strval', $current_ids );
		$provided = array_values( array_map( 'strval', $public_ids ) );
		$sorted_expected = $expected;
		$sorted_provided = $provided;
		sort( $sorted_expected );
		sort( $sorted_provided );

		if ( count( $provided ) !== count( array_unique( $provided ) ) || $sorted_expected !== $sorted_provided ) {
			$wpdb->query( 'ROLLBACK' );
			return false;
		}

		foreach ( $provided as $position => $public_id ) {
			$updated = $wpdb->update(
				$this->table( 'itinerary_items' ),
				array(
					'position'   => $position,
					'updated_at' => current_time( 'mysql', true ),
				),
				array(
					'public_id' => $public_id,
					'day_id'    => $day_id,
					'status'    => 'active',
				),
				array( '%d', '%s' ),
				array( '%s', '%d', '%s' )
			);

			if ( false === $updated ) {
				$wpdb->query( 'ROLLBACK' );
				return false;
			}
		}

		$wpdb->query( 'COMMIT' );
		return true;
	}

	/**
	 * Find an active item through its owning trip.
	 *
	 * @param string $public_id Item UUID.
	 * @param int    $owner_id Owner user ID.
	 * @return object|null
	 */
	public function find_item_owned( string $public_id, int $owner_id ): ?object {
		global $wpdb;

		$sql = $wpdb->prepare(
			'SELECT item.*, d.itinerary_id FROM ' . $this->table( 'itinerary_items' ) . ' item INNER JOIN ' . $this->table( 'itinerary_days' ) . ' d ON d.id = item.day_id INNER JOIN ' . $this->table( 'itineraries' ) . ' i ON i.id = d.itinerary_id INNER JOIN ' . $this->table( 'trips' ) . ' t ON t.id = i.trip_id WHERE item.public_id = %s AND item.status = %s AND t.owner_user_id = %d AND t.status <> %s LIMIT 1',
			$public_id,
			'active',
			$owner_id,
			'archived'
		);

		$row = $wpdb->get_row( $sql );
		return is_object( $row ) ? $row : null;
	}

	/**
	 * Update an active item after enforcing the daily main-stop limit.
	 *
	 * @param string               $public_id Item UUID.
	 * @param int                  $day_id Day ID.
	 * @param array<string,mixed>  $data Validated changes.
	 * @return bool|WP_Error
	 */
	public function update_item( string $public_id, int $day_id, array $data ) {
		global $wpdb;

		if ( false === $wpdb->query( 'START TRANSACTION' ) ) {
			return false;
		}

		$locked_day = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT id FROM ' . $this->table( 'itinerary_days' ) . ' WHERE id = %d FOR UPDATE',
				$day_id
			)
		);

		if ( ! $locked_day ) {
			$wpdb->query( 'ROLLBACK' );
			return false;
		}

		if ( isset( $data['item_type'] ) && 'stop' === $data['item_type'] && $this->count_main_stops( $day_id, $public_id ) >= 4 ) {
			$wpdb->query( 'ROLLBACK' );
			return new \WP_Error( 'dealz_daily_stop_limit', __( 'Mỗi ngày chỉ được có tối đa 4 điểm dừng chính.', 'dealz-core' ), array( 'status' => 400 ) );
		}

		$data['updated_at'] = current_time( 'mysql', true );
		$result = $wpdb->update(
			$this->table( 'itinerary_items' ),
			$data,
			array(
				'public_id' => $public_id,
				'day_id'    => $day_id,
				'status'    => 'active',
			)
		);

		if ( false === $result ) {
			$wpdb->query( 'ROLLBACK' );
			return false;
		}

		$wpdb->query( 'COMMIT' );
		return true;
	}

	/**
	 * Archive an item instead of physically deleting it.
	 *
	 * @param string $public_id Item UUID.
	 * @param int    $day_id Day ID.
	 * @return bool
	 */
	public function archive_item( string $public_id, int $day_id ): bool {
		global $wpdb;

		$result = $wpdb->update(
			$this->table( 'itinerary_items' ),
			array(
				'status'     => 'archived',
				'updated_at' => current_time( 'mysql', true ),
			),
			array(
				'public_id' => $public_id,
				'day_id'    => $day_id,
				'status'    => 'active',
			),
			array( '%s', '%s' ),
			array( '%s', '%d', '%s' )
		);

		return false !== $result;
	}
}
