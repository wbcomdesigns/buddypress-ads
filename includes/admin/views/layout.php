<?php
/**
 * Admin page shell: page header, sidebar nav, body slot.
 *
 * Receives from Buddypress_Ads_Rotator_Settings_Admin::render_page():
 *
 * @var array  $wbar_tabs           Tab registry keyed by slug.
 * @var string $active              Active tab slug.
 * @var string $page_url            Base URL (admin.php?page=buddypress-ads-rotator-settings).
 * @var array  $settings            buddypress-ads-rotator_admin_scripts_options option.
 * @var string $view                View slug (e.g. 'overview', 'settings-scripts').
 * @var string $view_path           Absolute path to the partial.
 * @var bool   $in_settings_group   True when the active tab is a Settings tab.
 * @var string $settings_form_group register_setting() group to pass to settings_fields().
 *
 * @package    Buddypress_Ads_Rotator
 * @subpackage Buddypress_Ads_Rotator/includes/admin/views
 * @since      1.6.0
 */

defined( 'ABSPATH' ) || exit;

$version = defined( 'BUDDYPRESS_ADS_ROTATOR_VERSION' ) ? BUDDYPRESS_ADS_ROTATOR_VERSION : '';
?>
<div class="wrap wbar-admin">

	<header class="wbar-page-header">
		<div class="wbar-page-header__title">
			<span class="dashicons dashicons-megaphone" aria-hidden="true"></span>
			<div>
				<h1><?php esc_html_e( 'BuddyPress Ads', 'buddypress-ads-rotator' ); ?></h1>
				<p class="wbar-page-header__subtitle"><?php esc_html_e( 'Display ads across posts, pages, your site header and footer, and BuddyPress activity streams.', 'buddypress-ads-rotator' ); ?></p>
			</div>
		</div>
		<div class="wbar-page-header__actions">
			<?php if ( $version ) : ?>
				<span class="wbar-version-pill">v<?php echo esc_html( $version ); ?></span>
			<?php endif; ?>
		</div>
	</header>

	<?php
	/*
	 * Without this marker, core's common.js re-parents every .notice to
	 * sit right after the first <h1> it finds, which slots the "Settings
	 * saved" banner between our title and its subtitle instead of below
	 * the whole header.
	 */
	?>
	<hr class="wp-header-end">

	<div class="wbar-settings-layout">

		<aside class="wbar-settings-sidebar">
			<div class="wbar-settings-sidebar-brand">
				<span class="wbar-settings-brand-icon" aria-hidden="true">
					<span class="dashicons dashicons-megaphone"></span>
				</span>
				<div class="wbar-settings-brand-text">
					<p class="wbar-settings-brand-name"><?php esc_html_e( 'BuddyPress Ads', 'buddypress-ads-rotator' ); ?></p>
					<p class="wbar-settings-brand-sub"><?php esc_html_e( 'Plugin', 'buddypress-ads-rotator' ); ?></p>
				</div>
			</div>
			<nav class="wbar-settings-sidebar-nav" aria-label="<?php esc_attr_e( 'BuddyPress Ads navigation', 'buddypress-ads-rotator' ); ?>">
				<?php
				$printed_groups = array();
				$group_labels   = array(
					'settings' => esc_html__( 'Settings', 'buddypress-ads-rotator' ),
					'help'     => esc_html__( 'Help', 'buddypress-ads-rotator' ),
					'more'     => esc_html__( 'More', 'buddypress-ads-rotator' ),
				);
				foreach ( $wbar_tabs as $slug => $wbar_tab ) {
					$group = isset( $wbar_tab['group'] ) ? $wbar_tab['group'] : 'main';
					if ( 'main' !== $group && ! in_array( $group, $printed_groups, true ) ) {
						echo '<div class="wbar-snav-divider" role="separator"></div>';
						if ( isset( $group_labels[ $group ] ) ) {
							echo '<p class="wbar-snav-section-label">' . esc_html( $group_labels[ $group ] ) . '</p>';
						}
						$printed_groups[] = $group;
					}
					$classes  = 'wbar-snav-link';
					$classes .= $active === $slug ? ' wbar-snav-link--active' : '';
					echo '<a href="' . esc_url( $page_url . '&tab=' . $slug ) . '" class="' . esc_attr( $classes ) . '">';
					echo '<span class="dashicons ' . esc_attr( $wbar_tab['icon'] ) . '" aria-hidden="true"></span>';
					echo esc_html( $wbar_tab['label'] );
					echo '</a>';
				}
				?>

				<div class="wbar-snav-divider" role="separator"></div>
				<p class="wbar-snav-section-label"><?php esc_html_e( 'Manage Ads', 'buddypress-ads-rotator' ); ?></p>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wb-ads' ) ); ?>" class="wbar-snav-link">
					<span class="dashicons dashicons-list-view" aria-hidden="true"></span>
					<?php esc_html_e( 'All Ads', 'buddypress-ads-rotator' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=wb-ads' ) ); ?>" class="wbar-snav-link">
					<span class="dashicons dashicons-plus-alt" aria-hidden="true"></span>
					<?php esc_html_e( 'Add New Ad', 'buddypress-ads-rotator' ); ?>
				</a>

				<div class="wbar-snav-divider" role="separator"></div>
				<p class="wbar-snav-section-label"><?php esc_html_e( 'Resources', 'buddypress-ads-rotator' ); ?></p>
				<a href="https://wbcomdesigns.com/downloads/buddypress-ads/" class="wbar-snav-link" target="_blank" rel="noopener noreferrer">
					<span class="dashicons dashicons-book" aria-hidden="true"></span>
					<?php esc_html_e( 'Documentation', 'buddypress-ads-rotator' ); ?>
					<span class="dashicons dashicons-external wbar-snav-link__ext" aria-hidden="true"></span>
					<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'buddypress-ads-rotator' ); ?></span>
				</a>
			</nav>
		</aside>

		<div class="wbar-settings-main">
			<?php settings_errors(); ?>
			<?php if ( $in_settings_group ) : ?>
				<form method="post" action="options.php" id="wbar-settings-form">
					<?php settings_fields( $settings_form_group ); ?>
					<?php
					if ( file_exists( $view_path ) ) {
						include $view_path;
					}
					?>
					<div class="wbar-save-bar">
						<?php submit_button( __( 'Save Settings', 'buddypress-ads-rotator' ), 'primary wbar-btn wbar-btn-primary', 'submit', false ); ?>
					</div>
				</form>
			<?php else : ?>
				<?php
				if ( file_exists( $view_path ) ) {
					include $view_path;
				}
				?>
			<?php endif; ?>
		</div>

	</div>
</div>
