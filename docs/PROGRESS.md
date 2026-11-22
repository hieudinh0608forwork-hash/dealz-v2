# DealZ V2 – Progress log

Branch: `work/ui-rental-cta-browser-migration-2026-11-22`  
PR: [#10 – Draft](https://github.com/hieudinh0608forwork-hash/dealz-v2/pull/10)  
Last reconciled: 2026-11-22 (UTC)

> Update this table in every subsequent commit. `Verified` is reserved for acceptance criteria backed by a green CI run on the relevant commit. This log does not authorize merge or deployment.

| Task | Status | Commit | CI run(s) | Next work | Blocker / note |
|---|---|---|---|---|---|
| T0 – Intake / baseline | Verified | [`d48e5caa`](https://github.com/hieudinh0608forwork-hash/dealz-v2/commit/d48e5caa44cd66acdf62a2b30099aa7925748428) | [PHP](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954967926) · [Integration](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954967918) · [Docker](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954967921) · [Browser](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954967914) – all success | Capture/attach baseline screenshots for all six viewports and required screens; verify artifact | CI green; visual baseline evidence not yet inventoried in this log |
| T1 – Schema migration | Verified | [`358abd49`](https://github.com/hieudinh0608forwork-hash/dealz-v2/commit/358abd492df09267b6fd5a31f57127b08e2d2198) | [PHP](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955753376) · [Integration](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955753371) · [Docker](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955753482) · [Browser](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955753383) – all success | CI now exercises failed migration → repaired index → successful retry; checks error option and notice clear, generic admin message hides SQL/path details, and test restores schema in finally | Recovery integration suite passed 25 tests / 164 assertions; all four workflows green on commit 358abd49, including cleanup, admin redaction and finally-based isolation |
| T2 – Design tokens, contrast, fonts | Tested | [`3f550d48`](https://github.com/hieudinh0608forwork-hash/dealz-v2/commit/3f550d481770b7c452f2327f6cd5ef6bd1ad95c3) | [PHP + token/contrast gates](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44958572274) success; Integration, Docker and Browser runs in progress | [PHP](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954590127) · [Integration](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954590118) · [Docker](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954590154) · [Browser](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44954590068) – all success | Exact used-pair contrast matrix and both CI gates pass PHP workflow; local fonts enqueued/preloaded, browser font/glyph/origin test added; full workflow set still pending | Official Google Fonts WOFF2 files + OFL licenses are now imported locally. FontTools proof: Be Vietnam Pro tnum=false; total WOFF2=279,284 bytes (272.74 KiB), above ~220 KiB target. Local WOFF2/OFL assets imported; current commit enqueues local CSS, preloads Vietnamese subsets and adds Playwright glyph/network checks. Known limitations: tnum absent, 272.74 KiB exceeds ~220 KiB; Master Handoff source unavailable |
| T3 – Components + styleguide | Implemented | [`fa7ea843`](https://github.com/hieudinh0608forwork-hash/dealz-v2/commit/fa7ea8430d9f7e7f95a0a1b86bf35fc80f6131ca) | CI triggered by current implementation commit | Styleguide route, tokens, button/input/chip/card/toast/skeleton/empty/ImageSlot/route examples added; verify access/noindex and browser tests | PHP lint found the custom route registration block duplicated/truncated; this commit replaces functions.php with a clean bootstrap and unified route registration. Rerun all four workflows |
| T4 – App shell | Implemented | [`6bc043c2`](https://github.com/hieudinh0608forwork-hash/dealz-v2/commit/6bc043c21b9d058adba5ed3202be156993c1b207) + [`14ca277c`](https://github.com/hieudinh0608forwork-hash/dealz-v2/commit/14ca277c9a9a7987b9c60ea495648dcddc9c3d92) | 14ca PHP + Docker smoke green; browser checks rerunning after 404 fix | Legal draft routes, Zalo settings, global mobile island with inline SVG icons/badge slot, sticky header, skip link, toast and skeleton | Zalo remains hidden unless HTTPS dealz_zalo_url is explicitly configured; three legal drafts contain headings only and are noindex. Mobile nav is global with inline SVG; badge count is now wired by T6; test rerun pending. Current commit adds browser assertions for public drafts and public styleguide denial |
| T5 – Screens | Implemented | [`92f30ee0`](https://github.com/hieudinh0608forwork-hash/dealz-v2/commit/92f30ee07e52e041a2f07bb9effea0849071b4ba) + [`6062b410`](https://github.com/hieudinh0608forwork-hash/dealz-v2/commit/6062b410e4ad6fd5140d431850f30d1cc712f6f5) | CI pending after layout and interaction changes | Home hero/bento, itinerary rail/timeline/form/budget hierarchy, and empty rental two-column screen with context/generic guidance. Desktop/mobile layouts, card archive dialog, stop-anchor navigation and rental two-column empty state implemented; trip date range/day count meta now uses real trip/day data; finance forms collapse into details and their save actions are secondary. Visual QA remains | Real destination photos/licences not supplied; retain honest ImageSlot placeholders; native archive confirmation dialog, labelled edit fields, accessible move controls and stop-anchor navigation added; badge count and viewport screenshot proof remain |
| T6 – Logic | Implemented | [[`84febcc1`](https://github.com/hieudinh0608forwork-hash/dealz-v2/commit/84febcc19edf54dff120955f8d4fce5da677740b) + [`62ddbf96`](https://github.com/hieudinh0608forwork-hash/dealz-v2/commit/62ddbf962884f1842bd11d1b25f8007e63401929) | [PHP](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955967523) · [Integration](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955967515) · [Docker](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955967598) · [Browser](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44955967511) – all success | Badge counts active unarchived stop items across all days of the selected itinerary, excludes lunch/dinner, hides at zero; Playwright asserts 2→2 after lunch→4→3 after archive. Current fix removes duplicate rental context declaration and updates strict robots selector; latest CI pending | Meal types excluded from the four-stop limit in code and integration/browser tests; owner confirmation still requested. Badge is a read-only aggregate, no existing records rewritten |
| T7 – Quality gates | Blocked | [`73c7ddf8`](https://github.com/hieudinh0608forwork-hash/dealz-v2/commit/73c7ddf80647c0707fec36d4dfb0364ceaec369a) | [Browser QA #62](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44959473937) – failure; PHP, Integration, Docker green | axe-core WCAG 2.1 AA, six viewport overflow/44px control matrix, reduced motion, 24-hour time, keyboard focus, admin-bar, CLS, primary CTA and screenshot matrix | Four browser failures remain; inspect logs and correct the loading-state race, WordPress admin-bar false positive, input focus ring and active-screen CTA visibility |
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


## Latest continuation note (2026-11-22)
- Latest pushed head before this note: `008a55287af5829e7a5b140b7b7758e453a9d734`.
- Fixed badge aggregation to use a per-day cache; only missing days are fetched after the active day read, preventing the badge request from racing the loading/error UI tests.
- Styleguide skeleton uses `role=status` with `aria-busy` and `aria-label`, correcting the axe `aria-prohibited-attr` finding.
- Browser QA follow-up fixes: exclude the 27×24px WordPress core admin-bar search input from DealZ control-size assertions; vi-VN 24-hour formatter returns localized `09 giờ`, so the test checks the 24-hour hour prefix; home create-trip submit action is secondary so the hero CTA remains the only primary action.
- Re-run all four workflows after this commit. Do not mark T7 Verified until axe, all six viewports, CLS, primary CTA and screenshot matrix pass on a green Browser QA run.
- Outstanding: no confirmed Master Handoff document; Be Vietnam Pro `tnum` absent and WOFF2 total 272.74 KiB; no real destination photos/licenses or Zalo URL; owner confirmation still needed for meal exclusion and badge definition.


## Reconciliation audit (2026-11-22)
- Actual PR #10 head at audit start: `73c7ddf80647c0707fec36d4dfb0364ceaec369a` (the earlier continuation note named ancestor `008a5528`, not the actual PR head).
- Latest verified workflow set on that head: PHP run [44959473807](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44959473807) success; Integration [44959473761](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44959473761) success; Docker [44959473762](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44959473762) success; Browser [44959473937](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44959473937) failure (8 passed / 4 failed).
- Browser failures on run #62: busy-state test was intercepting a badge aggregation GET before the selected-day list request; viewport matrix included WordPress core `.adminbar-input` (27×24px); keyboard focus on form inputs had `outline: 0`; primary CTA count included the still-visible landing-page hero while itinerary workspace was open.
- This audit supersedes any earlier claim that all follow-up fixes were already present: code inspection found the admin-bar exclusion and per-day badge cache were not in the actual PR head.
- Follow-up implementation is in progress; do not mark T6/T7 Verified until the new head has all four workflows green and Browser QA passes the complete 12-test suite.


## Owner decisions recorded (2026-11-22, part 2)
- `docs/13_HANDOFF_COMPLETION.md` is a process standard, not the Master Handoff. The future Master Handoff path is `docs/MASTER_HANDOFF.md`; it has not yet been authored.
- Four-main-stop cap: count only active, unarchived `Điểm dừng chính`; exclude `Bữa ăn` and `Thẻ nhỏ`; preserve legacy over-limit data and block additions only.
- Itinerary badge: count unarchived stops for the selected trip; hide at zero.
- Be Vietnam Pro remains unchanged. Measure glyph 0–9 advance widths with fontTools; if not tabular, preserve font and use right-aligned min-width columns for time/money.
- Font budget ≤300 KiB; preload only two files; use unicode-range; test removing latin-ext first while retaining latin and Vietnamese glyph coverage.
- Input border: measure on white, cream and mint-50; choose minimum color meeting 3:1 on all three. Prior `#7C948A` values remain provisional until verified against the actual three backgrounds; `#6F887D` is only an estimate.
- Supersedes the earlier owner-input notes: meal exclusion, badge definition and the identity of `13_HANDOFF_COMPLETION.md` are now decided. Zalo URL and licensed destination photography remain owner-supplied.


## Browser QA #63 diagnosis and root-cause fixes (2026-11-22)
- Run [Browser QA QA #63](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44988096279) ran on commit `475cdcf55ebea6481d9cf8020dfd3b1d569ae7f2`; result 9/12 passed, 3 failed. It is NOT the run for any later commit.
- Failure 1: `tests/browser/itinerary.spec.cjs:115` locator matched both the stop title and archive dialog `h3` because the native dialog was nested in the article. Root fix moves the dialog to `document.body`, removes it on close, and asserts top-level placement.
- Failure 2: axe reported serious `color-contrast` on `#create-item-submit`. During async reload it is disabled; opacity 0.6 composited white text against the primary background. Root fix removes disabled opacity and applies mint-100 + muted ink to disabled primary buttons.
- Failure 3: focus outline width 0 because `.item-form select:focus` and `.item-edit-form input:focus` explicitly set `outline: 0`. Root fix removes suppression and adds explicit 3px focus-visible outlines with 3px offset; test checks every itinerary form control against `:focus-visible`.
- No test deleted or relaxed. Re-run required on this new head.


## UI defect C1 – rental bento card (2026-11-22)
- Changed the “Thuê đồ theo hành trình” bento card from a row/wrap flex layout to a vertical label → heading → “Sắp có” chip → description stack, with bounded width and safe wrapping.
- Extended the existing viewport test (no new test removed) to inspect the card and every descendant for `scrollWidth > clientWidth` at 360px and 390px.
- Pending browser CI evidence on the new commit; no visual pass claimed yet.
