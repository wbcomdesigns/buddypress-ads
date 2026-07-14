<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wbcomdesigns.com
 * @since             1.0.0
 * @package           Buddypress_Ads
 *
 * @wordpress-plugin
 * Plugin Name:       Wbcom Designs - BuddyPress Ads
 * Plugin URI:        https://wbcomdesigns.com/downloads/buddypress-ads/
 * Description:       Display ads on your WordPress site with support for posts, pages, header, footer, and BuddyPress activity streams. Works as standalone WordPress or with BuddyPress.
 * Version:           1.6.0
 * Requires at least: 6.9
 * Requires PHP:      7.4
 * Author:            Wbcom Designs
 * Author URI:        https://wbcomdesigns.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buddypress-ads-rotator
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BUDDYPRESS_ADS_ROTATOR_VERSION', '1.6.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-buddypress-ads-rotator-activator.php
 */
function activate_buddypress_ads_rotator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-ads-rotator-activator.php';
	Buddypress_Ads_Rotator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-buddypress-ads-rotator-deactivator.php
 */
function deactivate_buddypress_ads_rotator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-ads-rotator-deactivator.php';
	Buddypress_Ads_Rotator_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_buddypress_ads_rotator' );
register_deactivation_hook( __FILE__, 'deactivate_buddypress_ads_rotator' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-ads-rotator.php';


/**
 * Check if BuddyPress is active.
 *
 * @since 1.6.0
 * @return bool
 */
function wb_ads_rotator_is_buddypress_active() {
	return class_exists( 'BuddyPress' );
}

/**
 * Display admin notice when BuddyPress is not active (informational only).
 *
 * @since 1.6.0
 */
function wb_ads_rotator_buddypress_notice() {
	// Only show on plugin settings page.
	$screen = get_current_screen();
	if ( ! $screen || false === strpos( $screen->id, 'buddypress-ads-rotator' ) ) {
		return;
	}

	if ( ! wb_ads_rotator_is_buddypress_active() ) {
		echo '<div class="notice notice-info"><p>'
		. esc_html__( 'BuddyPress is not active. BuddyPress activity stream ad positions are disabled. The plugin will work with WordPress posts, pages, and shortcodes.', 'buddypress-ads-rotator' )
		. '</p></div>';
	}
}
add_action( 'admin_notices', 'wb_ads_rotator_buddypress_notice' );

/**
 * Redirect to plugin settings page after activated.
 *
 * @since  1.0.0
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 */
function wb_ads_rotator_activation_redirect_settings( $plugin ) {
	if ( plugin_basename( __FILE__ ) === $plugin ) {
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'activate' && isset( $_REQUEST['plugin'] ) && $_REQUEST['plugin'] == $plugin ) { //phpcs:ignore
			wp_safe_redirect( admin_url( 'admin.php?page=buddypress-ads-rotator-settings' ) );
			exit;
		}
	}
	if ( isset( $_REQUEST['plugin'] ) && $plugin == $_REQUEST['plugin'] ) {
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'activate-plugin' && isset( $_REQUEST['plugin'] ) && $_REQUEST['plugin'] == $plugin ) { //phpcs:ignore
			set_transient( '_wb_ads_rotator_is_new_install', true, 30 );
		}
	}
}
add_action( 'activated_plugin', 'wb_ads_rotator_activation_redirect_settings' );

/**
 * Wb_ads_rotator_do_activation_redirect
 *
 * @return void
 */
function wb_ads_rotator_do_activation_redirect() {
	if ( get_transient( '_wb_ads_rotator_is_new_install' ) ) {
		delete_transient( '_wb_ads_rotator_is_new_install' );
		wp_safe_redirect( admin_url( 'admin.php?page=buddypress-ads-rotator-settings' ) );

	}
}
add_action( 'admin_init', 'wb_ads_rotator_do_activation_redirect' );


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_buddypress_ads_rotator() {

	$plugin = new Buddypress_Ads_Rotator();
	$plugin->run();
}
run_buddypress_ads_rotator();
