<?php use Intranet\Core\Helpers; ?>
<div class="page-shell">
    <section class="page-hero glass-panel">
        <div class="page-hero-grid">
            <div>
                <span class="eyebrow">Moderation Queue</span>
                <h1 class="page-title">Evidence-driven queue for flagged comments, inline controls, and future risk analysis.</h1>
                <p class="page-copy">The layout is already shaped for a fuller forensic workflow: visible actions now, expandable evidence and timeline modules later.</p>
            </div>
            <div class="page-meta">
                <span class="chip chip-moderation">Inline Controls</span>
                <span class="chip chip-warning">Future Risk Scores</span>
            </div>
        </div>
    </section>

    <div class="moderation-grid">
        <section class="glass-panel section-card">
            <div class="intel-panel-header">
                <div>
                    <div class="panel-kicker">Flagged Comments</div>
                    <h2 class="panel-title">Primary moderation rail</h2>
                    <p class="panel-copy">Hide, unhide, and tag without leaving the evidence panel.</p>
                </div>
            </div>

            <div class="evidence-list">
                <?php foreach (($queue['comments'] ?? []) as $comment): ?>
                    <article class="comment-card">
                        <div class="comment-header">
                            <div>
                                <div class="comment-author">#<?= (int) $comment['id'] ?> <?= Helpers::e($comment['display_name']) ?></div>
                                <div class="intel-feed-meta"><?= Helpers::e($comment['post_title']) ?></div>
                            </div>
                            <div class="page-actions">
                                <span class="chip chip-warning">Review</span>
                                <span class="chip chip-moderation">Evidence</span>
                            </div>
                        </div>

                        <div class="comment-body"><?= nl2br(Helpers::e($comment['body'])) ?></div>

                        <div class="comment-actions mt-3">
                            <form method="post" action="/admin/comments/<?= (int) $comment['id'] ?>/hide">
                                <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                                <button class="btn btn-outline-warning btn-sm" type="submit">Hide</button>
                            </form>
                            <form method="post" action="/admin/comments/<?= (int) $comment['id'] ?>/unhide">
                                <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                                <button class="btn btn-outline-success btn-sm" type="submit">Unhide</button>
                            </form>
                            <form method="post" action="/admin/comments/<?= (int) $comment['id'] ?>/tag" class="row g-2 flex-grow-1">
                                <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                                <div class="col-md-8">
                                    <select name="tag_id" class="form-select form-select-sm">
                                        <?php foreach (($commentTags ?? []) as $tag): ?>
                                            <option value="<?= (int) $tag['id'] ?>"><?= Helpers::e($tag['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-primary btn-sm w-100" type="submit">Apply Tag</button>
                                </div>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>

                <?php if (($queue['comments'] ?? []) === []): ?>
                    <div class="empty-state">No flagged comments in queue.</div>
                <?php endif; ?>
            </div>
        </section>

        <aside class="glass-panel section-card">
            <div class="panel-kicker">Future Proofing</div>
            <h2 class="panel-title">Investigation sidecar</h2>
            <p class="panel-copy">Reserved layout space for the higher-end forensic tooling you want next.</p>

            <div class="timeline-list mt-4">
                <div class="timeline-item">
                    <div class="intel-list-meta">Risk Score</div>
                    <div class="panel-copy">Display per-comment severity and confidence once scoring is wired in.</div>
                </div>
                <div class="timeline-item">
                    <div class="intel-list-meta">User Investigation</div>
                    <div class="panel-copy">Attach account behavior, source overlap, and prior moderation events.</div>
                </div>
                <div class="timeline-item">
                    <div class="intel-list-meta">IP Tracking</div>
                    <div class="panel-copy">Expose origin clusters and suspicious access patterns in a side-by-side panel.</div>
                </div>
                <div class="timeline-item">
                    <div class="intel-list-meta">Evidence Drawer</div>
                    <div class="panel-copy">Expandable records can hold raw reports, duplicate content, and timeline snapshots.</div>
                </div>
            </div>
        </aside>
    </div>
</div>
