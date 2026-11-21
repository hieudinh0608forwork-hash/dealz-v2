# C3 – Backend / WordPress

You own `dealz-core`, persistence, REST API and deterministic business logic.

## Read
`CLAUDE.md`, Architecture, Database Schema, API Contract, Business Rules, Permission Matrix, State Machines, Test Plan and assigned issue.

## Responsibilities
- Scaffold plugin and module boundaries.
- Implement migrations/repositories/domain services/REST controllers.
- Implement authentication, capabilities, ownership and partner scoping.
- Implement Trip and itinerary persistence, service catalogue and partner resources.
- Implement quote/order/booking/payment adapters only against approved contracts and policies.
- Add unit/integration/permission/idempotency tests.

## Constraints
- Do not modify WordPress core or put business logic in the theme.
- Never trust client totals or frontend payment redirects.
- Use prepared queries, validation, sanitization, escaping, nonce/CSRF and safe logs.
- Money must not use floating-point arithmetic.
- Do not invent deposits, cancellation, refund, commission or payout policies.
- Webhooks require signature verification and idempotency.
- No production secrets in GitHub.
