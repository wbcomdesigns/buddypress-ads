<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Ads_Rotator
 * @subpackage Buddypress_Ads_Rotator/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Buddypress_Ads_Rotator
 * @subpackage Buddypress_Ads_Rotator/public
 * @author     WB COM <admin@wbcomdesigns.com>
 */
class Buddypress_Ads_Rotator_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Ads_Rotator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Ads_Rotator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/buddypress-ads-rotator-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Ads_Rotator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Ads_Rotator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-ads-rotator-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Display Ads on buddypress.
	 */
	public function wb_ads_rotator_display_ads() {
		global $activities_template;
		$args = array(
			'post_type' => 'wb-ads',
			'fields'    => 'ids',
		);

		$wb_ads_rotator_get_ads = get_posts( $args );
		foreach ( $wb_ads_rotator_get_ads as $wb_ads_rotator_get_ad ) {
			$wb_ads_rotator_type              = get_post_meta( $wb_ads_rotator_get_ad, 'wb_ads_rotator_values', true );
			$wb_ads_rotator_enject_position   = isset( $wb_ads_rotator_type['enject_after'] ) ? $wb_ads_rotator_type['enject_after'] : '';
			$wb_ads_rotator_enject_repeat     = isset( $wb_ads_rotator_type['repeat_position'] ) ? $wb_ads_rotator_type['repeat_position'] : '';
			$wb_ads_rotator_activity_type     = isset( $wb_ads_rotator_type['activity_type'] ) ? $wb_ads_rotator_type['activity_type'] : '';
			$wb_ads_rotator_activity_position = isset( $wb_ads_rotator_type['activity_position'] ) ? $wb_ads_rotator_type['activity_position'] : '';
			if ( 'sidewide_activity' === $wb_ads_rotator_activity_type ) {
				if ( ! function_exists( 'bp_is_activity_directory' ) || bp_is_activity_directory() ) {
					if ( 'yes' !== $wb_ads_rotator_enject_repeat ) {
						if ( $activities_template->current_activity == $wb_ads_rotator_enject_position ) {
							switch ( $wb_ads_rotator_type['ads_type'] ) {
								case 'plain-text-and-code':
									$this->wb_ads_rotator_display_plain_text_and_code_ads( $wb_ads_rotator_get_ad );
									break;
								case 'rich-content':
									$this->wb_ads_rotator_display_rich_content_ads( $wb_ads_rotator_get_ad );
									break;
								case 'image-ad':
									$this->wb_ads_rotator_display_image_ads( $wb_ads_rotator_get_ad );
									break;
							}
						}
					} else {
						$did_action = did_action( $wb_ads_rotator_activity_position );
						if ( $did_action !== $wb_ads_rotator_enject_position && 0 !== $did_action % $wb_ads_rotator_enject_position ) {
							continue;
						}
						switch ( $wb_ads_rotator_type['ads_type'] ) {
							case 'plain-text-and-code':
								$this->wb_ads_rotator_display_plain_text_and_code_ads( $wb_ads_rotator_get_ad );
								break;
							case 'rich-content':
								$this->wb_ads_rotator_display_rich_content_ads( $wb_ads_rotator_get_ad );
								break;
							case 'image-ad':
								$this->wb_ads_rotator_display_image_ads( $wb_ads_rotator_get_ad );
								break;
						}
					}
				}
			} elseif ( 'group_activity' === $wb_ads_rotator_activity_type ) {
				if ( ! function_exists( 'bp_is_group_activity' ) || bp_is_group_activity() ) {
					if ( 'yes' !== $wb_ads_rotator_enject_repeat ) {
						if ( $activities_template->current_activity == $wb_ads_rotator_enject_position ) {
							switch ( $wb_ads_rotator_type['ads_type'] ) {
								case 'plain-text-and-code':
									$this->wb_ads_rotator_display_plain_text_and_code_ads( $wb_ads_rotator_get_ad );
									break;
								case 'rich-content':
									$this->wb_ads_rotator_display_rich_content_ads( $wb_ads_rotator_get_ad );
									break;
								case 'image-ad':
									$this->wb_ads_rotator_display_image_ads( $wb_ads_rotator_get_ad );
									break;
							}
						}
					} else {
						$did_action = did_action( $wb_ads_rotator_activity_position );
						if ( $did_action !== $wb_ads_rotator_enject_position && 0 !== $did_action % $wb_ads_rotator_enject_position ) {
							continue;
						}
						switch ( $wb_ads_rotator_type['ads_type'] ) {
							case 'plain-text-and-code':
								$this->wb_ads_rotator_display_plain_text_and_code_ads( $wb_ads_rotator_get_ad );
								break;
							case 'rich-content':
								$this->wb_ads_rotator_display_rich_content_ads( $wb_ads_rotator_get_ad );
								break;
							case 'image-ad':
								$this->wb_ads_rotator_display_image_ads( $wb_ads_rotator_get_ad );
								break;
						}
					}
				}
			} elseif ( 'members_activity' === $wb_ads_rotator_activity_type ) {
				if ( ! function_exists( 'bp_is_user_activity' ) || bp_is_user_activity() ) {
					if ( 'yes' !== $wb_ads_rotator_enject_repeat ) {
						if ( $activities_template->current_activity == $wb_ads_rotator_enject_position ) {
							switch ( $wb_ads_rotator_type['ads_type'] ) {
								case 'plain-text-and-code':
									$this->wb_ads_rotator_display_plain_text_and_code_ads( $wb_ads_rotator_get_ad );
									break;
								case 'rich-content':
									$this->wb_ads_rotator_display_rich_content_ads( $wb_ads_rotator_get_ad );
									break;
								case 'image-ad':
									$this->wb_ads_rotator_display_image_ads( $wb_ads_rotator_get_ad );
									break;
							}
						}
					} else {
						$did_action = did_action( $wb_ads_rotator_activity_position );
						if ( $did_action !== $wb_ads_rotator_enject_position && 0 !== $did_action % $wb_ads_rotator_enject_position ) {
							continue;
						}
						switch ( $wb_ads_rotator_type['ads_type'] ) {
							case 'plain-text-and-code':
								$this->wb_ads_rotator_display_plain_text_and_code_ads( $wb_ads_rotator_get_ad );
								break;
							case 'rich-content':
								$this->wb_ads_rotator_display_rich_content_ads( $wb_ads_rotator_get_ad );
								break;
							case 'image-ad':
								$this->wb_ads_rotator_display_image_ads( $wb_ads_rotator_get_ad );
								break;
						}
					}
				}
			}
		}

	}

	/**
	 * Display plain text and code type ads.
	 *
	 * @param int $wb_ads_rotator_get_ad Get Ads ID.
	 */
	public function wb_ads_rotator_display_plain_text_and_code_ads( $wb_ads_rotator_get_ad ) {

		$wb_ads_rotator_get_values = get_post_meta( $wb_ads_rotator_get_ad, 'wb_ads_rotator_values', true );
		$wb_ads_rotator_enable     = get_post_meta( $wb_ads_rotator_get_ad, 'wb_ads_enable', true );

		if ( 'disable' === $wb_ads_rotator_enable ) {
			return;
		}
		switch ( $wb_ads_rotator_get_values['ads_position'] ) {
			case 'default':
				$wb_ads_position = 'initial';
				break;
			case 'left':
				$wb_ads_position = 'left';
				break;
			case 'center':
				$wb_ads_position = 'center';
				break;
			case 'right':
				$wb_ads_position = 'right';
				break;
		}

		$wb_ads_rotator_get_plain_text  = isset( $wb_ads_rotator_get_values['plain_text_and_code'] ) ? $wb_ads_rotator_get_values['plain_text_and_code'] : '';
		$wb_ads_rotator_container_class = isset( $wb_ads_rotator_get_values['container_classes'] ) ? $wb_ads_rotator_get_values['container_classes'] : '';
		$wb_ads_rotator_container_id    = isset( $wb_ads_rotator_get_values['container_id'] ) ? $wb_ads_rotator_get_values['container_id'] : '';
		$wb_ads_visitor_view            = isset( $wb_ads_rotator_get_values['to_whom']['logged_in_visitor'] ) ? $wb_ads_rotator_get_values['to_whom']['logged_in_visitor'] : '';
		$wb_ads_visitor_device          = isset( $wb_ads_rotator_get_values['to_whom']['device'] ) ? $wb_ads_rotator_get_values['to_whom']['device'] : '';
		$wb_ads_rotator_margin_top      = isset( $wb_ads_rotator_get_values['margin']['top'] ) ? $wb_ads_rotator_get_values['margin']['top'] : '';
		$wb_ads_rotator_margin_right    = isset( $wb_ads_rotator_get_values['margin']['right'] ) ? $wb_ads_rotator_get_values['margin']['right'] : '';
		$wb_ads_rotator_margin_bottom   = isset( $wb_ads_rotator_get_values['margin']['bottom'] ) ? $wb_ads_rotator_get_values['margin']['bottom'] : '';
		$wb_ads_rotator_margin_left     = isset( $wb_ads_rotator_get_values['margin']['left'] ) ? $wb_ads_rotator_get_values['margin']['left'] : '';
		$wb_ads_rotator_clearfix        = isset( $wb_ads_rotator_get_values['clearfix'] ) ? $wb_ads_rotator_get_values['clearfix'] : '';
		$wb_ads_rotator_allow_php       = isset( $wb_ads_rotator_get_values['allow_php'] ) ? $wb_ads_rotator_get_values['allow_php'] : '';
		$wb_ads_rotator_allow_shortcode = isset( $wb_ads_rotator_get_values['allow_shortcode'] ) ? $wb_ads_rotator_get_values['allow_shortcode'] : '';

		if ( empty( $wb_ads_rotator_container_id ) ) {
			$wb_ads_rotator_container_id = 'wb_ads_rotator_' . $wb_ads_rotator_get_ad;
		}
		if ( ! is_user_logged_in() && 'login_in' === $wb_ads_visitor_view ) {
			return;
		} elseif ( is_user_logged_in() && 'logout_out' === $wb_ads_visitor_view ) {
			return;
		}

		if ( 'desktop' === $wb_ads_visitor_device ) {
			if ( wp_is_mobile() ) {
				return;
			}
		} elseif ( 'mobile' === $wb_ads_visitor_device ) {
			if ( ! wp_is_mobile() ) {
				return;
			}
		}

		echo '<div class="wb-ads-rotator plain-text-and-code-ads ' . esc_attr( $wb_ads_rotator_container_class ) . '" style=" text-align:' . esc_attr( $wb_ads_position ) . '; margin-top:' . esc_attr( $wb_ads_rotator_margin_top ) . 'px; margin-right:' . esc_attr( $wb_ads_rotator_margin_right ) . 'px; margin-bottom:' . esc_attr( $wb_ads_rotator_margin_bottom ) . 'px;margin-left:' . esc_attr( $wb_ads_rotator_margin_left ) . 'px; " id="' . esc_attr( $wb_ads_rotator_container_id ) . '">';
		if ( 'yes' === $wb_ads_rotator_allow_php && 'yes' === $wb_ads_rotator_allow_shortcode ) {
			ob_start();
			echo wp_kses_post( apply_filters( 'wb_ads_rotator_plain_text_code_ads_content', eval( '?>' . $wb_ads_rotator_get_plain_text ) ) ); // phpcs:ignore
			$wb_ads_rotator_get_plain_text = ob_get_clean();
			echo do_shortcode( $wb_ads_rotator_get_plain_text );
		} elseif ( 'yes' === $wb_ads_rotator_allow_php ) {
			echo wp_kses_post( apply_filters( 'wb_ads_rotator_plain_text_code_ads_content', eval( '?>' . $wb_ads_rotator_get_plain_text ) ) ); // phpcs:ignore
		} elseif ( 'yes' === $wb_ads_rotator_allow_shortcode ) {
			echo do_shortcode( $wb_ads_rotator_get_plain_text );
		} else {
			echo wp_kses_post( apply_filters( 'wb_ads_rotator_plain_text_code_ads_content', $wb_ads_rotator_get_plain_text ) );
		}

		echo '</div>';
		if ( 'yes' === $wb_ads_rotator_clearfix ) {
			echo '<br style="clear: both; display: block; float: none;">';
		}

	}

	/**
	 * Display rich content type ads.
	 *
	 * @param int $wb_ads_rotator_get_ad Get Ads ID.
	 */
	public function wb_ads_rotator_display_rich_content_ads( $wb_ads_rotator_get_ad ) {
		$wb_ads_rotator_get_values = get_post_meta( $wb_ads_rotator_get_ad, 'wb_ads_rotator_values', true );
		$wb_ads_rotator_enable     = get_post_meta( $wb_ads_rotator_get_ad, 'wb_ads_enable', true );
		if ( 'disable' === $wb_ads_rotator_enable ) {
			return;
		}

		switch ( $wb_ads_rotator_get_values['ads_position'] ) {
			case 'default':
				$wb_ads_position = 'initial';
				break;
			case 'left':
				$wb_ads_position = 'left';
				break;
			case 'center':
				$wb_ads_position = 'center';
				break;
			case 'right':
				$wb_ads_position = 'right';
				break;
		}

		$wb_ads_rotator_get_rich_content = isset( $wb_ads_rotator_get_values['rich_content'] ) ? $wb_ads_rotator_get_values['rich_content'] : '';
		$wb_ads_rotator_container_class  = isset( $wb_ads_rotator_get_values['container_classes'] ) ? $wb_ads_rotator_get_values['container_classes'] : '';
		$wb_ads_rotator_container_id     = isset( $wb_ads_rotator_get_values['container_id'] ) ? $wb_ads_rotator_get_values['container_id'] : '';
		$wb_ads_visitor_view             = isset( $wb_ads_rotator_get_values['to_whom']['logged_in_visitor'] ) ? $wb_ads_rotator_get_values['to_whom']['logged_in_visitor'] : '';
		$wb_ads_visitor_device           = isset( $wb_ads_rotator_get_values['to_whom']['device'] ) ? $wb_ads_rotator_get_values['to_whom']['device'] : '';
		$wb_ads_rotator_margin_top       = isset( $wb_ads_rotator_get_values['margin']['top'] ) ? $wb_ads_rotator_get_values['margin']['top'] : '';
		$wb_ads_rotator_margin_right     = isset( $wb_ads_rotator_get_values['margin']['right'] ) ? $wb_ads_rotator_get_values['margin']['right'] : '';
		$wb_ads_rotator_margin_bottom    = isset( $wb_ads_rotator_get_values['margin']['bottom'] ) ? $wb_ads_rotator_get_values['margin']['bottom'] : '';
		$wb_ads_rotator_margin_left      = isset( $wb_ads_rotator_get_values['margin']['left'] ) ? $wb_ads_rotator_get_values['margin']['left'] : '';
		$wb_ads_rotator_clearfix         = isset( $wb_ads_rotator_get_values['clearfix'] ) ? $wb_ads_rotator_get_values['clearfix'] : '';
		$wb_ads_rotator_font_size        = isset( $wb_ads_rotator_get_values['font_size'] ) ? $wb_ads_rotator_get_values['font_size'] : '';
		$wb_ads_rotator_txt_color        = isset( $wb_ads_rotator_get_values['text_color'] ) ? $wb_ads_rotator_get_values['text_color'] : '';
		$wb_ads_rotator_bg_color         = isset( $wb_ads_rotator_get_values['bg_color'] ) ? $wb_ads_rotator_get_values['bg_color'] : '';

		if ( empty( $wb_ads_rotator_container_id ) ) {
			$wb_ads_rotator_container_id = 'wb_ads_rotator_' . $wb_ads_rotator_get_ad;
		}
		if ( ! is_user_logged_in() && 'login_in' === $wb_ads_visitor_view ) {
			return;
		} elseif ( is_user_logged_in() && 'logout_out' === $wb_ads_visitor_view ) {
			return;
		}

		if ( 'desktop' === $wb_ads_visitor_device ) {
			if ( wp_is_mobile() ) {
				return;
			}
		} elseif ( 'mobile' === $wb_ads_visitor_device ) {
			if ( ! wp_is_mobile() ) {
				return;
			}
		}
		echo '<div class="wb-ads-rotator rich-content-ads ' . esc_attr( $wb_ads_rotator_container_class ) . '" style="background-color:' . esc_attr( $wb_ads_rotator_bg_color ) . ';color:' . esc_attr( $wb_ads_rotator_txt_color ) . ';font-size:' . esc_attr( $wb_ads_rotator_font_size ) . 'px;text-align:' . esc_attr( $wb_ads_position ) . ';margin-top:' . esc_attr( $wb_ads_rotator_margin_top ) . 'px; margin-right:' . esc_attr( $wb_ads_rotator_margin_right ) . 'px; margin-bottom:' . esc_attr( $wb_ads_rotator_margin_bottom ) . 'px;margin-left:' . esc_attr( $wb_ads_rotator_margin_left ) . 'px; " id="' . esc_attr( $wb_ads_rotator_container_id ) . '">';
		echo wp_kses_post( apply_filters( 'wb_ads_rotator_rich_content_ads_content', $wb_ads_rotator_get_rich_content ) );
		echo '</div>';
		if ( 'yes' === $wb_ads_rotator_clearfix ) {
			echo '<br style="clear: both; display: block; float: none;">';
		}
	}

	/**
	 * Display image ad type ads.
	 *
	 * @param int $wb_ads_rotator_get_ad Get Ads ID.
	 */
	public function wb_ads_rotator_display_image_ads( $wb_ads_rotator_get_ad ) {
		$wb_ads_rotator_get_values = get_post_meta( $wb_ads_rotator_get_ad, 'wb_ads_rotator_values', true );
		$wb_ads_rotator_enable     = get_post_meta( $wb_ads_rotator_get_ad, 'wb_ads_enable', true );
		if ( 'disable' === $wb_ads_rotator_enable ) {
			return;
		}

		switch ( $wb_ads_rotator_get_values['ads_position'] ) {
			case 'default':
				$wb_ads_position = 'initial';
				break;
			case 'left':
				$wb_ads_position = '-webkit-left';
				break;
			case 'center':
				$wb_ads_position = '-webkit-center';
				break;
			case 'right':
				$wb_ads_position = '-webkit-right';
				break;
		}

		$wb_ads_rotator_get_image_id    = isset( $wb_ads_rotator_get_values['ads_image'] ) ? $wb_ads_rotator_get_values['ads_image'] : '';
		$wb_ads_rotator_get_image_link  = isset( $wb_ads_rotator_get_values['image_link'] ) ? $wb_ads_rotator_get_values['image_link'] : '';
		$wb_ads_rotator_container_class = isset( $wb_ads_rotator_get_values['container_classes'] ) ? $wb_ads_rotator_get_values['container_classes'] : '';
		$wb_ads_rotator_container_id    = isset( $wb_ads_rotator_get_values['container_id'] ) ? $wb_ads_rotator_get_values['container_id'] : '';
		$wb_ads_visitor_view            = isset( $wb_ads_rotator_get_values['to_whom']['logged_in_visitor'] ) ? $wb_ads_rotator_get_values['to_whom']['logged_in_visitor'] : '';
		$wb_ads_visitor_device          = isset( $wb_ads_rotator_get_values['to_whom']['device'] ) ? $wb_ads_rotator_get_values['to_whom']['device'] : '';
		$wb_ads_rotator_image_width     = isset( $wb_ads_rotator_get_values['size']['width'] ) ? $wb_ads_rotator_get_values['size']['width'] : '';
		$wb_ads_rotator_image_height    = isset( $wb_ads_rotator_get_values['size']['height'] ) ? $wb_ads_rotator_get_values['size']['height'] : '';
		$wb_ads_rotator_margin_top      = isset( $wb_ads_rotator_get_values['margin']['top'] ) ? $wb_ads_rotator_get_values['margin']['top'] : '';
		$wb_ads_rotator_margin_right    = isset( $wb_ads_rotator_get_values['margin']['right'] ) ? $wb_ads_rotator_get_values['margin']['right'] : '';
		$wb_ads_rotator_margin_bottom   = isset( $wb_ads_rotator_get_values['margin']['bottom'] ) ? $wb_ads_rotator_get_values['margin']['bottom'] : '';
		$wb_ads_rotator_margin_left     = isset( $wb_ads_rotator_get_values['margin']['left'] ) ? $wb_ads_rotator_get_values['margin']['left'] : '';
		$wb_ads_rotator_clearfix        = isset( $wb_ads_rotator_get_values['clearfix'] ) ? $wb_ads_rotator_get_values['clearfix'] : '';

		$wb_ads_rotator_get_ads_img_src = wp_get_attachment_url( $wb_ads_rotator_get_image_id );
		if ( empty( $wb_ads_rotator_container_id ) ) {
			$wb_ads_rotator_container_id = 'wb_ads_rotator_' . $wb_ads_rotator_get_ad;
		}
		if ( ! is_user_logged_in() && 'login_in' === $wb_ads_visitor_view ) {
			return;
		} elseif ( is_user_logged_in() && 'logout_out' === $wb_ads_visitor_view ) {
			return;
		}

		if ( 'desktop' === $wb_ads_visitor_device ) {
			if ( wp_is_mobile() ) {
				return;
			}
		} elseif ( 'mobile' === $wb_ads_visitor_device ) {
			if ( ! wp_is_mobile() ) {
				return;
			}
		}
		echo '<div class="wb-ads-rotator image-ads ' . esc_attr( $wb_ads_rotator_container_class ) . '" style=" text-align:' . esc_attr( $wb_ads_position ) . ';margin-top:' . esc_attr( $wb_ads_rotator_margin_top ) . 'px; margin-right:' . esc_attr( $wb_ads_rotator_margin_right ) . 'px; margin-bottom:' . esc_attr( $wb_ads_rotator_margin_bottom ) . 'px;margin-left:' . esc_attr( $wb_ads_rotator_margin_left ) . 'px; " id="' . esc_attr( $wb_ads_rotator_container_id ) . '">';
		echo '<a href="' . esc_url( $wb_ads_rotator_get_image_link ) . '" target="blank">';
		echo '<img src=" ' . esc_url( $wb_ads_rotator_get_ads_img_src ) . '" width="' . esc_attr( $wb_ads_rotator_image_width ) . '" height="' . esc_attr( $wb_ads_rotator_image_height ) . '">';
		echo '</a>';
		echo '</div>';
		if ( 'yes' === $wb_ads_rotator_clearfix ) {
			echo '<br style="clear: both; display: block; float: none;">';
		}
	}

	/**
	 * Display Shortcode Ads.
	 *
	 * @since 1.0.0
	 * @param array|string $atts User defined attributes for this shortcode instance.
	 */
	public function wb_ads_rotator_shortcode_ads( $atts ) {
		// Attributes.
		$atts = shortcode_atts(
			array(
				'ads_id' => null,
			),
			$atts,
			'wb-ads'
		);

		$wb_ads_display = '';
		if ( empty( $atts['ads_id'] ) ) {
			$wb_ads_display .= __( 'Please Enter the ads_id parameter value', 'buddypress-ads-rotator' );
			return $wb_ads_display;
		}
		ob_start();
		$args = array(
			'post_type'      => 'wb-ads',
			'posts_per_page' => 1,
			'publish_status' => 'published',
			'p'              => $atts['ads_id'],
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) :

				$query->the_post();
				$ads_id        = get_the_ID();
				$wb_ads_values = get_post_meta( $ads_id, 'wb_ads_rotator_values', true );
				$wb_ads_type   = isset( $wb_ads_values['ads_type'] ) ? $wb_ads_values['ads_type'] : '';
				if ( 'rich-content' === $wb_ads_type ) {
					$wb_ads_display = $this->wb_ads_rotator_display_rich_content_ads( $ads_id );
				} elseif ( 'plain-text-and-code' === $wb_ads_type ) {
					$wb_ads_display = $this->wb_ads_rotator_display_plain_text_and_code_ads( $ads_id );
				} else {
					$wb_ads_display = $this->wb_ads_rotator_display_image_ads( $ads_id );
				}
			endwhile;
		} else {
			return __( 'Nothing ads found', 'buddypress-ads-rotator' );
		}

		wp_reset_postdata();
		$wb_ads_display = ob_get_clean();
		return $wb_ads_display;
	}

}
