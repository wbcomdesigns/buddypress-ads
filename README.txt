=== Wbcom Designs - BuddyPress Ads ===
Contributors: vapvarun, wbcomdesigns
Donate link: https://wbcomdesigns.com
Tags: BuddyPress, ads, advertising, ad rotator, banner
Requires at least: 5.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.6.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Integrate your BuddyPress community to provide a smooth customer experience and increase site engagement.

== Description ==

Do you consider integrating your BuddyPress community to enhance customer satisfaction and encourage more site engagement? You can improve your site's visibility by displaying ads in various BuddyPress locations, such as the activity page, group page, and member page. You can even choose the position of the ads based on your needs.

== Installation ==

1. Upload `buddypress-ads.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Admin overview - published and enabled ad counts, global header/body/footer markup, and quick actions.
2. Discover more free Wbcom Designs community plugins from the admin screen.

== Changelog ==
= 1.6.0 - July 2026 =

Standalone WordPress support plus a rebuilt admin. BuddyPress is now optional.

* New      - Added a Discover tab to the settings screen with more free tools from Wbcom Designs.
* Fix      - Activity stream ads now appear reliably. The BuddyPress integration was skipped whenever this plugin loaded before BuddyPress, which left every activity ad position dead.
* Fix      - An ad with no content can no longer be published. It is kept as a draft with a notice, instead of publishing an ad that renders as an empty space.
* Fix      - Iframe ads inside the activity stream now become visible. The script that moves them into place targeted class names the markup never had.
* Fix      - The Documentation link in the settings screen opened a 404. It now points at the current documentation.
* Improve  - Refactored the plugin to run on standalone WordPress; BuddyPress is now optional, not required.
* Improve  - Rebuilt the settings screen on the new Wbcom card-panel admin (Overview, Scripts / Styles, Welcome, FAQ) with a sidebar layout and shared WB Plugins hub.
* Improve  - Raised admin buttons to a 40px tap target and moved the warning-text color onto a design token.
* Security - Added capability checks (manage_options) to the ad image, image-removal, and enable/disable AJAX handlers.
* Dev      - Removed the legacy admin/wbcom/ wrapper and unused settings partials.
* Dev      - Fixed all WordPress Coding Standards violations and mixed boolean operator precedence issues across all PHP files.

= 1.5.5 =
* Fix: (#66,#68,#69)Fixed ads position issue on activity feed
* Fix: Compatibility check with WordPress 6.5.0

= 1.5.4 =
* Fix: BP v12 Fixes
* Fix: Updated class name and remove dynamic class name

= 1.5.3 =
* Fix: PHPCS fixes

= 1.5.2 =
* Fix: PHPCS fixes
* Fix: (#60) Not processing shortcodes
* Fix: Plugin redirect issue when multiple plugins activate at the same time
* Fix: (#59) Hide admin notice error
* Fix: (#51) First position is wrong

= 1.5.1 =
* Fix- Fixed BuddyBoss admin notice issue
* Fix- (#54) Fixed can not create an image ad
* Fix- Fixed copy shortcode option UI managed 

= 1.5.0 =
* Fix- Hide admin notices
* Fix- Fixed string mistakes
* Fix- Updated admin UI code

= 1.4.0 =
* Fix- (#48) Fixed HTML is not rendering
* Fix- (#49) changed admin ads type description
* Fix- (#43) Added Remove image option for image ads
* Fix- (#44) Fixed image ads URL setting issue
* Fix- (#42) Fixed warning issue displayed on the front-end
* Fix- (#47) Fixed warning issue displaying on single group for logout user

= 1.3.2 =
* Fix- (#41)Fixed Google Ad Issue with other ads type
* Fix- (#34)Fixed when not selected option in activity type, ads not showing
* Fix- (#30)Fixed when select image in Rich Content, it is not showing frontend

= 1.3.1 =
* Fix- Removed install plugin button from wrapper
* Fix- Fixed warning issue

= 1.3.0 =
* Fix- (#28)Fixed Ads are showing repeatedly
* Fix- (#29)changed shortcode text
* Fix- (#27)Fixed google ads script issue with rich content ads
* Fix- Update Code for CSS and JS Loading
* Fix- Load Admin CSS and JS on Plugins Settings Pages
* Fix- Changed Plugin documentation link

= 1.2.0 =
* Fix- js and iframe support

= 1.1.0 =
* Fix- PHPCS Fixes

= 1.0.0 =
* first version.
