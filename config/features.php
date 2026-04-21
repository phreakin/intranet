<?php

declare(strict_types=1);

return [
    'oauth' => true,
    'ai' => (bool) (int) (getenv('AI_ENABLED') ?: 0),
    'bookmarklet' => true,
    'moderation' => true,
    'reports' => true,
    'favorites' => true,
    'bookmarks' => true,
    'comments' => true,
    'dislikes' => true,
    'likes' => true,
    'voting' => true,
    'categories' => true,
    'tags' => true,
    'search' => true,
    'pagination' => true,
    'rss' => true,
    'admin' => true,
    'rbac' => true,
    'cms' => true,
    'moderation_queue' => true,
    'users_badges' => true,
    'users_roles' => true,
    'users_comments' => true,
    'users_favorites' => true,
    'users_bookmarks' => true,
    'users_reports' => true,
];
