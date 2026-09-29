# Workflows

## 1. Registration & account approval
1. Visitor registers on `/register` choosing account type **student** or **faculty** (only these two are accepted; server maps to role `student`/`faculty`).
2. Server creates user with `status = pending`, hashed password, no privileged fields. A profile row (`students` or `faculty`) is created alongside.
3. Pending users may log in but only see a "waiting for approval" page; all portal routes blocked by `active` middleware.
4. Admin → Accounts → Pending: approve (status=active), reject (status=rejected), later deactivate/reactivate. Super admin may also change roles.
5. Last-active-super-admin safeguard: refuse to deactivate/demote the final active `super_admin`.

Account states: `pending | active | rejected | deactivated`. Only `active` passes the `active` middleware.

## 2. Achievement lifecycle
States: `draft`, `pending`, `rejected`, `approved`, `published`.

| Transition | Allowed from | Actor | Requirements |
|---|---|---|---|
| create as draft | — | owner (student/faculty/admin) | valid input |
| submit (draft/rejected → pending) | draft, rejected | owner (or admin) | evidence optional at draft, required at submit? No — evidence optional; validated when present |
| edit content | draft, rejected | owner only | status unchanged by edit unless "save & submit" |
| approve (pending → approved) | pending | reviewer (admin/super, or faculty w/ `can_review_achievements`) | comment optional |
| reject (pending → rejected) | pending | reviewer | **feedback message required (min 10 chars)** |
| publish (approved → published) | approved | admin/super | sets `published_at`; public consent flag governs name display |
| unpublish (published → approved) | published | admin/super | audit logged |
| resubmit (rejected → pending) | rejected | owner | via explicit "Resubmit" action |

Invalid transitions return 403 and are audit-attempt-logged. Students can never set status/approved_by/featured directly; those inputs are ignored (not trusted) even if posted.

Faculty submitting on behalf of a student: `created_by` = faculty user, `student_id` = beneficiary; UI labels show "submitted by faculty". Never impersonation.

## 3. Reviewer feedback & resubmission
Rejected submissions display `rejection_feedback` to the owner on detail/edit pages. Owner edits then presses **Submit for review** again → status pending, previous feedback retained until next decision, `submitted_at` updated.

## 4. Audit trail
Every state transition, account-status change, role change, content mutation and private-evidence download writes an `audit_logs` row: actor id/name, action, target type/id, before/after summary (no secrets), IP, timestamp.
