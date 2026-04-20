# Home Intranet System

A modular private home intranet intelligence system for a single Linux server (PHP 8.2+, MySQL 8+, jQuery, Bootstrap 5 SSR-first).

## What is implemented

- Modular architecture with feature toggles and clean separation of controllers/services/repositories/views
- Core intranet feed with link-first submissions and compact premium dark-glass UI
- Global authentication (local + OAuth flow support for Google/Facebook/GitHub)
- RBAC with Admin / Moderator / Member and global admin route protection
- User profiles with role display, badge display, and activity stats
- Post system with categories, tags, comments, interactions, reporting, favorites, bookmarks
- Two-step URL submission workflow with metadata fetch + editable metadata before save
- Automatic dynamic post status tagging (`New`, `Trending`, `Rising`, `Popular`, `Hot`, `Discussed`, `Controversial`, `Needs Review`)
- Admin dashboard with self-aware operational recommendations
- Moderation queue with comment hide/unhide and moderation tagging
- Admin management actions for posts, taxonomy, and user role/badge assignments
- AI moderation integration layer with auditable logs and optional auto-action
- Bookmarklet for admin quick-submission prefill
- Full MySQL schema + seed data for required entities

## File structure

- `public/index.php` - front controller and route map
- `app/Core` - config, router, DB, auth, CSRF, view helpers
- `app/Modules/**` - modular domain folders (Authentication, Users, Posts, Moderation, AI, Admin, etc.)
- `resources/views/**` - reusable SSR layouts and module pages
- `public/assets/css/theme.css` - premium dark glass cinematic theme
- `public/assets/js/app.js` - lightweight jQuery interactions
- `database/migrations/001_initial_schema.sql` - complete schema
- `database/seeds/001_seed.sql` - roles, permissions, badges, admin seed
- `config/*.php` - application config, oauth providers, feature toggles

## Installation

1. Copy env:
   ```bash
   cp .env.example .env
   ```
2. Configure MySQL and OAuth values in `.env`.
3. Create database and apply schema:
   ```bash
   mysql -u root -p intranet < database/migrations/001_initial_schema.sql
   mysql -u root -p intranet < database/seeds/001_seed.sql
   ```
4. Run locally:
   ```bash
   php -S 127.0.0.1:8080 -t public
   ```
5. Default admin seed account:
   - email: `admin@local.intranet`
   - password: `password`

## Security model

- Session-based auth with regeneration on login
- CSRF token validation on write actions
- Prepared PDO statements (no interpolated user values)
- URL validation before metadata fetch
- Safe metadata fetch guard that blocks localhost/private-network targets
- Output escaping in views
- RBAC gate checks on admin/mod endpoints
- AI actions logged in `ai_moderation_logs` with input context and admin decision trail; destructive AI actions require explicit `AI_AUTO_REMOVE=1`

## AI moderation flow

1. New post/comment enters pipeline.
2. `AiModerationService` produces recommendation + confidence.
3. Decision and payload are written to `ai_moderation_logs`.
4. Admin dashboard surfaces pending AI queue.
5. Admin/mod overrides are applied through moderation actions and recorded in `moderation_logs`.

## Self-aware admin recommendations

Dashboard computes and surfaces:

- unresolved report queue volume
- missing metadata quality signals (thumbnail/description gaps)
- duplicate canonical URL suspects
- daily report spikes
- stale categories (unused > 90 days)
- duplicate tags for consolidation

## Module extensibility

- Add a module by creating `app/Modules/<ModuleName>` with controller/service/repository boundaries
- Add/disable behavior via `config/features.php`
- Register routes in `public/index.php`
- Reuse `resources/views/layouts/app.php` for consistent premium UI

## Bookmarklet

Admin page `/admin/bookmarklet` provides a drag-and-drop bookmarklet that opens `/submit` with prefilled URL/title/description/image/keywords from the active page.
