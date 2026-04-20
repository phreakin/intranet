<?php

declare(strict_types=1);

namespace Intranet\Modules\Shared\Repositories;

use Intranet\Core\Database;

final class AdminRepository
{
    public function dashboard(): array
    {
        $db = Database::connection();

        return [
            'reported_posts' => (int) $db->query('SELECT COUNT(*) FROM post_reports WHERE status = "open"')->fetchColumn(),
            'reported_comments' => (int) $db->query('SELECT COUNT(*) FROM comment_reports WHERE status = "open"')->fetchColumn(),
            'ai_queue' => (int) $db->query('SELECT COUNT(*) FROM ai_moderation_logs WHERE review_status = "pending"')->fetchColumn(),
            'missing_thumbnails' => (int) $db->query('SELECT COUNT(*) FROM posts WHERE thumbnail_url IS NULL OR thumbnail_url = ""')->fetchColumn(),
            'missing_descriptions' => (int) $db->query('SELECT COUNT(*) FROM posts WHERE description IS NULL OR description = ""')->fetchColumn(),
            'duplicates' => (int) $db->query('SELECT COUNT(*) FROM (SELECT canonical_url FROM posts GROUP BY canonical_url HAVING COUNT(*) > 1) d')->fetchColumn(),
            'report_spike_today' => (int) $db->query('SELECT COUNT(*) FROM post_reports WHERE DATE(created_at) = CURRENT_DATE()')->fetchColumn(),
            'stale_categories' => $db->query('SELECT c.name, MAX(p.created_at) AS last_used
                                             FROM categories c LEFT JOIN posts p ON p.category_id = c.id
                                             GROUP BY c.id
                                             HAVING last_used IS NULL OR last_used < DATE_SUB(NOW(), INTERVAL 90 DAY)
                                             ORDER BY last_used ASC LIMIT 5')->fetchAll(),
            'tag_duplicates' => $db->query('SELECT LOWER(name) AS name_key, COUNT(*) AS copies FROM tags GROUP BY LOWER(name) HAVING copies > 1 LIMIT 10')->fetchAll(),
            'recent_posts' => $db->query('SELECT id, title, created_at FROM posts ORDER BY created_at DESC LIMIT 8')->fetchAll(),
            'hot_posts' => $db->query('SELECT id, title, like_count, comment_count, favorite_count, bookmark_count
                                       FROM posts ORDER BY (like_count + comment_count + favorite_count + bookmark_count) DESC LIMIT 8')->fetchAll(),
            'activity_overview' => $db->query('SELECT DATE(created_at) AS day, COUNT(*) AS posts FROM posts WHERE created_at > DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY day ORDER BY day')->fetchAll(),
            'ai_flags' => $db->query('SELECT id, target_type, target_id, risk_level, confidence, recommendation, suggested_tags, review_status, admin_decision, created_at
                                      FROM ai_moderation_logs
                                      ORDER BY created_at DESC LIMIT 20')->fetchAll(),
            'categories' => $db->query('SELECT id, name, slug, created_at FROM categories ORDER BY name ASC LIMIT 100')->fetchAll(),
            'tags' => $db->query('SELECT id, name, slug, created_at FROM tags ORDER BY name ASC LIMIT 150')->fetchAll(),
            'manageable_posts' => $db->query('SELECT p.id, p.title, u.display_name, p.created_at FROM posts p JOIN users u ON u.id = p.user_id ORDER BY p.created_at DESC LIMIT 30')->fetchAll(),
        ];
    }

    public function moderationQueue(): array
    {
        $db = Database::connection();
        return [
            'post_reports' => $db->query('SELECT pr.*, p.title FROM post_reports pr JOIN posts p ON p.id = pr.post_id WHERE pr.status = "open" ORDER BY pr.created_at DESC LIMIT 50')->fetchAll(),
            'comment_reports' => $db->query('SELECT cr.*, c.body FROM comment_reports cr JOIN comments c ON c.id = cr.comment_id WHERE cr.status = "open" ORDER BY cr.created_at DESC LIMIT 50')->fetchAll(),
            'comments' => $db->query('SELECT c.*, p.title AS post_title, u.display_name FROM comments c JOIN posts p ON p.id = c.post_id JOIN users u ON u.id = c.user_id ORDER BY c.created_at DESC LIMIT 50')->fetchAll(),
        ];
    }

    public function setCommentVisibility(int $commentId, bool $hidden, int $actorId): void
    {
        $state = $hidden ? 1 : 0;
        Database::connection()->prepare('UPDATE comments SET is_hidden = :hidden, moderation_state = :state, updated_at = NOW() WHERE id = :id')
            ->execute(['hidden' => $state, 'state' => $hidden ? 'hidden' : 'visible', 'id' => $commentId]);
        Database::connection()->prepare('INSERT INTO moderation_logs (actor_user_id, target_type, target_id, action, detail, created_at)
            VALUES (:actor_user_id, :target_type, :target_id, :action, :detail, NOW())')->execute([
            'actor_user_id' => $actorId,
            'target_type' => 'comment',
            'target_id' => $commentId,
            'action' => $hidden ? 'hide' : 'unhide',
            'detail' => 'Comment visibility toggled',
        ]);
    }

    public function tagComment(int $commentId, int $tagId, int $actorId): void
    {
        Database::connection()->prepare('INSERT IGNORE INTO comment_tag_map (comment_id, comment_tag_id) VALUES (:comment_id, :tag_id)')
            ->execute(['comment_id' => $commentId, 'tag_id' => $tagId]);
        Database::connection()->prepare('INSERT INTO moderation_logs (actor_user_id, target_type, target_id, action, detail, created_at)
            VALUES (:actor_user_id, :target_type, :target_id, :action, :detail, NOW())')->execute([
            'actor_user_id' => $actorId,
            'target_type' => 'comment',
            'target_id' => $commentId,
            'action' => 'tag',
            'detail' => 'Comment moderation tag assigned',
        ]);
    }

    public function commentTags(): array
    {
        return Database::connection()->query('SELECT * FROM comment_tags ORDER BY name ASC')->fetchAll();
    }

    public function findPost(int $id): ?array
    {
        $sql = 'SELECT p.*, GROUP_CONCAT(DISTINCT t.name) AS tags
                FROM posts p
                LEFT JOIN post_tags pt ON pt.post_id = p.id
                LEFT JOIN tags t ON t.id = pt.tag_id
                WHERE p.id = :id
                GROUP BY p.id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function updatePostEditable(int $postId, string $title, string $description, ?int $categoryId, array $tags, int $actorId): void
    {
        $db = Database::connection();
        $db->prepare('UPDATE posts SET title = :title, description = :description, category_id = :category_id, updated_at = NOW() WHERE id = :id')
            ->execute([
                'title' => $title,
                'description' => $description,
                'category_id' => $categoryId,
                'id' => $postId,
            ]);

        $db->prepare('DELETE FROM post_tags WHERE post_id = :post_id')->execute(['post_id' => $postId]);
        foreach ($tags as $tag) {
            $name = trim($tag);
            if ($name === '') {
                continue;
            }
            $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
            $db->prepare('INSERT INTO tags (name, slug, created_at) VALUES (:name, :slug, NOW()) ON DUPLICATE KEY UPDATE name = VALUES(name)')
                ->execute(['name' => $name, 'slug' => $slug]);
            $tagIdStmt = $db->prepare('SELECT id FROM tags WHERE slug = :slug LIMIT 1');
            $tagIdStmt->execute(['slug' => $slug]);
            $tagId = (int) $tagIdStmt->fetchColumn();
            $db->prepare('INSERT INTO post_tags (post_id, tag_id) VALUES (:post_id, :tag_id)')
                ->execute(['post_id' => $postId, 'tag_id' => $tagId]);
        }

        $db->prepare('INSERT INTO moderation_logs (actor_user_id, target_type, target_id, action, detail, created_at)
                      VALUES (:actor_user_id, :target_type, :target_id, :action, :detail, NOW())')
            ->execute([
                'actor_user_id' => $actorId,
                'target_type' => 'post',
                'target_id' => $postId,
                'action' => 'edit',
                'detail' => 'Admin updated post metadata',
            ]);
    }

    public function deletePost(int $postId, int $actorId): void
    {
        $db = Database::connection();
        $db->prepare('INSERT INTO moderation_logs (actor_user_id, target_type, target_id, action, detail, created_at)
                      VALUES (:actor_user_id, :target_type, :target_id, :action, :detail, NOW())')
            ->execute([
                'actor_user_id' => $actorId,
                'target_type' => 'post',
                'target_id' => $postId,
                'action' => 'delete',
                'detail' => 'Admin deleted post',
            ]);
        $db->prepare('DELETE FROM posts WHERE id = :id')->execute(['id' => $postId]);
    }

    public function createCategory(string $name): void
    {
        $trimmed = trim($name);
        if ($trimmed === '') {
            return;
        }
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $trimmed), '-'));
        Database::connection()->prepare('INSERT INTO categories (name, slug, created_at) VALUES (:name, :slug, NOW())
            ON DUPLICATE KEY UPDATE name = VALUES(name)')
            ->execute(['name' => $trimmed, 'slug' => $slug]);
    }

    public function createTag(string $name): void
    {
        $trimmed = trim($name);
        if ($trimmed === '') {
            return;
        }
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $trimmed), '-'));
        Database::connection()->prepare('INSERT INTO tags (name, slug, created_at) VALUES (:name, :slug, NOW())
            ON DUPLICATE KEY UPDATE name = VALUES(name)')
            ->execute(['name' => $trimmed, 'slug' => $slug]);
    }

