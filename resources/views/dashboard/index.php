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
            <div class="page-meta flex flex-wrap items-center gap-2">
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

        <div class="dashboard-toolbar-actions flex flex-col gap-3 md:flex-row md:items-end md:justify-end">
            <label class="dashboard-field flex min-w-[220px] flex-col gap-2">
                <span class="dashboard-field-label text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">View</span>
                <select class="search-input" data-dashboard-filter>
                    <?php foreach ($dashboardGroups as $groupKey => $groupLabel): ?>
                        <option value="<?= Helpers::e($groupKey) ?>"><?= Helpers::e($groupLabel) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <div class="flex flex-col gap-2 sm:flex-row">
                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm font-medium text-slate-100 transition hover:border-cyan-400/40 hover:bg-cyan-400/10" data-dashboard-refresh-all>Refresh All</button>
                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-cyan-400/20 bg-cyan-400/10 px-3 py-2 text-sm font-medium text-cyan-200 transition hover:border-cyan-300/40 hover:bg-cyan-300/15" data-dashboard-autorefresh-toggle>Auto Refresh: Off</button>
            </div>
        </div>
    </section>

    <div class="dashboard-grid grid grid-cols-1 gap-4 xl:grid-cols-12" data-dashboard-grid>
        <?php foreach ($dashboardWidgets as $widget): ?>
            <?php
            $templatePath = dirname(__DIR__, 1) . '/dashboard/widgets/' . $widget['template'] . '.php';
            $widgetData = (array) ($widget['data'] ?? []);
            $span = (string) ($widget['span'] ?? '4');
            $spanClassMap = [
                '1' => 'xl:col-span-3',
                '2' => 'xl:col-span-4',
                '3' => 'xl:col-span-6',
                '4' => 'xl:col-span-8',
                '5' => 'xl:col-span-9',
                '6' => 'xl:col-span-12',
                'full' => 'xl:col-span-12',
            ];
            $spanClass = $spanClassMap[$span] ?? 'xl:col-span-4';
            ?>
            <section
                class="glass-panel dashboard-widget widget-span-<?= Helpers::e($span) ?> <?= Helpers::e($spanClass) ?> flex flex-col gap-4 overflow-hidden p-4"
                data-widget-container
                data-widget-name="<?= Helpers::e((string) $widget['name']) ?>"
                data-widget-group="<?= Helpers::e((string) $widget['group']) ?>"
            >
                <div class="widget-header flex items-start justify-between gap-4 border-b border-white/10 pb-3">
                    <div class="min-w-0">
                        <h2 class="widget-title truncate text-sm font-semibold tracking-wide text-slate-50"><?= Helpers::e((string) $widget['title']) ?></h2>
                        <span class="widget-meta mt-1 block text-xs text-slate-400"><?= Helpers::e((string) $widget['meta']) ?></span>
                    </div>
                    <div class="widget-actions flex items-center gap-2">
                        <button type="button" class="widget-action inline-flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-sm text-slate-200 transition hover:border-cyan-400/40 hover:bg-cyan-400/10 hover:text-cyan-100" data-widget-refresh="<?= Helpers::e((string) $widget['name']) ?>" title="Refresh widget">↻</button>
                        <button type="button" class="widget-action inline-flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-sm text-slate-200 transition hover:border-violet-400/40 hover:bg-violet-400/10 hover:text-violet-100" data-widget-collapse title="Collapse widget">–</button>
                    </div>
                </div>

                <div class="widget-body min-h-0 flex-1" data-widget-body>
                    <?php extract($widgetData, EXTR_SKIP); ?>
                    <?php require $templatePath; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
</div>
