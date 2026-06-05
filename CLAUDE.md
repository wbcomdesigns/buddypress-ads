# Plugin: Wbcom Designs - BuddyPress Ads

> **READ FIRST:** [`audit/manifest.json`](audit/manifest.json) is the canonical inventory — 0 REST endpoints, 4 AJAX handlers, 1 CPT (`wb-ads`), 2 shortcodes, 6 admin pages, 0 tables, 0 cron, 0 WP-CLI. Use it before grepping. See also [`audit/FEATURE_AUDIT.md`](audit/FEATURE_AUDIT.md), [`audit/CODE_FLOWS.md`](audit/CODE_FLOWS.md), and the QA baseline [`audit/wppqa-baseline-2026-06-05/SUMMARY.md`](audit/wppqa-baseline-2026-06-05/SUMMARY.md). Refresh via `/wp-plugin-onboard --refresh` after non-trivial changes.

> **Development standards:** Follow the [`/wp-plugin-development`](https://example/skills/wp-plugin-development) skill for ALL code work here — the 16 critical admin rules, Part 6 Admin UI (card-based pattern), the 3-layer design tokens, escaping rules, and dev hygiene (no `alert()`/`confirm()`, no inline SVG where a Lucide icon belongs, no em-dash in i18n strings, 3-breakpoint + 40px tap-target discipline). QA gate via the `wp-plugin-qa` MCP (`wppqa_check_plugin_dev_rules`).

## Quick reference
- **Main file**: `buddypress-ads-rotator.php`
- **Version**: `1.6.0`  ·  **Branch**: `1.6.0`  ·  **Constant**: `BUDDYPRESS_ADS_ROTATOR_VERSION`
- **Namespace**: none — global classes prefixed `Buddypress_Ads_Rotator_`
- **Text domain**: `buddypress-ads-rotator`  ·  **readme**: `README.txt` (capitalized)
- **Extends**: none (standalone free plugin; BuddyPress optional since 1.6.0)
- **Architecture**: WordPress Plugin Boilerplate (loader-orchestrated)

## Key entry points
- Bootstrap → `buddypress-ads-rotator.php` → `run_buddypress_ads_rotator()`
- Core / hook wiring: `includes/class-buddypress-ads-rotator.php` (`define_admin_hooks()` / `define_public_hooks()`)
- Hook registry: `includes/class-buddypress-ads-rotator-loader.php` (add hooks HERE via `$this->loader->add_*`, not scattered)
- Admin: `admin/class-buddypress-ads-rotator-admin.php` (CPT, metaboxes, settings tabs, AJAX, list columns)
- Public: `public/class-buddypress-ads-rotator-public.php` (content/header/footer/shortcode rendering)
- BuddyPress: `public/class-buddypress-ads-rotator-bp.php` (loaded only if `class_exists('BuddyPress')`)

## Admin UI wrapper classification: **OLD**
- **File**: `admin/wbcom/wbcom-admin-settings.php` — class `Wbcom_Admin_Settings` (legacy tab/header pattern). Required from the core class at `includes/class-buddypress-ads-rotator.php:167`.
- The plugin's own settings page (`buddypress-ads-rotator-settings`) is a hand-rolled tabbed Settings-API page in `Buddypress_Ads_Rotator_Admin` that embeds the wrapper header via `do_shortcode('[wbcom_admin_setting_header]')`.
- NOT the INTERMEDIATE (`includes/shared-admin/class-wbcom-shared-dashboard.php`) or NEW (`includes/admin/views/shell.php`) patterns — neither file exists.
- Wrapper-owned pages: Our Plugins (`wbcom-plugins-page`), Our Themes (`wbcom-themes-page` — **template missing**), Support (`wbcom-support-page`); AJAX `wbcom_addons_cards`; shortcode `wbcom_admin_setting_header`.

## Important patterns
- All ad config lives in ONE post-meta array `wb_ads_rotator_values` on the `wb-ads` CPT; on/off in `wb_ads_enable`.
- Frontend rendering = three `Public::wb_ads_rotator_display_{plain_text_and_code|rich_content|image}_ads()` methods; the BP class delegates to them.
- Ad code (scripts/styles/HTML) is rendered **raw/unescaped by design** (with `phpcs:ignore`) — input is gated to admin; treat ad content as trusted-author input.
- Build: `grunt` (gruntfile.js — checktextdomain, makepot).

## Known issues (from onboarding — see wppqa baseline for detail)
1. **4× AJAX nonce-without-capability** (`wb_ads_rotator_image`, `wb_ads_rotator_remove_image`, `wb_ads_rotator_enable` [writes meta], `wbcom_addons_cards`). Add `current_user_can()` after each nonce check. Plugin is NOT release-ready until fixed.
2. **Unbounded `get_posts` + N+1 `get_post_meta`** on `the_content`/`wp_head`/`wp_footer` and BP hooks — scale risk; needs `posts_per_page` bound + caching.
3. **Missing wrapper template** `admin/wbcom/templates/wbcom-themes-page.php` (Our Themes page warns/fatals on open).
4. **Settings name mismatch**: FAQ option `buddypress-ads-rotator_admin_faq_option` (singular) vs group `..._options` (plural); Welcome/FAQ options are placeholders never read.

## CSS selectors (for testing/dev)
- Frontend ad container: `.wb-bannerads-rotator` (+ `.plain-text-and-code-ads` / `.rich-contentwbads` / `.wbcom-imageads`; `.wbbpads-nouveau` under BP Nouveau).
- Admin metabox: `.wb_ads_rotator-panel`, `.wb_ads_rotator-wrapper`, type toggles `.plain-text-and-code` / `.rich-content` / `.image-ad`.
- Enable toggle: `.wb_ads_enable`. Shortcode copy: `.ads-copy` / `.shortcode-copy`.

## Recent changes
| Date | Type | Description | Files |
|---|---|---|---|
| 2026-06-05 | onboard | Generated audit/ manifest + reports + graph + wppqa baseline + this CLAUDE.md (no code change) | audit/*, CLAUDE.md |
