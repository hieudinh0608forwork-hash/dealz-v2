# DealZ V2 – Contributor Instructions

## Source of truth and authority
- Treat `docs/11_DESIGN_RULES.md` (20/11/2026) as the authoritative UI/visual/interaction rulebook. It replaces section 11 of the earlier handoff.
- When a design decision conflicts with that rulebook, follow the rulebook unless the project owner supplies a newer explicit decision.
- The project owner has delegated execution authority: proactively inspect, plan, implement, test, document and maintain the project without asking for approval for routine reversible technical decisions.
- Do not interpret delegation as permission to invent business policies, claim unverified facts, incur paid costs, expose secrets, or deploy production / make irreversible external changes without explicit authorization.
- Decide when a handoff is complete using the completion gates in `docs/13_HANDOFF_COMPLETION.md`. Report the whole delivered scope, exclusions, evidence, known gaps and next steps.

## Project rules
- Read `README.md` and relevant documents in `docs/` before changing code.
- DealZ V2 is independent from V1. Never copy V1 source code, database, theme, plugins or workflows into V2.
- Do not independently change business model, deposits, refunds, commissions or financial policies.
- Do not add paid services, real API keys or secrets to the repository.
- Do not present AI as active, English as available, or payment as available until each feature is implemented and tested.
- Never let AI confirm bookings or payments.
- Keep Trip, Order and Booking as separate entities.
- Backend is the source of truth; frontend does not determine transaction states.
- Prefer a modular monolith; do not introduce microservices prematurely.
- Every endpoint must authenticate, authorize, validate input and log safely.
- Webhooks must verify signatures and handle duplicate events safely.
- Never mark work Tested, Verified or Deployed without actual evidence.
- Never show shop contact details before DealZ confirmation.
- Never fabricate partner portal metrics, prices or commission rates.

## Design requirements
- Follow `docs/11_DESIGN_RULES.md` exactly.
- Route cards: maximum 4 main stops/day; current composition is 3 main stops plus small lunch/dinner cards.
- Never reintroduce numbered circular itinerary strips.
- Use Bricolage Grotesque and Be Vietnam Pro only, with local woff2 files in the theme; no external font requests.
- Use #D63F00 for primary button backgrounds with white text; #FD550A is accent/focus only.
- Preserve exact form, card, navigation, typography, palette and itinerary specifications in the rulebook.
- Clearly label estimates and do not fabricate missing route data.

## Workflow
1. Identify requirement and acceptance criteria.
2. Inspect data/API/access impact.
3. Implement small, testable increments.
4. Run relevant tests and record results.
5. Update documentation and changelog.
6. Report accurate status: Planned, In progress, Implemented, Tested, Verified or Deployed.

## Code conventions
- PHP follows WordPress Coding Standards; prefer namespace `DealZ\\` and `dealz_` prefixes for hooks/functions that need prefixes.
- Keep business logic out of the theme.
- Never hard-code secrets, unapproved business policies or production data.
- Escape output, sanitize/validate input; use capability checks and nonce where appropriate.
- REST APIs require clear schemas/validation and consistent responses.
