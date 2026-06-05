<?php
/**
 * Settings tab: Global Scripts / Styles.
 *
 * Wrapped by shell.php inside <form action="options.php"> with
 * settings_fields( 'buddypress-ads-rotator_admin_scripts_options' )
 * already emitted, so this view only renders the field cards. The
 * option name + sub-keys (header/body/footer) are preserved verbatim
 * from the legacy partial so saved data is unaffected.
 *
 * @package    Buddypress_Ads_Rotator
 * @subpackage Buddypress_Ads_Rotator/includes/admin/views
 * @since      1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Parent view provides $settings.
 *
 * @var array $settings buddypress-ads-rotator_admin_scripts_options.
 */

$wbar_header = isset( $settings['header'] ) ? $settings['header'] : '';
$wbar_body   = isset( $settings['body'] ) ? $settings['body'] : '';
$wbar_footer = isset( $settings['footer'] ) ? $settings['footer'] : '';
?>

<div class="wbar-card">
	<div class="wbar-card__head">
		<p class="wbar-card__title"><?php esc_html_e( 'Global Scripts & Styles', 'buddypress-ads-rotator' ); ?></p>
		<p class="wbar-card__desc"><?php esc_html_e( 'Add custom HTML, scripts, or styles that load on every page of your site. Useful for ad-network verification tags, global tracking, or shared styling for your ads.', 'buddypress-ads-rotator' ); ?></p>
	</div>
	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="wbar-scripts-header"><?php esc_html_e( 'Header', 'buddypress-ads-rotator' ); ?></label>
			</th>
			<td>
				<textarea id="wbar-scripts-header" class="large-text code" rows="6" name="buddypress-ads-rotator_admin_scripts_options[header]"><?php echo esc_textarea( $wbar_header ); ?></textarea>
				<p class="description"><?php esc_html_e( 'Injected into the site header (wp_head). Good for verification tags and global styles.', 'buddypress-ads-rotator' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="wbar-scripts-body"><?php esc_html_e( 'Body', 'buddypress-ads-rotator' ); ?></label>
			</th>
			<td>
				<textarea id="wbar-scripts-body" class="large-text code" rows="6" name="buddypress-ads-rotator_admin_scripts_options[body]"><?php echo esc_textarea( $wbar_body ); ?></textarea>
				<p class="description"><?php esc_html_e( 'Injected near the top of the page body, after the opening content.', 'buddypress-ads-rotator' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="wbar-scripts-footer"><?php esc_html_e( 'Footer', 'buddypress-ads-rotator' ); ?></label>
			</th>
			<td>
				<textarea id="wbar-scripts-footer" class="large-text code" rows="6" name="buddypress-ads-rotator_admin_scripts_options[footer]"><?php echo esc_textarea( $wbar_footer ); ?></textarea>
				<p class="description"><?php esc_html_e( 'Injected into the site footer (wp_footer). Good for deferred scripts.', 'buddypress-ads-rotator' ); ?></p>
			</td>
		</tr>
	</table>
</div>
