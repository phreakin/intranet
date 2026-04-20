<?php

declare(strict_types=1);

namespace Intranet\Modules\Shared\Repositories;

use Intranet\Core\Database;
use PDOException;

final class CmsRepository
{
    public function listRecent(?string $contentType = null, int $limit = 30): array
    {
        $sql = 'SELECT p.*, c.name AS category_name, u.display_name AS author_name,
                       GROUP_CONCAT(DISTINCT t.name) AS tags
                FROM pages p
                JOIN users u ON u.id = p.author_user_id
                LEFT JOIN categories c ON c.id = p.category_id
                LEFT JOIN page_tags pt ON pt.page_id = p.id
                LEFT JOIN tags t ON t.id = pt.tag_id';
        $params = [];
        if ($contentType !== null) {
            $sql .= ' WHERE p.content_type = :content_type';
            $params['content_type'] = $contentType;
        }
        $sql .= ' GROUP BY p.id ORDER BY p.updated_at DESC LIMIT :limit';

        $stmt = Database::connection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findPublishedBySlug(string $slug, string $contentType): ?array
    {
        $stmt = Database::connection()->prepare('SELECT p.*, c.name AS category_name, u.display_name AS author_name,
                GROUP_CONCAT(DISTINCT t.name) AS tags
            FROM pages p
            JOIN users u ON u.id = p.author_user_id
            LEFT JOIN categories c ON c.id = p.category_id
            LEFT JOIN page_tags pt ON pt.page_id = p.id
            LEFT JOIN tags t ON t.id = pt.tag_id
            WHERE p.slug = :slug
              AND p.content_type = :content_type
              AND (p.status = "published" OR (p.status = "draft" AND p.author_user_id = :current_user))
            GROUP BY p.id LIMIT 1');
        $stmt->execute([
            'slug' => $slug,
            'content_type' => $contentType,
            'current_user' => (int) ($_SESSION['uid'] ?? 0),
        ]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT p.*, GROUP_CONCAT(DISTINCT t.name) AS tags
            FROM pages p
            LEFT JOIN page_tags pt ON pt.page_id = p.id
            LEFT JOIN tags t ON t.id = pt.tag_id
            WHERE p.id = :id
            GROUP BY p.id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function revisions(int $pageId): array
    {
        $stmt = Database::connection()->prepare('SELECT pr.*, u.display_name
            FROM page_revisions pr
            JOIN users u ON u.id = pr.edited_by
            WHERE pr.page_id = :page_id
            ORDER BY pr.created_at DESC
            LIMIT 50');
        $stmt->execute(['page_id' => $pageId]);
        return $stmt->fetchAll();
    }

    public function create(array $payload, array $tags): int
    {
        $db = Database::connection();
        $db->prepare('INSERT INTO pages (
            author_user_id, category_id, content_type, title, slug, excerpt, body_markdown, body_html, status,
            scheduled_publish_at, published_at, created_at, updated_at
        ) VALUES (
            :author_user_id, :category_id, :content_type, :title, :slug, :excerpt, :body_markdown, :body_html, :status,
            :scheduled_publish_at, :published_at, NOW(), NOW()
        )')->execute($payload);
        $pageId = (int) $db->lastInsertId();
        $this->syncTags($pageId, $tags);
        $this->insertRevision($pageId, (int) $payload['author_user_id']);
        return $pageId;
    }

    public function update(int $pageId, array $payload, array $tags, int $editorId): void
    {
        $db = Database::connection();
        $payload['id'] = $pageId;
        $db->prepare('UPDATE pages SET
            category_id = :category_id,
            title = :title,
            slug = :slug,
            excerpt = :excerpt,
            body_markdown = :body_markdown,
            body_html = :body_html,
            status = :status,
            scheduled_publish_at = :scheduled_publish_at,
            published_at = :published_at,
            updated_at = NOW()
            WHERE id = :id')->execute($payload);
        $this->syncTags($pageId, $tags);
        $this->insertRevision($pageId, $editorId);
    }

    public function syncTags(int $pageId, array $tags): void
    {
        $db = Database::connection();
        $db->prepare('DELETE FROM page_tags WHERE page_id = :page_id')->execute(['page_id' => $pageId]);
        foreach ($tags as $tag) {
            $name = trim($tag);
            if ($name === '') {
                continue;
            }
            $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
            $db->prepare('INSERT INTO tags (name, slug, created_at) VALUES (:name, :slug, NOW())
                    ON DUPLICATE KEY UPDATE name = VALUES(name)')
                ->execute(['name' => $name, 'slug' => $slug]);
            $tagStmt = $db->prepare('SELECT id FROM tags WHERE slug = :slug LIMIT 1');
            $tagStmt->execute(['slug' => $slug]);
            $tagId = (int) $tagStmt->fetchColumn();
            if ($tagId > 0) {
                $db->prepare('INSERT INTO page_tags (page_id, tag_id) VALUES (:page_id, :tag_id)')
                    ->execute(['page_id' => $pageId, 'tag_id' => $tagId]);
            }
        }
    }

    public function categories(): array
    {
        return Database::connection()->query('SELECT id, name FROM categories ORDER BY name ASC')->fetchAll();
    }

    public function search(string $query, ?string $type): array
    {
        $db = Database::connection();
        $sql = 'SELECT p.id, p.title, p.slug, p.content_type, p.excerpt, p.status, p.updated_at
                FROM pages p
                WHERE ';
        $params = [];
        try {
            $sql .= 'MATCH (p.title, p.excerpt, p.body_markdown) AGAINST (:q IN NATURAL LANGUAGE MODE)';
            $params['q'] = $query;
            if ($type !== null) {
                $sql .= ' AND p.content_type = :content_type';
                $params['content_type'] = $type;
            }
            $sql .= ' ORDER BY p.updated_at DESC LIMIT 40';
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException) {
            $fallback = 'SELECT p.id, p.title, p.slug, p.content_type, p.excerpt, p.status, p.updated_at
                    FROM pages p
                    WHERE (p.title LIKE :like OR p.excerpt LIKE :like OR p.body_markdown LIKE :like)';
            $fallbackParams = ['like' => '%' . $query . '%'];
            if ($type !== null) {
                $fallback .= ' AND p.content_type = :content_type';
                $fallbackParams['content_type'] = $type;
            }
            $fallback .= ' ORDER BY p.updated_at DESC LIMIT 40';
            $stmt = $db->prepare($fallback);
            $stmt->execute($fallbackParams);
            return $stmt->fetchAll();
        }
    }

    private function insertRevision(int $pageId, int $editorId): void
    {
        Database::connection()->prepare('INSERT INTO page_revisions (
            page_id, edited_by, title, excerpt, body_markdown, body_html, status, created_at
        )
        SELECT id, :edited_by, title, excerpt, body_markdown, body_html, status, NOW() FROM pages WHERE id = :id')
            ->execute([
                'edited_by' => $editorId,
                'id' => $pageId,
            ]);
    }
}
