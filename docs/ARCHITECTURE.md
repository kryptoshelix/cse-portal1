# Architecture

Laravel 12 monolith, Blade + Bootstrap 5 (CSS/JS/icons served from bundled files under `public/vendor/` so the app works offline/intranet — see DECISIONS D-006), vanilla JS, MySQL/MariaDB in dev/prod, SQLite in tests.

## Layers
- `routes/web.php` — route groups per audience: `public`, `auth`, `student`, `faculty`, `admin`.
- `app/Http/Controllers/{Public,Auth,Student,Faculty,Admin}` — thin controllers; validation in Form Requests.
- `app/Http/Requests` — Form Requests (server-side validation only).
- `app/Policies` — record-level ownership/state checks (AchievementPolicy, UserPolicy, DocumentPolicy).
- `app/Services/AchievementWorkflowService` — single place enforcing state transitions + audit writes (DB transactions).
- `app/Models` — Eloquent models with explicit `$fillable`; privileged fields (status, approved_by, featured…) are never mass-assignable from user input.
- `app/Enums` — `UserRole`, `AccountStatus`, `AchievementStatus`.
- `app/Http/Middleware` — `EnsureActiveAccount`, `EnsureRole` (registered as `active` and `role` aliases in bootstrap/app.php).
- `app/Services/AuditLogger` — sensitive-action audit trail (never logs passwords/tokens/file contents).

## Route groups & middleware
- Guest/public: no auth. Only `published` status records are ever queried publicly.
- `student.*`: `web`, `auth`, `active`, `role:student`.
- `faculty.*`: `web`, `auth`, `active`, `role:faculty`.
- `admin.*`: `web`, `auth`, `active`, `role:dept_admin,super_admin` (+ policies/permission checks inside actions).
- Registration creates `status=pending`; role forced server-side to `student` or `faculty` only.

## Layouts
- `layouts/public.blade.php` — site header/footer/nav.
- `layouts/portal.blade.php` — student/faculty top-nav layout (deliberately NOT the admin sidebar).
- `layouts/admin.blade.php` — admin sidebar + topbar.

## Storage
- `public` disk: gallery images, avatars (only when intentionally published).
- `local` (private) disk named `evidence`: achievement supporting documents; downloads only through the authorized controller endpoint that rechecks ownership/permissions.
