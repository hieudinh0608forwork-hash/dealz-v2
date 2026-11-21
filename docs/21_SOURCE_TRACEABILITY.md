# 21 – Source Traceability & Precedence

## 1. Sources reviewed
1. **Master Handoff – DealZ V2**, dated 21/11/2026, retrieved from the user's Library as `Pasted text(20261121-192828).txt`.
2. **DealZ – Quy tắc thiết kế**, dated 20/11/2026, uploaded in this conversation as `DealZ_Quy_tac_thiet_ke.md`.
3. **Earlier DealZ V2 Project Knowledge Pack**, dated 19/11/2026, retrieved from the user's Library as `Pasted text(20261119-154453).txt`.
4. **Agent workflow / 12-week plan**, dated 19/11/2026, retrieved from the user's Library as `Pasted text(20261119-161031).txt`.
5. Current user instruction in this conversation: full autonomy for routine project work; coordinator decides when handoff is complete and then describes the whole delivery.
6. Current GitHub repository metadata and repository files, verified 22/11/2026.

## 2. Precedence
- Current explicit user instructions override older project notes.
- The 20/11/2026 Design Rules explicitly replace section 11 of the earlier handoff and are authoritative for visual/UI/interaction rules.
- The 21/11/2026 Master Handoff defines product scope, architecture direction, workstreams, role boundaries and autonomy constraints.
- Earlier Knowledge Pack content is supporting context. Where it differs from the newer handoff or design rules, use the newer source and record the discrepancy.
- Technical recommendations in this repository are proposals until validated against hosting/runtime, provider terms, cost and operational evidence.
- Business/legal/financial policies remain unresolved unless explicitly approved in source material.

## 3. Reconciled decisions
- DealZ V2 is a new, independent project; do not import V1 code/database/theme/plugins.
- Fresh WordPress installation; custom plugin `dealz-core`; custom theme `dealz-theme`; modular monolith; no WordPress core edits.
- Full product direction: travel planning, itinerary, maps/routing, budget/expenses, service marketplace, partner portal, booking/order, payment/finance, operations, notifications and analytics.
- Initial design pilot geography: Đà Nẵng – Hội An; other locations display "ngoài khu thí điểm".
- Itinerary: max 4 main stops/day; current target is 3 main stops plus small lunch/dinner cards; never use numbered circular stop strip.
- Rental action from a stop opens a style-filtered rental page and offers a return link to the originating stop.
- Exact font, color, control, card and navigation rules are in `docs/11_DESIGN_RULES.md`.
- AI, payment and English are product capabilities/roadmap, but must not be presented as live UI features before implemented and tested.
- Never show shop contact before DealZ confirmation; never fabricate partner portal metrics.
- Trip, Order and Booking are separate; multi-service bookings remain independently fulfilled.
- No unapproved deposits, cancellations, refunds, commission, payout or supplier SLA defaults.

## 4. Discrepancies and resolution
| Topic | Older note | Newer authoritative rule | Resolution |
|---|---|---|---|
| UI font | Generic sans-serif | Bricolage Grotesque + Be Vietnam Pro only | Follow Design Rules |
| Primary button | #FD550A | #D63F00 with white text; #FD550A accent/focus only | Follow Design Rules |
| Placeholder | Generic neutral | #7C948A | Follow Design Rules |
| Secondary text | Generic/TBD | #4C635A | Follow Design Rules |
| Itinerary navigation | Older numbered/other layouts may exist | No numbered circular strip | Prohibited |
| Route display | Generic map line | Solid connector + motorcycle + sourced estimate | Follow Design Rules; no invented metrics |
| AI/payment/English | Included in full-scope roadmap | Must not appear as live until built/tested | Keep in roadmap, hide/disable until real |
| Partner dashboard | Analytics envisioned | No fake metrics | Empty states until actual data |
| Repository | Older plan had private repo / Google Drive workflows | User selected current Public `dealz-v2` repository | Current repo is source of code/docs for this phase |
| Theme ZIP | Previously reported uploaded | Not available to inspect in current workspace | Do not assume access; ask only if required |

## 5. Assets and evidence
- Design Rules Markdown is available and has been transcribed into `docs/11_DESIGN_RULES.md`.
- Master Handoff text is available in the Library and has been reviewed.
- A prior handoff records a theme ZIP named `dealz-theme-3.9.5 (1).zip`, but the ZIP itself is not currently available for inspection.
- GitHub repository is Public, branch `main`, connected account has admin/push permission.
- No WordPress environment, code runtime, supplier inventory, provider credentials or deployment evidence has been verified.

## 6. Change control
Any newer explicit design rule or Master Handoff revision must be added to this traceability document with date, source, changed decisions and affected docs. Do not silently overwrite conflicts.
