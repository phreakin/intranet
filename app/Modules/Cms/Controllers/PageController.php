<?php

declare(strict_types=1);

namespace Intranet\Modules\Cms\Controllers;

use Intranet\Core\View;
use Intranet\Modules\Cms\Repositories\CmsRepository;

final class PageController
{
    public function showPage(string $slug): void
    {
        $this->renderContent($slug, 'page');
    }

    public function showArticle(string $slug): void
    {
        $this->renderContent($slug, 'article');
    }

    public function showLegacyPage(string $slug): void
    {
        $this->renderContent($slug, 'page');
    }

    private function renderContent(string $slug, string $contentType): void
    {
        $repo = new CmsRepository();
        $page = $repo->publishedContentBySlug($slug, $contentType);
        if ($page === null) {
            http_response_code(404);
            View::render('errors/not_found', [
                'title' => ucfirst($contentType) . ' not found',
                'message' => 'That ' . $contentType . ' either does not exist or is not yet published.',
            ]);
            return;
        }
        View::render('cms/page', [
            'title' => (string) $page['title'],
            'page' => $page,
            'contentType' => $contentType,
            'sidebar' => $repo->blockByKey('cms.sidebar.primary'),
        ]);
    }
}
