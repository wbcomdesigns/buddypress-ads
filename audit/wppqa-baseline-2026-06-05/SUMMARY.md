# wppqa Baseline — buddypress-ads @ 1.6.0

**Date:** 2026-06-05 · **Branch:** 1.6.0 · **Commit:** fad6e1f
**Plugin path:** `wp-content/plugins/buddypress-ads`

Run BEFORE manifest generation (hard rule #6). Three static checks executed; the live full-audit (`wppqa_audit_plugin`) not run in this onboarding-only pass.

## Per-check results

| Check | Passed | Failed | Skipped | Verdict |
|---|---|---|---|---|
| `wppqa_check_plugin_dev_rules` | 5 | 4 | 0 | FAIL — 4 high (nonce-no-cap) |
| `wppqa_check_rest_js_contract` | 0 | 0 | 1 | n/a — no REST endpoints |
| `wppqa_check_wiring_completeness` | 0 | 0 | 1 | n/a — no `templates/` settings dir |

## plugin-dev-rules findings (4 × high — "Nonce check without capability check")

| # | File:line | Handler | Triage |
|---|---|---|---|
| 1 | `admin/class-buddypress-ads-rotator-admin.php:933` | `wb_ads_rotator_image` (wp_ajax_) | REAL — auth-required AJAX, nonce only, no `current_user_can`. Returns attachment markup by ID. Low impact (read-only) but should gate on a capability. |
| 2 | `admin/class-buddypress-ads-rotator-admin.php:1034` | `wb_ads_rotator_ajax_enable_callback` (wp_ajax_) | REAL — **writes** `wb_ads_enable` post meta with no capability check. Any logged-in user (incl. subscriber) can enable/disable any ad. Highest-impact of the four. |
| 3 | `admin/class-buddypress-ads-rotator-admin.php:1055` | `wb_ads_remove_uploaded_image` (wp_ajax_) | REAL — auth-required AJAX, nonce only. Returns placeholder URL; low impact but unguarded. |
| 4 | `admin/wbcom/wbcom-admin-settings.php:43` | `wbcom_addons_cards_links` (wp_ajax_) | REAL but trivial — echoes back a sanitized `display_extension` string; legacy shared-wrapper helper. No data write. |

### Real vs false-positive classification
- All 4 are `wp_ajax_` (authenticated) actions, NOT `nopriv` — so the documented `nopriv` false-positive caveat does **not** apply here. **All 4 are genuine** nonce-without-capability findings.
- Severity ranking for a future fix pass: **#2 (write) > #1 ≈ #3 (read) > #4 (echo)**. The fix is a one-line `current_user_can('edit_pages')` (or the wb-ads CPT's effective cap) guard added after each nonce check.

## Beyond wppqa (surfaced during manual onboarding read)

- **Unbounded frontend `get_posts` + N+1 meta reads** on `the_content`/`wp_head`/`wp_footer` and BP hooks — scale risk. See `manifest.json#/static_analysis.performance_risks`.
- **Missing wrapper template** `admin/wbcom/templates/wbcom-themes-page.php` referenced at `wbcom-admin-settings.php:254` — `include` would warn/fatal if the "Our Themes" page is opened.
- **Settings name mismatch** — FAQ option registered as `buddypress-ads-rotator_admin_faq_option` (singular) under group `..._options` (plural).

## Release-readiness gate
`failed > 0` on `plugin-dev-rules` → **NOT release-ready** until the 4 nonce-no-cap handlers add capability checks.
