INSERT INTO roles (name) VALUES ('Admin'), ('Moderator'), ('Member');

INSERT INTO permissions (name) VALUES
('posts.create'), ('posts.edit'), ('posts.report'),
('comments.moderate'), ('reports.review'), ('admin.access'), ('users.manage'), ('badges.manage');

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r JOIN permissions p
WHERE (r.name = 'Admin')
   OR (r.name = 'Moderator' AND p.name IN ('comments.moderate','reports.review','admin.access'))
   OR (r.name = 'Member' AND p.name IN ('posts.create','posts.report'));

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

INSERT INTO categories (name, slug, created_at) VALUES
('Security', 'security', NOW()),
('Development', 'development', NOW()),
('Operations', 'operations', NOW());
