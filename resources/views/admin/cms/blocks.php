<?php use Intranet\Core\Helpers; ?>
<div class="glass-panel p-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
        <div>
            <h1 class="h4 mb-1">CMS Blocks</h1>
            <p class="text-secondary small mb-0">Manage reusable sidebar and content fragments used by the CMS surfaces.</p>
        </div>
        <a class="btn btn-sm btn-outline-light" href="/admin/cms/pages">Back to CMS Pages</a>
    </div>

    <form method="post" action="/admin/cms/blocks" class="glass-panel p-3 mb-4">
        <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small text-secondary">Block Key</label>
                <input class="form-control" type="text" name="block_key" placeholder="cms.sidebar.primary" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-secondary">Label</label>
                <input class="form-control" type="text" name="label" placeholder="Primary Sidebar" required>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="block-active" name="is_active" value="1" checked>
                    <label class="form-check-label" for="block-active">Active</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label small text-secondary">Content</label>
                <textarea class="form-control font-monospace" rows="8" name="content" required placeholder="Trusted HTML block content"></textarea>
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-primary" type="submit">Save Block</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr>
                    <th>Key</th>
                    <th>Label</th>
                    <th>Active</th>
                    <th>Updated</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($blocks ?? []) as $block): ?>
                    <tr>
                        <td><code><?= Helpers::e((string) $block['block_key']) ?></code></td>
                        <td><?= Helpers::e((string) $block['label']) ?></td>
                        <td><?= !empty($block['is_active']) ? 'Yes' : 'No' ?></td>
                        <td><?= Helpers::e((string) $block['updated_at']) ?></td>
                        <td class="text-end">
                            <form method="post" action="/admin/cms/blocks/<?= (int) $block['id'] ?>/delete" onsubmit="return confirm('Delete this block?');">
                                <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
