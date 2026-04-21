<?php

declare(strict_types=1);

namespace Intranet\Modules\Cms\Controllers;

use Intranet\Core\View;
use Intranet\Modules\Cms\Repositories\CmsRepository;

final class PageController
{
    public function show(string $slug): void
    {
        $repo = new CmsRepository();
        $page = $repo->publishedPageBySlug($slug);
        if ($page === null) {
            http_response_code(404);
            View::render('errors/not_found', [
                'title' => 'Page not found',
                'message' => 'That page either does not exist or is not yet published.',
            ]);
            return;
        }

        View::render('cms/page', [
            'title' => (string) $page['title'],
            'page' => $page,
            'sidebar' => $repo->blockByKey('cms.sidebar.primary'),
        ]);
    }
}
