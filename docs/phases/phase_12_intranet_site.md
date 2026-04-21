BASE REPOSITORY:
https://github.com/phreakin/intranet.git

INSTRUCTIONS:
- Clone or use existing repo
- Scan full codebase before making changes
- Do not recreate existing modules
- Extend existing architecture
- Maintain compatibility with all prior phases
- Use migrations for schema changes
- Validate all routes and UI after changes

This is Phase 6.

The system already includes:
- Core intranet/content platform
- Authentication + OAuth
- User profiles and badges
- Admin dashboard
- CMS
- RBAC
- AI moderation + admin intelligence
- Automation + analytics
- Session/IP tracking
- Ban system
- ModerationEngine module with cases, risk scoring, investigation views, and action logs

Now expand the platform into an ecosystem-ready system with:
- REST API
- external integrations
- ingestion connectors
- browser capture tools
- backup/restore capabilities
- import/export capabilities
- platform automation hooks

Maintain all existing constraints:
- runs on a single home Linux server
- limited resources
- modular architecture
- premium dark cinematic UI
- low bloat
- efficient MySQL usage
- keep background processing lightweight and controllable

--------------------------------------------------
PRIMARY GOALS
--------------------------------------------------

1. Add a robust internal/external API layer
2. Add import pipelines and external content ingestion
3. Add richer quick-capture tools (bookmarklet expansion / extension-ready architecture)
4. Add backup / restore and export tooling
5. Add admin integration controls
6. Keep everything modular and efficient

--------------------------------------------------
API LAYER
--------------------------------------------------

Build a versioned REST API.

Base path:
- /api/v1/

Required API domains:
- auth/session info
- users
- profiles
- posts
- comments
- categories
- tags
- CMS pages/articles
- moderation
- cases
- reports
- audit logs
- analytics
- recommendations
- settings
- integrations
- backups/imports/exports

Requirements:
- JSON responses
- consistent response structure
- pagination support
- filter/sort/query support
- proper permission enforcement using RBAC
- route middleware where needed
- admin-only endpoints protected properly
- activity logging for important API actions

Example endpoints:
- GET /api/v1/posts
- GET /api/v1/posts/{id}
- POST /api/v1/posts
- PUT /api/v1/posts/{id}
- DELETE /api/v1/posts/{id}
- GET /api/v1/comments
- POST /api/v1/comments
- GET /api/v1/users/{id}
- GET /api/v1/moderation/cases
- POST /api/v1/moderation/cases
- GET /api/v1/analytics/summary
- GET /api/v1/recommendations
- GET /api/v1/settings
- PUT /api/v1/settings
- POST /api/v1/imports/rss
- POST /api/v1/exports/posts
- POST /api/v1/backups/run

--------------------------------------------------
EXTERNAL INGESTION / IMPORT CONNECTORS
--------------------------------------------------

Implement modular import connectors for external content.

Phase 6 connectors should include:

1. RSS / Atom feed ingestion
- admin can add feed URL
- system fetches items
- maps feed items into link/content posts
- deduplicates by URL/guid/hash
- can auto-categorize or route to moderation/review

2. Generic webpage import
- admin can submit a URL to import
- metadata extraction already exists; expose it via integration flow

3. CSV import
- import post/link/content metadata in bulk
- import categories/tags if configured
- preview before commit

4. JSON import/export
- import/export structured platform data
- support backup migrations and portability

All imports must:
- preserve source info
- log actions
- allow preview/review before commit when appropriate
- avoid silent duplication

--------------------------------------------------
INTEGRATION REGISTRY
--------------------------------------------------

Create an Integrations module.

Admin can manage:
- RSS feeds
- import connectors
- external metadata sources
- AI provider settings
- webhook endpoints (if enabled)

Fields:
- integration type
- status
- last run
- next run
- failure count
- config JSON
- enabled flag

Admin UI should show:
- connected integrations
- health/status
- recent runs
- failures
- quick actions

--------------------------------------------------
BOOKMARKLET EXPANSION + BROWSER CAPTURE
--------------------------------------------------

Expand the bookmarklet system.

Existing bookmarklet should be upgraded to:
- capture page URL
- capture title
- capture description
- capture Open Graph image
- capture selected text on page if available
- capture current tab metadata
- optionally pass tags/category hints

Also build the architecture for a future browser extension:
- define capture endpoint(s)
- define payload format
- define CSRF-safe/local trusted usage approach
- create a documented extension-ready API contract

If lightweight enough, create a minimal browser-extension starter structure for:
- quick save current page
- prefilled submission modal/window

--------------------------------------------------
BACKUP / RESTORE SYSTEM
--------------------------------------------------

Create a proper backup/restore module.

Required capabilities:
- backup database
- backup uploaded files
- backup generated metadata/artifacts if relevant
- create backup manifests
- show backup history in admin
- allow manual backup run
- allow downloadable export packages if appropriate

