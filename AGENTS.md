# AGENTS.md — Repository Rules for AI Coding Sessions

Project: **CSE Department Portal** (Laravel 12 monolith, Blade + Bootstrap 5, MySQL prod / SQLite tests).

## Read first (token-efficient startup)
1. This file → 2. `docs/DEVELOPMENT_STATUS.md` → 3. `docs/PROJECT_SPEC.md` → 4. task-relevant doc → 5. `docs/DECISIONS.md` if architectural. Then open ONLY files relevant to the task. Never scan vendor/, node_modules/, storage/logs, public/vendor assets, or unrelated modules. Full protocol: `docs/AI_WORKFLOW.md`.

## Non-negotiable rules
- **Deny-by-default authorization.** Every route lives in a middleware-guarded group (`auth`, `active`, `role:...`). Students NEVER reach `/admin/*`; faculty never inherit admin. UI hiding is not a control — enforce server-side and TEST direct-URL access for every role when adding routes.
- **Never trust request fields**: `role`, `status`, `approved_by`, `created_by`/owner ids, `featured`, `is_published`, consent flags of other users. Keep them OUT of `$fillable`; set server-side only.
- **Achievement lifecycle** draft→pending→approved/rejected→published enforced in `App\Services\AchievementWorkflowService` only; rejection requires ≥10-char feedback; invalid transitions → 403 + audit. See docs/WORKFLOWS.md.
- **Public queries filter published-only** (`status='published'` / `is_published=1`) at query level.
- **Uploads**: validate size + extension + client MIME + server-detected MIME; random filenames; private evidence on `evidence` disk served through policy-checked endpoint. No SVG/HTML/executable/archives.
- **XSS**: Blade escaped output only (`{{ }}`); no `{!! !!}` with user data.
- **Secrets**: never commit `.env`/credentials/tokens; `.env.example` placeholders only; no default production passwords; first super admin via `php artisan app:create-admin`.
- **Audit** sensitive actions via `AuditLogger`; never log passwords/tokens/file contents.
- Validation in Form Requests; business logic not in Blade; transactions for multi-step writes; soft deletes for content history.

## Workflow
- Incremental changes → run targeted tests then `php artisan test` (in-memory SQLite; no MySQL daemon needed). Report REAL results; never fabricate. Do not weaken authz or delete tests to go green.
- After each milestone update DEVELOPMENT_STATUS.md, CHANGELOG.md, DECISIONS.md (if applicable).
- Scope guard: no ERP features (fees/attendance/exams/payroll), no React/Vue/microservices, minimal dependencies.
- Git: work on current branch; focused commits; no force-push/history rewrite/destructive DB commands without explicit approval.
