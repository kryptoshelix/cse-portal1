# CSE Department Portal — Project Specification

## 1. Purpose
A production-oriented portal for a Computer Science & Engineering department providing:
- A **public website** (department info, faculty/students directory, achievements, activities, projects, publications, patents, gallery, news).
- A **student portal** (own profile, achievement submissions with workflow).
- A **faculty portal** (own profile, own achievement submissions).
- An **admin dashboard** (content management, submission review/approval/publishing, account approval, audit logs).

Not an ERP: no fees, attendance, exams, payroll.

## 2. Roles
| Role | Slug | Access |
|---|---|---|
| Public visitor | — | Published content only |
| Student | `student` | `/student/*` portal |
| Faculty | `faculty` | `/faculty/*` portal |
| Department admin | `dept_admin` | `/admin/*` (scoped) |
| Super admin | `super_admin` | `/admin/*` (all) + role/account management |

## 3. Permission Matrix (server-enforced allowlist)
| Capability | public | student | faculty | dept_admin | super_admin |
|---|---|---|---|---|---|
| View published content | ✔ | ✔ | ✔ | ✔ | ✔ |
| Register / login | ✔ | ✔ | ✔ | ✔ | ✔ |
| Own dashboard (`/student`,`/faculty`) | ✖ | ✔(student) | ✔(faculty) | ✖ | ✖ |
| Edit own profile (limited fields) | ✖ | ✔ | ✔ | ✔ | ✔ |
| Create achievement submission | ✖ | ✔ (self) | ✔ (self; optional on-behalf-of-student recorded as faculty actor) | ✔ | ✔ |
| Edit own draft/rejected submission | ✖ | ✔ | ✔ | ✔ | ✔ |
| Resubmit rejected | ✖ | ✔ | ✔ | ✔ | ✔ |
| Review approve/reject (with feedback) | ✖ | ✖ | only if explicitly granted (`achievements.review`) | ✔ | ✔ |
| Publish approved content | ✖ | ✖ | ✖ | ✔ | ✔ |
| Manage content modules | ✖ | ✖ | ✖ | ✔ | ✔ |
| Approve/reject/deactivate accounts | ✖ | ✖ | ✖ | ✔ | ✔ |
| Manage roles of users | ✖ | ✖ | ✖ | ✖ | ✔ |
| View audit logs | ✖ | ✖ | ✖ | read-only | ✔ |
| Admin dashboard at all | ✖ | ✖ | ✖ | ✔ | ✔ |

Deny-by-default: any route not in the role's allowlist returns 403 (or redirects to the proper dashboard). Hiding links in the UI is never a security control.

## 4. Modules
Public: Home, About, Faculty dir, Student dir (opt-in per student privacy consent), Achievements, Activities/Events, Projects, Publications, Patents, Gallery, News, Contact form.
Student portal: Dashboard, My Achievements (list/create/edit/show/resubmit), Profile, Password change.
Faculty portal: Dashboard, My Achievements, Profile, Password change.
Admin: Dashboard metrics, Achievement Categories CRUD, Achievement Submissions review workflow, Students CRUD, Faculty CRUD, Activities CRUD, Projects CRUD, Publications CRUD, Patents CRUD, Gallery CRUD (+image upload), News CRUD, Documents/evidence download, Account approvals, Role management (super only), Audit log viewer.

## 5. Achievement Workflow States
`draft → pending → (approved | rejected)`; `rejected → pending` (resubmit by owner); `approved → published`; `published → approved` (unpublish). Full rules in docs/WORKFLOWS.md.

## 6. Acceptance criteria (Definition of Done)
Tracked in docs/DEVELOPMENT_STATUS.md: installable from fresh clone, server-side role separation verified by tests, ownership isolation, enforced transitions, published-only public queries, access-controlled private evidence, responsive Bootstrap 5 UI, complete documentation.
