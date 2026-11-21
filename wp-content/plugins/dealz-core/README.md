# DealZ Core – Plugin Scaffold

This is the initial WordPress plugin foundation for DealZ V2.

## Current scope
- Plugin bootstrap and namespace-based autoloading.
- Minimal `manage_dealz` capability added to the WordPress Administrator role on activation.
- Public, non-sensitive health endpoint: `GET /wp-json/dealz/v1/health`.
- Non-destructive uninstall behavior.

## Health endpoint
The endpoint deliberately returns `application_ready: false` while application modules are not implemented. It does not expose PHP, database, hosting, plugin or secret details.

## Install (local/staging only)
1. Copy `dealz-core` to `wp-content/plugins/`.
2. Activate **DealZ Core** in WordPress Admin.
3. Request `/wp-json/dealz/v1/health`.
4. Confirm the response reports `status: scaffolded` and `application_ready: false`.

This is an installation smoke check, not proof of complete application functionality.

## Not implemented
Database migrations, domain entities, authentication flows, traveler/partner endpoints, listings, trips, itinerary, booking, payment, AI, maps and notifications are not implemented yet.

## Requirements
PHP 8.1+ and WordPress 6.5+ are declared in the plugin header. Actual compatibility must be confirmed against the selected hosting/runtime before deployment.
