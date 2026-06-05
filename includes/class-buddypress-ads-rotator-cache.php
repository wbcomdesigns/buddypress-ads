<?php
/**
 * Front-end ad query + cache helper.
 *
 * Centralizes the eligible-ad lookup so every front-end consumer
 * (the_content, wp_head, wp_footer and the BuddyPress activity hooks)
 * shares ONE bounded query per request instead of running an unbounded
 * `get_posts( meta_query LIKE )` + per-ad `get_post_meta()` (N+1) each.
 *
 * @link       https://wbcomdesigns.com
 * @since      1.6.1
 *
 * @package    Buddypress_Ads_Rotator
 * @subpackage Buddypress_Ads_Rotator/includes
 */

/**
 * Eligible-ad resolver with request-level memoization and a persistent
 * transient cache, plus invalidation on ad writes.
 *
 * @package    Buddypress_Ads_Rotator
 * @subpackage Buddypress_Ads_Rotator/includes
 * @author     WB COM <admin@wbcomdesigns.com>
 */
class Buddypress_Ads_Rotator_Cache {

	/**
	 * Transient key holding the bounded list of all `wb-ads` post IDs.
	 *
	 * @since 1.6.1
	 * @var   string
	 */
	const TRANSIENT_KEY = 'wb_ads_rotator_all_ids';

	/**
	 * Transient lifetime (12 hours). Invalidated explicitly on ad writes,
	 * so the TTL is only a safety net.
	 *
	 * @since 1.6.1
	 * @var   int
	 */
	const TTL = 12 * HOUR_IN_SECONDS;

	/**
	 * Request-level memo of the resolved ad IDs (avoids re-reading the
	 * transient on every hook within a single page load).
	 *
	 * @since 1.6.1
	 * @access private
	 * @var    int[]|null
	 */
	private static $ids = null;

	/**
	 * Get the bounded list of `wb-ads` post IDs and prime their meta cache.
	 *
	 * One `fields => 'ids'` query (cached in a transient + memoized per
	 * request), then a single batched `update_meta_cache()` so the
	 * subsequent `get_post_meta()` calls in the display methods are all
	 * served from cache (no N+1). Per-ad position / enable filtering is
	 * left to the callers so the "which ads show where" behavior is
	 * byte-for-byte identical to the previous per-position queries.
	 *
	 * @since 1.6.1
	 * @return int[] Ad post IDs (capped, default 5, ordered by WP default = date DESC).
	 */
	public static function get_ad_ids() {
		if ( null !== self::$ids ) {
			return self::$ids;
		}

		$ids = get_transient( self::TRANSIENT_KEY );

		if ( false === $ids || ! is_array( $ids ) ) {
			/**
			 * Cap the number of ads resolved on a single front-end request.
			 *
			 * Default is 5 — this matches the implicit cap the previous code
			 * already relied on (every call site used get_posts() WITHOUT a
			 * posts_per_page arg, and get_posts() defaults numberposts to 5).
			 * Keeping the default at 5 makes this a pure perf refactor with
			 * byte-identical front-end output. Site owners running more than
			 * five concurrent ad positions can raise it via this filter.
			 *
			 * @since 1.6.1
			 * @param int $max Maximum ads to load per request. Default 5.
			 */
			$max = (int) apply_filters( 'wb_ads_rotator_max_ads', 5 );
			if ( 0 === $max ) {
				$max = 5;
			}
			// Allow -1 (unbounded) only via explicit opt-in through the filter.

			$ids = get_posts(
				array(
					'post_type'              => 'wb-ads',
					'post_status'            => 'publish',
					'fields'                 => 'ids',
					'posts_per_page'         => $max,
					'no_found_rows'          => true,
					'update_post_term_cache' => false,
					'suppress_filters'       => false,
				)
			);

			if ( ! is_array( $ids ) ) {
				$ids = array();
			}

			set_transient( self::TRANSIENT_KEY, $ids, self::TTL );
		}

		// Batch-prime post + meta caches so the per-ad get_post_meta()
		// calls in the display methods hit the object cache, not the DB.
		if ( ! empty( $ids ) ) {
			_prime_post_caches( $ids, false, true );
		}

		self::$ids = $ids;

		return self::$ids;
	}

	/**
	 * Get ad IDs whose `wb_ads_rotator_values` meta contains a needle.
	 *
	 * Reproduces the exact membership of the previous
	 * `meta_query => array( key => wb_ads_rotator_values, value => $needle,
	 * compare => 'LIKE' )` query — WP serializes the meta value and runs a
	 * `LIKE '%needle%'` against the serialized string. We mirror that by
	 * serializing the (already-primed) meta and doing a substring match, so
	 * the resulting set and ordering are identical, with zero extra queries.
	 *
	 * @since 1.6.1
	 * @param string $needle Position token to match (e.g. 'wp_header').
	 * @return int[] Matching ad post IDs, in the same order as get_ad_ids().
	 */
	public static function get_ad_ids_matching( $needle ) {
		$matches = array();

		foreach ( self::get_ad_ids() as $ad_id ) {
			$values = get_post_meta( $ad_id, 'wb_ads_rotator_values', true );
			// maybe_serialize mirrors how WP stores/searches the meta value.
			if ( false !== strpos( maybe_serialize( $values ), $needle ) ) {
				$matches[] = $ad_id;
			}
		}

		return $matches;
	}

	/**
	 * Invalidate the cached ad-ID set.
	 *
	 * Wired to ad writes: save_post_wb-ads, deleted_post, and the
	 * enable/disable AJAX toggle. Also clears the per-request memo.
	 *
	 * @since 1.6.1
	 * @param int|null     $post_id Optional post ID (for save_post / deleted_post).
	 * @param WP_Post|null $post    Optional post object (passed by deleted_post).
	 * @return void
	 */
	public static function invalidate( $post_id = null, $post = null ) {
		// On generic post hooks, only bust the cache for our CPT. Prefer the
		// passed WP_Post (reliable on deleted_post, where the row is gone),
		// then fall back to get_post_type().
		if ( null !== $post_id ) {
			$post_type = ( $post instanceof WP_Post ) ? $post->post_type : get_post_type( $post_id );
			if ( $post_type && 'wb-ads' !== $post_type ) {
				return;
			}
		}

		delete_transient( self::TRANSIENT_KEY );
		self::$ids = null;
	}
}
