# 05 – Database Schema Baseline

## 1. Design rules
- This is a logical model, not final migration SQL.
- Use stable UUID public identifiers; internal numeric PKs are acceptable.
- Store money as integer VND đồng or a precisely specified DECIMAL; never floating-point totals.
- Store UTC timestamps and explicit destination timezone for local itinerary times.
- Snapshot service, price, cancellation terms and fulfillment type at quote/booking.
- Keep estimated cost, quoted amount, confirmed amount and actual expense separate.
- Enforce ownership and partner scoping server-side.
- Use indexes for owner/status/date, supplier/status, service/location and foreign-key lookups.

## 2. Identity and organizations
### wp_users
WordPress account identity, authentication and base capabilities.
### dealz_user_profiles
user_id, display_name, phone/email visibility preferences, avatar, locale, timezone, preferences JSON (validated).
### dealz_partners
id, owner_user_id, legal/display name, status, onboarding state, created_at, updated_at.
### dealz_partner_members
partner_id, user_id, partner_role, status. Enables multiple staff later without sharing accounts.

## 3. Discovery and services
### dealz_destinations
id, slug, name, description, region, country_code, status, source metadata, optional centroid coordinates.
### dealz_places
id, destination_id, provider, provider_place_id, name, category, address, latitude, longitude, opening_hours JSON/source, source_url, verified_at, data_status.
### dealz_service_categories
id, slug, name, parent_id, status, sort_order.
### dealz_listings
id, partner_id, category_id, destination_id, title, description, fulfillment_type, price_basis, status, moderation_status, location, created_at, updated_at.
### dealz_listing_prices
id, listing_id, amount_minor, currency, unit, options_json, effective_from/to, source, updated_by.
### dealz_availability
id, listing_id, availability_type, start_at/end_at or service_date, capacity, remaining, status, version, updated_at.
Availability model must support manual request, date-range inventory and time-slot capacity; exact schema depends on supplier types.

## 4. Trips and planning
### dealz_trips
id, owner_user_id, title, origin_place_id/text, destination_id, start_date, end_date, destination_timezone, traveler_count, budget_amount_minor, currency, status, visibility, created_at, updated_at.
### dealz_trip_collaborators
trip_id, user_id/invitation, permission (view/comment/edit), invitation_status, token_hash, expires_at, revoked_at.
### dealz_itineraries
id, trip_id, version, source (manual/ai), status, input_snapshot, created_at, created_by.
### dealz_itinerary_days
id, itinerary_id, local_date, position, notes.
### dealz_itinerary_items
id, day_id, place_id nullable, title_snapshot, description, start_local/end_local, duration_minutes, position, transport_mode, address_snapshot, latitude, longitude, estimated_cost_minor, currency, data_status, metadata.
### dealz_route_legs
id, itinerary_id, from_item_id, to_item_id, mode, distance_meters, duration_seconds, provider, calculated_at, status, warnings.
### dealz_ai_runs
id, trip_id, provider, model, prompt_version, input_hash, status, latency_ms, token_usage, error_code, created_at. Do not store secrets or unnecessary personal data.

## 5. Budget and expenses
### dealz_budget_lines
id, trip_id, category, description, quantity, unit_amount_minor, total_amount_minor, currency, cost_type (estimate/quoted/confirmed/actual), source_type, source_id, status.
### dealz_expenses
id, trip_id, payer_user_id, category, amount_minor, currency, occurred_at, note, split_method, created_at.

## 6. Commerce
### dealz_orders
id, public_order_number, traveler_user_id, trip_id nullable, subtotal_minor, fees_minor, discount_minor, total_minor, currency, status, idempotency_key, created_at.
### dealz_bookings
id, public_booking_number, order_id, listing_id, partner_id, service_date/time, quantity, options_snapshot, fulfillment_type, price_snapshot, terms_snapshot, total_minor, currency, status, supplier_reference, created_at.
### dealz_booking_events
id, booking_id, from_status, to_status, actor_type, actor_id, reason, source, created_at.
### dealz_payments
id, order_id, booking_id nullable, provider, provider_reference, amount_minor, currency, status, idempotency_key, verified_at, created_at.
### dealz_refunds
id, payment_id, amount_minor, currency, reason, provider_reference, status, created_at.
### dealz_ledger_entries
id, transaction_group_id, account_code, debit_minor, credit_minor, currency, source_type, source_id, reversal_of_id nullable, created_at.
### dealz_commissions
id, booking_id, partner_id, basis_type, rate_bps nullable, amount_minor, currency, status, approved_at, reversed_at.
Exact commission basis/rate must remain configuration/policy and cannot be invented.

