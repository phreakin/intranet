INSERT IGNORE INTO roles (name, description, created_at, updated_at) VALUES
('Admin', 'Full system control across all modules.', NOW(), NOW()),
('Moderator', 'Moderation and report handling access.', NOW(), NOW()),
('Analyst', 'Investigation, audit, and intelligence review access.', NOW(), NOW()),
('Curator', 'Content classification and editorial curation access.', NOW(), NOW()),
('Trusted User', 'Elevated member with broader submission rights.', NOW(), NOW()),
('User', 'Standard authenticated user.', NOW(), NOW()),
('Observer', 'Read-only staff or viewer role.', NOW(), NOW()),
('System', 'Automation and internal system actor.', NOW(), NOW()),
('Banned', 'Restricted account with no platform actions.', NOW(), NOW());

INSERT IGNORE INTO permissions (name, description, created_at, updated_at) VALUES
('posts.create', 'Create new posts.', NOW(), NOW()),
('posts.edit.own', 'Edit own posts.', NOW(), NOW()),
('posts.edit.any', 'Edit any post.', NOW(), NOW()),
('posts.delete', 'Delete posts.', NOW(), NOW()),
('posts.report', 'Report posts for review.', NOW(), NOW()),
('posts.feature', 'Feature or prioritize posts.', NOW(), NOW()),
('posts.pin', 'Pin posts in key views.', NOW(), NOW()),
('posts.categorize', 'Assign categories to posts.', NOW(), NOW()),
('posts.tag.manage', 'Manage post tags and metadata.', NOW(), NOW()),
('comments.create', 'Create comments.', NOW(), NOW()),
('comments.moderate', 'Moderate comments.', NOW(), NOW()),
('comments.delete', 'Delete comments.', NOW(), NOW()),
('reports.create', 'Submit reports.', NOW(), NOW()),
('reports.review', 'Review open reports.', NOW(), NOW()),
('reports.resolve', 'Resolve report cases.', NOW(), NOW()),
('moderation.queue.access', 'Access moderation queue.', NOW(), NOW()),
('moderation.ai.review', 'Review AI moderation decisions.', NOW(), NOW()),
('moderation.ai.override', 'Override AI moderation decisions.', NOW(), NOW()),
('users.view', 'View user profiles and state.', NOW(), NOW()),
('users.manage', 'Manage users.', NOW(), NOW()),
('users.suspend', 'Suspend or deactivate users.', NOW(), NOW()),
('roles.assign', 'Assign roles to users.', NOW(), NOW()),
('badges.manage', 'Assign and manage badges.', NOW(), NOW()),
('taxonomy.manage', 'Manage categories and tags.', NOW(), NOW()),
('settings.manage', 'Manage system settings.', NOW(), NOW()),
('audit.view', 'View audit and moderation logs.', NOW(), NOW()),
('cms.manage', 'Manage CMS pages.', NOW(), NOW()),
('bookmarklet.use', 'Use submission bookmarklet.', NOW(), NOW()),
('admin.access', 'Access admin surfaces.', NOW(), NOW());

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p
WHERE r.name = 'Admin'
   OR (r.name = 'Moderator' AND p.name IN (
        'posts.edit.any', 'posts.report', 'comments.moderate', 'comments.delete',
        'reports.review', 'reports.resolve', 'moderation.queue.access',
        'moderation.ai.review', 'moderation.ai.override', 'users.view',
        'taxonomy.manage', 'audit.view', 'admin.access', 'bookmarklet.use'
   ))
   OR (r.name = 'Analyst' AND p.name IN (
        'posts.report', 'reports.create', 'reports.review', 'moderation.queue.access',
        'moderation.ai.review', 'users.view', 'audit.view', 'admin.access'
   ))
   OR (r.name = 'Curator' AND p.name IN (
        'posts.create', 'posts.edit.own', 'posts.feature', 'posts.pin',
        'posts.categorize', 'posts.tag.manage', 'comments.create',
        'reports.create', 'taxonomy.manage', 'bookmarklet.use'
   ))
   OR (r.name = 'Trusted User' AND p.name IN (
        'posts.create', 'posts.edit.own', 'posts.report', 'comments.create',
        'reports.create', 'bookmarklet.use'
   ))
   OR (r.name = 'User' AND p.name IN (
        'posts.create', 'posts.edit.own', 'posts.report', 'comments.create',
        'reports.create', 'bookmarklet.use'
   ))
   OR (r.name = 'Observer' AND p.name IN (
        'users.view', 'audit.view'
   ))
   OR (r.name = 'System' AND p.name IN (
        'moderation.ai.review', 'audit.view'
   ));

