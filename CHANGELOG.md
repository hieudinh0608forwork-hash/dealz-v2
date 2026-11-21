# Changelog

## 2026-11-22
- Initialized the DealZ V2 project knowledge pack in the new `dealz-v2` repository.
- Added authoritative design rules from the 2026-11-20 handoff and aligned UX/design system documents.
- Retrieved the 2026-11-21 Master Handoff from the user's Library and aligned product, architecture, data, API, business rules and backlog.
- Added handoff completion gates, permission matrix, AI planner, maps/routing, budget/finance, screen inventory, agent workflow, environment audit, source traceability and four compact agent prompts.
- Expanded product/architecture/data/API/business requirements to reflect the full-scope Master Handoff while preserving the 20/11 UI restrictions.
- Created initial GitHub issues for environment audit, UX handoff, contract freeze and WordPress foundation.
- Confirmed repository is Public, default branch `main`, and connected account has admin/push permissions.
- At that checkpoint, no application code, WordPress environment, runtime test or production deployment had been completed or verified.

- Started implementation: added DealZ Core plugin bootstrap, namespace autoloader, administrator capability bootstrap, non-sensitive health endpoint, non-destructive uninstall handler, and initial custom theme shell.
- Added PHP syntax GitHub Actions workflow. Workflow result has not yet been observed; do not treat it as a passing test.
- Corrected PHP namespace and WordPress REST response type issues during source review.

- Implemented additive WordPress schema migration v1.0.0 for seven trip-planning tables, with table-existence verification before recording schema version.
- Implemented authenticated owner-scoped Trip CRUD REST routes with validation, private-by-default creation and archive-only deletion.
- Added OpenAPI 3.1 contract for health and Trip routes.
- Added Docker Compose local WordPress/MySQL environment definition and local-only .env example; environment has not been executed in this context.
- Added MVP implementation notes and manual runtime acceptance checklist. No runtime test or successful CI run is claimed.

- Extended schema to v1.1.0 with item type/archive state and route-leg table; schema verification order is aligned with the migration statements.
- Implemented owner-scoped manual itinerary version, day and stop/meal-card APIs. Daily main-stop maximum is four and is enforced under a database row lock.
- Added item input validation, archive semantics, and integration test cases for date range, ownership and the fifth-main-stop rejection.
- Added the first responsive traveler home / Trip dashboard screen, wired to the Trip list/create REST API. Local WOFF2 assets remain outstanding; browser behavior is not runtime-verified.
- Expanded OpenAPI 3.1 contract and updated implementation/handoff notes. No passing CI or WordPress runtime test is claimed.

- Added deterministic day schedule validation endpoint. It detects overlapping explicit local time windows and reports missing/invalid ranges without inventing travel times or distances.
- Added WordPress integration test for overlapping stop windows and expanded the OpenAPI contract.

- Connected the day schedule-validation endpoint to the manual itinerary workspace. The UI reports explicit overlap findings and the validator's limitations; it does not claim travel feasibility without routing data.

- Registered the implemented FinanceController in the plugin REST bootstrap; budget and expense routes were present in source but previously were not registered.
- Added finance API integration test cases for server-calculated budget totals, owner scoping, UTC normalization and invalid calendar dates.
- Hardened expense timestamp parsing so invalid calendar dates are rejected rather than silently normalized; Zulu timestamps are normalized before strict parsing.
- Finance responses now serialize monetary minor-unit fields as JSON integers and normalize grouped budget summary values.
- Rebuilt the OpenAPI 3.1 file as a valid, concise contract for the implemented health, Trip, itinerary, budget and expense routes. Added correct partial-update schemas.
- Updated schema documentation to v1.2.0, including the expense archival status column.
- PHP syntax and WordPress integration tests still require an observed CI/runtime run; no passing test result is claimed.

- Added explicit end-time input when creating itinerary cards and inline editing for title, start/end time and duration. Stop cards now display the complete entered time window.
- Extended itinerary integration coverage: lunch/dinner cards do not count toward the four-main-stop limit; archiving a main stop frees a slot; editing an overlapping time window can resolve the schedule conflict.
- Added the initial outside-pilot destination warning for destinations not matching Da Nang or Hoi An.
