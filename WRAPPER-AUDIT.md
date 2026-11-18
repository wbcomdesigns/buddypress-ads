# Wrapper Audit — BuddyPress Ads (`buddypress-ads`)

**Branch:** 1.6.0  
**Wrapper type:** NEW card-panel (contact-me pattern)  
**Audit date:** 2026-06-05  
**Auditor:** AutoVAP (read-only; no code changed)

---

## 1. Controller contract (`class-buddypress-ads-rotator-settings-admin.php`)

| Method | Present? | Notes |
|---|---|---|
| `get_tabs()` | YES | Static; returns 4 tabs: overview / scripts / welcome / faq, filterable |
| `register()` | YES | Hooks admin_menu, admin_menu@999 (hub takeover), admin_init, admin_enqueue_scripts, in_admin_header |
| `add_menu()` | YES | First-wins guard for wbcomplugins top-level; single submenu `buddypress-ads-rotator-settings` |
| `takeover_hub_landing()` | YES | remove_all_actions + re-add at priority 999 — handles legacy wrapper co-install |
| `register_settings()` | YES | One option only: `buddypress-ads-rotator_admin_scripts_options` |
| `sanitize_scripts()` | YES | wp_kses_post per key (header/body/footer); array guard; drops unknown keys |
| `enqueue_assets()` | YES | Screen-scoped via `is_our_screen()` |
| `is_our_screen()` | YES | Matches `*_page_buddypress-ads-rotator-settings` + `toplevel_page_wbcomplugins` |
| `suppress_foreign_notices()` | YES | in_admin_header priority 1; removes admin_notices + all_admin_notices |
| `render_hub()` | YES | Includes via `BUDDYPRESS_ADS_ROTATOR_PATH . 'includes/admin/views/hub.php'` |
| `render_page()` | YES | Routes active tab to view; passes $wbar_tabs, $active, $settings, $page_url, $view_path, $in_settings_group, $settings_form_group |

All required contract methods are present.

---

## 2. Path-constant check — PASS

**The fix is correct and complete.**

`BUDDYPRESS_ADS_ROTATOR_URL` and `BUDDYPRESS_ADS_ROTATOR_PATH` are defined in
`includes/class-buddypress-ads-rotator.php::define_constants()` (lines 94–95):

```php
$this->define( 'BUDDYPRESS_ADS_ROTATOR_URL',  plugin_dir_url( __DIR__ ) );
$this->define( 'BUDDYPRESS_ADS_ROTATOR_PATH', plugin_dir_path( __DIR__ ) );
```

`__DIR__` in that file = `.../buddypress-ads/includes/`.
`plugin_dir_url( __DIR__ )` and `plugin_dir_path( __DIR__ )` apply `dirname()` internally,
so they step one level up to the **plugin root** — confirmed via `wp eval`:

```
PATH = .../wp-content/plugins/buddypress-ads/
URL  = http://wbcom-free.local/wp-content/plugins/buddypress-ads/
```

All four controller references use these root constants:

| Call | Resolved to | File exists? |
|---|---|---|
| `BUDDYPRESS_ADS_ROTATOR_URL . 'assets/css/admin.css'` | plugin root → `assets/css/admin.css` | YES |
| `BUDDYPRESS_ADS_ROTATOR_URL . 'assets/js/admin.js'` | plugin root → `assets/js/admin.js` | YES |
| `BUDDYPRESS_ADS_ROTATOR_PATH . 'includes/admin/views/hub.php'` | plugin root → `includes/admin/views/hub.php` | YES |
| `BUDDYPRESS_ADS_ROTATOR_PATH . 'includes/admin/views/<view>.php'` | plugin root → correct views | YES |

Zero `plugin_dir_*( BUDDYPRESS_ADS_ROTATOR_FILE )` calls in the new controller. The
`BUDDYPRESS_ADS_ROTATOR_FILE` constant (`includes/class-buddypress-ads-rotator.php`) is
never used for asset or view resolution in the settings admin.