INSERT IGNORE INTO badges (name, description, created_at) VALUES
('Admin', 'System administrator.', NOW()),
('Moderator', 'Trusted moderation team member.', NOW()),
('Analyst', 'Investigation and intelligence review operator.', NOW()),
('Curator', 'Curates and classifies valuable content.', NOW()),
('Trusted User', 'Member with elevated trust and permissions.', NOW()),
('Contributor', 'Submitted useful links and comments.', NOW()),
('Helpful', 'Consistently leaves useful comment context.', NOW()),
('Top Curator', 'High quality post curation.', NOW()),
('Popular Poster', 'Posted highly engaged content.', NOW()),
('Early Member', 'Founding member of the intranet.', NOW()),
('Incident Watch', 'Spots high-risk or suspicious content early.', NOW()),
('Signal Booster', 'Highlights high-value intelligence quickly.', NOW()),
('Needs Review', 'Behavior or content needs closer monitoring.', NOW()),
('Troll', 'Pattern of disruptive behavior.', NOW()),
('Spammer', 'Pattern of low-value or promotional spam.', NOW());

INSERT IGNORE INTO comment_tags (name) VALUES
('Informative'),
('Funny'),
('Interesting'),
('Helpful'),
('Insightful'),
('Constructive'),
('Answered'),
('Question'),
('Clarification Needed'),
('Source Added'),
('Context Added'),
('Investigation Lead'),
('Off-Topic'),
('Spam'),
('Needs Review'),
('Offensive'),
('Harassment'),
('Abusive'),
('Toxic'),
('Misleading'),
('Duplicate'),
('Low Effort'),
('Promotion'),
('Suspicious'),
('Escalate'),
('Unverified'),
('NSFW');

INSERT IGNORE INTO users (email, password_hash, display_name, bio, created_at, updated_at) VALUES
('admin@local.intranet', '$2y$10$NerruySTQVd/2VhQePMLbuACOHyYAFigqALGUo9iwQ6RpfmuPBkfy', 'Admin', 'Primary system administrator.', NOW(), NOW()),
('mod@local.intranet', '$2y$10$NerruySTQVd/2VhQePMLbuACOHyYAFigqALGUo9iwQ6RpfmuPBkfy', 'Moderator', 'Handles reports and comment moderation.', NOW(), NOW()),
('analyst@local.intranet', '$2y$10$NerruySTQVd/2VhQePMLbuACOHyYAFigqALGUo9iwQ6RpfmuPBkfy', 'Analyst', 'Investigates suspicious content and activity patterns.', NOW(), NOW()),
('curator@local.intranet', '$2y$10$NerruySTQVd/2VhQePMLbuACOHyYAFigqALGUo9iwQ6RpfmuPBkfy', 'Curator', 'Maintains taxonomy quality and content labeling.', NOW(), NOW()),
('member@local.intranet', '$2y$10$NerruySTQVd/2VhQePMLbuACOHyYAFigqALGUo9iwQ6RpfmuPBkfy', 'Member', 'Standard member account for seed interactions.', NOW(), NOW()),
('observer@local.intranet', '$2y$10$NerruySTQVd/2VhQePMLbuACOHyYAFigqALGUo9iwQ6RpfmuPBkfy', 'Observer', 'Read-only operator account.', NOW(), NOW()),
('system@local.intranet', NULL, 'System', 'Automated system actor.', NOW(), NOW());

INSERT IGNORE INTO user_roles (user_id, role_id)
SELECT u.id, r.id
FROM users u
JOIN roles r
WHERE (u.email = 'admin@local.intranet' AND r.name = 'Admin')
   OR (u.email = 'mod@local.intranet' AND r.name = 'Moderator')
   OR (u.email = 'analyst@local.intranet' AND r.name = 'Analyst')
   OR (u.email = 'curator@local.intranet' AND r.name = 'Curator')
   OR (u.email = 'member@local.intranet' AND r.name = 'Trusted User')
   OR (u.email = 'observer@local.intranet' AND r.name = 'Observer')
   OR (u.email = 'system@local.intranet' AND r.name = 'System');