## 7. Platform operations
### dealz_notifications
id, user_id, channel, event_type, payload_safe, status, attempts, sent_at.
### dealz_audit_events
id, actor_type, actor_id, action, target_type, target_id, request_id, safe_metadata, created_at.
### dealz_support_cases
id, customer_id, booking_id nullable, category, status, assigned_to, resolution, created_at, updated_at.

## 8. Relationships
User 1:N Trips; Trip 1:N Itineraries; Itinerary 1:N Days; Day 1:N Items; Itinerary N:1 RouteLegs; Partner 1:N Listings; Listing 1:N Prices/Availability; Order 1:N Bookings; Booking 1:N Events; Order/Booking 1:N Payments; Payment 1:N Refunds; financial events map to ledger entries.

Trip is not Order. Order is not Booking. A Trip may contain multiple independent bookings from different partners.

## 9. Integrity and concurrency
- Checkout revalidates price and availability.
- Prevent overbooking with transaction/locking or atomic capacity update.
- Idempotency key unique within a defined actor/operation scope.
- Provider references unique where guaranteed by provider.
- Ledger entries append-only; corrections use reversal entries.
- Webhook event IDs are deduplicated.
- No status transition without a recorded event.
- Foreign key/index strategy must be verified against selected MySQL version.

## 10. Initial physical MVP schema (implemented as migration v1.0.0)

The additive WordPress `dbDelta` schema is implemented in `wp-content/plugins/dealz-core/src/Database/Schema.php`. It creates these prefixed tables:

| Table suffix | Purpose |
|---|---|
| `dealz_trips` | Traveler-owned trip records |
| `dealz_trip_collaborators` | Trip invitation and role records (not yet exposed through API) |
| `dealz_itineraries` | Versioned manual/AI itinerary containers |
| `dealz_itinerary_days` | Local-date itinerary days |
| `dealz_itinerary_items` | Ordered stop snapshots and optional coordinates |
| `dealz_budget_lines` | Estimated/quoted/confirmed/actual planning lines |
| `dealz_expenses` | Actual trip expense records |

Physical schema decisions:
- Internal primary keys are unsigned BIGINT; public identifiers are UUID strings.
- Money columns are unsigned BIGINT minor units; no floating-point amount columns.
- Date-only trip/day fields use MySQL DATE; local stop start/end use TIME and are interpreted in the trip's destination timezone.
- Created/updated timestamps are UTC values supplied by WordPress.
- Ownership, parent-child and status indexes are present for the first MVP use cases.
- No SQL foreign keys are added in v1 because WordPress `dbDelta` does not provide a reliable cross-version foreign-key migration workflow. Referential integrity and ownership must be enforced by application services; a later migration may add verified constraints after runtime compatibility testing.
- The migration is additive and non-destructive. It does not drop tables or erase data.
- The schema version is stored in the non-autoloaded `dealz_db_version` option and upgraded on plugin activation / WordPress init.
- Schema v1.1.0 adds `item_type` (`stop`, `lunch`, `dinner`) and `status` to itinerary items, plus the `dealz_route_legs` table. Main stops are limited to four per day by application logic; the limit is enforced under a row lock to avoid concurrent requests exceeding it.

Scope note: this first migration does not yet create marketplace, listing, availability, order, booking, payment, refund, ledger, notification or audit tables. Those are deliberately deferred until their domain contracts are frozen.

## 11. Implementation gate
Before migrations: freeze ERD, field types/nullability, enum/state ownership, indexes, deletion policy, retention/privacy policy, availability model and financial policy dependencies.
