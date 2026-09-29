# AI-Assisted Development Workflow (token efficiency)

## At the start of every session, read ONLY:
1. `AGENTS.md`
2. `docs/DEVELOPMENT_STATUS.md`  ← authoritative checkpoint
3. `docs/PROJECT_SPEC.md`
4. The one doc relevant to the current task (ARCHITECTURE / SECURITY / WORKFLOWS / DATABASE / TESTING / SETUP / DEPLOYMENT)
5. `docs/DECISIONS.md` if architecture matters

Then open only source files directly involved in the task. Never scan `vendor/`, `node_modules/`, `public/build|vendor assets`, `storage/`, caches, or unrelated controllers/views. Use `git grep`/targeted `sed -n` instead of full-file reads when possible.

## During work
- One logical increment at a time; run `php artisan test` (or targeted subset) after each increment.
- New routes MUST join an existing role-guarded group; add a matching authorization test in the same commit.
- Never add privileged fields to `$fillable`; never trust request role/status fields.

## At the end of every milestone
- Update `docs/DEVELOPMENT_STATUS.md` (verified results, blockers, exact next task).
- Append entry to `docs/CHANGELOG.md`.
- Record architectural choices in `docs/DECISIONS.md`.
- Paste real test command + summary line into DEVELOPMENT_STATUS (no fabricated results).

## Do not
- Re-read whole repo, duplicate specs across docs, add frameworks (React/Vue/etc.), build ERP modules, weaken security for convenience, commit secrets, claim untested features work.
