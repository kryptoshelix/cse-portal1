# Database Design

MySQL/MariaDB in dev/prod (utf8mb4), SQLite for tests. All tables InnoDB with FK constraints and timestamps. Soft deletes where history matters (achievements, news…).

## Tables
- **users**: id, name, email(unique), password, role enum(`student`,`faculty`,`dept_admin`,`super_admin`), status enum(`pending`,`active`,`rejected`,`deactivated`), can_review_achievements bool(default false), phone, address, avatar_path?, remember_token, timestamps. Indexes: (role,status).
- **students**: id, user_id(FK unique→users.id cascade), roll_number(unique), program, batch(year int), cgpa decimal nullable, public_display_consent bool default false, timestamps.
- **faculty**: id, user_id(FK unique), employee_id(unique), designation, specialization, qualification, joined_on date nullable, office?, bio text nullable, timestamps.
- **achievement_categories**: id, name(unique), slug(unique), description, timestamps.
- **achievements**: id, title, description(text), achievement_category_id FK, achievement_date(date), issuing_organization, level(varchar e.g. National/International), owner_user_id FK→users (the person it belongs to / beneficiary), created_by FK→users (submitting actor), student_id nullable FK→students (when beneficiary is a student), status enum(draft,pending,rejected,approved,published) default draft, rejection_feedback text nullable, submitted_at, reviewed_by nullable FK, reviewed_at, approved_at, published_at, featured bool default false, public_display_consent bool, timestamps, softDeletes. Indexes: (status, achievement_date), (owner_user_id,status), fulltext-ish LIKE search uses title index.
- **achievement_people**: id, achievement_id FK cascade, user_id nullable FK, participant_name string, role_in_achievement varchar — supports multiple participants without forcing accounts. Unique(achievement_id, participant_name).
- **documents**: id, documentable_type/id (nullable morph index OR simpler: achievement_id FK + gallery image path separate) → design: `documentable_type`,`documentable_id` polymorphic; disk('evidence'|'public'), path(random filename), original_name, mime, size_bytes, uploaded_by FK users, is_private bool, timestamps. Private downloads only via authorized endpoint.
- **activities**: id, title, slug unique, description, type enum(event,workshop,seminar,guest_lecture,social), start_date, end_date nullable, venue, organizer, status(published/draft like others: use `is_published` bool + published_at), featured, timestamps, softDeletes.
- **projects**: id, title, description, category, guide_faculty_id nullable FK faculty, student_ids text? → use project_participants? Keep simple: title, description, tech_stack, year, guide, members(string names), is_published, published_at, timestamps, softDeletes.
- **publications**: id, title, authors, journal_or_conference, volume_issue, publication_date, doi/url, faculty_id FK nullable, abstract, is_published, timestamps, softDeletes.
- **patents**: id, title, patent_number, filing_date, status_enum(applied/granted/published_portal?) → filing_status varchar, inventors, faculty_id nullable, is_published, timestamps.
- **gallery**: id, title, caption, image_path (public disk), taken_on date nullable, is_published, published_at, timestamps, softDeletes.
- **news**: id, heading, slug unique, body(text), excerpt, published_at nullable, is_published bool, pinned bool, author_user_id nullable FK, timestamps, softDeletes.
- **contact_messages**: id, name, email, subject, message, created_at (public form; stored only).
- **audit_logs**: id, actor_user_id nullable FK, actor_name snapshot, action(string), auditable_type/id nullable index, description, meta json (sanitized), ip_address varchar45, user_agent, created_at, updated_at.
- Laravel standard: password_reset_tokens, sessions, cache, jobs.

## Deletion safety
FKs: ON DELETE CASCADE for owned rows (students/faculty per user optional — actually RESTRICT to keep directory stable; users soft-deactivated not deleted). Content soft-deleted. Documents reference paths cleaned by service, never raw unlink in controllers.

## Migration order
users(+reset/sessions) → students/faculty → categories → achievements → achievement_people → documents → activities/projects/publications/patents/gallery/news → contact_messages → audit_logs.
