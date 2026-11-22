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


## UI defect C2 – route screen isolation (2026-11-22)
- Added explicit route-state visibility and `inert` handling for Explore, Trips, selected itinerary and Rental screens. CSS-only hiding is no longer the state mechanism; hidden screens are also removed from keyboard navigation.
- Canonical selected-itinerary route is now `#route?trip=<uuid>`; legacy `#trip/<uuid>` remains readable. Rental return URLs preserve the selected route; `#route` without a trip resolves to the Trips list. `#trips` and `#explore` resolve to their own screens.
- Browser scenarios now assert visible screen, hidden/inert sibling screens and zero enabled focusable descendants in hidden screens across itinerary, rental, trips and explore navigation. Existing flow remains covered; URL expectation changed because the canonical route was introduced.
- Pending CI proof; no route isolation pass claimed yet.


## UI defect C3 – explicit 24-hour time fields (2026-11-22)
- Replaced native `type=time` controls with masked text inputs (`inputmode=numeric`, strict HH:MM pattern, max length 5) in both create and inline edit forms. The input mask normalizes digits to HH:MM and validates 00:00–23:59; no locale-specific AM/PM widget can appear.
- Kept unset time as the explicit card label “Chưa đặt giờ”; added browser assertion for this state.
- Browser QA now exercises the field in separate `en-US` and `vi-VN` contexts, asserting the input type, mask and stable `09:30` value.
- Pending CI evidence on this commit.

- Follow-up inspection caught over-escaping in the first HH:MM mask commit; corrected the JavaScript digit and hour regex literals in this commit. This is a source correction, not a test change.


## UI defect C4 – clean-install footer and Zalo (2026-11-22)
- The footer Hỗ trợ/Zalo column now renders only when the sanitized `dealz_zalo_url` option contains an HTTPS URL. With no configured URL, the entire column is absent; the floating button was already conditional and remains absent.
- Browser test for the clean unauthenticated installation now asserts no footer support column, no floating Zalo action, and no user-facing “cấu hình” or “QA” copy. Existing legal footer links remain.
- No URL or contact detail was invented. Pending CI evidence.


## UI defect C5 – hero heading orphan prevention (2026-11-22)
- Hero heading now uses a 20ch measure and `text-wrap: balance`; this avoids forcing the final “bạn.” onto a one-word line at desktop widths while preserving the approved wording.
- The existing six-viewport Playwright matrix now measures word line positions and requires at least two words on the final line at each viewport (360, 390, 768, 1024, 1280, 1536). No screenshot-only judgment substituted for the assertion.
- Pending CI evidence.

- Corrected the test's whitespace-token regex literal after source review; the test now tokenizes visible hero words rather than matching a literal backslash. No assertion was relaxed.


## UI defect C6 – compact empty ImageSlot (2026-11-22)
- Rebuilt the empty stop ImageSlot as a compact forest-to-ink gradient with faint inline contour SVG, lime map-pin SVG, Bricolage 700 stop name and “Chưa có ảnh đã xác minh” status aligned at the bottom.
- Preserved `role="img"` and added the exact accessible label “Chưa có ảnh cho [tên điểm dừng]”. Desktop minimum height is 144px; mobile minimum height is 112px. No stock or AI image is used.
- Mobile Browser QA now asserts the role, label, title, status, pin and computed minimum height. Pending CI and desktop measurement.


## UI defect C7 – reduce nested mobile card framing (2026-11-22)
- At mobile widths up to 767px, the outer `#day-items-panel` loses redundant padding, border, radius and background while retaining the stop-card component and its own border/padding. Stop cards use 16px internal padding.
- Mobile Playwright at 390px measures the first stop card's actual content box (border and padding excluded) and requires at least 320px. No itinerary or API behavior changed.
- Pending CI evidence.


