<?php

use Intranet\Core\Helpers;

$statusTags = array_values(array_filter(array_map('trim', explode(',', (string) ($post['status_tags'] ?? '')))));
$postTags = array_values(array_filter(array_map('trim', explode(',', (string) ($post['tags'] ?? '')))));
$slugify = static function (?string $value): string {
    $value = strtolower(trim((string) $value));
    $value = preg_replace('/[^a-z0-9]+/i', '-', $value ?? '') ?? '';
    return trim($value, '-') ?: 'unknown';
};
?>
<div class="page-shell">
    <section class="page-hero glass-panel">
        <div class="page-hero-grid">
            <div>
                <span class="eyebrow">Post Detail</span>
                <h1 class="page-title"><?= Helpers::e($post['title']) ?></h1>
                <p class="page-copy"><?= Helpers::e($post['description'] ?? 'No description captured for this signal.') ?></p>
            </div>
            <div class="page-meta flex flex-wrap gap-2">
                <?php foreach ($statusTags as $chip): ?>
                    <span class="chip chip-status"><?= Helpers::e($chip) ?></span>
                <?php endforeach; ?>
                <?php if (!empty($post['category_name'])): ?>
                    <a class="chip chip-category" href="/category/<?= Helpers::e($slugify($post['category_name'])) ?>"><?= Helpers::e($post['category_name']) ?></a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
        <div class="xl:col-span-8">
            <div class="flex flex-col gap-6">
                <section class="glass-panel intel-detail-hero">
                    <?php if (!empty($post['thumbnail_url'])): ?>
                        <img src="<?= Helpers::e($post['thumbnail_url']) ?>" alt="" class="intel-detail-image">
                    <?php endif; ?>

                    <div class="panel-kicker">Content Record</div>
                    <div class="intel-prose mt-3">
                        <?= nl2br(Helpers::e((string) ($post['description'] ?? 'No description provided.'))) ?>
                    </div>

                    <div class="intel-divider"></div>

                    <div class="info-list">
                        <div class="info-row">
                            <div class="info-label">Source URL</div>
                            <div class="info-value"><a href="<?= Helpers::e($post['url']) ?>" target="_blank" rel="noreferrer"><?= Helpers::e($post['url']) ?></a></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Site</div>
                            <div class="info-value"><?= Helpers::e($post['site_name'] ?? 'Unknown source') ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Author</div>
                            <div class="info-value"><?= Helpers::e($post['author_name'] ?? 'Unknown author') ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Published</div>
                            <div class="info-value"><?= Helpers::e((string) ($post['published_at'] ?? 'Unspecified')) ?></div>
                        </div>
                    </div>
                </section>

                <section class="glass-panel section-card">
                    <div class="intel-panel-header">
                        <div>
                            <div class="panel-kicker">Comments</div>
                            <h2 class="panel-title">Discussion log</h2>
                            <p class="panel-copy">Threaded evidence notes, moderation hooks, and report actions stay inline.</p>
                        </div>
                    </div>

                    <form method="post" action="/post/<?= (int) $post['id'] ?>/comment" class="intel-form-panel">
                        <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="comment-body">Add Comment</label>
                        <textarea id="comment-body" class="search-input min-h-[120px]" name="body" rows="4" placeholder="Add investigative context, moderation notes, or correlation details." required></textarea>
                        <button class="mt-3 inline-flex items-center justify-center rounded-lg bg-cyan-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-cyan-400" type="submit">Post Comment</button>
                    </form>

                    <div class="intel-comment-list mt-4">
                        <?php foreach (($comments ?? []) as $comment): ?>
                            <article class="comment-card rounded-xl border border-white/10 bg-white/5 p-4">
                                <div class="comment-header flex items-start justify-between gap-4">
                                    <div>
                                        <div class="comment-author text-sm font-semibold text-white"><?= Helpers::e($comment['display_name']) ?></div>
                                        <div class="intel-feed-meta text-xs text-slate-400"><?= Helpers::e((string) $comment['created_at']) ?></div>
                                    </div>
                                    <?php if (!empty($comment['moderation_tags'])): ?>
                                        <span class="chip chip-moderation"><?= Helpers::e($comment['moderation_tags']) ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="comment-body mt-3 text-sm leading-6 text-slate-200"><?= nl2br(Helpers::e($comment['body'])) ?></div>

                                <div class="comment-actions mt-3 flex flex-wrap gap-2">
                                    <span class="chip chip-neutral">Reply Ready</span>
                                    <span class="chip chip-warning">Reportable</span>
                                </div>

                                <form method="post" action="/post/<?= (int) $post['id'] ?>/comments/<?= (int) $comment['id'] ?>/report" class="mt-3 grid grid-cols-1 gap-2 md:grid-cols-12">
                                    <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                                    <div class="md:col-span-8">
                                        <input type="text" class="search-input" name="reason" placeholder="Report reason">
                                    </div>
                                    <div class="md:col-span-4">
                                        <button class="inline-flex w-full items-center justify-center rounded-lg border border-yellow-500/30 bg-yellow-500/10 px-3 py-2 text-sm font-medium text-yellow-100 transition hover:bg-yellow-500/20" type="submit">Report Comment</button>
                                    </div>
                                </form>
                            </article>
                        <?php endforeach; ?>

                        <?php if (($comments ?? []) === []): ?>
                            <div class="empty-state rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-400">No comments yet. The first operator note starts the evidence trail.</div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </div>

        <aside class="xl:col-span-4">
            <div class="flex flex-col gap-4">
                <section class="glass-panel section-card">
                    <div class="panel-kicker">Stats</div>
                    <h2 class="panel-title">Signal telemetry</h2>
                    <div class="stats-strip mt-3 flex flex-wrap gap-2">
                        <span class="stat-pill">Likes <?= (int) $post['like_count'] ?></span>
                        <span class="stat-pill">Dislikes <?= (int) $post['dislike_count'] ?></span>
                        <span class="stat-pill">Comments <?= (int) $post['comment_count'] ?></span>
                        <span class="stat-pill">Favorites <?= (int) $post['favorite_count'] ?></span>
                        <span class="stat-pill">Bookmarks <?= (int) $post['bookmark_count'] ?></span>
                        <span class="stat-pill">Reports <?= (int) $post['report_count'] ?></span>
                    </div>
                </section>

                <section class="glass-panel section-card">
                    <div class="panel-kicker">Metadata</div>
                    <h2 class="panel-title">Classification</h2>
                    <div class="page-actions mt-3 flex flex-wrap gap-2">
                        <?php if (!empty($post['category_name'])): ?>
                            <a class="chip chip-category" href="/category/<?= Helpers::e($slugify($post['category_name'])) ?>"><?= Helpers::e($post['category_name']) ?></a>
                        <?php endif; ?>
                        <?php foreach ($postTags as $tag): ?>
                            <a class="chip chip-tag" href="/tag/<?= Helpers::e($slugify($tag)) ?>">#<?= Helpers::e($tag) ?></a>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="glass-panel section-card">
                    <div class="panel-kicker">Actions</div>
                    <h2 class="panel-title">Operator controls</h2>
                    <div class="action-cluster mt-3 flex flex-col gap-3">
                        <form method="post" action="/post/<?= (int) $post['id'] ?>/like">
                            <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                            <button class="inline-flex w-full items-center justify-center rounded-lg border border-green-500/30 bg-green-500/10 px-3 py-2 text-sm font-medium text-green-100 transition hover:bg-green-500/20" type="submit">Like (<?= (int) $post['like_count'] ?>)</button>
                        </form>
                        <form method="post" action="/post/<?= (int) $post['id'] ?>/bookmark">
                            <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                            <button class="inline-flex w-full items-center justify-center rounded-lg border border-cyan-500/30 bg-cyan-500/10 px-3 py-2 text-sm font-medium text-cyan-100 transition hover:bg-cyan-500/20" type="submit">Bookmark (<?= (int) $post['bookmark_count'] ?>)</button>
                        </form>
                        <form method="post" action="/post/<?= (int) $post['id'] ?>/favorite">
                            <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                            <button class="inline-flex w-full items-center justify-center rounded-lg border border-yellow-500/30 bg-yellow-500/10 px-3 py-2 text-sm font-medium text-yellow-100 transition hover:bg-yellow-500/20" type="submit">Favorite (<?= (int) $post['favorite_count'] ?>)</button>
                        </form>
                        <form method="post" action="/post/<?= (int) $post['id'] ?>/dislike">
                            <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                            <button class="inline-flex w-full items-center justify-center rounded-lg border border-red-500/30 bg-red-500/10 px-3 py-2 text-sm font-medium text-red-100 transition hover:bg-red-500/20" type="submit">Dislike (<?= (int) $post['dislike_count'] ?>)</button>
                        </form>
                        <button class="copy-share inline-flex items-center justify-center rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm font-medium text-slate-100 transition hover:border-cyan-400/40 hover:bg-cyan-400/10" type="button" data-url="<?= Helpers::e((string) getenv('APP_URL') . '/post/' . (int) $post['id']) ?>">Copy Share Link</button>
                        <?php if (!empty($currentUser) && (int) $currentUser['id'] === (int) $post['user_id']): ?>
                            <a class="inline-flex items-center justify-center rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm font-medium text-slate-100 transition hover:border-violet-400/40 hover:bg-violet-400/10" href="/post/<?= (int) $post['id'] ?>/edit">Edit Post</a>
                        <?php endif; ?>
                        <form method="post" action="/post/<?= (int) $post['id'] ?>/report" class="rounded-xl border border-white/10 bg-white/5 p-3">
                            <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                            <label class="mb-2 block text-sm font-medium text-slate-200" for="report-reason">Report reason</label>
                            <input id="report-reason" type="text" class="search-input" name="reason" placeholder="Needs review">
                            <button class="mt-3 inline-flex w-full items-center justify-center rounded-lg border border-yellow-500/30 bg-yellow-500/10 px-3 py-2 text-sm font-medium text-yellow-100 transition hover:bg-yellow-500/20" type="submit">Report Signal</button>
                        </form>
                    </div>
                </section>
            </div>
        </aside>
    </div>
</div>
