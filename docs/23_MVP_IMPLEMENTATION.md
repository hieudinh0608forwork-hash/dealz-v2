# 23 – MVP Implementation Notes

## 1. Current implemented slice

The first implementation slice covers authenticated traveler trip records and manual itinerary construction (itinerary versions, days, stops and meal cards).

### WordPress foundation
- Custom plugin: wp-content/plugins/dealz-core.
- Custom theme shell: wp-content/themes/dealz-theme.
- Versioned schema migration: dealz_db_version = 1.1.0.
- Local environment definition: root compose.yaml and .env.example.

### Database tables created by schema v1.1.0
- {prefix}dealz_trips
- {prefix}dealz_trip_collaborators
- {prefix}dealz_itineraries
- {prefix}dealz_itinerary_days
- {prefix}dealz_itinerary_items
- {prefix}dealz_budget_lines
- {prefix}dealz_expenses

Trip, itinerary, day and item tables are used by the implemented APIs. Collaborator, route-leg, budget-line and expense tables remain schema foundations; their APIs and business logic are not yet implemented.

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
| GET / POST | /days/{public_id}/items | List or add stops / lunch / dinner cards |
| PATCH / DELETE | /items/{public_id} | Update or archive an owned item |

A day can contain at most four active main stops (item_type=stop). Lunch and dinner are separate small-card types. The main-stop limit is checked under a day-row lock to protect against concurrent creates. Item deletion is archival, not physical deletion. Route-leg records are a schema foundation only; no routing provider is connected and no travel time/distance is fabricated.

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
- [ ] Seven expected custom tables exist with the active WordPress prefix.
- [ ] dealz_db_version equals 1.0.0 only after all tables exist.
- [ ] Health route returns 200 and application_ready: false.
- [ ] Anonymous Trip API requests are rejected.
- [ ] Authenticated user A can create, list, read, update and archive their trip.
- [ ] Authenticated user B cannot read, update or archive user A's trip, even with its UUID.
- [ ] Invalid dates, reversed date ranges, invalid timezone, invalid currency and invalid traveler count return 400.
- [ ] Archived trips no longer appear in list or detail responses.
- [ ] Repeated plugin activation does not delete data or duplicate schema.
- [ ] Theme activates and renders without fatal errors.

## 4. Known limitations

- No automated WordPress integration test runner yet.
- No verified successful CI run is recorded yet.
- No runtime test has been performed because Docker is unavailable in the current execution environment.
- Initial responsive traveler home and trip dashboard screen is implemented in the theme and connected to Trip list/create API; visual/runtime behavior has not been verified.
- Manual itinerary/day/stop APIs are implemented; route calculation, maps provider, AI, service marketplace, partner portal, booking and payment are not implemented.
- Collaborator, itinerary, budget and expense tables are currently unused foundations.
- No production hosting or deployment is configured.

## 5. Next build sequence

1. Execute the local environment and fix migration/runtime issues.
2. Add WordPress integration tests for ownership and validation.
3. Add itinerary/day/stop CRUD with owner checks and versioning.
4. Add deterministic itinerary schedule validation and reorder.
5. Add budget line and expense APIs with server-side calculations.
6. Implement the approved itinerary UI and responsive states.
7. Add verified destination/place catalogue and provider-backed routing only after provider and source decisions are resolved.
