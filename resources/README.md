# Resources Directory

This folder is the source-of-truth for server-rendered views and uncompiled UI assets.

## Active structure

- `assets/css`
  - source CSS files used by the Tailwind build pipeline
- `assets/js`
  - source JavaScript files before publishing to `public/assets`
- `assets/sass`
  - optional Sass source kept with the rest of the asset sources
- `data`
  - JSON-backed UI or permission metadata used by build/admin tooling
- `svg`
  - raw SVG source assets and icon sets
- `views`
  - PHP server-rendered templates used by `Intranet\Core\View`

## Views structure

- `views/layouts`
  - top-level application layouts
- `views/components`
  - reusable view fragments used inside pages
- `views/errors`
  - rendered error screens
- `views/admin`
  - admin-facing pages
- `views/auth`
  - login and registration screens
- `views/cms`
  - public CMS pages
- `views/dashboard`
  - dashboard/control-room screens
- `views/moderation`
  - moderation queue screens
- `views/posts`
  - post submission, taxonomy, and detail screens
- `views/users`
  - user profile and saved-content screens

## Notes

- The app loads templates from `resources/views`.
- Published browser assets belong in `public/assets`.
- Empty framework-style placeholder folders were removed so this tree reflects the actual app.
