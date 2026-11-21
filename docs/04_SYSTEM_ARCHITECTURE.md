# 04 – System Architecture

## 1. Architecture decision
Fresh WordPress + custom plugin `dealz-core` + custom theme `dealz-theme` + REST API + MySQL. Start as a modular monolith. Do not modify WordPress core and do not copy V1 implementation.

## 2. Layering
1. Presentation: templates, components, CSS, JS, local fonts/assets in theme.
2. API: REST controllers, schema validation, auth/capability checks, response serialization.
3. Application: use cases/orchestration and transaction boundaries.
4. Domain: entities, value objects, policies, state transitions, calculators.
5. Infrastructure: repositories, DB migrations, jobs, cache, logging.
6. Provider adapters: AI, place search/geocoding, routing, weather, payments, email/maps.

Theme must not own business rules. Controllers must not contain domain calculations. AI adapter must not write booking/payment states.

## 3. Modules
- Identity & Access.
- Destination & Place Catalogue.
- Trip & Collaboration.
- Itinerary & Schedule Validation.
- AI Planner.
- Map/Geocoding/Routing.
- Budget & Expense.
- Service Catalogue & Search.
- Partner & Availability.
- Quote, Order & Booking.
- Payment & Financial Ledger.
- Notification & Support.
- Admin, Analytics & Audit.

## 4. Technology direction
- WordPress for CMS, identity foundation and REST infrastructure.
- PHP custom plugin for business logic.
- HTML/CSS/JavaScript custom theme; choose rendering/build method after environment audit.
- MySQL supported by selected WordPress version.
- WordPress REST API namespace `/wp-json/dealz/v1`.
- MapLibre is a map renderer only; place search/geocoding/routing need separate providers.
- LLM, routing, geocoding, weather, payment and email behind interfaces/adapters.
- WooCommerce is optional; use only if its cart/order primitives fit after technical review. Time-based inventory and supplier approval may require custom booking domain.

## 5. Suggested plugin structure
```
wp-content/plugins/dealz-core/
  dealz-core.php
  src/
    Core/
    Database/
    Domain/
    Repositories/
    Application/
    REST/
    Integrations/AI/
    Integrations/Places/
    Integrations/Maps/
    Integrations/Routing/
    Integrations/Payments/
    Jobs/
    Security/
    Admin/
  tests/
```

## 6. Suggested theme structure
```
wp-content/themes/dealz-theme/
  style.css
  functions.php
  theme.json
  templates/
  parts/
  assets/
    css/
    js/
    fonts/
    images/
```
`functions.php` should register/enqueue theme assets and theme features only. Local WOFF2 font files belong under theme assets/fonts; no remote font requests.

## 7. Data boundaries
- WordPress users/capabilities: identity and login.
- CPT/taxonomies: editorial destinations/categories where suitable.
- Custom tables: relational/high-volume trip, itinerary, availability, booking, payment, ledger and audit data.
- Avoid storing all itinerary/transaction data as one serialized post-meta blob.
- Money: integer VND đồng or defined DECIMAL; never float totals.
- Time: store UTC, render in destination timezone.
- Public IDs: UUIDs; internal DB IDs may be numeric.
- Store immutable service, price and terms snapshots at quote/booking time.

## 8. Provider failure model
Every external provider adapter must define timeout, retry policy, rate limit, quota/cost controls, error mapping, caching, observability and fallback. Do not fake a live integration. Mocks/sandbox must be clearly labelled and cannot be confused with production data.

## 9. Security baseline
Least privilege; partner portal separate from wp-admin; ownership checks at service/repository layer; prepared SQL; sanitize/validate/escape; CSRF/nonce; rate limits; secret management outside repository; signed webhook verification; idempotency; audit trail; safe logs; upload validation; AI output treated as untrusted text.

## 10. Environments
Local → staging with mocks/sandbox → production. Before setup, audit hosting/domain, PHP, WordPress, MySQL, SSL, backups, cron, SMTP, storage, SSH/SFTP/deployment permissions, Composer/build support and environment secret management.

## 11. Decisions not yet frozen
Frontend rendering mode, hosting/runtime versions, provider selection, queue/cache strategy, media storage, optional WooCommerce role, deployment workflow. Resolve via evidence and cost/compatibility review, not assumptions.
