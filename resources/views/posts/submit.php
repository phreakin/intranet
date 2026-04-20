<?php use Intranet\Core\Helpers; ?>
<div class="glass-panel p-4 col-12 col-xl-8 mx-auto">
    <h1 class="h3 mb-3">Submit Intelligence Link</h1>
    <?php if (!empty($error)): ?><div class="alert alert-danger"><?= Helpers::e($error) ?></div><?php endif; ?>
    <form method="post" action="/submit" class="vstack gap-3">
        <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
        <input class="form-control" type="url" name="url" placeholder="https://example.com/article" required>
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Category</label>
                <select class="form-select" name="category_id">
                    <option value="">Uncategorized</option>
                    <?php foreach (($categories ?? []) as $category): ?>
                        <option value="<?= (int) $category['id'] ?>"><?= Helpers::e($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Or create category</label>
                <input class="form-control" type="text" name="new_category" placeholder="e.g. Incident Response">
            </div>
        </div>
        <div>
            <label class="form-label">Manual tags (comma-separated, used when source has no keywords)</label>
            <input class="form-control" type="text" name="tags" placeholder="security, tutorial, reference">
        </div>
        <button type="submit" class="btn btn-primary">Ingest Link</button>
    </form>
</div>
