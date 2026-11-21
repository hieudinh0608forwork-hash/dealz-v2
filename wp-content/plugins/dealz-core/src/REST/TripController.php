<?php
/**
 * Traveler trip REST endpoints.
 *
 * @package DealZ
 */

namespace DealZ\REST;

use DealZ\Repositories\TripRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides authenticated, owner-scoped trip CRUD.
 */
final class TripController {

	/**
	 * Persistence layer.
	 *
	 * @var TripRepository
	 */
	private $trips;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->trips = new TripRepository();
	}

	/**
	 * Register routes.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			'dealz/v1',
			'/trips',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'list_trips' ),
					'permission_callback' => array( $this, 'is_authenticated' ),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'create_trip' ),
					'permission_callback' => array( $this, 'is_authenticated' ),
				),
			)
		);

		register_rest_route(
			'dealz/v1',
			'/trips/(?P<public_id>[a-fA-F0-9-]{36})',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_trip' ),
					'permission_callback' => array( $this, 'is_authenticated' ),
				),
				array(
					'methods'             => 'PATCH',
					'callback'            => array( $this, 'update_trip' ),
					'permission_callback' => array( $this, 'is_authenticated' ),
				),
				array(
					'methods'             => 'DELETE',
					'callback'            => array( $this, 'archive_trip' ),
					'permission_callback' => array( $this, 'is_authenticated' ),
				),
			)
		);
	}

	/**
	 * Require a logged-in WordPress user.
	 *
	 * @return bool
	 */
	public function is_authenticated(): bool {
		return is_user_logged_in();
	}

	/**
	 * List current user's trips.
	 *
	 * @return \WP_REST_Response
	 */
	public function list_trips(): \WP_REST_Response {
		return new \WP_REST_Response( $this->trips->find_for_owner( get_current_user_id() ), 200 );
	}

	/**
	 * Create a trip owned by the current user.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function create_trip( \WP_REST_Request $request ) {
		$data  = $this->validated_data( $request, true );
		$error = is_wp_error( $data );

		if ( $error ) {
			return $data;
		}

		$public_id = $this->trips->create( $data, get_current_user_id() );

		if ( false === $public_id ) {
			return new \WP_Error( 'dealz_trip_create_failed', __( 'Không thể tạo chuyến đi lúc này.', 'dealz-core' ), array( 'status' => 500 ) );
		}

		$trip = $this->trips->find_owned( $public_id, get_current_user_id() );
		return new \WP_REST_Response( $trip, 201 );
	}

	/**
	 * Read one trip after owner-scoped lookup.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_trip( \WP_REST_Request $request ) {
		$trip = $this->trips->find_owned( (string) $request['public_id'], get_current_user_id() );

		if ( ! $trip || 'archived' === $trip->status ) {
			return new \WP_Error( 'dealz_trip_not_found', __( 'Không tìm thấy chuyến đi.', 'dealz-core' ), array( 'status' => 404 ) );
		}

		return new \WP_REST_Response( $trip, 200 );
	}

	/**
	 * Update an owned trip.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_trip( \WP_REST_Request $request ) {
		$public_id = (string) $request['public_id'];
		$existing  = $this->trips->find_owned( $public_id, get_current_user_id() );

		if ( ! $existing || 'archived' === $existing->status ) {
			return new \WP_Error( 'dealz_trip_not_found', __( 'Không tìm thấy chuyến đi.', 'dealz-core' ), array( 'status' => 404 ) );
		}

		$data = $this->validated_data( $request, false );

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$effective_start = array_key_exists( 'start_date', $data ) ? $data['start_date'] : $existing->start_date;
		$effective_end   = array_key_exists( 'end_date', $data ) ? $data['end_date'] : $existing->end_date;

		if ( $effective_start && $effective_end && $effective_end < $effective_start ) {
			return new \WP_Error( 'dealz_invalid_trip_range', __( 'Ngày kết thúc không được trước ngày bắt đầu.', 'dealz-core' ), array( 'status' => 400 ) );
		}

		if ( empty( $data ) ) {
			return new \WP_Error( 'dealz_trip_empty_update', __( 'Không có thông tin hợp lệ để cập nhật.', 'dealz-core' ), array( 'status' => 400 ) );
		}

		if ( ! $this->trips->update_owned( $public_id, get_current_user_id(), $data ) ) {
			return new \WP_Error( 'dealz_trip_update_failed', __( 'Không thể cập nhật chuyến đi.', 'dealz-core' ), array( 'status' => 500 ) );
		}

		return new \WP_REST_Response( $this->trips->find_owned( $public_id, get_current_user_id() ), 200 );
	}

	/**
	 * Archive an owned trip.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function archive_trip( \WP_REST_Request $request ) {
		$public_id = (string) $request['public_id'];

		if ( ! $this->trips->find_owned( $public_id, get_current_user_id() ) ) {
			return new \WP_Error( 'dealz_trip_not_found', __( 'Không tìm thấy chuyến đi.', 'dealz-core' ), array( 'status' => 404 ) );
		}

		if ( ! $this->trips->archive_owned( $public_id, get_current_user_id() ) ) {
			return new \WP_Error( 'dealz_trip_archive_failed', __( 'Không thể lưu trữ chuyến đi.', 'dealz-core' ), array( 'status' => 500 ) );
		}

		return new \WP_REST_Response( array( 'public_id' => $public_id, 'status' => 'archived' ), 200 );
	}

	/**
	 * Validate and sanitize allowed trip fields.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @param bool             $creating Whether this is a create request.
	 * @return array<string,mixed>|\WP_Error
	 */
	private function validated_data( \WP_REST_Request $request, bool $creating ) {
		$allowed = array( 'title', 'origin_text', 'destination_name', 'start_date', 'end_date', 'destination_timezone', 'traveler_count', 'budget_amount_minor', 'currency' );
		$data    = array();

		foreach ( $allowed as $field ) {
			if ( ! $request->has_param( $field ) ) {
				continue;
			}

			$value = $request->get_param( $field );

			switch ( $field ) {
				case 'title':
					$value = sanitize_text_field( (string) $value );
					if ( '' === $value || ( function_exists( 'mb_strlen' ) ? mb_strlen( $value ) : strlen( $value ) ) > 190 ) {
						return new \WP_Error( 'dealz_invalid_title', __( 'Tên chuyến đi không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'origin_text':
				case 'destination_name':
					$value = sanitize_text_field( (string) $value );
					if ( ( function_exists( 'mb_strlen' ) ? mb_strlen( $value ) : strlen( $value ) ) > 255 ) {
						return new \WP_Error( 'dealz_invalid_location', __( 'Tên địa điểm quá dài.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'start_date':
				case 'end_date':
					if ( null === $value || '' === $value ) {
						$value = null;
					} elseif ( ! $this->is_valid_date( (string) $value ) ) {
						return new \WP_Error( 'dealz_invalid_date', __( 'Ngày đi không đúng định dạng YYYY-MM-DD.', 'dealz-core' ), array( 'status' => 400 ) );
					} else {
						$value = (string) $value;
					}
					break;
				case 'destination_timezone':
					$value = sanitize_text_field( (string) $value );
					try {
						new \DateTimeZone( $value );
					} catch ( \Exception $exception ) {
						return new \WP_Error( 'dealz_invalid_timezone', __( 'Múi giờ không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'traveler_count':
					$value = filter_var( $value, FILTER_VALIDATE_INT );
					if ( false === $value || $value < 1 || $value > 50 ) {
						return new \WP_Error( 'dealz_invalid_traveler_count', __( 'Số lượng khách phải từ 1 đến 50.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'budget_amount_minor':
					if ( null === $value || '' === $value ) {
						$value = null;
					} else {
						$value = filter_var( $value, FILTER_VALIDATE_INT );
						if ( false === $value || $value < 0 ) {
							return new \WP_Error( 'dealz_invalid_budget', __( 'Ngân sách phải là số nguyên không âm.', 'dealz-core' ), array( 'status' => 400 ) );
						}
					}
					break;
				case 'currency':
					$value = strtoupper( sanitize_text_field( (string) $value ) );
					if ( ! preg_match( '/^[A-Z]{3}$/', $value ) ) {
						return new \WP_Error( 'dealz_invalid_currency', __( 'Mã tiền tệ không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
			}

			$data[ $field ] = $value;
		}

		if ( $creating ) {
			if ( ! isset( $data['title'] ) ) {
				return new \WP_Error( 'dealz_missing_title', __( 'Vui lòng nhập tên chuyến đi.', 'dealz-core' ), array( 'status' => 400 ) );
			}

			$data = array_merge(
				array(
					'origin_text'          => '',
					'destination_name'     => '',
					'start_date'           => null,
					'end_date'             => null,
					'destination_timezone' => 'Asia/Ho_Chi_Minh',
					'traveler_count'       => 1,
					'budget_amount_minor'  => null,
					'currency'             => 'VND',
				),
				$data
			);
		}

		if ( isset( $data['start_date'], $data['end_date'] ) && $data['start_date'] && $data['end_date'] && $data['end_date'] < $data['start_date'] ) {
			return new \WP_Error( 'dealz_invalid_trip_range', __( 'Ngày kết thúc không được trước ngày bắt đầu.', 'dealz-core' ), array( 'status' => 400 ) );
		}

		return $data;
	}

	/**
	 * Check strict calendar date.
	 *
	 * @param string $date Date.
	 * @return bool
	 */
	private function is_valid_date( string $date ): bool {
		$parsed = \DateTimeImmutable::createFromFormat( '!Y-m-d', $date );
		return $parsed && $parsed->format( 'Y-m-d' ) === $date;
	}
}
