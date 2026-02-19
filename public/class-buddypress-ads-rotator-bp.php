<?php
/**
 * BuddyPress-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com
 * @since      1.6.0
 *
 * @package    Buddypress_Ads_Rotator
 * @subpackage Buddypress_Ads_Rotator/public
 */

/**
 * BuddyPress-specific functionality of the plugin.
 *
 * Handles all BuddyPress activity stream ad placements.
 * This class is only loaded when BuddyPress is active.
 *
 * @package    Buddypress_Ads_Rotator
 * @subpackage Buddypress_Ads_Rotator/public
 * @author     WB COM <admin@wbcomdesigns.com>
 */
class Buddypress_Ads_Rotator_BP {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.6.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.6.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Reference to the public class for shared display methods.
	 *
	 * @since    1.6.0
	 * @access   private
	 * @var      Buddypress_Ads_Rotator_Public    $public_class    The public class instance.
	 */
	private $public_class;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.6.0
	 * @param    string                        $plugin_name    The name of the plugin.
	 * @param    string                        $version        The version of this plugin.
	 * @param    Buddypress_Ads_Rotator_Public $public_class   The public class instance.
	 */
	public function __construct( $plugin_name, $version, $public_class ) {
		$this->plugin_name  = $plugin_name;
		$this->version      = $version;
		$this->public_class = $public_class;
	}

	/**
	 * Check if current page is a BuddyPress activity page.
	 *
	 * @since 1.6.0
	 * @return bool True if on a BP activity page.
	 */
	private function is_buddypress_page() {
		if ( ! function_exists( 'bp_is_activity_directory' ) ) {
			return false;
		}
		return bp_is_activity_directory() || bp_is_group() || bp_is_user_activity();
	}

	/**
	 * Display ads before activity entry.
	 *
	 * @since 1.0.0
	 */
	public function wb_ads_display_before_activity_entry() {
		$this->wb_ads_rotator_display_ads( 'bp_before_activity_entry' );
	}

	/**
	 * Display ads in activity entry content.
	 *
	 * @since 1.0.0
	 */
	public function wb_ads_display_activity_entry_content() {
		$this->wb_ads_rotator_display_ads( 'bp_activity_entry_content' );
	}

	/**
	 * Display ads after activity entry.
	 *
	 * @since 1.0.0
	 */
	public function wb_ads_display_after_activity_entry() {
		$this->wb_ads_rotator_display_ads( 'bp_after_activity_entry' );
	}

	/**
	 * Display ads before activity entry comments.
	 *
	 * @since 1.0.0
	 */
	public function wb_ads_display_before_activity_entry_comments() {
		$this->wb_ads_rotator_display_ads( 'bp_before_activity_entry_comments' );
	}

	/**
	 * Display ads in activity entry comments.
	 *
	 * @since 1.0.0
	 */
	public function wb_ads_display_activity_entry_comments() {
		$this->wb_ads_rotator_display_ads( 'bp_activity_entry_comments' );
	}

	/**
	 * Display ads after activity entry comments.
	 *
	 * @since 1.0.0
	 */
	public function wb_ads_display_after_activity_entry_comments() {
		$this->wb_ads_rotator_display_ads( 'bp_after_activity_entry_comments' );
	}

