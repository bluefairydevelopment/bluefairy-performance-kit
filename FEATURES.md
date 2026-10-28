# BlueFairy Performance Kit — Feature Guide

Version 1.1.1 | GPL-2.0-or-later | PHP 8.1+ | WordPress 6.4+

WordPress ships front-end JavaScript and CSS that most sites never use. This
plugin takes it out, adds lazy loading for iframes, and otherwise stays out of
your way. Everything is on after activation and there is nothing you must
configure.

---

## Emoji removal

WordPress loads a detection script and a stylesheet on every page so it can
replace emoji characters with images on browsers that cannot render them. That
was worth doing in 2015.

Removed: the detection script from the front end, the admin and embeds; the
emoji stylesheet on all three; the feed and RSS comment filters; the email
filter; the TinyMCE plugin; and the `s.w.org` DNS-prefetch hint.

> **This was broken before 1.1.0.** WordPress now enqueues emoji styles through
> `wp_enqueue_emoji_styles()` and unhooks the older `print_emoji_styles()`
> itself, so a plugin that removes only the old callback leaves the stylesheet
> loading. Both paths are removed now.

Your emoji still display. They are ordinary Unicode characters and the device
renders them; only WordPress's detection machinery goes.

---

## oEmbed removal

Two things are removed from the front end:

- The oEmbed **discovery links** in `<head>` — the metadata that lets other
  sites embed your posts.
- The **`wp-embed` script**, which powers embeds of *other* sites' content
  inside your pages.

Pasting a YouTube or Twitter URL into the editor still produces an embed.

> **This was broken before 1.1.0 too.** WordPress binds
> `wp_oembed_add_discovery_links` at `wp_head` priority 4, and `remove_action()`
> defaults to priority 10, so the removal silently did nothing and the links
> were printed on every singular page.

---

## Front-end heartbeat

The WordPress heartbeat polls `admin-ajax.php` on an interval. In wp-admin that
drives autosave, post locking and session warnings. On the front end, for a
visitor who will never open wp-admin, it does nothing but consume requests.

The heartbeat script is deregistered on `wp_enqueue_scripts`, which does not run
in wp-admin or the block editor — so autosave and post locking are untouched.

---

## Lazy loading

WordPress already adds `loading="lazy"` to images. It does not add it to
iframes in post content, which is where an embedded video or map sits. This
plugin adds it to any iframe that does not already carry a `loading` attribute.

---

## Settings

**Settings → Performance.** One checkbox per optimisation, with a sentence
explaining what it does. All four are on after activation.

Turning one off unbinds it completely — nothing is left running.

### Conflict detection

If WP Rocket, LiteSpeed Cache, W3 Total Cache, Autoptimize, WP Super Cache or
SiteGround Optimizer is active, the settings page says so and names it.

Nothing breaks when both are on; the work is simply done twice. Switch off
whichever half you prefer. The check runs on every request, so it reflects what
is active now rather than what was active when this plugin was installed.

---

## Uninstalling

Deleting the plugin removes the three options it created. It stores nothing
else — no tables, no files, no transients.

---

## What Performance Kit Pro adds

This plugin removes weight WordPress adds. Pro goes after the Core Web Vitals
themselves. It installs **alongside** this plugin rather than replacing it —
your settings carry over, and these four optimisations keep working exactly as
they do now.

| Feature | Performance Kit Pro | Performance Kit (this plugin) |
|---|---|---|
| `loading="lazy"` on iframes in post content | Yes | Yes |
| Emoji scripts, styles, feed and mail filters removed | Yes | Yes |
| oEmbed discovery links and the `wp-embed` script removed | Yes | Yes |
| Front-end heartbeat disabled | Yes | Yes |
| Settings page with per-feature toggles | Yes | Yes |
| Conflict detection for six caching plugins | Yes | Yes |
| Critical CSS extracted per template and inlined | Yes | — |
| Remaining stylesheets deferred, with a `<noscript>` fallback | Yes | — |
| Optional headless browser endpoint for real above-fold CSS | Yes | — |
| Google Fonts downloaded and served from your own domain | Yes | — |
| The request to `fonts.googleapis.com` removed entirely | Yes | — |
| `preconnect` for third-party origins the page actually loads | Yes | — |
| `preload` for the likely LCP image | Yes | — |
| A `prefetch` for the likely next page | Yes | — |
| Blurred placeholders for WooCommerce product images | Yes | — |
| `srcset` repair and `sizes` defaults | Yes | — |
| Background queue with retries, status panel and WP-CLI | Yes | — |
| Feature toggles on the settings page | 8 | 4 |

### What the missing rows cost you

**Your stylesheets block the first paint.** Every CSS file in `<head>` must
download and parse before the browser draws anything. Pro inlines the CSS that
page template needs and defers the rest, so the first paint stops waiting on the
network.

**Every Google Font is a third-party round trip.** A DNS lookup, a TLS
handshake and two requests to a domain you do not control, before any text
renders. Pro downloads the woff2 files into your uploads folder and serves them
from your own domain, and the request to `fonts.googleapis.com` disappears.

**The browser discovers your biggest image last.** The LCP image is usually
found only after the HTML and CSS have parsed. Pro preloads it, and preconnects
to the third-party origins your page really uses.

### Honest limits

Two things Pro does **not** do, so you are not surprised:

- The built-in critical CSS extractor is a best-effort fallback. It keeps every
  rule whose selector appears in your markup, which on a block or WooCommerce
  theme is most of the stylesheet — and it refuses to inline more than 50 KB,
  because that would make pages slower. A headless browser endpoint is the route
  to a real above-fold result.
- The image placeholders are **WooCommerce product images only**, and the
  placeholder is your existing thumbnail with a CSS blur rather than a generated
  low-quality image. On a site without WooCommerce that feature does nothing.

### Get Pro

**https://wordpress.bluefairydevelopment.com/product/bluefairy-performance-kit-pro/**

One payment, no subscription. Pro requires this plugin to be installed and
active — it is an add-on, not a replacement.
