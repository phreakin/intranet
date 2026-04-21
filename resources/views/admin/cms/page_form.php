<?php use Intranet\Core\Helpers; ?>
<?php
$isEdit = is_array($page ?? null);
$action = $isEdit ? '/admin/cms/pages/' . (int) $page['id'] . '/edit' : '/admin/cms/pages';
$contentType = (string) ($page['content_type'] ?? 'page');
?>
<div class="glass-panel p-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
        <div>
            <h1 class="h4 mb-1"><?= $isEdit ? 'Edit CMS Content' : 'New CMS Content' ?></h1>
            <p class="text-secondary small mb-0">Create internal pages and articles without leaving the control room.</p>
        </div>
        <a class="btn btn-sm btn-outline-light" href="/admin/cms/pages">Back to CMS Pages</a>
    </div>

    <form method="post" action="<?= Helpers::e($action) ?>" class="row g-3">
        <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">

        <div class="col-md-4">
            <label class="form-label small text-secondary">Content Type</label>
            <select class="form-select" name="content_type">
                <option value="page" <?= $contentType === 'page' ? 'selected' : '' ?>>Page</option>
                <option value="article" <?= $contentType === 'article' ? 'selected' : '' ?>>Article</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label small text-secondary">Status</label>
            <select class="form-select" name="status">
                <?php foreach (['draft', 'published', 'archived'] as $status): ?>
                    <option value="<?= Helpers::e($status) ?>" <?= (($page['status'] ?? 'draft') === $status) ? 'selected' : '' ?>>
                        <?= Helpers::e(ucfirst($status)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label small text-secondary">Layout</label>
            <input class="form-control" type="text" name="layout" value="<?= Helpers::e((string) ($page['layout'] ?? 'default')) ?>" placeholder="default">
        </div>

        <div class="col-md-8">
            <label class="form-label small text-secondary">Title</label>
            <input class="form-control" type="text" name="title" value="<?= Helpers::e((string) ($page['title'] ?? '')) ?>" required>
        </div>

        <div class="col-md-4">
            <label class="form-label small text-secondary">Slug</label>
            <input class="form-control" type="text" name="slug" value="<?= Helpers::e((string) ($page['slug'] ?? '')) ?>" placeholder="leave blank to auto-generate">
        </div>

        <div class="col-12">
            <label class="form-label small text-secondary">Summary</label>
            <textarea class="form-control" rows="3" name="summary" placeholder="Short teaser or excerpt"><?= Helpers::e((string) ($page['summary'] ?? '')) ?></textarea>
        </div>

        <div class="col-12">
            <label class="form-label small text-secondary">Body</label>
            <textarea class="form-control font-monospace" rows="16" name="body" required placeholder="HTML or lightweight formatted content"><?= Helpers::e((string) ($page['body'] ?? '')) ?></textarea>
        </div>

        <div class="col-12 d-flex gap-2 justify-content-end">
            <?php if ($isEdit && !empty($page['slug'])): ?>
                <?php $previewPath = (($page['content_type'] ?? 'page') === 'article' ? '/articles/' : '/pages/') . $page['slug']; ?>
                <a class="btn btn-outline-info" href="<?= Helpers::e($previewPath) ?>" target="_blank" rel="noreferrer">Preview</a>
            <?php endif; ?>
            <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Save Changes' : 'Create Content' ?></button>
        </div>
    </form>
</div>
