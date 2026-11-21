# DealZ Core tests

The Trip, manual itinerary and finance API integration tests use the official WordPress PHPUnit test suite and require a disposable local WordPress/MySQL database.

## Current test coverage
- Owner can create and read a Trip.
- A different authenticated user cannot read the Trip by UUID.
- Reversed trip date ranges are rejected.
- DELETE archives the Trip and the archived Trip is no longer readable.
- A day cannot be added outside the Trip date range.
- A different user cannot list days in another user's itinerary.
- A fifth active main stop on one day is rejected.
- Lunch and dinner cards do not count toward the four-main-stop cap.
- Archiving a main stop frees a slot for a replacement.
- Reordering requires the complete active item set, preserves owner scope and updates positions atomically.
- Explicitly overlapping local time windows are reported by the day validator.
- Budget line totals and grouped summaries are calculated using integer minor units.
- Budget lines are scoped to their Trip owner.
- Expense timestamps with offsets are normalized to UTC.
- Invalid calendar dates in expense timestamps are rejected.
- Budget line totals and grouped summaries are calculated using integer minor units.
- Budget lines are scoped to their Trip owner.
- Expense timestamps with offsets are normalized to UTC.
- Invalid calendar dates in expense timestamps are rejected.

## Run
1. Set up the local WordPress environment using the root README.
2. Install the WordPress PHPUnit test suite and PHPUnit according to the official WordPress testing handbook.
3. Ensure the DealZ Core plugin is active in the test WordPress installation.
4. Configure the test bootstrap to load the plugin and this test file.
5. Run the test class with PHPUnit.

The repository includes a GitHub Actions workflow that provisions MySQL, installs the WordPress 6.8 test library and runs this suite. The WordPress 6.8 / MySQL 8.0 CI run on commit `318908cc09d3a2ece20ff72f4373480b90320ec0` passed: 15 tests and 71 assertions, including meal-card limits, archive slot reuse, schedule conflict resolution, transactional item reordering and budget total recalculation after edits. Future changes must keep the workflow green.