The old-class `Buddypress_Ads_Rotator_Admin` still uses `plugin_dir_url( __FILE__ )` for
CPT-screen assets — that file is `admin/class-buddypress-ads-rotator-admin.php`, so its
`__FILE__` correctly resolves to `admin/`, where `css/`, `js/`, and `images/` actually live.
All four CPT asset paths confirmed on disk.

---

## 3. Tab ↔ view map

### get_tabs() keys:

| Tab slug | Label | Group | View file (via view_map) | File on disk |
|---|---|---|---|---|
| `overview` | Overview | main | `overview.php` | EXISTS |
| `scripts` | Scripts / Styles | settings | `settings-scripts.php` | EXISTS |
| `welcome` | Welcome | help | `welcome.php` | EXISTS |
| `faq` | FAQ | help | `faq.php` | EXISTS |

No dead nav links. No orphan views. `hub.php` and `shell.php` are framework files, not
navigable tabs — both exist on disk.

---

## 4. Option wiring (end-to-end)

### `buddypress-ads-rotator_admin_scripts_options` (the live option)

| Stage | Result | Evidence |
|---|---|---|
| Registered | YES | `register_setting( OPTION_GROUP, OPTION_NAME, [...] )` at controller:135 |
| Rendered | YES | Three `<textarea>` fields in `settings-scripts.php` with `name="buddypress-ads-rotator_admin_scripts_options[header/body/footer]"` |
| Sanitized | YES | `sanitize_scripts()` — `wp_kses_post` per key, array guard, unknown keys dropped |
| Saved | YES | Standard Settings-API `options.php` POST via `settings_fields( OPTION_GROUP )` in `shell.php` |
| Read back in form | YES | `$settings = get_option( OPTION_NAME, [] )` in `render_page()`, passed to view |
| Read on frontend | YES | `get_option('buddypress-ads-rotator_admin_scripts_options')` at `public/class-buddypress-ads-rotator-public.php:265,323` and `public/class-buddypress-ads-rotator-bp.php:278,293,310` |
| Verdict | PASS | Full end-to-end round-trip intact; option name/group/sub-keys byte-for-byte identical to legacy |

### Orphan options — CLEAN

| Legacy option | Status |
|---|---|
| `wb_ads_rotator_admin_welcome_options` | NOT registered in the new controller — intentionally dropped (was a never-read placeholder). Welcome tab is informational HTML only. |
| `buddypress-ads-rotator_admin_faq_option` | NOT registered — intentionally dropped (same reason; FAQ is informational `<details>` accordions). |

No `register_setting()` call for either orphan option exists anywhere in the codebase. Confirmed by grep. No orphaned fields remain.

### Multi-tab data-loss guard

N/A — only one tab (`scripts`) writes to one option. Playbook 7.1 sentinel-merge not required.

---

## 5. Security fix verification — PASS

All three AJAX handlers in `admin/class-buddypress-ads-rotator-admin.php` now gate on
`current_user_can( 'manage_options' )` **before** the nonce check:

| Handler | Method | Line | Cap check | Order |
|---|---|---|---|---|
| `wb_ads_rotator_image` | `wb_ads_rotator_image()` | 758–759 | `manage_options` | Cap first, then nonce |
| `wb_ads_rotator_ajax_enable_callback` | toggle writes `wb_ads_enable` meta | 863–864 | `manage_options` | Cap first, then nonce |
| `wb_ads_remove_uploaded_image` | `wb_ads_remove_uploaded_image()` | 891–892 | `manage_options` | Cap first, then nonce |

The privilege-escalation risk (any logged-in subscriber could toggle ads via
`wb_ads_rotator_enable`) is fixed. The legacy `wbcom_addons_cards` AJAX handler is
completely gone with the deleted `admin/wbcom/` directory — confirmed by grep (zero
`wbcom_addons_cards` references anywhere in PHP files).

---

## 6. "Add New Ad" button label — PASS

The button in the sidebar nav (`shell.php:95–98`) renders:

