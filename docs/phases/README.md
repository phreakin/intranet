# Home Intranet
### A Modular, Local-First, Cinematic Home Intranet System

**Home Intranet** is a lightweight, modular, private home intranet designed for a single Linux server with limited resources.
It delivers a premium cinematic UI, a clean PHP/MySQL architecture, and a future-proof modular system prepared for additional phases such as RBAC, CMS, AI moderation, and plugins.

This repository contains **Phase 1 — Core Intranet**, fully implemented.

---

## Project Goals

- Local-first, self-hosted intranet
- Lightweight and resource-efficient
- Modular and extendable
- Server-side rendered with PHP
- Premium cinematic UI
- Real, working code — no placeholders

---

## Architecture Overview

The system is organized into independent modules, each removable and extendable without breaking others.

```text
app/
├── Core/
├── Dashboard/
├── Posts/
├── Categories/
├── Tags/
├── Comments/
├── Voting/
├── Favorites/
├── Reports/
├── Bookmarklet/
└── Admin/
```

Each module contains:

```text
Controllers/
Models/
Services/
Views/
```

Shared logic lives in `app/Core/Services`.
Modules are enabled or disabled via `config/modules.php`.

---

## Tech Stack

- PHP 8.2+ for server-side rendering
- MySQL 8+
- jQuery
- Bootstrap 5, heavily customized
- Composer with lightweight dependencies
- Optional AI integration, disabled by default

---

## UI / Design System

A premium cinematic aesthetic:

- dark glass panels
- blurred backgrounds
- cyan, blue, and violet glow accents
- strong typography contrast
- compact, dense layout
- card-based content
- chip and tag UI
- investigative evidence-wall inspiration
- subtle hover animations
- premium admin shell

This is **not** a generic Bootstrap CRUD interface.

---

## Phase 1 — Core Intranet

### Link Submission
- URL form
- Auto-fetch metadata:
  - title, description, thumbnail
  - Open Graph data
  - canonical URL
  - site name
- Inline category creation
- Comma-separated tags
- Manual metadata editing

### Categories & Tags
- Select existing category
- Create new category inline
- Tag chips
- Tag search

### Dashboard
- Newest posts first
- Cinematic card layout
- Shows:
  - title
  - thumbnail
  - description snippet
  - category
  - tags
  - stats for likes and comments

### Post Display
- Full metadata
- Tag chips
- Category badge
- Interaction panel

### Interactions
- Like / dislike
- Comment
- Favorite
- Bookmark
- Report
- Share on social media,email, etc. ( future phase)

### Comments
- Basic threaded commenting system
- Timestamped
- Displayed under each post
- users can edit their own comments
- users can post new comment or reply to existing ones
- users can delete their own comments
- users can edit other people's comments
- users can delete other people's comments
- users of each comment has ip address and user agent stored for admin review under each comment
- users can report comments
- users can flag comments
- users can block comments
- users can hide comments
- users can tag comments with tags (future phase)
- AI suggested tags for comments (future phase)
- AI moderation for comments (future phase)

### Reporting
- Flags content
- No deletion
- Admin review only
- Reason for report
- Timestamped
- Displayed under each post
- Admin review only
- hides post from public view until reviewed

### Bookmarklet
Drag-to-bookmarks JavaScript snippet that:
- captures the current page URL
- attempts metadata extraction
- displays the post preview
- gets a thumbnail from the Open Graph data
- opens the submission form prefilled
- allows manual editing before submission
- saves the post to the database

### Admin Tools
- View posts
- Edit metadata
- Delete posts
- View reports

---

## MySQL Schema (Phase 1)

### `posts`
```text
id (PK)
url
canonical_url
title
description
thumbnail
site_name
category_id (FK)
created_at
updated_at
like_count
dislike_count
comment_count
```

### `categories`
```text
id (PK)
name
slug
created_at
```

### `tags`
```text
id (PK)
name
slug
```

### `post_tags`
```text
post_id (FK)
tag_id (FK)
```

### `comments`
```text
id (PK)
post_id (FK)
user_name
content
created_at
```

### `post_votes`
```text
id (PK)
post_id (FK)
vote_type
created_at
```

### `post_favorites`
```text
id (PK)
post_id (FK)
created_at
```

### `post_bookmarks`
```text
id (PK)
post_id (FK)
created_at
```

### `post_reports`
```text
id (PK)
post_id (FK)
reason
created_at
```

---

## Metadata Extraction Service

Located in:

```php
app/Core/Services/MetadataService.php
```

Responsibilities:

- Validate URLs
- Fetch HTML
- Parse Open Graph tags
- Extract title, description, and thumbnail
- Normalize canonical URLs
- Sanitize all fields
- Cache responses to reduce repeated fetches

---

## Security

- CSRF protection
- Prepared statements with PDO
- Input validation
- URL validation
- Sanitized metadata
- No authentication yet; planned for Phase 2

---

## Configuration

### `config/app.php`
- app name
- base URL
- caching options

### `config/database.php`
- MySQL credentials

### `config/modules.php`
Enable or disable modules:

```php
return [
    'Dashboard'   => true,
    'Posts'       => true,
    'Categories'  => true,
    'Tags'        => true,
    'Comments'    => true,
    'Voting'      => true,
    'Favorites'   => true,
    'Reports'     => true,
    'Bookmarklet' => true,
    'Admin'       => true,

    // Future phases
    'RBAC'        => false,
    'CMS'         => false,
    'AI'          => false,
    'Plugins'     => false,
];
```

### `config/ui.php`
- visual theme settings
- layout tuning
- component styling options

---

## Folder Structure

```text
intranet/
├── app/
│   ├── Core/
│   ├── Dashboard/
│   ├── Posts/
│   ├── Categories/
│   ├── Tags/
│   ├── Comments/
│   ├── Voting/
│   ├── Favorites/
│   ├── Reports/
│   ├── Bookmarklet/
│   └── Admin/
├── config/
│   ├── app.php
│   ├── database.php
│   ├── modules.php
│   └── ui.php
├── public/
│   ├── index.php
│   ├── assets/
│   └── bookmarklet.js
├── storage/
│   ├── cache/
│   └── logs/
└── vendor/
```

---

## Future Phases

### Phase 2 — RBAC
- roles
- permissions
- user accounts

### Phase 3 — CMS
- pages
- blocks
- layouts

### Phase 4 — AI
- moderation
- auto-tagging
- spam detection

### Phase 5 — Plugins
- module discovery
- feature toggles
- plugin loader

The architecture is already prepared for these future phases.
