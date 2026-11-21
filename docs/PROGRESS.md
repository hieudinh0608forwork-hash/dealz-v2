# DealZ V2 – Progress log

Branch: `work/ui-rental-cta-browser-migration-2026-11-22`  
PR: [#10 – Draft](https://github.com/hieudinh0608forwork-hash/dealz-v2/pull/10)  
Last reconciled: 2026-11-22 (UTC)

> Update this table in every subsequent commit. `Verified` is reserved for acceptance criteria backed by a green CI run on the relevant commit. This log does not authorize merge or deployment.

| Task | Status | Commit | CI run(s) | Next work | Blocker / note |
|---|---|---|---|---|---|
| T0 – Intake / baseline | Verified | [`d48e5caa`](https://github.com/hieudinh0608forwork-hash/dealz-v2/commit/d48e5caa44cd66acdf62a2b30099aa7925748428) | [PHP](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954967926) · [Integration](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954967918) · [Docker](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954967921) · [Browser](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954967914) – all success | Capture/attach baseline screenshots for all six viewports and required screens; verify artifact | CI green; visual baseline evidence not yet inventoried in this log |
| T1 – Schema migration | Verified | [`358abd49`](https://github.com/hieudinh0608forwork-hash/dealz-v2/commit/358abd492df09267b6fd5a31f57127b08e2d2198) | [PHP](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955753376) · [Integration](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955753371) · [Docker](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955753482) · [Browser](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955753383) – all success | CI now exercises failed migration → repaired index → successful retry; checks error option and notice clear, generic admin message hides SQL/path details, and test restores schema in finally | Recovery integration suite passed 25 tests / 164 assertions; all four workflows green on commit 358abd49, including cleanup, admin redaction and finally-based isolation |
| T2 – Design tokens, contrast, fonts | Implemented | [`ffd61aed`](https://github.com/hieudinh0608forwork-hash/dealz-v2/commit/ffd61aedbbae6ccff98100aa0095d84382e91e3a) | [PHP](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954590127) · [Integration](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954590118) · [Docker](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954590154) · [Browser](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954590068) – all success | T2 token and contrast matrix implemented; local fonts enqueued, two Vietnamese subsets preloaded, Playwright font/glyph/origin test added; review CI before marking Tested | Official Google Fonts WOFF2 files + OFL licenses are now imported locally. FontTools proof: Be Vietnam Pro tnum=false; total WOFF2=279,284 bytes (272.74 KiB), above ~220 KiB target. Local WOFF2/OFL assets imported; current commit enqueues local CSS, preloads Vietnamese subsets and adds Playwright glyph/network checks. Known limitations: tnum absent, 272.74 KiB exceeds ~220 KiB; Master Handoff source unavailable |
| T3 – Components + styleguide | Implemented | [`fa7ea843`](https://github.com/hieudinh0608forwork-hash/dealz-v2/commit/fa7ea8430d9f7e7f95a0a1b86bf35fc80f6131ca) | CI triggered by current implementation commit | Styleguide route, tokens, button/input/chip/card/toast/skeleton/empty/ImageSlot/route examples added; verify access/noindex and browser tests | PHP lint found the custom route registration block duplicated/truncated; this commit replaces functions.php with a clean bootstrap and unified route registration. Rerun all four workflows |
| T4 – App shell | Implemented | `14ca277c` | CI pending | Legal draft routes, Zalo settings, global mobile island with inline SVG icons/badge slot, sticky header, skip link, toast and skeleton | Zalo remains hidden unless HTTPS dealz_zalo_url is explicitly configured; three legal drafts contain headings only and are noindex. Mobile nav is global with inline SVG; badge count is awaiting T6. Current commit adds browser assertions for public drafts and public styleguide denial |
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
