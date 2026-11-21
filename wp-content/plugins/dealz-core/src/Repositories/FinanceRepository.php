<?php
/**
 * Budget and expense persistence.
 *
 * @package DealZ
 */

namespace DealZ\Repositories;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owner-scoped persistence for trip planning costs and actual expenses.
 */
final class FinanceRepository {

	/**
	 * Resolve a prefixed DealZ table.
	 *
	 * @param string $suffix Table suffix.
	 * @return string
	 */
	private function table( string $suffix ): string {
		global $wpdb;
		return $wpdb->prefix . 'dealz_' . $suffix;
	}

	/**
	 * Find an active trip owned by a user.
	 *
	 * @param string $public_id Trip UUID.
	 * @param int    $owner_id User ID.
	 * @return object|null
	 */
	public function find_trip( string $public_id, int $owner_id ): ?object {
		global $wpdb;

		$sql = $wpdb->prepare(
			'SELECT id, public_id, currency FROM ' . $this->table( 'trips' ) . ' WHERE public_id = %s AND owner_user_id = %d AND status <> %s LIMIT 1',
			$public_id,
			$owner_id,
			'archived'
		);

		$row = $wpdb->get_row( $sql );
		return is_object( $row ) ? $row : null;
	}

	/**
	 * List active budget lines.
	 *
	 * @param int $trip_id Trip ID.
	 * @return array<int,object>
	 */
	public function list_budget_lines( int $trip_id ): array {
		global $wpdb;

		$sql = $wpdb->prepare(
			'SELECT public_id, category, description, quantity, unit_amount_minor, total_amount_minor, currency, cost_type, source_type, source_id, status, created_at, updated_at FROM ' . $this->table( 'budget_lines' ) . ' WHERE trip_id = %d AND status = %s ORDER BY category ASC, created_at ASC',
			$trip_id,
			'active'
		);

		return $wpdb->get_results( $sql );
	}

	/**
	 * Summarize budget totals by cost type and currency.
	 *
	 * @param int $trip_id Trip ID.
	 * @return array<int,object>
	 */
	public function budget_summary( int $trip_id ): array {
		global $wpdb;

		$sql = $wpdb->prepare(
			'SELECT cost_type, currency, COUNT(*) AS line_count, SUM(total_amount_minor) AS total_amount_minor FROM ' . $this->table( 'budget_lines' ) . ' WHERE trip_id = %d AND status = %s GROUP BY cost_type, currency ORDER BY currency ASC, cost_type ASC',
			$trip_id,
			'active'
		);

		return $wpdb->get_results( $sql );
	}

	/**
	 * Create a budget line.
	 *
	 * @param int                 $trip_id Trip ID.
	 * @param array<string,mixed> $data Validated values.
	 * @return string|false
	 */
	public function create_budget_line( int $trip_id, array $data ) {
		global $wpdb;

		$public_id = wp_generate_uuid4();
		$now       = current_time( 'mysql', true );
		$data      = array_merge(
			array(
				'public_id'  => $public_id,
				'trip_id'    => $trip_id,
				'status'     => 'active',
				'created_at' => $now,
				'updated_at' => $now,
			),
			$data
		);

		$inserted = $wpdb->insert( $this->table( 'budget_lines' ), $data );
		return false === $inserted ? false : $public_id;
	}

	/**
	 * Find a budget line through an owned trip.
	 *
	 * @param string $public_id Budget line UUID.
	 * @param int    $owner_id User ID.
	 * @return object|null
	 */
	public function find_budget_line_owned( string $public_id, int $owner_id ): ?object {
		global $wpdb;

		$sql = $wpdb->prepare(
			'SELECT b.*, t.owner_user_id FROM ' . $this->table( 'budget_lines' ) . ' b INNER JOIN ' . $this->table( 'trips' ) . ' t ON t.id = b.trip_id WHERE b.public_id = %s AND b.status = %s AND t.owner_user_id = %d AND t.status <> %s LIMIT 1',
			$public_id,
			'active',
			$owner_id,
			'archived'
		);

		$row = $wpdb->get_row( $sql );
		return is_object( $row ) ? $row : null;
	}

	/**
	 * Update a budget line in its owning trip.
	 *
	 * @param string              $public_id Line UUID.
	 * @param int                 $trip_id Trip ID.
	 * @param array<string,mixed> $data Validated values.
	 * @return bool
	 */
	public function update_budget_line( string $public_id, int $trip_id, array $data ): bool {
		global $wpdb;

		$data['updated_at'] = current_time( 'mysql', true );
		$result = $wpdb->update(
			$this->table( 'budget_lines' ),
			$data,
			array(
				'public_id' => $public_id,
				'trip_id'   => $trip_id,
				'status'    => 'active',
			)
		);

		return false !== $result;
	}

