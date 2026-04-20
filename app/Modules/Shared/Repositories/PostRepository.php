<?php

declare(strict_types=1);

namespace Intranet\Modules\Shared\Repositories;

use Intranet\Core\Database;

final class PostRepository
{
    public function feed(int $limit = 50): array
    {
        $sql = 'SELECT p.*, c.name AS category_name, u.display_name,
                       GROUP_CONCAT(DISTINCT t.name) AS tags,
                       GROUP_CONCAT(DISTINCT pst.status_tag) AS status_tags
                FROM posts p
                JOIN users u ON u.id = p.user_id
                LEFT JOIN categories c ON c.id = p.category_id
                LEFT JOIN post_tags pt ON pt.post_id = p.id
                LEFT JOIN tags t ON t.id = pt.tag_id
                LEFT JOIN post_status_tags pst ON pst.post_id = p.id
                GROUP BY p.id
                ORDER BY p.created_at DESC
                LIMIT :limit';
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $sql = 'SELECT p.*, c.name AS category_name, u.display_name,
                       GROUP_CONCAT(DISTINCT t.name) AS tags,
                       GROUP_CONCAT(DISTINCT pst.status_tag) AS status_tags
                FROM posts p
                JOIN users u ON u.id = p.user_id
                LEFT JOIN categories c ON c.id = p.category_id
                LEFT JOIN post_tags pt ON pt.post_id = p.id
                LEFT JOIN tags t ON t.id = pt.tag_id
                LEFT JOIN post_status_tags pst ON pst.post_id = p.id
                WHERE p.id = :id
                GROUP BY p.id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function comments(int $postId): array
    {
        $sql = 'SELECT c.*, u.display_name, GROUP_CONCAT(DISTINCT ct.name) AS moderation_tags
                FROM comments c
                JOIN users u ON u.id = c.user_id
                LEFT JOIN comment_tag_map ctm ON ctm.comment_id = c.id
                LEFT JOIN comment_tags ct ON ct.id = ctm.comment_tag_id
                WHERE c.post_id = :post_id AND c.is_hidden = 0
                GROUP BY c.id
                ORDER BY c.created_at ASC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['post_id' => $postId]);
        return $stmt->fetchAll();
    }

    public function byCategory(string $slug): array
    {
        $sql = 'SELECT p.*, c.name AS category_name, GROUP_CONCAT(DISTINCT pst.status_tag) AS status_tags
                FROM posts p
                JOIN categories c ON c.id = p.category_id
                LEFT JOIN post_status_tags pst ON pst.post_id = p.id
                WHERE c.slug = :slug
                GROUP BY p.id
                ORDER BY p.created_at DESC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetchAll();
    }

    public function byTag(string $slug): array
    {
        $sql = 'SELECT p.*, GROUP_CONCAT(DISTINCT pst.status_tag) AS status_tags
                FROM posts p
                JOIN post_tags pt ON pt.post_id = p.id
                JOIN tags t ON t.id = pt.tag_id
                LEFT JOIN post_status_tags pst ON pst.post_id = p.id
                WHERE t.slug = :slug
                GROUP BY p.id
                ORDER BY p.created_at DESC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetchAll();
    }

    public function categories(): array
    {
        return Database::connection()->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll();
    }

