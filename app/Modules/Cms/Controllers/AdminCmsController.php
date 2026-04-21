<?php

declare(strict_types=1);

namespace Intranet\Modules\Cms\Controllers;

use Intranet\Core\Auth;
use Intranet\Core\Csrf;
use Intranet\Core\Helpers;
use Intranet\Core\View;
use Intranet\Modules\Cms\Repositories\CmsRepository;

final class AdminCmsController
{
    // -------- Pages -----------------------------------------------------

    public function pagesIndex(): void
    {
        $this->guardManage();
        View::render('admin/cms/pages', [
            'title' => 'CMS Pages',
            'pages' => (new CmsRepository())->allPages(),
            'csrf' => Csrf::token(),
        ]);
    }

    public function pageNewForm(): void
    {
        $this->guardManage();
        View::render('admin/cms/page_form', [
            'title' => 'New CMS Page',
            'page' => null,
            'csrf' => Csrf::token(),
        ]);
    }

    public function pageCreate(): void
    {
        $this->guardManage();
        $this->checkCsrf();
        $data = $this->pageInput();
        $authorId = (int) Auth::user()['id'];
        $id = (new CmsRepository())->createPage($data, $authorId);
        Helpers::redirect('/admin/cms/pages/' . $id . '/edit');
    }

    public function pageEditForm(int $id): void
    {
        $this->guardManage();
        $page = (new CmsRepository())->pageById($id);
        if ($page === null) {
            http_response_code(404);
            exit('Page not found');
        }
        View::render('admin/cms/page_form', [
            'title' => 'Edit CMS Page',
            'page' => $page,
            'csrf' => Csrf::token(),
        ]);
    }

    public function pageUpdate(int $id): void
    {
        $this->guardManage();
        $this->checkCsrf();
        (new CmsRepository())->updatePage($id, $this->pageInput());
        Helpers::redirect('/admin/cms/pages/' . $id . '/edit');
    }

    public function pageDelete(int $id): void
    {
        $this->guardManage();
        $this->checkCsrf();
        (new CmsRepository())->deletePage($id);
        Helpers::redirect('/admin/cms/pages');
    }

    // -------- Blocks ----------------------------------------------------

    public function blocksIndex(): void
    {
        $this->guardManage();
        View::render('admin/cms/blocks', [
            'title' => 'CMS Blocks',
            'blocks' => (new CmsRepository())->allBlocks(),
            'csrf' => Csrf::token(),
        ]);
    }

    public function blockSave(): void
    {
        $this->guardManage();
        $this->checkCsrf();
        (new CmsRepository())->upsertBlock(
            (string) ($_POST['block_key'] ?? ''),
            (string) ($_POST['label'] ?? ''),
            (string) ($_POST['content'] ?? ''),
            !empty($_POST['is_active']),
            (int) Auth::user()['id']
        );
        Helpers::redirect('/admin/cms/blocks');
    }

    public function blockDelete(int $id): void
    {
        $this->guardManage();
        $this->checkCsrf();
        (new CmsRepository())->deleteBlock($id);
        Helpers::redirect('/admin/cms/blocks');
    }

    // -------- Menus -----------------------------------------------------

    public function menusIndex(): void
    {
        $this->guardManage();
        $repo = new CmsRepository();
        $menus = $repo->menus();
        $items = [];
        foreach ($menus as $menu) {
            $items[(int) $menu['id']] = $repo->menuItems((int) $menu['id']);
        }
        View::render('admin/cms/menus', [
            'title' => 'CMS Menus',
            'menus' => $menus,
            'items' => $items,
            'csrf' => Csrf::token(),
        ]);
    }

    public function menuCreate(): void
    {
        $this->guardManage();
        $this->checkCsrf();
        (new CmsRepository())->createMenu(
            (string) ($_POST['slug'] ?? ''),
            (string) ($_POST['label'] ?? '')
        );
        Helpers::redirect('/admin/cms/menus');
    }

    public function menuDelete(int $id): void
    {
        $this->guardManage();
        $this->checkCsrf();
        (new CmsRepository())->deleteMenu($id);
        Helpers::redirect('/admin/cms/menus');
    }

    public function menuItemCreate(int $menuId): void
    {
        $this->guardManage();
        $this->checkCsrf();
        (new CmsRepository())->addMenuItem(
            $menuId,
            (string) ($_POST['label'] ?? ''),
            (string) ($_POST['url'] ?? ''),
            (int) ($_POST['position'] ?? 0),
            (string) ($_POST['target'] ?? '_self'),
            !empty($_POST['is_active'])
        );
        Helpers::redirect('/admin/cms/menus');
    }

    public function menuItemDelete(int $itemId): void
    {
        $this->guardManage();
        $this->checkCsrf();
        (new CmsRepository())->deleteMenuItem($itemId);
        Helpers::redirect('/admin/cms/menus');
    }

    // -------- Helpers ---------------------------------------------------

    private function guardManage(): void
    {
        Auth::requireAuth();
        // Admins always pass; otherwise require the fine-grained CMS permission.
        if (!Auth::hasRole('Admin') && !Auth::can('cms.pages.manage')) {
            http_response_code(403);
            exit('Forbidden');
        }
    }

    private function checkCsrf(): void
    {
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }
    }

    /**
     * @return array{slug:string,title:string,summary:?string,body:string,status:string,layout:string}
     */
    private function pageInput(): array
    {
        return [
            'slug' => trim((string) ($_POST['slug'] ?? '')),
            'title' => trim((string) ($_POST['title'] ?? 'Untitled')),
            'summary' => (string) ($_POST['summary'] ?? '') !== '' ? trim((string) $_POST['summary']) : null,
            'body' => (string) ($_POST['body'] ?? ''),
            'status' => (string) ($_POST['status'] ?? 'draft'),
            'layout' => (string) ($_POST['layout'] ?? 'default'),
        ];
    }
}