	/**
	 * Display Ads on BuddyPress activity stream.
	 *
	 * @since 1.0.0
	 * @param string $position Ads position.
	 */
	public function wb_ads_rotator_display_ads( $position ) {
		global $activities_template;
		$args                   = array(
			'post_type'  => 'wb-ads',
			'fields'     => 'ids',
			'meta_query' => array(
				array(
					'key'     => 'wb_ads_rotator_values',
					'value'   => $position,
					'compare' => 'LIKE',
				),
			),
		);
		$ads                    = get_posts( $args );
		$wb_ads_rotator_get_ads = ( count( $ads ) > 0 ) ? $ads : array();

		foreach ( $wb_ads_rotator_get_ads as $wb_ads_rotator_get_ad ) {
			$wb_ads_rotator_enable = get_post_meta( $wb_ads_rotator_get_ad, 'wb_ads_enable', true );
			if ( 'disable' === $wb_ads_rotator_enable ) {
				continue;
			}
			$wb_ads_rotator_type              = get_post_meta( $wb_ads_rotator_get_ad, 'wb_ads_rotator_values', true );
			$wb_ads_rotator_enject_position   = isset( $wb_ads_rotator_type['enject_after'] ) ? $wb_ads_rotator_type['enject_after'] : '';
			$wb_ads_rotator_enject_repeat     = isset( $wb_ads_rotator_type['repeat_position'] ) ? $wb_ads_rotator_type['repeat_position'] : '';
			$wb_ads_rotator_activity_type     = isset( $wb_ads_rotator_type['activity_type'] ) ? $wb_ads_rotator_type['activity_type'] : array();
			$wb_ads_rotator_activity_position = isset( $wb_ads_rotator_type['activity_position'] ) ? $wb_ads_rotator_type['activity_position'] : '';
			if ( $position !== $wb_ads_rotator_activity_position ) {
				return;
			}

			// Sitewide activity.
			if ( in_array( 'sidewide_activity', $wb_ads_rotator_activity_type, true ) ) {
				if ( bp_is_activity_directory() ) {
					$this->display_ad_at_position( $wb_ads_rotator_get_ad, $wb_ads_rotator_type, $wb_ads_rotator_activity_position, $wb_ads_rotator_enject_position, $wb_ads_rotator_enject_repeat );
				}
			}

			// Group activity.
			if ( in_array( 'group_activity', $wb_ads_rotator_activity_type, true ) ) {
				if ( function_exists( 'bp_is_group_activity' ) && bp_is_group_activity() ) {
					$this->display_ad_at_position( $wb_ads_rotator_get_ad, $wb_ads_rotator_type, $wb_ads_rotator_activity_position, $wb_ads_rotator_enject_position, $wb_ads_rotator_enject_repeat );
				}
			}

			// Member activity.
			if ( in_array( 'members_activity', $wb_ads_rotator_activity_type, true ) ) {
				if ( bp_is_user_activity() ) {
					$this->display_ad_at_position( $wb_ads_rotator_get_ad, $wb_ads_rotator_type, $wb_ads_rotator_activity_position, $wb_ads_rotator_enject_position, $wb_ads_rotator_enject_repeat );
				}
			}
		}
	}

	/**
	 * Display ad at specific position based on injection settings.
	 *
	 * @since 1.6.0
	 * @param int    $ad_id            Ad post ID.
	 * @param array  $ad_type          Ad type settings.
	 * @param string $activity_position Activity position.
	 * @param int    $enject_position  Inject after position.
	 * @param string $enject_repeat    Whether to repeat.
	 */
	private function display_ad_at_position( $ad_id, $ad_type, $activity_position, $enject_position, $enject_repeat ) {
		$did_action = did_action( $activity_position );

		if ( 'yes' !== $enject_repeat ) {
			if ( $did_action == $enject_position ) {
				$this->render_ad( $ad_id, $ad_type );
			}
		} else {
			if ( $did_action !== $enject_position && 0 !== $did_action % $enject_position ) {
				return;
			}
			$this->render_ad( $ad_id, $ad_type );
		}
	}

	/**
	 * Render the ad based on its type.
	 *
	 * @since 1.6.0
	 * @param int   $ad_id   Ad post ID.
	 * @param array $ad_type Ad type settings.
	 */
	private function render_ad( $ad_id, $ad_type ) {
		switch ( $ad_type['ads_type'] ) {
			case 'plain-text-and-code':
				$this->public_class->wb_ads_rotator_display_plain_text_and_code_ads( $ad_id );
				break;
			case 'rich-content':
				$this->public_class->wb_ads_rotator_display_rich_content_ads( $ad_id );
				break;
			case 'image-ad':
				$this->public_class->wb_ads_rotator_display_image_ads( $ad_id );
				break;
		}
	}

