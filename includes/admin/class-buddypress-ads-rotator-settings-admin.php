<?php
/**
 * BuddyPress Ads settings admin: menu, enqueue, page rendering.
 *
 * Replaces the legacy Wbcom wrapper chrome (admin/wbcom/). Follows the
 * wp-plugin-development skill Part 6 card-panel pattern and the
 * references/wbcom-wrapper-migration.md playbook (Parts 5, 6, 15).
 *
 * This class owns ONLY the BuddyPress Ads settings page (Overview,
 * Scripts/Styles, Welcome, FAQ). The wb-ads CPT, its metaboxes, list
 * columns, save handler and metabox AJAX stay in
 * Buddypress_Ads_Rotator_Admin (the retained legacy service class).
 *
 * @package    Buddypress_Ads_Rotator
 * @subpackage Buddypress_Ads_Rotator/includes/admin
 * @since      1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Buddypress_Ads_Rotator_Settings_Admin
 *
 * @since 1.6.0
 */
class Buddypress_Ads_Rotator_Settings_Admin {

	/**
	 * Menu slug (kept identical to legacy so bookmarks still work).
	 */
	const MENU_SLUG = 'buddypress-ads-rotator-settings';

	/**
	 * The only option this page actually reads/writes: the global
	 * header/body/footer scripts + styles. Group name == option name,
	 * preserved byte-for-byte from the legacy Settings-API page.
	 */
	const OPTION_GROUP = 'buddypress-ads-rotator_admin_scripts_options';
	const OPTION_NAME  = 'buddypress-ads-rotator_admin_scripts_options';

	/**
	 * Sidebar tabs rendered inside the one admin page.
	 *
	 * @return array<string, array{label:string, icon:string, group:string}>
	 */
	public static function get_tabs() {
		$tabs = array(
			'overview' => array(
				'label' => __( 'Overview', 'buddypress-ads-rotator' ),
				'icon'  => 'dashicons-chart-bar',
				'group' => 'main',
			),
			'scripts'  => array(
				'label' => __( 'Scripts / Styles', 'buddypress-ads-rotator' ),
				'icon'  => 'dashicons-editor-code',
				'group' => 'settings',
			),
			'welcome'  => array(
				'label' => __( 'Welcome', 'buddypress-ads-rotator' ),
				'icon'  => 'dashicons-megaphone',
				'group' => 'help',
			),
			'faq'      => array(
				'label' => __( 'FAQ', 'buddypress-ads-rotator' ),
				'icon'  => 'dashicons-editor-help',
				'group' => 'help',
			),
		);
		return apply_filters( 'buddypress_ads_rotator_admin_tabs', $tabs );
	}

	/**
	 * Bootstrap hooks.
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		// Priority 999 reclaims the shared hub landing render when a legacy
		// wbcom-wrapper plugin registered wbcomplugins first. See playbook
		// Part 16.
		add_action( 'admin_menu', array( $this, 'takeover_hub_landing' ), 999 );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'in_admin_header', array( $this, 'suppress_foreign_notices' ), 1 );
	}

	/**
	 * Attach as a single submenu under the shared WB Plugins hub.
	 */
	public function add_menu() {
		$cap = 'manage_options';

		// First Wbcom plugin to load creates the shared WB Plugins hub.
		if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
			add_menu_page(
				esc_html__( 'WB Plugins', 'buddypress-ads-rotator' ),
				esc_html__( 'WB Plugins', 'buddypress-ads-rotator' ),
				$cap,
				'wbcomplugins',
				array( $this, 'render_hub' ),
				'dashicons-lightbulb',
				59
			);
		}

		add_submenu_page(
			'wbcomplugins',
			esc_html__( 'BuddyPress Ads', 'buddypress-ads-rotator' ),
			esc_html__( 'BuddyPress Ads', 'buddypress-ads-rotator' ),
			$cap,
			self::MENU_SLUG,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Force the shared hub landing to render our card-panel dashboard even
	 * when a legacy wbcom-wrapper plugin registered wbcomplugins first.
	 * See references/wbcom-wrapper-migration.md Part 16.
	 */
	public function takeover_hub_landing() {
		global $admin_page_hooks;
		if ( empty( $admin_page_hooks['wbcomplugins'] ) ) {
			return;
		}
		remove_all_actions( 'toplevel_page_wbcomplugins' );
		add_action( 'toplevel_page_wbcomplugins', array( $this, 'render_hub' ) );
	}

	/**
	 * Register the scripts/styles option. Group + name are preserved
	 * verbatim from the legacy page so saved data is unaffected. The
	 * sanitizer mirrors the legacy wp_kses_post-per-key contract exactly.
	 */
	public function register_settings() {
		register_setting(
			self::OPTION_GROUP,
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_scripts' ),
				'default'           => array(
					'header' => '',
					'body'   => '',
					'footer' => '',
				),
			)
		);
	}

