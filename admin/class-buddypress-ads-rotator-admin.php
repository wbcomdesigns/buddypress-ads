<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Ads_Rotator
 * @subpackage Buddypress_Ads_Rotator/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Legacy admin service class for the wb-ads CPT.
 *
 * Post-1.6.0 scope: registers the wb-ads custom post type, its
 * metaboxes, the save-post handler, the All Ads list columns, the
 * publish-time default, the CPT-screen assets, and the three metabox
 * AJAX handlers (image preview, image removal, enable toggle). The
 * plugin settings page (Overview / Scripts / Welcome / FAQ) moved to
 * Buddypress_Ads_Rotator_Settings_Admin during the card-panel admin
 * migration, so this class no longer owns any settings-page UI.
 *
 * @package    Buddypress_Ads_Rotator
 * @subpackage Buddypress_Ads_Rotator/admin
 * @author     WB COM <admin@wbcomdesigns.com>
 */
class Buddypress_Ads_Rotator_Admin {

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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		$screen = get_current_screen();
		// Only on the wb-ads CPT add/edit screens. The settings page has
		// its own card-panel stylesheet (assets/css/admin.css) enqueued by
		// Buddypress_Ads_Rotator_Settings_Admin.
		if ( isset( $screen->post_type ) && 'wb-ads' === $screen->post_type ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/buddypress-ads-rotator-admin.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'wb_ads_rotator-selectize', plugin_dir_url( __FILE__ ) . 'css/selectize.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();
		// Only on the wb-ads CPT add/edit screens (metabox behaviour:
		// ad-type toggle, image uploader, enable switch, codemirror,
		// shortcode copy, selectize). The settings page uses its own
		// card-panel script.
		if ( isset( $screen->post_type ) && 'wb-ads' === $screen->post_type ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-ads-rotator-admin.js', array( 'jquery' ), $this->version, false );
			wp_localize_script(
				$this->plugin_name,
				'ajax',
				array(
					'url'   => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'ajax-nonce' ),
				)
			);
			wp_enqueue_script( 'wb_ads_rotator-selectize-min', plugin_dir_url( __FILE__ ) . 'js/selectize.min.js', array( 'jquery' ), $this->version, false );
			$cm_settings['codeEditor'] = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
			wp_localize_script( 'jquery', 'cm_settings', $cm_settings );

			wp_enqueue_script( 'wp-theme-plugin-editor' );
			wp_enqueue_style( 'wp-codemirror' );
		}
	}

	/**
	 * This Function added the WB Ads CPT.
	 *
	 * @since    1.0.0
	 */
	public function wb_ads_rotator_add_cpt() {
		$labels = array(
			'name'               => _x( 'WB Ads', 'Post type general name', 'buddypress-ads-rotator' ),
			'singular_name'      => _x( 'WB Ads', 'Post type singular name', 'buddypress-ads-rotator' ),
			'menu_name'          => _x( 'WB Ads', 'Admin Menu text', 'buddypress-ads-rotator' ),
			'name_admin_bar'     => _x( 'WB Ads', 'Add New on Toolbar', 'buddypress-ads-rotator' ),
			'add_new'            => __( 'Add New WB Ads', 'buddypress-ads-rotator' ),
			'add_new_item'       => __( 'Add New WB Ads', 'buddypress-ads-rotator' ),
			'new_item'           => __( 'New WB Ads', 'buddypress-ads-rotator' ),
			'edit_item'          => __( 'Edit WB Ads', 'buddypress-ads-rotator' ),
			'view_item'          => __( 'View WB Ads', 'buddypress-ads-rotator' ),
			'all_items'          => __( 'All WB Ads', 'buddypress-ads-rotator' ),
			'search_items'       => __( 'Search WB Ads', 'buddypress-ads-rotator' ),
			'parent_item_colon'  => __( 'Parent WB Ads:', 'buddypress-ads-rotator' ),
			'not_found'          => __( 'No WB Ads found.', 'buddypress-ads-rotator' ),
			'not_found_in_trash' => __( 'No WB Ads found in Trash.', 'buddypress-ads-rotator' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'wb-ads' ),
			'capability_type'    => 'page',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'show_in_rest'       => true,
			'menu_icon'          => 'dashicons-chart-line',
			'supports'           => array( 'title' ),
		);
		register_post_type( 'wb-ads', $args );
	}

	/**
	 * Added the WB Ads CPT Metaboxes.
	 */
	public function wb_ads_rotator_add_metaboxes() {

		$post_types = array( 'wb-ads' );

		add_meta_box(
			'wb_ads_rotator_metaboxs',
			__( 'WB Ads Type', 'buddypress-ads-rotator' ),
			array( $this, 'wb_ads_rotator_render_add_meta_box' ),
			array( $post_types )
		);

		add_meta_box(
			'wb_ads_rotator_parameter_metaboxs',
			__( 'Ads Parameter', 'buddypress-ads-rotator' ),
			array( $this, 'wb_ads_rotator_render_parameter_box' ),
			array( $post_types )
		);

		add_meta_box(
			'wb_ads_rotator_layout_metaboxs',
			__( 'Layout / Output', 'buddypress-ads-rotator' ),
			array( $this, 'wb_ads_rotator_render_layout_box' ),
			array( $post_types )
		);

		add_meta_box(
			'wb_ads_rotator_display_conditions_metaboxs',
			__( 'Display Conditions', 'buddypress-ads-rotator' ),
			array( $this, 'wb_ads_rotator_render_display_conditions_box' ),
			array( $post_types )
		);

		add_meta_box(
			'wb_ads_rotator_to_whom_metaboxs',
			__( 'To Whom', 'buddypress-ads-rotator' ),
			array( $this, 'wb_ads_rotator_render_to_whom_box' ),
			array( $post_types )
		);

		add_meta_box(
			'wb_ads_rotator_shortcode',
			__( 'Shortcode', 'buddypress-ads-rotator' ),
			array( $this, 'wb_ads_rotator_render_shortcode_box' ),
			array( $post_types ),
			'side',
		);
	}

	/**
	 * WB Ads Metabox callback function.
	 *
	 * @param WP_Post $post Ad post being edited.
	 */
	public function wb_ads_rotator_render_add_meta_box( $post ) {
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'wb_ads_rotator_render_add_meta_box', 'wb_ads_rotator_render_add_meta_box_nonce' );
		$post_id               = $post->ID;
		$post_type             = get_post_type();
		$wb_ads_rotator_values = get_post_meta( $post_id, 'wb_ads_rotator_values', true );
		?>
		<div class="wb_ads_rotator-panel">
		<?php do_action( 'wb_ads_rotator_options_before' ); ?>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Plain Text and Code', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="radio" name="wb_ads_rotator[ads_type]" class="wb-ads_types" id="wb_ads_type_plain" value="plain-text-and-code" <?php echo ( isset( $wb_ads_rotator_values['ads_type'] ) ) ? checked( $wb_ads_rotator_values['ads_type'], 'plain-text-and-code' ) : 'checked'; ?>/>
				<label for="wb_ads_type_plain"><?php esc_html_e( 'Any ad network, Amazon, customized AdSense codes, shortcodes, and code like JavaScript or HTML.', 'buddypress-ads-rotator' ); ?></label>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Rich Content', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="radio" name="wb_ads_rotator[ads_type]" class="wb-ads_types" id="wb_ads_type_rich" value="rich-content" <?php ( isset( $wb_ads_rotator_values['ads_type'] ) ) ? checked( $wb_ads_rotator_values['ads_type'], 'rich-content' ) : ''; ?>/>
				<label for="wb_ads_type_rich"><?php esc_html_e( 'The full content editor from WordPress with all features like shortcodes, image upload or styling, but also simple text/html mode for scripts.', 'buddypress-ads-rotator' ); ?></label>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Image Ad', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="radio" name="wb_ads_rotator[ads_type]" class="wb-ads_types" id="wb_ads_type_image" value="image-ad" <?php ( isset( $wb_ads_rotator_values['ads_type'] ) ) ? checked( $wb_ads_rotator_values['ads_type'], 'image-ad' ) : ''; ?>/>
				<label for="wb_ads_type_image"><?php esc_html_e( 'Ads in various image formats.', 'buddypress-ads-rotator' ); ?></label>
				</div>
			</div>
		<?php do_action( 'wb_ads_rotator_options_after' ); ?>
		</div>
		<?php
	}

	/**
	 * WB Ads Metabox callback function.
	 *
	 * @param WP_Post $post Ad post being edited.
	 */
	public function wb_ads_rotator_render_parameter_box( $post ) {
		$post_id                   = $post->ID;
		$post_type                 = get_post_type();
		$wb_ads_rotator_values     = get_post_meta( $post_id, 'wb_ads_rotator_values', true );
		$wb_ads_rotator_type       = isset( $wb_ads_rotator_values['ads_type'] ) ? $wb_ads_rotator_values['ads_type'] : 'plain-text-and-code';
		$wb_ads_rotator_width      = isset( $wb_ads_rotator_values['size']['width'] ) ? $wb_ads_rotator_values['size']['width'] : '';
		$wb_ads_rotator_height     = isset( $wb_ads_rotator_values['size']['height'] ) ? $wb_ads_rotator_values['size']['height'] : '';
		$wb_ads_rotator_plain_text = isset( $wb_ads_rotator_values['plain_text_and_code'] ) ? $wb_ads_rotator_values['plain_text_and_code'] : '';
		$wb_ads_rotator_image_url  = isset( $wb_ads_rotator_values['image_link'] ) ? $wb_ads_rotator_values['image_link'] : '';
		$wb_ads_rotator_image_id   = isset( $wb_ads_rotator_values['ads_image'] ) ? $wb_ads_rotator_values['ads_image'] : '';
		$wb_ads_rotator_font_size  = isset( $wb_ads_rotator_values['font_size'] ) ? $wb_ads_rotator_values['font_size'] : '20';
		$wb_ads_rotator_txt_color  = isset( $wb_ads_rotator_values['text_color'] ) ? $wb_ads_rotator_values['text_color'] : '#FFFFFF';
		$wb_ads_rotator_bg_color   = isset( $wb_ads_rotator_values['bg_color'] ) ? $wb_ads_rotator_values['bg_color'] : '';

		// Off unless the owner deliberately chose colours. See wb_ads_rotator_uses_custom_colors().
		$wb_ads_rotator_custom_colors = wb_ads_rotator_uses_custom_colors( $wb_ads_rotator_values );
		?>
		<div class="wb_ads_rotator-panel">
		<?php do_action( 'wb_ads_rotator_options_before' ); ?>
			<div class="wb_ads_rotator-wrapper plain-text-and-code"
		<?php
		if ( 'plain-text-and-code' !== $wb_ads_rotator_type ) {
			echo 'style="display:none"';
		}
		?>
			>
				<div class="wb_ads_rotator-input">
					<strong>
					<?php esc_html_e( 'Insert plain text or code into this field.', 'buddypress-ads-rotator' ); ?>
					</strong>
				<?php echo '<textarea id="wb-ad-rotator-content-plain" name="wb_ads_rotator[plain_text_and_code]" >' . esc_textarea( $wb_ads_rotator_plain_text ) . '</textarea>'; ?>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper rich-content"
			<?php
			if ( 'rich-content' !== $wb_ads_rotator_type ) {
				echo 'style="display:none"';
			}
			?>
			>
				<div class="wb_ads_rotator-input">
				<?php
				$wb_ads_rotator_content = '';
				if ( isset( $wb_ads_rotator_values['rich_content'] ) ) {
					$wb_ads_rotator_content = $wb_ads_rotator_values['rich_content'];
				}
				$editor_id      = 'wb_ads_rotator_editor';
				$editor_setting = array(
					'textarea_name' => 'wb_ads_rotator[rich_content]',
					'textarea_rows' => get_option( 'default_post_edit_rows', 10 ),
				);
				wp_editor( $wb_ads_rotator_content, $editor_id, $editor_setting );
				?>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper select-ads-image image-ad"
				<?php
				if ( 'image-ad' !== $wb_ads_rotator_type ) {
					echo 'style="display:none"';
				}
				?>
			>
				<div class="wb_ads_rotator-label">
					<div id="wb-ads-preview-image">
					<?php
					$image_id = get_option( 'wb_ads_image_id' );
					if ( intval( $wb_ads_rotator_image_id ) > 0 ) {
						$image = wp_get_attachment_image( $wb_ads_rotator_image_id, 'large', false, array( 'id' => 'wb-ads-preview-images' ) );
					} else {
						$image = '<img id="wb-ads-preview-images" src="' . esc_url( plugin_dir_url( __FILE__ ) . 'images/wb-ads-placeholder-image.jpg' ) . '" />';
					}
					echo wp_kses_post( $image );
					?>
					</div>
					<input type="hidden" name="wb_ads_rotator[ads_image]" id="wb_ads_image_id" value="<?php echo esc_attr( $wb_ads_rotator_image_id ); ?>" class="regular-text" />
				</div>
				<div class="wb_ads_rotator-input">
					<input type='button' class="button-primary" value="<?php esc_attr_e( 'Remove Image', 'buddypress-ads-rotator' ); ?>" data-img-id="<?php echo esc_attr( $wb_ads_rotator_image_id ); ?>" id="wb_ads_rotator_remove_image"/>
					<input type='button' class="button-primary" value="<?php esc_attr_e( 'Choose Image', 'buddypress-ads-rotator' ); ?>"  id="wb_ads_rotator_select_image"/>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper select-ads-image image-ad"
				<?php
				if ( 'image-ad' !== $wb_ads_rotator_type ) {
					echo 'style="display:none"';
				}
				?>
			>
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'URL', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="text" name="wb_ads_rotator[image_link]" class="wb_ads_rotator_image" placeholder="http://" value="<?php echo esc_attr( $wb_ads_rotator_image_url ); ?>"/>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper select-ads-image image-ad" <?php echo 'image-ad' !== $wb_ads_rotator_type ? 'style="display:none"' : ''; ?>>
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Size', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input wb-ads-rotator-ads-size">
					<div class="wb-bp-ads-size">
						<span>
						<?php esc_html_e( 'Width', 'buddypress-ads-rotator' ); ?>
						</span>
						<label>
							<input type="number" name="wb_ads_rotator[size][width]" value="<?php echo esc_attr( $wb_ads_rotator_width ); ?>" id="wb_ads_image_width"/>
						<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
						</label>
					</div>
					<div class="wb-bp-ads-size">
						<span>
							<?php esc_html_e( 'Height', 'buddypress-ads-rotator' ); ?>
						</span>
						<label>
							<input type="number" name="wb_ads_rotator[size][height]" value="<?php echo esc_attr( $wb_ads_rotator_height ); ?>" id="wb_ads_image_height"/>
						<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
						</label>
					</div>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper plain-text-and-code"
			<?php
			if ( 'plain-text-and-code' !== $wb_ads_rotator_type ) {
				echo 'style="display:none"';
			}
			?>
			>
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Allow Shortcodes', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="checkbox" id="wb-ad-rotator-allow-shortcode" name="wb_ads_rotator[allow_shortcode]" value="yes"  <?php esc_attr( isset( $wb_ads_rotator_values['allow_shortcode'] ) ? checked( $wb_ads_rotator_values['allow_shortcode'], 'yes' ) : '' ); ?>/>
				<?php esc_html_e( 'Execute shortcodes', 'buddypress-ads-rotator' ); ?>
					<div class="wb-ad-rotator-error-message" id="wb-ad-rotator-allow-shortcode-warning"
					<?php
					if ( true !== isset( $wb_ads_rotator_values['allow_shortcode'] ) ) {
						echo 'style="display:none"';
					}
					?>
						>
						<?php esc_html_e( 'No shortcode detected in your code.', 'buddypress-ads-rotator' ); ?><?php esc_html_e( 'Uncheck this checkbox for improved performance.', 'buddypress-ads-rotator' ); ?>
					</div>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper rich-content"
				<?php
				if ( 'rich-content' !== $wb_ads_rotator_type ) {
					echo 'style="display:none"';
				}
				?>
			>
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Rich Content Font Size', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<label>
						<input type="number" name="wb_ads_rotator[font_size]" value="<?php echo esc_attr( $wb_ads_rotator_font_size ); ?>"/>
					<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
					</label>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper rich-content"
			<?php
			if ( 'rich-content' !== $wb_ads_rotator_type ) {
				echo 'style="display:none"';
			}
			?>
			>
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Rich Content Colors', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input wb_ads_custom_colors">
					<?php // An unchecked box is not submitted, so "off" must be posted explicitly - otherwise the key goes missing and the ad falls back to its stored colors. ?>
					<input type="hidden" name="wb_ads_rotator[custom_colors]" value="0" />
					<label>
						<input type="checkbox" name="wb_ads_rotator[custom_colors]" value="1" <?php checked( $wb_ads_rotator_custom_colors ); ?> />
						<?php esc_html_e( 'Use custom colors for this ad', 'buddypress-ads-rotator' ); ?>
					</label>
					<p class="description">
						<?php esc_html_e( 'Leave this off and the ad inherits the colors of the page it sits on, so it follows the theme in both light and dark mode. Turn it on to set the ad\'s own colors, which are then used exactly as chosen everywhere.', 'buddypress-ads-rotator' ); ?>
					</p>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper rich-content"
			<?php
			if ( 'rich-content' !== $wb_ads_rotator_type ) {
				echo 'style="display:none"';
			}
			?>
			>
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Rich Content Background Color', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input wb_ads_bg_color">
					<label>
						<input type="color" name="wb_ads_rotator[bg_color]" value="<?php echo esc_attr( $wb_ads_rotator_bg_color ); ?>"/>
					</label>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper rich-content"
			<?php
			if ( 'rich-content' !== $wb_ads_rotator_type ) {
				echo 'style="display:none"';
			}
			?>
			>
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Rich Content Text Color', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input wb_ads_text_color">
					<label>
						<input type="color" name="wb_ads_rotator[text_color]" value="<?php echo esc_attr( $wb_ads_rotator_txt_color ); ?>"/>
					</label>
				</div>
			</div>
			<?php do_action( 'wb_ads_rotator_options_after' ); ?>
		</div>
			<?php
	}

	/**
	 * WB Ads Metabox callback function.
	 *
	 * @param WP_Post $post Ad post being edited.
	 */
	public function wb_ads_rotator_render_layout_box( $post ) {
		$post_id                        = $post->ID;
		$post_type                      = get_post_type();
		$wb_ads_rotator_values          = get_post_meta( $post_id, 'wb_ads_rotator_values', true );
		$wb_ads_rotator_margin_top      = isset( $wb_ads_rotator_values['margin']['top'] ) ? $wb_ads_rotator_values['margin']['top'] : '10';
		$wb_ads_rotator_margin_right    = isset( $wb_ads_rotator_values['margin']['right'] ) ? $wb_ads_rotator_values['margin']['right'] : '10';
		$wb_ads_rotator_margin_bottom   = isset( $wb_ads_rotator_values['margin']['bottom'] ) ? $wb_ads_rotator_values['margin']['bottom'] : '10';
		$wb_ads_rotator_margin_left     = isset( $wb_ads_rotator_values['margin']['left'] ) ? $wb_ads_rotator_values['margin']['left'] : '10';
		$wb_ads_rotator_container_class = isset( $wb_ads_rotator_values['container_classes'] ) ? $wb_ads_rotator_values['container_classes'] : 'adsclass';

		?>
		<div class="wb_ads_rotator-panel">
		<?php do_action( 'wb_ads_rotator_options_before' ); ?>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-input">
				<?php esc_html_e( 'Everything connected to the ads layout and output.', 'buddypress-ads-rotator' ); ?>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Alignment', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<label title="left">
						<input type="radio" name="wb_ads_rotator[ads_position]" value="left" <?php echo ( isset( $wb_ads_rotator_values['ads_position'] ) ) ? checked( $wb_ads_rotator_values['ads_position'], 'left' ) : 'checked'; ?>/>
						<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'images/output-left.png' ); ?>" width="60" height="40" alt="<?php esc_attr_e( 'Left', 'buddypress-ads-rotator' ); ?>">
					</label>
					<label title="center">
						<input type="radio" name="wb_ads_rotator[ads_position]" value="center" <?php ( isset( $wb_ads_rotator_values['ads_position'] ) ) ? checked( $wb_ads_rotator_values['ads_position'], 'center' ) : ''; ?>/>
						<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'images/output-center.png' ); ?>" width="60" height="40" alt="<?php esc_attr_e( 'Center', 'buddypress-ads-rotator' ); ?>">
					</label>
					<label title="right">
						<input type="radio" name="wb_ads_rotator[ads_position]" value="right" <?php ( isset( $wb_ads_rotator_values['ads_position'] ) ) ? checked( $wb_ads_rotator_values['ads_position'], 'right' ) : ''; ?>/>
						<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'images/output-right.png' ); ?>" width="60" height="40" alt="<?php esc_attr_e( 'Right', 'buddypress-ads-rotator' ); ?>">
					</label>
					<p>
						<label>
						<input type="checkbox" name="wb_ads_rotator[clearfix]" value="yes" <?php ( isset( $wb_ads_rotator_values['clearfix'] ) ) ? checked( $wb_ads_rotator_values['clearfix'], 'yes' ) : ''; ?>/>
						<?php esc_html_e( 'Add a line break after the ad, so the content that follows starts on its own line.', 'buddypress-ads-rotator' ); ?>
						</label>
					</p>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Margin', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<div class="wb_ads_margin">
						<label>
							<span><?php esc_html_e( 'Top', 'buddypress-ads-rotator' ); ?></span>
							<input type="number" name="wb_ads_rotator[margin][top]" value="<?php echo esc_attr( $wb_ads_rotator_margin_top ); ?>"/>
						<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
						</label>
						<label>
							<span><?php esc_html_e( 'Right', 'buddypress-ads-rotator' ); ?></span>
							<input type="number" name="wb_ads_rotator[margin][right]" value="<?php echo esc_attr( $wb_ads_rotator_margin_right ); ?>"/>
						<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
						</label>
						<label>
							<span><?php esc_html_e( 'Bottom', 'buddypress-ads-rotator' ); ?></span>
							<input type="number" name="wb_ads_rotator[margin][bottom]" value="<?php echo esc_attr( $wb_ads_rotator_margin_bottom ); ?>"/>
						<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
						</label>
						<label>
							<span><?php esc_html_e( 'Left', 'buddypress-ads-rotator' ); ?></span>
							<input type="number" name="wb_ads_rotator[margin][left]" value="<?php echo esc_attr( $wb_ads_rotator_margin_left ); ?>"/>
						<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
						</label>
					</div>
					<p class="wb_ads_margin_disc">
				<?php esc_html_e( 'Space around the ad, in pixels.', 'buddypress-ads-rotator' ); ?>
				</p>
				</div>
			</div>
		<?php do_action( 'wb_ads_rotator_options_after' ); ?>
		</div>
		<?php
	}

	/**
	 * WB Ads Metabox callback function.
	 *
	 * @param WP_Post $post Ad post being edited.
	 */
	public function wb_ads_rotator_render_display_conditions_box( $post ) {
		$post_id                        = $post->ID;
		$post_type                      = get_post_type();
		$wb_ads_rotator_values          = get_post_meta( $post_id, 'wb_ads_rotator_values', true );
		$wb_ads_rotator_enject_position = isset( $wb_ads_rotator_values['enject_after'] ) ? $wb_ads_rotator_values['enject_after'] : '3';
		$is_buddypress_active           = function_exists( 'bp_is_active' ) && bp_is_active( 'activity' );
		$wp_position                    = isset( $wb_ads_rotator_values['wp_position'] ) ? $wb_ads_rotator_values['wp_position'] : '';
		?>
		<div class="wb_ads_rotator-panel">
		<?php do_action( 'wb_ads_rotator_options_before' ); ?>

			<!-- WordPress Native Positions - Always Available -->
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'WordPress Position', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<select id="wb_ads_rotator_wp_position" name="wb_ads_rotator[wp_position]">
						<option value="" <?php selected( $wp_position, '' ); ?>><?php esc_html_e( '— None (Use Shortcode Only) —', 'buddypress-ads-rotator' ); ?></option>
						<option value="wp_before_content" <?php selected( $wp_position, 'wp_before_content' ); ?>><?php esc_html_e( 'Before Post/Page Content', 'buddypress-ads-rotator' ); ?></option>
						<option value="wp_after_content" <?php selected( $wp_position, 'wp_after_content' ); ?>><?php esc_html_e( 'After Post/Page Content', 'buddypress-ads-rotator' ); ?></option>
						<option value="wp_header" <?php selected( $wp_position, 'wp_header' ); ?>><?php esc_html_e( 'Site Header (wp_head)', 'buddypress-ads-rotator' ); ?></option>
						<option value="wp_footer" <?php selected( $wp_position, 'wp_footer' ); ?>><?php esc_html_e( 'Site Footer (wp_footer)', 'buddypress-ads-rotator' ); ?></option>
					</select>
					<p>
					<?php esc_html_e( 'Before or after content shows the ad on posts and pages. Site header and site footer show it on every page, BuddyPress pages included. Site header only outputs script and style tags, so use it for ad-network code.', 'buddypress-ads-rotator' ); ?>
					</p>
				</div>
			</div>

			<?php if ( $is_buddypress_active ) : ?>
			<!-- BuddyPress Options - Only when BuddyPress is Active -->
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'BuddyPress Activity Type', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<select id="wb_ads_rotator_activity_type" name="wb_ads_rotator[activity_type][]" multiple>
						<option value="sidewide_activity" <?php echo ( isset( $wb_ads_rotator_values['activity_type'] ) && in_array( 'sidewide_activity', $wb_ads_rotator_values['activity_type'], true ) ) ? 'selected' : ''; ?>><?php esc_html_e( 'Sitewide activity', 'buddypress-ads-rotator' ); ?></option>
						<?php if ( bp_is_active( 'groups' ) ) : ?>
						<option value="group_activity" <?php echo ( isset( $wb_ads_rotator_values['activity_type'] ) && in_array( 'group_activity', $wb_ads_rotator_values['activity_type'], true ) ) ? 'selected' : ''; ?>><?php esc_html_e( 'Group activity', 'buddypress-ads-rotator' ); ?></option>
						<?php endif; ?>
						<option value="members_activity" <?php echo ( isset( $wb_ads_rotator_values['activity_type'] ) && in_array( 'members_activity', $wb_ads_rotator_values['activity_type'], true ) ) ? 'selected' : ''; ?>><?php esc_html_e( 'Member profile activity', 'buddypress-ads-rotator' ); ?></option>
					</select>
					<p>
					<?php esc_html_e( 'Choose the activity streams this ad appears in. Leave it empty to keep the ad out of activity streams.', 'buddypress-ads-rotator' ); ?>
					</p>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'BuddyPress Activity Position', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<select id="wb_ads_rotator_activity_position" name="wb_ads_rotator[activity_position]">
						<option value="bp_before_activity_entry" <?php isset( $wb_ads_rotator_values['activity_position'] ) && selected( $wb_ads_rotator_values['activity_position'], 'bp_before_activity_entry' ); ?>><?php esc_html_e( 'Before an activity entry', 'buddypress-ads-rotator' ); ?></option>
						<option value="bp_activity_entry_content" <?php isset( $wb_ads_rotator_values['activity_position'] ) && selected( $wb_ads_rotator_values['activity_position'], 'bp_activity_entry_content' ); ?>><?php esc_html_e( 'Inside the activity entry content', 'buddypress-ads-rotator' ); ?></option>
						<option value="bp_after_activity_entry" <?php isset( $wb_ads_rotator_values['activity_position'] ) && selected( $wb_ads_rotator_values['activity_position'], 'bp_after_activity_entry' ); ?>><?php esc_html_e( 'After an activity entry', 'buddypress-ads-rotator' ); ?></option>
						<option value="bp_before_activity_entry_comments" <?php isset( $wb_ads_rotator_values['activity_position'] ) && selected( $wb_ads_rotator_values['activity_position'], 'bp_before_activity_entry_comments' ); ?>><?php esc_html_e( 'Before the entry comments', 'buddypress-ads-rotator' ); ?></option>
						<option value="bp_activity_entry_comments" <?php isset( $wb_ads_rotator_values['activity_position'] ) && selected( $wb_ads_rotator_values['activity_position'], 'bp_activity_entry_comments' ); ?>><?php esc_html_e( 'Inside the entry comments', 'buddypress-ads-rotator' ); ?></option>
						<option value="bp_after_activity_entry_comments" <?php isset( $wb_ads_rotator_values['activity_position'] ) && selected( $wb_ads_rotator_values['activity_position'], 'bp_after_activity_entry_comments' ); ?>><?php esc_html_e( 'After the entry comments', 'buddypress-ads-rotator' ); ?></option>
					</select>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<label for="wb_ads_rotator_enject_after"><?php esc_html_e( 'Show After Entry', 'buddypress-ads-rotator' ); ?></label>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="number" id="wb_ads_rotator_enject_after" name="wb_ads_rotator[enject_after]" min="1" value="<?php echo esc_attr( $wb_ads_rotator_enject_position ); ?>">
				<?php esc_html_e( 'Show the ad at entry number N of the stream. With Repeat on, it shows at every Nth entry.', 'buddypress-ads-rotator' ); ?>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<label for="wb_ads_rotator_repeat_position"><?php esc_html_e( 'Repeat', 'buddypress-ads-rotator' ); ?></label>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="checkbox" id="wb_ads_rotator_repeat_position" name="wb_ads_rotator[repeat_position]" value="yes" <?php esc_attr( isset( $wb_ads_rotator_values['repeat_position'] ) ? checked( $wb_ads_rotator_values['repeat_position'], 'yes' ) : '' ); ?>>
				<?php esc_html_e( 'Repeat the ad every N entries instead of showing it once.', 'buddypress-ads-rotator' ); ?>
				</div>
			</div>
			<?php else : ?>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-input">
					<p class="description">
					<?php
					if ( function_exists( 'bp_is_active' ) ) {
						esc_html_e( 'The BuddyPress Activity Streams component is off. Turn it on in Settings > BuddyPress to display ads in activity streams.', 'buddypress-ads-rotator' );
					} else {
						esc_html_e( 'BuddyPress is not active. Install and activate BuddyPress to display ads in activity streams.', 'buddypress-ads-rotator' );
					}
					?>
					</p>
				</div>
			</div>
			<?php endif; ?>
		<?php do_action( 'wb_ads_rotator_options_after' ); ?>
		</div>
		<?php
	}

	/**
	 * WB Ads Metabox callback function.
	 *
	 * @param WP_Post $post Ad post being edited.
	 */
	public function wb_ads_rotator_render_to_whom_box( $post ) {
		$post_id               = $post->ID;
		$post_type             = get_post_type();
		$wb_ads_rotator_values = get_post_meta( $post_id, 'wb_ads_rotator_values', true );
		?>
		<div class="wb_ads_rotator-panel">
		<?php do_action( 'wb_ads_rotator_options_before' ); ?>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Logged-In Visitor', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<select id="wb_ads_rotator_to_whom_visitor" name="wb_ads_rotator[to_whom][logged_in_visitor]">
						<option value="both_visitor"<?php isset( $wb_ads_rotator_values['to_whom']['logged_in_visitor'] ) && selected( $wb_ads_rotator_values['to_whom']['logged_in_visitor'], 'both_visitor' ); ?>><?php esc_html_e( 'Both Logged-in and Logged-out Visitor', 'buddypress-ads-rotator' ); ?></option>
						<option value="login_in"<?php isset( $wb_ads_rotator_values['to_whom']['logged_in_visitor'] ) && selected( $wb_ads_rotator_values['to_whom']['logged_in_visitor'], 'login_in' ); ?>><?php esc_html_e( 'Logged-in Visitor', 'buddypress-ads-rotator' ); ?></option>
						<option value="logout_out"<?php isset( $wb_ads_rotator_values['to_whom']['logged_in_visitor'] ) && selected( $wb_ads_rotator_values['to_whom']['logged_in_visitor'], 'logout_out' ); ?>><?php esc_html_e( 'Logged-out Visitor', 'buddypress-ads-rotator' ); ?></option>
					</select>
					<p>
					<?php esc_html_e( 'Show the ad to logged-in members, logged-out visitors, or both.', 'buddypress-ads-rotator' ); ?>
					</p>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Device', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<select id="wb_ads_rotator_to_whom_device" name="wb_ads_rotator[to_whom][device]">
						<option value="both-device"<?php isset( $wb_ads_rotator_values['to_whom']['device'] ) && selected( $wb_ads_rotator_values['to_whom']['device'], 'both-device' ); ?>><?php esc_html_e( 'Both Mobile and Desktop Devices', 'buddypress-ads-rotator' ); ?></option>
						<option value="mobile"<?php isset( $wb_ads_rotator_values['to_whom']['device'] ) && selected( $wb_ads_rotator_values['to_whom']['device'], 'mobile' ); ?>><?php esc_html_e( 'Mobile (including tablets)', 'buddypress-ads-rotator' ); ?></option>
						<option value="desktop"<?php isset( $wb_ads_rotator_values['to_whom']['device'] ) && selected( $wb_ads_rotator_values['to_whom']['device'], 'desktop' ); ?>><?php esc_html_e( 'Desktop', 'buddypress-ads-rotator' ); ?></option>
					</select>
					<p>
					<?php esc_html_e( 'Show the ad on mobile devices, desktops, or both. Tablets count as mobile devices.', 'buddypress-ads-rotator' ); ?>
					</p>
				</div>
			</div>
		<?php do_action( 'wb_ads_rotator_options_after' ); ?>
		</div>
		<?php
	}

	/**
	 * WB Ads Metabox callback function.
	 *
	 * @param WP_Post $post Ad post being edited.
	 */
	public function wb_ads_rotator_render_shortcode_box( $post ) {
		$post_id               = $post->ID;
		$post_type             = get_post_type();
		$wb_ads_rotator_values = get_post_meta( $post_id, 'wb_ads_rotator_values', true );
		?>
		<div class="wb_ads_rotator-panel">
		<?php do_action( 'wb_ads_rotator_options_before' ); ?>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-input ads-copy" data-code="[ads-shortcode ads_id=<?php echo absint( $post_id ); ?>]">
					<code><?php echo esc_html( '[ads-shortcode ads_id=' . absint( $post_id ) . ']' ); ?></code>
				</div>
				<span class="ads-shortcode-text shortcode-text-hide"><?php echo esc_attr__( 'Shortcode Copied!', 'buddypress-ads-rotator' ); ?></span>
			</div>
			<p>
			<?php esc_html_e( 'Paste this shortcode into any post, page or widget to show this ad there.', 'buddypress-ads-rotator' ); ?>
			</p>
		<?php do_action( 'wb_ads_rotator_options_after' ); ?>
		</div>
		<?php
	}

	/**
	 * Saved the post meta box value.
	 *
	 * @param int $post_id Get a Ads id.
	 */
	public function wb_ads_rotator_save_post_meta( $post_id ) {

		// Check if our nonce is set.
		if ( ! isset( $_POST['wb_ads_rotator_render_add_meta_box_nonce'] ) ) {
			return $post_id;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wb_ads_rotator_render_add_meta_box_nonce'] ) ), 'wb_ads_rotator_render_add_meta_box' ) ) {
			return $post_id;
		}
		// Bail if we're doing an auto save .
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( ! empty( $_POST['wb_ads_rotator'] ) ) {
			$ads_data = filter_input( INPUT_POST, 'wb_ads_rotator', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			update_post_meta( $post_id, 'wb_ads_rotator_values', $this->wb_ads_rotator_sanitize_values( (array) $ads_data ) );
		}
		do_action( 'wb_ads_rotator_admin_save_options' );
	}

	/**
	 * Sanitize the ad settings array field by field.
	 *
	 * The two ad-code fields keep raw markup (ad networks need script tags) only for
	 * users who may post unfiltered HTML; everyone else gets wp_kses_post().
	 *
	 * @since 1.6.0
	 * @param array $values Raw `wb_ads_rotator` payload.
	 * @return array Sanitized values.
	 */
	private function wb_ads_rotator_sanitize_values( $values ) {
		$clean = array();
		foreach ( $values as $key => $value ) {
			$key = sanitize_key( $key );
			if ( is_array( $value ) ) {
				$clean[ $key ] = $this->wb_ads_rotator_sanitize_values( $value );
				continue;
			}
			$value = (string) $value;
			switch ( $key ) {
				case 'plain_text_and_code':
				case 'rich_content':
					$clean[ $key ] = current_user_can( 'unfiltered_html' ) ? $value : wp_kses_post( $value );
					break;
				case 'image_link':
					$clean[ $key ] = esc_url_raw( $value );
					break;
				case 'ads_image':
					$clean[ $key ] = absint( $value );
					break;
				case 'enject_after':
					$clean[ $key ] = absint( $value ) ? absint( $value ) : 3; // Same default the form shows.
					break;
				case 'text_color':
				case 'bg_color':
					$clean[ $key ] = (string) sanitize_hex_color( $value );
					break;
				default:
					$clean[ $key ] = sanitize_text_field( $value );
			}
		}
		return $clean;
	}

	/**
	 * Whether an ad has no content for its selected type.
	 *
	 * An ad with no content renders an empty container in the stream, so it must not
	 * reach a published state. Only the field that belongs to the chosen ad type is
	 * considered - a plain-text ad does not need an image, and vice versa.
	 *
	 * @since 1.6.0
	 * @param array $values The `wb_ads_rotator_values` payload.
	 * @return bool True when the ad has nothing to render.
	 */
	private function wb_ads_rotator_is_empty( $values ) {
		if ( ! is_array( $values ) ) {
			return true;
		}

		$type = isset( $values['ads_type'] ) ? $values['ads_type'] : 'plain-text-and-code';

		switch ( $type ) {
			case 'image-ad':
				return empty( $values['ads_image'] );

			case 'rich-content':
				$content = isset( $values['rich_content'] ) ? $values['rich_content'] : '';
				// Media-only rich content is legitimate, so keep anything embedded.
				if ( preg_match( '/<(img|iframe|video|embed|object|svg)\b/i', $content ) ) {
					return false;
				}
				return '' === trim( wp_strip_all_tags( $content ) );

			case 'plain-text-and-code':
			default:
				$content = isset( $values['plain_text_and_code'] ) ? $values['plain_text_and_code'] : '';
				return '' === trim( $content );
		}
	}

	/**
	 * Keep an empty ad out of a published state.
	 *
	 * Runs on wp_insert_post_data, before the post row is written, so the ad is saved
	 * as a draft rather than published. The meta is still stored by the save_post
	 * handler, so nothing the author typed is lost - the ad simply stays a draft until
	 * it has content.
	 *
	 * @since 1.6.0
	 * @param array $data    Sanitized post data about to be written.
	 * @param array $postarr Raw post data as submitted.
	 * @return array Post data, forced to draft when the ad is empty.
	 */
	public function wb_ads_rotator_block_empty_publish( $data, $postarr ) {
		if ( ! isset( $data['post_type'] ) || 'wb-ads' !== $data['post_type'] ) {
			return $data;
		}

		if ( ! in_array( $data['post_status'], array( 'publish', 'future' ), true ) ) {
			return $data;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $data;
		}

		// The metabox payload when editing; the stored meta when publishing from
		// Quick Edit or a bulk action, where the metabox is not part of the request.
		// Core verifies the edit nonce before this filter runs.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['wb_ads_rotator'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$values = filter_input( INPUT_POST, 'wb_ads_rotator', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		} elseif ( isset( $postarr['meta_input']['wb_ads_rotator_values'] ) ) {
			// wp_insert_post() / REST callers pass the ad in meta_input; it is written after this filter.
			$values = $postarr['meta_input']['wb_ads_rotator_values'];
		} elseif ( ! empty( $postarr['ID'] ) ) {
			$values = get_post_meta( (int) $postarr['ID'], 'wb_ads_rotator_values', true );
		} else {
			$values = array();
		}

		if ( ! $this->wb_ads_rotator_is_empty( $values ) ) {
			return $data;
		}

		$data['post_status'] = 'draft';

		set_transient( 'wb_ads_rotator_empty_notice_' . get_current_user_id(), 1, 60 );

		return $data;
	}

	/**
	 * Tell the author why their empty ad was kept as a draft.
	 *
	 * @since 1.6.0
	 */
	public function wb_ads_rotator_empty_ad_notice() {
		$key = 'wb_ads_rotator_empty_notice_' . get_current_user_id();

		if ( ! get_transient( $key ) ) {
			return;
		}

		delete_transient( $key );

		printf(
			'<div class="notice notice-error is-dismissible"><p>%s</p></div>',
			esc_html__( 'This ad was saved as a draft because it has no content. Add the ad content for the selected ad type, then publish.', 'buddypress-ads-rotator' )
		);
	}

	/**
	 * Get a selected ads media.
	 */
	public function wb_ads_rotator_image() {
		// Anyone who can edit ads (the CPT uses page capabilities) may preview its image.
		if ( ! current_user_can( 'edit_pages' ) ) {
			wp_send_json_error();
		}
		// Check if our nonce is set.
		if ( ! isset( $_GET['nonce'] ) ) {
			wp_send_json_error();
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'ajax-nonce' ) ) {
			die( 'Busted!' );
		}
		if ( isset( $_GET['id'] ) ) {
			$image = wp_get_attachment_image( filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT ), 'medium', false, array( 'id' => 'wb-ads-preview-images' ) );
			$data  = array(
				'image' => $image,
			);
			wp_send_json_success( $data );
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Function Added the new column in data table columns.
	 *
	 * @param  array $columns Get the Columns.
	 */
	public function wb_ads_rotator_add_disable_admin_columns( $columns ) {
		unset( $columns['date'] );
		return array_merge(
			$columns,
			array(
				'ads-id'        => __( 'Ads ID', 'buddypress-ads-rotator' ),
				'ads-type'      => __( 'Ads Type', 'buddypress-ads-rotator' ),
				'disable-ads'   => __( 'Enable/Disable Ads', 'buddypress-ads-rotator' ),
				'ads-shortcode' => __( 'Shortcode', 'buddypress-ads-rotator' ),
				'date'          => __( 'Date', 'buddypress-ads-rotator' ),
			)
		);
	}

	/**
	 * Function Added the new column content.
	 *
	 * @param  string $column_key Contains the Columns Key.
	 * @param  int    $post_id Get a Ads ID.
	 */
	public function wb_ads_rotator_add_disable_column_content( $column_key, $post_id ) {

		switch ( $column_key ) {
			case 'ads-id':
				echo '<strong>' . esc_html( $post_id ) . '</strong>';
				break;
			case 'ads-type':
				$wb_ads_data = get_post_meta( $post_id, 'wb_ads_rotator_values', true );
				$wb_ads_type = isset( $wb_ads_data['ads_type'] ) ? $wb_ads_data['ads_type'] : '';
				if ( 'rich-content' === $wb_ads_type ) {
					echo '<strong>' . esc_html( 'Rich Content' ) . '</strong>';
				} elseif ( 'plain-text-and-code' === $wb_ads_type ) {
					echo '<strong>' . esc_html( 'Plain Text and Code' ) . '</strong>';
				} elseif ( 'image-ad' === $wb_ads_type ) {
					echo '<strong>' . esc_html( 'Image Ads' ) . '</strong>';
				}
				break;
			case 'disable-ads':
				$wb_ads_rotator_enable = get_post_meta( $post_id, 'wb_ads_enable', true );
				if ( 'enable' === $wb_ads_rotator_enable ) {
					echo '<label class="wb-ads-rotator-switch">
				<input type="checkbox" class="wb_ads_enable" data-ads-visible="disable" data-ads="' . absint( $post_id ) . '" checked>
				<span class="wb-ads-rotator-slider wb-ads-rotator-round"></span>
				</label>';
				} else {
					echo '<label class="wb-ads-rotator-switch">
				<input type="checkbox" class="wb_ads_enable" data-ads-visible="enable" data-ads="' . absint( $post_id ) . '">
				<span class="wb-ads-rotator-slider wb-ads-rotator-round"></span>
				</label>';
				}
				break;
			case 'ads-shortcode':
				echo '<div class="wb_ads_rotator-input wb-ads-shortcode-text shortcode-copy" data-shortcode="[ads-shortcode ads_id=' . absint( $post_id ) . ']">
					<code> ' . esc_html( '[ads-shortcode ads_id=' . $post_id . ']' ) . '</code>
					</div><span class="ads-shortcode-text shortcode-text-hide">' . esc_attr__( 'Shortcode Copied!', 'buddypress-ads-rotator' ) . '</span>';

		}
	}

	/**
	 * This Function is updates the default option.
	 *
	 * @param string  $new_status New post status.
	 * @param string  $old_status Old post status.
	 * @param WP_Post $post       Post object.
	 */
	public function wb_ads_rotator_update_default_value_on_ads_publish( $new_status, $old_status, $post ) {

		$wb_ads_id = $post->ID;
		$post_type = $post->post_type;
		// Switch a new ad on when it is first published. Updating a published ad must keep the
		// owner's Enable/Disable choice: transition_post_status also fires publish -> publish.
		if ( 'publish' === $new_status && 'wb-ads' === $post_type && '' === get_post_meta( $wb_ads_id, 'wb_ads_enable', true ) ) {
			update_post_meta( $wb_ads_id, 'wb_ads_enable', 'enable' );
		}
	}
	/**
	 * This Function is handle the ajax callback.
	 */
	public function wb_ads_rotator_ajax_enable_callback() {
		// Toggling writes post meta; the edit_post check on the ad itself follows the nonce.
		// Check if our nonce is set.
		if ( ! isset( $_POST['nonce'] ) ) {
			die;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ajax-nonce' ) ) {
			die( 'Busted!' );
		}
		$action       = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';
		$action_value = isset( $_POST['visible'] ) ? sanitize_text_field( wp_unslash( $_POST['visible'] ) ) : '';
		$wb_ads_id    = isset( $_POST['datanotice'] ) ? absint( $_POST['datanotice'] ) : 0;
		if ( 'wb-ads' !== get_post_type( $wb_ads_id ) || ! current_user_can( 'edit_post', $wb_ads_id ) ) {
			die;
		}
		if ( 'wb_ads_rotator_enable' === $action && 'enable' === $action_value ) {
			update_post_meta( $wb_ads_id, 'wb_ads_enable', 'enable' );
		} elseif ( 'wb_ads_rotator_enable' === $action && 'disable' === $action_value ) {
			update_post_meta( $wb_ads_id, 'wb_ads_enable', 'disable' );
		}
		// Toggling enable/disable changes which ads render on the front end;
		// bust the cached eligible-ad set.
		if ( class_exists( 'Buddypress_Ads_Rotator_Cache' ) ) {
			Buddypress_Ads_Rotator_Cache::invalidate();
		}
		die;
	}

	/**
	 * Added Remove image button for Image Ads.
	 *
	 * @return void
	 */
	public function wb_ads_remove_uploaded_image() {
		// Anyone who can edit ads (the CPT uses page capabilities) may reset an ad image.
		if ( ! current_user_can( 'edit_pages' ) ) {
			die;
		}
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( 'Busted!' );
		}
		$wb_ads_placeholder_img = plugin_dir_url( __FILE__ ) . 'images/wb-ads-placeholder-image.jpg';
		wp_send_json( $wb_ads_placeholder_img );
	}
}
