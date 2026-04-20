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
<div class="row g-3 mt-1">
    <div class="col-lg-6">
        <div class="glass-panel p-3">
            <h2 class="h6 text-uppercase">Manage taxonomy</h2>
            <form method="post" action="/admin/categories" class="d-flex gap-2 mb-2">
                <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                <input type="text" class="form-control form-control-sm" name="name" placeholder="New category name" required>
                <button class="btn btn-sm btn-outline-info" type="submit">Add category</button>
            </form>
            <form method="post" action="/admin/tags" class="d-flex gap-2">
                <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                <input type="text" class="form-control form-control-sm" name="name" placeholder="New tag name" required>
                <button class="btn btn-sm btn-outline-info" type="submit">Add tag</button>
            </form>
            <div class="small text-secondary mt-3">Categories: <?= Helpers::e(implode(', ', array_map(static fn ($c) => (string) $c['name'], $stats['categories'] ?? []))) ?></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="glass-panel p-3">
            <h2 class="h6 text-uppercase">Manage posts</h2>
            <div class="small vstack gap-2">
                <?php foreach (($stats['manageable_posts'] ?? []) as $post): ?>
                    <div class="d-flex justify-content-between gap-2">
                        <span class="text-secondary">#<?= (int) $post['id'] ?> · <?= Helpers::e($post['title']) ?> · <?= Helpers::e($post['display_name']) ?></span>
                        <span class="d-flex gap-1">
                            <a class="btn btn-sm btn-outline-light" href="/admin/posts/<?= (int) $post['id'] ?>/edit">Edit</a>
                            <form method="post" action="/admin/posts/<?= (int) $post['id'] ?>/delete">
                                <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<div class="glass-panel p-3 mt-3">
    <h2 class="h6 text-uppercase">AI moderation review</h2>
    <div class="vstack gap-2 small">
        <?php foreach (($stats['ai_flags'] ?? []) as $flag): ?>
            <div class="d-flex flex-wrap justify-content-between gap-2 border-bottom border-secondary pb-2">
                <span class="text-secondary">
                    #<?= (int) $flag['id'] ?> · <?= Helpers::e($flag['target_type']) ?>:<?= (int) $flag['target_id'] ?>
                    · risk <?= Helpers::e($flag['risk_level']) ?> · conf <?= Helpers::e((string) $flag['confidence']) ?>
                    · <?= Helpers::e($flag['recommendation']) ?> · tags <?= Helpers::e((string) $flag['suggested_tags']) ?>
                </span>
                <?php if (($flag['review_status'] ?? 'pending') === 'pending'): ?>
                    <form method="post" action="/admin/ai/<?= (int) $flag['id'] ?>/review" class="d-flex gap-1">
                        <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                        <button class="btn btn-sm btn-outline-success" type="submit" name="decision" value="accepted">Accept</button>
                        <button class="btn btn-sm btn-outline-warning" type="submit" name="decision" value="overridden">Override</button>
                        <button class="btn btn-sm btn-outline-danger" type="submit" name="decision" value="rejected">Reject</button>
                    </form>
                <?php else: ?>
                    <span class="badge bg-secondary">Reviewed: <?= Helpers::e((string) ($flag['admin_decision'] ?? $flag['review_status'])) ?></span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
