# Security Policies & Acceptance Criteria

## Authorization model
- Deny by default. Route groups carry `auth` + `active` (account status ∈ {active}) + `role:<allowed roles>` middleware. Record-level checks via policies. Controllers never trust client-supplied `role`, `status`, `created_by`, `approved_by`, `featured`, `is_published`.
- Students have zero access to `/admin/*`; verified by direct-URL tests (403/redirect), not by hidden links.
- Faculty do not inherit admin rights. Extra review capability requires the explicit boolean flag `users.can_review_achievements` set by a super admin.

## Authentication
- Session-based; `SESSION_DRIVER=database` recommended in prod. CSRF enabled everywhere (state-changing routes POST/PUT/PATCH/DELETE only).
- Session ID regenerated on login; full logout destroys session + clears remember token.
- Login throttling: Laravel throttle (5 min attempts / email+IP lockout 1:60s… default RateLimiter 'login' 5/min, lock 1 min → we use 5 tries, 60s lock documented).
- Passwords: bcrypt (cost configurable), min 8 chars + confirmation on register/change. Generic error messages ("These credentials do not match our records." plus distinct-but-safe message for pending/deactivated accounts).
- Password reset available only when MAIL_MAILER configured; README states it is disabled-by-default placeholder otherwise. Email verification NOT claimed (no feature flag enabled) — documented.

## Mass assignment & validation
- All models use `$fillable` whitelists. Privileged columns writable only through dedicated service/admin paths.
- Every write path uses a Form Request; server-side validation of types, lengths, dates, enum values, MIME/extension/size for uploads.

## File upload security
- Evidence: pdf/doc/docx/jpg/jpeg/png, ≤ 5 MB, extension AND client MIME AND server-detected MIME (`File::fake` tests + real `finfo` check in code) must agree. Random generated filename (`Str::random(40).ext`). Stored on private `evidence` disk outside webroot.
- Images (gallery/avatar): jpg/jpeg/png/webp, ≤ 4 MB, re-encoded via GD where feasible to strip payloads; stored on public disk only through admin-published modules.
- SVG, HTML, executables, archives rejected. Download endpoint streams with `Content-Disposition` after policy check; errors never leak absolute paths.

## XSS / injection / IDOR
- Blade `{{ }}` escaped output everywhere; no `{!! !!}` on user data.
- All DB access via Eloquent/parameterized queries; search LIKE terms bound.
- Every show/edit/download route passes through a policy that verifies ownership or admin scope (IDOR tests included).

## Data protection & audit
- `audit_logs` records who/what/before/after/ip for: login-failure lockouts? (no—kept simple), account approve/reject/deactivate/reactivate, role changes, achievement submit/approve/reject/publish/unpublish, content create/update/delete, file download of private evidence. Never stores secrets/passwords.
- `.env` excluded from VCS; `.env.example` placeholders only. `APP_DEBUG=false` required in production (documented). Secure cookies (`SESSION_SECURE_COOKIE=true`) behind HTTPS.
- Least-privilege DB user for production documented in DEPLOYMENT.md.

## Security regression tests (see docs/TESTING.md)
Direct URL access per role; crafted requests setting privileged fields; cross-user record access; self-approval attempts; invalid transitions; unauthorized downloads; spoofed-extension and oversized uploads; unpublished content invisible publicly; registration cannot pick admin roles.
