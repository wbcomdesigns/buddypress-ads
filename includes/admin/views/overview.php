<?php
/**
 * Overview dashboard partial: rendered inside layout.php.
 *
 * @package    Buddypress_Ads_Rotator
 * @subpackage Buddypress_Ads_Rotator/includes/admin/views
 * @since      1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Parent view provides $settings + $page_url.
 *
 * @var array  $settings buddypress-ads-rotator_admin_scripts_options.
 * @var string $page_url Base settings page URL.
 */

// Ad counts via dedicated COUNT(*) — never count( get_posts() ). The
// enabled count uses a meta_query, which is acceptable here: this runs
// once per admin overview render, bounded to the small wb-ads set.
$wbar_total_counts = (array) wp_count_posts( 'wb-ads' );
$wbar_published    = isset( $wbar_total_counts['publish'] ) ? (int) $wbar_total_counts['publish'] : 0;
$wbar_draft        = isset( $wbar_total_counts['draft'] ) ? (int) $wbar_total_counts['draft'] : 0;

$wbar_enabled_query = new WP_Query(
	array(
		'post_type'      => 'wb-ads',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'no_found_rows'  => false,
		// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Admin-only, runs once per overview render on the small wb-ads set.
		'meta_query'     => array(
			array(
				'key'   => 'wb_ads_enable',
				'value' => 'enable',
			),
		),
	)
);

$wbar_enabled = (int) $wbar_enabled_query->found_posts;

$wbar_bp_active  = class_exists( 'BuddyPress' );
$wbar_bp_streams = $wbar_bp_active && bp_is_active( 'activity' );

// Global scripts/styles status.
$wbar_header_on = ! empty( $settings['header'] );
$wbar_body_on   = ! empty( $settings['body'] );
$wbar_footer_on = ! empty( $settings['footer'] );

$wbar_all_ads_url = admin_url( 'edit.php?post_type=wb-ads' );
$wbar_new_ad_url  = admin_url( 'post-new.php?post_type=wb-ads' );
$wbar_scripts_url = $page_url . '&tab=scripts';
?>

<div class="wbar-stats-grid">
	<div class="wbar-stat">
		<p class="wbar-stat__label"><?php esc_html_e( 'Published Ads', 'buddypress-ads-rotator' ); ?></p>
		<p class="wbar-stat__value"><?php echo esc_html( number_format_i18n( $wbar_published ) ); ?></p>
		<p class="wbar-stat__trend"><?php esc_html_e( 'Ads ready to display', 'buddypress-ads-rotator' ); ?></p>
	</div>
	<div class="wbar-stat">
		<p class="wbar-stat__label"><?php esc_html_e( 'Enabled Ads', 'buddypress-ads-rotator' ); ?></p>
		<p class="wbar-stat__value"><?php echo esc_html( number_format_i18n( $wbar_enabled ) ); ?></p>
		<p class="wbar-stat__trend"><?php esc_html_e( 'Published ads with the toggle on', 'buddypress-ads-rotator' ); ?></p>
	</div>
	<div class="wbar-stat">
		<p class="wbar-stat__label"><?php esc_html_e( 'Drafts', 'buddypress-ads-rotator' ); ?></p>
		<p class="wbar-stat__value"><?php echo esc_html( number_format_i18n( $wbar_draft ) ); ?></p>
		<p class="wbar-stat__trend"><?php esc_html_e( 'Ads not yet published', 'buddypress-ads-rotator' ); ?></p>
	</div>
	<div class="wbar-stat">
		<p class="wbar-stat__label"><?php esc_html_e( 'BuddyPress', 'buddypress-ads-rotator' ); ?></p>
		<p class="wbar-stat__value">
			<?php echo $wbar_bp_active ? esc_html__( 'Active', 'buddypress-ads-rotator' ) : esc_html__( 'Off', 'buddypress-ads-rotator' ); ?>
		</p>
		<p class="wbar-stat__trend">
			<?php
			if ( $wbar_bp_streams ) {
				esc_html_e( 'Activity stream ad positions available', 'buddypress-ads-rotator' );
			} elseif ( $wbar_bp_active ) {
				esc_html_e( 'Activity component is off: WordPress positions only', 'buddypress-ads-rotator' );
			} else {
				esc_html_e( 'WordPress positions only', 'buddypress-ads-rotator' );
			}
			?>
		</p>
	</div>