    public function usersWithRolesBadges(): array
    {
        return Database::connection()->query('SELECT u.id, u.display_name, u.email, GROUP_CONCAT(DISTINCT r.name) AS roles, GROUP_CONCAT(DISTINCT b.name) AS badges
                             FROM users u
                             LEFT JOIN user_roles ur ON ur.user_id = u.id
                             LEFT JOIN roles r ON r.id = ur.role_id
                             LEFT JOIN user_badges ub ON ub.user_id = u.id
                             LEFT JOIN badges b ON b.id = ub.badge_id
                             GROUP BY u.id ORDER BY u.created_at DESC LIMIT 100')->fetchAll();
    }

    public function allBadges(): array
    {
        return Database::connection()->query('SELECT id, name FROM badges ORDER BY name ASC')->fetchAll();
    }

    public function allRoles(): array
    {
        return Database::connection()->query('SELECT id, name FROM roles ORDER BY name ASC')->fetchAll();
    }

    public function assignBadge(int $userId, int $badgeId, int $actorId): void
    {
        Database::connection()->prepare('INSERT IGNORE INTO user_badges (user_id, badge_id, assigned_by, created_at) VALUES (:user_id, :badge_id, :assigned_by, NOW())')
            ->execute(['user_id' => $userId, 'badge_id' => $badgeId, 'assigned_by' => $actorId]);
    }

    public function assignRole(int $userId, int $roleId): void
    {
        Database::connection()->prepare('INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)')
            ->execute(['user_id' => $userId, 'role_id' => $roleId]);
    }

    public function reviewAiLog(int $id, string $decision, int $actorId): void
    {
        Database::connection()->prepare('UPDATE ai_moderation_logs
            SET review_status = :status, admin_decision = :decision, admin_reviewed_by = :reviewed_by, reviewed_at = NOW()
            WHERE id = :id')->execute([
            'status' => in_array($decision, ['accepted', 'overridden'], true) ? 'reviewed' : 'rejected',
            'decision' => $decision,
            'reviewed_by' => $actorId,
            'id' => $id,
        ]);
    }
}
