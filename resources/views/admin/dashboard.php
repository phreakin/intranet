<?php use Intranet\Core\Helpers; ?>
<div class="glass-panel p-4 mb-3">
    <h1 class="h3">Operational Intelligence Dashboard</h1>
    <p class="text-secondary">Self-aware system insights, moderation pressure, and maintenance recommendations.</p>
</div>
<div class="row g-3 mb-3">
    <?php foreach ([
        'Reported posts' => $stats['reported_posts'] ?? 0,
        'Reported comments' => $stats['reported_comments'] ?? 0,
        'AI queue' => $stats['ai_queue'] ?? 0,
        'Missing thumbnails' => $stats['missing_thumbnails'] ?? 0,
        'Missing descriptions' => $stats['missing_descriptions'] ?? 0,
        'Potential duplicates' => $stats['duplicates'] ?? 0,
        'Report spikes today' => $stats['report_spike_today'] ?? 0,
    ] as $label => $value): ?>
        <div class="col-md-3 col-6"><div class="glass-subpanel p-3"><div class="small text-secondary"><?= Helpers::e($label) ?></div><div class="display-6"><?= (int) $value ?></div></div></div>
    <?php endforeach; ?>
</div>
<div class="row g-3">
    <div class="col-lg-6">
        <div class="glass-panel p-3">
            <h2 class="h6 text-uppercase">System recommendations</h2>
            <ul class="small text-secondary ps-3 mb-0">
                <li><?= (int) ($stats['missing_thumbnails'] ?? 0) ?> posts are missing thumbnails.</li>
                <li><?= (int) ($stats['duplicates'] ?? 0) ?> potential duplicate canonical URLs detected.</li>
                <li><?= (int) ($stats['report_spike_today'] ?? 0) ?> reports were created today.</li>
                <li>Review stale categories and consolidate duplicate tags.</li>
            </ul>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="glass-panel p-3">
            <h2 class="h6 text-uppercase">Stale categories (90+ days)</h2>
            <ul class="small text-secondary ps-3 mb-0">
                <?php foreach (($stats['stale_categories'] ?? []) as $row): ?>
                    <li><?= Helpers::e($row['name']) ?> (last used: <?= Helpers::e((string) $row['last_used']) ?>)</li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<div class="mt-3 d-flex gap-2">
    <a class="btn btn-outline-light" href="/admin/moderation">Moderation Queue</a>
    <a class="btn btn-outline-warning" href="/admin/reports">Reports</a>
    <a class="btn btn-outline-success" href="/admin/users-badges">Users & Badges</a>
    <a class="btn btn-outline-info" href="/admin/bookmarklet">Admin Bookmarklet</a>
</div>
