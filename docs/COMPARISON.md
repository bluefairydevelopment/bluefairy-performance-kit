# BlueFairy Performance Kit — Pro vs free

Single source of truth for the feature comparison. Verified against the shipped
code of both plugins at 1.1.0 on 2026-08-31, after the Stage 0–3 rework.

Every downstream copy of this table — the hub page, the FAQ, both readme.txt
files, both features guides and both product descriptions — derives from here.
Change a row here first, then rebuild every node.

| Feature | Performance Kit Pro | Performance Kit (free) |
|---|---|---|
| `loading="lazy"` on iframes in post content | Yes | Yes |
| Emoji detection script, styles, feed and mail filters removed | Yes | Yes |
| oEmbed discovery links and the `wp-embed` script removed | Yes | Yes |
| Front-end heartbeat disabled (kept in wp-admin and the editor) | Yes | Yes |
| Settings → Performance with per-feature toggles | Yes | Yes |
| Conflict detection: WP Rocket, LiteSpeed, W3 Total Cache, Autoptimize, WP Super Cache, SiteGround Optimizer | Yes | Yes |
| Critical CSS extracted per page template and inlined | Yes | — |
| Remaining stylesheets deferred with a `<noscript>` fallback | Yes | — |
| Optional headless browser endpoint for real above-fold extraction | Yes | — |
| Google Fonts downloaded and served from your own uploads folder | Yes | — |
| The request to `fonts.googleapis.com` removed entirely | Yes | — |
| `preconnect` and `dns-prefetch` for third-party origins the page loads | Yes | — |
| `<link rel="preload" as="image">` for the likely LCP image | Yes | — |
| A single `<link rel="prefetch">` for the likely next page | Yes | — |
| Blurred placeholders for WooCommerce product images | Yes | — |
| `srcset` repair and `sizes` defaults | Yes | — |
| Background generation queue with retries, status panel and WP-CLI | Yes | — |
| Feature toggles on the settings page | 8 | 4 |

## Rows that are easy to get wrong

**The free plugin is not a cut-down Pro.** Until 2026-08-31 the two plugins
shared no feature at all: the free one removed WordPress's own front-end weight,
the paid one chased Core Web Vitals, and neither knew the other existed. Pro is
now a genuine add-on and requires the free plugin, so a Pro customer keeps
everything in the first six rows. Never describe Pro as a replacement.

**Image placeholders are WooCommerce-only, and they are not generated LQIPs.**
`LqipHandler` hooks `woocommerce_product_get_image` and nothing else, so on a
site without WooCommerce the image feature does nothing. What it serves as the
placeholder is the existing `thumbnail` size with a CSS blur, swapped for the
full image by an IntersectionObserver — not a generated low-quality image
embedded in the markup. Do not write "LQIP for every image on the page"; that
claim shipped in 1.0.0 and was false in both halves.

**Prefetch is one link, in three contexts.** `PrefetchResolver` emits a single
`<link rel="prefetch">`: the first product on a shop or product archive, the
cart from a single product page, and the checkout from the cart. A blog archive
gets none. Do not write "prefetch for post links in the current archive
listing"; that claim shipped in 1.0.0 and was false.

**Critical CSS will decline to run on many sites, by design.** The built-in
extractor keeps every rule whose key selector appears in the markup, which on a
block or WooCommerce theme is most of the stylesheet. Anything over 50 KB is
rejected with a message pointing at the headless endpoint, because inlining that
much makes pages slower. Say that plainly: the built-in extractor is a
best-effort fallback and a headless endpoint is the route to a real result.

**Resource hints only cover what the page loads.** Enqueued handles and their
dependencies, not everything registered. This matters because stock WordPress
registers `open-sans` and `wp-editor-font` with `fonts.googleapis.com` URLs, and
1.0.0 emitted Google Fonts preconnects on sites that used no Google fonts.

## Claims that are NOT true and must never be restored

Each was checked against the code and found false.

- **"Generates low-quality image placeholders for every image on the page."**
  WooCommerce product images only, and it reuses the thumbnail size.
- **"Prefetch for post links in the current archive listing."** One link, and
  never on a post archive.
- **"Requires at least WordPress 6.4"** while the activation check demanded 7.0.
  The advertised and enforced minimums are now the same constant.
- **"Admin-pinned LCP image."** Nothing ever wrote that meta key; the branch has
  been removed.
