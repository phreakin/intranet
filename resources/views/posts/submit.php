<?php use Intranet\Core\Helpers; ?>
<div class="glass-panel p-4 col-12 col-xl-8 mx-auto">
    <h1 class="h3 mb-3">Submit Intelligence Link</h1>
    <?php if (!empty($error)): ?><div class="alert alert-danger"><?= Helpers::e($error) ?></div><?php endif; ?>
    <?php if (!empty($metaError)): ?><div class="alert alert-warning"><?= Helpers::e($metaError) ?></div><?php endif; ?>
    <form method="get" action="/submit" class="mb-3">
        <label class="form-label">Source URL</label>
        <div class="input-group">
            <input class="form-control" type="url" name="url" placeholder="https://example.com/article" value="<?= Helpers::e((string) (($prefill['url'] ?? ''))) ?>" required>
            <button type="submit" class="btn btn-outline-info">Fetch Metadata</button>
        </div>
    </form>
    <form method="post" action="/submit" class="vstack gap-3">
        <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
        <input class="form-control" type="url" name="url" placeholder="https://example.com/article" value="<?= Helpers::e((string) (($prefill['url'] ?? ''))) ?>" required>
        <div class="row g-2">
            <div class="col-md-8">
                <label class="form-label">Title</label>
                <input class="form-control" type="text" name="title" value="<?= Helpers::e((string) (($prefill['title'] ?? ''))) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Publish date</label>
                <input class="form-control" type="text" name="published_at" value="<?= Helpers::e((string) (($prefill['published_at'] ?? ''))) ?>" placeholder="2026-01-31 09:15:00">
            </div>
        </div>
        <div>
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="3"><?= Helpers::e((string) (($prefill['description'] ?? ''))) ?></textarea>
        </div>
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Thumbnail URL</label>
                <input class="form-control" type="url" name="thumbnail_url" value="<?= Helpers::e((string) (($prefill['thumbnail_url'] ?? ''))) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Canonical URL</label>
                <input class="form-control" type="url" name="canonical_url" value="<?= Helpers::e((string) (($prefill['canonical_url'] ?? ''))) ?>">
            </div>
        </div>
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Site name</label>
                <input class="form-control" type="text" name="site_name" value="<?= Helpers::e((string) (($prefill['site_name'] ?? ''))) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Author</label>
                <input class="form-control" type="text" name="author_name" value="<?= Helpers::e((string) (($prefill['author_name'] ?? ''))) ?>">
            </div>
        </div>
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
            <input class="form-control" type="text" name="tags" value="<?= Helpers::e((string) (($prefill['tags'] ?? ''))) ?>" placeholder="security, tutorial, reference">
        </div>
        <button type="submit" class="btn btn-primary">Ingest Link</button>
    </form>
</div>
