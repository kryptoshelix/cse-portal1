# Architectural Decisions

- **D-001 — Laravel 12.x on PHP 8.2.** Session sandbox has PHP 8.2.33; Laravel 12 supports ^8.2/^8.3. Chose stable `laravel/laravel:^12.0` (installed 12.69.2). XAMPP users on 8.2/8.3 are compatible.
- **D-002 — Custom lightweight auth (not Breeze/Jetstream).** Avoids extra npm/Vite dependency and gives full control over pending-account gating, role-aware redirects, and registration hardening. Blade views written by hand.
- **D-003 — Single `users.role` enum + one boolean permission flag** (`can_review_achievements`) instead of a full permissions package. Five fixed roles; matrix documented in PROJECT_SPEC §3. Simple, auditable, zero dependencies.
- **D-004 — Approval vs publication kept distinct.** `status` enum carries both review outcome (pending/approved/rejected) and visibility (published); `approved_at` and `published_at` timestamps recorded separately. Only `published` appears publicly.
- **D-005 — Private evidence on `local` disk named `evidence`; gallery images on `public` disk.** Downloads via controller with policy check.
- **D-006 — Bootstrap 5 + Bootstrap Icons bundled as static files in `public/vendor/bootstrap*`.** No CDN dependency (intranet-friendly), no Node build step required for deployment; Vite config retained but optional. Assets vendored once during development.
- **D-007 — Tests on in-memory SQLite; production MySQL.** Migrations written portably (avoid MySQL-only syntax); enum columns implemented as `string` + PHP-backed `enum()` validation for cross-DB consistency.
- **D-008 — Achievements have `owner_user_id` (beneficiary) separate from `created_by` (submitting actor).** Supports faculty-on-behalf-of-student without impersonation.
- **D-009 — First super admin created only via `php artisan app:create-admin`** (interactive, refuses weak passwords, never seeded with a default password). Demo seed accounts exist only in dev seeder with clearly fictional data.
- **D-010 — Email verification disabled; password reset included but requires configured mailer.** Documented rather than half-claimed.

## Open questions (non-blocking)
- Q-1: Should department admins publish content or only approve? Currently both allowed (spec §3). Owner may restrict later.
- Q-2: Student directory public display is opt-in per student consent; default OFF. Confirm institutional policy before go-live.
