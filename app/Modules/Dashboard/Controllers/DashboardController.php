<?php

declare(strict_types=1);

namespace Intranet\Modules\Dashboard\Controllers;

use Intranet\Core\Database;
use Intranet\Core\View;
use Intranet\Modules\Dashboard\Services\DashboardWidgetService;

final class DashboardController
{
    private DashboardWidgetService $widgets;

    public function __construct(?DashboardWidgetService $widgets = null)
    {
        $this->widgets = $widgets ?? new DashboardWidgetService(Database::connection());
    }

    public function index(): void
    {
        View::render('dashboard/index', [
            'title' => 'Control Dashboard',
            'dashboardWidgets' => $this->widgets->getDashboardWidgets(),
            'dashboardGroups' => $this->widgets->getDashboardGroups(),
            'dashboardAutoRefreshMs' => 60000,
        ]);
    }

    public function widget(string $widget): void
    {
        if (!$this->widgets->isAllowedWidget($widget)) {
            http_response_code(404);
            echo 'Invalid widget';
            return;
        }

        $widgetDefinition = $this->widgets->getWidgetDefinition($widget);
        $widgetData = $this->widgets->getWidgetData($widget);
        $templatePath = dirname(__DIR__, 4) . '/resources/views/dashboard/widgets/' . $widgetDefinition['template'] . '.php';

        if (!is_file($templatePath)) {
            http_response_code(404);
            echo 'Widget template missing';
            return;
        }

        if (!headers_sent()) {
            header('Content-Type: text/html; charset=utf-8');
        }
        extract($widgetData, EXTR_SKIP);
        require $templatePath;
    }
}
