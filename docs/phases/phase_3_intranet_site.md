Extend the existing “Home Intranet” system.

This is Phase 3.

The system already includes:
- Core intranet link system
- Authentication with OAuth
- User profiles and badges
- Admin dashboard
- AI moderation and admin intelligence
- Modular architecture

Now expand it into a full platform with:
- CMS capabilities
- hardened RBAC system
- extensibility foundation

Maintain all previous constraints:
- single home Linux server
- low resource usage
- modular architecture
- premium dark cinematic UI
- no unnecessary bloat

--------------------------------------------------
PRIMARY GOALS
--------------------------------------------------

1. Add a CMS system for internal content (not just links)
2. Expand RBAC into a real permission engine
3. Prepare system for plugin/module extensibility
4. Maintain performance and modularity

--------------------------------------------------
CMS SYSTEM (IMPLEMENT)
--------------------------------------------------

Add a full CMS module.

Users (based on permissions) should be able to create:

- Pages
- Articles
- Internal documentation
- Guides
- Notes

Each CMS item should support:
- title
- slug
- body content (rich text editor)
- excerpt
- tags
- categories
- author
- status (draft, published, archived)
- created_at
- updated_at

Features:
- WYSIWYG editor (lightweight, not heavy)
- markdown support preferred
- revision history (basic versioning)
- preview mode
- publish/unpublish toggle
- scheduled publishing (optional if lightweight)

Routing:
- /pages/{slug}
- /articles/{slug}

CMS content should integrate with:
- tags
- categories
- search
- admin dashboard

--------------------------------------------------
RBAC SYSTEM (HARDEN)
--------------------------------------------------

Replace/expand the basic role system with a full RBAC engine.

Implement:

Tables:
- roles
- permissions
- role_permissions
- user_roles

Permissions should be granular, examples:

Posts:
- create_post
- edit_post
- delete_post
- moderate_post

Comments:
- create_comment
- delete_comment
- moderate_comment

CMS:
- create_page
- edit_page
- publish_page
- delete_page

Admin:
- access_admin
- manage_users
- manage_roles
- manage_settings

AI:
- approve_ai_actions
- override_ai

System:
- view_logs
- manage_modules

Requirements:
- permission checks at controller level
- permission checks in UI (hide actions if unauthorized)
- flexible permission assignment to roles
- admin UI to manage roles and permissions

--------------------------------------------------
ADMIN RBAC INTERFACE
--------------------------------------------------

Extend admin dashboard to include:

- Role management UI
- Permission assignment UI
- User-role assignment
- Permission audit view (who can do what)

UI must match premium admin dashboard style.

--------------------------------------------------
CMS + POSTS INTEGRATION
--------------------------------------------------

Integrate CMS with existing post system:

- posts and CMS content should share:
    - tags
    - categories
- dashboard should optionally show:
    - latest posts
    - latest CMS content

Add filters:
- content type (post vs page vs article)

--------------------------------------------------
SEARCH SYSTEM
--------------------------------------------------

Implement lightweight search:

- search posts
- search CMS content
- search tags/categories

Use:
- MySQL full-text search if possible
- fallback LIKE queries if needed

Keep it lightweight.

--------------------------------------------------
EXTENSIBILITY FOUNDATION
--------------------------------------------------

Prepare the system for future plugins/modules.

Implement a basic module system:

- each module has:
    - config file
    - enable/disable flag
    - service registration
- modules can register:
    - routes
    - admin panels
    - UI components

Create structure like:
- /modules/{ModuleName}

Add:
- module loader
- module registry

Do NOT overengineer plugin system yet.
Just create a clean foundation.

--------------------------------------------------
AI EXPANSION (PHASE 3)
--------------------------------------------------

Extend AI system to support CMS:

- suggest tags for CMS content
- summarize long articles
- flag low-quality or duplicate content
- recommend linking related posts/pages

Add admin insight:
- “These pages are similar”
- “This content may be duplicated”
- “This page has low engagement”

--------------------------------------------------
SELF-AWARE SYSTEM EXPANSION
--------------------------------------------------

Extend admin intelligence to include:

- unused CMS pages
- outdated content detection
- broken internal links
- orphaned content (no category/tags)
- permission misconfigurations
- role conflicts

Examples:
- “3 pages have no category”
- “2 roles have conflicting permissions”
- “Page X has not been updated in 180 days”

--------------------------------------------------
DATABASE ADDITIONS
--------------------------------------------------

Add tables:

- pages
- page_revisions
- permissions
- role_permissions
- module_registry

Update existing tables as needed.

--------------------------------------------------
UI REQUIREMENTS
--------------------------------------------------

Maintain:

- dark glass aesthetic
- cinematic panels
- admin intelligence dashboard
- compact data-rich layout

Add:

CMS UI:
- editor screen
- page list
- revision history view

RBAC UI:
- role editor
- permission matrix
- assignment panels

Module UI:
- module enable/disable list

--------------------------------------------------
SECURITY
--------------------------------------------------

- enforce RBAC on all routes
- validate all CMS input
- sanitize rich text content
- protect against XSS
- enforce permission checks strictly

--------------------------------------------------
OUTPUT REQUIREMENTS
--------------------------------------------------

Create:

- CMS module
- RBAC system
- permission middleware
- admin role/permission UI
- CMS editor UI
- search system
- module loader system
- updated database schema/migrations

Explain:

- how RBAC works
- how CMS integrates with posts
- how modules can be added later
- how permissions are enforced
- how to extend system further

--------------------------------------------------
IMPORTANT RULES
--------------------------------------------------

- do NOT break existing system
- do NOT introduce heavy frameworks
- keep everything modular
- keep everything efficient
- avoid overengineering plugin system
- maintain premium UI
- maintain clean architecture