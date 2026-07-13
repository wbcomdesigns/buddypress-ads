<?php
/**
 * Help tab: Welcome / support resources (informational only).
 *
 * The legacy wb_ads_rotator_admin_welcome_options option was a
 * registered placeholder that was never read, so there is no form here
 * -- this tab is purely informational.
 *
 * @package    Buddypress_Ads_Rotator
 * @subpackage Buddypress_Ads_Rotator/includes/admin/views
 * @since      1.6.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="wbar-card">
	<div class="wbar-card__head">
		<p class="wbar-card__title"><?php esc_html_e( 'Welcome to BuddyPress Ads', 'buddypress-ads-rotator' ); ?></p>
		<p class="wbar-card__desc"><?php esc_html_e( 'Display ads on your WordPress site -- on posts and pages, in the site header and footer, and across BuddyPress activity streams when BuddyPress is active. Create each ad from the WB Ads menu, choose where it appears, and you are done.', 'buddypress-ads-rotator' ); ?></p>
	</div>
	<div class="wbar-card__body">
		<div class="wbar-action-row">
			<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=wb-ads' ) ); ?>" class="wbar-btn wbar-btn-primary">
				<span class="dashicons dashicons-plus-alt" aria-hidden="true"></span>
				<?php esc_html_e( 'Create Your First Ad', 'buddypress-ads-rotator' ); ?>
			</a>
		</div>
	</div>
</div>

<div class="wbar-card">
	<div class="wbar-card__head">
		<p class="wbar-card__title"><?php esc_html_e( 'Help & Support Resources', 'buddypress-ads-rotator' ); ?></p>
		<p class="wbar-card__desc"><?php esc_html_e( 'Everything you need to get the most out of the plugin. Documentation is the best place to start.', 'buddypress-ads-rotator' ); ?></p>
	</div>
	<div class="wbar-card__body">
		<div class="wbar-resource-grid">
			<div class="wbar-resource">
				<h3><span class="dashicons dashicons-book" aria-hidden="true"></span><?php esc_html_e( 'Documentation', 'buddypress-ads-rotator' ); ?></h3>
				<p><?php esc_html_e( 'A complete guide covering every aspect of BuddyPress Ads. Most answers live here.', 'buddypress-ads-rotator' ); ?></p>
				<a href="https://docs.wbcomdesigns.com/docs/buddypress-ads/" class="wbar-btn wbar-btn-secondary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Read Documentation', 'buddypress-ads-rotator' ); ?></a>
			</div>
			<div class="wbar-resource">
				<h3><span class="dashicons dashicons-sos" aria-hidden="true"></span><?php esc_html_e( 'Support Center', 'buddypress-ads-rotator' ); ?></h3>
				<p><?php esc_html_e( 'Stuck on something? Our support team is ready to help.', 'buddypress-ads-rotator' ); ?></p>
				<a href="https://wbcomdesigns.com/support/" class="wbar-btn wbar-btn-secondary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Get Support', 'buddypress-ads-rotator' ); ?></a>
			</div>
			<div class="wbar-resource">
				<h3><span class="dashicons dashicons-admin-comments" aria-hidden="true"></span><?php esc_html_e( 'Got Feedback?', 'buddypress-ads-rotator' ); ?></h3>
				<p><?php esc_html_e( 'Tell us about your experience and what you would like to see next.', 'buddypress-ads-rotator' ); ?></p>
				<a href="https://wbcomdesigns.com/contact/" class="wbar-btn wbar-btn-secondary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Send Feedback', 'buddypress-ads-rotator' ); ?></a>
			</div>
		</div>
	</div>
</div>
