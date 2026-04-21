Extend the existing “Home Intranet” system.

This is phase 9 of the intranet system.

This system runs on a single home Linux server with limited resources.
Maintain all prior constraints:
- modular architecture
- premium dark cinematic UI
- efficient MySQL usage
- low CPU / low memory overhead
- server-side rendered PHP
- avoid unnecessary bloat

Use:
- PHP 8.2+
- MySQL 8+
- jQuery
- Bootstrap 5 with heavy custom styling
- Composer packages where needed
- optional external AI APIs for moderation/admin intelligence

--------------------------------------------------
NEW REQUIRED CAPABILITIES
--------------------------------------------------

Add full user activity tracking, moderation intelligence, and admin visibility features.

The system should keep track of:
- user sessions
- user activity while on the site
- content creation activity
- source IP addresses
- user agents
- login/session events
- actions taken by users across posts/comments

Admins must be able to:
- ban by username
- ban by IP address
- inspect the IP address used for posts/comments
- see whether multiple usernames appear to be associated with the same IP address
- see related account activity signals

This is a private intranet system, so strong admin visibility is allowed.
However, implement it cleanly, efficiently, and with proper data structure.

--------------------------------------------------
SESSION + USER ACTIVITY TRACKING
--------------------------------------------------

Implement a session/activity tracking system.

Track:
- login timestamp
- logout timestamp
- session start
- session expiration
- last activity
- IP address
- user agent
- optional referrer
- page visits
- actions performed

User actions to log can include:
- login
- logout
- create post
- edit post
- delete post
- create comment
- delete comment
- like/dislike
- bookmark/favorite
- report content
- moderation actions
- admin actions

Store this in structured tables, not just flat logs.

Suggested tables:
- user_sessions
- user_activity_log
- user_ip_history
- ban_list

Requirements:
- session records should link to users
- activity log should link to session if available
- IP history should be queryable
- keep performance reasonable by not overlogging unnecessary noise
- make tracking configuration-driven where practical

--------------------------------------------------
POST / COMMENT ORIGIN VISIBILITY
--------------------------------------------------

For every post and comment, store:
- created_by_user_id
- source IP address
- user agent
- created_at
- edited_at if applicable

Admin interface must allow:
- on any post: view posting IP address
- on any comment: view posting IP address
- see user agent used when created if available
- see whether the same IP has been used by other usernames
- see whether a given username has used multiple IPs

In the admin moderation view for a post/comment, show:
- author username
- posting IP
- user agent
- linked usernames seen on the same IP
- recent activity from that user

--------------------------------------------------
BAN SYSTEM
--------------------------------------------------

Implement moderation controls to ban by:

1. Username
2. IP address

Ban capabilities:
- temporary or permanent ban
- ban reason
- created_by_admin
- created_at
- expires_at nullable
- active flag

Behavior:
- banned username cannot authenticate or interact based on ban scope
- banned IP cannot authenticate or interact based on ban scope
- allow admin to see whether a ban is username-based, IP-based, or both
- log all ban/unban actions

Suggested tables:
- user_bans
- ip_bans
- moderation_logs

Admin tools:
- ban from user profile
- ban from post moderation view
- ban from comment moderation view
- view active bans
- revoke ban
- search bans

--------------------------------------------------
CROSS-ACCOUNT / RELATED-USER DETECTION
--------------------------------------------------

Implement admin intelligence features to detect related usernames.

Examples:
- usernames sharing the same IP
- usernames sharing similar session patterns
- repeated actions from the same IP
- suspicious vote/comment/report patterns

Do NOT overclaim certainty.
This should be an admin-assist tool.

Examples of admin signals:
- “This IP has been used by 3 usernames”
- “User X shares posting IP with User Y”
- “This user has posted from 4 distinct IPs”
- “These two accounts have overlapping session fingerprints”

Create an admin-facing related-account panel for:
- user profile
- post moderation view
- comment moderation view

--------------------------------------------------
ADMIN DASHBOARD EXPANSION
--------------------------------------------------

Extend the admin dashboard to include:

1. Session intelligence
- active sessions
- recent logins
- failed logins if tracked
- users currently active
- recent activity feed

