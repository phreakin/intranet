<?php $stats = $stats ?? []; ?>
<div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
    <article class="metric-tile">
        <div class="panel-meta">Total Posts</div>
        <div class="metric-value"><?= (int) ($stats['total_posts'] ?? 0) ?></div>
        <div class="text-xs text-slate-400">Signals currently tracked in the primary feed.</div>
    </article>
    <article class="metric-tile">
        <div class="panel-meta">Comments Today</div>
        <div class="metric-value"><?= (int) ($stats['comments_today'] ?? 0) ?></div>
        <div class="text-xs text-slate-400">Conversation volume over the current day boundary.</div>
    </article>
    <article class="metric-tile">
        <div class="panel-meta">Open Reports</div>
        <div class="metric-value"><?= (int) ($stats['reports_pending'] ?? 0) ?></div>
        <div class="text-xs text-slate-400">Items still waiting for moderation review.</div>
    </article>
    <article class="metric-tile">
        <div class="panel-meta">Bookmarks</div>
        <div class="metric-value"><?= (int) ($stats['bookmarks_total'] ?? 0) ?></div>
        <div class="text-xs text-slate-400">Save-for-later volume across the full signal graph.</div>
    </article>
    <article class="metric-tile">
        <div class="panel-meta">Active Categories</div>
        <div class="metric-value"><?= (int) ($stats['active_categories'] ?? 0) ?></div>
        <div class="text-xs text-slate-400">Taxonomy lanes with live content attached.</div>
    </article>
</div>