    public function createCategoryIfMissing(string $name): int
    {
        $normalized = trim($name);
        $stmt = Database::connection()->prepare('SELECT id FROM categories WHERE name = :name LIMIT 1');
        $stmt->execute(['name' => $normalized]);
        $existing = $stmt->fetchColumn();
        if ($existing) {
            return (int) $existing;
        }

        Database::connection()->prepare('INSERT INTO categories (name, slug, created_at) VALUES (:name, :slug, NOW())')
            ->execute([
                'name' => $normalized,
                'slug' => strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $normalized), '-')),
            ]);

        return (int) Database::connection()->lastInsertId();
    }

    public function create(array $payload): int
    {
        $sql = 'INSERT INTO posts (
                    user_id, category_id, url, canonical_url, title, description, thumbnail_url, site_name,
                    author_name, published_at, metadata_json, like_count, dislike_count, comment_count,
                    favorite_count, bookmark_count, report_count, created_at, updated_at
                ) VALUES (
                    :user_id, :category_id, :url, :canonical_url, :title, :description, :thumbnail_url, :site_name,
                    :author_name, :published_at, :metadata_json, 0, 0, 0, 0, 0, 0, NOW(), NOW()
                )';

        Database::connection()->prepare($sql)->execute($payload);
        return (int) Database::connection()->lastInsertId();
    }

    public function syncTags(int $postId, array $tags): void
    {
        $db = Database::connection();
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
            $db->prepare('INSERT INTO post_tags (post_id, tag_id) VALUES (:post_id, :tag_id)')->execute(['post_id' => $postId, 'tag_id' => $tagId]);
        }
    }

    public function applyStatusTags(int $postId, array $statusTags): void
    {
        $db = Database::connection();
        $db->prepare('DELETE FROM post_status_tags WHERE post_id = :post_id')->execute(['post_id' => $postId]);
        foreach ($statusTags as $tag) {
            $db->prepare('INSERT INTO post_status_tags (post_id, status_tag, created_at) VALUES (:post_id, :status_tag, NOW())')
                ->execute(['post_id' => $postId, 'status_tag' => $tag]);
        }
    }

    public function addComment(int $postId, int $userId, string $body): void
    {
        Database::connection()->prepare('INSERT INTO comments (post_id, user_id, body, moderation_state, is_hidden, created_at, updated_at) VALUES (:post_id, :user_id, :body, :state, 0, NOW(), NOW())')
            ->execute([
                'post_id' => $postId,
                'user_id' => $userId,
                'body' => $body,
                'state' => 'visible',
            ]);
        Database::connection()->prepare('UPDATE posts SET comment_count = comment_count + 1 WHERE id = :id')->execute(['id' => $postId]);
    }

    public function recordInteraction(string $table, int $postId, int $userId): void
    {
        $allowed = ['post_favorites', 'post_bookmarks'];
        if (!in_array($table, $allowed, true)) {
            return;
        }
        Database::connection()->prepare("INSERT IGNORE INTO {$table} (post_id, user_id, created_at) VALUES (:post_id, :user_id, NOW())")
            ->execute(['post_id' => $postId, 'user_id' => $userId]);
        $counter = $table === 'post_favorites' ? 'favorite_count' : 'bookmark_count';
        Database::connection()->prepare("UPDATE posts SET {$counter} = (SELECT COUNT(*) FROM {$table} WHERE post_id = :id) WHERE id = :id")
            ->execute(['id' => $postId]);
    }

    public function vote(int $postId, int $userId, int $vote): void
    {
        Database::connection()->prepare('INSERT INTO post_votes (post_id, user_id, vote, created_at)
            VALUES (:post_id, :user_id, :vote, NOW())
            ON DUPLICATE KEY UPDATE vote = VALUES(vote)')
            ->execute(['post_id' => $postId, 'user_id' => $userId, 'vote' => $vote]);

        Database::connection()->prepare('UPDATE posts SET
                like_count = (SELECT COUNT(*) FROM post_votes WHERE post_id = :id AND vote = 1),
                dislike_count = (SELECT COUNT(*) FROM post_votes WHERE post_id = :id AND vote = -1)
            WHERE id = :id')->execute(['id' => $postId]);
    }

    public function reportPost(int $postId, int $userId, string $reason): void
    {
        Database::connection()->prepare('INSERT INTO post_reports (post_id, user_id, reason, status, created_at)
            VALUES (:post_id, :user_id, :reason, :status, NOW())')
            ->execute(['post_id' => $postId, 'user_id' => $userId, 'reason' => $reason, 'status' => 'open']);
        Database::connection()->prepare('UPDATE posts SET report_count = (SELECT COUNT(*) FROM post_reports WHERE post_id = :id AND status = \"open\") WHERE id = :id')
            ->execute(['id' => $postId]);
    }

    public function reportComment(int $commentId, int $userId, string $reason): void
    {
        Database::connection()->prepare('INSERT INTO comment_reports (comment_id, user_id, reason, status, created_at)
            VALUES (:comment_id, :user_id, :reason, :status, NOW())')
            ->execute(['comment_id' => $commentId, 'user_id' => $userId, 'reason' => $reason, 'status' => 'open']);
    }

    public function updateEditable(int $postId, int $userId, string $title, string $description, int $categoryId, array $tags): bool
    {
        $stmt = Database::connection()->prepare('UPDATE posts SET title = :title, description = :description, category_id = :category_id, updated_at = NOW() WHERE id = :id AND user_id = :user_id');
        $ok = $stmt->execute([
            'title' => $title,
            'description' => $description,
            'category_id' => $categoryId > 0 ? $categoryId : null,
            'id' => $postId,
            'user_id' => $userId,
        ]);
        if ($ok) {
            $this->syncTags($postId, $tags);
        }
        return $ok;
    }
}