	/**
	 * Archive a budget line.
	 *
	 * @param string $public_id Line UUID.
	 * @param int    $trip_id Trip ID.
	 * @return bool
	 */
	public function archive_budget_line( string $public_id, int $trip_id ): bool {
		global $wpdb;

		$result = $wpdb->update(
			$this->table( 'budget_lines' ),
			array(
				'status'     => 'archived',
				'updated_at' => current_time( 'mysql', true ),
			),
			array(
				'public_id' => $public_id,
				'trip_id'   => $trip_id,
				'status'    => 'active',
			),
			array( '%s', '%s' ),
			array( '%s', '%d', '%s' )
		);

		return false !== $result;
	}

	/**
	 * List active expenses.
	 *
	 * @param int $trip_id Trip ID.
	 * @return array<int,object>
	 */
	public function list_expenses( int $trip_id ): array {
		global $wpdb;

		$sql = $wpdb->prepare(
			'SELECT public_id, category, amount_minor, currency, occurred_at, note, split_method, created_at FROM ' . $this->table( 'expenses' ) . ' WHERE trip_id = %d AND status = %s ORDER BY occurred_at DESC',
			$trip_id,
			'active'
		);

		return $wpdb->get_results( $sql );
	}

	/**
	 * Create an expense.
	 *
	 * @param int                 $trip_id Trip ID.
	 * @param int                 $payer_user_id Current user ID.
	 * @param array<string,mixed> $data Validated values.
	 * @return string|false
	 */
	public function create_expense( int $trip_id, int $payer_user_id, array $data ) {
		global $wpdb;

		$public_id = wp_generate_uuid4();
		$now       = current_time( 'mysql', true );
		$data      = array_merge(
			array(
				'public_id'     => $public_id,
				'trip_id'       => $trip_id,
				'payer_user_id' => $payer_user_id,
				'split_method'  => 'none',
				'status'        => 'active',
				'created_at'    => $now,
				'updated_at'    => $now,
			),
			$data
		);

		$inserted = $wpdb->insert( $this->table( 'expenses' ), $data );
		return false === $inserted ? false : $public_id;
	}

	/**
	 * Find an expense through an owned trip.
	 *
	 * @param string $public_id Expense UUID.
	 * @param int    $owner_id User ID.
	 * @return object|null
	 */
	public function find_expense_owned( string $public_id, int $owner_id ): ?object {
		global $wpdb;

		$sql = $wpdb->prepare(
			'SELECT e.*, t.owner_user_id FROM ' . $this->table( 'expenses' ) . ' e INNER JOIN ' . $this->table( 'trips' ) . ' t ON t.id = e.trip_id WHERE e.public_id = %s AND e.status = %s AND t.owner_user_id = %d AND t.status <> %s LIMIT 1',
			$public_id,
			'active',
			$owner_id,
			'archived'
		);

		$row = $wpdb->get_row( $sql );
		return is_object( $row ) ? $row : null;
	}

	/**
	 * Update an expense.
	 *
	 * @param string              $public_id Expense UUID.
	 * @param int                 $trip_id Trip ID.
	 * @param array<string,mixed> $data Validated values.
	 * @return bool
	 */
	public function update_expense( string $public_id, int $trip_id, array $data ): bool {
		global $wpdb;

		$data['updated_at'] = current_time( 'mysql', true );
		$result = $wpdb->update(
			$this->table( 'expenses' ),
			$data,
			array(
				'public_id' => $public_id,
				'trip_id'   => $trip_id,
				'status'    => 'active',
			)
		);

		return false !== $result;
	}

	/**
	 * Archive an expense.
	 *
	 * @param string $public_id Expense UUID.
	 * @param int    $trip_id Trip ID.
	 * @return bool
	 */
	public function archive_expense( string $public_id, int $trip_id ): bool {
		global $wpdb;

		$result = $wpdb->update(
			$this->table( 'expenses' ),
			array(
				'status'     => 'archived',
				'updated_at' => current_time( 'mysql', true ),
			),
			array(
				'public_id' => $public_id,
				'trip_id'   => $trip_id,
				'status'    => 'active',
			),
			array( '%s', '%s' ),
			array( '%s', '%d', '%s' )
		);

		return false !== $result;
	}
}
