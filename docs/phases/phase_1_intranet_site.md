Build a modular private home intranet system called “Home Intranet”.

This project will be built in phases, but this version should now include:
- Core Intranet
- Global authentication
- Social OAuth
- Admin dashboard
- AI-assisted moderation and admin intelligence
- Advanced user system
- Automatic post-status tagging

--------------------------------------------------
ENVIRONMENT CONSTRAINTS (CRITICAL)
--------------------------------------------------

This system runs on a single home Linux server with limited resources.

Design constraints:
- low memory usage
- low CPU overhead
- minimal background processes
- avoid unnecessary dependencies
- efficient MySQL queries
- server-side rendering preferred
- modular architecture
- features must be toggleable where possible

This is a private home intranet, not a public cloud-scale app.

--------------------------------------------------
TECH STACK
--------------------------------------------------

Use:
- PHP 8.2+
- MySQL 8+
- jQuery
- Bootstrap 5 with heavy custom theming
- Composer packages where needed
- modular PHP architecture
- optional AI integrations via external APIs
- OAuth packages/libraries for social login

--------------------------------------------------
UI / BRANDING REQUIREMENTS
--------------------------------------------------

Use the premium UI branding established earlier.

The UI must feel:
- premium dark glass
- cinematic
- forensic / investigative
- evidence-wall inspired
- modern admin intelligence dashboard
- compact and information-dense
- soft glows
- blurred panels
- strong visual hierarchy
- metadata chips
- premium control surfaces

This should NOT look like a generic CRUD intranet.
This should feel like a premium private intelligence system.

--------------------------------------------------
ARCHITECTURE REQUIREMENTS
--------------------------------------------------

The system MUST be modular.

Organize the code into modules such as:
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
- CMS-ready hooks
- RBAC / Permissions

Each module should:
- be independently maintainable
- be removable or replaceable later
- expose clean services/interfaces
- avoid tight coupling

Use:
- MVC or similarly structured PHP architecture
- controllers / services / models / views separated
- reusable layouts and partials
- configuration-driven feature toggles

--------------------------------------------------
CORE SYSTEM PURPOSE
--------------------------------------------------

This is a private intranet content-sharing and discovery system where users can submit links to:
- YouTube videos
- Facebook videos
- articles
- tutorials
- blog posts
- guides
- reference pages
- other web content

The product should feel like a private Reddit-style content system with categories, tags, moderation, comments, user profiles, and admin intelligence.

--------------------------------------------------
AUTHENTICATION + ACCESS CONTROL
--------------------------------------------------

Implement a global authentication system.

Requirements:
- social OAuth integration
- support at least:
    - Google
    - Facebook
    - GitHub (if practical)
- local session-based auth is acceptable as fallback if needed
- protected admin section
- authenticated user profiles
- global permission checks

Implement RBAC with roles such as:
- Admin
- Moderator
- Member

Admin routes must be protected globally.

--------------------------------------------------
USER SYSTEM
--------------------------------------------------

Implement advanced user features:
- user profiles
- display name
- avatar
- bio
- profile stats
- badges
- role display
- user activity summary
- favorites and bookmarks views

Badges can include examples like:
- Contributor
- Moderator
- Admin
- Top Curator
- Early Member
- Popular Poster

Store badges in the database and display them in user profile and post/comment areas.

--------------------------------------------------
LINK SUBMISSION SYSTEM
--------------------------------------------------

Users should be able to submit a URL.

When a URL is entered, the system should fetch as much metadata as possible:
- title
- description
- thumbnail / preview image
- site name
- canonical URL
- author if available
- publish date if available
- keywords / tags if available
- Open Graph metadata
- Twitter card metadata

If the source does not provide tags/keywords:
- allow user to enter comma-separated tags manually

Users must be able to:
- choose an existing category
- or create a new category

--------------------------------------------------
POST SYSTEM
--------------------------------------------------

Posts are link-first content entries.

Store:
- URL
- metadata
- category
- tags
- submitter
- timestamps
- counts for interactions

Display posts on main index/dashboard:
- newest first
- with title
- thumbnail
- description excerpt
- category
- tags
- status chips
- engagement stats

--------------------------------------------------
AUTOMATIC POST STATUS TAGGING
--------------------------------------------------

Posts should automatically receive dynamic state tags where applicable, such as:
- New
- Hot
- Popular
- Trending
- Rising
- Discussed
- Controversial

These should be computed based on rules such as:
- recency
- vote velocity
- comment activity
- favorites/bookmarks
- report volume if needed

Store or derive these efficiently and show them as chips/badges in the UI.

--------------------------------------------------
POST INTERACTIONS
--------------------------------------------------

Each post should support:
- like
- dislike
- comment
- favorite
- bookmark
- share
- report

Definitions:
- like/dislike = thumbs up/down
- favorite = personal saved favorite
- bookmark = save for later
- share = copyable direct URL
- report = flags content for admin/moderator review

Database should track counts for:
- likes
- dislikes
- comments
- favorites
- bookmarks
- reports

