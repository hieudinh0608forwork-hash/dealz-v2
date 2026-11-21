<?php
/**
 * Manual itinerary REST endpoints.
 *
 * @package DealZ
 */

namespace DealZ\REST;

use DealZ\Repositories\ItineraryRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides owner-scoped itinerary, day and stop operations.
 */
final class ItineraryController {

	/**
	 * Repository.
	 *
	 * @var ItineraryRepository
	 */
	private $repository;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->repository = new ItineraryRepository();
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
			'/trips/(?P<trip_public_id>' . $uuid . ')/itineraries',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'list_itineraries' ),
					'permission_callback' => array( $this, 'is_authenticated' ),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'create_itinerary' ),
					'permission_callback' => array( $this, 'is_authenticated' ),
				),
			)
		);

		register_rest_route(
			'dealz/v1',
			'/itineraries/(?P<public_id>' . $uuid . ')/days',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'list_days' ),
					'permission_callback' => array( $this, 'is_authenticated' ),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'create_day' ),
					'permission_callback' => array( $this, 'is_authenticated' ),
				),
			)
		);

		register_rest_route(
			'dealz/v1',
			'/days/(?P<public_id>' . $uuid . ')/items',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'list_items' ),
					'permission_callback' => array( $this, 'is_authenticated' ),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'is_authenticated' ),
				),
			)
		);

		register_rest_route(
			'dealz/v1',
			'/items/(?P<public_id>' . $uuid . ')',
			array(
				array(
					'methods'             => 'PATCH',
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'is_authenticated' ),
				),
				array(
					'methods'             => 'DELETE',
					'callback'            => array( $this, 'archive_item' ),
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
	 * List manual itineraries for an owned trip.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function list_itineraries( \WP_REST_Request $request ) {
		$trip = $this->repository->find_trip( (string) $request['trip_public_id'], get_current_user_id() );

		if ( ! $trip ) {
			return $this->not_found();
		}

		return new \WP_REST_Response( $this->repository->list_itineraries( (int) $trip->id ), 200 );
	}

	/**
	 * Create a new manual itinerary version.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function create_itinerary( \WP_REST_Request $request ) {
		$trip = $this->repository->find_trip( (string) $request['trip_public_id'], get_current_user_id() );

		if ( ! $trip ) {
			return $this->not_found();
		}

		$public_id = $this->repository->create_itinerary( (int) $trip->id, get_current_user_id() );

		if ( ! $public_id ) {
			return new \WP_Error( 'dealz_itinerary_create_failed', __( 'Không thể tạo lịch trình lúc này.', 'dealz-core' ), array( 'status' => 500 ) );
		}

		$itinerary = $this->repository->find_itinerary_owned( $public_id, get_current_user_id() );
		return new \WP_REST_Response( $itinerary, 201 );
	}

	/**
	 * List days in an owned itinerary.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function list_days( \WP_REST_Request $request ) {
		$itinerary = $this->repository->find_itinerary_owned( (string) $request['public_id'], get_current_user_id() );

		if ( ! $itinerary ) {
			return $this->not_found();
		}

		return new \WP_REST_Response( $this->repository->list_days( (int) $itinerary->id ), 200 );
	}

	/**
	 * Add a day to an owned itinerary.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function create_day( \WP_REST_Request $request ) {
		$itinerary = $this->repository->find_itinerary_owned( (string) $request['public_id'], get_current_user_id() );

		if ( ! $itinerary ) {
			return $this->not_found();
		}

		$date = (string) $request->get_param( 'local_date' );
		if ( ! $this->is_valid_date( $date ) ) {
			return new \WP_Error( 'dealz_invalid_day_date', __( 'Ngày lịch trình không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
		}

		if ( $itinerary->trip_start_date && $date < $itinerary->trip_start_date ) {
			return new \WP_Error( 'dealz_day_before_trip', __( 'Ngày này nằm trước thời gian chuyến đi.', 'dealz-core' ), array( 'status' => 400 ) );
		}

		if ( $itinerary->trip_end_date && $date > $itinerary->trip_end_date ) {
			return new \WP_Error( 'dealz_day_after_trip', __( 'Ngày này nằm sau thời gian chuyến đi.', 'dealz-core' ), array( 'status' => 400 ) );
		}

		$position = null;
		if ( $request->has_param( 'position' ) ) {
			$position = filter_var( $request->get_param( 'position' ), FILTER_VALIDATE_INT );
			if ( false === $position || $position < 0 || $position > 999 ) {
				return new \WP_Error( 'dealz_invalid_day_position', __( 'Thứ tự ngày không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
			}
		}

		$public_id = $this->repository->create_day( (int) $itinerary->id, $date, $position );

		if ( ! $public_id ) {
			return new \WP_Error( 'dealz_day_create_failed', __( 'Không thể thêm ngày. Có thể ngày này đã tồn tại.', 'dealz-core' ), array( 'status' => 409 ) );
		}

		return new \WP_REST_Response(
			array(
				'public_id' => $public_id,
				'local_date' => $date,
				'position' => null === $position ? count( $this->repository->list_days( (int) $itinerary->id ) ) - 1 : $position,
			),
			201
		);
	}

	/**
	 * List active stops and meal cards for an owned day.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function list_items( \WP_REST_Request $request ) {
		$day = $this->repository->find_day_owned( (string) $request['public_id'], get_current_user_id() );

		if ( ! $day ) {
			return $this->not_found();
		}

		return new \WP_REST_Response( $this->repository->list_items( (int) $day->id ), 200 );
	}

	/**
	 * Create a stop or small meal card.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function create_item( \WP_REST_Request $request ) {
		$day = $this->repository->find_day_owned( (string) $request['public_id'], get_current_user_id() );

		if ( ! $day ) {
			return $this->not_found();
		}

		$data = $this->validated_item_data( $request, true );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		if ( $data['start_local'] && $data['end_local'] && $data['end_local'] <= $data['start_local'] ) {
			return new \WP_Error( 'dealz_invalid_item_time', __( 'Giờ kết thúc phải sau giờ bắt đầu.', 'dealz-core' ), array( 'status' => 400 ) );
		}

		$public_id = $this->repository->create_item( (int) $day->id, $data );

		if ( is_wp_error( $public_id ) ) {
			return $public_id;
		}

		if ( ! $public_id ) {
			return new \WP_Error( 'dealz_item_create_failed', __( 'Không thể thêm điểm dừng lúc này.', 'dealz-core' ), array( 'status' => 500 ) );
		}

		return new \WP_REST_Response(
			$this->repository->find_item_owned( $public_id, get_current_user_id() ),
			201
		);
	}

	/**
	 * Update an owned active item.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_item( \WP_REST_Request $request ) {
		$public_id = (string) $request['public_id'];
		$existing  = $this->repository->find_item_owned( $public_id, get_current_user_id() );

		if ( ! $existing ) {
			return $this->not_found();
		}

		$data = $this->validated_item_data( $request, false );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$start = array_key_exists( 'start_local', $data ) ? $data['start_local'] : $existing->start_local;
		$end   = array_key_exists( 'end_local', $data ) ? $data['end_local'] : $existing->end_local;

		if ( $start && $end && $end <= $start ) {
			return new \WP_Error( 'dealz_invalid_item_time', __( 'Giờ kết thúc phải sau giờ bắt đầu.', 'dealz-core' ), array( 'status' => 400 ) );
		}

		if ( empty( $data ) ) {
			return new \WP_Error( 'dealz_empty_item_update', __( 'Không có thông tin hợp lệ để cập nhật.', 'dealz-core' ), array( 'status' => 400 ) );
		}

		$result = $this->repository->update_item( $public_id, (int) $existing->day_id, $data );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( ! $result ) {
			return new \WP_Error( 'dealz_item_update_failed', __( 'Không thể cập nhật điểm dừng.', 'dealz-core' ), array( 'status' => 500 ) );
		}

		return new \WP_REST_Response( $this->repository->find_item_owned( $public_id, get_current_user_id() ), 200 );
	}

	/**
	 * Archive an owned active item.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function archive_item( \WP_REST_Request $request ) {
		$public_id = (string) $request['public_id'];
		$existing  = $this->repository->find_item_owned( $public_id, get_current_user_id() );

		if ( ! $existing ) {
			return $this->not_found();
		}

		if ( ! $this->repository->archive_item( $public_id, (int) $existing->day_id ) ) {
			return new \WP_Error( 'dealz_item_archive_failed', __( 'Không thể lưu trữ điểm dừng.', 'dealz-core' ), array( 'status' => 500 ) );
		}

		return new \WP_REST_Response( array( 'public_id' => $public_id, 'status' => 'archived' ), 200 );
	}

	/**
	 * Validate the allowed stop fields.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @param bool             $creating Create or update.
	 * @return array<string,mixed>|\WP_Error
	 */
	private function validated_item_data( \WP_REST_Request $request, bool $creating ) {
		$allowed = array(
			'item_type',
			'title_snapshot',
			'description',
			'address_snapshot',
			'start_local',
			'end_local',
			'duration_minutes',
			'position',
			'transport_mode',
			'latitude',
			'longitude',
			'estimated_cost_minor',
			'currency',
			'data_status',
			'place_provider',
			'place_provider_id',
		);
		$data = array();

		foreach ( $allowed as $field ) {
			if ( ! $request->has_param( $field ) ) {
				continue;
			}

			$value = $request->get_param( $field );

			switch ( $field ) {
				case 'item_type':
					$value = sanitize_key( (string) $value );
					if ( ! in_array( $value, array( 'stop', 'lunch', 'dinner' ), true ) ) {
						return new \WP_Error( 'dealz_invalid_item_type', __( 'Loại điểm dừng không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'title_snapshot':
					$value = sanitize_text_field( (string) $value );
					if ( '' === $value || $this->string_length( $value ) > 190 ) {
						return new \WP_Error( 'dealz_invalid_item_title', __( 'Tên điểm dừng không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'description':
					$value = sanitize_textarea_field( (string) $value );
					if ( $this->string_length( $value ) > 2000 ) {
						return new \WP_Error( 'dealz_invalid_item_description', __( 'Mô tả quá dài.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'address_snapshot':
					$value = sanitize_text_field( (string) $value );
					if ( $this->string_length( $value ) > 255 ) {
						return new \WP_Error( 'dealz_invalid_item_address', __( 'Địa chỉ quá dài.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'start_local':
				case 'end_local':
					if ( null === $value || '' === $value ) {
						$value = null;
					} elseif ( ! preg_match( '/^(?:[01][0-9]|2[0-3]):[0-5][0-9](?::[0-5][0-9])?$/', (string) $value ) ) {
						return new \WP_Error( 'dealz_invalid_item_time', __( 'Giờ phải theo định dạng HH:MM hoặc HH:MM:SS.', 'dealz-core' ), array( 'status' => 400 ) );
					} else {
						$value = 5 === strlen( (string) $value ) ? (string) $value . ':00' : (string) $value;
					}
					break;
				case 'duration_minutes':
					if ( null === $value || '' === $value ) {
						$value = null;
					} else {
						$value = filter_var( $value, FILTER_VALIDATE_INT );
						if ( false === $value || $value < 1 || $value > 1440 ) {
							return new \WP_Error( 'dealz_invalid_duration', __( 'Thời lượng phải từ 1 đến 1.440 phút.', 'dealz-core' ), array( 'status' => 400 ) );
						}
					}
					break;
				case 'position':
					$value = filter_var( $value, FILTER_VALIDATE_INT );
					if ( false === $value || $value < 0 || $value > 999 ) {
						return new \WP_Error( 'dealz_invalid_item_position', __( 'Thứ tự điểm dừng không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'transport_mode':
					$value = sanitize_key( (string) $value );
					if ( strlen( $value ) > 24 ) {
						return new \WP_Error( 'dealz_invalid_transport_mode', __( 'Phương tiện không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'latitude':
				case 'longitude':
					if ( null === $value || '' === $value ) {
						$value = null;
					} elseif ( ! is_numeric( $value ) || ( 'latitude' === $field && ( (float) $value < -90 || (float) $value > 90 ) ) || ( 'longitude' === $field && ( (float) $value < -180 || (float) $value > 180 ) ) ) {
						return new \WP_Error( 'dealz_invalid_coordinates', __( 'Tọa độ không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
					} else {
						$value = (float) $value;
					}
					break;
				case 'estimated_cost_minor':
					if ( null === $value || '' === $value ) {
						$value = null;
					} else {
						$value = filter_var( $value, FILTER_VALIDATE_INT );
						if ( false === $value || $value < 0 ) {
							return new \WP_Error( 'dealz_invalid_item_cost', __( 'Chi phí dự kiến phải là số nguyên không âm.', 'dealz-core' ), array( 'status' => 400 ) );
						}
					}
					break;
				case 'currency':
					$value = strtoupper( sanitize_text_field( (string) $value ) );
					if ( ! preg_match( '/^[A-Z]{3}$/', $value ) ) {
						return new \WP_Error( 'dealz_invalid_currency', __( 'Mã tiền tệ không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'data_status':
					$value = sanitize_key( (string) $value );
					if ( ! in_array( $value, array( 'verified', 'unverified', 'suggested', 'estimated' ), true ) ) {
						return new \WP_Error( 'dealz_invalid_data_status', __( 'Trạng thái dữ liệu không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'place_provider':
					$value = sanitize_key( (string) $value );
					if ( strlen( $value ) > 32 ) {
						return new \WP_Error( 'dealz_invalid_place_provider', __( 'Nguồn địa điểm không hợp lệ.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
				case 'place_provider_id':
					$value = sanitize_text_field( (string) $value );
					if ( $this->string_length( $value ) > 190 ) {
						return new \WP_Error( 'dealz_invalid_place_id', __( 'Mã địa điểm quá dài.', 'dealz-core' ), array( 'status' => 400 ) );
					}
					break;
			}

			$data[ $field ] = $value;
		}

		if ( $creating ) {
			if ( ! isset( $data['title_snapshot'] ) ) {
				return new \WP_Error( 'dealz_missing_item_title', __( 'Vui lòng nhập tên điểm dừng.', 'dealz-core' ), array( 'status' => 400 ) );
			}

			$data = array_merge(
				array(
					'item_type'            => 'stop',
					'description'          => null,
					'address_snapshot'     => '',
					'start_local'          => null,
					'end_local'            => null,
					'duration_minutes'     => null,
					'transport_mode'       => '',
					'latitude'             => null,
					'longitude'            => null,
					'estimated_cost_minor' => null,
					'currency'             => 'VND',
					'data_status'          => 'unverified',
					'place_provider'       => '',
					'place_provider_id'    => '',
				),
				$data
			);
		}

		return $data;
	}

	/**
	 * Validate a strict calendar date.
	 *
	 * @param string $date Date.
	 * @return bool
	 */
	private function is_valid_date( string $date ): bool {
		$parsed = \DateTimeImmutable::createFromFormat( '!Y-m-d', $date );
		return $parsed && $parsed->format( 'Y-m-d' ) === $date;
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
	 * Standard not-found response to avoid disclosing other users' resources.
	 *
	 * @return \WP_Error
	 */
	private function not_found(): \WP_Error {
		return new \WP_Error( 'dealz_resource_not_found', __( 'Không tìm thấy dữ liệu.', 'dealz-core' ), array( 'status' => 404 ) );
	}
}
