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
];
