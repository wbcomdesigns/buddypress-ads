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

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/buddypress-ads-rotator-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'wb_ads_rotator-selectize', plugin_dir_url( __FILE__ ) . 'css/selectize.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-ads-rotator-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'wb_ads_rotator-selectize-min', plugin_dir_url( __FILE__ ) . 'js/selectize.min.js', array( 'jquery' ), $this->version, false );
		$cm_settings['codeEditor'] = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
		wp_localize_script( 'jquery', 'cm_settings', $cm_settings );

		wp_enqueue_script( 'wp-theme-plugin-editor' );
		wp_enqueue_style( 'wp-codemirror' );
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
			'menu_icon'          => __( 'dashicons-chart-line', 'buddypress-ads-rotator' ),
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

	}

	/**
	 * WB Ads Metabox callback function.
	 *
	 * @param array $post Get a Post Object.
	 */
	public function wb_ads_rotator_render_add_meta_box( $post ) {
		global $post;
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
					<input type="radio" name="wb_ads_rotator[ads_type]" class="wb-ads_types" id="wb_ads_rotator_type" value="plain-text-and-code" <?php ( isset( $wb_ads_rotator_values['ads_type'] ) ) ? checked( $wb_ads_rotator_values['ads_type'], 'plain-text-and-code' ) : ''; ?>/>
					<p>
						<?php esc_html_e( 'Any ad network, Amazon, customized AdSense codes, shortcodes, and code like JavaScript, HTML or PHP.', 'buddypress-ads-rotator' ); ?>
					</p>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
					<?php esc_html_e( 'Rich Content', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="radio" name="wb_ads_rotator[ads_type]" class="wb-ads_types" id="wb_ads_rotator_type" value="rich-content" <?php ( isset( $wb_ads_rotator_values['ads_type'] ) ) ? checked( $wb_ads_rotator_values['ads_type'], 'rich-content' ) : ''; ?>/>
					<p>
						<?php esc_html_e( 'The full content editor from WordPress with all features like shortcodes, image upload or styling, but also simple text/html mode for scripts and code.', 'buddypress-ads-rotator' ); ?>
					</p>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
					<?php esc_html_e( 'Image Ad', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="radio" name="wb_ads_rotator[ads_type]" class="wb-ads_types" id="wb_ads_rotator_type" value="image-ad" <?php ( isset( $wb_ads_rotator_values['ads_type'] ) ) ? checked( $wb_ads_rotator_values['ads_type'], 'image-ad' ) : ''; ?>/>
					<p>
						<?php esc_html_e( 'Ads in various image formats.', 'buddypress-ads-rotator' ); ?>
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
	 * @param array $post Get a Post Object.
	 */
	public function wb_ads_rotator_render_parameter_box( $post ) {
		global $post;
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'wb_ads_rotator_render_add_meta_box', 'wb_ads_rotator_render_add_meta_box_nonce' );
		$post_id                   = $post->ID;
		$post_type                 = get_post_type();
		$wb_ads_rotator_values     = get_post_meta( $post_id, 'wb_ads_rotator_values', true );
		$wb_ads_rotator_width      = isset( $wb_ads_rotator_values['size']['width'] ) ? $wb_ads_rotator_values['size']['width'] : '';
		$wb_ads_rotator_height     = isset( $wb_ads_rotator_values['size']['height'] ) ? $wb_ads_rotator_values['size']['height'] : '';
		$wb_ads_rotator_plain_text = isset( $wb_ads_rotator_values['plain_text_and_code'] ) ? $wb_ads_rotator_values['plain_text_and_code'] : '';
		?>
		<div class="wb_ads_rotator-panel">
			<?php do_action( 'wb_ads_rotator_options_before' ); ?>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-input">
					<?php esc_html_e( 'Insert plain text or code into this field.', 'buddypress-ads-rotator' ); ?>
					<?php echo '<textarea id="wb-ad-rotator-content-plain" name="wb_ads_rotator[plain_text_and_code]">' . esc_textarea( $wb_ads_rotator_plain_text ) . '</textarea>'; ?>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
					<?php esc_html_e( 'Allow PHP', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="checkbox" id="wb-ad-rotator-parameters-php" name="wb_ads_rotator[allow_php]" value="yes" <?php ( isset( $wb_ads_rotator_values['allow_php'] ) ) ? checked( $wb_ads_rotator_values['allow_php'], 'yes' ) : ''; ?> />
					<?php esc_html_e( 'Execute PHP code (wrapped in <?php ?>)', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb-ad-rotator-error-message" id="wb-ad-rotator-parameters-php-warning" style="display:none;"><?php esc_html_e( 'No PHP tag detected in your code.', 'buddypress-ads-rotator' ); ?><?php esc_html_e( 'Uncheck this checkbox for improved performance.', 'buddypress-ads-rotator' ); ?></div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
					<?php esc_html_e( 'Allow Shortcodes', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="checkbox" id="wb-ad-rotator-parameters-php" name="wb_ads_rotator[allow_shortcode]" value="yes" <?php ( isset( $wb_ads_rotator_values['allow_shortcode'] ) ) ? checked( $wb_ads_rotator_values['allow_shortcode'], 'yes' ) : ''; ?> />
					<?php esc_html_e( 'Execute shortcodes', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb-ad-rotator-error-message" id="wb-ad-rotator-parameters-php-warning" style="display:none;"><?php esc_html_e( 'No shortcode detected in your code.', 'buddypress-ads-rotator' ); ?><?php esc_html_e( 'Uncheck this checkbox for improved performance.', 'buddypress-ads-rotator' ); ?></div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
					<?php esc_html_e( 'Size', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<label>
						<?php esc_html_e( 'Width', 'buddypress-ads-rotator' ); ?>
						<input type="number" name="wb_ads_rotator[size][width]" value="<?php echo esc_attr( $wb_ads_rotator_width ); ?>"/>
						<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
					</label>
					<label>
						<?php esc_html_e( 'Heigh', 'buddypress-ads-rotator' ); ?>
						<input type="number" name="wb_ads_rotator[size][height]" value="<?php echo esc_attr( $wb_ads_rotator_height ); ?>"/>
						<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
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
	 * @param array $post Get a Post Object.
	 */
	public function wb_ads_rotator_render_layout_box( $post ) {
		global $post;
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'wb_ads_rotator_render_add_meta_box', 'wb_ads_rotator_render_add_meta_box_nonce' );
		$post_id                        = $post->ID;
		$post_type                      = get_post_type();
		$wb_ads_rotator_values          = get_post_meta( $post_id, 'wb_ads_rotator_values', true );
		$wb_ads_rotator_margin_top      = isset( $wb_ads_rotator_values['margin']['top'] ) ? $wb_ads_rotator_values['margin']['top'] : '';
		$wb_ads_rotator_margin_right    = isset( $wb_ads_rotator_values['margin']['right'] ) ? $wb_ads_rotator_values['margin']['right'] : '';
		$wb_ads_rotator_margin_bottom   = isset( $wb_ads_rotator_values['margin']['bottom'] ) ? $wb_ads_rotator_values['margin']['bottom'] : '';
		$wb_ads_rotator_margin_left     = isset( $wb_ads_rotator_values['margin']['left'] ) ? $wb_ads_rotator_values['margin']['left'] : '';
		$wb_ads_rotator_container_id    = isset( $wb_ads_rotator_values['container_id'] ) ? $wb_ads_rotator_values['container_id'] : '';
		$wb_ads_rotator_container_class = isset( $wb_ads_rotator_value['container_classes'] ) ? $wb_ads_rotator_values['container_classes'] : '';

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
					<?php esc_html_e( 'Position', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<label title="default">
						<input type="radio" name="wb_ads_rotator[ads_position]" value="default" <?php ( isset( $wb_ads_rotator_values['ads_position'] ) ) ? checked( $wb_ads_rotator_values['ads_position'], 'default' ) : ''; ?>/>
						<?php esc_html_e( 'Default', 'buddypress-ads-rotator' ); ?>
					</label>
					<label title="left">
						<input type="radio" name="wb_ads_rotator[ads_position]" value="left" <?php ( isset( $wb_ads_rotator_values['ads_position'] ) ) ? checked( $wb_ads_rotator_values['ads_position'], 'left' ) : ''; ?>/>
						<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'images/output-left.png' ); ?>" width="60" height="40">
					</label>
					<label title="center">
						<input type="radio" name="wb_ads_rotator[ads_position]" value="center" <?php ( isset( $wb_ads_rotator_values['ads_position'] ) ) ? checked( $wb_ads_rotator_values['ads_position'], 'center' ) : ''; ?>/>
						<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'images/output-center.png' ); ?>" width="60" height="40">
					</label>
					<label title="right">
						<input type="radio" name="wb_ads_rotator[ads_position]" value="right" <?php ( isset( $wb_ads_rotator_values['ads_position'] ) ) ? checked( $wb_ads_rotator_values['ads_position'], 'right' ) : ''; ?>/>
						<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'images/output-right.png' ); ?>" width="60" height="40">
					</label>
					<p>
						<input type="checkbox" name="wb_ads_rotator[clearfix]" value="yes" <?php ( isset( $wb_ads_rotator_values['clearfix'] ) ) ? checked( $wb_ads_rotator_values['clearfix'], 'yes' ) : ''; ?>/>
						<?php esc_html_e( 'Check this if you don’t want the following elements to float around the ad. (adds a clearfix)', 'buddypress-ads-rotator' ); ?>
					</p>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
					<?php esc_html_e( 'Margin', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<label>
						<?php esc_html_e( 'Top', 'buddypress-ads-rotator' ); ?>
						<input type="number" name="wb_ads_rotator[margin][top]" value="<?php echo esc_attr( $wb_ads_rotator_margin_top ); ?>"/>
						<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
					</label>
					<label>
						<?php esc_html_e( 'Right', 'buddypress-ads-rotator' ); ?>
						<input type="number" name="wb_ads_rotator[margin][right]" value="<?php echo esc_attr( $wb_ads_rotator_margin_right ); ?>"/>
						<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
					</label>
					<label>
						<?php esc_html_e( 'Bottom', 'buddypress-ads-rotator' ); ?>
						<input type="number" name="wb_ads_rotator[margin][bottom]" value="<?php echo esc_attr( $wb_ads_rotator_margin_bottom ); ?>"/>
						<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
					</label>
					<label>
						<?php esc_html_e( 'Left', 'buddypress-ads-rotator' ); ?>
						<input type="number" name="wb_ads_rotator[margin][left]" value="<?php echo esc_attr( $wb_ads_rotator_margin_left ); ?>"/>
						<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
					</label>
					<p>
						<?php echo esc_html_e( 'tip: use this to add a margin around the ad', 'buddypress-ads-rotator' ); ?>
					</p>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
					<?php esc_html_e( 'Container ID', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="text" id="wb-ad-rotator-parameters-php" name="wb_ads_rotator[container_id]" value="<?php echo esc_attr( $wb_ads_rotator_container_id ); ?>"  />
					<p>
						<?php esc_html_e( 'Specify the id of the ad container. Leave blank for random or no id.  An id-like string with only letters in lower case, numbers, and hyphens.', 'buddypress-ads-rotator' ); ?>
					</p>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
					<?php esc_html_e( 'Container Classes', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="text" id="wb-ad-rotator-parameters-php" name="wb_ads_rotator[container_classes]" value="<?php echo esc_attr( $wb_ads_rotator_container_class ); ?>"  />
				<p>
					<?php esc_html_e( 'Specify one or more classes for the container. Separate multiple classes with a space.', 'buddypress-ads-rotator' ); ?>
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
	 * @param array $post Get a Post Object.
	 */
	public function wb_ads_rotator_render_display_conditions_box( $post ) {
		global $post;
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
					<?php esc_html_e( 'New condition', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<select id="wb_ads_rotator_condition" name="wb_ads_rotator[condition]">
						<option value=""<?php isset( $wb_ads_rotator_values['condition'] ) && selected( $wb_ads_rotator_values['condition'], '' ); ?>><?php esc_html_e( '-- choose a condition --', 'buddypress-ads-rotator' ); ?></option>
						<option value="before_activity_entery" <?php isset( $wb_ads_rotator_values['condition'] ) && selected( $wb_ads_rotator_values['condition'], 'before_activity_entery' ); ?>><?php esc_html_e( 'before activity entry', 'buddypress-ads-rotator' ); ?></option>
						<option value="activity_entry_content" <?php isset( $wb_ads_rotator_values['condition'] ) && selected( $wb_ads_rotator_values['condition'], 'activity_entry_content' ); ?>><?php esc_html_e( 'activity entry content', 'buddypress-ads-rotator' ); ?></option>
						<option value="after_activity_entry" <?php isset( $wb_ads_rotator_values['condition'] ) && selected( $wb_ads_rotator_values['condition'], 'after_activity_entry' ); ?>><?php esc_html_e( 'after activity entry', 'buddypress-ads-rotator' ); ?></option>
						<option value="before_activity_entry_comments" <?php isset( $wb_ads_rotator_values['condition'] ) && selected( $wb_ads_rotator_values['condition'], 'before_activity_entry_comments' ); ?>><?php esc_html_e( 'before activity entry comments', 'buddypress-ads-rotator' ); ?></option>
						<option value="activity_entry_comments" <?php isset( $wb_ads_rotator_values['condition'] ) && selected( $wb_ads_rotator_values['condition'], 'activity_entry_comments' ); ?>><?php esc_html_e( 'activity entry comments', 'buddypress-ads-rotator' ); ?></option>
						<option value="after_activity_entry_comments" <?php isset( $wb_ads_rotator_values['condition'] ) && selected( $wb_ads_rotator_values['condition'], 'after_activity_entry_comments' ); ?>><?php esc_html_e( 'after activity entry comments', 'buddypress-ads-rotator' ); ?></option>
						<option value="directory_group_item" <?php isset( $wb_ads_rotator_values['condition'] ) && selected( $wb_ads_rotator_values['condition'], 'directory_group_item' ); ?>><?php esc_html_e( 'directory group item', 'buddypress-ads-rotator' ); ?></option>
						<option value="directory_members_item" <?php isset( $wb_ads_rotator_values['condition'] ) && selected( $wb_ads_rotator_values['condition'], 'directory_members_item' ); ?>><?php esc_html_e( 'directory members item', 'buddypress-ads-rotator' ); ?></option>
					</select>
					<p>
						<?php esc_html_e( 'A page with this ad on it must match all of the following conditions.', 'buddypress-ads-rotator' ); ?>
					</p>
					<p>
						<?php esc_html_e( 'If you want to display the ad everywhere, dont do anything here.', 'buddypress-ads-rotator' ); ?>
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
	 * @param array $post Get a Post Object.
	 */
	public function wb_ads_rotator_render_to_whom_box( $post ) {
		global $post;
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
					<?php esc_html_e( 'new condition', 'buddypress() - ads - rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<select id="wb_ads_rotator_to_whom" name="wb_ads_rotator[to_whom]">
						<option value=""<?php isset( $wb_ads_rotator_values['to_whom'] ) && selected( $wb_ads_rotator_values['to_whom'], '' ); ?>><?php esc_html_e( '-- choose a condition --', 'buddypress-ads-rotator' ); ?></option>
						<option value="login_user"<?php isset( $wb_ads_rotator_values['to_whom'] ) && selected( $wb_ads_rotator_values['to_whom'], 'login_user' ); ?>><?php esc_html_e( 'Login User', 'buddypress-ads-rotator' ); ?></option>
						<option value="logout_user"<?php isset( $wb_ads_rotator_values['to_whom'] ) && selected( $wb_ads_rotator_values['to_whom'], 'logout_user' ); ?>><?php esc_html_e( 'Logout User', 'buddypress-ads-rotator' ); ?></option>
					</select>				
					<p>
						<?php esc_html_e( 'Visitor conditions limit the number of users who can see your ad. There is no need to set visitor conditions if you want all users to see the ad.', 'buddypress-ads-rotator' ); ?>
					</p>
				</div>
			</div>
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

		$wb_ads_rotator_metabox_nonce = $_POST['wb_ads_rotator_render_add_meta_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $wb_ads_rotator_metabox_nonce, 'wb_ads_rotator_render_add_meta_box' ) ) {
			return $post_id;
		}
		// Bail if we're doing an auto save .
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
					// if our current user can't edit this post, bail.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}
		if ( ! empty( $_POST['wb_ads_rotator'] ) ) {
			update_post_meta( $post_id, 'wb_ads_rotator_values', $_POST['wb_ads_rotator'] );
		}
					do_action( 'wb_ads_rotator_admin_save_options' );
	}

}
