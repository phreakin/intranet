<?php

declare(strict_types=1);

namespace Intranet\Modules\Moderation\Controllers;

use Intranet\Core\Database;
use Intranet\Core\View;
use Intranet\Modules\Moderation\Services\ModerationDashboardService;

final class ModerationController
{
    private ModerationDashboardService $service;

    public function __construct()
    {
        $this->service = new ModerationDashboardService(Database::connection());
    }

    public function dashboard(): void
    {
        View::render('moderation/dashboard', [
            'title'            => 'Moderation Command Center',
            'quickStats'       => $this->service->getQuickStats(),
            'reportedPosts'    => $this->service->getReportedPosts(),
            'reportedComments' => $this->service->getReportedComments(),
            'recentActions'    => $this->service->getRecentActions(),
        ]);
    }

    public function widget(): void
    {
        $widget = $_GET['widget'] ?? '';

        $allowed = [
            'quick-stats',
            'reported-posts',
            'reported-comments',
            'recent-actions',
        ];

        if (!in_array($widget, $allowed, true)) {
            http_response_code(404);
            echo 'Invalid moderation widget';
            return;
        }

        $data = $this->service->getWidgetData($widget);
        extract($data, EXTR_SKIP);

        $viewPath = dirname(__DIR__, 4)
            . '/resources/views/moderation/widgets/'
            . $widget . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo 'Moderation widget view not found';
            return;
        }

        require $viewPath;
    }
}