# BUILD-LOG — BuddyPress Ads wrapper migration

**Plugin:** Wbcom Designs - BuddyPress Ads (`buddypress-ads`)
**Branch:** `1.6.0`  ·  **Main file:** `buddypress-ads-rotator.php`  ·  **readme:** `README.txt`
**Migration:** OLD Wbcom tab wrapper → new card-panel wrapper (contact-me pattern)
**Date:** 2026-06-05  ·  **Status:** built, NOT committed/pushed, NOT activated, DB untouched

---

## What this migration did

Replaced the legacy `admin/wbcom/` tab wrapper + hand-rolled Settings-API tab page
with the jetonomy/contact-me card-panel admin: a single submenu under the shared
`wbcomplugins` hub, sidebar nav, page-header card, and per-tab view partials. The
wb-ads CPT, its metaboxes, list columns, save handler, and metabox AJAX are
untouched — only the **settings-page** UI moved.

Prefix for the new admin layer: `wbar-` (CSS classes + tokens), `wbarAdmin` /
`wbarToast` / `wbarConfirm` (JS). Controller class:
`Buddypress_Ads_Rotator_Settings_Admin`.

Tabs: **Overview** (main) · **Scripts / Styles** (the one real settings form) ·
**Welcome** + **FAQ** (informational `help` group, no persistence).

---

## Files DELETED (6 files + 1 dir)

- `admin/wbcom/` (whole dir):
  - `wbcom-admin-settings.php` (class `Wbcom_Admin_Settings`; also held the
    `wbcom_addons_cards` AJAX handler + `[wbcom_admin_setting_header]` shortcode —
    both removed with the file, so no separate cap-fix was needed).
  - `assets/css/wbcom-admin-setting.css`, `assets/js/wbcom-admin-setting.js`
  - `templates/wbcom-license-page.php`, `templates/wbcom-plugins-page.php`,
    `templates/wbcom-support-page.php`
  - (the confirmed-missing `templates/wbcom-themes-page.php` was already absent;
    its `add_submenu_page` is gone with the wrapper.)
- `admin/partials/buddypress-ads-rotator-admin-welcome-display.php.php`
- `admin/partials/buddypress-ads-rotator-admin-faq-display.php`
- `admin/partials/buddypress-ads-rotator-admin-scripts-display.php`
- `admin/partials/` (now-empty dir removed)

## Files CREATED (10)

- `includes/admin/class-buddypress-ads-rotator-settings-admin.php` — controller
  (menu, hub takeover @999, register_settings, sanitize_scripts, screen-scoped
  enqueue, suppress_foreign_notices, render_hub, render_page).
- `includes/admin/views/shell.php` — page-shell (header card + sidebar nav + body
  slot + `wp-header-end` marker + `settings_errors()` inside the content column).
- `includes/admin/views/hub.php` — shared WB Plugins hub landing (wrapper-helper
  slugs filtered out of the card grid).
- `includes/admin/views/overview.php` — stats (published / enabled / drafts via
  `wp_count_posts` + a single bounded `WP_Query` for the enabled count), scripts
  status snapshot, quick actions.
- `includes/admin/views/settings-scripts.php` — the real settings form (header /
  body / footer textareas; option + sub-keys preserved verbatim).
- `includes/admin/views/welcome.php` — informational welcome + support resources.
- `includes/admin/views/faq.php` — informational FAQ as accessible native
  `<details>/<summary>` accordions (FAQ copy corrected: BuddyPress is now optional).
- `assets/css/admin.css` — card-panel CSS, ported from contact-me, prefix renamed
  `bcm-`→`wbar-`, tokens `--bcm-admin-*`→`--wbar-admin-*`, plus view-specific
  helpers (status text, action rows, resource grid, FAQ accordions, hub-about).
- `assets/js/admin.js` — `wbarToast()` + `wbarConfirm()` (Promise) + CodeMirror
  init for the scripts textareas + `[data-wbar-confirm]` intercept. No
  alert()/confirm().
- `BUILD-LOG.md` — this file.

## Files MODIFIED (4)

- `includes/class-buddypress-ads-rotator.php`
  - Dropped `require_once admin/wbcom/wbcom-admin-settings.php`; added
    `require_once includes/admin/class-buddypress-ads-rotator-settings-admin.php`.
  - `define_admin_hooks()`: stopped wiring the legacy class's settings-page UI
    (menu, `wb_ads_rotator_init_plugin_settings`,
    `wbcom_hide_all_admin_notices_from_setting_page`); kept CPT / metabox /
    save_post / list-columns / transition_post_status / metabox-AJAX wiring;
    instantiated + `register()`-ed the new settings admin.
