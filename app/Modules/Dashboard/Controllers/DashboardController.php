<?php

declare(strict_types=1);

namespace Intranet\Modules\Dashboard\Controllers;

use Intranet\Core\Database;
use Intranet\Core\View;
use Intranet\Modules\Dashboard\Services\DashboardWidgetService;
use Intranet\Modules\Shared\Repositories\PostRepository;

final class DashboardController
{
    private DashboardWidgetService $widgets;

    public function __construct()
    {
        $this->widgets = new DashboardWidgetService(Database::connection());
    }

    /**
     * Main dashboard view
     */
    public function index(): void
    {
        View::render('dashboard/index', [
            'title'         => 'Control Dashboard',
            'posts'         => (new PostRepository())->feed(60),

            // Dashboard widgets (initial load)
            'quickStats'    => $this->widgets->getQuickStats(),
            'newestPosts'   => $this->widgets->getNewestPosts(),
            'trendingPosts' => $this->widgets->getTrendingPosts(),
            'activityFeed'  => $this->widgets->getActivityFeed(),
            'systemAlerts'  => $this->widgets->getSystemAlerts(),
        ]);
    }

    /**
     * AJAX widget loader
     * Used by dashboard.js
     */
    public function widget(): void
    {
        $widget = $_GET['widget'] ?? '';

        $allowedWidgets = [
            'quick-stats',
            'newest-posts',
            'trending-posts',
            'activity-feed',
            'system-alerts',
        ];

        if (!in_array($widget, $allowedWidgets, true)) {
            http_response_code(404);
            echo 'Invalid widget';
            return;
        }

        // Get widget data
        $data = $this->widgets->getWidgetData($widget);

        // Extract variables safely
        extract($data, EXTR_SKIP);

        // IMPORTANT: match your current view structure
        $viewPath = dirname(__DIR__, 4)
            . '/resources/views/dashboard/widgets/'
            . $widget . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo 'Widget view not found';
            return;
        }

        require $viewPath;
    }
}