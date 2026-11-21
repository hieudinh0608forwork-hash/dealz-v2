# 18 – Screen Inventory

This is the screen-level handoff baseline. Detailed wireframes and acceptance checks are a subsequent deliverable. Every visible control must work or be disabled/omitted honestly.

## Public / guest
1. Home: value proposition, destination discovery, search entry, service categories, trust/footer.
2. Destination discovery: filters, cards, location/pilot status.
3. Destination detail: editorial content, verified places, services and next-step CTA.
4. Service discovery: category, location, date, price status, availability status.
5. Service detail: supplier identity, media, service options, fulfillment type, terms, price provenance, CTA.
6. Login/register/reset: labels, validation, password visibility, system date picker where relevant.

## Traveler
7. Traveler dashboard: trips, recent activity, empty state with one useful action.
8. Create trip: destination/origin, dates, traveler count, optional budget/preferences.
9. Trip overview: dates, destination, status, next action.
10. Itinerary day view: horizontally scrollable stop-name strip; stop cards; small meal cards; route connectors; save/share/print/PDF when functional.
11. Stop detail: arrival, suggested duration, photo angle suggestion, outfit suggestion, rent-for-this-stop and Google Maps directions.
12. Rental results: prefiltered by style; return link to originating stop.
13. Map view: synchronized marker/list, day filter, route estimates and no-data state.
14. Budget view: estimates, confirmed bookings, actual expenses, category breakdown and per-person method.
15. Expense entry: amount, category, date, payer, note.
16. Share/collaborate: view/comment/edit permissions, expiry/revoke.
17. Booking/order dashboard: each booking's supplier, terms and independent state.
18. Profile/preferences: user-controlled preferences and privacy.

## Partner
19. Partner onboarding/profile: review state and required business information.
20. Partner dashboard: only real metrics; empty state until actual transactions exist.
21. Listing manager: create/edit, category, media, price basis, terms and moderation.
22. Availability manager: date range/slot/capacity/manual request.
23. Booking requests: accept/reject with confirmation and audit.
24. Partner finance: only verified own booking/settlement data; no fabricated metrics.

## Operations/Admin
25. Admin dashboard: actual system metrics and alerts.
26. Partner moderation.
27. Listing moderation.
28. Destination/place management and source verification.
29. Booking exception/support queue.
30. Payment/refund/ledger/reconciliation views when finance module is enabled.
31. User/role/audit management.
32. Integration settings/health; secrets never exposed.

## Global states and behaviors
- Loading/skeleton, empty, error, success, disabled and stale-data states.
- Keyboard focus, accessible labels, form validation and Vietnamese diacritic QA.
- Mobile bottom navigation max 5 items with icon + label; Journey count where applicable.
- Each page has a distinct route/address so browser Back works.
- Customer-facing pages end with "Tiếp theo".
- Add-to-trip toast includes "Xem hành trình".
- Footer includes deposit/cancellation policy, terms, privacy and Zalo; floating Zalo action.
- Do not show AI, English or payment as live capabilities before implementation and tests.
- Do not show shop contact before DealZ confirmation.
- No fake partner portal numbers.
