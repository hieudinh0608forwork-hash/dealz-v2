# ADR 0001 – Modular Monolith

- Status: Accepted
- Date: 2026-11-22

## Context
DealZ V2 cần nhiều module nghiệp vụ nhưng nguồn lực và nhu cầu vận hành MVP chưa chứng minh cần microservices.

## Decision
Bắt đầu bằng modular monolith trên WordPress. Business logic nằm trong custom plugin; theme chỉ trình bày. Các module có ranh giới và adapter rõ ràng.

## Consequences
- Triển khai và vận hành ban đầu đơn giản hơn.
- Dễ dùng chung transaction/database cho nghiệp vụ cốt lõi.
- Cần kỷ luật module để tránh coupling.
- Chỉ cân nhắc tách service khi có nhu cầu đo được về scale, deployment hoặc ownership.