```html
<a href=".../post-new.php?post_type=wb-ads" class="wbar-snav-link">
    <span class="dashicons dashicons-plus-alt" aria-hidden="true"></span>
    Add New Ad   <!-- via esc_html_e('Add New Ad', 'buddypress-ads-rotator') -->
</a>
```

The label is correctly emitted by `esc_html_e`. No empty or duplicate button. The same
CTA also appears in the Quick Actions card in `overview.php:143–146` as a
`wbar-btn wbar-btn-primary`, and in `welcome.php:24–27` as "Create Your First Ad". All
three are correctly labeled.

---

## 7. CPT `wb-ads` + metaboxes — UNTOUCHED CONFIRMED

`Buddypress_Ads_Rotator_Admin` still owns:
- `wb_ads_rotator_add_cpt()` — CPT registration
- `wb_ads_rotator_add_metaboxes()` — 4 metaboxes  
- `wb_ads_rotator_save_post_meta()` — save handler  
- `wb_ads_rotator_add_disable_admin_columns()` / `_column_content()` — list columns  
- `wb_ads_rotator_update_default_value_on_ads_publish()` — transition hook  

All 11 retained methods confirmed still wired via `define_admin_hooks()` in the core class.
None of the 8 deleted methods appear anywhere. No CPT-related code changed.

---

## 8. Legacy wrapper removal — CLEAN

| Item | Status |
|---|---|
| `admin/wbcom/` directory | DELETED (confirmed absent on disk) |
| `admin/partials/` directory | DELETED (confirmed absent on disk) |
| `[wbcom_admin_setting_header]` shortcode | GONE — handler deleted with `admin/wbcom/` |
| `wbcom_addons_cards` AJAX | GONE — handler deleted with `admin/wbcom/` |
| `Wbcom_Admin_Settings` class | GONE — file deleted |
| `do_shortcode('[wbcom_admin_setting_header]')` calls | NONE remaining in PHP |
| `wbcom-designs` top-level menu | NEVER existed in this plugin; wbcomplugins is the parent |
| `shared-admin/class-wbcom-shared-dashboard.php` | NEVER existed in this plugin |

Only two remaining references to `admin/wbcom/` are in code comments (BUILD-LOG and
the core class docblock) — both intentional documentation, not live references.

---

## 9. Hygiene checks

| Check | Result | Detail |
|---|---|---|
| Native `alert()`/`confirm()` in new JS | NONE | `assets/js/admin.js` uses `wbarToast()` (DOM-based) and `wbarConfirm()` (Promise/modal) exclusively |
| Inline `<style>` / `<script>` in views | NONE | Zero inline blocks in all 6 view files |
| Screen-scoped enqueue | PASS | `is_our_screen()` gates both CSS and JS; CPT admin assets separately gated in legacy class |
| Token coverage | PASS with one minor gap | All colours reference `var(--wbar-admin-*)` tokens. Two structural white usages (`color: #fff` on accent buttons — standard per-skin hardcode) and one amber warning text (`color: #92400e` on `.wbar-notice--warn`) lack a dedicated token. Not an error; `#92400e` is the accessible dark amber for the light-yellow background. No raw hex in layouts or spacing. |
| Responsive breakpoints | 2 of 2 | `@media (max-width: 1024px)` — sidebar stacks; `@media (max-width: 640px)` — header + form-table block. CSS comment says "exactly two media blocks per skill Part 7.5". No third breakpoint at 480px, but the 640px block handles narrower widths too. |
| Tap target (buttons) | MINOR GAP | `.wbar-btn` has `min-height: 36px`. The 40px minimum tap-target rule is not met. Rendered height ≈ 36px (no padding-top/bottom adds enough to bridge the gap). `.wbar-role-chip` correctly uses `min-height: 40px`. |
| `$hook_suffix` unused param | LOW | `enqueue_assets( $hook_suffix )` immediately calls `unset($hook_suffix)` and uses `get_current_screen()` instead. Works correctly but WPCS will warn about unused param; could accept `$unused = null` instead or just document the discard. |
| Focus-visible handling | PASS | `outline: none` on `:focus:not(:focus-visible)` with `outline: 2px solid var(--wbar-admin-accent)` on `:focus-visible` throughout. Keyboard users retain a visible ring. |
| Hub card icon size | PASS | 44×44px icon container. |

