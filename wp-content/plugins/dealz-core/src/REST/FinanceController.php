<?php
/**
 * Budget and expense REST endpoints.
 *
 * @package DealZ
 */

namespace DealZ\REST;

use DealZ\Repositories\FinanceRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides owner-scoped budget and actual expense operations.
 */
final class FinanceController {

	/**
	 * Repository.
	 *
	 * @var FinanceRepository
	 */
	private $repository;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->repository = new FinanceRepository();
	}

	/**
	 * Register routes.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		$uuid = '[a-fA-F0-9-]{36}';

		register_rest_route(
			'dealz/v1',
			'/trips/(?P<trip_public_id>' . $uuid . ')/budget',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_budget' ),
				'permission_callback' => array( $this, 'is_authenticated' ),
			)
		);

		register_rest_route(
			'dealz/v1',
			'/trips/(?P<trip_public_id>' . $uuid . ')/budget/lines',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'create_budget_line' ),
				'permission_callback' => array( $this, 'is_authenticated' ),
			)
		);

		register_rest_route(
			'dealz/v1',
			'/budget/lines/(?P<public_id>' . $uuid . ')',
			array(
				array(
					'methods'             => 'PATCH',
					'callback'            => array( $this, 'update_budget_line' ),
					'permission_callback' => array( $this, 'is_authenticated' ),
				),
				array(
					'methods'             => 'DELETE',
					'callback'            => array( $this, 'archive_budget_line' ),
					'permission_callback' => array( $this, 'is_authenticated' ),
				),
			)
		);

		register_rest_route(
			'dealz/v1',
			'/trips/(?P<trip_public_id>' . $uuid . ')/expenses',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'list_expenses' ),
					'permission_callback' => array( $this, 'is_authenticated' ),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'create_expense' ),
					'permission_callback' => array( $this, 'is_authenticated' ),
				),
			)
		);

		register_rest_route(
			'dealz/v1',
			'/expenses/(?P<public_id>' . $uuid . ')',
			array(
				array(
					'methods'             => 'PATCH',
					'callback'            => array( $this, 'update_expense' ),
					'permission_callback' => array( $this, 'is_authenticated' ),
				),
				array(
					'methods'             => 'DELETE',
					'callback'            => array( $this, 'archive_expense' ),
					'permission_callback' => array( $this, 'is_authenticated' ),
				),
			)
		);
	}

	/**
	 * Require an authenticated user.
	 *
	 * @return bool
	 */
	public function is_authenticated(): bool {
		return is_user_logged_in();
	}

	/**
	 * Return budget lines and grouped totals.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_budget( \WP_REST_Request $request ) {
		$trip = $this->repository->find_trip( (string) $request['trip_public_id'], get_current_user_id() );

		if ( ! $trip ) {
			return $this->not_found();
		}

		return new \WP_REST_Response(
			array(
				'lines'   => $this->repository->list_budget_lines( (int) $trip->id ),
				'summary' => $this->repository->budget_summary( (int) $trip->id ),
			),
			200
		);
	}

	/**
	 * Create a budget line.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function create_budget_line( \WP_REST_Request $request ) {
		$trip = $this->repository->find_trip( (string) $request['trip_public_id'], get_current_user_id() );

		if ( ! $trip ) {
			return $this->not_found();
		}

		$data = $this->validated_budget_data( $request, true );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$public_id = $this->repository->create_budget_line( (int) $trip->id, $data );
		if ( ! $public_id ) {
			return new \WP_Error( 'dealz_budget_line_create_failed', __( 'Không thể thêm khoản ngân sách.', 'dealz-core' ), array( 'status' => 500 ) );
		}

		$line = $this->repository->find_budget_line_owned( $public_id, get_current_user_id() );
		return new \WP_REST_Response( $this->serialize_budget_line( $line ), 201 );
	}

	/**
	 * Update an owned budget line.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_budget_line( \WP_REST_Request $request ) {
		$public_id = (string) $request['public_id'];
		$existing  = $this->repository->find_budget_line_owned( $public_id, get_current_user_id() );

		if ( ! $existing ) {
			return $this->not_found();
		}

		$data = $this->validated_budget_data( $request, false, $existing );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		if ( ! $this->repository->update_budget_line( $public_id, (int) $existing->trip_id, $data ) ) {
			return new \WP_Error( 'dealz_budget_line_update_failed', __( 'Không thể cập nhật khoản ngân sách.', 'dealz-core' ), array( 'status' => 500 ) );
		}

		return new \WP_REST_Response(
			$this->serialize_budget_line( $this->repository->find_budget_line_owned( $public_id, get_current_user_id() ) ),
			200
		);
	}

	/**
	 * Archive a budget line.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function archive_budget_line( \WP_REST_Request $request ) {
		$public_id = (string) $request['public_id'];
		$existing  = $this->repository->find_budget_line_owned( $public_id, get_current_user_id() );

		if ( ! $existing ) {
			return $this->not_found();
		}

		if ( ! $this->repository->archive_budget_line( $public_id, (int) $existing->trip_id ) ) {
			return new \WP_Error( 'dealz_budget_line_archive_failed', __( 'Không thể lưu trữ khoản ngân sách.', 'dealz-core' ), array( 'status' => 500 ) );
		}

		return new \WP_REST_Response( array( 'public_id' => $public_id, 'status' => 'archived' ), 200 );
	}

	/**
	 * List actual expenses for an owned trip.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function list_expenses( \WP_REST_Request $request ) {
		$trip = $this->repository->find_trip( (string) $request['trip_public_id'], get_current_user_id() );

		if ( ! $trip ) {
			return $this->not_found();
		}

		return new \WP_REST_Response( $this->repository->list_expenses( (int) $trip->id ), 200 );
	}

	/**
	 * Create an actual expense.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function create_expense( \WP_REST_Request $request ) {
		$trip = $this->repository->find_trip( (string) $request['trip_public_id'], get_current_user_id() );

		if ( ! $trip ) {
			return $this->not_found();
		}

		$data = $this->validated_expense_data( $request, true );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$public_id = $this->repository->create_expense( (int) $trip->id, get_current_user_id(), $data );
		if ( ! $public_id ) {
			return new \WP_Error( 'dealz_expense_create_failed', __( 'Không thể ghi nhận khoản chi.', 'dealz-core' ), array( 'status' => 500 ) );
		}

		return new \WP_REST_Response(
			$this->serialize_expense( $this->repository->find_expense_owned( $public_id, get_current_user_id() ) ),
			201
		);
	}

	/**
	 * Update an owned expense.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_expense( \WP_REST_Request $request ) {
		$public_id = (string) $request['public_id'];
		$existing  = $this->repository->find_expense_owned( $public_id, get_current_user_id() );

		if ( ! $existing ) {
			return $this->not_found();
		}

		$data = $this->validated_expense_data( $request, false );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		if ( empty( $data ) ) {
			return new \WP_Error( 'dealz_empty_expense_update', __( 'Không có thông tin hợp lệ để cập nhật.', 'dealz-core' ), array( 'status' => 400 ) );
		}

		if ( ! $this->repository->update_expense( $public_id, (int) $existing->trip_id, $data ) ) {
			return new \WP_Error( 'dealz_expense_update_failed', __( 'Không thể cập nhật khoản chi.', 'dealz-core' ), array( 'status' => 500 ) );
		}

		return new \WP_REST_Response(
			$this->serialize_expense( $this->repository->find_expense_owned( $public_id, get_current_user_id() ) ),
			200
		);
	}

	/**
	 * Archive an expense.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function archive_expense( \WP_REST_Request $request ) {
		$public_id = (string) $request['public_id'];
		$existing  = $this->repository->find_expense_owned( $public_id, get_current_user_id() );

		if ( ! $existing ) {
			return $this->not_found();
		}

		if ( ! $this->repository->archive_expense( $public_id, (int) $existing->trip_id ) ) {
			return new \WP_Error( 'dealz_expense_archive_failed', __( 'Không thể lưu trữ khoản chi.', 'dealz-core' ), array( 'status' => 500 ) );
		}

		return new \WP_REST_Response( array( 'public_id' => $public_id, 'status' => 'archived' ), 200 );
	}

	/**
	 * Validate budget line data and calculate total server-side.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @param bool             $creating Create or update.
	 * @param object|null      $existing Existing line.
	 * @return array<string,mixed>|\WP_Error
	 */
	private function validated_budget_data( \WP_REST_Request $request, bool $creating, ?object $existing = null ) {
		$allowed = array( 'category', 'description', 'quantity', 'unit_amount_minor', 'currency', 'cost_type', 'source_type', 'source_id' );
		$data    = array();

		foreach ( $allowed as $field ) {
			if ( ! $request->has_param( $field ) ) {
				continue;
			}

			$value = $request->get_param( $field );
			switch ( $field ) {
				case 'category':
					$value = sanitize_key( (string) $value );
					if ( '' === $value || strlen( $value ) > 40 ) {
						return new \WP_Error( 'dealz_invalid_budget_category', __( 'Nhóm ngân sách không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'description':
					$value = sanitize_text_field( (string) $value );
					if ( $this->string_length( $value ) > 255 ) {
						return new \WP_Error( 'dealz_invalid_budget_description', __( 'Mô tả khoản ngân sách quá dài.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'quantity':
					$quantity = $this->parse_quantity( $value );
					if ( is_wp_error( $quantity ) ) {
						return $quantity;
					}
					$value = $quantity['formatted'];
					break;
				case 'unit_amount_minor':
					if ( null === $value || '' === $value ) {
						$value = null;
					} else {
						$value = filter_var( $value, FILTER_VALIDATE_INT );
						if ( false === $value || $value < 0 ) {
							return new \WP_Error( 'dealz_invalid_unit_amount', __( 'Đơn giá phải là số nguyên không âm.', 'dealz-core' ), array( 'status' => 400 ) );
						}
					}
					break;
				case 'currency':
					$value = strtoupper( sanitize_text_field( (string) $value ) );
					if ( ! preg_match( '/^[A-Z]{3}$/', $value ) ) {
						return new \WP_Error( 'dealz_invalid_currency', __( 'Mã tiền tệ không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'cost_type':
					$value = sanitize_key( (string) $value );
					if ( ! in_array( $value, array( 'estimate', 'quoted', 'confirmed', 'actual' ), true ) ) {
						return new \WP_Error( 'dealz_invalid_cost_type', __( 'Loại chi phí không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'source_type':
					$value = sanitize_key( (string) $value );
					if ( strlen( $value ) > 32 ) {
						return new \WP_Error( 'dealz_invalid_budget_source', __( 'Nguồn khoản ngân sách không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'source_id':
					$value = sanitize_text_field( (string) $value );
					if ( $this->string_length( $value ) > 64 ) {
						return new \WP_Error( 'dealz_invalid_budget_source_id', __( 'Mã nguồn quá dài.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
			}
			$data[ $field ] = $value;
		}

		if ( $creating && empty( $data['category'] ) ) {
			return new \WP_Error( 'dealz_missing_budget_category', __( 'Vui lòng chọn nhóm ngân sách.', 'dealz-core' ), array( 'status' => 400 ) );
		}

		if ( $creating ) {
			$data = array_merge(
				array(
					'description'        => '',
					'quantity'           => '1.00',
					'unit_amount_minor'  => null,
					'currency'           => 'VND',
					'cost_type'          => 'estimate',
					'source_type'        => '',
					'source_id'          => '',
				),
				$data
			);
		} elseif ( empty( $data ) ) {
			return new \WP_Error( 'dealz_empty_budget_update', __( 'Không có thông tin hợp lệ để cập nhật.', 'dealz-core' ), array( 'status' => 400 ) );
		}

		$quantity_input = array_key_exists( 'quantity', $data ) ? $data['quantity'] : ( $existing ? $existing->quantity : '1.00' );
		$quantity       = $this->parse_quantity( $quantity_input );
		if ( is_wp_error( $quantity ) ) {
			return $quantity;
		}

		$unit_input = array_key_exists( 'unit_amount_minor', $data ) ? $data['unit_amount_minor'] : ( $existing ? $existing->unit_amount_minor : null );
		if ( null !== $unit_input && '' !== $unit_input ) {
			$unit_input = filter_var( $unit_input, FILTER_VALIDATE_INT );
			if ( false === $unit_input || $unit_input < 0 ) {
				return new \WP_Error( 'dealz_invalid_unit_amount', __( 'Đơn giá phải là số nguyên không âm.', 'dealz-core' ), array( 'status' => 400 ) );
			}
		} else {
			$unit_input = null;
		}

		$total = $this->calculate_total( $unit_input, $quantity['scaled'] );
		if ( is_wp_error( $total ) ) {
			return $total;
		}

		$data['quantity']          = $quantity['formatted'];
		$data['unit_amount_minor'] = $unit_input;
		$data['total_amount_minor'] = $total;

		return $data;
	}

	/**
	 * Parse a decimal quantity without floating-point arithmetic.
	 *
	 * @param mixed $value Quantity input.
	 * @return array<string,int|string>|\WP_Error
	 */
	private function parse_quantity( $value ) {
		$value = trim( (string) $value );
		if ( ! preg_match( '/^\d{1,8}(?:\.\d{1,2})?$/', $value ) ) {
			return new \WP_Error( 'dealz_invalid_quantity', __( 'Số lượng phải lớn hơn 0 và có tối đa hai chữ số thập phân.', 'dealz-core' ), array( 'status' => 400 ) );
		}

		$parts    = array_pad( explode( '.', $value, 2 ), 2, '0' );
		$scaled   = ( (int) $parts[0] * 100 ) + (int) str_pad( $parts[1], 2, '0' );
		if ( $scaled < 1 ) {
			return new \WP_Error( 'dealz_invalid_quantity', __( 'Số lượng phải lớn hơn 0.', 'dealz-core' ), array( 'status' => 400 ) );
		}

		$formatted = intdiv( $scaled, 100 ) . '.' . str_pad( (string) ( $scaled % 100 ), 2, '0', STR_PAD_LEFT );
		return array(
			'scaled'    => $scaled,
			'formatted' => $formatted,
		);
	}

	/**
	 * Calculate a line total using integer minor units and hundredths quantity.
	 *
	 * @param int|null $unit_amount Unit amount.
	 * @param int      $quantity_scaled Quantity multiplied by 100.
	 * @return int|null|\WP_Error
	 */
	private function calculate_total( ?int $unit_amount, int $quantity_scaled ) {
		if ( null === $unit_amount ) {
			return null;
		}

		if ( $unit_amount > intdiv( PHP_INT_MAX - 50, $quantity_scaled ) ) {
			return new \WP_Error( 'dealz_budget_total_overflow', __( 'Tổng khoản ngân sách vượt giới hạn tính toán.', 'dealz-core' ), array( 'status' => 400 ) );
		}

		return intdiv( ( $unit_amount * $quantity_scaled ) + 50, 100 );
	}

	/**
	 * Validate expense fields.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @param bool             $creating Create or update.
	 * @return array<string,mixed>|\WP_Error
	 */
	private function validated_expense_data( \WP_REST_Request $request, bool $creating ) {
		$allowed = array( 'category', 'amount_minor', 'currency', 'occurred_at', 'note' );
		$data    = array();

		foreach ( $allowed as $field ) {
			if ( ! $request->has_param( $field ) ) {
				continue;
			}

			$value = $request->get_param( $field );
			switch ( $field ) {
				case 'category':
					$value = sanitize_key( (string) $value );
					if ( '' === $value || strlen( $value ) > 40 ) {
						return new \WP_Error( 'dealz_invalid_expense_category', __( 'Nhóm khoản chi không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'amount_minor':
					$value = filter_var( $value, FILTER_VALIDATE_INT );
					if ( false === $value || $value < 1 ) {
						return new \WP_Error( 'dealz_invalid_expense_amount', __( 'Số tiền chi phải là số nguyên dương.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'currency':
					$value = strtoupper( sanitize_text_field( (string) $value ) );
					if ( ! preg_match( '/^[A-Z]{3}$/', $value ) ) {
						return new \WP_Error( 'dealz_invalid_currency', __( 'Mã tiền tệ không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'occurred_at':
					$value = $this->normalize_utc_datetime( (string) $value );
					if ( is_wp_error( $value ) ) {
						return $value;
					}
					break;
				case 'note':
					$value = sanitize_textarea_field( (string) $value );
					if ( $this->string_length( $value ) > 1000 ) {
						return new \WP_Error( 'dealz_invalid_expense_note', __( 'Ghi chú quá dài.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
			}
			$data[ $field ] = $value;
		}

		if ( $creating ) {
			if ( empty( $data['category'] ) || ! isset( $data['amount_minor'] ) || empty( $data['occurred_at'] ) ) {
				return new \WP_Error( 'dealz_missing_expense_fields', __( 'Vui lòng nhập nhóm chi, số tiền và thời điểm phát sinh.', 'dealz-core' ), array( 'status' => 400 ) );
			}

			$data = array_merge(
				array(
					'currency' => 'VND',
					'note'     => '',
				),
				$data
			);
		}

		return $data;
	}

	/**
	 * Require an explicit timezone offset and normalize timestamp to UTC.
	 *
	 * @param string $value ISO-8601 datetime.
	 * @return string|\WP_Error
	 */
	private function normalize_utc_datetime( string $value ) {
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(?::\d{2})?(?:Z|[+-]\d{2}:\d{2})$/', $value ) ) {
			return new \WP_Error( 'dealz_invalid_expense_datetime', __( 'Thời điểm phải là ISO-8601 và có múi giờ rõ ràng.', 'dealz-core' ), array( 'status' => 400 ) );
		}

		$has_seconds = (bool) preg_match( '/^\\d{4}-\\d{2}-\\d{2}T\\d{2}:\\d{2}:\\d{2}/', $value );
		$format       = $has_seconds ? '!Y-m-d\\TH:i:sP' : '!Y-m-d\\TH:iP';
		$date         = \\DateTimeImmutable::createFromFormat( $format, $value );
		$errors       = \\DateTimeImmutable::getLastErrors();

		if ( ! $date || ( is_array( $errors ) && ( $errors['warning_count'] > 0 || $errors['error_count'] > 0 ) ) ) {
			return new \\WP_Error( 'dealz_invalid_expense_datetime', __( 'Thời điểm phát sinh không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
		}

		return $date->setTimezone( new \\DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );
	}

	/**
	 * Serialize a budget line without internal identifiers.
	 *
	 * @param object|null $line Stored line.
	 * @return array<string,mixed>
	 */
	private function serialize_budget_line( ?object $line ): array {
		if ( ! $line ) {
			return array();
		}

		$fields = array( 'public_id', 'category', 'description', 'quantity', 'unit_amount_minor', 'total_amount_minor', 'currency', 'cost_type', 'source_type', 'source_id', 'status', 'created_at', 'updated_at' );
		return array_intersect_key( (array) $line, array_flip( $fields ) );
	}

	/**
	 * Serialize an expense without internal identifiers or payer account IDs.
	 *
	 * @param object|null $expense Stored expense.
	 * @return array<string,mixed>
	 */
	private function serialize_expense( ?object $expense ): array {
		if ( ! $expense ) {
			return array();
		}

		$fields = array( 'public_id', 'category', 'amount_minor', 'currency', 'occurred_at', 'note', 'split_method', 'created_at' );
		return array_intersect_key( (array) $expense, array_flip( $fields ) );
	}

	/**
	 * Count characters when mbstring is available.
	 *
	 * @param string $value Text.
	 * @return int
	 */
	private function string_length( string $value ): int {
		return function_exists( 'mb_strlen' ) ? mb_strlen( $value ) : strlen( $value );
	}

	/**
	 * Standard not-found response.
	 *
	 * @return \WP_Error
	 */
	private function not_found(): \WP_Error {
		return new \WP_Error( 'dealz_resource_not_found', __( 'Không tìm thấy dữ liệu.', 'dealz-core' ), array( 'status' => 404 ) );
	}
}
