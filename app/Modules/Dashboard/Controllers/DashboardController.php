<?php

declare(strict_types=1);

namespace Intranet\Modules\Dashboard\Controllers;

use Intranet\Core\View;
use Intranet\Modules\Shared\Repositories\PostRepository;

final class DashboardController
{
    public function index(): void
    {
        View::render('dashboard/index', [
            'title' => 'Newest Intelligence Feed',
            'posts' => (new PostRepository())->feed(60),
        ]);
    }
}
