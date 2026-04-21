<?php

declare(strict_types=1);

namespace Intranet\Modules\Cms\Repositories;

use Intranet\Core\Database;

final class CmsRepository
{
    // ----- Pages ---------------------------------------------------------

    public function publishedPageBySlug(string $slug): ?array
    {
        $stmt = Database::connection()->prepare(
            "SELECT * FROM cms_pages WHERE slug = :slug AND status = 'published' LIMIT 1"
        );
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch() ?: null;
    }

    public function pageById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM cms_pages WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function allPages(): array
    {
        return Database::connection()
            ->query('SELECT id, slug, title, status, layout, published_at, updated_at FROM cms_pages ORDER BY updated_at DESC LIMIT 200')
            ->fetchAll();
    }

    /**
     * @param array{slug:string,title:string,summary:?string,body:string,status:string,layout:string} $data
     */
    public function createPage(array $data, int $authorId): int
    {
        $slug = $this->normalizeSlug($data['slug'] ?: $data['title']);
        $status = $this->normalizeStatus($data['status']);
        $publishedAt = $status === 'published' ? date('Y-m-d H:i:s') : null;

        $stmt = Database::connection()->prepare(
            'INSERT INTO cms_pages (slug, title, summary, body, status, layout, author_id, published_at, created_at, updated_at)
             VALUES (:slug, :title, :summary, :body, :status, :layout, :author_id, :published_at, NOW(), NOW())'
        );
        $stmt->execute([
            'slug' => $slug,
            'title' => $data['title'],
            'summary' => $data['summary'] ?? null,
            'body' => $data['body'],
            'status' => $status,
            'layout' => $data['layout'] ?: 'default',
            'author_id' => $authorId,
            'published_at' => $publishedAt,
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    /**
     * @param array{slug:string,title:string,summary:?string,body:string,status:string,layout:string} $data
     */
    public function updatePage(int $id, array $data): void
    {
        $existing = $this->pageById($id);
        if ($existing === null) {
            return;
        }

        $slug = $this->normalizeSlug($data['slug'] ?: $data['title']);
        $status = $this->normalizeStatus($data['status']);

        // Preserve published_at when staying published; set it when moving to published; clear otherwise.
        $publishedAt = $existing['published_at'];
        if ($status === 'published' && empty($publishedAt)) {
            $publishedAt = date('Y-m-d H:i:s');
        } elseif ($status !== 'published') {
            $publishedAt = null;
        }

        $stmt = Database::connection()->prepare(
            'UPDATE cms_pages
                SET slug = :slug,
                    title = :title,
                    summary = :summary,
                    body = :body,
                    status = :status,
                    layout = :layout,
                    published_at = :published_at,
                    updated_at = NOW()
              WHERE id = :id'
        );
        $stmt->execute([
            'slug' => $slug,
            'title' => $data['title'],
            'summary' => $data['summary'] ?? null,
            'body' => $data['body'],
            'status' => $status,
            'layout' => $data['layout'] ?: 'default',
            'published_at' => $publishedAt,
            'id' => $id,
        ]);
    }

    public function deletePage(int $id): void
    {
        Database::connection()->prepare('DELETE FROM cms_pages WHERE id = :id')->execute(['id' => $id]);
    }

    // ----- Blocks --------------------------------------------------------

    /**
     * @return array<int,array<string,mixed>>
     */
    public function allBlocks(): array
    {
        return Database::connection()
            ->query('SELECT id, block_key, label, is_active, updated_at FROM cms_blocks ORDER BY block_key ASC')
            ->fetchAll();
    }

    public function blockByKey(string $key): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM cms_blocks WHERE block_key = :key AND is_active = 1 LIMIT 1'
        );
        $stmt->execute(['key' => $key]);
        return $stmt->fetch() ?: null;
    }

    public function blockById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM cms_blocks WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function upsertBlock(string $blockKey, string $label, string $content, bool $isActive, int $actorId): void
    {
        $key = $this->normalizeBlockKey($blockKey);
        $stmt = Database::connection()->prepare(
            'INSERT INTO cms_blocks (block_key, label, content, is_active, updated_by, created_at, updated_at)
             VALUES (:key, :label, :content, :is_active, :updated_by, NOW(), NOW())
             ON DUPLICATE KEY UPDATE
                 label = VALUES(label),
                 content = VALUES(content),
                 is_active = VALUES(is_active),
                 updated_by = VALUES(updated_by),
                 updated_at = NOW()'
        );
        $stmt->execute([
            'key' => $key,
            'label' => $label,
            'content' => $content,
            'is_active' => $isActive ? 1 : 0,
            'updated_by' => $actorId,
        ]);
    }

    public function deleteBlock(int $id): void
    {
        Database::connection()->prepare('DELETE FROM cms_blocks WHERE id = :id')->execute(['id' => $id]);
    }

    // ----- Menus ---------------------------------------------------------

    /**
     * @return array<int,array<string,mixed>>
     */
    public function menus(): array
    {
        return Database::connection()
            ->query('SELECT id, slug, label, is_active FROM cms_menus ORDER BY label ASC')
            ->fetchAll();
    }

    public function menuBySlug(string $slug): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM cms_menus WHERE slug = :slug AND is_active = 1 LIMIT 1'
        );
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch() ?: null;
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function menuItems(int $menuId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT id, label, url, position, target, is_active
               FROM cms_menu_items
              WHERE menu_id = :menu_id
              ORDER BY position ASC, id ASC'
        );
        $stmt->execute(['menu_id' => $menuId]);
        return $stmt->fetchAll();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function activeMenuItems(string $menuSlug): array
    {
        $menu = $this->menuBySlug($menuSlug);
        if ($menu === null) {
            return [];
        }
        $stmt = Database::connection()->prepare(
            'SELECT id, label, url, position, target
               FROM cms_menu_items
              WHERE menu_id = :menu_id AND is_active = 1
              ORDER BY position ASC, id ASC'
        );
        $stmt->execute(['menu_id' => (int) $menu['id']]);
        return $stmt->fetchAll();
    }

