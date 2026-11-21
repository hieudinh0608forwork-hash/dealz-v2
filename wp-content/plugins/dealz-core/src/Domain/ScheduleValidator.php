<?php
/**
 * Deterministic itinerary schedule validation.
 *
 * @package DealZ
 */

namespace DealZ\Domain;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Checks overlaps using only user-entered local times and durations.
 *
 * This validator never estimates travel time or invents route distance.
 */
final class ScheduleValidator {

	/**
	 * Validate one day's active items.
	 *
	 * @param array<int,object> $items Active itinerary items.
	 * @return array<string,mixed>
	 */
	public function validate( array $items ): array {
		$timed_items = array();
		$issues      = array();

		foreach ( $items as $item ) {
			if ( empty( $item->start_local ) ) {
				continue;
			}

			$start = $this->time_to_minutes( (string) $item->start_local );
			if ( null === $start ) {
				$issues[] = $this->issue( 'invalid_start_time', $item, __( 'Giờ bắt đầu không hợp lệ.', 'dealz-core' ) );
				continue;
			}

			$end = null;
			if ( ! empty( $item->end_local ) ) {
				$end = $this->time_to_minutes( (string) $item->end_local );
				if ( null === $end ) {
					$issues[] = $this->issue( 'invalid_end_time', $item, __( 'Giờ kết thúc không hợp lệ.', 'dealz-core' ) );
					continue;
				}
			} elseif ( ! empty( $item->duration_minutes ) ) {
				$end = $start + (int) $item->duration_minutes;
			}

			if ( null !== $end && ( $end <= $start || $end > 1440 ) ) {
				$issues[] = $this->issue( 'invalid_time_range', $item, __( 'Khung giờ của điểm dừng không hợp lệ hoặc vượt qua ngày hiện tại.', 'dealz-core' ) );
				continue;
			}

			if ( null !== $end ) {
				$timed_items[] = array(
					'item'  => $item,
					'start' => $start,
					'end'   => $end,
				);
			}
		}

		usort(
			$timed_items,
			static function ( array $left, array $right ): int {
				return $left['start'] <=> $right['start'];
			}
		);

		$count = count( $timed_items );
		for ( $index = 0; $index < $count; $index++ ) {
			for ( $next = $index + 1; $next < $count; $next++ ) {
				if ( $timed_items[ $next ]['start'] >= $timed_items[ $index ]['end'] ) {
					break;
				}

				$issues[] = array(
					'code'             => 'time_overlap',
					'item_public_id'   => $timed_items[ $next ]['item']->public_id,
					'conflict_public_id' => $timed_items[ $index ]['item']->public_id,
					'message'          => __( 'Hai hoạt động có khung giờ bị chồng lấn.', 'dealz-core' ),
				);
			}
		}

		return array(
			'is_valid'      => empty( $issues ),
			'checked_items' => $count,
			'issues'        => $issues,
			'limitations'   => array(
				'Chỉ kiểm tra thời gian do người dùng nhập.',
				'Chưa tính thời gian di chuyển giữa các điểm vì chưa có dữ liệu routing từ nhà cung cấp.',
				'Điểm không có giờ bắt đầu hoặc không có giờ kết thúc/thời lượng sẽ không được kiểm tra chồng lấn.',
			),
		);
	}

	/**
	 * Convert MySQL TIME to minutes since midnight.
	 *
	 * @param string $time Time in HH:MM:SS.
	 * @return int|null
	 */
	private function time_to_minutes( string $time ): ?int {
		if ( ! preg_match( '/^([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $time, $matches ) ) {
			return null;
		}

		return ( (int) $matches[1] * 60 ) + (int) $matches[2];
	}

	/**
	 * Format a validation issue.
	 *
	 * @param string $code Issue code.
	 * @param object $item Itinerary item.
	 * @param string $message Human-readable message.
	 * @return array<string,mixed>
	 */
	private function issue( string $code, object $item, string $message ): array {
		return array(
			'code'           => $code,
			'item_public_id' => $item->public_id,
			'message'        => $message,
		);
	}
}
