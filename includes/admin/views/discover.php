<?php
/**
 * Discover partial: ecosystem cross-promotion (read-only display view).
 *
 * Rendered inside shell.php. Pure presentation - product cards linking out
 * to other free Wbcom Designs tools. No forms, settings, options, or AJAX.
 *
 * @package    Buddypress_Ads_Rotator
 * @subpackage Buddypress_Ads_Rotator/includes/admin/views
 * @since      1.6.0
 */

defined( 'ABSPATH' ) || exit;

$wbar_ecosystem_img = BUDDYPRESS_ADS_ROTATOR_URL . 'assets/images/ecosystem/';
$wbar_ecosystem_dir = defined( 'BUDDYPRESS_ADS_ROTATOR_PATH' ) ? BUDDYPRESS_ADS_ROTATOR_PATH . 'assets/images/ecosystem/' : '';

/*
 * Each product: brand mark (shipped in assets/images/ecosystem/), name, a
 * short admin-specific blurb (worded differently from readme.txt), and the
 * wbcomdesigns.com download URL.
 */
$wbar_ecosystem = array(
	array(
		'name' => __( 'BuddyX', 'buddypress-ads-rotator' ),
		'logo' => 'buddyx.png',
		'icon' => 'admin-appearance',
		'desc' => __( 'A free, fast community theme for BuddyPress, BuddyBoss and PeepSo with a modern layout and dark mode.', 'buddypress-ads-rotator' ),
		'url'  => 'https://wbcomdesigns.com/downloads/buddyx-theme/',
	),
	array(
		'name' => __( 'BuddyNext', 'buddypress-ads-rotator' ),
		'logo' => 'buddynext.svg',
		'icon' => 'groups',
		'desc' => __( 'The full community stack: activity feeds, member spaces, profiles, and private messaging on WordPress.', 'buddypress-ads-rotator' ),
		'url'  => 'https://wbcomdesigns.com/downloads/buddynext/',
	),
	array(
		'name' => __( 'Jetonomy', 'buddypress-ads-rotator' ),
		'logo' => 'jetonomy.svg',
		'icon' => 'format-chat',
		'desc' => __( 'Self-moderating forums and Q&A boards that stay fast well beyond 100,000 topics.', 'buddypress-ads-rotator' ),
		'url'  => 'https://wbcomdesigns.com/downloads/jetonomy/',
	),
	array(
		'name' => __( 'Mediaverse', 'buddypress-ads-rotator' ),
		'logo' => 'mediaverse.svg',
		'icon' => 'format-gallery',
		'desc' => __( 'A photo and video hub with albums, reactions, following, and private chat.', 'buddypress-ads-rotator' ),
		'url'  => 'https://wbcomdesigns.com/downloads/mediaverse/',
	),
	array(
		'name' => __( 'Eventonomy', 'buddypress-ads-rotator' ),
		'logo' => 'eventonomy.svg',
		'icon' => 'calendar-alt',
		'desc' => __( 'Run community events with RSVPs, calendars, and front-end submissions.', 'buddypress-ads-rotator' ),
		'url'  => 'https://wbcomdesigns.com/downloads/eventonomy/',
	),
	array(
		'name' => __( 'WB Gamification', 'buddypress-ads-rotator' ),
		'logo' => 'wb-gamification.svg',
		'icon' => 'awards',
		'desc' => __( 'Reward members with points, badges, and leaderboards to keep engagement high.', 'buddypress-ads-rotator' ),
		'url'  => 'https://wbcomdesigns.com/downloads/wordpress-gamification-plugin/',
	),
	array(
		'name' => __( 'Listora', 'buddypress-ads-rotator' ),
		'logo' => 'listora.svg',
		'icon' => 'list-view',
		'desc' => __( 'Searchable directories with ten listing types, ratings, maps, and front-end submissions.', 'buddypress-ads-rotator' ),
		'url'  => 'https://wbcomdesigns.com/downloads/listora/',
	),
	array(
		'name' => __( 'WP Career Board', 'buddypress-ads-rotator' ),
		'logo' => 'wp-career-board.svg',
		'icon' => 'businessman',
		'desc' => __( 'Add a job board with front-end listings, applications, and employer profiles.', 'buddypress-ads-rotator' ),
		'url'  => 'https://wbcomdesigns.com/downloads/wp-career-board/',
	),
	array(
		'name' => __( 'Learnomy', 'buddypress-ads-rotator' ),
		'logo' => 'learnomy.svg',
		'icon' => 'welcome-learn-more',
		'desc' => __( 'Create, sell, and auto-grade online courses, then hand out certificates automatically.', 'buddypress-ads-rotator' ),
		'url'  => 'https://wbcomdesigns.com/downloads/learnomy/',
	),
);
?>

<div class="wbar-card">
	<div class="wbar-card__head">
		<p class="wbar-card__title"><?php esc_html_e( 'More Free Tools from Wbcom Designs', 'buddypress-ads-rotator' ); ?></p>
		<p class="wbar-card__desc"><?php esc_html_e( 'BuddyPress Ads places your promotions where members already spend time. These free Wbcom Designs tools grow the community around those ads: the theme and network itself, forums, media, events, gamification, directories, jobs, and courses.', 'buddypress-ads-rotator' ); ?></p>
	</div>
	<div class="wbar-card__body">
		<div class="wbar-discover-grid">
			<?php foreach ( $wbar_ecosystem as $wbar_product ) : ?>
				<div class="wbar-discover-card">
					<span class="wbar-discover-card__logo" aria-hidden="true">
						<?php if ( $wbar_ecosystem_dir && file_exists( $wbar_ecosystem_dir . $wbar_product['logo'] ) ) : ?>
							<img src="<?php echo esc_url( $wbar_ecosystem_img . $wbar_product['logo'] ); ?>" alt="<?php echo esc_attr( $wbar_product['name'] ); ?>" width="52" height="52" loading="lazy" />
						<?php else : ?>
							<span class="dashicons dashicons-<?php echo esc_attr( isset( $wbar_product['icon'] ) ? $wbar_product['icon'] : 'admin-plugins' ); ?>"></span>
						<?php endif; ?>
					</span>
					<h3 class="wbar-discover-card__title"><?php echo esc_html( $wbar_product['name'] ); ?></h3>
					<p class="wbar-discover-card__desc"><?php echo esc_html( $wbar_product['desc'] ); ?></p>
					<a class="wbar-btn wbar-btn-secondary wbar-discover-card__cta" href="<?php echo esc_url( $wbar_product['url'] ); ?>" target="_blank" rel="noopener noreferrer">
						<?php esc_html_e( 'Get it free', 'buddypress-ads-rotator' ); ?>
						<span class="dashicons dashicons-external" aria-hidden="true"></span>
						<span class="screen-reader-text">
							<?php
							/* translators: %s: product name. */
							echo esc_html( sprintf( __( '%s (opens in a new tab)', 'buddypress-ads-rotator' ), $wbar_product['name'] ) );
							?>
						</span>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
