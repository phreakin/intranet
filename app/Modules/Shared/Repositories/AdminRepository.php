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
}
