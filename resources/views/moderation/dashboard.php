<div class="moderation-shell">
    <div class="moderation-toolbar glass-panel">
        <div class="moderation-toolbar-left">
            <h1 class="moderation-title">Moderation Command Center</h1>
            <p class="moderation-subtitle">
                Report review, action history, and operational moderation visibility.
            </p>
        </div>

        <div class="moderation-toolbar-right">
            <button
                type="button"
                class="btn btn-sm btn-outline-info"
                data-moderation-refresh-all>
                Refresh
            </button>

            <button
                type="button"
                class="btn btn-sm btn-outline-secondary"
                data-moderation-autorefresh-toggle>
                Auto Refresh: Off
            </button>
        </div>
    </div>

    <div class="moderation-grid">
        <section
            class="moderation-widget glass-panel moderation-span-full"
            data-moderation-widget-container
            data-moderation-widget-name="quick-stats">
            <div class="widget-header">
                <div>
                    <h2 class="widget-title">Moderation Overview</h2>
                    <span class="widget-meta">High-level queue and activity metrics</span>
                </div>
                <div class="widget-actions">
                    <button class="widget-action" data-moderation-widget-refresh="quick-stats">↻</button>
                    <button class="widget-action" data-moderation-widget-collapse>–</button>
                </div>
            </div>
            <div class="widget-body" id="moderation-widget-quick-stats">
                <?php $stats = $quickStats; require __DIR__ . '/widgets/quick-stats.php'; ?>
            </div>
        </section>

        <section
            class="moderation-widget glass-panel"
            data-moderation-widget-container
            data-moderation-widget-name="reported-posts">
            <div class="widget-header">
                <div>
                    <h2 class="widget-title">Reported Posts</h2>
                    <span class="widget-meta">Post-level moderation queue</span>
                </div>
                <div class="widget-actions">
                    <button class="widget-action" data-moderation-widget-refresh="reported-posts">↻</button>
                    <button class="widget-action" data-moderation-widget-collapse>–</button>
                </div>
            </div>
            <div class="widget-body" id="moderation-widget-reported-posts">
                <?php $posts = $reportedPosts; require __DIR__ . '/widgets/reported-posts.php'; ?>
            </div>
        </section>

        <section
            class="moderation-widget glass-panel"
            data-moderation-widget-container
            data-moderation-widget-name="reported-comments">
            <div class="widget-header">
                <div>
                    <h2 class="widget-title">Reported Comments</h2>
                    <span class="widget-meta">Comment-level moderation queue</span>
                </div>
                <div class="widget-actions">
                    <button class="widget-action" data-moderation-widget-refresh="reported-comments">↻</button>
                    <button class="widget-action" data-moderation-widget-collapse>–</button>
                </div>
            </div>
            <div class="widget-body" id="moderation-widget-reported-comments">
                <?php $comments = $reportedComments; require __DIR__ . '/widgets/reported-comments.php'; ?>
            </div>
        </section>

        <section
            class="moderation-widget glass-panel moderation-span-full"
            data-moderation-widget-container
            data-moderation-widget-name="recent-actions">
            <div class="widget-header">
                <div>
                    <h2 class="widget-title">Recent Moderation Actions</h2>
                    <span class="widget-meta">Latest logged interventions and reviews</span>
                </div>
                <div class="widget-actions">
                    <button class="widget-action" data-moderation-widget-refresh="recent-actions">↻</button>
                    <button class="widget-action" data-moderation-widget-collapse>–</button>
                </div>
            </div>
            <div class="widget-body" id="moderation-widget-recent-actions">
                <?php $actions = $recentActions; require __DIR__ . '/widgets/recent-actions.php'; ?>
            </div>
        </section>
    </div>
</div>

<link rel="stylesheet" href="/assets/css/moderation.css">
<script src="/assets/js/moderation.js"></script>