## FontTools digit-width measurement gate (2026-11-22)
- Added `scripts/check-font-metrics.py`; the PHP syntax workflow installs the development-only `fonttools` + `brotli` packages and measures Be Vietnam Pro 400 Latin WOFF2 `hmtx` advance widths for digits 0–9, reporting units-per-em, each width and whether all ten are equal.
- The PHP syntax workflow has NO `paths` filter: `.github/workflows/php-lint.yml` runs on pushes to `main` and pull requests targeting `main`. The new measurement step therefore runs on PR #10 CI.
- Await the next PHP workflow log before recording the measured widths or deciding whether the UI needs min-width/right-aligned numeric columns. Font family remains unchanged.


## Input-border contrast decision verified (2026-11-22)
- Recalculated `#7C948A` on white/cream/mint-50: 3.25079:1 / 3.04038:1 / 3.00095:1. Nearby lightened candidates `#7D958B` (2.96252:1) and `#7C948B` (2.99825:1) fail on mint-50. Retained `--dealz-input-border: #7C948A`; no token change.
- Updated `docs/DECISIONS.md`, `docs/design-contrast.md`, and `docs/11_DESIGN_RULES.md` together.


## Font subset optimization (2026-11-22)
- Removed five latin-ext WOFF2 subsets after a source scan of 12 theme files found zero codepoints requiring latin-ext but not covered by Latin or Vietnamese. Added `scripts/check-font-subsets.py` to reproduce this check in PHP syntax CI.
- Kept Latin + Vietnamese subsets and all required weights. Removed files total 77,852 bytes; payload falls from 279,284 bytes (272.74 KiB) to 201,432 bytes (196.71 KiB), under 300 KiB. `fonts.css` still uses unicode-range; theme preload remains exactly two files.
- Browser font test now checks the new payload, ≤300 KiB budget and absence of latin-ext. The six already-uncovered decorative symbols remain on fallback fonts; no regression introduced by this subset removal.


