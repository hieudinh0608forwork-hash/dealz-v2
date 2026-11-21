# DealZ V2

Nền tảng du lịch tích hợp, kết nối khách du lịch, nhà cung cấp dịch vụ và nền tảng công nghệ.

## Project status
- Current stage: Phase 0 – Product/Technical Handoff (in progress).
- Repository: `hieudinh0608forwork-hash/dealz-v2` (Public).
- Default branch: `main`.
- Platform direction: fresh WordPress + custom plugin `dealz-core` + custom theme `dealz-theme` + REST API + MySQL.
- Architecture: modular monolith.
- Application code/runtime: not yet implemented or verified.

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
- Delivery: [Test Plan](docs/09_TEST_PLAN.md), [Risk Register](docs/11_RISK_REGISTER.md), [Execution Backlog](docs/12_EXECUTION_BACKLOG.md), [Handoff Completion](docs/13_HANDOFF_COMPLETION.md), [Agent Workflow](docs/19_AGENT_WORKFLOW.md), [Environment Audit](docs/20_ENVIRONMENT_AUDIT.md).

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

## Handoff status
The repository currently contains the project knowledge pack and detailed baseline specifications. This is not a completed application handoff: no WordPress environment, application code, runtime tests or deployment have been verified. See [Handoff Completion Standard](docs/13_HANDOFF_COMPLETION.md).
