# BlueFairy Performance Kit Lite

A zero-configuration WordPress plugin that applies four essential performance optimisations on activation.

## Features

| Feature | Description |
|---|---|
| Native lazy loading | Enables `loading="lazy"` on images (via core filter) and iframes (via content filter) |
| Emoji removal | Strips the emoji detection script, styles, TinyMCE plugin, and `s.w.org` DNS-prefetch |
| Embed cleanup | Removes oEmbed discovery `<link>` tags and the `wp-embed` script from the frontend |
| Heartbeat control | Deregisters the Heartbeat API script on the frontend; admin and editor are unaffected |

## Requirements

- WordPress 6.4+
- PHP 8.1+

## Installation

```
wp plugin install bluefairy-performance-kit-lite --activate
```

Or upload the folder to `wp-content/plugins/` and activate via the Plugins screen.

## Development

```
# Lint with PHP_CodeSniffer (WordPress Coding Standards)
phpcs --standard=WordPress includes/ bluefairy-performance-kit-lite.php
```

No build step, no Composer dependencies, no npm packages.

## Upgrading

[BlueFairy Performance Kit Pro](https://wordpress.bluefairydevelopment.com/product/bluefairy-performance-kit/) adds LQIP blur-up placeholders, critical CSS extraction, resource hints, and local Google Fonts.

## License

GPL-2.0-or-later. See [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html).
