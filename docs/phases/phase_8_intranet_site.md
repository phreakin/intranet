Extend the existing “Home Intranet” system.

This is Phase 8 of the intranet system.

The system already includes:
- Core intranet content/link platform
- Authentication + OAuth
- User profiles and badges
- Admin dashboard
- CMS
- RBAC
- AI moderation + admin intelligence
- Automation + analytics
- Session/IP tracking
- Ban system
- ModerationEngine module
- REST API
- external integrations
- RSS/import/export
- backup/restore
- bookmarklet and capture architecture

Now expand the system into a modular ecosystem with:
- plugin/module framework
- personalization
- saved dashboards
- configurable feeds
- notification system
- internal app framework
- user-tunable scoring and recommendation behavior

Maintain all constraints:
- single home Linux server
- limited resources
- modular architecture
- premium dark cinematic UI
- efficient MySQL usage
- avoid unnecessary bloat
- keep background tasks lightweight and controllable

--------------------------------------------------
PRIMARY GOALS
--------------------------------------------------

1. Build a plugin/module ecosystem
2. Add user personalization
3. Add saved dashboard layouts and custom feeds
4. Add notifications
5. Add an internal “apps” framework
6. Add user/admin-tunable scoring and recommendation preferences
7. Keep the system modular, removable, and efficient

--------------------------------------------------
PLUGIN / MODULE ECOSYSTEM
--------------------------------------------------

Extend the existing module system into a real plugin-capable architecture.

Requirements:
- each module/plugin can register:
    - routes
    - admin panels
    - navigation items
    - dashboard widgets
    - settings sections
    - permissions
    - database migrations
    - scheduled jobs
- modules can be enabled/disabled from admin UI
- disabled modules should not break core system
- module metadata should include:
    - name
    - slug
    - version
    - description
    - author
    - dependencies
    - enabled flag

Create a module registry and loader.

Suggested structure:
- /modules/{ModuleName}/
    - module.json or config.php
    - routes.php
    - services/
    - views/
    - migrations/
    - assets/

Admin UI should show:
- installed modules
- enabled/disabled state
- version
- dependencies
- health/issues
- quick enable/disable actions

Do NOT overengineer a marketplace.
This is a private extensible system for a home intranet.

--------------------------------------------------
PERSONALIZATION SYSTEM
--------------------------------------------------

Implement user personalization features.

Users should be able to configure:
- preferred categories
- preferred tags
- hidden categories/tags
- favorite content types
- dashboard layout preferences
- notification preferences
- recommendation preference weights
- default sort modes
- saved filters

Store personalization settings per user.

Use personalization to influence:
- dashboard feed ordering
- recommendation ranking
- what widgets appear first
- what content is highlighted

--------------------------------------------------
SAVED DASHBOARDS
--------------------------------------------------

Allow users to create and save dashboard views.

A dashboard view can include widgets such as:
- newest posts
- trending posts
- favorite categories
- bookmark list
- recommended content
- moderation queue (for moderators/admins)
- analytics panels
- CMS updates
- activity feed

Requirements:
- users can save multiple dashboard layouts
- users can rename, duplicate, and delete layouts
- one layout can be marked default
- layout configuration stored in database
- drag/reorder widgets if feasible with lightweight JS
- admin can optionally define system default dashboards

--------------------------------------------------
CUSTOM FEEDS
--------------------------------------------------

Implement configurable feeds.

Users should be able to create custom feeds based on:
- category
- tag
- content type
- author
- popularity
- recency
- bookmarked/favorited
- AI score or recommendation score if applicable

Examples:
- “My Dev Feed”
- “Trending Tutorials”
- “Admin Watchlist”
- “Funny + Popular”
- “Moderation Priority Feed”

Feeds should be:
- savable
- shareable internally if permitted
- reusable in dashboard widgets

--------------------------------------------------
NOTIFICATION SYSTEM
--------------------------------------------------

Implement an internal notification system.

Notifications may include:
- comment on your post
- reply to your comment
- report resolved
- badge awarded
- moderation action
- content from favorite category
- recommended content
- admin alerts
- system alerts for admins

Requirements:
- unread/read status
- notification center dropdown/panel
- notification history page
- per-user notification settings
- optional digest mode instead of high-frequency alerts
- lightweight and efficient