    public function createMenu(string $slug, string $label): int
    {
        $normalized = $this->normalizeSlug($slug ?: $label);
        Database::connection()->prepare(
            'INSERT INTO cms_menus (slug, label, is_active, created_at, updated_at)
             VALUES (:slug, :label, 1, NOW(), NOW())
             ON DUPLICATE KEY UPDATE label = VALUES(label), updated_at = NOW()'
        )->execute(['slug' => $normalized, 'label' => $label]);

        $stmt = Database::connection()->prepare('SELECT id FROM cms_menus WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $normalized]);
        return (int) $stmt->fetchColumn();
    }

    public function deleteMenu(int $id): void
    {
        Database::connection()->prepare('DELETE FROM cms_menus WHERE id = :id')->execute(['id' => $id]);
    }

    public function addMenuItem(int $menuId, string $label, string $url, int $position, string $target, bool $isActive): void
    {
        Database::connection()->prepare(
            'INSERT INTO cms_menu_items (menu_id, label, url, position, target, is_active)
             VALUES (:menu_id, :label, :url, :position, :target, :is_active)'
        )->execute([
            'menu_id' => $menuId,
            'label' => $label,
            'url' => $url,
            'position' => $position,
            'target' => in_array($target, ['_self', '_blank'], true) ? $target : '_self',
            'is_active' => $isActive ? 1 : 0,
        ]);
    }

    public function deleteMenuItem(int $itemId): void
    {
        Database::connection()->prepare('DELETE FROM cms_menu_items WHERE id = :id')->execute(['id' => $itemId]);
    }

    // ----- Helpers -------------------------------------------------------

    private function normalizeSlug(string $raw): string
    {
        $slug = strtolower(trim((string) preg_replace('/[^a-z0-9]+/i', '-', $raw), '-'));
        return $slug !== '' ? $slug : 'page-' . bin2hex(random_bytes(3));
    }

    private function normalizeBlockKey(string $raw): string
    {
        $key = strtolower(trim((string) preg_replace('/[^a-z0-9._-]+/i', '.', $raw), '.-_'));
        return $key !== '' ? $key : 'block.' . bin2hex(random_bytes(3));
    }

    private function normalizeStatus(string $raw): string
    {
        $status = strtolower(trim($raw));
        return in_array($status, ['draft', 'published', 'archived'], true) ? $status : 'draft';
    }
}
