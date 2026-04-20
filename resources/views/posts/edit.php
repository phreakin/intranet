<?php use Intranet\Core\Helpers; ?>
<div class="glass-panel p-4 col-12 col-xl-8 mx-auto">
    <h1 class="h4">Edit Post</h1>
    <form method="post" action="/post/<?= (int) $post['id'] ?>/edit" class="vstack gap-3">
        <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
        <input type="text" class="form-control" name="title" value="<?= Helpers::e($post['title']) ?>" required>
        <textarea class="form-control" name="description" rows="4"><?= Helpers::e($post['description']) ?></textarea>
        <select class="form-select" name="category_id">
            <option value="">Uncategorized</option>
            <?php foreach (($categories ?? []) as $category): ?>
                <option value="<?= (int) $category['id'] ?>" <?= (int) $post['category_id'] === (int) $category['id'] ? 'selected' : '' ?>><?= Helpers::e($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" class="form-control" name="tags" value="<?= Helpers::e((string) $post['tags']) ?>" placeholder="tag1, tag2">
        <button class="btn btn-primary" type="submit">Save Changes</button>
    </form>
</div>
