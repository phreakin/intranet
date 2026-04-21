<?php $stats = $stats ?? []; ?>
<div class="dashboard-stats-grid">
    <article class="intel-stat-card">
        <div class="intel-stat-label">Total Posts</div>
        <div class="intel-stat-value"><?= (int) ($stats['total_posts'] ?? 0) ?></div>
        <div class="intel-stat-foot">Signals currently tracked in the primary feed.</div>
    </article>
    <article class="intel-stat-card">
        <div class="intel-stat-label">Comments Today</div>
        <div class="intel-stat-value"><?= (int) ($stats['comments_today'] ?? 0) ?></div>
        <div class="intel-stat-foot">Conversation volume over the current day boundary.</div>
    </article>
    <article class="intel-stat-card">
        <div class="intel-stat-label">Open Reports</div>
        <div class="intel-stat-value"><?= (int) ($stats['reports_pending'] ?? 0) ?></div>
        <div class="intel-stat-foot">Items still waiting for moderation review.</div>
    </article>
    <article class="intel-stat-card">
        <div class="intel-stat-label">Bookmarks</div>
        <div class="intel-stat-value"><?= (int) ($stats['bookmarks_total'] ?? 0) ?></div>
        <div class="intel-stat-foot">Save-for-later volume across the full signal graph.</div>
    </article>
    <article class="intel-stat-card">
        <div class="intel-stat-label">Active Categories</div>
        <div class="intel-stat-value"><?= (int) ($stats['active_categories'] ?? 0) ?></div>
        <div class="intel-stat-foot">Taxonomy lanes with live content attached.</div>
    </article>
</div>
