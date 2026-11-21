<?php
/**
 * Capability registration.
 *
 * @package DealZ
 */

namespace DealZ\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Minimal administrative capability bootstrap.
 */
final class Capabilities {

	/**
	 * Add the top-level DealZ management capability to administrators.
	 *
	 * This does not grant partner or traveler access. Those permissions must be
	 * implemented as resource-scoped checks in the relevant application services.
	 *
	 * @return void
	 */
	public static function activate(): void {
		$role = get_role( 'administrator' );

		if ( $role && ! $role->has_cap( 'manage_dealz' ) ) {
			$role->add_cap( 'manage_dealz' );
		}
	}
}
