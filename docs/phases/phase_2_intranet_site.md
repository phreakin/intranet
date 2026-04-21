Build a modular private home intranet system called “Home Intranet”.

This project runs on a single home Linux server with limited resources.

Important constraints:
- optimize for low memory and low CPU usage
- avoid unnecessary dependencies
- use server-side rendering
- use modular PHP architecture
- keep everything maintainable and removable by module
- build real working code, not pseudocode

Use:
- PHP 8.2+
- MySQL 8+
- jQuery
- Bootstrap 5 with heavy custom styling
- Composer packages where needed
- optional external AI APIs for moderation/admin intelligence

Use the premium UI branding established previously:
- premium dark glass
- cinematic
- forensic / evidence-wall inspired
- blurred surfaces
- soft cyan / blue / violet glows
- compact information-dense layout
- premium admin dashboard feel
- strong typography hierarchy
- chip-based metadata
- modular control-panel UI

Do NOT build a generic CRUD intranet.
Do NOT build a generic Bootstrap theme.
Make it feel like a premium private intelligence and moderation system.

--------------------------------------------------
BUILD STRATEGY
--------------------------------------------------

Implement this in phases, but in this prompt build:

PHASE 1:
- Core Intranet
- Global Authentication
- Social OAuth
- Basic user system
- Protected admin area

PHASE 2:
- Admin Intelligence
- AI moderation
- AI-assisted tagging
- automatic post-status tagging
- system self-awareness in admin dashboard

Structure the code so later phases can add:
- full CMS
- advanced RBAC expansion
- plugin/extension modules
- richer analytics

--------------------------------------------------
ARCHITECTURE
--------------------------------------------------

Build the app as a modular PHP system with modules such as:
- Core
- Authentication
- Users
- Dashboard
- Posts
- Categories
- Tags
- Comments
- Voting
- Favorites
- Bookmarks
- Reports
- Moderation
- AI
- Admin
- Bookmarklet

Each module must:
- be independently maintainable
- be removable or replaceable later
- avoid tight coupling
- use services/interfaces where appropriate

Use:
- controllers
- services
- models
- reusable views/layouts/partials
- config-driven module toggles where practical

--------------------------------------------------
PHASE 1 — CORE INTRANET + AUTH (IMPLEMENT NOW)
--------------------------------------------------

Implement these features:

1. Link submission
- user submits a URL
- system fetches metadata:
    - title
    - description
    - thumbnail
    - site name
    - canonical URL
    - author if available
    - publish date if available
    - Open Graph metadata
    - Twitter card metadata
    - keywords/tags if available
- user can edit fetched metadata before save
- if tags/keywords are missing, allow comma-separated manual tags

2. Categories
- choose an existing category
- or create a new category inline during submission

3. Posts
- store posts in database
- show newest first on main dashboard
- display:
    - title
    - thumbnail
    - description excerpt
    - category
    - tags
    - submitter
    - created date
    - stats

4. Post interactions
- like
- dislike
- favorite
- bookmark
- share
- report

5. Comments
- add comments to posts
- display comments clearly
- store user, timestamp, body, moderation state

6. Authentication
   Implement global authentication.

Requirements:
- support social OAuth
- include at least:
    - Google
    - Facebook
    - GitHub if practical
- use secure session-based auth
- no fake auth
- protect admin routes globally

7. Basic user system
- user profiles
- display name
- avatar
- bio
- profile stats
- badges
- role display

8. Admin protected area
- admin dashboard must be protected by auth
- admins can:
    - edit/delete posts
    - review reports
    - manage categories
    - manage tags
    - manage users/basic badges
    - moderate comments

9. Bookmarklet
   Create a JavaScript bookmarklet for admins that:
- can be dragged to the browser bookmarks bar
- when clicked on a page:
    - opens the submission form
    - prefills URL
    - prefills page title
    - prefills meta description
    - prefills Open Graph image if available
    - prefills tags/keywords if available

--------------------------------------------------
PHASE 2 — ADMIN INTELLIGENCE + AI (IMPLEMENT NOW)
--------------------------------------------------

Implement an AI-assisted moderation and operational intelligence layer.

1. AI moderation
   Add AI integration for:
