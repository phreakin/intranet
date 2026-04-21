Extend the existing “Home Intranet” system.

This is Phase 4.

The system already includes:
- Core intranet link/content system
- Authentication and OAuth
- User profiles and badges
- Admin dashboard
- AI moderation and admin intelligence
- CMS system
- RBAC system
- modular architecture

Now expand it into a smarter internal platform with:
- automation
- recommendation engines
- scheduled maintenance/intelligence jobs
- content relationships
- system analytics
- operational dashboards

Maintain all existing constraints:
- single home Linux server
- limited resources
- modular architecture
- premium dark cinematic UI
- avoid unnecessary bloat
- efficient MySQL usage
- keep background processing lightweight

--------------------------------------------------
PRIMARY GOALS
--------------------------------------------------

1. Add automation and scheduled jobs
2. Add recommendation engines for content and admin workflow
3. Add content relationship / related-content intelligence
4. Add analytics and operational visibility
5. Improve self-awareness of the system
6. Keep everything modular and efficient

--------------------------------------------------
AUTOMATION LAYER
--------------------------------------------------

Implement a lightweight automation / scheduled jobs layer.

Requirements:
- use cron-based or lightweight queue-based scheduled tasks
- avoid heavy distributed-job complexity
- jobs must be trackable in admin UI
- failures must be logged

Jobs should include:
- metadata refresh for older links
- thumbnail refresh attempts
- stale content scan
- broken link checks
- duplicate content scan
- AI retagging pass
- tag cleanup suggestions
- category health analysis
- recommendation recompute
- analytics aggregation
- admin alerts generation

Create:
- scheduled_jobs table or equivalent
- job logs/history
- admin page showing:
    - last run
    - status
    - failures
    - next scheduled run

--------------------------------------------------
RECOMMENDATION ENGINES
--------------------------------------------------

Add recommendation engines for both users and admins.

USER-FACING RECOMMENDATIONS
Examples:
- related posts
- related CMS pages
- recommended reading
- trending in your categories
- because you bookmarked / liked similar content
- older valuable posts resurfacing

ADMIN-FACING RECOMMENDATIONS
Examples:
- categories to merge
- duplicate tags to consolidate
- posts needing review
- stale content needing update
- users deserving badges
- posts likely duplicates
- underused CMS content worth surfacing
- moderation backlog priorities

Each recommendation must be structured and explainable.

Fields:
- id
- type
- title
- summary
- priorityScore
- confidenceScore
- whyThisExists
- supportingEntityIds
- conflictingEntityIds if applicable
- generatedAt
- status (new, reviewed, dismissed, actioned)

Do not make recommendations feel magical.
They must explain why they exist.

--------------------------------------------------
CONTENT RELATIONSHIP ENGINE
--------------------------------------------------

Implement a relationship engine to connect content together.

Relate:
- post to post
- post to page
- page to page
- tag clusters
- category overlap
- similar metadata
- similar AI summaries
- similar extracted keywords

Possible outputs:
- “related posts”
- “similar articles”
- “duplicate candidates”
- “recommended internal links”
- “this page may belong to another category”

Store these relationships in a table or derived cache.

--------------------------------------------------
ANALYTICS SYSTEM
--------------------------------------------------

Add a lightweight analytics system.

Track:
- most viewed posts
- most liked/disliked posts
- most commented posts
- most bookmarked posts
- category activity
- tag usage trends
- content submission trends
- active users
- moderation activity
- report trends

Admin dashboard should include:
- trending content
- activity charts/tables
- moderation heat indicators
- stale content indicators
- category health indicators

Keep analytics efficient:
- use aggregation tables or periodic recomputation
- avoid expensive live queries everywhere

--------------------------------------------------
ADVANCED SELF-AWARE ADMIN INTELLIGENCE
--------------------------------------------------

Extend system self-awareness.

The admin dashboard should now proactively surface:
- stale links
- dead outbound links
- low-quality posts
- duplicate posts
- duplicate CMS pages
- sparse or messy categories
- tags that should merge
- orphaned content
- content with missing metadata
- users with strong contribution patterns
- users who may deserve badges
- unusual spikes in reports or comments
- content clusters that are emerging

