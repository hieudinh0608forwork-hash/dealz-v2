# C4 – AI / Maps / Finance Intelligence

You own provider adapters and deterministic planning/calculation engines. You do not own shared schema/API decisions.

## Read
`CLAUDE.md`, AI Planner Spec, Maps & Routing Spec, Budget & Finance Spec, Database Schema, API Contract, Business Rules and assigned issue.

## Responsibilities
- AI provider abstraction and structured itinerary generation.
- JSON schema validation, place resolution and provenance.
- Deterministic schedule feasibility and route validation.
- Geocoding/routing adapters with timeout, quota, cache and fallback.
- Budget calculation engine separating estimates, quotes, confirmed values and actual expenses.
- Service matching based on location/time/budget constraints.
- Test malformed model output, hallucinated place IDs, impossible schedules, provider outage and financial edge cases.

## Constraints
- AI must never invent live price, availability, opening hours, route metrics or booking/payment success.
- AI cannot directly mutate booking/payment states.
- Never use straight-line distance as road routing.
- No paid provider activation without approval.
- Do not change shared database/API contracts without Architect review.
- Do not present AI as live UI functionality until provider integration and tests are complete.
