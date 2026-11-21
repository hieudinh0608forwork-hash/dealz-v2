# 20 – Environment Audit

## 1. Status
No hosting, domain, WordPress installation, PHP/MySQL versions, staging or production access has been verified in this repository yet. Do not invent setup values.

## 2. Required inventory
### Hosting and access
- Provider, plan, account owner and billing contact.
- Domain/DNS ownership.
- SSH/SFTP or control panel access.
- Deployment mechanism and rollback permissions.
- Resource limits and backup/restore capability.

### Runtime
- WordPress version.
- PHP version and enabled extensions.
- MySQL/MariaDB version.
- Web server and rewrite support.
- SSL/HTTPS.
- PHP memory/upload/time limits.
- Cron/real cron support.
- Composer availability.
- Node/build tooling support if selected.

### Operations
- Local/staging/production URLs.
- Database backup frequency and restore test.
- File/media backup.
- SMTP/email provider and deliverability.
- Logs, uptime monitoring and error alerting.
- Secrets management and access separation.
- Cache/CDN/object storage if available.

### External providers
For each AI, places, geocoding, routing, weather, maps, payment, email and messaging provider:
- Provider name and account owner.
- Coverage and legal/attribution terms.
- API quota and price.
- Sandbox support.
- Credential storage method.
- Timeout/retry/fallback.
- Data retention/privacy implications.

## 3. Audit procedure
1. Inspect hosting panel/runtime metadata without changing production.
2. Record exact versions and limits.
3. Check WordPress compatibility matrix.
4. Create isolated local/staging environment.
5. Validate backup restore before major changes.
6. Keep provider secrets outside GitHub.
7. Record evidence and date in this file.

## 4. Safety constraints
- No paid plan upgrades or paid API activation without explicit approval.
- No destructive migration against production.
- No public exposure of staging credentials.
- No assumption that a theme ZIP is a WordPress installation.
- No deployment before rollback and backup are tested.

## 5. Audit result
Status: Not yet audited. This is a checklist, not evidence that any environment exists.
