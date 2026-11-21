# 06 – API Contract Baseline

## 1. Namespace and conventions
Base path: `/wp-json/dealz/v1`.
JSON request/response; ISO-8601 timestamps; money object `{amount_minor, currency}`; stable public UUIDs; paginated collection; error shape `{code, message, details, request_id}`. Never expose stack traces, secrets or raw provider credentials.

Authentication initially uses WordPress session/cookie + nonce for same-origin web requests. Token auth is a separate decision if a mobile/external client requires it.

Every private endpoint must check capability and resource ownership. Partner endpoints must scope all reads/writes to the active partner organization.

## 2. Public discovery
- GET /destinations
- GET /destinations/{id}
- GET /places/search
- GET /places/{id}
- GET /services
- GET /services/{id}
Filters must be whitelisted, paginated and backed by published/verified data. Distinguish missing/unverified values.

## 3. Account and Trip
- GET/PATCH /me
- GET/POST /trips
- GET/PATCH/DELETE /trips/{id}
- POST /trips/{id}/duplicate
- POST /trips/{id}/share
- DELETE /trips/{id}/share/{share_id}
- GET/POST /trips/{id}/collaborators
- PATCH/DELETE /trips/{id}/collaborators/{collaborator_id}

## 4. Itinerary
- GET/POST /trips/{id}/itineraries
- POST /trips/{id}/itineraries/generate (AI capability only when implemented/configured)
- POST /trips/{id}/itineraries/{itinerary_id}/replan
- POST /trips/{id}/itineraries/{itinerary_id}/items
- PATCH/DELETE /itinerary-items/{item_id}
- POST /trips/{id}/itineraries/{itinerary_id}/reorder
- POST /trips/{id}/itineraries/{itinerary_id}/validate
- GET /trips/{id}/itineraries/{itinerary_id}/routes

Generation returns a preview with source/warnings; persistence occurs only after explicit user acceptance or a separately approved autosave behavior. Schema and provider status must be explicit.

## 5. Budget and expenses
- GET /trips/{id}/budget
- POST /trips/{id}/budget/lines
- PATCH/DELETE /budget/lines/{line_id}
- GET/POST /trips/{id}/expenses
- PATCH/DELETE /expenses/{expense_id}
Server recalculates totals; client totals are display hints only.

## 6. Partner and listings
- GET/PATCH /partner/profile
- GET/POST /partner/listings
- GET/PATCH/DELETE /partner/listings/{id}
- POST /partner/listings/{id}/availability
- GET /partner/bookings
- POST /partner/bookings/{id}/accept
- POST /partner/bookings/{id}/reject
Partner cannot read or modify another partner's resources.

## 7. Quote, order, booking and payment
- POST /bookings/quote
- POST /orders
- GET /orders/{id}
- GET /bookings/{id}
- POST /bookings/{id}/cancel
- POST /payments/intent (only when payment integration is enabled)
- POST /webhooks/{provider}

Quote response must include fulfillment type, source/status of price, availability status, terms snapshot, expiry and known breakdown. Never trust client-supplied totals. Order may group multiple independent bookings; each booking retains its own status.

## 8. AI and external providers
- POST /ai/itinerary/generate
- POST /ai/itinerary/replan
- POST /routes/estimate
- POST /places/resolve
AI and provider endpoints require rate limits, timeout, cost controls, schema validation and safe error handling. AI output cannot set official price, inventory, booking or payment state.

## 9. Security and idempotency
- Validate request schema and allowed fields.
- Verify capability and ownership for every object ID.
- Use nonce/CSRF protection for browser session writes.
- Use idempotency for order/payment creation.
- Webhook signature, event ID, amount, currency and provider reference must be verified.
- Duplicate webhook must not create duplicate state changes or ledger entries.
- Log request ID and safe event metadata.

## 10. Contract freeze checklist
For each endpoint record method/path, request/response JSON Schema, auth, capability, ownership, validation, errors, idempotency, rate limits, provider dependencies, data source and test cases. This document is a broad route inventory; it is not yet a frozen executable OpenAPI spec.