--------------------------------------------------
COMMENT SYSTEM
--------------------------------------------------

Implement a commenting system.

Requirements:
- users can add comments to posts
- comments store user, timestamp, body, moderation state
- comments should support admin moderation
- threaded comments optional, flat comments acceptable initially

--------------------------------------------------
COMMENT MODERATION
--------------------------------------------------

Admins and moderators must be able to:
- delete comments
- hide/unhide comments
- tag comments with moderation tags such as:
    - Informative
    - Funny
    - Off-Topic
    - Interesting
    - Spam
    - Needs Review

Store moderation tags and moderation actions in the database.

--------------------------------------------------
ADMIN DASHBOARD
--------------------------------------------------

The admin section must be a real dashboard, not just plain tables.

It should be globally protected by the authentication system and include:

- reported posts queue
- reported comments queue
- AI moderation queue
- recent uploads/posts
- user activity overview
- flagged content overview
- trending / hot content overview
- system issues and recommendations
- moderation actions panel
- category/tag management
- badge/user management

UI should match the premium dark-glass cinematic admin style.

--------------------------------------------------
AI INTEGRATION
--------------------------------------------------

Implement AI integration for:
- post tagging assistance
- comment tagging assistance
- spam / suspicious content detection
- moderation recommendations
- admin assistance tasks
- summarization of reported content
- detection of potential duplicates or off-topic content

AI must also support admin intelligence:
- surface system issues
- recommend moderation actions
- recommend category cleanup
- recommend tag consolidation
- identify unusual report spikes
- identify inactive or broken sections
- suggest admin attention areas

Important rules:
- AI decisions must be logged
- admin must be able to override AI
- AI can recommend removal/flagging
- AI auto-removal should only happen if explicitly configured
- no silent destructive actions without logs

--------------------------------------------------
SELF-AWARE SYSTEM / ADMIN INTELLIGENCE
--------------------------------------------------

The system should be “self-aware” in the operational sense.

That means the admin dashboard should show:
- current system health
- unresolved moderation backlog
- broken metadata fetches
- posts missing thumbnails/descriptions
- categories with low quality / low usage
- tag duplication issues
- unusual report patterns
- AI confidence warnings
- recommended admin actions

Examples:
- “12 posts are missing thumbnails”
- “Category X has not been used in 90 days”
- “3 posts may be duplicates”
- “Report volume is elevated today”
- “AI suggests merging these similar tags”

This should function like an internal operational intelligence layer.

--------------------------------------------------
BOOKMARKLET
--------------------------------------------------

Create a JavaScript bookmarklet for admins.

Behavior:
- admin drags bookmarklet to browser toolbar
- when clicked on any page, it opens the submission form
- prefill:
    - current URL
    - title
    - description
    - Open Graph image
    - keywords/tags if available

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
- like/dislike counts
- comment counts
- favorite counts
- bookmark counts
- report counts
- post status tags
- user badge associations

--------------------------------------------------
URL METADATA EXTRACTION
--------------------------------------------------

Implement robust server-side metadata extraction in PHP.

Extract:
- HTML title
- meta description
- Open Graph tags
- Twitter card tags
- canonical URL
- preview image
- keywords
- author
- publish date when possible

Handle:
- invalid URLs
- redirects
- timeouts
- missing metadata
- unsupported domains gracefully

--------------------------------------------------
USER-FACING PAGES
--------------------------------------------------

Build:
- main dashboard / newest posts
- category page
- tag page
- single post detail page
- submission page
- edit post page
- favorites page
- bookmarks page
- user profile page
- moderation queue
- admin dashboard
- reports page
- comment management page
- badge/user management page

--------------------------------------------------
SECURITY
--------------------------------------------------

Implement:
- CSRF protection
- prepared statements or PDO / proper database abstraction
- input validation
- OAuth security best practices
- output escaping
- URL validation
- safe metadata fetching
- permission enforcement for admin/mod tools

--------------------------------------------------
TECHNICAL IMPLEMENTATION EXPECTATIONS
--------------------------------------------------

Build this as a real PHP application, not pseudocode.

Use:
- modular folder structure
- reusable views/layouts/partials
- controllers/services/models
- SQL migrations or migration system
- jQuery for interactive moderation and submission UX
- Composer dependencies where needed

--------------------------------------------------
OUTPUT REQUIREMENTS
--------------------------------------------------

Create:
- full file structure
- SQL schema or migrations
- config example
- installation instructions
- seed data
- bookmarklet code
- AI moderation integration layer
- URL metadata extraction service
- admin dashboard
- user profile system
- badges system
- automatic post-status tagging logic

Explain:
- module structure
- auth/RBAC structure
- AI moderation flow
- how to add/remove modules later
- how the self-aware admin recommendations work

--------------------------------------------------
IMPORTANT RULES
--------------------------------------------------

- use the premium branding language described above
- keep architecture modular
- build for a home Linux server with limited resources
- avoid giant files
- avoid unnecessary bloat
- no fake placeholder logic
- create real working code
- keep future expansion possible for CMS and additional modules