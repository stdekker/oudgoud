# Oudgoud theme

Oudgoud is the site-specific block theme for Scouting Jan van Hoof Groep. It
has no build step or runtime dependencies: deploy the tracked theme directory
as-is.

## Source of truth

Git owns `theme.json`, templates, template-part structure, patterns, styles,
scripts, fixed contact details, and social links. Page, post, and Navigation
content remain in WordPress. `parts/header.html` deliberately references the
site's `wp_navigation` record with ID `4`.

Do not customize Header or Footer in the Site Editor. A saved `wp_template_part`
record overrides the corresponding file and makes later Git changes appear to
do nothing. Use the Navigation screen for menu items and edit the part files for
fixed header or footer changes.

## Where behavior lives

- `functions.php` registers theme features and conditionally owned assets.
- `images/favicon-*.png` and `images/apple-touch-icon.png` are the theme's
  fallback icons. Setting a Site Icon in WordPress automatically replaces them.
- `style.css` contains global layout and saved-content compatibility styles;
  component styles live in `css/`.
- The Core Archives block with class `news-date-selector` replaces pagination
  below the main news feed and remains available on monthly archive pages.
- `js/header-navigation.js` owns header scrolling, the mobile overlay, submenu
  toggles, action placement, and search reset behavior.
- `js/landing-page-editor.js` seeds a landing introduction only for a new page
  or a transition to the landing template. It must never reinsert content an
  editor deliberately removed.
- `js/wide-image-compatibility.js` works around WordPress 7.1 clearing Image
  crop settings when wide/full alignment mounts. Its comment contains the
  regression test required before removal.
- `patterns/` contains the only reusable theme-owned block markup.

The mobile Navigation breakpoint is `599px`. Header actions are rendered once
beside the desktop menu and moved into WordPress's native overlay on mobile.
Keep this single ownership model when changing the header.

## Releases and checks

Production asset URLs use the `Version` value in `style.css`. Increase it for
every release that changes CSS, JavaScript, fonts, or images.

From the DDEV project root, run:

```sh
ddev wp theme status oudgoud
ddev exec php -l public_html/wp-content/themes/oudgoud/functions.php
```

Repeat PHP linting for every changed PHP pattern. From this theme directory,
run `node --check` for every changed JavaScript file, validate `theme.json`, and
run `git diff --check`.

Smoke-test the home page, a normal page, `/nieuws/`, a single post, search, and
404 output. At 375px, 720px, and 1440px verify the menu, nested submenus, search,
scroll-aware header, keyboard focus, and reduced motion. Editor changes also
require a page-editor check.