- automatic post tagging
- automatic comment tagging
- spam detection
- suspicious/off-topic detection
- moderation suggestions
- duplicate-content suggestions

2. AI admin assistance
   The admin dashboard should include system intelligence such as:
- posts missing thumbnails
- posts missing descriptions
- possibly duplicate posts
- tag duplication or messy taxonomy
- unusual report spikes
- high-activity posts that may need moderation
- stale categories
- low-quality / underused tags
- recommendations for admin cleanup

3. AI logging
   AI actions must be logged.
   Store:
- input context
- output suggestion
- confidence if available
- action recommended
- whether admin accepted/rejected it

4. No silent destructive AI actions
- AI may suggest removal or tagging
- admin can approve/override
- do not silently delete content without a log and admin visibility

--------------------------------------------------
AUTOMATIC POST STATUS TAGGING
--------------------------------------------------

Automatically apply dynamic state labels to posts such as:
- New
- Hot
- Popular
- Trending
- Rising
- Discussed
- Controversial

These should be computed based on:
- recency
- vote velocity
- comments
- favorites/bookmarks
- report activity if relevant

Show these as chips/badges on post cards and detail pages.

--------------------------------------------------
SELF-AWARE ADMIN DASHBOARD
--------------------------------------------------

The admin dashboard should act like an operational intelligence center.

Show:
- new reports
- AI-flagged posts
- AI-flagged comments
- hot/trending content
- moderation backlog
- broken metadata fetches
- tag/category cleanup recommendations
- unresolved system issues
- admin recommendations

Examples:
- “8 posts are missing thumbnails”
- “3 posts may be duplicates”
- “Category X has had no activity in 60 days”
- “Report volume increased today”
- “AI recommends merging these two tags”

This is “self-aware” in the operational/admin sense, not in a sci-fi sense.

--------------------------------------------------
DATABASE REQUIREMENTS
--------------------------------------------------

Create MySQL schema/tables for at minimum:
- users
- roles
- permissions
- user_roles
- oauth_accounts
- badges
- user_badges
- posts
- categories
- tags
- post_tags
- comments
- comment_tags
- post_votes
- post_favorites
- post_bookmarks
- post_reports
- comment_reports
- moderation_logs
- ai_moderation_logs
- settings

Track:
- like count
- dislike count
- comment count
- favorite count
- bookmark count
- report count
- automatic post-status labels

--------------------------------------------------
SECURITY
--------------------------------------------------

Implement:
- CSRF protection
- secure session handling
- OAuth best practices
- input validation
- output escaping
- prepared statements / PDO or safe database abstraction
- URL validation
- safe remote metadata fetching
- permission checks for admin/mod actions

--------------------------------------------------
USER-FACING PAGES
--------------------------------------------------

Build:
- main dashboard / newest posts
- category page
- tag page
- single post page
- submission page
- edit post page
- favorites page
- bookmarks page
- user profile page
- admin dashboard
- reports page
- moderation queue
- comment management page
- user/badge management page

--------------------------------------------------
TECHNICAL EXPECTATIONS
--------------------------------------------------

Build this as a real modular PHP application.

Use:
- organized folder structure
- reusable layouts and partials
- service layer for metadata extraction
- OAuth integration layer
- AI service layer
- admin dashboard views
- jQuery for enhanced form/admin UX
- migrations or SQL schema files
- seed data

--------------------------------------------------
OUTPUT REQUIREMENTS
--------------------------------------------------

Create:
- full file structure
- SQL schema or migrations
- configuration example
- installation instructions
- seed data
- bookmarklet code
- metadata extraction service
- global auth system
- social OAuth integration
- protected admin dashboard
- user profile system
- badges system
- AI moderation layer
- admin intelligence dashboard
- automatic post-status tagging logic

Explain:
- module structure
- auth + OAuth structure
- AI moderation flow
- admin intelligence flow
- how to add/remove modules later
- how to keep the system lightweight for a home Linux server

--------------------------------------------------
IMPORTANT RULES
--------------------------------------------------

- build real working code
- avoid giant files
- avoid unnecessary bloat
- keep it modular
- optimize for a resource-limited home Linux server
- maintain the premium dark cinematic UI branding
- do not silently delete content via AI
- protect admin globally with auth