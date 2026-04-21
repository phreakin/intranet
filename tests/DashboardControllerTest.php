<?php

declare(strict_types=1);

namespace Tests;

use Intranet\Modules\Dashboard\Controllers\DashboardController;
use Intranet\Modules\Dashboard\Services\DashboardWidgetService;

final class DashboardControllerTest extends TestCase
{
    protected function setUp(): void
    {
        http_response_code(200);
    }

    protected function tearDown(): void
    {
        http_response_code(200);
    }

    public function testWidgetRejectsUnknownWidget(): void
    {
        $controller = new DashboardController(new FakeDashboardWidgetService());

        ob_start();
        $controller->widget('unknown-widget');
        $output = (string) ob_get_clean();

        $this->assertSame(404, http_response_code());
        $this->assertContains('Invalid widget', $output);
    }

    public function testWidgetRendersQuickStatsPartial(): void
    {
        $controller = new DashboardController(new FakeDashboardWidgetService());

        ob_start();
        $controller->widget('quick-stats');
        $output = (string) ob_get_clean();

        $this->assertSame(200, http_response_code());
        $this->assertContains('Total Posts', $output);
        $this->assertContains('42', $output);
    }
}

final class FakeDashboardWidgetService extends DashboardWidgetService
{
    public function __construct()
    {
    }

    public function isAllowedWidget(string $widget): bool
    {
        return in_array($widget, ['quick-stats', 'newest-posts', 'trending-posts', 'activity-feed', 'system-alerts'], true);
    }

    public function getWidgetDefinition(string $widget): array
    {
        return [
            'template' => $widget === 'quick-stats' ? 'quick_stats' : 'activity_feed',
        ];
    }

    public function getWidgetData(string $widget): array
    {
        if ($widget === 'quick-stats') {
            return [
                'stats' => [
                    'total_posts' => 42,
                    'comments_today' => 7,
                    'reports_pending' => 3,
                    'bookmarks_total' => 9,
                    'active_categories' => 5,
                ],
            ];
        }

        return [
            'items' => [],
        ];
    }
}