2. User risk / moderation intelligence
- users with multiple IPs
- IPs shared by multiple usernames
- users with high report volume
- suspicious account clusters
- recently banned usernames/IPs

3. Content origin intelligence
- recent posts by IP
- recent comments by IP
- repeated activity from same IP/user agent
- flagged origin patterns

4. Moderation controls
- quick ban by username
- quick ban by IP
- quick access to related usernames
- quick view of user activity history

UI should match the premium dark-glass cinematic admin control-room style.

--------------------------------------------------
DATABASE ADDITIONS
--------------------------------------------------

Add/update schema for at minimum:
- user_sessions
- user_activity_log
- user_ip_history
- user_bans
- ip_bans

Recommended fields:

user_sessions:
- id
- user_id
- session_token or session_identifier
- ip_address
- user_agent
- started_at
- last_activity_at
- ended_at
- is_active

user_activity_log:
- id
- user_id
- session_id nullable
- action_type
- entity_type
- entity_id nullable
- ip_address
- user_agent
- metadata_json
- created_at

user_ip_history:
- id
- user_id
- ip_address
- first_seen_at
- last_seen_at
- occurrence_count

user_bans:
- id
- user_id
- reason
- created_by_admin_id
- created_at
- expires_at nullable
- is_active

ip_bans:
- id
- ip_address
- reason
- created_by_admin_id
- created_at
- expires_at nullable
- is_active

--------------------------------------------------
AUTH / SECURITY INTEGRATION
--------------------------------------------------

Integrate bans with the existing auth system.

Requirements:
- blocked users cannot log in if username-ban active
- blocked IPs cannot log in if IP-ban active
- optionally block post/comment actions even if session exists
- admin actions require proper RBAC/permission checks

Permission examples:
- view_session_data
- view_ip_data
- ban_user
- ban_ip
- unban_user
- unban_ip
- view_related_accounts

--------------------------------------------------
AI + MODERATION INTELLIGENCE EXPANSION
--------------------------------------------------

Extend AI/admin intelligence to use session/origin data where appropriate.

Examples:
- identify suspicious clusters
- identify repeated spam/report/vote behavior
- suggest review when multiple usernames use same IP
- prioritize moderation queues by suspicious activity signals

Important:
- AI should recommend or flag, not silently punish
- all AI-generated moderation signals must be logged
- admin can override

--------------------------------------------------
USER PROFILE / ADMIN PROFILE VIEWS
--------------------------------------------------

Expand user profile/admin views to include:

For admins:
- usernames used
- IP history
- recent sessions
- recent activity
- related usernames
- current bans
- moderation notes
- badge history
- content history

For regular users:
- keep profile cleaner and do not expose sensitive internal moderation data

--------------------------------------------------
UI REQUIREMENTS
--------------------------------------------------

Maintain premium design language:
- dark glass
- blurred panels
- soft glows
- compact data views
- chip/badge metadata
- admin intelligence dashboard feel

Add UI screens/panels for:
- active sessions
- user activity log
- ban management
- IP history
- related usernames/accounts
- post origin inspector
- comment origin inspector

--------------------------------------------------
PERFORMANCE REQUIREMENTS
--------------------------------------------------

Because this is on a limited home Linux server:
- do not log excessive noise that provides no value
- index IP address fields appropriately
- index user/session lookup fields appropriately
- make activity retention configurable
- allow pruning/archiving of old logs
- keep admin queries optimized

--------------------------------------------------
OUTPUT REQUIREMENTS
--------------------------------------------------

Create:
- schema/migrations for session and ban systems
- activity logging system
- session tracking integration
- ban system
- admin UI for bans and origin tracking
- related-user detection views
- admin dashboard expansion
- moderation tools using IP/username data

Explain:
- how session tracking works
- how IP tracking works
- how related-user detection works
- how bans are enforced
- how to keep tracking efficient on a small home Linux server

--------------------------------------------------
IMPORTANT RULES
--------------------------------------------------

- do not break prior phases
- keep modules isolated
- keep it efficient
- build real working code
- maintain admin visibility
- maintain traceability
- preserve premium cinematic UI
- do not silently auto-ban without admin control