	/**
	 * Display scripts and styles in the header (BuddyPress only).
	 *
	 * @since 1.0.0
	 */
	public function wb_ads_scripts() {
		if ( ! $this->is_buddypress_page() ) {
			return;
		}

		global $activities_template;
		$args                   = array(
			'post_type' => 'wb-ads',
			'fields'    => 'ids',
		);
		$ads                    = get_posts( $args );
		$wb_ads_rotator_get_ads = ( count( $ads ) > 0 ) ? $ads : array();

		foreach ( $wb_ads_rotator_get_ads as $wb_ads_rotator_get_ad ) {
			$wb_ads_rotator_type = get_post_meta( $wb_ads_rotator_get_ad, 'wb_ads_rotator_values', true );
			if ( 'plain-text-and-code' === $wb_ads_rotator_type['ads_type'] ) {
				$wb_ads_rotator_get_values = get_post_meta( $wb_ads_rotator_get_ad, 'wb_ads_rotator_values', true );
				$wb_ads_rotator_enable     = get_post_meta( $wb_ads_rotator_get_ad, 'wb_ads_enable', true );
				if ( 'disable' === $wb_ads_rotator_enable ) {
					return;
				}
				$wb_ads_rotator_get_plain_text = isset( $wb_ads_rotator_get_values['plain_text_and_code'] ) ? $wb_ads_rotator_get_values['plain_text_and_code'] : '';
				preg_match_all( '/<script\b[^>]*>(.*?)<\/script>/is', $wb_ads_rotator_get_plain_text, $scripts );
				preg_match_all( '/<style\b[^>]*>(.*?)<\/style>/is', $wb_ads_rotator_get_plain_text, $styles );
				if ( isset( $scripts[0] ) ) {
					foreach ( $scripts[0] as $key => $script ) {
						echo $script; // phpcs:ignore
					}
				}
				if ( isset( $styles[0] ) ) {
					foreach ( $styles[0] as $key => $style ) {
						echo $style; // phpcs:ignore
					}
				}
			}
		}
		$buddypress_ads_options = get_option( 'buddypress-ads-rotator_admin_scripts_options' );
		$buddypress_ads_header  = ( isset( $buddypress_ads_options['header'] ) ) ? $buddypress_ads_options['header'] : '';
		echo $buddypress_ads_header; // phpcs:ignore
	}

	/**
	 * Display scripts and styles in the footer (BuddyPress only).
	 *
	 * @since 1.0.0
	 */
	public function wb_ads_footer_scripts() {
		if ( ! $this->is_buddypress_page() ) {
			return;
		}

		$buddypress_ads_options = get_option( 'buddypress-ads-rotator_admin_scripts_options' );
		$buddypress_ads_footer  = ( isset( $buddypress_ads_options['footer'] ) ) ? $buddypress_ads_options['footer'] : '';
		echo $buddypress_ads_footer; // phpcs:ignore
	}

	/**
	 * Display scripts and styles in the body (BuddyPress only).
	 *
	 * @since 1.0.0
	 * @param string $wb_ads_content The content.
	 * @return string
	 */
	public function wb_ads_body_scripts( $wb_ads_content ) {
		if ( ! $this->is_buddypress_page() ) {
			return $wb_ads_content;
		}

		$buddypress_ads_options = get_option( 'buddypress-ads-rotator_admin_scripts_options' );
		$buddypress_ads_body    = ( isset( $buddypress_ads_options['body'] ) ) ? $buddypress_ads_options['body'] : '';
		echo wp_unslash( $buddypress_ads_body ); // phpcs:ignore
		return $wb_ads_content;
	}

	/**
	 * Check and display ads for BuddyPress Nouveau template.
	 *
	 * @since 1.0.0
	 */
	public function wb_ads_rotator_check_ads() {
		// Only run on BuddyPress with Nouveau template.
		if ( ! function_exists( 'bp_get_theme_compat_id' ) || 'nouveau' !== bp_get_theme_compat_id() ) {
			return;
		}

		$args = array(
			'post_type' => 'wb-ads',
			'fields'    => 'ids',
		);

		$wb_ads_rotator_get_ads = get_posts( $args );
		echo '<div id="rich-ads">';
		foreach ( $wb_ads_rotator_get_ads as $wb_ads_rotator_get_ad ) {
			$wb_ads_rotator_type = get_post_meta( $wb_ads_rotator_get_ad, 'wb_ads_rotator_values', true );
			$check_ads_position  = isset( $wb_ads_rotator_type['activity_type'] ) ? $wb_ads_rotator_type['activity_type'] : '';
			$check               = false;
			if ( ! empty( $check_ads_position ) ) {
				if ( bp_is_activity_directory() && in_array( 'sidewide_activity', $check_ads_position, true ) ) {
					$check = true;
				}
				if ( bp_is_user_activity() && in_array( 'members_activity', $check_ads_position, true ) ) {
					$check = true;
				}
				if ( function_exists( 'bp_is_group_activity' ) && bp_is_group_activity() && in_array( 'group_activity', $check_ads_position, true ) ) {
					$check = true;
				}
			}
			if ( $check ) {
				switch ( $wb_ads_rotator_type['ads_type'] ) {
					case 'plain-text-and-code':
						$this->public_class->wb_ads_rotator_display_plain_text_and_code_ads( $wb_ads_rotator_get_ad );
						break;
					case 'rich-content':
						$this->public_class->wb_ads_rotator_display_rich_content_ads( $wb_ads_rotator_get_ad );
						break;
				}
			}
		}
		echo '</div>';
	}
}
