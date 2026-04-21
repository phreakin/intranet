<?php

use Intranet\Core\Helpers;

$dashboardWidgets = $dashboardWidgets ?? [];
$dashboardGroups = $dashboardGroups ?? [];
$dashboardAutoRefreshMs = (int) ($dashboardAutoRefreshMs ?? 60000);
?>
<div class="page-shell dashboard-shell" data-dashboard-shell data-dashboard-autorefresh-interval="<?= $dashboardAutoRefreshMs ?>">
    <section class="page-hero glass-panel">
        <div class="page-hero-grid">
            <div>
                <span class="eyebrow">Interactive Control Room</span>
                <h1 class="page-title">Modular intelligence dashboard with refreshable panels, activity filters, and operator controls.</h1>
                <p class="page-copy">This is a lightweight server-rendered dashboard system with selective widget refresh, local panel state, and minimal JavaScript overhead.</p>
            </div>
            <div class="page-meta">
                <span class="chip chip-status">Refreshable Widgets</span>
                <span class="chip chip-category">SSR First</span>
                <span class="chip chip-tag">Local State</span>
            </div>
        </div>
    </section>

    <section class="glass-panel dashboard-toolbar">
        <div class="dashboard-toolbar-copy">
            <div class="panel-kicker">Widget Filters</div>
            <h2 class="panel-title">Interactive dashboard controls</h2>
            <p class="panel-copy">Filter the board by signal type, refresh selected modules, or enable cheap timed updates.</p>
        </div>

        <div class="dashboard-toolbar-actions">
            <label class="dashboard-field">
                <span class="dashboard-field-label">View</span>
                <select class="form-select form-select-sm" data-dashboard-filter>
                    <?php foreach ($dashboardGroups as $groupKey => $groupLabel): ?>
                        <option value="<?= Helpers::e($groupKey) ?>"><?= Helpers::e($groupLabel) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <button type="button" class="btn btn-outline-light btn-sm" data-dashboard-refresh-all>Refresh All</button>
            <button type="button" class="btn btn-outline-info btn-sm" data-dashboard-autorefresh-toggle>Auto Refresh: Off</button>
        </div>
    </section>

    <div class="dashboard-grid" data-dashboard-grid>
        <?php foreach ($dashboardWidgets as $widget): ?>
            <?php
            $templatePath = dirname(__DIR__, 1) . '/dashboard/widgets/' . $widget['template'] . '.php';
            $widgetData = (array) ($widget['data'] ?? []);
            ?>
            <section
                class="glass-panel dashboard-widget widget-span-<?= Helpers::e((string) $widget['span']) ?>"
                data-widget-container
                data-widget-name="<?= Helpers::e((string) $widget['name']) ?>"
                data-widget-group="<?= Helpers::e((string) $widget['group']) ?>"
            >
                <div class="widget-header">
                    <div>
                        <h2 class="widget-title"><?= Helpers::e((string) $widget['title']) ?></h2>
                        <span class="widget-meta"><?= Helpers::e((string) $widget['meta']) ?></span>
                    </div>
                    <div class="widget-actions">
                        <button type="button" class="widget-action" data-widget-refresh="<?= Helpers::e((string) $widget['name']) ?>" title="Refresh widget">↻</button>
                        <button type="button" class="widget-action" data-widget-collapse title="Collapse widget">–</button>
                    </div>
                </div>

                <div class="widget-body" data-widget-body>
                    <?php extract($widgetData, EXTR_SKIP); ?>
                    <?php require $templatePath; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
</div>
