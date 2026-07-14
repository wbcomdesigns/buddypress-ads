<?php
/**
 * Shared helpers used by both the admin and public sides.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.6.0
 *
 * @package    Buddypress_Ads_Rotator
 * @subpackage Buddypress_Ads_Rotator/includes
 */

defined( 'ABSPATH' ) || exit;

/**
 * The exact pair the colour pickers submit when the owner never touched them.
 *
 * `<input type="color">` has no empty state. The background field ships with no value,
 * which the browser renders as #000000, and the text field defaults to #FFFFFF - so both
 * are submitted on every save whether or not the owner ever opened the colour section.
 */
const WB_ADS_ROTATOR_DEFAULT_BG   = '#000000';
const WB_ADS_ROTATOR_DEFAULT_TEXT = '#ffffff';

/**
 * Whether this ad's wrapper should be painted with the owner's chosen colours.
 *
 * The ad's own content belongs to whoever made it and we never restyle it. The wrapper
 * is ours, and it must not paint a colour nobody asked for: an ad that hard-codes
 * background and text inline can never sit correctly on a page whose colours it does not
 * know about, which is why ads showed as a solid block in dark mode.
 *
 * So the wrapper stays transparent and inherits its text colour unless the owner
 * deliberately chose colours - in which case those are honoured exactly, in every colour
 * mode, because an ad's colours are the advertiser's call and not ours to override.
 *
 * @since 1.6.0
 * @param array $values The `wb_ads_rotator_values` payload.
 * @return bool True when the owner deliberately chose the wrapper's colours.
 */
function wb_ads_rotator_uses_custom_colors( $values ) {
	if ( ! is_array( $values ) ) {
		return false;
	}

	// Saved since the "Custom colours" checkbox exists: the owner said so explicitly.
	if ( array_key_exists( 'custom_colors', $values ) ) {
		return ! empty( $values['custom_colors'] );
	}

	$bg   = isset( $values['bg_color'] ) ? strtolower( trim( $values['bg_color'] ) ) : '';
	$text = isset( $values['text_color'] ) ? strtolower( trim( $values['text_color'] ) ) : '';

	if ( '' === $bg && '' === $text ) {
		return false;
	}

	/*
	 * Saved before the checkbox existed. The pickers could not express "no colour", so
	 * this exact pair is what an ad carries when the owner never opened the colour
	 * section - treat it as never chosen. Any other combination was a deliberate act and
	 * is left exactly as the owner set it.
	 */
	if ( WB_ADS_ROTATOR_DEFAULT_BG === $bg && WB_ADS_ROTATOR_DEFAULT_TEXT === $text ) {
		return false;
	}

	return true;
}

/**
 * The wrapper's colour declarations, or '' when the owner chose none.
 *
 * @since 1.6.0
 * @param array $values The `wb_ads_rotator_values` payload.
 * @return string CSS declarations, ready to concatenate into a style attribute.
 */
function wb_ads_rotator_color_styles( $values ) {
	if ( ! wb_ads_rotator_uses_custom_colors( $values ) ) {
		return '';
	}

	$styles = '';

	if ( ! empty( $values['bg_color'] ) ) {
		$styles .= 'background-color:' . $values['bg_color'] . ';';
	}

	if ( ! empty( $values['text_color'] ) ) {
		$styles .= 'color:' . $values['text_color'] . ';';
	}

	return $styles;
}
