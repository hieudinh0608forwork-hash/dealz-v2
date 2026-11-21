# Changelog

## 2026-11-22
- Initialized the DealZ V2 project knowledge pack in the new `dealz-v2` repository.
- Added authoritative design rules from the 2026-11-20 handoff and aligned UX/design system documents.
- Retrieved the 2026-11-21 Master Handoff from the user's Library and aligned product, architecture, data, API, business rules and backlog.
- Added handoff completion gates, permission matrix, AI planner, maps/routing, budget/finance, screen inventory, agent workflow, environment audit, source traceability and four compact agent prompts.
- Expanded product/architecture/data/API/business requirements to reflect the full-scope Master Handoff while preserving the 20/11 UI restrictions.
- Created initial GitHub issues for environment audit, UX handoff, contract freeze and WordPress foundation.
- Confirmed repository is Public, default branch `main`, and connected account has admin/push permissions.
- At that checkpoint, no application code, WordPress environment, runtime test or production deployment had been completed or verified.

- Started implementation: added DealZ Core plugin bootstrap, namespace autoloader, administrator capability bootstrap, non-sensitive health endpoint, non-destructive uninstall handler, and initial custom theme shell.
- Added PHP syntax GitHub Actions workflow. Workflow result has not yet been observed; do not treat it as a passing test.
- Corrected PHP namespace and WordPress REST response type issues during source review.

- Implemented additive WordPress schema migration v1.0.0 for seven trip-planning tables, with table-existence verification before recording schema version.
- Implemented authenticated owner-scoped Trip CRUD REST routes with validation, private-by-default creation and archive-only deletion.
- Added OpenAPI 3.1 contract for health and Trip routes.
- Added Docker Compose local WordPress/MySQL environment definition and local-only .env example; environment has not been executed in this context.
- Added MVP implementation notes and manual runtime acceptance checklist. No runtime test or successful CI run is claimed.
