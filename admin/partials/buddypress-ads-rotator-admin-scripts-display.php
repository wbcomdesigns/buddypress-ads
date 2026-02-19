<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Ads_Rotator
 * @subpackage Buddypress_Ads_Rotator/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$buddypress_ads_options = get_option( 'buddypress-ads-rotator_admin_scripts_options' );
$buddypress_ads_header  = ( isset( $buddypress_ads_options['header'] ) ) ? $buddypress_ads_options['header'] : '';
$buddypress_ads_body    = ( isset( $buddypress_ads_options['body'] ) ) ? $buddypress_ads_options['body'] : '';
$buddypress_ads_footer  = ( isset( $buddypress_ads_options['footer'] ) ) ? $buddypress_ads_options['footer'] : '';
?>
<div class="wbcom-tab-content">
	<div class="wbcom-wrapper-admin">
		<div class="wbcom-admin-title-section">
			<h3><?php esc_html_e( 'Add Global Scripts/Styles', 'buddypress-ads-rotator' ); ?></h3>
		</div>
		<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">	
			<form method="post" action="options.php">
				<?php
				settings_fields( 'buddypress-ads-rotator_admin_scripts_options' );
				do_settings_sections( 'buddypress-ads-rotator_admin_scripts_options' );
				?>
				<div class="form-table bpchk-admin-page-table">
					
				<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label><?php esc_html_e( 'Header', 'buddypress-ads-rotator' ); ?></label>
							<p class="description"><?php esc_html_e( 'Add Global script and style to the Header of site.', 'buddypress-ads-rotator' ); ?></p>
						</div>
						<div class="wbcom-settings-section-options">
						<textarea id="wb-ad-rotator-header-scripts" name="buddypress-ads-rotator_admin_scripts_options[header]" ><?php echo esc_textarea( $buddypress_ads_header ); ?></textarea>
						</div>
					</div>
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label><?php esc_html_e( 'Body', 'buddypress-ads-rotator' ); ?></label>
							<p class="description"><?php esc_html_e( 'Add Global script and style to the Body of site.', 'buddypress-ads-rotator' ); ?></p>
						</div>
						<div class="wbcom-settings-section-options">
							<textarea id="wb-ad-rotator-body-scripts" name="buddypress-ads-rotator_admin_scripts_options[body]" ><?php echo esc_textarea( $buddypress_ads_body ); ?></textarea>
						</div>
					</div>
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label><?php esc_html_e( 'Footer', 'buddypress-ads-rotator' ); ?></label>
							<p class="description"><?php esc_html_e( 'Add Global script and style to the Footer of site.', 'buddypress-ads-rotator' ); ?></p>
						</div>
						<div class="wbcom-settings-section-options">
							<textarea id="wb-ad-rotator-footer-scripts" name="buddypress-ads-rotator_admin_scripts_options[footer]" ><?php echo esc_textarea( $buddypress_ads_footer ); ?></textarea>
						</div>
					</div>									
				</div>
				<?php submit_button(); ?>
			</form>	
		</div>
	</div>
</div>

