# DealZ Core tests

The Trip and manual itinerary API integration tests use the official WordPress PHPUnit test suite and require a disposable local WordPress/MySQL database.

## Current test coverage
- Owner can create and read a Trip.
- A different authenticated user cannot read the Trip by UUID.
- Reversed trip date ranges are rejected.
- DELETE archives the Trip and the archived Trip is no longer readable.
- A day cannot be added outside the Trip date range.
- A different user cannot list days in another user's itinerary.
- A fifth active main stop on one day is rejected.
- Explicitly overlapping local time windows are reported by the day validator.
- Explicitly overlapping local time windows are reported by the day validator.

## Run
1. Set up the local WordPress environment using the root README.
2. Install the WordPress PHPUnit test suite and PHPUnit according to the official WordPress testing handbook.
3. Ensure the DealZ Core plugin is active in the test WordPress installation.
4. Configure the test bootstrap to load the plugin and this test file.
5. Run the test class with PHPUnit.

The repository does not yet include the WordPress test library or an automated CI integration-test job. These tests are authored but have not been executed.