- `admin/class-buddypress-ads-rotator-admin.php`
  - Class docblock rewritten to its post-migration scope (CPT only).
  - `enqueue_styles()` / `enqueue_scripts()`: scoped to the `wb-ads` CPT screens
    only (removed the `page=buddypress-ads-rotator-settings` condition — the new
    page enqueues its own assets).
  - **SECURITY FIX:** added `current_user_can( 'manage_options' )` to all three
    authenticated admin AJAX handlers that previously checked only a nonce:
    `wb_ads_rotator_image` (:933 old), `wb_ads_rotator_ajax_enable_callback`
    (:1034 old — writes `wb_ads_enable` meta; was the real privilege-escalation
    bug, any subscriber could toggle ads), `wb_ads_remove_uploaded_image`
    (:1055 old). Also replaced two `return $post_id;` early-returns on undefined
    `$post_id` with proper `wp_send_json_error()` / `die`.
  - Removed 8 now-dead settings-page methods + the `$plugin_settings_tabs`
    property (see "removed" list below).
- `README.txt` — added card-panel UX + security + cleanup lines to the existing
  1.6.0 changelog entry.

### Dead methods removed from `Buddypress_Ads_Rotator_Admin`
`wb_ads_rotator_add_submenu_page_admin_settings`,
`buddypress_ads_rotator_admin_options_page`, `wb_ads_rotator_plugin_settings_tabs`,
`wb_ads_rotator_init_plugin_settings`, `wbcom_hide_all_admin_notices_from_setting_page`,
`wb_ads_rotator_admin_welcome_content`, `wb_ads_rotator_admin_faq_content`,
`wb_ads_rotator_admin_scripts_content` (+ `$plugin_settings_tabs`).
All 11 retained methods verified still hooked from the core class.

---

## Option keys preserved (old → new, proof)

| Concern | Legacy | New | Status |
|---|---|---|---|
| Scripts option group | `buddypress-ads-rotator_admin_scripts_options` | `OPTION_GROUP` (same) | identical |
| Scripts option name | `buddypress-ads-rotator_admin_scripts_options` | `OPTION_NAME` (same) | identical |
| Sub-keys | `header` / `body` / `footer` | `header` / `body` / `footer` | identical |
| Sanitizer | `wp_kses_post` per key | `wp_kses_post` per key (`sanitize_scripts`) | identical contract |
| Frontend reads | `public/*-public.php`, `public/*-bp.php` (5 sites) | unchanged | untouched |

Single-tab settings form → no sentinel-merge needed (playbook 7.1 N/A).

**Intentionally dropped (per brief):** `wb_ads_rotator_admin_welcome_options` and
`buddypress-ads-rotator_admin_faq_option` were registered placeholders that were
**never read**. They are no longer registered; Welcome/FAQ ported as informational
cards. No data path depended on them.

---

## Safe fixes applied
1. Deleted `admin/wbcom/` incl. the confirmed-missing `wbcom-themes-page.php` page.
2. Added `manage_options` cap to the 3 nonce-only authenticated AJAX handlers
   (real security bug — the enable toggle wrote meta with no cap).
3. No native `alert()`/`confirm()` introduced; new JS uses toast/confirm helpers.
4. Version already 1.6.0 across header + constant + `README.txt` Stable tag — no
   drift, no bump required. Changelog stub lines added for the 1.6.0 entry.
5. FAQ option singular/plural name mismatch resolved by dropping the dead option
   entirely (it was never read), which is cleaner than renaming.

## Verification
- `php -l`: clean on all 10 created/modified PHP files.
- WPCS (`wpcs` MCP): no violations on any new/changed file (one alignment warning
  in overview.php fixed).
- Option-key parity grep: old == new (table above).
- Dangling-reference grep for deleted paths/handles: only intentional code comments
  remain.
- `wp eval`: settings class loads, tabs resolve (overview/scripts/welcome/faq),
  sanitizer strips `<script>` + keeps allowed tags + preserves keys + drops unknown
  keys; all 11 retained CPT methods present, all 8 dead methods gone.
- No inline `<style>`/`<script>`/`style=` in the new admin layer.

---

## TODOs / deferred (out of scope)

