# 17 – Budget & Finance Specification

## 1. Keep financial concepts separate
- **Estimated budget:** planning estimate, may be incomplete.
- **Quoted amount:** price offered by supplier/system with expiry and source.
- **Confirmed booking amount:** amount bound to an accepted booking snapshot.
- **Actual expense:** expense entered/recorded after spending.
- **Amount collected:** funds actually received and verified.
- **Supplier payable:** amount owed to partner under approved terms.
- **Refundable deposit liability:** amount potentially owed back; not platform revenue.
- **Commission:** amount earned only under approved contract and recognition rule.
- **Net platform revenue:** calculated after refunds, fees and reversals according to approved accounting policy.

## 2. Trip budget
Categories: transport, accommodation, food/drink, tickets/experiences, services (rental/photo), incidental/contingency. Each line has amount, currency, quantity, unit, type, source, status and optional per-person allocation method.

Totals must be calculated server-side. If mixed currencies appear, do not sum until a supported exchange-rate source and timestamp exist. Per-person amount must state whether split equally or by custom allocation.

## 3. Price provenance
Each price is labelled one of: live provider price, supplier-confirmed, manually entered, estimated, user-entered or unavailable. Store source and timestamp. Quote expiry must be explicit. If no reliable data, show "chưa có giá" rather than a fabricated number.

## 4. Booking breakdown
When supported, show base price, quantity/duration, options, known fees/taxes, deposit (only if approved), discount, due now, balance later, refundable/non-refundable components, currency and quote expiry. Store immutable snapshot.

## 5. Ledger principles
- Append-only financial ledger.
- Corrections through reversal/adjustment entries with references.
- Idempotent event processing.
- Separate customer payment, refund, deposit liability, supplier payable, platform fee, commission and payment processing fee.
- Reconcile internal transactions with provider settlement reports before payout.
- No automatic payout until accounting rules and provider support are verified.

## 6. Unapproved policy
No rate, deposit percentage, cancellation window, refund SLA, damage deduction, supplier payout schedule, tax treatment or platform fee may be invented. Keep these as unresolved configuration until owner approval and legal/accounting review.

## 7. Security
Never trust client totals. Verify provider signature, order reference, amount and currency. Protect payment metadata. No payment success based on browser redirect. Handle duplicate/out-of-order webhooks safely.

## 8. Test cases
Budget sum, per-person split, zero/missing price, currency mismatch, estimate vs actual, quote expiry, changed supplier price, duplicate payment event, partial refund, deposit liability, commission reversal, reconciliation mismatch and ledger reversal.
