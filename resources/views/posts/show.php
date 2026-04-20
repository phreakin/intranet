<?php use Intranet\Core\Helpers; ?>
<div class="row g-3">
    <div class="col-lg-8">
        <div class="glass-panel p-4 mb-3">
            <h1 class="h3"><?= Helpers::e($post['title']) ?></h1>
            <p class="text-secondary"><?= Helpers::e($post['description']) ?></p>
            <a class="btn btn-outline-light btn-sm" href="<?= Helpers::e($post['url']) ?>" target="_blank" rel="noreferrer">Open Source</a>
            <?php if (!empty($currentUser) && (int) $currentUser['id'] === (int) $post['user_id']): ?>
                <a class="btn btn-outline-info btn-sm" href="/post/<?= (int) $post['id'] ?>/edit">Edit</a>
            <?php endif; ?>
            <div class="d-flex gap-1 mt-3 flex-wrap">
                <?php foreach (array_filter(explode(',', (string) ($post['status_tags'] ?? ''))) as $chip): ?>
                    <span class="badge chip-status"><?= Helpers::e($chip) ?></span>
                <?php endforeach; ?>
            </div>
            <div class="d-flex gap-2 mt-3">
                <form method="post" action="/post/<?= (int) $post['id'] ?>/like">
                    <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                    <button class="btn btn-sm btn-outline-success" type="submit">Like (<?= (int) $post['like_count'] ?>)</button>
                </form>
                <form method="post" action="/post/<?= (int) $post['id'] ?>/dislike">
                    <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                    <button class="btn btn-sm btn-outline-danger" type="submit">Dislike (<?= (int) $post['dislike_count'] ?>)</button>
                </form>
                <form method="post" action="/post/<?= (int) $post['id'] ?>/favorite">
                    <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                    <button class="btn btn-sm btn-outline-warning" type="submit">Favorite (<?= (int) $post['favorite_count'] ?>)</button>
                </form>
                <form method="post" action="/post/<?= (int) $post['id'] ?>/bookmark">
                    <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                    <button class="btn btn-sm btn-outline-info" type="submit">Bookmark (<?= (int) $post['bookmark_count'] ?>)</button>
                </form>
                <button class="btn btn-sm btn-outline-light copy-share" type="button" data-url="<?= Helpers::e((string) getenv('APP_URL') . '/post/' . (int) $post['id']) ?>">Share</button>
                <form method="post" action="/post/<?= (int) $post['id'] ?>/report" class="d-flex gap-1">
                    <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                    <input type="text" class="form-control form-control-sm" name="reason" placeholder="Report reason">
                    <button class="btn btn-sm btn-outline-warning" type="submit">Report (<?= (int) $post['report_count'] ?>)</button>
                </form>
            </div>
        </div>
        <div class="glass-panel p-4">
            <h2 class="h5">Comments</h2>
            <form method="post" action="/post/<?= (int) $post['id'] ?>/comment" class="mb-3">
                <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                <textarea class="form-control mb-2" name="body" rows="3" placeholder="Add intelligence context..." required></textarea>
                <button class="btn btn-primary btn-sm" type="submit">Post Comment</button>
            </form>
            <?php foreach (($comments ?? []) as $comment): ?>
                <div class="comment-card mb-2">
                    <div class="small text-secondary mb-1"><?= Helpers::e($comment['display_name']) ?> · <?= Helpers::e((string) $comment['created_at']) ?></div>
                <div><?= nl2br(Helpers::e($comment['body'])) ?></div>
                <?php if (!empty($comment['moderation_tags'])): ?><div class="small text-info mt-1">Tags: <?= Helpers::e($comment['moderation_tags']) ?></div><?php endif; ?>
                <form method="post" action="/post/<?= (int) $post['id'] ?>/comments/<?= (int) $comment['id'] ?>/report" class="mt-2 d-flex gap-1">
                    <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                    <input type="text" class="form-control form-control-sm" name="reason" placeholder="Report comment">
                    <button class="btn btn-sm btn-outline-warning" type="submit">Report comment</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="glass-panel p-3">
            <h3 class="h6 text-uppercase text-secondary">Metadata</h3>
            <div class="small vstack gap-2">
                <span><strong>Site:</strong> <?= Helpers::e($post['site_name']) ?></span>
                <span><strong>Author:</strong> <?= Helpers::e($post['author_name']) ?></span>
                <span><strong>Published:</strong> <?= Helpers::e((string) $post['published_at']) ?></span>
                <span><strong>Category:</strong> <?= Helpers::e($post['category_name']) ?></span>
                <span><strong>Tags:</strong> <?= Helpers::e((string) $post['tags']) ?></span>
            </div>
        </div>
    </div>
</div>
