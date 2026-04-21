<?php

declare(strict_types=1);

use Intranet\Modules\Admin\Controllers\AdminController;
use Intranet\Modules\Authentication\Controllers\AuthController;
use Intranet\Modules\Dashboard\Controllers\DashboardController;
use Intranet\Modules\Posts\Controllers\PostController;
use Intranet\Modules\Users\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Public / Core
|--------------------------------------------------------------------------
*/

$router->group(['middleware' => ['auth']], function ($router) {
    $router->get('/dashboard', [DashboardController::class, 'index']);
    $router->get('/dashboard/widget', [DashboardController::class, 'widget']);
});

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| Posts
|--------------------------------------------------------------------------
*/

$router->get('/posts', [PostController::class, 'index']);
$router->get('/posts/show', [PostController::class, 'show']);
$router->get('/posts/submit', [PostController::class, 'create']);
$router->post('/posts/store', [PostController::class, 'store']);
$router->get('/posts/edit', [PostController::class, 'edit']);
$router->post('/posts/update', [PostController::class, 'update']);
$router->post('/posts/delete', [PostController::class, 'delete']);

/*
|--------------------------------------------------------------------------
| Users
|--------------------------------------------------------------------------
*/

$router->get('/profile', [UserController::class, 'profile']);
$router->get('/users/show', [UserController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

$router->get('/admin', [AdminController::class, 'dashboard']);
$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);