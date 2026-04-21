<div class="stats-grid">
    <div class="metric-tile">
        <span class="metric-label">Reported Posts</span>
        <span class="metric-value danger" data-counter><?= (int) ($stats['reported_posts'] ?? 0); ?></span>
    </div>
    <div class="metric-tile">
        <span class="metric-label">Reported Comments</span>
        <span class="metric-value warning" data-counter><?= (int) ($stats['reported_comments'] ?? 0); ?></span>
    </div>
    <div class="metric-tile">
        <span class="metric-label">Total Comments</span>
        <span class="metric-value" data-counter><?= (int) ($stats['total_comments'] ?? 0); ?></span>
    </div>
    <div class="metric-tile">
        <span class="metric-label">Total Posts</span>
        <span class="metric-value" data-counter><?= (int) ($stats['total_posts'] ?? 0); ?></span>
    </div>
</div>