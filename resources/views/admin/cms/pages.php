<?php use Intranet\Core\Helpers; ?>
<div class="glass-panel p-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">CMS Pages</h1>
            <p class="text-secondary small mb-0">Admin-authored content rendered at <code>/pages/{slug}</code> or <code>/articles/{slug}</code>.</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-sm btn-outline-light" href="/admin/cms/blocks">Blocks</a>
            <a class="btn btn-sm btn-outline-light" href="/admin/cms/menus">Menus</a>
            <a class="btn btn-sm btn-primary" href="/admin/cms/pages/new">New Page</a>
        </div>
    </div>

    <?php if (empty($pages)): ?>
        <div class="small text-secondary">No pages yet. <a href="/admin/cms/pages/new" class="text-info">Create one</a>.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                        <tr>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Slug</th>
                        <th>Status</th>
                        <th>Layout</th>
                        <th>Published</th>
                        <th>Updated</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $page): ?>
                        <tr>
                            <td><span class="badge chip-category"><?= Helpers::e((string) ($page['content_type'] ?? 'page')) ?></span></td>
                            <td><?= Helpers::e($page['title']) ?></td>
                            <?php $contentPath = (($page['content_type'] ?? 'page') === 'article' ? '/articles/' : '/pages/') . Helpers::e($page['slug']); ?>
                            <td><a class="text-info text-decoration-none" href="<?= $contentPath ?>"><?= Helpers::e($contentPath) ?></a></td>
                            <td><span class="badge chip-status"><?= Helpers::e($page['status']) ?></span></td>
                            <td><?= Helpers::e($page['layout']) ?></td>
                            <td><?= Helpers::e((string) ($page['published_at'] ?? '')) ?></td>
                            <td><?= Helpers::e((string) $page['updated_at']) ?></td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a class="btn btn-sm btn-outline-info" href="/admin/cms/pages/<?= (int) $page['id'] ?>/edit">Edit</a>
                                    <form method="post" action="/admin/cms/pages/<?= (int) $page['id'] ?>/delete" onsubmit="return confirm('Delete this page?');">
                                        <input type="hidden" name="_csrf" value="<?= Helpers::e($csrf) ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