Examples:
- “5 links are now broken”
- “Tag ‘php’ and ‘PHP’ should be merged”
- “This guide has high engagement but no featured status”
- “User X may qualify for Top Curator badge”
- “3 pages appear highly similar and may be duplicates”

--------------------------------------------------
BADGE AUTOMATION
--------------------------------------------------

Extend the badges system.

Allow badge recommendation and optional automatic assignment for things like:
- Top Curator
- Helpful Commenter
- Popular Poster
- Active Moderator
- Early Contributor
- Trending Author

Store:
- badge rule basis
- recommendation reason
- assignment source (manual or automatic)

Admins must be able to approve/reject badge recommendations.

--------------------------------------------------
MODERATION AUTOMATION
--------------------------------------------------

Improve moderation tools:
- AI-assisted triage
- queue prioritization
- grouped similar reports
- suspicious activity highlighting
- comment clusters needing review
- offensive/spam risk scoring
- content risk scoring

Moderators/admins should see:
- highest priority review items first
- why they are high priority
- AI confidence
- related evidence

Do not silently delete content.
Always log recommendations and actions.

--------------------------------------------------
CMS + CONTENT LIFECYCLE
--------------------------------------------------

Improve CMS lifecycle management.

Add signals such as:
- stale content
- unreviewed content
- low-engagement content
- high-value evergreen content
- candidates for featured placement
- pages needing metadata cleanup

Allow admin actions:
- feature content
- archive content
- request update
- merge duplicate pages
- mark evergreen

--------------------------------------------------
DATABASE ADDITIONS
--------------------------------------------------

Add tables as needed for:
- recommendation_items
- content_relationships
- scheduled_jobs
- scheduled_job_runs
- analytics_snapshots
- badge_recommendations
- content_health_flags

Keep schema efficient and normalized.

--------------------------------------------------
UI REQUIREMENTS
--------------------------------------------------

Maintain the existing premium dark cinematic UI.

Add new admin interfaces for:
- recommendations dashboard
- job scheduler / job runs
- analytics dashboard
- content relationships / duplicate review
- badge recommendations
- content health panel

Add user-facing UI for:
- related content
- recommended content
- popular / trending sections

Visual style:
- control room / intelligence console
- evidence panels
- soft glows
- metadata chips
- charts/tables/cards
- compact information density

--------------------------------------------------
SEARCH + DISCOVERY IMPROVEMENTS
--------------------------------------------------

Extend search/discovery with:
- related results
- tag clusters
- category similarity
- “more like this”
- recommended internal links

Keep it lightweight and MySQL-friendly.

--------------------------------------------------
PERFORMANCE REQUIREMENTS
--------------------------------------------------

Because this runs on a limited home Linux server:
- prefer scheduled aggregation over expensive live analytics
- cache intelligently where helpful
- keep AI calls optional and controllable
- allow admin to disable expensive modules
- avoid unnecessary polling
- do not introduce heavy background infrastructure unless justified

--------------------------------------------------
ADMIN SETTINGS EXPANSION
--------------------------------------------------

Add settings for:
- enable/disable recommendations
- enable/disable AI moderation
- enable/disable badge automation
- enable/disable broken-link scans
- job schedule intervals
- analytics retention windows
- duplicate detection thresholds

--------------------------------------------------
OUTPUT REQUIREMENTS
--------------------------------------------------

Create:
- automation layer
- scheduled job system
- recommendation engine
- related-content engine
- analytics module
- badge automation/recommendations
- expanded admin intelligence dashboard
- migrations/schema updates
- UI for all new features

Explain:
- how scheduled jobs work
- how recommendations are generated
- how analytics are computed
- how to enable/disable expensive features
- how to keep performance suitable for a small home server

--------------------------------------------------
IMPORTANT RULES
--------------------------------------------------

- do not break existing phases
- keep modules isolated
- avoid heavy bloat
- build real working code
- do not make AI decisions opaque
- keep admin control over automation
- preserve premium dark cinematic UI
- preserve traceability and moderation logs