</div>

<?php if ( ! $wbar_bp_active ) : ?>
	<div class="wbar-notice wbar-notice--info">
		<span class="dashicons dashicons-info-outline" aria-hidden="true"></span>
		<p>
			<?php esc_html_e( 'BuddyPress is not active. Activity-stream ad positions are disabled. Ads still display on posts, pages, the site header and footer, and through the shortcode.', 'buddypress-ads-rotator' ); ?>
		</p>
	</div>
<?php endif; ?>

<div class="wbar-card">
	<div class="wbar-card__head">
		<p class="wbar-card__title"><?php esc_html_e( 'Global Scripts & Styles', 'buddypress-ads-rotator' ); ?></p>
		<p class="wbar-card__desc"><?php esc_html_e( 'Custom markup injected into every page. Manage these on the Scripts / Styles tab.', 'buddypress-ads-rotator' ); ?></p>
	</div>
	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_html_e( 'Header markup', 'buddypress-ads-rotator' ); ?></th>
			<td>
				<?php if ( $wbar_header_on ) : ?>
					<span class="wbar-status wbar-status--on"><?php esc_html_e( 'On: custom code is injected into the site header', 'buddypress-ads-rotator' ); ?></span>
				<?php else : ?>
					<span class="wbar-status wbar-status--off"><?php esc_html_e( 'Empty', 'buddypress-ads-rotator' ); ?></span>
					<a href="<?php echo esc_url( $wbar_scripts_url ); ?>" class="wbar-inline-hint"><?php esc_html_e( 'Add code', 'buddypress-ads-rotator' ); ?></a>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Body markup', 'buddypress-ads-rotator' ); ?></th>
			<td>
				<?php if ( $wbar_body_on ) : ?>
					<span class="wbar-status wbar-status--on"><?php esc_html_e( 'On: custom code is injected into the body', 'buddypress-ads-rotator' ); ?></span>
				<?php else : ?>
					<span class="wbar-status wbar-status--off"><?php esc_html_e( 'Empty', 'buddypress-ads-rotator' ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Footer markup', 'buddypress-ads-rotator' ); ?></th>
			<td>
				<?php if ( $wbar_footer_on ) : ?>
					<span class="wbar-status wbar-status--on"><?php esc_html_e( 'On: custom code is injected into the site footer', 'buddypress-ads-rotator' ); ?></span>
				<?php else : ?>
					<span class="wbar-status wbar-status--off"><?php esc_html_e( 'Empty', 'buddypress-ads-rotator' ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
	</table>
</div>

<div class="wbar-card">
	<div class="wbar-card__head">
		<p class="wbar-card__title"><?php esc_html_e( 'Quick Actions', 'buddypress-ads-rotator' ); ?></p>
	</div>
	<div class="wbar-card__body">
		<div class="wbar-save-bar wbar-action-row">
			<a href="<?php echo esc_url( $wbar_new_ad_url ); ?>" class="wbar-btn wbar-btn-primary">
				<span class="dashicons dashicons-plus-alt" aria-hidden="true"></span>
				<?php esc_html_e( 'Add New Ad', 'buddypress-ads-rotator' ); ?>
			</a>
			<a href="<?php echo esc_url( $wbar_all_ads_url ); ?>" class="wbar-btn wbar-btn-secondary">
				<span class="dashicons dashicons-list-view" aria-hidden="true"></span>
				<?php esc_html_e( 'Manage All Ads', 'buddypress-ads-rotator' ); ?>
			</a>
			<a href="<?php echo esc_url( $wbar_scripts_url ); ?>" class="wbar-btn wbar-btn-secondary">
				<span class="dashicons dashicons-editor-code" aria-hidden="true"></span>
				<?php esc_html_e( 'Scripts / Styles', 'buddypress-ads-rotator' ); ?>
			</a>
		</div>
	</div>
</div>
