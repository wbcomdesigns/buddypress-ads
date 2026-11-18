<?php
/**
 * Help tab: FAQ (informational only).
 *
 * The legacy buddypress-ads-rotator_admin_faq_option was a registered
 * placeholder that was never read, so there is no form here. Native
 * <details>/<summary> accordions replace the legacy custom-JS
 * accordion -- accessible and keyboard-reachable with no script.
 *
 * @package    Buddypress_Ads_Rotator
 * @subpackage Buddypress_Ads_Rotator/includes/admin/views
 * @since      1.6.0
 */

defined( 'ABSPATH' ) || exit;

$wbar_faqs = array(
	array(
		'q' => __( 'Does this plugin require BuddyPress?', 'buddypress-ads-rotator' ),
		'a' => __( 'No. Since version 1.6.0 BuddyPress is optional. Ads work on WordPress posts, pages, the site header and footer, and through the shortcode. When BuddyPress is active with the Activity component on, you also get activity stream ad positions.', 'buddypress-ads-rotator' ),
	),
	array(
		'q' => __( 'What do I do after activating the plugin?', 'buddypress-ads-rotator' ),
		'a' => __( 'Create an ad from the WB Ads menu, pick where it should appear, then publish it.', 'buddypress-ads-rotator' ),
	),
	array(
		'q' => __( 'Where can the ads be displayed?', 'buddypress-ads-rotator' ),
		'a' => __( 'On WordPress posts and pages (before/after content), in the site header and footer, anywhere via the shortcode, and, when BuddyPress is active with the Activity component on, in the sitewide, group and member profile activity streams.', 'buddypress-ads-rotator' ),
	),
	array(
		'q' => __( 'Can I hide a specific ad?', 'buddypress-ads-rotator' ),
		'a' => __( 'Yes. Use the Enable/Disable toggle in the All Ads list to switch any individual ad on or off.', 'buddypress-ads-rotator' ),
	),
	array(
		'q' => __( 'Can I show or hide ads for mobile or desktop?', 'buddypress-ads-rotator' ),
		'a' => __( 'Yes. Each ad has a Device condition so you can target mobile (including tablets), desktop, or both.', 'buddypress-ads-rotator' ),
	),
	array(
		'q' => __( 'Can I show or hide ads for logged-in or logged-out visitors?', 'buddypress-ads-rotator' ),
		'a' => __( 'Yes. Each ad has a visitor condition for logged-in only, logged-out only, or both.', 'buddypress-ads-rotator' ),
	),
	array(
		'q' => __( 'Can I control the ad layout?', 'buddypress-ads-rotator' ),
		'a' => __( 'Yes. You can set the alignment (left, center or right), the margin on each side, and, for rich content ads, the font size and custom colors. Every ad also carries the CSS class wbcomads-{ad ID} for your own styles.', 'buddypress-ads-rotator' ),
	),
);
?>

<div class="wbar-card">
	<div class="wbar-card__head">
		<p class="wbar-card__title"><?php esc_html_e( 'Frequently Asked Questions', 'buddypress-ads-rotator' ); ?></p>
		<p class="wbar-card__desc"><?php esc_html_e( 'Quick answers to the most common questions about BuddyPress Ads.', 'buddypress-ads-rotator' ); ?></p>
	</div>
	<div class="wbar-card__body">
		<?php foreach ( $wbar_faqs as $wbar_faq ) : ?>
			<details class="wbar-faq-item">
				<summary class="wbar-faq-item__q"><?php echo esc_html( $wbar_faq['q'] ); ?></summary>
				<div class="wbar-faq-item__a">
					<p><?php echo esc_html( $wbar_faq['a'] ); ?></p>
				</div>
			</details>
		<?php endforeach; ?>
	</div>
</div>
