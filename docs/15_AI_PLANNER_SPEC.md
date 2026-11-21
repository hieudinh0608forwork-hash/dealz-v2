# 15 – AI Planner Specification

## 1. Product role
AI assists with trip ideation and itinerary drafts. It is not the authority for facts, prices, availability, route durations, booking, payment or financial eligibility. Per design rules, do not expose AI as a live user-facing feature until implementation, provider configuration and tests are complete.

## 2. Inputs
- Origin and destination or discovery intent.
- Start/end dates and destination timezone.
- Traveler count and group composition when voluntarily provided.
- Budget amount/currency.
- Interests and exclusions.
- Preferred transport and travel pace.
- Accessibility or schedule constraints where relevant.
- Existing itinerary and user edits for replanning.

Only collect inputs necessary for planning. Avoid sensitive inference.

## 3. Generation pipeline
1. Validate request and normalize dates/timezone.
2. Retrieve candidate destinations/places from trusted provider or curated database.
3. Build bounded context with source and freshness metadata.
4. Ask model for versioned structured JSON only.
5. Validate JSON schema and allowed fields.
6. Resolve candidate place IDs; reject invented/unresolved IDs.
7. Validate coordinates and source provenance.
8. Run deterministic schedule validator: opening hours when verified, durations, overlap, travel time, pace and day limits.
9. Run deterministic budget estimator using known prices or explicit estimates.
10. Return preview with warnings, assumptions and data confidence.
11. Persist only after user accepts; preserve previous itinerary version and user edits.

## 4. Output schema concept
- schema_version
- trip_summary
- days[]
  - local_date
  - stops[]
    - stop_id
    - place_id nullable
    - title
    - description
    - arrival_local
    - suggested_duration_minutes
    - category
    - address
    - coordinates (only resolved/verified)
    - photo_reference/source
    - photo_angle_suggestion (generic suggestion allowed if no verified data)
    - outfit_style_suggestion
    - estimated_cost {amount_minor, currency, source_status}
    - warnings[]
  - meal_cards[] (small cards, e.g. lunch/dinner)
  - route_legs[] (provider-backed estimates only)
- global_warnings[]
- sources[]
- generated_at

Exact JSON Schema and field constraints must be frozen before implementation.

## 5. Design and itinerary limits
- Maximum 4 main stops/day; current layout target: 3 main stops plus lunch/dinner small cards.
- No numbered circular stop strip.
- Day header shows stop names in horizontal scroll.
- Main stop card uses 16:10 image, arrival time, suggested duration, photo angle, outfit suggestion, "Thuê đồ cho điểm này" and "Chỉ đường".
- Rental action opens style-filtered rental listing and offers "Quay lại [điểm dừng]".
- Route connector uses solid line, motorcycle icon and grounded estimated duration/distance.
- Outside Da Nang–Hoi An pilot region show "ngoài khu thí điểm".

## 6. Safety and reliability
- Model output is untrusted; never execute HTML or code.
- No fabricated live prices, availability, opening hours, coordinates or route metrics.
- If provider fails, preserve user input and offer manual planning; do not display fake AI success.
- Timeouts, rate limits, quota and spend controls are mandatory.
- User can edit, reject, regenerate or remove suggestions.
- AI must not overwrite user edits without explicit confirmation.
- Store prompt/model/version and safe diagnostics, not secrets or unnecessary personal content.

## 7. Evaluation
Test schema validity, place resolution rate, hallucinated place IDs, time overlaps, impossible routes, budget arithmetic, language quality, Vietnamese diacritics, provider timeout, invalid output, prompt injection and user-edit preservation.
