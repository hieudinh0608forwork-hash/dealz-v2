# 23 – MVP Implementation Notes

## 1. Current implemented slice

The first implementation slice covers authenticated traveler trip records and manual itinerary construction (itinerary versions, days, stops and meal cards).

### WordPress foundation
- Custom plugin: wp-content/plugins/dealz-core.
- Custom theme shell: wp-content/themes/dealz-theme.
- Versioned schema migration: dealz_db_version = 1.2.0.
- Local environment definition: root compose.yaml and .env.example.

### Database tables created by schema v1.2.0
- {prefix}dealz_trips
- {prefix}dealz_trip_collaborators
- {prefix}dealz_itineraries
- {prefix}dealz_itinerary_days
- {prefix}dealz_itinerary_items
- {prefix}dealz_route_legs
- {prefix}dealz_budget_lines
- {prefix}dealz_expenses

Trip, itinerary, day and item tables are used by the implemented APIs. Collaborator and route-leg tables remain schema foundations; budget-line and expense tables are used by the finance APIs.

### Implemented REST routes

| Method | Route | Behavior |
|---|---|---|
| GET | /trips | List up to 100 non-archived trips owned by current user |
| POST | /trips | Create a private draft trip |
| GET | /trips/{public_id} | Read a trip owned by current user |
| PATCH | /trips/{public_id} | Update allowed trip fields |
| DELETE | /trips/{public_id} | Archive an owned trip; never hard-delete |

All routes require an authenticated WordPress user. Queries scope by both public ID and current owner ID. A different user's trip is indistinguishable from a missing trip and returns 404.

### Input validation currently covered
- Title is required on create and limited to 190 characters.
- Origin text is limited to 255 characters; destination label to 190.
- Dates must be real calendar dates in YYYY-MM-DD format.
- End date cannot precede start date, including partial updates.
- Destination timezone must be recognized by PHP DateTimeZone.
- Traveler count must be 1–50.
- Budget must be a non-negative integer minor-unit amount or null.
- Currency must be a three-letter code.
- Unknown request fields are ignored; only the explicit allowlist is persisted.

### Manual itinerary endpoints

| Method | Route | Behavior |
|---|---|---|
| GET / POST | /trips/{trip_public_id}/itineraries | List or create manual itinerary versions |
| GET / POST | /itineraries/{public_id}/days | List or add days within trip date range |
| GET | /days/{public_id}/validation | Validate explicit local-time overlaps without estimating travel time |
| GET / POST | /days/{public_id}/items | List or add stops / lunch / dinner cards |
| POST | /days/{public_id}/reorder | Atomically replace the complete active item order |
| PATCH / DELETE | /items/{public_id} | Update or archive an owned item |
| GET | /trips/{trip_public_id}/budget | List active budget lines and grouped totals |
| POST | /trips/{trip_public_id}/budget/lines | Add a budget line; server calculates its total |
| PATCH / DELETE | /budget/lines/{public_id} | Update or archive an owned budget line |
| GET / POST | /trips/{trip_public_id}/expenses | List or record actual expenses |
| PATCH / DELETE | /expenses/{public_id} | Update or archive an owned expense |

A day can contain at most four active main stops (item_type=stop). Lunch and dinner are separate small-card types. The main-stop limit is checked under a day-row lock to protect against concurrent creates. Item deletion is archival, not physical deletion. Route-leg records are a schema foundation only; no routing provider is connected and no travel time/distance is fabricated. A deterministic validator reports overlaps only where user-entered start/end times or duration are available.

## 2. Local environment

The Docker Compose file defines:
- WordPress using the PHP 8.2 Apache image, exposed at port 8080.
- MySQL 8.4, not exposed directly to the host.
- Persistent named volumes for WordPress and MySQL.
- Health-gated WordPress startup after MySQL is healthy.
- Bind mounts for the DealZ plugin and theme to support local code iteration.

The environment is a development convenience only. The sample .env.example credentials must never be reused in staging or production.

## 3. Manual runtime acceptance checklist

These checks must be performed after starting the compose environment and activating the plugin. They have not yet been run.

- [ ] Fresh WordPress install completes.
- [ ] DealZ Core activates without PHP fatal errors.
- [ ] Eight expected custom tables exist with the active WordPress prefix.
- [ ] dealz_db_version equals 1.2.0 only after all tables and required item/expense columns exist.
- [ ] Health route returns 200 and application_ready: false.
- [ ] Anonymous Trip API requests are rejected.
- [ ] Authenticated user A can create, list, read, update and archive their trip.
- [ ] Authenticated user B cannot read, update or archive user A's trip, even with its UUID.
- [ ] Invalid dates, reversed date ranges, invalid timezone, invalid currency and invalid traveler count return 400.
- [ ] Archived trips no longer appear in list or detail responses.
- [ ] Repeated plugin activation does not delete data or duplicate schema.
- [ ] Theme activates and renders without fatal errors.

## 4. Known limitations (audited 2026-11-22)

- GitHub Actions is not pending: the current `main` commit `90e5298cf530595a6b6dddeab83d92deed290e9c` passed WordPress integration run [44947029698](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44947029698) with **15 tests / 71 assertions**, using the WordPress 6.8 test library and MySQL 8.0 service. The PHP syntax workflow [44947029701](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44947029701) also passed PHP syntax, JavaScript syntax and OpenAPI validation (14 paths, 24 operations, all internal references resolved).
- The CI run is not a local environment acceptance test. The Docker Compose stack has not been started in a verified local environment; fresh WordPress install, plugin activation, theme rendering and browser acceptance remain unverified.
- The current theme contains an initial traveler home, Trip dashboard and manual itinerary workspace connected to the implemented REST slice. Stop cards support title/time/duration editing and transactional up/down reordering; budget/expense screens support initial create/list/archive flows. Browser interaction and responsive visual behavior have not been verified.
- The required licensed local WOFF2 files are not present. `assets/fonts/README.md` documents the required files and the current system-font fallback. Do not substitute remote Google Fonts or invent font assets.
- The full approved itinerary UI is incomplete: 16:10 stop photography, approved card details/actions, sourced route connectors, map synchronization, save/share/print/PDF and complete interaction states remain open.
- No map, geocoding, place catalogue, routing, AI LLM, weather, supplier inventory, availability synchronization, marketplace, partner portal, booking lifecycle, checkout, payment gateway, ledger/payout or production notifications are connected.
- `dealz_trip_collaborators` and `dealz_route_legs` are schema foundations only. Do not expose them as functioning sharing or route features.
- Hosting, domain, staging and production deployment are not configured or verified. No real supplier, price, availability or transaction data is available in this repository audit.

## 5. Next build sequence

1. Obtain the approved, redistributable Bricolage Grotesque and Be Vietnam Pro WOFF2 assets; add local font-face definitions only after licensing/source verification.
2. Execute the documented Docker Compose environment and complete the manual runtime acceptance checklist in this document.
3. Perform browser-based traveler home, Trip, itinerary and finance acceptance across desktop/tablet/mobile, including keyboard, focus, empty/error/loading/disabled states and long content.
4. Complete the approved itinerary stop-card UI and its rental-by-stop/navigation contract; keep route estimates absent until a routing provider and data source are approved.
5. Add a verified destination/place catalogue and provider-backed routing only after provider, licensing, attribution and cost decisions are resolved.
6. Continue MVP commerce foundation (service catalogue, partner access, availability and booking) only after the relevant schema/API/state contracts are reconciled; do not invent commercial policy.
7. Keep payment integration sandbox-only until credentials and business/legal policies are explicitly approved.
