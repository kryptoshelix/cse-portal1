# Testing

Framework: PHPUnit 11 (Laravel defaults). Isolated in-memory SQLite (`phpunit.xml` sets `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`). No production data.

## Commands
```bash
php artisan test                 # full suite
php artisan test --testsuite=Feature
php artisan test tests/Feature/Security   # security regression subset
```

## Test map (acceptance criteria)
- `Auth/AuthTest` — register (student/faculty only; pending status; duplicate email rejected; role injection ignored), login/logout, session regeneration, throttle.
- `Auth/RoleRedirectTest` — each role lands on its own dashboard; authenticated guest visiting /login redirected appropriately.
- `Auth/AccountStatusTest` — pending/rejected/deactivated blocked from dashboards; active allowed.
- `Security/StudentAdminDenialTest` — student hitting every `/admin/*` URI → 403/redirect (data provider over route list).
- `Security/FacultyRestrictionTest` — faculty cannot admin; can faculty portal; review only with flag.
- `Policy/AchievementOwnershipTest` — Student A vs B read/update/delete/resubmit isolation; crafted `owner_user_id`/`status`/`approved_by` fields ignored.
- `Workflow/AchievementTransitionTest` — legal & illegal transitions; reject requires ≥10-char feedback; publish only from approved; self-approval denied for student owners.
- `Admin/AccountApprovalTest` — approve/reject/deactivate/reactivate + last-super-admin safeguard + role change super-only.
- `Uploads/EvidenceUploadTest` — valid upload stored private w/ random name; oversized rejected; spoofed extension (php file named .jpg? and jpg-named .php) rejected via server MIME check; unauthorized download 403; owner+admin download OK; audit row written.
- `Public/PublishedOnlyTest` — lists & detail pages show published only; draft/pending/approved invisible (404); stats count real rows.
- `Public/SearchPaginationTest` — search/filter/pagination keep published-only scope.
- `Audit/AuditLogTest` — sensitive actions produce sanitized audit rows (no password/token values).

## Rules
Never weaken authorization to pass tests. Report real output. Factories create fictional data only.
