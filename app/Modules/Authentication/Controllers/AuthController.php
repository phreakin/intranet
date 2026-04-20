<?php

declare(strict_types=1);

namespace Intranet\Modules\Authentication\Controllers;

use Intranet\Core\Auth;
use Intranet\Core\Csrf;
use Intranet\Core\Helpers;
use Intranet\Core\View;
use Intranet\Modules\Shared\Repositories\UserRepository;
use Intranet\Modules\Shared\Services\OAuthService;

final class AuthController
{
    public function showLogin(): void
    {
        View::render('auth/login', [
            'title' => 'Sign in',
            'csrf' => Csrf::token(),
            'providers' => (new OAuthService())->providers(),
        ]);
    }

    public function login(): void
    {
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $user = (new UserRepository())->findByEmail($email);

        if (!$user || !password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            View::render('auth/login', [
                'title' => 'Sign in',
                'csrf' => Csrf::token(),
                'error' => 'Invalid credentials.',
                'providers' => (new OAuthService())->providers(),
            ]);
            return;
        }

        Auth::login($user);
        Helpers::redirect('/');
    }

    public function showRegister(): void
    {
        View::render('auth/register', ['title' => 'Create account', 'csrf' => Csrf::token()]);
    }

    public function register(): void
    {
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }

        $email = filter_var((string) ($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $displayName = trim((string) ($_POST['display_name'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if (!$email || $displayName === '' || strlen($password) < 8) {
            View::render('auth/register', [
                'title' => 'Create account',
                'csrf' => Csrf::token(),
                'error' => 'Provide valid email, display name, and password (min 8 chars).',
            ]);
            return;
        }

        $repo = new UserRepository();
        if ($repo->findByEmail($email)) {
            View::render('auth/register', [
                'title' => 'Create account',
                'csrf' => Csrf::token(),
                'error' => 'Email is already registered.',
            ]);
            return;
        }

        $id = $repo->createLocal($email, $displayName, $password);
        $user = $repo->findById($id);
        if ($user) {
            Auth::login($user);
        }
        Helpers::redirect('/');
    }

    public function oauthRedirect(string $provider): void
    {
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;
        $_SESSION['oauth_provider'] = $provider;

        $url = (new OAuthService())->authorizeUrl($provider, $state);
        if (!$url) {
            http_response_code(400);
            exit('OAuth provider is not configured.');
        }

        Helpers::redirect($url);
    }

    public function oauthCallback(string $provider): void
    {
        $state = (string) ($_GET['state'] ?? '');
        if ($state === '' || !hash_equals((string) ($_SESSION['oauth_state'] ?? ''), $state) || $provider !== ($_SESSION['oauth_provider'] ?? '')) {
            http_response_code(422);
            exit('Invalid OAuth state.');
        }

        $code = (string) ($_GET['code'] ?? '');
        $oauthUser = (new OAuthService())->fetchUser($provider, $code);
        if (!$oauthUser || $oauthUser['provider_user_id'] === '') {
            http_response_code(400);
            exit('OAuth login failed.');
        }

        $repo = new UserRepository();
        $user = $repo->findByOAuth($provider, $oauthUser['provider_user_id']);
        if (!$user) {
            $email = $oauthUser['email'] !== '' ? $oauthUser['email'] : sprintf('%s-%s@local.oauth', $provider, $oauthUser['provider_user_id']);
            $existing = $repo->findByEmail($email);
            if ($existing) {
                $user = $existing;
            } else {
                $id = $repo->createLocal($email, $oauthUser['name'], bin2hex(random_bytes(20)));
                $user = $repo->findById($id);
            }
            if ($user) {
                $repo->attachOAuth((int) $user['id'], $provider, $oauthUser['provider_user_id']);
            }
        }

        if ($user) {
            Auth::login($user);
        }

        Helpers::redirect('/');
    }

    public function logout(): void
    {
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(422);
            exit('Invalid CSRF token.');
        }
        Auth::logout();
        Helpers::redirect('/login');
    }
}
