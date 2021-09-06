(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	jQuery(document).ready(function ($) {

		$('#wb_ads_rotator_condition').selectize({
			placeholder: "-- choose a condition --",
			plugins: ['remove_button'],
		});

		$('#wb_ads_rotator_to_whom').selectize({
			placeholder: "-- choose a condition --",
			plugins: ['remove_button'],
		});

		jQuery(document).ready(function ($) {
			wp.codeEditor.initialize($('#wb-ad-rotator-content-plain'), cm_settings);
		});

		$(document).on("click", "#wb-ad-rotator-parameters-php", function (e) {
			if ($(this).is(":checked")) {
				$(".wb_ads_rotator-wrapper #wb-ad-rotator-parameters-php-warning").show(200);
			} else {
				$(".wb_ads_rotator-wrapper #wb-ad-rotator-parameters-php-warning").hide(200);
			}
		});

		

		
		
	});
})( jQuery );