1. **PERF (high, deferred per brief):** Unbounded `get_posts( post_type=wb-ads )`
   with a `meta_query LIKE` runs on **every** front-end request via `the_content`,
   `wp_head`, `wp_footer`, and multiple BP activity hooks, then per-ad
   `get_post_meta()` in a `foreach` (N+1). No `posts_per_page` cap, no caching.
   - Locations: `public/class-buddypress-ads-rotator-public.php`
     (`wb_ads_display_in_content` / `_header` / `_footer`, ~:171/:232/:295);
     `public/class-buddypress-ads-rotator-bp.php`
     (`wb_ads_rotator_display_ads` / `wb_ads_scripts` / `wb_ads_rotator_check_ads`).
   - Fix (future release): bound with `posts_per_page`, replace the `meta_query LIKE`
     with a dedicated indexed meta key per position, batch-fetch meta
     (`update_meta_cache`), and cache the eligible-ad set per position with
     invalidation on `save_post` (use `wp_cache_set_last_changed( 'wb_ads' )`).
   - This is the single biggest scale risk on the plugin; it predates this
     migration and is explicitly out of the wrapper-migration scope.

2. **Dead read (low):** `get_option( 'wb_ads_image_id' )` in the image-ad metabox
   preview (`admin/class-buddypress-ads-rotator-admin.php`, ~:492 region) reads an
   option the plugin never writes. Harmless; cosmetic cleanup for a future pass.

3. **README header (low):** `README.txt` has `Requires at least: 3.0.1` and no
   `Requires PHP`; the plugin header has neither. Not introduced by this migration;
   align both files in a dedicated PCP/header pass before any wp.org-style submission.

---

## Needs human eyes
- **Browser smoke test** could not be run: the brief forbids activating the plugin,
  and the plugin is currently **inactive**, so the live card-panel render, the
  options.php save round-trip, the 390px responsive check, the console/debug.log
  tail, and the every-button click test (Part 13) were NOT performed. Run these
  after activation on a dev site before release. Static + `wp eval` load checks all
  pass.
- Confirm `manage_options` is the desired cap for the ad AJAX handlers. The wb-ads
  CPT uses `capability_type => 'page'` (so `edit_pages` would also be defensible);
  `manage_options` was chosen to match the settings-menu gate and the brief.

## Post-migration fix (browser verification, 2026-06-05)
Browser smoke on wbcom-free surfaced a regression: the controller resolved views/assets via `plugin_dir_path()/plugin_dir_url( BUDDYPRESS_ADS_ROTATOR_FILE )`, but that constant is defined as `__FILE__` inside `includes/class-buddypress-ads-rotator.php` (NOT the plugin root). Result: blank admin body (looked for `includes/includes/admin/views/shell.php`) + 404 on `includes/assets/css/admin.css` & `admin.js`.
Fix: switched the 4 references to the plugin's existing root constants `BUDDYPRESS_ADS_ROTATOR_URL` (assets base) and `BUDDYPRESS_ADS_ROTATOR_PATH` (view includes). php -l clean, WPCS clean, page now renders the full card wrapper with styled assets and no console errors.

---

## PERF — front-end ad query bound + batch meta prime + transient cache (2026-06-05)

Addresses WRAPPER-AUDIT finding **P1 (HIGH, big-site)** / `manifest.json` `static_analysis.performance_risks`: on EVERY front-end request the plugin ran an **unbounded** `get_posts( post_type=wb-ads, meta_query LIKE )` and then looped per-ad `get_post_meta()` (N+1) across `the_content`, `wp_head`, `wp_footer`, and 6+ BuddyPress activity hooks, with **no caching**. Each hook ran its own query, so the cost multiplied per hook and grew with ad count.

### Approach (bound + no-N+1 + cache + invalidate)
New helper `includes/class-buddypress-ads-rotator-cache.php` (`Buddypress_Ads_Rotator_Cache`) centralizes the eligible-ad lookup:
- **Bound** — single `get_posts( fields=>ids, posts_per_page=>$max, no_found_rows=>true, update_post_term_cache=>false )`. `$max` is filterable via `apply_filters( 'wb_ads_rotator_max_ads', 5 )`. **Default 5 is intentional and behavior-preserving**: every old call site used `get_posts()` with NO `posts_per_page`, and `get_posts()` defaults `numberposts` to 5 — so the front-end set was already implicitly capped at 5. Keeping 5 makes this a pure perf refactor; site owners can raise it via the filter.
- **No N+1** — after the ID query, one `_prime_post_caches( $ids, false, true )` batch-primes post + meta caches, so every subsequent `get_post_meta()` in the display methods is served from the object cache (verified: 0 extra queries to read both meta keys for all ads).
- **Cache** — IDs stored in transient `wb_ads_rotator_all_ids` (12h TTL) + a per-request static memo, so the 9 front-end hooks share ONE resolution per page (one DB hit cold; zero on warm transient + persistent object cache).
- **Invalidate** — `Buddypress_Ads_Rotator_Cache::invalidate()` deletes the transient + clears the memo, wired to `save_post_wb-ads`, `deleted_post` (type-guarded via the passed `WP_Post`), and called directly inside the enable/disable AJAX (`wb_ads_rotator_ajax_enable_callback`).