Restore capabilities:
- admin-triggered restore workflow
- restore from selected backup
- validation before restore
- restoration logs
- warnings before destructive restore actions

Add tables/logs for:
- backup_runs
- restore_runs

Admin UI should show:
- backup status
- last successful backup
- backup size
- restore history
- failures

Keep implementation practical for a home server.

--------------------------------------------------
IMPORT / EXPORT TOOLING
--------------------------------------------------

Add admin tools to export:

- posts
- comments
- categories
- tags
- CMS pages
- users
- moderation cases
- audit logs
- analytics summaries

Support formats like:
- JSON
- CSV where appropriate

Add import/export center UI:
- choose export type
- date range
- filters
- run export
- download generated file

For import:
- upload file
- preview records
- validate
- commit
- log result

--------------------------------------------------
WEBHOOKS / AUTOMATION HOOKS
--------------------------------------------------

Implement a lightweight webhook system.

Possible events:
- post_created
- comment_created
- post_reported
- user_banned
- moderation_case_opened
- backup_completed
- import_completed

Requirements:
- admin can register outgoing webhook endpoints
- store secret/token if needed
- log success/failure
- retry with limits
- disable failing hooks if configured

Keep this lightweight and optional.

--------------------------------------------------
ANALYTICS / REPORTING API
--------------------------------------------------

Expose analytics through admin UI and API.

Add report endpoints and pages for:
- post activity summary
- comment activity summary
- moderation activity summary
- user activity summary
- category usage
- tag usage
- stale content report
- duplicate content report
- broken link report

Allow export of reports.

--------------------------------------------------
MOBILE / RESPONSIVE ADMIN ACCESS
--------------------------------------------------

Do not build a native mobile app.
But improve responsive behavior so core admin and moderation views work on:
- tablet
- phone
- smaller laptop screens

Make sure:
- dashboard cards stack cleanly
- moderation queues remain usable
- case view works on narrower screens
- admin quick actions remain accessible

--------------------------------------------------
PERMISSION MODEL EXPANSION
--------------------------------------------------

Extend RBAC for integrations and platform tools.

Permissions should include examples like:
- manage_integrations
- run_imports
- run_exports
- run_backups
- run_restores
- manage_webhooks
- view_api_logs
- use_capture_tools

Apply permissions consistently in UI and controller/API logic.

--------------------------------------------------
AI + INTEGRATIONS
--------------------------------------------------

Allow AI provider configuration through admin settings.

Use AI optionally for:
- feed item summarization
- duplicate detection during imports
- tag suggestion for imported content
- moderation triage for imported items
- intelligent categorization suggestions

Important:
- AI actions must be logged
- AI never silently destroys imported data
- admins can review or override

--------------------------------------------------
DATABASE ADDITIONS
--------------------------------------------------

Add tables as needed for:
- integrations
- integration_runs
- rss_sources
- import_jobs
- export_jobs
- webhook_endpoints
- webhook_deliveries
- backup_runs
- restore_runs
- api_tokens if needed for internal trusted tools only
- api_request_logs if lightweight and useful

If API tokens are added for trusted internal tools, make them optional and admin-managed.
Do NOT introduce public multi-tenant auth complexity.

--------------------------------------------------
UI REQUIREMENTS
--------------------------------------------------

Maintain premium dark-glass cinematic style.

Add new admin sections/pages for:
- API / integrations dashboard
- feed/import management
- export center
- backup/restore center
- webhook management
- browser capture tools / bookmarklet instructions
- system interoperability dashboard

Visual style:
- intelligence console
- modular control panels
- compact data-rich tables
- status chips
- run history cards
- health indicators
- forensic-style logs

--------------------------------------------------
PERFORMANCE REQUIREMENTS
--------------------------------------------------

Because this runs on a limited home Linux server:
- imports should be queueable or chunked if large
- backup jobs should be controlled and logged
- analytics/report exports should avoid blocking requests when large
- make expensive integrations optional
- allow disabling integrations individually
- cache where sensible
- keep API efficient and paginated

--------------------------------------------------
OUTPUT REQUIREMENTS
--------------------------------------------------

Create:
- versioned REST API
- integrations module
- RSS ingestion system
- import/export center
- backup/restore module
- webhook system
- improved bookmarklet / extension-ready capture flow
- admin UI for all new platform features
- migrations/schema updates
- docs for API and capture payloads

Explain:
- how API versioning works
- how imports are processed
- how backups/restores work
- how webhooks work
- how to enable/disable expensive features
- how the architecture stays modular for future additions

--------------------------------------------------
IMPORTANT RULES
--------------------------------------------------

- do not break existing phases
- keep modules isolated
- keep code production-grade
- no giant files
- no heavy unnecessary frameworks
- preserve traceability, moderation logs, and admin control
- preserve premium cinematic UI
- optimize for a small home Linux server