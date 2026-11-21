# DealZ V2 – Progress log

Branch: `work/ui-rental-cta-browser-migration-2026-11-22`  
PR: [#10 – Draft](https://github.com/hieudinh0608forwork-hash/dealz-v2/pull/10)  
Last reconciled: 2026-11-22 (UTC)

> Update this table in every subsequent commit. `Verified` is reserved for acceptance criteria backed by a green CI run on the relevant commit. This log does not authorize merge or deployment.

| Task | Status | Commit | CI run(s) | Next work | Blocker / note |
|---|---|---|---|---|---|
| T0 – Intake / baseline | Verified | [`d48e5caa`](https://github.com/hieudinh0608forwork-hash/dealz-v2/commit/d48e5caa44cd66acdf62a2b30099aa7925748428) | [PHP](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954967926) · [Integration](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954967918) · [Docker](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954967921) · [Browser](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954967914) – all success | Capture/attach baseline screenshots for all six viewports and required screens; verify artifact | CI green; visual baseline evidence not yet inventoried in this log |
| T1 – Schema migration | Tested | [`134c5d62`](https://github.com/hieudinh0608forwork-hash/dealz-v2/commit/134c5d62d1e5b4c1e064c31e7d894bf8b759b30c) | [PHP](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955753376) · [Integration](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955753371) · [Docker](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955753482) · [Browser](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955753383) – all success | Verify later successful migration clears error option/notice; escape and redact admin error; isolate test state/order | Cleanup behavior, output escaping, and test isolation still require explicit review/tests |
| T2 – Design tokens, contrast, fonts | Implemented | [`2c681566`](https://github.com/hieudinh0608forwork-hash/dealz-v2/commit/2c6815664ff5d5360ab72790cf5f37e0e1b33b8f) | [PHP](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954590127) · [Integration](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954590118) · [Docker](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954590154) · [Browser](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954590068) – all success | Implement requested exact contrast matrix, automated contrast+hex gates, licensed local WOFF2 fonts and Playwright font proof | Existing contrast doc is incomplete; no confirmed local WOFF2 font assets/test evidence |
| T3 – Components + styleguide | Planned | — | — | Build component states and admin-only noindex styleguide | Depends on T2 foundation |
| T4 – App shell | Planned | — | — | Header, mobile nav, footer, legal drafts, Zalo option, skip link, toast, skeleton | No Zalo URL supplied; keep Zalo hidden until configured |
| T5 – Screens | Planned | — | — | Home, itinerary, empty rental screen against brief | Real destination photos/licences not supplied; retain honest placeholders |
| T6 – Logic | Implemented | [`84febcc1`](https://github.com/hieudinh0608forwork-hash/dealz-v2/commit/84febcc19edf54dff120955f8d4fce5da677740b) | [PHP](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955967523) · [Integration](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955967515) · [Docker](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955967598) · [Browser](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955967511) – all success | Finish badge count, verify UI behavior and QA-fixture isolation; keep 4-main-stop API/UI guard | Meal types currently excluded from the four-stop limit in code/tests; owner confirmation still requested |
| T7 – Quality gates | Planned | — | — | Add required viewport, overflow, type size, target size, axe, keyboard/focus, font/network, primary CTA, locale/time, admin bar, reduced motion, CLS and flow tests | Full acceptance suite not yet evidenced |
| T8 – Self-review | Planned | — | — | Score 10 premium criteria with screenshot evidence; iterate up to three rounds | Cannot score before T3–T7 evidence exists |

## Source intake notes
- Read `docs/UI_BRIEF.md` and `docs/DECISIONS.md` before this log was created. `DECISIONS.md` wins over conflicting brief details.
- `docs/PROGRESS.md` did not exist before this commit.
- `docs/13_HANDOFF_COMPLETION.md` exists but remains unapproved as Master Handoff; do not use it as an implementation source until owner confirmation.
- Earlier required paths `docs/HANDOFF.md` and `docs/design-rules.md` are not present under those exact names; current design rules are `docs/11_DESIGN_RULES.md`.
- PR #10 was confirmed open and Draft; no merge or deployment performed.

## Current unresolved owner inputs
- Zalo URL (only if/when `dealz_zalo_url` should be configured).
- Real destination photos and explicit usage licences/attributions.
- Confirm the itinerary mobile badge definition: number of unarchived stops in the selected trip.
- Confirm whether lunch/dinner meal cards are excluded from the four-main-stop daily limit; current code excludes them.
- Confirm whether `docs/13_HANDOFF_COMPLETION.md` is the Master Handoff.

## Verification boundary
- Green CI is evidence only for the commit and workflows linked above; it does not establish Safari, Firefox, physical-device, production or whole-system verification.
- All later commits must update this file in the same commit and push immediately.