Suggested tables:
- notifications
- notification_preferences

Do NOT implement heavy real-time websocket infrastructure unless truly lightweight.
Polling or refresh-based updates are acceptable if efficient.

--------------------------------------------------
INTERNAL APPS FRAMEWORK
--------------------------------------------------

Add a lightweight internal “apps” framework.

The goal:
allow the intranet to grow into a platform where modules behave like internal apps.

Examples of future apps:
- links/content app
- CMS app
- moderation app
- analytics app
- bookmarks app
- knowledge base app
- reports app

Requirements now:
- app registry
- app navigation model
- app metadata
- app-to-module relationship
- admin ability to enable/disable app visibility

This should work as an abstraction layer over the module system.

--------------------------------------------------
SCORING / RECOMMENDATION PREFERENCES
--------------------------------------------------

Allow admins and optionally users to tune recommendation/scoring behavior.

Examples:
- prioritize recency more
- prioritize comments more
- prioritize votes more
- reduce weight for controversial content
- boost favorite categories
- reduce repeated content
- tune “Hot / Trending / Popular” thresholds

Requirements:
- scoring settings stored in DB
- configurable through UI
- explainable defaults
- safe validation
- preserve admin override ability

Use this to influence:
- post status chips
- recommendations
- feed ranking
- dashboard widgets

--------------------------------------------------
WIDGET SYSTEM
--------------------------------------------------

Create a reusable widget system for dashboards.

Widgets may include:
- stat summaries
- content lists
- moderation queues
- analytics tables/charts
- notifications
- recommendation panels
- recent comments
- favorite items

Requirements:
- modular widget registration
- widget enable/disable
- widget config per dashboard
- lightweight rendering

--------------------------------------------------
ADMIN PERSONALIZATION + CONTROL
--------------------------------------------------

Extend admin dashboard with:
- dashboard builder
- widget manager
- module manager
- app visibility manager
- scoring rules manager
- notification system settings
- user personalization oversight if appropriate

Admin should be able to:
- define defaults
- override certain system-wide settings
- inspect plugin/app/module health

--------------------------------------------------
DATABASE ADDITIONS
--------------------------------------------------

Add tables as needed for:
- modules
- module_settings
- apps
- user_dashboard_layouts
- dashboard_widgets
- custom_feeds
- notifications
- notification_preferences
- scoring_profiles
- user_personalization

Keep schema efficient and normalized.

--------------------------------------------------
UI REQUIREMENTS
--------------------------------------------------

Maintain premium dark-glass cinematic UI.

Add UI for:
- dashboard builder
- custom feed builder
- notifications center
- module/app manager
- scoring settings panel
- personalization settings

Visual style:
- control-room dashboard
- investigative metadata panels
- soft glows
- chips and status pills
- modular widgets
- reorderable panels where practical
- compact information density

Do NOT let the UI degrade into generic admin tables.

--------------------------------------------------
PERFORMANCE REQUIREMENTS
--------------------------------------------------

Because this runs on a limited home Linux server:
- make notifications lightweight
- avoid heavy real-time infra
- make widget queries efficient
- cache repeated dashboard queries where useful
- allow disabling expensive widgets/modules
- paginate where appropriate
- keep personalization logic efficient

--------------------------------------------------
API / INTEGRATION EXPANSION
--------------------------------------------------

If helpful, extend the API to support:
- dashboard layouts
- custom feeds
- notifications
- module/app metadata
- personalization settings

Keep permission enforcement consistent.

--------------------------------------------------
OUTPUT REQUIREMENTS
--------------------------------------------------

Create:
- plugin/module ecosystem improvements
- module registry UI
- internal apps framework
- user personalization system
- saved dashboards
- custom feeds
- notification system
- scoring/recommendation tuning UI
- widget system
- migrations/schema updates

Explain:
- how modules/plugins work
- how apps differ from modules
- how saved dashboards are stored
- how notifications are delivered
- how scoring preferences affect recommendations
- how to keep the system lightweight on a small home Linux server

--------------------------------------------------
IMPORTANT RULES
--------------------------------------------------

- do not break previous phases
- keep everything modular
- no giant files
- no unnecessary heavy frameworks
- preserve premium cinematic UI
- keep admin control over defaults
- optimize for a single home Linux server