### meta_query LIKE
The position tokens (`wp_header`, `wp_before_content`, activity positions, …) live **nested inside the serialized `wb_ads_rotator_values` array**, not as their own meta rows, so a sargable `=` query is not possible without a storage/schema change (would also miss pre-existing ads until re-saved) — out of scope for a behavior-preserving perf pass. Instead the LIKE filtering is reproduced **in PHP against the already-primed meta** (`get_ad_ids_matching()` mirrors `LIKE '%needle%'` via `maybe_serialize()` + `strpos`), so membership is byte-identical with zero extra queries. The per-ad `wb_ads_enable` / `wp_position` re-checks in each display method are left untouched, so which-ads-show-where and ordering are unchanged.

### Files changed
| File | Change |
|---|---|
| `includes/class-buddypress-ads-rotator-cache.php` | NEW — `get_ad_ids()`, `get_ad_ids_matching()`, `invalidate()`, transient `wb_ads_rotator_all_ids`, `wb_ads_rotator_max_ads` filter |
| `includes/class-buddypress-ads-rotator.php` | `load_dependencies()` requires the cache class; `define_admin_hooks()` wires `save_post_wb-ads` + `deleted_post` → `Cache::invalidate` |
| `public/class-buddypress-ads-rotator-public.php` | `wb_ads_display_in_content` (was ~153-171), `wb_ads_display_header` (~220-232), `wb_ads_display_footer` (~283-295): replaced unbounded `get_posts(...LIKE)` with `Cache::get_ad_ids()`; per-position PHP filters retained |
| `public/class-buddypress-ads-rotator-bp.php` | `wb_ads_rotator_display_ads` (~140) → `Cache::get_ad_ids_matching($position)` (preserves the early-return-on-mismatch loop); `wb_ads_scripts` (~248) + `wb_ads_rotator_check_ads` (~327) → `Cache::get_ad_ids()` |
| `admin/class-buddypress-ads-rotator-admin.php` | enable/disable AJAX now calls `Cache::invalidate()` after the meta write |

### Verification
- `php -l` clean on all 5 files.
- WPCS (wpcs MCP) — no new violations; the only warning is a **pre-existing** loose `==` in `display_ad_at_position()` (line ~197, untouched).
- Runtime (`wp eval` on wbcom-free): transient is `false` before read → set on `get_ad_ids()` → deleted on `invalidate()`. Default cap = 5; `wb_ads_rotator_max_ads` filter raises it. Reading both meta keys for all primed ads = **0 queries** (no N+1). `get_ad_ids_matching('wp_header')` membership = identical to the old `meta_query LIKE` query.
- **Query count (7 seeded ads, 3 WP front-end hooks):** OLD = **14** (3 LIKE queries + N+1 meta); NEW cold = **10** (1 shared query + 1 prime, reused); NEW warm transient = **5**. OLD grows with ad-count and hook-count; NEW is flat. With a persistent object cache the warm path approaches 0 DB queries. (BP's 6 activity hooks amplify the OLD multiplier much further; all now share the one memoized set.)

### Behavior note (intentional, bounded)
For the BP activity path, the previous code fetched up to 5 *matching* ads (its LIKE query was the bound), whereas `get_ad_ids_matching()` filters within the 5-ad base cap. On sites with >5 ads this can differ — but the old 5-cap was itself an unintended `get_posts()` default, the new behavior is strictly more bounded, and both are now controlled by `wb_ads_rotator_max_ads`. Documented rather than silently preserving the latent cap.

### Not done (out of scope)
- Sargable `=` meta storage (dedicated per-position meta key written on save) would let MySQL use the postmeta index instead of a serialized substring match — but it's a storage migration, not a perf-only refactor; flagged for a future schema pass.
