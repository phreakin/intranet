<?php

declare(strict_types=1);

namespace Intranet\Modules\Moderation\Services;

use PDO;

final class ModerationDashboardService
{
    public function __construct(private PDO $pdo)
    {
    }

    public function getQuickStats(): array
    {
        return [
            'reported_posts' => $this->fetchScalar("SELECT COUNT(*) FROM post_reports"),
            'reported_comments' => $this->tableExists('comment_reports')
                ? $this->fetchScalar("SELECT COUNT(*) FROM comment_reports")
                : 0,
            'total_comments' => $this->tableExists('comments')
                ? $this->fetchScalar("SELECT COUNT(*) FROM comments")
                : 0,
            'total_posts' => $this->fetchScalar("SELECT COUNT(*) FROM posts"),
        ];
    }

    public function getReportedPosts(int $limit = 12): array
    {
        $sql = "
            SELECT
                p.id,
                p.title,
                p.description,
                p.thumbnail,
                p.created_at,
                COUNT(pr.id) AS report_count,
                MAX(pr.created_at) AS last_reported_at
            FROM posts p
            INNER JOIN post_reports pr ON pr.post_id = p.id
            GROUP BY p.id, p.title, p.description, p.thumbnail, p.created_at
            ORDER BY report_count DESC, last_reported_at DESC
            LIMIT :limit
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getReportedComments(int $limit = 12): array
    {
        if (!$this->tableExists('comment_reports') || !$this->tableExists('comments')) {
            return [];
        }

        $sql = "
            SELECT
                c.id,
                c.post_id,
                c.content,
                c.created_at,
                COUNT(cr.id) AS report_count,
                MAX(cr.created_at) AS last_reported_at
            FROM comments c
            INNER JOIN comment_reports cr ON cr.comment_id = c.id
            GROUP BY c.id, c.post_id, c.content, c.created_at
            ORDER BY report_count DESC, last_reported_at DESC
            LIMIT :limit
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getRecentActions(int $limit = 12): array
    {
        if (!$this->tableExists('moderation_logs')) {
            return [];
        }

        $sql = "
            SELECT
                id,
                action_type,
                target_type,
                target_id,
                reason,
                created_at
            FROM moderation_logs
            ORDER BY created_at DESC
            LIMIT :limit
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getWidgetData(string $widget): array
    {
        return match ($widget) {
            'quick-stats'       => ['stats' => $this->getQuickStats()],
            'reported-posts'    => ['posts' => $this->getReportedPosts()],
            'reported-comments' => ['comments' => $this->getReportedComments()],
            'recent-actions'    => ['actions' => $this->getRecentActions()],
            default             => [],
        };
    }

    private function fetchScalar(string $sql): int
    {
        $stmt = $this->pdo->query($sql);
        $value = $stmt ? $stmt->fetchColumn() : 0;
        return (int) $value;
    }

    private function tableExists(string $table): bool
    {
        $stmt = $this->pdo->prepare("SHOW TABLES LIKE :table");
        $stmt->bindValue(':table', $table);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }
}