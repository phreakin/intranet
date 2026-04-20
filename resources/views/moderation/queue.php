<?php use Intranet\Core\Helpers; ?>
<div class="glass-panel p-4">
    <h1 class="h4">Moderation Queue</h1>
    <h2 class="h6 text-uppercase mt-4">Comment actions</h2>
    <?php foreach (($queue['comments'] ?? []) as $comment): ?>
        <div class="comment-card mb-2">
            <div class="small text-secondary mb-2">#<?= (int) $comment['id'] ?> · <?= Helpers::e($comment['display_name']) ?> · <?= Helpers::e($comment['post_title']) ?></div>
            <div class="mb-2"><?= nl2br(Helpers::e($comment['body'])) ?></div>
            <div class="d-flex gap-2 flex-wrap">
                <form method="post" action="/admin/comments/<?= (int) $comment['id'] ?>/hide">
                    <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                    <button class="btn btn-sm btn-outline-warning" type="submit">Hide</button>
                </form>
                <form method="post" action="/admin/comments/<?= (int) $comment['id'] ?>/unhide">
                    <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                    <button class="btn btn-sm btn-outline-success" type="submit">Unhide</button>
                </form>
                <form method="post" action="/admin/comments/<?= (int) $comment['id'] ?>/tag" class="d-flex gap-1">
                    <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                    <select name="tag_id" class="form-select form-select-sm">
                        <?php foreach (($commentTags ?? []) as $tag): ?>
                            <option value="<?= (int) $tag['id'] ?>"><?= Helpers::e($tag['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-sm btn-primary" type="submit">Tag</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>
