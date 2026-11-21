# DealZ Core – WordPress Plugin

DealZ Core is the custom application plugin for DealZ V2. It is an early MVP implementation, not a production-ready travel platform.

## Implemented in repository

- Plugin bootstrap and namespace-based autoloading.
- Additive, versioned database migration v1.1.0 using WordPress `dbDelta`.
- Eight initial custom tables for trips, collaborators, itineraries, itinerary days/items, route legs, budget lines and expenses.
- Minimal `manage_dealz` capability added to WordPress Administrators on activation.
- Public, non-sensitive health endpoint: `GET /wp-json/dealz/v1/health`.
- Authenticated, owner-scoped Trip endpoints:
  - `GET /wp-json/dealz/v1/trips`
  - `POST /wp-json/dealz/v1/trips`
  - `GET /wp-json/dealz/v1/trips/{public_id}`
  - `PATCH /wp-json/dealz/v1/trips/{public_id}`
  - `DELETE /wp-json/dealz/v1/trips/{public_id}` (archives; does not hard-delete)
- Itinerary routes:
  - `GET|POST /wp-json/dealz/v1/trips/{trip_public_id}/itineraries`
  - `GET|POST /wp-json/dealz/v1/itineraries/{public_id}/days`
  - `GET|POST /wp-json/dealz/v1/days/{public_id}/items`
  - `PATCH|DELETE /wp-json/dealz/v1/items/{public_id}`
- Manual itinerary endpoints to create/list itinerary versions, add/list days, add/list stop and meal cards, update and archive items.
- Main stop limit of four per day, enforced under a database row lock.
- PHP syntax CI workflow.

## Local installation

1. Use a fresh WordPress installation (do not import V1 code, database or plugins).
2. Copy `dealz-core` into `wp-content/plugins/`.
3. Activate **DealZ Core** in WordPress Admin.
4. Activation creates/updates the additive v1.0.0 tables. The migration checks that every expected table exists before recording the schema version.
5. Open `/wp-json/dealz/v1/health`; it reports scaffold status and `application_ready: false`.
6. Test Trip endpoints while authenticated using WordPress cookie authentication and the `X-WP-Nonce` REST nonce. For same-origin JavaScript, obtain the nonce through a server-rendered/localized value; never disable nonce verification to make requests work.

## Trip API example

Create a draft trip:

```http
POST /wp-json/dealz/v1/trips
Content-Type: application/json
X-WP-Nonce: <wordpress-rest-nonce>

{
  "title": "Đà Nẵng – Hội An",
  "origin_text": "Đà Nẵng",
  "destination_name": "Hội An",
  "start_date": "2027-01-10",
  "end_date": "2027-01-12",
  "destination_timezone": "Asia/Ho_Chi_Minh",
  "traveler_count": 2,
  "currency": "VND"
}
```

Only the authenticated owner can list, read, update or archive their own trips. A lookup by another user's UUID returns 404. DELETE changes status to `archived`; it does not erase data.

## Contract

See [OpenAPI 3.1 contract](../../../docs/openapi/dealz-v1.openapi.yaml) for the implemented health and Trip routes. The contract intentionally excludes unimplemented AI, marketplace, route-provider, booking and payment features.

## Not implemented yet

- Itinerary frontend screens, schedule overlap validation, route-provider integrations and maps.
- Budget and expense REST endpoints/calculators.
- Collaborator invitations and shared-trip permissions.
- Destinations and verified place catalogue.
- Marketplace listings, availability, quotes, orders and bookings.
- Payments, refunds, commissions, payout, ledger and webhooks.
- AI planner, provider adapters, notifications and operations console.
- Complete frontend screens and local font assets.

## Verification limits

The source and contracts have been reviewed, but this plugin has not yet been activated in a real WordPress/MySQL environment. Database migration, REST behavior, cross-user access and theme rendering still require runtime tests before any feature can be called verified or production-ready.

## Requirements

PHP 8.1+ and WordPress 6.5+ are declared in the plugin header. Actual compatibility must be confirmed against the selected hosting/runtime before deployment.
