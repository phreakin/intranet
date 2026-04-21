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
- 
--------------------------------------------------
ENVIRONMENT CONSTRAINTS (CRITICAL)
--------------------------------------------------

This system runs on a single home Linux server with limited resources.

Design constraints:
- low memory usage
- low CPU overhead
- minimal background processes
- avoid heavy frameworks
- avoid unnecessary dependencies
- avoid SPA frontend frameworks
- prioritize server-side rendering (PHP)
- optimize queries and database usage
- efficient caching where appropriate
- avoid bloat

This is NOT a cloud-scale system.
This is a **lean, efficient, local-first intranet system**.

--------------------------------------------------
TECH STACK
--------------------------------------------------

- PHP 8.2+
- MySQL 8+
- jQuery
- Bootstrap 5 (customized heavily for premium UI)
- Composer (only lightweight dependencies)
- optional AI API integration (modular, disabled by default)

--------------------------------------------------
ARCHITECTURE REQUIREMENTS
--------------------------------------------------

The system MUST be modular.

Organize the code into modules such as:

- Core
- Dashboard
- Posts
- Categories
- Tags
- Comments
- Voting
- Favorites
- Reports
- Moderation
- AI
- Admin
- Bookmarklet

Each module must:
- be independently removable
- be independently extendable
- not tightly couple to other modules
- use clean interfaces/services

Use:
- MVC or similar structured architecture
- controllers / services / models separation
- reusable layouts and partials
- config-driven feature toggles (enable/disable modules)

--------------------------------------------------
UI / DESIGN SYSTEM
--------------------------------------------------

Use the premium cinematic UI system:

- dark glass aesthetic
- blurred panels
- soft glow accents (cyan / blue / violet)
- strong typography contrast
- compact, dense layout
- card-based content
- chip/tag UI elements
- investigative / forensic dashboard feel
- “evidence wall” layout inspiration
- subtle hover animations
- premium admin control panels

DO NOT build a generic Bootstrap CRUD UI.

--------------------------------------------------
PHASED DEVELOPMENT PLAN
--------------------------------------------------

Only implement PHASE 1 now, but structure system for future phases.

PHASE 1: Core Intranet (IMPLEMENT NOW)
PHASE 2: Global RBAC system (future)
PHASE 3: CMS system (future)
PHASE 4: Advanced moderation + AI automation (future)
PHASE 5: Extensions / plugins system (future)

--------------------------------------------------
PHASE 1 — CORE INTRANET (BUILD THIS)
--------------------------------------------------

This phase includes:

1. Link submission system
2. URL metadata extraction
3. Categories
4. Tags
5. Dashboard (newest posts)
6. Post display
7. Basic interactions (like/dislike, comments)
8. Basic reporting
9. Bookmarklet
10. Basic admin tools

--------------------------------------------------
FEATURES TO IMPLEMENT
--------------------------------------------------

LINK SUBMISSION
- form to submit URL
- auto-fetch metadata:
    - title
    - description
    - thumbnail
    - site name
    - canonical URL
    - Open Graph data
- allow manual editing before save
- allow comma-separated tags if missing

CATEGORIES
- select existing category
- create new category inline

POST STORAGE
- store post with metadata
- store tags and category
- track timestamps
- show newest first

DASHBOARD
- show posts newest first
- display:
    - title
    - thumbnail
    - description snippet
    - category
    - tags
    - stats (likes, comments)

POST INTERACTIONS
- like (thumbs up)
- dislike (thumbs down)
- comment
- favorite
- bookmark
- report

COMMENTS
- basic commenting system
- store user, content, timestamp
- display under post

REPORT SYSTEM
- reporting flags content
- no deletion
- admin review only

BOOKMARKLET
- JavaScript snippet admins can drag to bookmarks bar
- when clicked:
    - captures current page URL
    - captures title
    - captures metadata if possible
    - opens submission form prefilled

ADMIN BASICS
- view posts
- delete posts
- edit metadata
- view reports

--------------------------------------------------
DATABASE (PHASE 1)
--------------------------------------------------

Create tables:

- posts
- categories
- tags
- post_tags
- comments
- post_votes
- post_favorites
- post_bookmarks
- post_reports

Track:
- like count
- dislike count
- comment count

--------------------------------------------------
MODULAR PREPARATION (IMPORTANT)
--------------------------------------------------

Even though we are only building Phase 1:

Prepare system so we can later add:

PHASE 2:
- global RBAC (roles, permissions)

PHASE 3:
- CMS pages
- content blocks
- layouts

PHASE 4:
- AI moderation
- auto-tagging
- spam detection

PHASE 5:
- plugin system
- feature toggles

DO NOT implement these yet.
ONLY structure for them.

--------------------------------------------------
SECURITY
--------------------------------------------------

- CSRF protection
- prepared statements / PDO
- input validation
- URL validation
- safe metadata fetching

--------------------------------------------------
OUTPUT REQUIREMENTS
--------------------------------------------------

Create:

- full folder structure
- modular architecture
- SQL schema
- configuration files
- submission UI
- dashboard UI
- post detail UI
- comment UI
- bookmarklet code
- metadata extraction service

Explain:
- how modules are organized
- how to extend system later
- how to enable/disable modules

--------------------------------------------------
IMPORTANT RULES
--------------------------------------------------

- no authentication system yet (Phase 2)
- keep system lightweight
- avoid overengineering
- no giant files
- no fake placeholder logic
- build real working code
- optimize for home server performance