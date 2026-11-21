# 12 – Execution Backlog

Status labels: Planned / In progress / Implemented / Tested / Verified / Deployed. Checkboxes are only completed with evidence.

## Phase 0 – Source & environment audit
- [x] Confirm repository `hieudinh0608forwork-hash/dealz-v2`, Public, branch main.
- [x] Confirm connected GitHub account has admin/push permission.
- [x] Create initial Knowledge Pack.
- [x] Import and apply DealZ design rules dated 20/11/2026.
- [x] Retrieve Master Handoff dated 21/11/2026 from Library.
- [x] Expand product scope and requirements from Master Handoff.
- [x] Add permission, AI, maps/routing, budget/finance, screen inventory, agent workflow and environment audit specifications.
- [x] Add evidence-based handoff completion gates.
- [ ] Inventory supplied theme ZIP, if re-supplied; inspect as reference only.
- [ ] Audit hosting/domain/WordPress/PHP/MySQL/SSL/backup/cron/SMTP/build support.
- [ ] Inventory current supplier data and proof of availability/pricing.
- [ ] Record provider/cost constraints.

## Phase 1 – Product and UX handoff
- [x] Define product roles and broad full-scope direction from Master Handoff.
- [x] Apply itinerary card and visual rules.
- [x] Create initial screen inventory; [ ] complete sitemap and screen-by-screen wireframes.
- [ ] Complete traveler, partner and operations user journeys.
- [ ] Wireframe every MVP screen and all states.
- [ ] Define component inventory and design QA checklist.
- [ ] Freeze MVP vertical slice and acceptance criteria.

## Phase 2 – Data, API and security contracts
- [x] Initial MVP physical schema/migration v1.1.0 for trip planning (8 tables); [ ] full commerce ERD, retention and deletion policy.
- [x] Initial permission matrix for current roles; [ ] executable permission tests and resource-sharing permissions.
- [ ] Booking/Order/Payment state transition table.
- [x] OpenAPI 3.1 contract for health, Trip CRUD and manual itinerary/day/stop endpoints; [ ] expand schemas for budget and commerce endpoints.
- [ ] AI itinerary schema and validation contract.
- [ ] Places/geocoding/routing provider contract.
- [ ] Budget and expense calculation specification.
- [ ] Availability model by service type.
- [ ] Audit, notifications, idempotency and webhook contracts.

## Phase 3 – Fresh WordPress foundation
- [x] Add Docker Compose definition for isolated local WordPress/MySQL; [ ] execute and verify environment.
- [ ] Confirm WordPress/PHP/MySQL compatibility in a running environment.
- [x] Scaffold custom plugin dealz-core (bootstrap, namespace autoloader, capability bootstrap, public non-sensitive health route).
- [x] Scaffold custom theme dealz-theme shell (header/footer/index/theme setup); [ ] add approved local WOFF2 font assets and implement complete visual system.
- [x] Add additive v1.1.0 WordPress dbDelta migration for 8 trip-planning tables; [ ] configure and execute automated WordPress integration test runner.
- [x] Add PHP syntax CI workflow; [ ] add WordPress Coding Standards / static analysis after dependency and runtime compatibility are confirmed.
- [ ] Authentication, profile and capabilities.
- [ ] Destination/place and service catalogue.
- [x] Implement authenticated, owner-scoped Trip CRUD REST endpoints (runtime verification pending).
- [x] Implement manual itinerary version, day and stop/meal-card APIs with ownership checks and 4-main-stop limit (runtime verification pending).

## Phase 4 – Core planning experience
- [x] Add initial responsive manual itinerary workspace wired to APIs; [ ] implement full 16:10 stop cards, sourced route connectors and all exact design-rule details.
- [x] Implement deterministic validation of explicit time overlaps; [ ] route-aware feasibility validation and reorder.
- [ ] Implement route provider abstraction and map UI.
- [ ] Implement budget lines and expense records.
- [ ] Implement save/share/print/PDF only after interaction is functional.
- [ ] Add outside-pilot warning for destinations beyond Da Nang–Hoi An.

## Phase 5 – AI and marketplace
- [ ] AI provider adapter and structured output.
- [ ] Deterministic validation and safe fallback.
- [ ] Service search and service detail.
- [ ] Rental-by-stop flow: filtered rental listing and return link to source stop.
- [ ] Partner onboarding, listing management and availability.
- [ ] Quote and booking lifecycle.

## Phase 6 – Finance and operations
- [ ] Payment adapter in sandbox.
- [ ] Webhook verification and idempotency.
- [ ] Ledger and reconciliation.
- [ ] Deposit/refund/commission/payout only after policy approval.
- [ ] Admin support, audit and notification workflows.

## Phase 7 – QA and release
- [x] Author WordPress integration tests for Trip ownership/date validation and itinerary day/stop-limit ownership; [ ] execute tests in a configured WordPress test environment.
- [ ] Cross-user and cross-partner access tests.
- [ ] Responsive, accessibility, Vietnamese font and visual rule QA.
- [ ] Provider failure, concurrency and webhook replay tests.
- [ ] Backup/restore and rollback rehearsal.
- [ ] Staging acceptance.
- [ ] Production deployment only after explicit owner approval.

## Governance
Do not mark a feature implemented because a document describes it. Do not mark tested without test output. Do not mark verified without evidence in a real environment. Do not deploy production or incur paid API charges without approval.