INSERT IGNORE INTO user_badges (user_id, badge_id, assigned_by, created_at)
SELECT u.id, b.id, admin.id, NOW()
FROM users u
JOIN badges b
JOIN users admin ON admin.email = 'admin@local.intranet'
WHERE (u.email = 'admin@local.intranet' AND b.name IN ('Admin', 'Early Member', 'Signal Booster'))
   OR (u.email = 'mod@local.intranet' AND b.name IN ('Moderator', 'Incident Watch'))
   OR (u.email = 'analyst@local.intranet' AND b.name IN ('Analyst', 'Incident Watch'))
   OR (u.email = 'curator@local.intranet' AND b.name IN ('Curator', 'Top Curator'))
   OR (u.email = 'member@local.intranet' AND b.name IN ('Trusted User', 'Contributor', 'Helpful'))
   OR (u.email = 'observer@local.intranet' AND b.name IN ('Early Member'));

INSERT INTO settings (`key`, `value`, updated_at) VALUES
('feature.ai.enabled', '0', NOW()),
('feature.ai.auto_remove', '0', NOW()),
('feature.bookmarklet.enabled', '1', NOW()),
('feature.reporting.enabled', '1', NOW()),
('feature.moderation.enabled', '1', NOW()),
('system.health.status', 'ok', NOW()),
('system.mode', 'monitoring', NOW()),
('ui.theme', 'dark-glass', NOW()),
('ui.sidebar.default', 'expanded', NOW())
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), updated_at = VALUES(updated_at);

INSERT IGNORE INTO categories (name, slug, created_at) VALUES
('Security', 'security', NOW()),
('Development', 'development', NOW()),
('Operations', 'operations', NOW()),
('OSINT', 'osint', NOW()),
('Moderation', 'moderation', NOW()),
('Infrastructure', 'infrastructure', NOW()),
('Incident Response', 'incident-response', NOW()),
('Research', 'research', NOW());

INSERT IGNORE INTO tags (name, slug, created_at) VALUES
('Threat Intel', 'threat-intel', NOW()),
('Breach', 'breach', NOW()),
('Forensics', 'forensics', NOW()),
('Linux', 'linux', NOW()),
('Docker', 'docker', NOW()),
('Networking', 'networking', NOW()),
('OSINT', 'osint', NOW()),
('Automation', 'automation', NOW()),
('Monitoring', 'monitoring', NOW()),
('AI Moderation', 'ai-moderation', NOW()),
('Risk', 'risk', NOW()),
('Investigation', 'investigation', NOW());

INSERT INTO posts (
    user_id, category_id, url, canonical_url, title, description, thumbnail_url,
    site_name, author_name, published_at, metadata_json,
    like_count, dislike_count, comment_count, favorite_count, bookmark_count, report_count,
    created_at, updated_at
)
SELECT
    u.id,
    c.id,
    'https://example.com/security/malware-ops',
    'https://example.com/security/malware-ops',
    'Malware infrastructure indicators linked to current phishing wave',
    'A compact evidence note covering infrastructure overlap, delivery patterns, and indicators worth monitoring.',
    'https://images.unsplash.com/photo-1510511459019-5dda7724fd87?auto=format&fit=crop&w=900&q=80',
    'Example Security',
    'Ops Desk',
    NOW() - INTERVAL 2 DAY,
    JSON_OBJECT('source', 'seed', 'priority', 'high'),
    12, 1, 2, 4, 6, 1,
    NOW() - INTERVAL 2 DAY,
    NOW() - INTERVAL 1 DAY
FROM users u
JOIN categories c ON c.slug = 'security'
WHERE u.email = 'analyst@local.intranet'
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE canonical_url = 'https://example.com/security/malware-ops'
  );

INSERT INTO posts (
    user_id, category_id, url, canonical_url, title, description, thumbnail_url,
    site_name, author_name, published_at, metadata_json,
    like_count, dislike_count, comment_count, favorite_count, bookmark_count, report_count,
    created_at, updated_at
)
SELECT
    u.id,
    c.id,
    'https://example.com/osint/geofence-case-study',
    'https://example.com/osint/geofence-case-study',
    'OSINT workflow for geofence anomaly review',
    'A practical review flow for correlating public signals, archived snapshots, and source trust levels.',
    'https://images.unsplash.com/photo-1526379095098-d400fd0bf935?auto=format&fit=crop&w=900&q=80',
    'Example Research',
    'Field Notes',
    NOW() - INTERVAL 1 DAY,
    JSON_OBJECT('source', 'seed', 'priority', 'medium'),
    8, 0, 1, 2, 3, 0,
    NOW() - INTERVAL 1 DAY,
    NOW() - INTERVAL 12 HOUR