---

## 10. Severity-ranked findings

### Pre-existing (not introduced by this migration — included for completeness)

| # | Severity | Finding | File:line |
|---|---|---|---|
| P1 | HIGH (pre-existing, deferred) | Unbounded `get_posts( post_type=wb-ads )` with `meta_query LIKE` runs on every front-end request (`the_content`, `wp_head`, `wp_footer`, multiple BP activity hooks); per-ad `get_post_meta()` N+1 in foreach; no `posts_per_page` cap, no caching | `public/class-buddypress-ads-rotator-public.php:171,232,295`; `public/class-buddypress-ads-rotator-bp.php` |
| P2 | LOW (pre-existing) | `get_option('wb_ads_image_id')` in image-ad metabox preview — option is never written; always returns empty | `admin/class-buddypress-ads-rotator-admin.php:492` |
| P3 | LOW (pre-existing) | `README.txt` requires `at least: 3.0.1`, no `Requires PHP` in either README or plugin header | `README.txt` + `buddypress-ads-rotator.php` |
| P4 | LOW (pre-existing) | `die('Busted!')` on bad nonce vs `wp_send_json_error()` on cap failure — inconsistent AJAX error surface | `admin/class-buddypress-ads-rotator-admin.php:766,871,896` |

### New wrapper layer

| # | Severity | Finding | File:line |
|---|---|---|---|
| N1 | MINOR | `.wbar-btn` `min-height: 36px` is 4px below the 40px tap-target minimum required by the skill. Affects the three Quick-Action buttons on the Overview tab and the Settings save button. | `assets/css/admin.css:570` |
| N2 | MINOR | `color: #92400e` on `.wbar-notice--warn` (amber warning text) has no dedicated `--wbar-admin-*` token. Not wrong, but breaks token-coverage completeness. | `assets/css/admin.css:684` |
| N3 | LOW | `enqueue_assets( $hook_suffix )` immediately discards its parameter with `unset()`. Works but is unconventional; WPCS may flag it in future tooling passes. | `includes/admin/class-buddypress-ads-rotator-settings-admin.php:178` |

---

## 11. Overall verdict

**SHIP-CLEAN** for the wrapper migration scope.

Every mandatory check passes:

- Path-constant fix is correct — `BUDDYPRESS_ADS_ROTATOR_PATH/URL` resolve to plugin root; all view includes and asset URLs resolve to existing files; no 404 risk.
- Security fix confirmed — `current_user_can('manage_options')` is the first check in all three AJAX handlers, before nonce verification.
- Option wiring complete — `buddypress-ads-rotator_admin_scripts_options` registers, renders (correct field names), sanitizes (wp_kses_post per key), saves, and is read by all five frontend call sites.
- Orphan options clean — the two never-read placeholder options are gone with no registered remnants.
- Legacy wrapper fully removed — `admin/wbcom/` deleted, no live references, no shortcode, no AJAX handler.
- CPT and metaboxes untouched.
- "Add New Ad" button correctly labeled in all three locations.
- No native `alert()`/`confirm()` in new JS.
- PHP syntax clean on all 9 new/modified files.

Must-fix before release (wrapper scope): **none**.

Minor items to carry forward to a polish pass (N1, N2, N3 above): `min-height: 36px → 40px` on `.wbar-btn`, add `--wbar-admin-warn-text` token for `#92400e`, and resolve the unused `$hook_suffix` parameter.

Pre-existing issues P1 (unbounded frontend queries / N+1) and P4 (AJAX error surface inconsistency) are already tracked in the deferred TODOs list in `BUILD-LOG.md` and should be addressed before a production release, but are outside this migration's scope.
