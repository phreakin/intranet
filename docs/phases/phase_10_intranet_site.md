Extend the existing “Home Intranet” system.

This is Phase 5.

The system already includes:
- Core intranet system
- Authentication + OAuth
- User profiles and badges
- Admin dashboard
- CMS system
- RBAC system
- AI moderation + admin intelligence
- Automation + analytics
- Session tracking + IP tracking
- Ban system (username + IP)

Now build a dedicated, advanced Moderation Engine as a standalone module.

This module must function as a centralized control system for:
- content moderation
- user moderation
- behavioral analysis
- enforcement actions
- forensic investigation

Maintain all constraints:
- single home Linux server
- limited resources
- modular architecture
- premium dark cinematic UI
- no unnecessary bloat

--------------------------------------------------
PRIMARY GOAL
--------------------------------------------------

Create a full Moderation Engine that acts like:

- a control center
- a forensic investigation tool
- a behavioral intelligence system
- a decision-support system for admins/mods

--------------------------------------------------
MODERATION ENGINE MODULE
--------------------------------------------------

Create a dedicated module:

/modules/ModerationEngine

Responsibilities:
- unify all moderation workflows
- centralize all moderation data
- provide investigation tools
- provide decision-making tools
- integrate AI signals
- integrate session/IP intelligence

--------------------------------------------------
MODERATION DASHBOARD (CORE UI)
--------------------------------------------------

Create a central moderation dashboard.

This is NOT a simple list.

It should feel like:
- a command center
- a live intelligence feed
- an evidence board

Show:

1. Priority Queue
- high-risk posts
- high-risk comments
- flagged users
- AI-flagged content
- heavily reported items

2. Moderation Feed
- live stream of:
    - reports
    - flags
    - suspicious activity
    - bans
    - AI signals

3. Risk Scoring
   Each item should have:
- riskScore (0–100)
- confidenceScore
- reason summary

4. Filters
- by type (post/comment/user)
- by risk level
- by report count
- by AI flag
- by IP cluster

--------------------------------------------------
RISK SCORING SYSTEM
--------------------------------------------------

Implement a unified risk scoring model.

Each entity (post, comment, user) should have:

- riskScore
- contributing factors
- last updated timestamp

Factors may include:
- report count
- report velocity
- AI flags
- duplicate detection
- suspicious IP usage
- multiple accounts from same IP
- rapid posting behavior
- spam patterns
- content similarity

Store:
- risk_score
- risk_factors (JSON)

--------------------------------------------------
CASE SYSTEM (CRITICAL)
--------------------------------------------------

Introduce a “Case” system for moderation.

Admins should be able to:
- open a case
- attach:
    - posts
    - comments
    - users
    - IPs
- add notes
- track actions
- assign status:
    - open
    - investigating
    - resolved
    - dismissed

Each case should store:
- evidence list
- timeline of actions
- admin notes
- AI insights

This becomes your forensic investigation layer.

--------------------------------------------------
USER INVESTIGATION VIEW
--------------------------------------------------

Create a deep user investigation panel.

For any user, admins should see:

- profile info
- badges
- roles
- post history
- comment history
- report history
- bans
- IP history
- session history
- related usernames (shared IPs)
- risk score
- AI flags

Also show:
- “related accounts” cluster
- timeline of activity
- suspicious behavior indicators

--------------------------------------------------
IP / SESSION FORENSICS
--------------------------------------------------

Create an IP investigation view.

For any IP address:

Show:
- associated usernames
- number of posts/comments
- activity timeline
- ban status
- suspicious behavior indicators

Also show:
- clusters of users sharing the IP
- repeated patterns
- high-risk activity flags

--------------------------------------------------
CONTENT INVESTIGATION VIEW
--------------------------------------------------

For any post or comment:

Show:
- content
- author
- IP address
- user agent
- edit history
- report history
- AI analysis
- related content
- similar posts
- risk score
- moderation history

--------------------------------------------------
MODERATION ACTIONS SYSTEM
--------------------------------------------------

All actions must be structured and logged.

Actions include:
- delete content
- hide content
- flag content
- tag content
- warn user
- suspend user
- ban user
- ban IP
- remove ban

Each action must store:
- action type
- performed by admin
- target entity
- reason
- timestamp
- optional notes

--------------------------------------------------
ACTION LOGGING (CRITICAL)
--------------------------------------------------

Create a unified moderation log.

Everything must be logged:

- reports
- AI flags
- admin actions
- bans
- case actions
- overrides

Admins should be able to:
- filter logs
- search logs
- audit actions

--------------------------------------------------
AI MODERATION INTEGRATION
--------------------------------------------------

Enhance AI role:

AI should:
- assign risk signals
- suggest actions
- cluster similar content
- detect suspicious behavior patterns
- assist in case summaries

AI outputs must include:
- reasoning
- confidence
- suggested action

AI must NOT:
- silently ban
- silently delete

Everything must be visible and overrideable.

--------------------------------------------------
MODERATION QUEUE SYSTEM
--------------------------------------------------

Create structured queues:

- high priority
- medium priority
- low priority
- AI flagged
- user reported
- system detected

Queue ordering based on:
- risk score
- recency
- activity level

--------------------------------------------------
BULK ACTIONS
--------------------------------------------------

Allow admins to:
- select multiple items
- apply actions in bulk:
    - delete
    - tag
    - hide
    - assign to case
    - mark reviewed

--------------------------------------------------
MODERATION TAGGING SYSTEM
--------------------------------------------------

Extend tagging for moderation.

Tags such as:
- spam
- abusive
- off-topic
- duplicate
- low quality
- suspicious

Tags should apply to:
- posts
- comments
- users
- cases

--------------------------------------------------
ADMIN UX REQUIREMENTS
--------------------------------------------------

UI must feel like:

- intelligence console
- forensic workstation
- control system

Design:
- dark glass panels
- glowing highlights
- risk indicators (color-coded)
- expandable evidence panels
- timeline views
- side-by-side comparisons
- fast navigation between entities

--------------------------------------------------
PERFORMANCE REQUIREMENTS
--------------------------------------------------

Because this runs on a limited server:

- compute risk scores incrementally
- cache frequently accessed investigation data
- avoid heavy joins in real-time views
- paginate large logs
- index:
    - IP addresses
    - user IDs
    - risk scores
    - timestamps

--------------------------------------------------
DATABASE ADDITIONS
--------------------------------------------------

Add tables:

- moderation_cases
- case_entities
- moderation_actions
- risk_scores
- risk_factors
- moderation_queue
- moderation_tags
- entity_moderation_tags

--------------------------------------------------
OUTPUT REQUIREMENTS
--------------------------------------------------

Create:

- ModerationEngine module
- moderation dashboard UI
- case system
- risk scoring system
- investigation panels (user, IP, content)
- moderation queue
- action system
- logging system
- AI moderation integration updates

Explain:

- how risk scoring works
- how cases work
- how moderation flows operate
- how to extend moderation rules
- how to keep system performant

--------------------------------------------------
IMPORTANT RULES
--------------------------------------------------

- do not break prior modules
- keep everything modular
- no silent destructive actions
- everything must be traceable
- optimize for home server performance
- maintain premium cinematic UI