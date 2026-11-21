# 19 – Agent Workflow & Workstream Handoff

## 1. Roles
### Architect / Integrator (C1)
Owns architecture, domain model, schema, API contracts, dependency map, task breakdown, contract tests, PR review, integration QA and release checklist. Does not take over all feature coding.
### Frontend / UX (C2)
Owns custom theme, design system, public pages, traveler UI, partner portal UI and responsive/accessibility. Consumes frozen API contracts; mock data must match schema and be visibly non-production.
### Backend / WordPress (C3)
Owns dealz-core, migrations, authentication/capabilities, REST, repositories, Trip/itinerary persistence, service/partner/booking/payment domain and admin operations.
### AI / Maps / Finance Intelligence (C4)
Owns AI orchestration, schema validation, place resolution, geocoding/routing adapters, deterministic schedule/budget engines and service matching. Does not change shared schema/API without Architect review.

## 2. Branch model
Target branches: `agent/architect`, `agent/frontend`, `agent/backend`, `agent/intelligence`. Main is integration branch. No direct unreviewed agent commits to main. If external Claude accounts are not connected, work is performed serially by the project coordinator using the same ownership boundaries.

## 3. Task contract
Every task must state:
- Task ID and goal.
- Owner/workstream.
- Dependencies.
- Files allowed to change.
- Files forbidden to change.
- API/schema contracts used.
- Acceptance criteria.
- Tests required.
- Handoff evidence.

## 4. HANDOFF.md required fields
- Completed.
- Files created/changed.
- Contracts used/changed.
- Tests run and exact results.
- Setup/run instructions.
- Known limitations.
- Dependencies/risks.
- Next task.

## 5. Token efficiency
- Read CLAUDE.md and only the relevant contracts/ticket.
- Do not repeatedly paste the full project pack into each prompt.
- Work in small bounded tasks.
- Do not let another agent continue unfamiliar code without reading handoff and running tests.
- Save durable progress in GitHub docs/commits, not only chat history.

## 6. Integration protocol
1. Architect freezes relevant contract.
2. Backend implements contract and tests.
3. Frontend uses same contract; mocks only until API exists.
4. Intelligence adapter implements its agreed schema and deterministic validators.
5. Integrator checks compatibility, permissions, edge cases and tests.
6. Merge only after review and evidence.
7. Update changelog and task board.

## 7. Current coordination mode
The project owner delegated routine technical decisions to the coordinator. The coordinator may perform work directly through connected GitHub tools. No assumption is made that four external Claude accounts are connected or actively executing.
