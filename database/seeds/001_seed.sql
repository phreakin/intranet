INSERT INTO roles (name) VALUES ('Admin'), ('Moderator'), ('Member');

INSERT INTO permissions (name) VALUES
('create_post'), ('edit_post'), ('delete_post'), ('moderate_post'),
('create_comment'), ('delete_comment'), ('moderate_comment'),
('create_page'), ('edit_page'), ('publish_page'), ('delete_page'),
('access_admin'), ('manage_users'), ('manage_roles'), ('manage_settings'),
('approve_ai_actions'), ('override_ai'),
('view_logs'), ('manage_modules');

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r JOIN permissions p
WHERE (r.name = 'Admin')
   OR (r.name = 'Moderator' AND p.name IN ('moderate_post','create_comment','delete_comment','moderate_comment','create_page','edit_page','publish_page','access_admin','approve_ai_actions','override_ai','view_logs'))
   OR (r.name = 'Member' AND p.name IN ('create_post','edit_post','create_comment','create_page','edit_page'));

INSERT INTO badges (name, description, created_at) VALUES
('Contributor', 'Submitted useful links', NOW()),
('Moderator', 'Moderation team member', NOW()),
('Admin', 'System administrator', NOW()),
('Top Curator', 'High quality post curation', NOW()),
('Early Member', 'Founding member of intranet', NOW()),
('Popular Poster', 'Posted highly engaged content', NOW());

INSERT INTO comment_tags (name) VALUES
('Informative'), ('Funny'), ('Off-Topic'), ('Interesting'), ('Spam'), ('Needs Review');

INSERT INTO users (email, password_hash, display_name, bio, created_at, updated_at)
VALUES ('admin@local.intranet', '$2y$10$q7uZb9w06PTpA2XQaAsH3usV11S5nNNLzv5uXGxROtAHA2hIiYQm6', 'Admin Analyst', 'Default admin account', NOW(), NOW());

INSERT INTO user_roles (user_id, role_id)
SELECT 1, id FROM roles WHERE name = 'Admin';

INSERT INTO user_badges (user_id, badge_id, assigned_by, created_at)
SELECT 1, id, 1, NOW() FROM badges WHERE name IN ('Admin','Contributor','Early Member');

INSERT INTO settings (`key`,`value`,updated_at) VALUES
('feature.ai.enabled', '0', NOW()),
('feature.ai.auto_remove', '0', NOW()),
('system.health.status', 'ok', NOW());

INSERT INTO module_registry (module_name, enabled, version, config_json, updated_at) VALUES
('CoreCMS', 1, '1.0.0', JSON_OBJECT('routes', JSON_ARRAY('/pages/{slug}', '/articles/{slug}')), NOW()),
('RBACAdmin', 1, '1.0.0', JSON_OBJECT('admin_panels', JSON_ARRAY('roles', 'permissions')), NOW()),
('Search', 1, '1.0.0', JSON_OBJECT('features', JSON_ARRAY('fulltext', 'fallback_like')), NOW());

INSERT INTO categories (name, slug, created_at) VALUES
('Security', 'security', NOW()),
('Development', 'development', NOW()),
('Operations', 'operations', NOW());
