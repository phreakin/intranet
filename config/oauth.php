<?php

declare(strict_types=1);

return [
    'google' => [
        'enabled' => (bool) getenv('OAUTH_GOOGLE_CLIENT_ID'),
        'client_id' => getenv('OAUTH_GOOGLE_CLIENT_ID') ?: '',
        'client_secret' => getenv('OAUTH_GOOGLE_CLIENT_SECRET') ?: '',
        'redirect_uri' => getenv('OAUTH_GOOGLE_REDIRECT_URI') ?: '',
        'authorize_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
        'token_url' => 'https://oauth2.googleapis.com/token',
        'user_url' => 'https://www.googleapis.com/oauth2/v3/userinfo',
        'scopes' => ['openid', 'email', 'profile'],
    ],
    'facebook' => [
        'enabled' => (bool) getenv('OAUTH_FACEBOOK_CLIENT_ID'),
        'client_id' => getenv('OAUTH_FACEBOOK_CLIENT_ID') ?: '',
        'client_secret' => getenv('OAUTH_FACEBOOK_CLIENT_SECRET') ?: '',
        'redirect_uri' => getenv('OAUTH_FACEBOOK_REDIRECT_URI') ?: '',
        'authorize_url' => 'https://www.facebook.com/v20.0/dialog/oauth',
        'token_url' => 'https://graph.facebook.com/v20.0/oauth/access_token',
        'user_url' => 'https://graph.facebook.com/me?fields=id,name,email,picture',
        'scopes' => ['email', 'public_profile'],
    ],
    'github' => [
        'enabled' => (bool) getenv('OAUTH_GITHUB_CLIENT_ID'),
        'client_id' => getenv('OAUTH_GITHUB_CLIENT_ID') ?: '',
        'client_secret' => getenv('OAUTH_GITHUB_CLIENT_SECRET') ?: '',
        'redirect_uri' => getenv('OAUTH_GITHUB_REDIRECT_URI') ?: '',
        'authorize_url' => 'https://github.com/login/oauth/authorize',
        'token_url' => 'https://github.com/login/oauth/access_token',
        'user_url' => 'https://api.github.com/user',
        'scopes' => ['read:user', 'user:email'],
    ],
];
