# 22 – Implementation Log

## 2026-11-22 – Initial WordPress foundation

### Repository
- `hieudinh0608forwork-hash/dealz-v2`
- Branch: `main`

### Added
- `wp-content/plugins/dealz-core/dealz-core.php`: plugin header, constants, namespace autoloader, activation hook and bootstrap.
- `src/Core/Plugin.php`: plugin module bootstrap.
- `src/Core/Capabilities.php`: minimal administrator capability registration.
- `src/REST/HealthController.php`: public non-sensitive `GET /wp-json/dealz/v1/health` endpoint.
- `uninstall.php`: intentionally non-destructive uninstall.
- `wp-content/themes/dealz-theme/`: initial theme shell with setup, header, footer, index and stylesheet.
- `.github/workflows/php-lint.yml`: PHP syntax lint workflow on pushes and pull requests to main.
- Plugin README with local/staging smoke-check instructions and explicit scope limits.

### Design/safety decisions
- Health endpoint reports `application_ready: false`; this is a scaffold indicator, not an application readiness claim.
- Uninstall preserves data; no destructive cleanup.
- Only the WordPress Administrator receives the broad `manage_dealz` capability. Partner/traveler permissions are not implemented by this scaffold.
- Theme does not load remote fonts. Required licensed local WOFF2 font assets remain outstanding.
- No schema migrations or financial/booking rules were invented.

### Verification status
- Source reviewed after initial commit; PHP namespaces and REST response class qualification corrected.
- GitHub Actions workflow file exists. A passing run has not been observed.
- No WordPress/PHP runtime or database environment is available in this work context; plugin activation, REST route and theme rendering are not runtime-tested.
- No production deployment performed.

### Next implementation steps
1. Obtain/verify WordPress local runtime or hosting environment.
2. Add automated coding standards and WordPress integration test harness.
3. Freeze physical database schema and migrations.
4. Implement Identity/Access and Trip/Itinerary MVP services and REST contracts.
5. Build actual UI screens against the approved design rules and local font assets.
