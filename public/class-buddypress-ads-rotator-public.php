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
	 * Check if current page is a BuddyPress activity page.
	 *
	 * BuddyPress presence is tested here rather than cached in the constructor. This
	 * class is built while the plugin file is being included, which is before
	 * plugins_loaded, so class_exists( 'BuddyPress' ) at that point is a race with the
	 * active_plugins order. When it lost that race the flag stuck at false, this guard
	 * never fired, and the header/footer code was printed twice on BuddyPress pages -
	 * once here and once from the BuddyPress integration class.
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
		global $post;
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
		wp_localize_script(
			$this->plugin_name,
			'bp_ads_check',
			array(
				'is_shortcode_page' => has_shortcode( isset( $post->post_content ) ? $post->post_content : '', 'ads-shortcode' ) ? true : false,
			)
		);
	}

	/**
	 * Display ads in WordPress post/page content.
	 *
	 * @since 1.6.0
	 * @param string $content The post content.
	 * @return string Modified content with ads.
	 */
	public function wb_ads_display_in_content( $content ) {
		// Skip if not a singular post/page or in admin.
		if ( is_admin() || ! is_singular() ) {
			return $content;
		}

		// Skip on BuddyPress pages - those have their own handlers.
		if ( $this->is_buddypress_page() ) {
			return $content;
		}

		$before_content = '';
		$after_content  = '';

		// Bounded + cached eligible-ad set (meta primed in one batch).
		// Position filtering stays in PHP below so the result is identical
		// to the previous per-position meta_query LIKE.
		$ads = Buddypress_Ads_Rotator_Cache::get_ad_ids();
		if ( empty( $ads ) ) {
			return $content;
		}

		foreach ( $ads as $ad_id ) {
			$wb_ads_enable = get_post_meta( $ad_id, 'wb_ads_enable', true );
			if ( 'disable' === $wb_ads_enable ) {
				continue;
			}

			$wb_ads_values = get_post_meta( $ad_id, 'wb_ads_rotator_values', true );
			$wp_position   = isset( $wb_ads_values['wp_position'] ) ? $wb_ads_values['wp_position'] : '';

			ob_start();
			switch ( $wb_ads_values['ads_type'] ) {
				case 'plain-text-and-code':
					$this->wb_ads_rotator_display_plain_text_and_code_ads( $ad_id );
					break;
				case 'rich-content':
					$this->wb_ads_rotator_display_rich_content_ads( $ad_id );
					break;
				case 'image-ad':
					$this->wb_ads_rotator_display_image_ads( $ad_id );
					break;
			}
			$ad_html = ob_get_clean();

			if ( 'wp_before_content' === $wp_position ) {
				$before_content .= $ad_html;
			} elseif ( 'wp_after_content' === $wp_position ) {
				$after_content .= $ad_html;
			}
		}

		return $before_content . $content . $after_content;
	}

	/**
	 * Display ads in WordPress header.
	 *
	 * @since 1.6.0
	 */
	public function wb_ads_display_header() {
		// Skip on BuddyPress pages - those have their own handlers.
		if ( $this->is_buddypress_page() ) {
			return;
		}

		// Bounded + cached eligible-ad set (meta primed in one batch);
		// wp_header position filtered in PHP below (identical result).
		$ads = Buddypress_Ads_Rotator_Cache::get_ad_ids();
		foreach ( $ads as $ad_id ) {
			$wb_ads_enable = get_post_meta( $ad_id, 'wb_ads_enable', true );
			if ( 'disable' === $wb_ads_enable ) {
				continue;
			}

			$wb_ads_values = get_post_meta( $ad_id, 'wb_ads_rotator_values', true );
			$wp_position   = isset( $wb_ads_values['wp_position'] ) ? $wb_ads_values['wp_position'] : '';

			if ( 'wp_header' !== $wp_position ) {
				continue;
			}

			// Output scripts/styles for plain text ads.
			if ( 'plain-text-and-code' === $wb_ads_values['ads_type'] && wb_ads_rotator_ad_code_allowed( $ad_id ) ) {
				$plain_text = isset( $wb_ads_values['plain_text_and_code'] ) ? $wb_ads_values['plain_text_and_code'] : '';
				preg_match_all( '/<script\b[^>]*>(.*?)<\/script>/is', $plain_text, $scripts );
				preg_match_all( '/<style\b[^>]*>(.*?)<\/style>/is', $plain_text, $styles );
				if ( isset( $scripts[0] ) ) {
					foreach ( $scripts[0] as $script ) {
						echo $script; // phpcs:ignore
					}
				}
				if ( isset( $styles[0] ) ) {
					foreach ( $styles[0] as $style ) {
						echo $style; // phpcs:ignore
					}
				}
			}
		}

		// Output global header scripts.
		$options = get_option( 'buddypress-ads-rotator_admin_scripts_options' );
		$header  = isset( $options['header'] ) ? $options['header'] : '';
		if ( ! empty( $header ) ) {
			echo $header; // phpcs:ignore
		}
	}

	/**
	 * Display ads in WordPress footer.
	 *
	 * @since 1.6.0
	 */
	public function wb_ads_display_footer() {
		// Skip on BuddyPress pages - those have their own handlers.
		if ( $this->is_buddypress_page() ) {
			return;
		}

		// Bounded + cached eligible-ad set (meta primed in one batch);
		// wp_footer position filtered in PHP below (identical result).
		$ads = Buddypress_Ads_Rotator_Cache::get_ad_ids();
		foreach ( $ads as $ad_id ) {
			$wb_ads_enable = get_post_meta( $ad_id, 'wb_ads_enable', true );
			if ( 'disable' === $wb_ads_enable ) {
				continue;
			}

			$wb_ads_values = get_post_meta( $ad_id, 'wb_ads_rotator_values', true );
			$wp_position   = isset( $wb_ads_values['wp_position'] ) ? $wb_ads_values['wp_position'] : '';

			if ( 'wp_footer' !== $wp_position ) {
				continue;
			}

			switch ( $wb_ads_values['ads_type'] ) {
				case 'plain-text-and-code':
					$this->wb_ads_rotator_display_plain_text_and_code_ads( $ad_id );
					break;
				case 'rich-content':
					$this->wb_ads_rotator_display_rich_content_ads( $ad_id );
					break;
				case 'image-ad':
					$this->wb_ads_rotator_display_image_ads( $ad_id );
					break;
			}
		}

		// Output global footer scripts.
		$options = get_option( 'buddypress-ads-rotator_admin_scripts_options' );
		$footer  = isset( $options['footer'] ) ? $options['footer'] : '';
		if ( ! empty( $footer ) ) {
			echo $footer; // phpcs:ignore
		}
	}

	/**
	 * Saved settings of an ad that may show to this visitor, or null when the ad is
	 * disabled or targets other visitors (logged in/out) or another device.
	 *
	 * @since 1.6.0
	 * @param int $ad_id Ad post ID.
	 * @return array|null
	 */
	private function wb_ads_rotator_visible_values( $ad_id ) {
		if ( 'disable' === get_post_meta( $ad_id, 'wb_ads_enable', true ) ) {
			return null;
		}
		$values = get_post_meta( $ad_id, 'wb_ads_rotator_values', true );
		$values = is_array( $values ) ? $values : array();
		$view   = $values['to_whom']['logged_in_visitor'] ?? '';
		$device = $values['to_whom']['device'] ?? '';

		if ( ( 'login_in' === $view && ! is_user_logged_in() ) || ( 'logout_out' === $view && is_user_logged_in() ) ) {
			return null;
		}
		if ( ( 'desktop' === $device && wp_is_mobile() ) || ( 'mobile' === $device && ! wp_is_mobile() ) ) {
			return null;
		}
		return $values;
	}

	/**
	 * Text-align value for the ad's position setting.
	 *
	 * @since 1.6.0
	 * @param array  $values Ad settings.
	 * @param string $prefix '' or '-webkit-' (rich content and image ads).
	 * @return string
	 */
	private function wb_ads_rotator_align( $values, $prefix ) {
		$position = $values['ads_position'] ?? '';
		if ( 'default' === $position ) {
			return 'initial';
		}
		return in_array( $position, array( 'left', 'center', 'right' ), true ) ? $prefix . $position : '';
	}

	/**
	 * Open an ad container: id, classes, alignment and margins.
	 *
	 * @since 1.6.0
	 * @param int      $ad_id   Ad post ID.
	 * @param array    $values  Ad settings.
	 * @param string[] $classes Container classes.
	 * @param string   $align   Text-align value.
	 * @param string   $style   Extra inline style placed before alignment and margins.
	 */
	private function wb_ads_rotator_open( $ad_id, $values, $classes, $align, $style = '' ) {
		$id      = ! empty( $values['container_id'] ) ? $values['container_id'] : 'wbcomads-' . $ad_id;
		$margins = array();
		foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
			$margins[] = $values['margin'][ $side ] ?? '';
		}
		echo '<div id="' . esc_attr( $id ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '" style="' . esc_attr( $style . $this->wb_ads_rotator_box_style( $align, $margins ) ) . '">';
	}

	/**
	 * Close an ad container, with the optional clearfix.
	 *
	 * @since 1.6.0
	 * @param array $values Ad settings.
	 */
	private function wb_ads_rotator_close( $values ) {
		echo '</div>';
		if ( 'yes' === ( $values['clearfix'] ?? '' ) ) {
			echo '<br style="clear: both; display: block; float: none;">';
		}
	}

	/**
	 * Display plain text and code type ads.
	 *
	 * @param int $wb_ads_rotator_get_ad Get Ads ID.
	 */
	public function wb_ads_rotator_display_plain_text_and_code_ads( $wb_ads_rotator_get_ad ) {
		$values = $this->wb_ads_rotator_visible_values( $wb_ads_rotator_get_ad );
		if ( null === $values ) {
			return;
		}

		$content = $values['plain_text_and_code'] ?? '';
		if ( ! wb_ads_rotator_ad_code_allowed( $wb_ads_rotator_get_ad ) ) {
			$content = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/is', '', $content );
			$content = preg_replace( '/<style\b[^>]*>(.*?)<\/style>/is', '', $content );
		}

		$classes = array( 'wb-bannerads-rotator' );
		if ( function_exists( 'bp_get_theme_compat_id' ) && 'nouveau' === bp_get_theme_compat_id() ) {
			$classes[] = 'wbbpads-nouveau';
			if ( false !== strpos( $content, '<script' ) || false !== strpos( $content, 'iframe' ) ) {
				$classes[] = 'wbads-iframe';
			}
		}
		$classes[] = 'plain-text-and-code-ads';

		$this->wb_ads_rotator_open( $wb_ads_rotator_get_ad, $values, $classes, $this->wb_ads_rotator_align( $values, '' ) );
		if ( 'yes' === ( $values['allow_shortcode'] ?? '' ) ) {
			echo do_shortcode( $content );
		} else {
			echo apply_filters( 'wb_ads_rotator_plain_text_code_ads_content', $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The owner's ad code; script/style already stripped unless the author may post code.
		}
		$this->wb_ads_rotator_close( $values );
	}

	/**
	 * Display rich content type ads.
	 *
	 * @param int $wb_ads_rotator_get_ad Get Ads ID.
	 */
	public function wb_ads_rotator_display_rich_content_ads( $wb_ads_rotator_get_ad ) {
		$values = $this->wb_ads_rotator_visible_values( $wb_ads_rotator_get_ad );
		if ( null === $values ) {
			return;
		}

		$content   = $values['rich_content'] ?? '';
		$font_size = $values['font_size'] ?? '';
		/*
		 * Colour declarations for the wrapper - empty unless the owner deliberately chose
		 * them. Painting a background and text colour nobody asked for is what made an ad
		 * a solid block on a dark page: an inline style cannot be overridden by any theme,
		 * so the ad was frozen in whatever colours the picker happened to submit. The ad's
		 * own content is never touched - its colours are the advertiser's call.
		 */
		$style = wb_ads_rotator_color_styles( $values ) . ( is_numeric( $font_size ) ? 'font-size:' . $font_size . 'px;' : '' );

		$classes = ( function_exists( 'bp_get_theme_compat_id' ) && 'nouveau' === bp_get_theme_compat_id() )
			? array( 'wb-bannerads-rotator', 'wbbpads-nouveau', 'wbads-iframe', 'rich-contentwbads' )
			: array( 'wb-bannerads-rotator', 'rich-contentwbads' );

		$this->wb_ads_rotator_open( $wb_ads_rotator_get_ad, $values, $classes, $this->wb_ads_rotator_align( $values, '-webkit-' ), $style );
		if ( false !== strpos( $content, '[' ) ) {
			preg_match_all( '/' . get_shortcode_regex() . '/', $content, $matches, PREG_SET_ORDER );
			foreach ( $matches as $shortcode ) {
				echo do_shortcode( $shortcode[0] );
			}
		}
		if ( ! empty( $shortcode[0] ) ) {
			$content = str_replace( $shortcode[0], '', $content );
		}
		echo wp_kses( apply_filters( 'wb_ads_rotator_rich_content_ads_content', $content ), $this->wb_ads_allowed_html() );
		$this->wb_ads_rotator_close( $values );
	}

	/**
	 * Inline alignment + margin style for an ad container.
	 *
	 * Skips empty or non-numeric margins so the markup never carries `margin-top:px`.
	 *
	 * @since 1.6.0
	 * @param string $align   text-align value, or ''.
	 * @param array  $margins Top, right, bottom, left.
	 * @return string
	 */
	private function wb_ads_rotator_box_style( $align, $margins ) {
		$style = '' !== $align ? 'text-align:' . $align . ';' : '';
		foreach ( array( 'top', 'right', 'bottom', 'left' ) as $i => $side ) {
			if ( isset( $margins[ $i ] ) && is_numeric( $margins[ $i ] ) ) {
				$style .= 'margin-' . $side . ':' . $margins[ $i ] . 'px;';
			}
		}
		return $style;
	}

	/**
	 * Display image ad type ads.
	 *
	 * @param int $wb_ads_rotator_get_ad Get Ads ID.
	 */
	public function wb_ads_rotator_display_image_ads( $wb_ads_rotator_get_ad ) {
		$values = $this->wb_ads_rotator_visible_values( $wb_ads_rotator_get_ad );
		if ( null === $values ) {
			return;
		}

		$link = $values['image_link'] ?? '';
		$src  = ! empty( $values['ads_image'] ) ? wp_get_attachment_url( $values['ads_image'] ) : $link;

		$this->wb_ads_rotator_open( $wb_ads_rotator_get_ad, $values, array( 'wb-bannerads-rotator', 'wbcom-imageads' ), $this->wb_ads_rotator_align( $values, '-webkit-' ) );
		echo '<a href="' . esc_url( $link ) . '" target="blank">';
		echo '<img src="' . esc_url( $src ) . '" width="' . esc_attr( $values['size']['width'] ?? '' ) . '" height="' . esc_attr( $values['size']['height'] ?? '' ) . '">';
		echo '</a>';
		$this->wb_ads_rotator_close( $values );
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

	/**
	 * Allowed HTML.
	 */
	public function wb_ads_allowed_html() {

		$allowed_tags = array(
			'a'          => array(
				'class' => array(),
				'href'  => array(),
				'rel'   => array(),
				'title' => array(),
				'style' => array(),
			),
			'abbr'       => array(
				'title' => array(),
				'style' => array(),
			),
			'b'          => array(
				'style' => array(),
			),
			'blockquote' => array(
				'cite'  => array(),
				'style' => array(),
			),
			'cite'       => array(
				'title' => array(),
				'style' => array(),
			),
			'code'       => array(
				'style' => array(),
			),
			'del'        => array(
				'datetime' => array(),
				'title'    => array(),
				'style'    => array(),
			),
			'dd'         => array(
				'style' => array(),
			),
			'div'        => array(
				'class' => array(),
				'title' => array(),
				'style' => array(),
			),
			'dl'         => array(
				'style' => array(),
			),
			'dt'         => array(
				'style' => array(),
			),
			'em'         => array(
				'style' => array(),
			),
			'h1'         => array(
				'style' => array(),
			),
			'h2'         => array(
				'style' => array(),
			),
			'h3'         => array(
				'style' => array(),
			),
			'h4'         => array(
				'style' => array(),
			),
			'h5'         => array(
				'style' => array(),
			),
			'h6'         => array(
				'style' => array(),
			),
			'i'          => array(
				'style' => array(),
			),
			'img'        => array(
				'alt'    => array(),
				'class'  => array(),
				'height' => array(),
				'src'    => array(),
				'width'  => array(),
				'style'  => array(),
			),
			'li'         => array(
				'class' => array(),
				'style' => array(),
			),
			'ol'         => array(
				'class' => array(),
				'style' => array(),
			),
			'p'          => array(
				'class' => array(),
				'style' => array(),
			),
			'q'          => array(
				'cite'  => array(),
				'title' => array(),
				'style' => array(),
			),
			'span'       => array(
				'class' => array(),
				'title' => array(),
				'style' => array(),
			),
			'strike'     => array(
				'style' => array(),
			),
			'strong'     => array(
				'style' => array(),
			),
			'ul'         => array(
				'class' => array(),
				'style' => array(),
			),
			'script'     => array(
				'type'              => array(),
				'id'                => array(),
				'src'               => array(),
				'width'             => array(),
				'height'            => array(),
				'scrolling'         => array(),
				'frameborder'       => array(),
				'allowtransparency' => array(),
				'allow'             => array(),
				'allowfullscreen'   => array(),
			),
			'iframe'     => array(
				'type'              => array(),
				'id'                => array(),
				'src'               => array(),
				'width'             => array(),
				'height'            => array(),
				'scrolling'         => array(),
				'frameborder'       => array(),
				'allowtransparency' => array(),
				'allow'             => array(),
				'allowfullscreen'   => array(),
			),
			'style'      => array(
				'type'   => array(),
				'media'  => array(),
				'scoped' => array(),
			),
		);

		return $allowed_tags;
	}
}
