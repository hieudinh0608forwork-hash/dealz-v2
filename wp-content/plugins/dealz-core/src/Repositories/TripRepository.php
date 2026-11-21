<?php
/**
 * Trip persistence.
 *
 * @package DealZ
 */

namespace DealZ\Repositories;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Database access for traveler-owned trips.
 */
final class TripRepository {

	/**
	 * Resolve trips table.
	 *
	 * @return string
	 */
	private function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'dealz_trips';
	}

	/**
	 * List active trips for one owner.
	 *
	 * @param int $owner_id WordPress user ID.
	 * @return array<int,object>
	 */
	public function find_for_owner( int $owner_id ): array {
		global $wpdb;

		$sql = $wpdb->prepare(
			'SELECT public_id, title, origin_text, destination_name, start_date, end_date, destination_timezone, traveler_count, budget_amount_minor, currency, status, created_at, updated_at FROM ' . $this->table() . ' WHERE owner_user_id = %d AND status <> %s ORDER BY updated_at DESC LIMIT 100',
			$owner_id,
			'archived'
		);

		return $wpdb->get_results( $sql );
	}

	/**
	 * Find a trip owned by a specific user.
	 *
	 * @param string $public_id Public UUID.
	 * @param int    $owner_id WordPress user ID.
	 * @return object|null
	 */
	public function find_owned( string $public_id, int $owner_id ): ?object {
		global $wpdb;

		$sql = $wpdb->prepare(
			'SELECT public_id, title, origin_text, destination_name, start_date, end_date, destination_timezone, traveler_count, budget_amount_minor, currency, status, visibility, created_at, updated_at FROM ' . $this->table() . ' WHERE public_id = %s AND owner_user_id = %d LIMIT 1',
			$public_id,
			$owner_id
		);

		$result = $wpdb->get_row( $sql );
		return is_object( $result ) ? $result : null;
	}

	/**
	 * Create a trip.
	 *
	 * @param array<string,mixed> $data Validated trip data.
	 * @param int                 $owner_id Owner user ID.
	 * @return string|false Public UUID or false on database error.
	 */
	public function create( array $data, int $owner_id ) {
		global $wpdb;

		$public_id = wp_generate_uuid4();
		$now       = current_time( 'mysql', true );

		$inserted = $wpdb->insert(
			$this->table(),
			array(
				'public_id'             => $public_id,
				'owner_user_id'         => $owner_id,
				'title'                 => $data['title'],
				'origin_text'           => $data['origin_text'],
				'destination_name'      => $data['destination_name'],
				'start_date'            => $data['start_date'],
				'end_date'              => $data['end_date'],
				'destination_timezone'  => $data['destination_timezone'],
				'traveler_count'        => $data['traveler_count'],
				'budget_amount_minor'   => $data['budget_amount_minor'],
				'currency'              => $data['currency'],
				'status'                => 'draft',
				'visibility'            => 'private',
				'created_at'            => $now,
				'updated_at'            => $now,
			),
			array( '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s' )
		);

		return false === $inserted ? false : $public_id;
	}

	/**
	 * Update editable fields for an owned trip.
	 *
	 * @param string               $public_id Public UUID.
	 * @param int                  $owner_id Owner user ID.
	 * @param array<string,mixed>  $data Validated fields.
	 * @return bool
	 */
	public function update_owned( string $public_id, int $owner_id, array $data ): bool {
		global $wpdb;

		$data['updated_at'] = current_time( 'mysql', true );

		$result = $wpdb->update(
			$this->table(),
			$data,
			array(
				'public_id'     => $public_id,
				'owner_user_id' => $owner_id,
			)
		);

		return false !== $result;
	}

	/**
	 * Archive an owned trip instead of deleting user data.
	 *
	 * @param string $public_id Public UUID.
	 * @param int    $owner_id Owner user ID.
	 * @return bool
	 */
	public function archive_owned( string $public_id, int $owner_id ): bool {
		global $wpdb;

		$result = $wpdb->update(
			$this->table(),
			array(
				'status'     => 'archived',
				'updated_at' => current_time( 'mysql', true ),
			),
			array(
				'public_id'     => $public_id,
				'owner_user_id' => $owner_id,
			),
			array( '%s', '%s' ),
			array( '%s', '%d' )
		);

		return false !== $result;
	}
}