FROM users u
JOIN categories c ON c.slug = 'osint'
WHERE u.email = 'curator@local.intranet'
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE canonical_url = 'https://example.com/osint/geofence-case-study'
  );

INSERT INTO posts (
    user_id, category_id, url, canonical_url, title, description, thumbnail_url,
    site_name, author_name, published_at, metadata_json,
    like_count, dislike_count, comment_count, favorite_count, bookmark_count, report_count,
    created_at, updated_at
)
SELECT
    u.id,
    c.id,
    'https://example.com/operations/thumbnail-gap-report',
    'https://example.com/operations/thumbnail-gap-report',
    'Operational note: thumbnail coverage is dropping in the intake queue',
    'Tracks visual metadata quality issues and proposes automatic backfill for missing previews.',
    'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=900&q=80',
    'Example Ops',
    'Queue Monitor',
    NOW() - INTERVAL 6 HOUR,
    JSON_OBJECT('source', 'seed', 'priority', 'medium'),
    5, 0, 2, 1, 2, 0,
    NOW() - INTERVAL 6 HOUR,
    NOW() - INTERVAL 3 HOUR
FROM users u
JOIN categories c ON c.slug = 'operations'
WHERE u.email = 'mod@local.intranet'
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE canonical_url = 'https://example.com/operations/thumbnail-gap-report'
  );

INSERT IGNORE INTO post_tags (post_id, tag_id)
SELECT p.id, t.id
FROM posts p
JOIN tags t
WHERE (p.canonical_url = 'https://example.com/security/malware-ops' AND t.slug IN ('threat-intel', 'forensics', 'risk'))
   OR (p.canonical_url = 'https://example.com/osint/geofence-case-study' AND t.slug IN ('osint', 'investigation', 'monitoring'))
   OR (p.canonical_url = 'https://example.com/operations/thumbnail-gap-report' AND t.slug IN ('automation', 'monitoring', 'ai-moderation'));

INSERT INTO post_status_tags (post_id, status_tag, created_at)
SELECT p.id, s.status_tag, NOW()
FROM posts p
JOIN (
    SELECT 'https://example.com/security/malware-ops' AS canonical_url, 'Hot' AS status_tag
    UNION ALL SELECT 'https://example.com/security/malware-ops', 'Trending'
    UNION ALL SELECT 'https://example.com/osint/geofence-case-study', 'Interesting'
    UNION ALL SELECT 'https://example.com/operations/thumbnail-gap-report', 'Needs Review'
) s ON s.canonical_url = p.canonical_url
WHERE NOT EXISTS (
    SELECT 1 FROM post_status_tags pst WHERE pst.post_id = p.id AND pst.status_tag = s.status_tag
);

INSERT INTO comments (post_id, user_id, body, moderation_state, is_hidden, created_at, updated_at)
SELECT p.id, u.id,
       'Infrastructure overlap matches two domains already in the watchlist. Worth escalating to the moderation queue.',
       'visible', 0, NOW() - INTERVAL 20 HOUR, NOW() - INTERVAL 20 HOUR
FROM posts p
JOIN users u ON u.email = 'mod@local.intranet'
WHERE p.canonical_url = 'https://example.com/security/malware-ops'
  AND NOT EXISTS (
      SELECT 1 FROM comments c WHERE c.post_id = p.id AND c.body = 'Infrastructure overlap matches two domains already in the watchlist. Worth escalating to the moderation queue.'
  );

INSERT INTO comments (post_id, user_id, body, moderation_state, is_hidden, created_at, updated_at)
SELECT p.id, u.id,
       'Adding source context: archived snapshots show the landing page rotated branding twice in 24 hours.',
       'visible', 0, NOW() - INTERVAL 10 HOUR, NOW() - INTERVAL 10 HOUR
FROM posts p
JOIN users u ON u.email = 'analyst@local.intranet'
WHERE p.canonical_url = 'https://example.com/osint/geofence-case-study'
  AND NOT EXISTS (
      SELECT 1 FROM comments c WHERE c.post_id = p.id AND c.body = 'Adding source context: archived snapshots show the landing page rotated branding twice in 24 hours.'
  );

