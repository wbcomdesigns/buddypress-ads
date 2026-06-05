<?php
/**
 * WB Plugins hub -- landing dashboard at ?page=wbcomplugins.
 *
 * Lists every Wbcom plugin that has registered a submenu under the
 * shared wbcomplugins parent. No manual registration needed: peer
 * plugins appear automatically once their admin page is in the
 * WordPress menu table.
 *
 * Legacy wbcom-wrapper plugins register four boilerplate helper pages
 * (Our Plugins, Our Themes, Support, License) under this hub. Those are
 * not real plugins and must not appear as cards -- we filter them out
 * via wbcom_hub_wrapper_helper_slugs.
 * See references/wbcom-wrapper-migration.md Part 16.
 *
 * @package    Buddypress_Ads_Rotator
 * @subpackage Buddypress_Ads_Rotator/includes/admin/views
 * @since      1.6.0
 */

defined( 'ABSPATH' ) || exit;

$submenu_entries = isset( $GLOBALS['submenu']['wbcomplugins'] ) && is_array( $GLOBALS['submenu']['wbcomplugins'] )
	? $GLOBALS['submenu']['wbcomplugins']
	: array();

// Slugs of the legacy wbcom-wrapper boilerplate helper pages. Filterable
// so Pro modules or future wrapper slugs can be added.
$wrapper_helper_slugs = apply_filters(
	'wbcom_hub_wrapper_helper_slugs',
	array(
		'wbcom-plugins-page',
		'wbcom-themes-page',
		'wbcom-support-page',
		'wbcom-license-page',
	)
);

$wbar_plugins = array();
foreach ( $submenu_entries as $entry ) {
	$slug = isset( $entry[2] ) ? (string) $entry[2] : '';
	if ( '' === $slug || 'wbcomplugins' === $slug ) {
		continue;
	}
	if ( in_array( $slug, $wrapper_helper_slugs, true ) ) {
		continue;
	}
	$wbar_plugins[] = array(
		'slug'       => $slug,
		'menu_title' => isset( $entry[0] ) ? wp_strip_all_tags( (string) $entry[0] ) : $slug,
		'page_title' => isset( $entry[3] ) ? wp_strip_all_tags( (string) $entry[3] ) : '',
		'url'        => admin_url( 'admin.php?page=' . rawurlencode( $slug ) ),
	);
}

$plugin_count = count( $wbar_plugins );
?>
<div class="wrap wbar-admin">
	<header class="wbar-page-header">
		<div class="wbar-page-header__title">
			<span class="dashicons dashicons-lightbulb" aria-hidden="true"></span>
			<div>
				<h1><?php esc_html_e( 'WB Plugins', 'buddypress-ads-rotator' ); ?></h1>
				<p class="wbar-page-header__subtitle">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d: active Wbcom plugin count */
							_n(
								'%d Wbcom plugin active on this site.',
								'%d Wbcom plugins active on this site.',
								$plugin_count,
								'buddypress-ads-rotator'
							),
							$plugin_count
						)
					);
					?>
				</p>
			</div>
		</div>
	</header>

	<?php if ( 0 === $plugin_count ) : ?>
		<div class="wbar-empty-state">
			<span class="wbar-empty-state__icon" aria-hidden="true">
				<span class="dashicons dashicons-lightbulb"></span>
			</span>
			<p class="wbar-empty-state__title"><?php esc_html_e( 'No Wbcom plugins attached to this hub yet', 'buddypress-ads-rotator' ); ?></p>
			<p class="wbar-empty-state__desc">
				<?php esc_html_e( 'Activate one or more Wbcom plugins and they will appear here automatically.', 'buddypress-ads-rotator' ); ?>
			</p>
		</div>
	<?php else : ?>
		<div class="wbar-hub-grid">
			<?php foreach ( $wbar_plugins as $wbar_p ) : ?>
				<a href="<?php echo esc_url( $wbar_p['url'] ); ?>" class="wbar-hub-card">
					<span class="wbar-hub-card__icon" aria-hidden="true">
						<span class="dashicons dashicons-admin-plugins"></span>
					</span>
					<span class="wbar-hub-card__title"><?php echo esc_html( $wbar_p['menu_title'] ); ?></span>
					<?php if ( ! empty( $wbar_p['page_title'] ) && $wbar_p['page_title'] !== $wbar_p['menu_title'] ) : ?>
						<span class="wbar-hub-card__subtitle"><?php echo esc_html( $wbar_p['page_title'] ); ?></span>
					<?php endif; ?>
					<span class="wbar-hub-card__cta">
						<?php esc_html_e( 'Open settings', 'buddypress-ads-rotator' ); ?>
						<span class="dashicons dashicons-arrow-right-alt" aria-hidden="true"></span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<div class="wbar-card wbar-hub-about">
		<div class="wbar-card__head">
			<p class="wbar-card__title"><?php esc_html_e( 'About WB Plugins', 'buddypress-ads-rotator' ); ?></p>
		</div>
		<div class="wbar-card__body">
			<p class="wbar-hub-about__lead">
				<?php esc_html_e( 'This hub is the single entry point for every Wbcom Designs plugin installed on your site. Each plugin lives on its own page under this menu and keeps its own settings and data.', 'buddypress-ads-rotator' ); ?>
			</p>
			<p>
				<a href="https://wbcomdesigns.com/" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Visit wbcomdesigns.com for more plugins and themes', 'buddypress-ads-rotator' ); ?>
				</a>
			</p>
		</div>
	</div>
</div>
