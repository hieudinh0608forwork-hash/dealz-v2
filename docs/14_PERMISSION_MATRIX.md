# 14 – Permission Matrix

## 1. Roles
Guest, Traveler, Partner Owner, Partner Staff, Operations, Administrator. Capabilities are granted explicitly; role labels alone do not authorize access.

## 2. Matrix

| Resource/action | Guest | Traveler owner | Collaborator | Partner | Operations | Admin |
|---|---|---|---|---|---|---|
| Browse published destination/service | Read | Read | Read | Read | Read | Full |
| Read own profile | — | Read/write | Own only | Own only | Assigned scope | Full |
| Create Trip | — | Yes | No | No | Support override audited | Yes |
| Read Trip | Public shared fields only | Own | Per share permission | No | Case-based | Yes |
| Edit Trip/itinerary | — | Yes | If edit permission | No | Case-based | Yes |
| Share/revoke Trip | — | Owner | No | No | No | Audited override |
| Read private budget/expenses | — | Own | If explicitly shared | No | Case-based | Yes |
| Create booking | — | Yes | No by default | No | No | Controlled override |
| Read booking | — | Customer's own | Only if explicit share | Own supplier bookings | Assigned cases | Yes |
| Manage listing | — | No | No | Own partner only | Moderation | Yes |
| Manage availability | — | No | No | Own listing only | Controlled support | Yes |
| Accept/reject supplier request | — | No | No | Own partner booking | Case-based | Audited override |
| View financial ledger | — | Own payment summary only | No | Own payout statement only | Least-privilege | Yes |
| Moderate destination/listing | — | No | No | No | Assigned scope | Yes |
| Manage roles/system settings | — | No | No | No | No | Yes |

## 3. Mandatory enforcement
- Every object lookup must verify owner/partner scope server-side.
- Never rely on hidden buttons or frontend route guards as authorization.
- Share links use unguessable tokens stored hashed, with expiry/revocation and restricted fields.
- Partner staff membership is scoped to partner organization and can be revoked.
- Operations access is case-based and audited; do not grant blanket admin.
- Payment, refund, commission and payout actions require dedicated capabilities and audit events.
- Admin overrides must capture actor, reason, timestamp and request ID.

## 4. Security tests
- Traveler A cannot read/update/delete Traveler B's Trip, expense, share or booking.
- Partner A cannot read/update Partner B's listing, availability, booking or finance data.
- Collaborator view permission cannot edit; comment permission cannot change trip ownership.
- Revoked/expired share link stops working.
- Guest cannot access private APIs.
- Operations cannot access unrelated cases.
- Partner cannot access wp-admin administrator functions.