INSERT IGNORE INTO comment_tag_map (comment_id, comment_tag_id)
SELECT c.id, ct.id
FROM comments c
JOIN comment_tags ct
WHERE (c.body = 'Infrastructure overlap matches two domains already in the watchlist. Worth escalating to the moderation queue.' AND ct.name IN ('Informative', 'Escalate', 'Investigation Lead'))
   OR (c.body = 'Adding source context: archived snapshots show the landing page rotated branding twice in 24 hours.' AND ct.name IN ('Source Added', 'Context Added', 'Helpful'));

INSERT IGNORE INTO post_votes (post_id, user_id, vote, created_at)
SELECT p.id, u.id, 1, NOW() - INTERVAL 8 HOUR
FROM posts p
JOIN users u ON u.email = 'member@local.intranet'
WHERE p.canonical_url = 'https://example.com/security/malware-ops';

INSERT IGNORE INTO post_favorites (post_id, user_id, created_at)
SELECT p.id, u.id, NOW() - INTERVAL 7 HOUR
FROM posts p
JOIN users u ON u.email = 'member@local.intranet'
WHERE p.canonical_url = 'https://example.com/osint/geofence-case-study';

INSERT IGNORE INTO post_bookmarks (post_id, user_id, created_at)
SELECT p.id, u.id, NOW() - INTERVAL 6 HOUR
FROM posts p
JOIN users u ON u.email = 'observer@local.intranet'
WHERE p.canonical_url IN (
    'https://example.com/security/malware-ops',
    'https://example.com/osint/geofence-case-study'
);

INSERT INTO post_reports (post_id, user_id, reason, status, created_at)
SELECT p.id, u.id, 'Potential duplicate indicators need validation.', 'open', NOW() - INTERVAL 5 HOUR
FROM posts p
JOIN users u ON u.email = 'member@local.intranet'
WHERE p.canonical_url = 'https://example.com/security/malware-ops'
  AND NOT EXISTS (
      SELECT 1 FROM post_reports pr WHERE pr.post_id = p.id AND pr.reason = 'Potential duplicate indicators need validation.'
  );

INSERT INTO comment_reports (comment_id, user_id, reason, status, created_at)
SELECT c.id, u.id, 'Needs moderator review for escalation language.', 'open', NOW() - INTERVAL 4 HOUR
FROM comments c
JOIN users u ON u.email = 'observer@local.intranet'
WHERE c.body = 'Infrastructure overlap matches two domains already in the watchlist. Worth escalating to the moderation queue.'
  AND NOT EXISTS (
      SELECT 1 FROM comment_reports cr WHERE cr.comment_id = c.id AND cr.reason = 'Needs moderator review for escalation language.'
  );

INSERT INTO moderation_logs (actor_user_id, target_type, target_id, action, detail, created_at)
SELECT u.id, 'post', p.id, 'status_tagged', 'Seeded Hot/Trending state for dashboard realism.', NOW() - INTERVAL 3 HOUR
FROM users u
JOIN posts p ON p.canonical_url = 'https://example.com/security/malware-ops'
WHERE u.email = 'system@local.intranet'
  AND NOT EXISTS (
      SELECT 1 FROM moderation_logs ml
      WHERE ml.actor_user_id = u.id AND ml.target_type = 'post' AND ml.target_id = p.id AND ml.action = 'status_tagged'
  );

INSERT INTO ai_moderation_logs (
    target_type, target_id, input_context, ai_provider, confidence, risk_level,
    recommendation, action_recommended, suggested_tags, raw_response,
    auto_action_taken, review_status, admin_decision, admin_reviewed_by, reviewed_at,
    created_at, updated_at
)
SELECT
    'comment',
    c.id,
    c.body,
    'seed-provider',
    0.8125,
    'medium',
    'Escalate for moderator review due to operational urgency language.',
    'review',
    'Escalate, Investigation Lead',
    JSON_OBJECT('source', 'seed'),
    0,
    'pending',
    'pending',
    NULL,
    NULL,
    NOW() - INTERVAL 2 HOUR,
    NOW() - INTERVAL 2 HOUR
FROM comments c
WHERE c.body = 'Infrastructure overlap matches two domains already in the watchlist. Worth escalating to the moderation queue.'
  AND NOT EXISTS (
      SELECT 1 FROM ai_moderation_logs aml WHERE aml.target_type = 'comment' AND aml.target_id = c.id
  );