## Browser QA route-fixture correction (2026-11-22)
- Browser run [#44989482181](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44989482181) ran on commit `c5381f07ddcf31eed873a18dc27ba2a2feb1015c` and failed 7/12 tests. The failures all timed out filling `#trip-title` because the new route model correctly hides `#trips` on the default Explore route, while `createTripAndDay()` still assumed the trip form was visible immediately after login.
- Root fix: the test setup now explicitly navigates to `/#trips` and asserts the create-trip panel is visible before creating its fixture. This aligns test setup with the intended user route; no assertion was removed, weakened or bypassed.
- Rerun required on a new commit before claiming route isolation or other Browser QA fixes verified.


## Be Vietnam Pro digit widths measured (2026-11-22)
- PHP syntax workflow [44989847815](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44989847815) ran `fontTools` against `be-vietnam-pro-400-latin.woff2`, unitsPerEm=1000. Advance widths: 0=676, 1=385, 2=651, 3=660, 4=710, 5=660, 6=680, 7=594, 8=634, 9=680; 8 unique widths, not tabular.
- Kept Be Vietnam Pro. Removed false reliance on `font-variant-numeric: tabular-nums` for these fields; `.item-time`, `.budget-summary__tile strong` and `.finance-row__side strong` now have min-width and right alignment. `font-proof.json`, the font metrics CI script, browser assertions, DECISIONS and design rules record the same measurement.


## Browser screenshot and error artifact matrix (2026-11-22)
- Reworked the screenshot test to capture viewport-only images (not full-page) at 360, 390, 768, 1024 and 1280px for Explore home, itinerary fixture (one day, two main stops, budget and two expenses), open stop-edit panel, empty rental handoff and admin styleguide. Filenames follow `screen-viewport-state.png`, e.g. `itinerary-390-edit-open.png`.
- Header, mobile navigation and any configured floating action are captured in viewport coordinates; the clean-install Zalo action remains absent because no owner URL was supplied.
- Browser workflow now emits `artifacts/playwright-results.json` and a readable `artifacts/playwright-errors.txt` including Playwright failures and `error-context.md` snapshots. Artifact upload includes both screenshots and text logs.
- Screenshot suite timeout is 120 seconds to accommodate the expanded 25-image matrix; assertions and test count remain unchanged.
- Pending new Browser QA artifact/run evidence.


## Route QA selector correction (2026-11-22)
- Browser run [44989906380](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44989906380) on commit `b233cac320e27e8f0d297dd4ed20b68f53920751` completed with 5 passed / 7 failed. All seven failures stopped in the shared route assertion because `.next-step-section` matched both the home next-step section and the distinct rental-next-step section (strict-mode locator ambiguity).
- Root correction: scope the hidden home section to `.next-step-section.page-container`; the rental route's own `.rental-next-step` remains visible, and CSS no longer hides it as a side effect. Browser test asserts rental-next-step visibility. No test was removed or weakened.


## Canonical #route root restoration (2026-11-22)
- `#route?trip=<uuid>` remains the explicit deep link. The bare `#route` now restores the last selected trip from tab-scoped `sessionStorage`; if no trip has ever been selected, it falls back to the Trips list. The selected trip is stored when opened and when a route URL is resolved.
- Desktop Browser QA now navigates from the selected route to bare `#route`, verifies the itinerary workspace returns, then restores the explicit deep link before continuing. No session or business data is persisted beyond the browser tab.
- Pending CI evidence.


## Focus coverage expansion – finance controls and forest surfaces (2026-11-22)
- Found finance form overrides still using a 3px shadow and `outline: 0`; changed finance inputs/selects to the same `#FD550A` border and 4px halo as other inputs, leaving the global 3px `:focus-visible` outline/3px offset in force.
- Forest-background links in the site footer and rental next-step card now use the lime focus outline. The focus browser test opens budget/expense details, checks computed `:focus-visible` outline width/offset on all itinerary/finance controls, verifies the finance input border and 4px shadow, and verifies lime outline color on a footer link.
- No focus assertion removed or relaxed. Pending Browser QA rerun.


## Hidden-route focus assertion correction (2026-11-22)
- Browser run [44990231479](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44990231479) on commit `7cb5de3929ae2c127c205c62dbf17ce8ced29334` failed 7/12 tests at the shared hidden-route assertion. The product roots were correctly `hidden` and `inert`; the test incorrectly counted every enabled descendant in the DOM as focusable without honoring its inert ancestor.
- Corrected the assertion to attempt real focus on each enabled descendant and fail only if the browser actually moves `document.activeElement` into a hidden/inert screen. The test still separately requires the screen root to have both `hidden` and `inert`; this verifies browser behavior rather than weakening the condition.
- Rerun required on the new commit.


## Playwright error artifact formatting correction (2026-11-22)
- Inspected the generated workflow source and found the error-export script used double-escaped `\\n`, which would write literal backslash-n sequences instead of readable line breaks. Corrected to real newline escapes for failure headings, error-context separators and file joins.
- Artifact still contains both JSON report and `error-context.md` snapshots; the exported `.txt` is now line-oriented and readable. Pending next Browser QA artifact verification.


## Stable itinerary/item loading assertions (2026-11-22)
- Addressed the observed `Đang tải điểm dừng…` race with Playwright web-first assertions, no fixed sleeps: after route reload the test waits for the selected trip title and the resolved empty-itinerary state; after creating a day and each stop it waits until `#day-items-list` no longer contains the loading message and `aria-busy` is no longer `true`.
- These assertions prevent subsequent clicks/axe scans/screenshots from racing the async API render. Test coverage is stronger; no delay was added and no assertion removed.

- Screenshot fixture now closes the budget and expense entry details after creating the two expense records, so the “default itinerary” and “edit panel open” captures show the intended screen state rather than unrelated open finance forms.


## Browser QA #44990443176 findings and fixes (2026-11-22)
- Run on commit `646693a72ab6e85db7f6e984e2a175db12f0238d`: 9 passed / 3 failed. (1) Reorder test's second-card heading selector incorrectly used `h4`; actual stop cards render `h3`. Corrected selector to the actual semantic heading without changing the expected title. (2) Mobile content box measured 298.656px vs 320px target because the timeline list reserved 22px left padding. Mobile stop list now removes the redundant timeline gutter and marker, expands 4px beyond its container, and keeps the stop card with 16px padding; the >=320px assertion remains unchanged. (3) Unset-time fixture reused the prior `09:30` value; explicitly clears the field before creating the no-time stop, preserving the `Chưa đặt giờ` assertion.
- No test removed or relaxed. Rerun required on the new head.


## Browser QA #44990716061 and admin-bar measurement correction (2026-11-22)
- Run [44990716061](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44990716061) was triggered on commit `8c891093014148608df38e7f0c4e3337cf1aaa1c`: PHP syntax [44990716106](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44990716106), Docker [44990716108](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44990716108), Integration [44990716076](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44990716076) passed; Browser was still running at audit time. These runs do not apply to later commits.
- Inspection found the viewport matrix excluded any element with class `.adminbar-input`, which was broader than the approved exception. Replaced it with `!element.closest('#wpadminbar')`; added an injected, visible 43×43px real button probe at every viewport and assert the detector catches it before removing the probe. The production control-size assertion remains unchanged and still requires zero undersized controls outside WordPress core admin bar.
- This is a test precision correction: only WordPress core admin-bar descendants are excluded; no DealZ control is exempted and no threshold is relaxed.
- On historical commit `475cdcf55ebea6481d9cf8020dfd3b1d569ae7f2`, workspace focus was NOT correct: `itinerary.js:79-85` added a body class and hid trip-list panels, while `style.css:767-770` hid home sections with CSS only (no `hidden`/ `inert` on those roots). Its generic focus rule was `style.css:33-36`, but `style.css:439` still set `outline: 0` on the inline edit input, overriding the global focus-visible outline. Later route-isolation and finance-focus commits address these defects; verify only on their own head and green browser run.
- PHP syntax workflow source `.github/workflows/php-lint.yml:3-7` has push-to-main and pull-request-to-main triggers and no `paths` filter. FontTools measurement is part of that workflow; do not describe it as path-filtered.
- Current viewport gate now includes a positive 43px-control detection probe and limits the admin exception to `#wpadminbar`. Await CI on the commit containing this correction.


## Browser QA #44990716061 – failure analysis and fixture corrections (2026-11-22)
- Run [44990716061](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44990716061) ran on `8c891093014148608df38e7f0c4e3337cf1aaa1c`: 9/12 passed, 3 failed. PHP [44990716106](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44990716106), Integration [44990716076](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44990716076) and Docker [44990716108](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44990716108) passed on that same head.
- Desktop test: `tests/browser/itinerary.spec.cjs:193` used `input[type=text]`, which matched the stop name and both masked time fields. Changed to the accessible textbox name `Tên điểm dừng`; no behavior assertion weakened.
- Mobile test: line 280 expected legacy `#trip/<id>`, while the product correctly returned canonical `#route?trip=<id>`. Updated the expected route to the current canonical contract; no navigation behavior changed.
- Locale test: shared helper selected the first itinerary in the list, which could be a previous fixture rather than the newly created locale-specific trip. Changed helper to select exactly one trip matching the supplied title and assert count=1 before opening.
- Browser run #44990716061 did not verify the newly pushed admin-bar probe commit; this correction is followed by another run on a later head.


## Browser QA #44990993337 – trip fixture locator root cause (2026-11-22)
- Run [44990993337](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44990993337) ran on `a5d16dad42177ab2feeedd9b8cae9b39b571c046`: 5/12 passed, 7 failed. PHP [44990993336](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44990993336), Integration [44990993384](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44990993384) and Docker [44990993330](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44990993330) passed on the same commit.
- Seven browser failures shared one fixture-selector regression: `createTripAndDay()` filtered the `[data-open-trip]` button by trip title, but the button's visible text is only “Mở lịch trình”; the title is rendered in the parent `.trip-card` heading. The selector therefore matched zero cards and cascaded into the desktop, mobile, badge, axe, focus, primary-action and screenshot tests.
- Corrected the helper to locate exactly one `.trip-card` containing the requested title, then click its `[data-open-trip]` button. The 43px positive control probe and #wpadminbar-only exclusion remain intact. No test removed, skipped, or relaxed.
- Artifact from this run: [dealz-browser-qa](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44990993337/artifacts).


## Browser QA #44991229331 – remaining two assertions (2026-11-22)
- Run [44991229331](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44991229331) ran on `484d18327e1c50e4784fb24b2068028d047544dd`: 10/12 passed, 2 failed. PHP [44991229352](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44991229352), Integration [44991229353](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44991229353), Docker [44991229333](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44991229333) passed on the same commit.
- Failure 1: desktop edit assertion at `tests/browser/itinerary.spec.cjs:197` queried `h4`; stop card title is `h3`. Updated only the selector to the rendered semantic heading.
- Failure 2: focus test programmatically called `.focus()` on `#back-to-trips` and then incorrectly expected the browser's keyboard-only `:focus-visible` heuristic to match. Kept the computed-style assertions; now exercises the link with Tab then Shift+Tab and verifies it is focused, `:focus-visible`, outline ≥3px and offset ≥3px. Form controls remain checked individually.
- Viewport matrix (including the 43px positive probe), primary-action/CLS test, and screenshot test all passed on this head. Screenshot artifact [dealz-browser-qa](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44991229331/artifacts) includes the 25 viewport captures plus readable Playwright error report. The two assertion fixes require a new full four-workflow run.


## Browser QA #44991475239 – final focus indicator correction (2026-11-22)
- Run [44991475239](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44991475239) ran on `190d0ffcbc01dae89700c30c7db159d78d7faca3`: 11/12 passed. PHP [44991475277](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44991475277), Integration [44991475246](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44991475246), Docker [44991475247](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44991475247) passed on the same commit.
- Remaining failure: `tests/browser/itinerary.spec.cjs:517` – `#item-title` matched `:focus-visible`, but computed `outlineWidth` was `0px` (required ≥3px). The test's heading selector, keyboard-link modality, route flow, positive 43px probe, viewport checks, primary action/CLS and 25 screenshot captures passed.
- Added a high-specificity, form-scoped `#create-item-form .form-field input:focus-visible` outline using the approved `--dealz-focus-outline` token and 3px offset. The assertion remains unchanged. This is an explicit style correction for the actual form input, not a test relaxation.
- Screenshot/error artifact from this run: [dealz-browser-qa](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44991475239/artifacts). It contains 25 viewport images and readable Playwright error text.


## Owner follow-up – disabled CTA, inline validation and unified focus (2026-11-22)
- Browser QA head before this change: `190d0ffcbc01dae89700c30c7db159d78d7faca3`; PHP, Integration and Docker passed; Browser failed only the focus-visible assertion for `item-title` (run 44991475239).
- Create-stop submit remains enabled when the title is empty. Clicking it displays an inline field error with `aria-invalid` and `aria-describedby`; the error clears on input.
- Disabled buttons use mint-100 + muted ink (the ink-2 token), with no opacity dimming, including item edit/archive controls.
- Consolidated form focus indicators under one high-specificity `:focus-visible` rule (3px solid outline, 3px offset); removed duplicate form-specific outline declarations. Keyboard tests now switch modality with Tab/Shift+Tab before checking each form control.
- The itinerary editor locator remains the exact `#itinerary-editor` id selector; no broad locator or weakened condition was introduced.
- Await full four-workflow CI on the new commit.


## Browser QA #44992063025 – focus outline cascade remains unresolved (2026-11-22)
- Run [44992063025](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44992063025) ran on `0ef8da72e40987ab1ba7389c6ffd63df813e2761`: 11/12 passed. PHP [44992063039](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44992063039), Integration [44992063020](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44992063020), Docker [44992063003](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44992063003) passed on the same commit.
- The single remaining failure is still `#item-title`: it matches `:focus-visible` and has a non-`none` outline style, but computed outline width remains `0px`. The consolidated `body #primary :focus-visible` rule still loses the width in the WordPress browser context.
- Strengthened the global 3px outline and 3px offset declarations with `!important`, and made the approved lime outline-color override important on green surfaces so the focus color decision remains intact. The test remains unchanged and strict.


## Screenshot viewport audit and capture correction (2026-11-22)
- Inspected the 25-image artifact from run [44991475239](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44991475239/artifacts). Home and itinerary screenshots were viewport captures; however, rental captures retained the previous scroll position and showed the rental next-step/footer while the rental workspace title was outside the viewport.
- Corrected the screenshot test to scroll each active screen below the sticky header before capture, assert the screen title is actually in the viewport, and use explicit filenames `screen-width-viewport-state.png`. Edit-panel captures position the selected stop card below the header. All captures remain `fullPage: false`; this is a screenshot-position correction, not a change to product behavior.
- Required matrix remains 5 widths × 5 states = 25 viewport screenshots: home, itinerary with fixture data, edit open, rental empty and styleguide. Header/nav remain in the viewport; no configured Zalo control is fabricated.


## Focus indicator diagnostics – isolate WordPress cascade (2026-11-22)
- The consolidated global focus rule with `!important` still needs Browser QA confirmation; previous Browser run [44992361334](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44992361334) is 11/12 and retains the `#item-title` computed-width failure.
- Added strict diagnostic output to the existing focus assertion: matching CSS rules, stylesheet URLs, resolved token value, `#primary` ancestry, inline style and computed outline color/width. The width ≥3px assertion remains unchanged.
- Current viewport screenshot capture commit `7644de68` also moves each screen title into the viewport before taking images. New Browser run is in progress; do not call 12/12 until the diagnostic run passes.


## Focus cascade – longhand enforcement and element-level token proof (2026-11-22)
- Browser run [44992620574](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44992620574) on `02aac315b52a5b0130438b7c0552e02e95971edf`: PHP [44992620575](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44992620575), Integration [44992620477](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44992620477) and Docker [44992620481](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44992620481) passed; Browser remained 11/12.
- Diagnostic proves `body #primary :focus-visible` matches and root token is `#D63F00`, but computed outline width is 0px and color falls back to text color. Expanded diagnostics to collect every matched outline rule and the custom property resolved on the input itself.
- Replaced outline shorthand with explicit longhand width/style/color/offset in the single global focus rule. Added a strict input focus-color assertion; no test is weakened. Await new CI.


## Diagnostic source correction (2026-11-22)
- Corrected two literal `\\n` sequences accidentally written into the Browser QA JavaScript diagnostic by the previous patch; replaced them with actual line breaks. No test logic, thresholds or assertions changed.


## Browser QA #44992995705 – computed focus cascade diagnostics (2026-11-22)
- Run [44992995705](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44992995705) on `a5137e405e75a0f83040745c39ac77e8b1376231`: 11/12 passed. PHP/lint [44992995708](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44992995708), Integration [44992995707](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44992995707) and fresh install [44992995699](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44992995699) passed.
- Focus diagnostic: `#item-title` is `:focus-visible`, inside `#primary`; root and element token are `#D63F00`; no inline style; matching global rule is loaded and marked important, yet computed result is `0px solid rgb(14,42,34)`. The viewport screenshots and remaining 11 tests passed.
- Expanded diagnostic to recursively inspect nested CSS rules/media blocks and all matched rules declaring `outline`, including inline WordPress stylesheets. No threshold or expected color was changed.


## Browser QA #44993280135 – nested rule trace correction (2026-11-22)
- Run [44993280135](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44993280135) on `40530779ceeda9dc42bfc972ac6a93fe27593175`: 11/12 passed; PHP [44993280180](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44993280180), Docker [44993280236](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44993280236) and Integration [44993280242](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44993280242) passed.
- `#item-title` still computes `0px solid rgb(14,42,34)` despite `:focus-visible`, root/element token `#D63F00`, and the global important rule. The recursive diagnostic returned no rules because its traversal skipped parent CSSStyleRules when nested rules existed. Corrected traversal to record each matching style rule before descending into nested/media rules. The 3px width and red-color assertions remain strict.


## Screenshot viewport header and bento proof refinement (2026-11-22)
- Visual inspection of artifact [run 44993280135](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44993280135/artifacts) confirmed rental and edit content were visible but the sticky site header was clipped after element-offset scrolling.
- Changed itinerary edit and rental screenshots to reset scroll to the top of the active route, assert ≥90% of `.site-header` is in viewport, and assert the screen title/summary is in viewport. Home, itinerary and styleguide captures also assert the header is ≥90% visible.
- Added two extra bento proof screenshots at 360px and 390px, with the rental-card heading and “Sắp có” chip visible. Required 25-screen matrix remains viewport-only; artifact now has 27 images.


## Focus computed-style synchronization (2026-11-22)
- Browser run [44993571495](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44993571495) on `a82d208bd0c39963aa78b3edd247ea986a61d53d`: 11/12 passed. PHP/lint [44993571496](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44993571496), Integration [44993571535](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44993571535), fresh install [44993571494](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44993571494) passed.
- Recursive diagnostic now confirms the only matching outline declaration is the global `body #primary :focus-visible` rule, marked important; the token resolves correctly, but an immediate synchronous computed-style read still returns width 0px. No competing author outline rule is present in the accessible stylesheets.
- Replaced the immediate-only sampling with Playwright web-first `toHaveCSS('outline-width', '3px')` and an exact red-color poll for `#item-title`, then retained the subsequent computed-style assertions. This waits for the browser's focus style to settle without fixed sleep and does not relax the requirement.


## Screenshot scroll behavior correction (2026-11-22)
- The 27-image matrix showed the site header still clipped in rental/edit captures after `window.scrollTo(0, 0)`. Root cause: the theme sets `html { scroll-behavior: smooth; }`, so the scroll reset animated and screenshots were captured before the scroll reached 0.
- Changed all screenshot-matrix top resets to explicit `behavior: 'instant'`; existing assertions still require ≥90% of the site header and active screen title/summary in the viewport. This removes animation timing from evidence capture without adding sleeps or changing production scroll behavior.


## Finance input focus test correction (2026-11-22)
- Browser run [44993898346](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44993898346) on `179d160638639e340026134681d0ff426bc046b5`: 11/12 passed. Lint [44993898298](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44993898298), Integration [44993898316](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44993898316), Fresh install [44993898368](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44993898368) passed.
- Remaining assertion expected #FD550A and 4px halo on `#expense-amount`, but the test helper had collapsed the expense `<details>` after inserting fixtures. The test called `.focus()` on a hidden input, so it remained at its neutral #7C948A border. Open the expense details, assert the input is visible and focused, then retain the exact #FD550A and 4px checks. This corrects the test setup, not the acceptance criteria.


## Browser QA #44994343276 – finance visibility and screenshot scroll root (2026-11-22)
- Run [44994343276](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44994343276) on `dd70271249269d4ba6a56830f77b18301052976a`: 10/12 passed. Lint [44994343297](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44994343297), Integration [44994343282](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44994343282), Fresh install [44994343284](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44994343284) passed.
- Finance failure: the test clicked a generic `.finance-card` summary and did not confirm the expense `<details>` opened; `#expense-amount` remained hidden. It now targets `#finance-panel .finance-card:nth-child(2) details.finance-form-details`, opens only if closed, asserts `open`, then asserts visible/focused before keeping exact #FD550A and 4px halo checks.
- Screenshot failure: the edit summary was below the viewport after reset-to-top. Added a shared instant scroll helper that disables smooth behavior temporarily, sets the actual scrolling element, polls `window.scrollY`, and positions edit/rental targets below the sticky header. No fixed sleeps; viewport assertions remain.


## Browser QA #44994711873 – final test harness corrections (2026-11-22)
- Run [44994711873](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44994711873) on `e5884ced87702c0e47c681c6d725a6fe139de0ee`: 10/12 passed. PHP [44994711871](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44994711871), Integration [44994711885](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44994711885), Docker [44994711889](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44994711889) passed.
- Finance input remained hidden because the card-index locator did not reliably identify the `<details>` owning `#expense-amount`. The test now finds the actual parent `<details>` through `#create-expense-form` and asserts its open state before visibility/focus/border checks.
- Screenshot scroll poll failed by 1 CSS pixel (target 1802, actual 1803) due fractional scroll rounding. Changed only the scroll-position poll to accept ≤1px rounding error; the independent header ≥90% and target-in-viewport assertions remain exact. No sleep or viewport threshold reduction.


## Finance focus computed-style synchronization (2026-11-22)
- Browser run [44995025423](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44995025423) on `115064bcd3c7815e87fc981d22d6f9157a558d3a`: 11/12 passed; all four other browser scenarios, including the 27-image viewport capture, passed. PHP [44995025465](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44995025465), Docker [44995025439](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44995025439), Integration [44995025492](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44995025492) passed.
- Remaining assertion: visible and focused `#expense-amount` still returned neutral border on the immediate computed-style read. The CSS has `.finance-form input:focus` with approved `--dealz-input-focus` and 4px halo. Added Playwright web-first `toHaveCSS` checks for exact orange border and 4px shadow before the existing computed-value assertions; no threshold, color, or shadow requirement changed.


## Tiếp quản – ZIP, DEPLOY và chính sách tài khoản (22/11/2026)
- HEAD khi bắt đầu lát việc này: `115d74fa165a360fe94bb94a0e93e28d5e10abff`. PR #10 vẫn Draft.
- Commit `115d74f` bổ sung web-first CSS assertions cho viền #FD550A và quầng 4px của #expense-amount; giữ nguyên giá trị mong đợi, không sleep.
- Browser run [44995414089](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44995414089) trên `115d74f` đang chạy khi cập nhật; PHP [44995414046](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44995414046), Integration [44995414117](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44995414117), Docker [44995414041](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44995414041) success.
- Thêm workflow đóng gói hai ZIP cài đặt từ đúng thư mục plugin/theme; loại tests, docs, .github, node_modules, README và cấu hình PHPUnit. Artifact được upload với `if: always()`; cần kiểm tra artifact contents ở run đầu.
- `docs/DEPLOY.md` ghi yêu cầu PHP 8.1+, WordPress 6.5+, MySQL 8.0+ (Docker đang dùng MySQL 8.4; MariaDB chưa xác nhận), cài plugin trước theme, endpoint health, schema 1.2.0/8 bảng, rollback và checklist bảo mật.
- Chính sách tài khoản khách: `users_can_register=false`; pilot dùng tài khoản tạo bằng lời mời; đăng ký công khai cần trang tài khoản riêng và chủ dự án duyệt.
- Browser screenshot matrix bổ sung viewport 1536; upload artifact được xác nhận có `if: always()`.
- Các kết quả CI của commit mới sẽ được ghi bổ sung sau khi GitHub tạo run IDs; không gắn nhầm run của HEAD trước.
