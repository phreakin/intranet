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

    public function __construct(?DashboardWidgetService $widgets = null)
    {
        $this->widgets = $widgets ?? new DashboardWidgetService(Database::connection());
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
    public function widget(?string $widget = null): void
    {
        $widgetName = $widget ?? (string) ($_GET['widget'] ?? '');

        if (!$this->widgets->isAllowedWidget($widgetName)) {
            http_response_code(404);
            echo 'Invalid widget';
            return;
        }

        // Get widget data
        $data = $this->widgets->getWidgetData($widgetName);
        $definition = $this->widgets->getWidgetDefinition($widgetName);
        $template = (string) ($definition['template'] ?? str_replace('-', '_', $widgetName));

        // Extract variables safely
        extract($data, EXTR_SKIP);

        // IMPORTANT: match your current view structure
        $viewPath = dirname(__DIR__, 4)
            . '/resources/views/dashboard/widgets/'
            . $template . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo 'Widget view not found';
            return;
        }

        require $viewPath;
    }
}