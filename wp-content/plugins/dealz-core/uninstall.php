<?php
/**
 * DealZ Core uninstall handler.
 *
 * Intentionally preserves application data. Data removal requires a separate,
 * explicitly approved and reviewed retention policy.
 *
 * @package DealZ
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// No destructive cleanup is performed.
