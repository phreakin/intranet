<?php

declare(strict_types=1);

return [
    // Shared runtime / platform services
    'Shared' => true,

    // Primary application modules
    'Authentication' => true,
    'Users' => true,
    'Dashboard' => true,
    'Posts' => true,
    'Admin' => true,
    'Cms' => true,

    // Phase 1 / 2 logical domain modules handled inside broader folders today
    'Categories' => true,
    'Tags' => true,
    'Comments' => true,
    'Voting' => true,
    'Favorites' => true,
    'Bookmarks' => true,
    'Reports' => true,
    'Moderation' => true,
    'AiModeration' => true,
    'Bookmarklet' => true,
    'RBAC' => true,
    'Taxonomy' => true,
    'Profiles' => true,
    'Badges' => true,
    'Roles' => true,
    'Settings' => true,
    'Search' => true,

    // Phase 3 expansion
    'ModuleRegistry' => true,
    'Pages' => true,
    'Articles' => true,

    // Phase 4 expansion
    'Automation' => false,
    'RecommendationEngine' => false,
    'RelatedContent' => false,
    'Analytics' => false,

    // Phase 5 / 9 expansion
    'ActivityTracking' => false,
    'SessionTracking' => false,
    'BanManagement' => false,

    // Phase 6 / 10 expansion
    'ModerationEngine' => false,

    // Phase 7 / 11 / 12 expansion
    'Api' => false,
    'Integrations' => false,
    'ImportExport' => false,
    'BackupRestore' => false,
    'Webhooks' => false,

    // Phase 8 expansion
    'PluginSystem' => false,
    'Personalization' => false,
    'CustomFeeds' => false,
    'Notifications' => false,
    'Apps' => false,
];
