<?php

declare(strict_types=1);

namespace Intranet\Modules\Users\Controllers;

use Intranet\Core\Auth;
use Intranet\Core\Database;
use Intranet\Core\View;
use Intranet\Modules\Shared\Repositories\UserRepository;

final class UserController
{
    public function profile(int $id): void
    {
        $profile = (new UserRepository())->profileWithStats($id);
        if (!$profile) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'User not found']);
            return;
        }

        View::render('users/profile', [
            'title' => $profile['display_name'] . ' Profile',
            'profile' => $profile,
        ]);
    }

    public function favorites(): void
    {
        Auth::requireAuth();
        $sql = 'SELECT p.* FROM posts p JOIN post_favorites pf ON pf.post_id = p.id WHERE pf.user_id = :uid ORDER BY pf.created_at DESC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['uid' => Auth::user()['id']]);
        View::render('users/favorites', ['title' => 'Favorites', 'posts' => $stmt->fetchAll()]);
    }

    public function bookmarks(): void
    {
        Auth::requireAuth();
        $sql = 'SELECT p.* FROM posts p JOIN post_bookmarks pb ON pb.post_id = p.id WHERE pb.user_id = :uid ORDER BY pb.created_at DESC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['uid' => Auth::user()['id']]);
        View::render('users/bookmarks', ['title' => 'Bookmarks', 'posts' => $stmt->fetchAll()]);
    }
}
