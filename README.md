# DealZ V2

Nền tảng du lịch tích hợp, kết nối khách du lịch, nhà cung cấp dịch vụ và nền tảng công nghệ.

## Project status
- Current stage: MVP implementation (in progress).
- Repository: `hieudinh0608forwork-hash/dealz-v2` (Public).
- Default branch: `main`.
- Platform direction: fresh WordPress + custom plugin `dealz-core` + custom theme `dealz-theme` + REST API + MySQL.
- Architecture: modular monolith.
- Application code: versioned trip-planning/finance schema, owner-scoped Trip and itinerary APIs, budget/expense APIs and initial traveler/itinerary workspace committed; runtime not yet available or verified.

## Start here
1. Read [CLAUDE.md](CLAUDE.md).
2. Read [Project Charter](docs/00_PROJECT_CHARTER.md) and [Product Requirements](docs/01_PRODUCT_REQUIREMENTS.md).
3. Read [Design Rules](docs/11_DESIGN_RULES.md) before any UI work.
4. Read the relevant architecture, data, API, business and module specifications.
5. Read [Handoff Completion Standard](docs/13_HANDOFF_COMPLETION.md) and [Execution Backlog](docs/12_EXECUTION_BACKLOG.md).

## Core documents
- Product: [Charter](docs/00_PROJECT_CHARTER.md), [Requirements](docs/01_PRODUCT_REQUIREMENTS.md), [Screen Inventory](docs/18_SCREEN_INVENTORY.md).
- UX/UI: [UX Blueprint](docs/02_UX_BLUEPRINT.md), [Design System](docs/03_DESIGN_SYSTEM.md), [Design Rules](docs/11_DESIGN_RULES.md).
- Engineering: [Architecture](docs/04_SYSTEM_ARCHITECTURE.md), [Database](docs/05_DATABASE_SCHEMA.md), [API](docs/06_API_CONTRACT.md), [Permissions](docs/14_PERMISSION_MATRIX.md).
- Domain: [Business Rules](docs/07_BUSINESS_RULES.md), [State Machines](docs/08_STATE_MACHINES.md), [AI Planner](docs/15_AI_PLANNER_SPEC.md), [Maps & Routing](docs/16_MAPS_ROUTING_SPEC.md), [Budget & Finance](docs/17_BUDGET_FINANCE_SPEC.md).
- Delivery: [Test Plan](docs/09_TEST_PLAN.md), [Risk Register](docs/11_RISK_REGISTER.md), [Execution Backlog](docs/12_EXECUTION_BACKLOG.md), [Handoff Completion](docs/13_HANDOFF_COMPLETION.md), [Agent Workflow](docs/19_AGENT_WORKFLOW.md), [Environment Audit](docs/20_ENVIRONMENT_AUDIT.md), [Source Traceability](docs/21_SOURCE_TRACEABILITY.md), [Implementation Log](docs/22_IMPLEMENTATION_LOG.md), [MVP Implementation Notes](docs/23_MVP_IMPLEMENTATION.md), [OpenAPI Contract](docs/openapi/dealz-v1.openapi.yaml).
- Agent prompts: [agents/](agents/README.md) – Architect, Frontend, Backend, Intelligence.

## Non-negotiable rules
- DealZ V2 is a new, independent project. Never import V1 source code, database, theme, plugin or workflow.
- Follow [Design Rules](docs/11_DESIGN_RULES.md) exactly. Never reintroduce numbered circular itinerary strips.
- Never present AI, payment or English support as live before implementation and testing.
- Never fabricate supplier data, partner metrics, prices, route estimates or availability.
- Never show shop contact details before DealZ confirmation.
- AI suggests; user decides. Backend owns price, availability and transaction state.
- Trip, Order and Booking remain separate.
- Do not invent deposits, refunds, commission, payout or cancellation policy.
- Do not commit secrets or incur paid API costs without approval.
- Do not claim Tested/Verified/Deployed without evidence.

## Local development (Docker)

1. Install Docker Desktop / Docker Engine with the Compose plugin.
2. Copy `.env.example` to `.env` in the repository root. The sample values are local-only credentials.
3. Run `docker compose up -d`.
4. Open `http://localhost:8080` and complete the fresh WordPress installation.
5. In WordPress Admin, activate **DealZ Core** and optionally activate **DealZ Theme**.
6. Check `http://localhost:8080/wp-json/dealz/v1/health`; it should report scaffold status, not application readiness.
7. Use WordPress cookie authentication and an `X-WP-Nonce` REST nonce when testing Trip, itinerary and finance endpoints.

Stop the environment with `docker compose down`. To preserve local data, do not add `-v`. To deliberately reset local data, `docker compose down -v` removes the named WordPress/MySQL volumes; this is destructive and local-only.

The compose environment has been authored but has not been executed or runtime-verified in this work session.

## Handoff status
The repository contains the project knowledge pack, detailed baseline specifications and initial WordPress application slices. The first WordPress integration workflow passed 14 tests / 51 assertions, and PHP/JavaScript/OpenAPI checks also passed. This is not a completed application handoff: local browser acceptance, staging and production deployment remain unverified. See [Handoff Completion Standard](docs/13_HANDOFF_COMPLETION.md).