	/**
	 * Sanitize the header/body/footer ad markup. wp_kses_post per key,
	 * identical to the legacy inline sanitizer (admin class :246-256).
	 *
	 * @param mixed $input Raw form input.
	 * @return array{header:string, body:string, footer:string}
	 */
	public function sanitize_scripts( $input ) {
		if ( ! is_array( $input ) ) {
			return array(
				'header' => '',
				'body'   => '',
				'footer' => '',
			);
		}
		return array(
			'header' => isset( $input['header'] ) ? wp_kses_post( $input['header'] ) : '',
			'body'   => isset( $input['body'] ) ? wp_kses_post( $input['body'] ) : '',
			'footer' => isset( $input['footer'] ) ? wp_kses_post( $input['footer'] ) : '',
		);
	}

	/**
	 * Enqueue admin assets only on our screen + the shared hub landing.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 */
	public function enqueue_assets( $hook_suffix ) {
		unset( $hook_suffix );
		$screen = get_current_screen();
		if ( ! $screen || ! $this->is_our_screen( $screen ) ) {
			return;
		}

		$version = defined( 'BUDDYPRESS_ADS_ROTATOR_VERSION' ) ? BUDDYPRESS_ADS_ROTATOR_VERSION : '1.0.0';
		$base    = BUDDYPRESS_ADS_ROTATOR_URL;

		wp_enqueue_style(
			'buddypress-ads-rotator-admin-panel',
			$base . 'assets/css/admin.css',
			array(),
			$version
		);

		// CodeMirror for the Scripts/Styles textareas (same as legacy).
		$cm_settings['codeEditor'] = wp_enqueue_code_editor( array( 'type' => 'text/html' ) );
		wp_localize_script( 'jquery', 'cm_settings', $cm_settings );
		wp_enqueue_script( 'wp-theme-plugin-editor' );
		wp_enqueue_style( 'wp-codemirror' );

		wp_enqueue_script(
			'buddypress-ads-rotator-admin-panel',
			$base . 'assets/js/admin.js',
			array( 'jquery' ),
			$version,
			true
		);

		wp_localize_script(
			'buddypress-ads-rotator-admin-panel',
			'wbarAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'i18n'    => array(
					'saved'           => __( 'Settings saved.', 'buddypress-ads-rotator' ),
					'saveFailed'      => __( 'Could not save. Please try again.', 'buddypress-ads-rotator' ),
					'confirmDanger'   => __( 'Are you sure? This cannot be undone.', 'buddypress-ads-rotator' ),
					'confirmContinue' => __( 'Continue', 'buddypress-ads-rotator' ),
					'confirmCancel'   => __( 'Cancel', 'buddypress-ads-rotator' ),
				),
			)
		);
	}

	/**
	 * Suppress 3rd-party admin notices on our screen.
	 */
	public function suppress_foreign_notices() {
		$screen = get_current_screen();
		if ( ! $screen || ! $this->is_our_screen( $screen ) ) {
			return;
		}
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );
	}

	/**
	 * True if the current screen is the plugin settings page OR the shared
	 * hub landing (our callback owns the hub render too, so our CSS/JS must
	 * load there).
	 *
	 * @param WP_Screen $screen Current screen.
	 * @return bool
	 */
	private function is_our_screen( $screen ) {
		if ( empty( $screen->id ) ) {
			return false;
		}
		return (bool) preg_match( '/_page_' . preg_quote( self::MENU_SLUG, '/' ) . '$/', $screen->id )
			|| 'toplevel_page_wbcomplugins' === $screen->id;
	}

	/**
	 * Render the shared WB Plugins hub landing page.
	 */
	public function render_hub() {
		$view = BUDDYPRESS_ADS_ROTATOR_PATH . 'includes/admin/views/hub.php';
		if ( file_exists( $view ) ) {
			include $view;
		}
	}

	/**
	 * Render the single admin page. Routes to the active tab.
	 */
	public function render_page() {
		$wbar_tabs = self::get_tabs();
		$tab_slugs = array_keys( $wbar_tabs );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only tab switch.
		$active = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : $tab_slugs[0];
		if ( ! isset( $wbar_tabs[ $active ] ) ) {
			$active = $tab_slugs[0];
		}

		$page_url = admin_url( 'admin.php?page=' . self::MENU_SLUG );
		$settings = get_option( self::OPTION_NAME, array() );

		$view_map = array(
			'overview' => 'overview',
			'scripts'  => 'settings-scripts',
			'welcome'  => 'welcome',
			'faq'      => 'faq',
		);

		$view                = isset( $view_map[ $active ] ) ? $view_map[ $active ] : 'overview';
		$in_settings_group   = isset( $wbar_tabs[ $active ]['group'] ) && 'settings' === $wbar_tabs[ $active ]['group'];
		$settings_form_group = self::OPTION_GROUP;

		$view_path = BUDDYPRESS_ADS_ROTATOR_PATH . 'includes/admin/views/' . $view . '.php';
		$shell     = BUDDYPRESS_ADS_ROTATOR_PATH . 'includes/admin/views/shell.php';

		include $shell;
	}
}
