<?php use Intranet\Core\Helpers; ?>
<div class="page-shell">
    <section class="page-hero glass-panel">
        <div class="page-hero-grid">
            <div>
                <span class="eyebrow">Admin Control</span>
                <h1 class="page-title">Operational intelligence dashboard for moderation pressure, system anomalies, and response actions.</h1>
                <p class="page-copy">This screen is the system brain: flagged content, spike detection, maintenance cues, and direct intervention controls in one cinematic control layer.</p>
            </div>
            <div class="page-meta">
                <span class="chip chip-moderation">Moderator Access</span>
                <span class="chip chip-warning">Live Queue</span>
                <span class="chip chip-status">AI Review</span>
            </div>
        </div>
    </section>

    <section class="metric-grid">
        <?php foreach ([
            ['label' => 'Reported Posts', 'value' => $stats['reported_posts'] ?? 0, 'foot' => 'Posts in need of operator review.'],
            ['label' => 'Reported Comments', 'value' => $stats['reported_comments'] ?? 0, 'foot' => 'Comment-level moderation pressure.'],
            ['label' => 'AI Queue', 'value' => $stats['ai_queue'] ?? 0, 'foot' => 'Pending machine triage decisions.'],
            ['label' => 'Duplicates', 'value' => $stats['duplicates'] ?? 0, 'foot' => 'Potential source collisions.'],
            ['label' => 'Missing Thumbnails', 'value' => $stats['missing_thumbnails'] ?? 0, 'foot' => 'Posts lacking visual confirmation.'],
            ['label' => 'Report Spikes', 'value' => $stats['report_spike_today'] ?? 0, 'foot' => 'Today\'s volume increase.'],
        ] as $metric): ?>
            <article class="intel-stat-card">
                <div class="intel-stat-label"><?= Helpers::e($metric['label']) ?></div>
                <div class="intel-stat-value"><?= (int) $metric['value'] ?></div>
                <div class="intel-stat-foot"><?= Helpers::e($metric['foot']) ?></div>
            </article>
        <?php endforeach; ?>
    </section>

    <div class="row g-3">
        <div class="col-12 col-xxl-7">
            <section class="glass-panel section-card h-100">
                <div class="intel-panel-header">
                    <div>
                        <div class="panel-kicker">Recommendations</div>
                        <h2 class="panel-title">System-generated intervention queue</h2>
                        <p class="panel-copy">Action cards for taxonomy cleanup, media repair, report surges, and duplicate detection.</p>
                    </div>
                    <div class="page-actions">
                        <a class="btn btn-outline-light btn-sm" href="/admin/moderation">Moderation Queue</a>
                        <a class="btn btn-outline-warning btn-sm" href="/admin/reports">Reports</a>
                    </div>
                </div>

                <div class="intel-alert-list">
                    <article class="intel-alert-card">
                        <span class="alert-severity severity-medium"></span>
                        <div class="flex-grow-1">
                            <div class="intel-feed-meta">SYSTEM MAINTENANCE</div>
                            <div class="intel-feed-body"><strong><?= (int) ($stats['missing_thumbnails'] ?? 0) ?> posts missing thumbnails</strong></div>
                            <div class="panel-copy mt-1">Repair missing preview assets to restore fast visual scanning in feeds and queues.</div>
                        </div>
                    </article>
                    <article class="intel-alert-card">
                        <span class="alert-severity severity-high"></span>
                        <div class="flex-grow-1">
                            <div class="intel-feed-meta">DUPLICATE DETECTION</div>
                            <div class="intel-feed-body"><strong><?= (int) ($stats['duplicates'] ?? 0) ?> possible duplicate URLs detected</strong></div>
                            <div class="panel-copy mt-1">Merge or suppress duplicate signals before they distort engagement and moderation data.</div>
                        </div>
                    </article>
                    <article class="intel-alert-card">
                        <span class="alert-severity severity-low"></span>
                        <div class="flex-grow-1">
                            <div class="intel-feed-meta">REPORT TREND</div>
                            <div class="intel-feed-body"><strong>Report volume increased by <?= (int) ($stats['report_spike_today'] ?? 0) ?> today</strong></div>
                            <div class="panel-copy mt-1">A sudden jump usually means one source, tag, or user cluster needs immediate inspection.</div>
                        </div>
                    </article>
                </div>
            </section>
        </div>

        <div class="col-12 col-xxl-5">
            <section class="glass-panel section-card h-100">
                <div class="intel-panel-header">
                    <div>
                        <div class="panel-kicker">Stale Taxonomy</div>
                        <h2 class="panel-title">Dormant categories</h2>
                        <p class="panel-copy">Weak taxonomy branches are easy to miss until retrieval quality collapses.</p>
                    </div>
                </div>

                <div class="intel-list-stack">
                    <?php foreach (($stats['stale_categories'] ?? []) as $row): ?>
                        <article class="intel-list-item">
                            <div class="intel-list-meta">Last used <?= Helpers::e((string) $row['last_used']) ?></div>
                            <div class="intel-list-body"><strong><?= Helpers::e($row['name']) ?></strong> should be archived, merged, or reactivated.</div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-6">
            <section class="glass-panel section-card">
                <div class="intel-panel-header">
                    <div>
                        <div class="panel-kicker">Moderation Actions</div>
                        <h2 class="panel-title">Rapid response controls</h2>
                        <p class="panel-copy">Direct links to the highest-friction intervention paths.</p>
                    </div>
                </div>

                <div class="page-actions mb-3">
                    <a class="btn btn-outline-light btn-sm" href="/admin/moderation">Open queue</a>
                    <a class="btn btn-outline-warning btn-sm" href="/admin/reports">Review reports</a>
                    <a class="btn btn-outline-info btn-sm" href="/admin/cms/pages">CMS Pages</a>
                    <a class="btn btn-outline-success btn-sm" href="/admin/users-badges">Users & Badges</a>
                    <a class="btn btn-outline-info btn-sm" href="/admin/bookmarklet">Bookmarklet</a>
                </div>

                <div class="intel-form-panel">
                    <div class="panel-kicker">Manage Taxonomy</div>
                    <div class="row g-3 mt-1">
                        <div class="col-12">
                            <form method="post" action="/admin/categories" class="row g-2">
                                <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                                <div class="col-md-8">
                                    <input type="text" class="form-control form-control-sm" name="name" placeholder="New category name" required>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-outline-info btn-sm w-100" type="submit">Add Category</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-12">
                            <form method="post" action="/admin/tags" class="row g-2">
                                <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                                <div class="col-md-8">
                                    <input type="text" class="form-control form-control-sm" name="name" placeholder="New tag name" required>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-outline-info btn-sm w-100" type="submit">Add Tag</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="panel-copy mt-3">Current categories: <?= Helpers::e(implode(', ', array_map(static fn ($c) => (string) $c['name'], $stats['categories'] ?? []))) ?></div>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-6">
            <section class="glass-panel section-card">
                <div class="intel-panel-header">
                    <div>
                        <div class="panel-kicker">Manage Posts</div>
                        <h2 class="panel-title">Recent editable records</h2>
                        <p class="panel-copy">A compact edit rail for broken titles, wrong categories, and bad source entries.</p>
                    </div>
                </div>

                <div class="intel-list-stack">
                    <?php foreach (($stats['manageable_posts'] ?? []) as $post): ?>
                        <article class="intel-list-item">
                            <div class="d-flex justify-content-between gap-3 flex-wrap">
                                <div>
                                    <div class="intel-list-meta">Post #<?= (int) $post['id'] ?> by <?= Helpers::e($post['display_name']) ?></div>
                                    <div class="intel-list-body"><strong><?= Helpers::e($post['title']) ?></strong></div>
                                </div>
                                <div class="page-actions">
                                    <a class="btn btn-outline-light btn-sm" href="/admin/posts/<?= (int) $post['id'] ?>/edit">Edit</a>
                                    <form method="post" action="/admin/posts/<?= (int) $post['id'] ?>/delete">
                                        <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                                        <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </div>

    <section class="glass-panel section-card">
        <div class="intel-panel-header">
            <div>
                <div class="panel-kicker">AI Moderation</div>
                <h2 class="panel-title">Flag review console</h2>
                <p class="panel-copy">Future-proofed for risk scoring, evidence expansion, and operator overrides.</p>
            </div>
        </div>

        <div class="moderation-grid">
            <div class="evidence-list">
                <?php foreach (($stats['ai_flags'] ?? []) as $flag): ?>
                    <article class="intel-list-item">
                        <div class="d-flex justify-content-between gap-3 flex-wrap">
                            <div>
                                <div class="intel-list-meta">Flag #<?= (int) $flag['id'] ?> / <?= Helpers::e($flag['target_type']) ?> <?= (int) $flag['target_id'] ?></div>
                                <div class="intel-list-body">
                                    <strong><?= Helpers::e($flag['recommendation']) ?></strong><br>
                                    Risk <?= Helpers::e($flag['risk_level']) ?>, confidence <?= Helpers::e((string) $flag['confidence']) ?>, tags <?= Helpers::e((string) $flag['suggested_tags']) ?>
                                </div>
                            </div>
                            <div class="page-actions">
                                <?php if (($flag['review_status'] ?? 'pending') === 'pending'): ?>
                                    <form method="post" action="/admin/ai/<?= (int) $flag['id'] ?>/review">
                                        <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                                        <button class="btn btn-outline-success btn-sm" type="submit" name="decision" value="accepted">Accept</button>
                                    </form>
                                    <form method="post" action="/admin/ai/<?= (int) $flag['id'] ?>/review">
                                        <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                                        <button class="btn btn-outline-warning btn-sm" type="submit" name="decision" value="overridden">Override</button>
                                    </form>
                                    <form method="post" action="/admin/ai/<?= (int) $flag['id'] ?>/review">
                                        <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                                        <button class="btn btn-outline-danger btn-sm" type="submit" name="decision" value="rejected">Reject</button>
                                    </form>
                                <?php else: ?>
                                    <span class="chip chip-neutral">Reviewed: <?= Helpers::e((string) ($flag['admin_decision'] ?? $flag['review_status'])) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="glass-subpanel section-card">
                <div class="panel-kicker">Timeline View</div>
                <h3 class="panel-title">Operational sequence</h3>
                <div class="timeline-list mt-3">
                    <div class="timeline-item">
                        <div class="intel-list-meta">Detect</div>
                        <div class="panel-copy">AI flags new content and assigns a risk score plus recommended tags.</div>
                    </div>
                    <div class="timeline-item">
                        <div class="intel-list-meta">Correlate</div>
                        <div class="panel-copy">Operators cross-check queue volume, duplicate clusters, and report spikes.</div>
                    </div>
                    <div class="timeline-item">
                        <div class="intel-list-meta">Intervene</div>
                        <div class="panel-copy">Accept, override, reject, hide, or retag from the same control layer.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
