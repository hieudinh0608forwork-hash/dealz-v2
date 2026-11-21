# 07 – Business Rules Baseline

## 1. Confirmed product invariants
- AI suggests; the user chooses.
- Backend is authoritative for prices, availability, order/booking/payment states and totals.
- Trip, Order and Booking are separate.
- One Order may group multiple independent Bookings.
- Each Booking has its own supplier, service, terms and lifecycle.
- No fake partner metrics, fake prices or fabricated availability.
- Do not display shop contact details before DealZ confirmation.
- Do not present unimplemented AI, payment or English support as live functionality.
- No unapproved commission, price proposal or deposit rule is hard-coded.

## 2. Service fulfillment types
- `instant_book`: only if reliable availability and price are verified at checkout.
- `supplier_confirmation`: supplier must accept; state remains pending until accepted.
- `request_only`: enquiry/request, not an instant reservation.
- `external_affiliate`: redirect to external provider; DealZ does not claim to have completed the booking.

The interface and payment flow must match the fulfillment type.

## 3. Price and quote
A quote should disclose base price/unit, quantity/duration, options, known taxes/fees, deposit if approved, discount, due now, balance later, refundable/non-refundable amounts, currency, price source/status and expiry. Store immutable quote/price/terms snapshots with booking. Labels: live, supplier-confirmed, manually entered or estimated.

Backend recalculates all totals. If price or availability changes before confirmation, show the difference and ask the customer to accept again.

## 4. Booking and multi-service trip
A Trip may contain bookings with different suppliers, dates, terms and states. The trip dashboard is an aggregation, not one atomic reservation. Never mark confirmed until the relevant supplier/payment conditions are met. State transitions must be audited.

## 5. Deposit, cancellation, refund and damage
Deposit amount, refundable status, cancellation window, damage deduction, refund SLA and evidence process are service/listing-specific and require approved policy/configuration. No default percentage or deadline may be invented. Refunds are separate transactions; do not edit original payment totals.

## 6. Budget
Estimated itinerary costs, supplier quotes, confirmed booking amounts and actual expenses are different types. Per-person estimates disclose allocation method. Contingency is a planning allowance, not a hidden fee. Budget does not guarantee affordability. Do not infer a user's financial eligibility.

## 7. Revenue accounting
Track separately: gross booking value, amount collected, refunds, refundable deposit liabilities, supplier payable, commission receivable/earned, payment fees and net platform revenue. Refundable deposits are not revenue. Reverse commission when the approved policy requires it. Ledger is append-only with reversal entries.

## 8. Availability
Support multiple models: date-range inventory, time-slot/capacity, manual supplier confirmation, request-only and external affiliate. Do not assume all services have real-time inventory. Prevent overbooking with atomic checks/transactions. Hold duration and release behavior require approved rules.

## 9. Partner operations
Partner onboarding and listing moderation precede public publication. Partner can only access its own organization/listings/bookings. Response deadlines, enforcement and dispute thresholds remain configurable only after owner approval. Contact details remain hidden until DealZ confirmation per design rule.

## 10. Payment safety
Sandbox/manual flow until credentials and webhooks are configured. Verify provider signature, expected amount/currency/order and event identity. Use idempotency. Never trust frontend redirect. Reconcile pending payments.

## 11. Unapproved policy register
Still unresolved: exact commission rates/basis, deposit amounts, cancellation windows, refund SLA, damage deductions, no-show rules, payout schedule, taxes/fees, supplier response deadline, booking hold TTL and dispute enforcement thresholds. Do not code business defaults for these.
