<?php

declare(strict_types=1);

namespace Intranet\Modules\Dashboard\Services;

use PDO;

class DashboardWidgetService
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @return array<string,string>
     */
    public function getDashboardGroups(): array
    {
        return [
            'all' => 'All Panels',
            'overview' => 'Overview',
            'content' => 'Content',
            'activity' => 'Activity',
            'alerts' => 'Alerts',
        ];
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function getDashboardWidgets(): array
    {
        $widgets = [];

        foreach ($this->widgetMap() as $name => $definition) {
            $definition['name'] = $name;
            $definition['data'] = $this->getWidgetData($name);
            $widgets[] = $definition;
        }

        return $widgets;
    }

    public function isAllowedWidget(string $widget): bool
    {
        return isset($this->widgetMap()[$widget]);
    }

    /**
     * @return array<string,mixed>
     */
    public function getWidgetDefinition(string $widget): array
    {
        return $this->widgetMap()[$widget] ?? [];
    }

    /**
     * @return array<string,mixed>
     */
    public function getWidgetData(string $widget): array
    {
        return match ($widget) {
            'quick-stats' => ['stats' => $this->getQuickStats()],
            'newest-posts' => ['posts' => $this->getNewestPosts()],
            'trending-posts' => ['posts' => $this->getTrendingPosts()],
            'activity-feed' => ['items' => $this->getActivityFeed()],
            'system-alerts' => ['alerts' => $this->getSystemAlerts()],
            default => [],
        };
    }

    /**
     * @return array<string,int>
     */
    public function getQuickStats(): array
    {
        return [
            'total_posts' => $this->fetchScalar('SELECT COUNT(*) FROM posts'),
            'comments_today' => $this->fetchScalar("
                SELECT COUNT(*)
                FROM comments
                WHERE DATE(created_at) = CURDATE()
            "),
            'reports_pending' => $this->fetchScalar("
                SELECT COUNT(*)
                FROM post_reports
                WHERE status = 'open'
            "),
            'bookmarks_total' => $this->fetchScalar('SELECT COALESCE(SUM(bookmark_count), 0) FROM posts'),
            'active_categories' => $this->fetchScalar("
                SELECT COUNT(DISTINCT category_id)
                FROM posts
                WHERE category_id IS NOT NULL
            "),
        ];
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function getNewestPosts(int $limit = 6): array
    {
        return $this->fetchPosts(
            'ORDER BY p.created_at DESC LIMIT :limit',
            ['limit' => $limit]
        );
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function getTrendingPosts(int $limit = 6): array
    {
        return $this->fetchPosts(
            'ORDER BY trend_score DESC, p.created_at DESC LIMIT :limit',
            ['limit' => $limit],
            ',
                (
                    (p.like_count * 2)
                    + (p.comment_count * 3)
                    + (p.bookmark_count * 2)
                    + p.favorite_count
                    - p.dislike_count
                ) AS trend_score'
        );
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function getActivityFeed(int $limit = 10): array
    {
        $sql = "
            SELECT *
            FROM (
                SELECT
                    'post' AS activity_type,
                    p.id AS entity_id,
                    p.title AS title,
                    p.created_at AS created_at,
                    CONCAT(COALESCE(u.display_name, 'Unknown'), ' submitted a new signal') AS activity_label
                FROM posts p
                LEFT JOIN users u ON u.id = p.user_id

                UNION ALL

                SELECT
                    'comment' AS activity_type,
                    c.id AS entity_id,
                    CONCAT('Comment on post #', c.post_id) AS title,
                    c.created_at AS created_at,
                    CONCAT(COALESCE(u.display_name, 'Unknown'), ' added a comment') AS activity_label
                FROM comments c
                LEFT JOIN users u ON u.id = c.user_id

                UNION ALL

                SELECT
                    'report' AS activity_type,
                    pr.id AS entity_id,
                    CONCAT('Report on post #', pr.post_id) AS title,
                    pr.created_at AS created_at,
                    CONCAT(COALESCE(u.display_name, 'Unknown'), ' opened a report') AS activity_label
                FROM post_reports pr
                LEFT JOIN users u ON u.id = pr.user_id
            ) activity_stream
            ORDER BY created_at DESC
            LIMIT :limit
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @return array<int,array<string,string|int>>
     */
    public function getSystemAlerts(): array
    {
        $alerts = [];

        $missingThumbnails = $this->fetchScalar("
            SELECT COUNT(*)
            FROM posts
            WHERE thumbnail_url IS NULL OR thumbnail_url = ''
        ");

        if ($missingThumbnails > 0) {
            $alerts[] = [
                'severity' => $missingThumbnails >= 5 ? 'high' : 'medium',
                'title' => $missingThumbnails . ' posts missing thumbnails',
                'message' => 'Restore preview assets so dense content scanning stays fast and reliable.',
            ];
        }

        $missingDescriptions = $this->fetchScalar("
            SELECT COUNT(*)
            FROM posts
            WHERE description IS NULL OR description = ''
        ");

        if ($missingDescriptions > 0) {
            $alerts[] = [
                'severity' => 'medium',
                'title' => $missingDescriptions . ' posts missing descriptions',
                'message' => 'Thin metadata lowers triage quality and weakens downstream search signals.',
            ];
        }

        $duplicateUrls = $this->fetchScalar("
            SELECT COUNT(*)
            FROM (
                SELECT canonical_url
                FROM posts
                WHERE canonical_url IS NOT NULL AND canonical_url <> ''
                GROUP BY canonical_url
                HAVING COUNT(*) > 1
            ) duplicates
        ");

        if ($duplicateUrls > 0) {
            $alerts[] = [
                'severity' => 'high',
                'title' => $duplicateUrls . ' duplicate source clusters detected',
                'message' => 'Merge or suppress duplicated links before they distort engagement and moderation data.',
            ];
        }

        $staleCategories = $this->fetchScalar("
            SELECT COUNT(*)
            FROM categories c
            LEFT JOIN posts p ON p.category_id = c.id
            GROUP BY c.id
            HAVING MAX(p.created_at) IS NULL OR MAX(p.created_at) < DATE_SUB(NOW(), INTERVAL 90 DAY)
        ");

        if ($staleCategories > 0) {
            $alerts[] = [
                'severity' => 'low',
                'title' => $staleCategories . ' stale categories need review',
                'message' => 'Dormant taxonomy branches are usually merge, archive, or quality-control candidates.',
            ];
        }

        if ($alerts === []) {
            $alerts[] = [
                'severity' => 'low',
                'title' => 'System clear',
                'message' => 'No major dashboard anomalies detected in the current snapshot.',
            ];
        }

        return $alerts;
    }

    /**
     * @param array<string,mixed> $parameters
     * @return array<int,array<string,mixed>>
     */
    private function fetchPosts(string $orderClause, array $parameters, string $extraSelect = ''): array
    {
        $sql = "
            SELECT
                p.id,
                p.title,
                p.description,
                p.thumbnail_url,
                p.site_name,
                p.like_count,
                p.dislike_count,
                p.comment_count,
                p.favorite_count,
                p.bookmark_count,
                p.created_at,
                c.name AS category_name,
                u.display_name,
                GROUP_CONCAT(DISTINCT t.name ORDER BY t.name SEPARATOR ', ') AS tags,
                GROUP_CONCAT(DISTINCT pst.status_tag ORDER BY pst.status_tag SEPARATOR ', ') AS status_tags
                $extraSelect
            FROM posts p
            LEFT JOIN categories c ON c.id = p.category_id
            LEFT JOIN users u ON u.id = p.user_id
            LEFT JOIN post_tags pt ON pt.post_id = p.id
            LEFT JOIN tags t ON t.id = pt.tag_id
            LEFT JOIN post_status_tags pst ON pst.post_id = p.id
            GROUP BY
                p.id,
                p.title,
                p.description,
                p.thumbnail_url,
                p.site_name,
                p.like_count,
                p.dislike_count,
                p.comment_count,
                p.favorite_count,
                p.bookmark_count,
                p.created_at,
                c.name,
                u.display_name
            $orderClause
        ";

        $stmt = $this->pdo->prepare($sql);

        foreach ($parameters as $name => $value) {
            $stmt->bindValue(':' . $name, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @return array<string,array<string,string>>
     */
    private function widgetMap(): array
    {
        return [
            'quick-stats' => [
                'title' => 'Quick Stats',
                'meta' => 'Live operational snapshot',
                'group' => 'overview',
                'span' => 'full',
                'template' => 'quick_stats',
            ],
            'newest-posts' => [
                'title' => 'Newest Posts',
                'meta' => 'Latest signals entering the grid',
                'group' => 'content',
                'span' => 'wide',
                'template' => 'newest_posts',
            ],
            'trending-posts' => [
                'title' => 'Trending Posts',
                'meta' => 'Engagement-weighted signal ranking',
                'group' => 'content',
                'span' => 'standard',
                'template' => 'trending_posts',
            ],
            'activity-feed' => [
                'title' => 'Activity Feed',
                'meta' => 'Recent post, comment, and report movement',
                'group' => 'activity',
                'span' => 'standard',
                'template' => 'activity_feed',
            ],
            'system-alerts' => [
                'title' => 'System Alerts',
                'meta' => 'Operational cues and maintenance pressure',
                'group' => 'alerts',
                'span' => 'standard',
                'template' => 'system_alerts',
            ],
        ];
    }

    private function fetchScalar(string $sql): int
    {
        $stmt = $this->pdo->query($sql);
        $value = $stmt ? $stmt->fetchColumn() : 0;

        return (int) $value;
    }
}
