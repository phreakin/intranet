<?php

declare(strict_types=1);

return [
    // Authentication and access
    'oauth' => true,
    'admin' => true,
    'rbac' => true,

    // Core content system
    'categories' => true,
    'tags' => true,
    'comments' => true,
    'likes' => true,
    'dislikes' => true,
    'voting' => true,
    'favorites' => true,
    'bookmarks' => true,
    'reports' => true,
    'moderation' => true,

    // Intelligence / ingestion tooling
    'bookmarklet' => true,
    'ai' => (bool) (int) (getenv('AI_ENABLED') ?: 0),

    // Content discovery / navigation
    'search' => true,
    'pagination' => true,
    'rss' => true,

    // CMS / admin surfaces
    'cms' => true,
    'moderation_queue' => true,
    'users_badges' => true,
    'users_roles' => true,

    // User profile surfaces
    'users_comments' => true,
    'users_favorites' => true,
    'users_bookmarks' => true,
    'users_reports' => true,
];
