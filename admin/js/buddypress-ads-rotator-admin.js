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

		$('#wb_ads_rotator_activity_type').selectize({
			placeholder: "-- choose a activity type --",
			plugins: ['remove_button'],
		});

		$('#wb_ads_rotator_activity_position').selectize({
			placeholder: "-- choose a activity position --",
			plugins: ['remove_button'],
		});

		$('#wb_ads_rotator_to_whom_device').selectize({
			placeholder: "-- choose a condition --",
			plugins: ['remove_button'],
		});

		$('#wb_ads_rotator_to_whom_visitor').selectize({
			placeholder: "-- choose a condition --",
			plugins: ['remove_button'],
		});
		
		$("#wb-ad-rotator-container-id").keyup(function () {
			var Text = $(this).val();
			Text = Text.toLowerCase();
			Text = Text.replace(/[^a-zA-Z0-9]+/g, '-');
			$("#wb-ad-rotator-container-id").val(Text);
		});

		$("#wb-ad-rotator-container-class").keyup(function () {
			var Text = $(this).val();
			Text = Text.toLowerCase();
			Text = Text.replace(/[^a-zA-Z0-9]+/g, '-');
			$("#wb-ad-rotator-container-class").val(Text);
		});

		$(document).on("click", "#wb-ad-rotator-allow-shortcode", function (e) {
			if ($(this).is(":checked")) {
				$(".wb_ads_rotator-wrapper #wb-ad-rotator-allow-shortcode-warning").show(200);
			} else {
				$(".wb_ads_rotator-wrapper #wb-ad-rotator-allow-shortcode-warning").hide(200);
			}
		});

		$(document).on("click", "#wb-ad-rotator-allow-php", function (e) {
			if ($(this).is(":checked")) {
				$(".wb_ads_rotator-wrapper #wb-ad-rotator-allow-php-warning").show(200);
			} else {
				$(".wb_ads_rotator-wrapper #wb-ad-rotator-allow-php-warning").hide(200);
			}
		});
		
		function myFunction() {
			//var text = wp.codeEditor.getValue();
			var text = jQuery("#wb-ad-rotator-content-plain").val();
			
			return text;
		}
		var texts = myFunction();
		var enabled_php = jQuery('#wb-ad-rotator-allow-php').prop('checked');
		var enabled_sc = jQuery('#wb-ad-rotator-allow-shortcode').prop('checked');
		if (enabled_php && ! /\<\?php/.test(texts) ) {
			console.log(texts);
			$(".wb_ads_rotator-wrapper #wb-ad-rotator-allow-php-warning").show(200);
			} else {
			$(".wb_ads_rotator-wrapper #wb-ad-rotator-allow-php-warning").hide(200);
			}
		if (enabled_sc && ! /\[[^\]]+\]/.test(texts)) {
			$(".wb_ads_rotator-wrapper #wb-ad-rotator-allow-shortcode-warning").show(200);
			} else {
			$(".wb_ads_rotator-wrapper #wb-ad-rotator-allow-shortcode-warning").hide(200);
			}
			

		$(document).ready(function () {
			$('input[type="radio"]').click(function () {
				var inputValue = $(this).attr("value");
				if (inputValue === 'plain-text-and-code' ) {
					$('.plain-text-and-code').show(500);
					$('.rich-content').hide(500);
					$('.image-ad').hide(500);
				} 
				if(inputValue === 'rich-content') {
					$('.plain-text-and-code').hide(500);
					$('.rich-content').show(500);
					$('.image-ad').hide(500);
				} 
				if(inputValue === 'image-ad') {
					$('.plain-text-and-code').hide(500);
					$('.rich-content').hide(500);
					$('.image-ad').show(500);
				}
				
			});
		});
		$('.wb_ads_enable').click(function (e) {
			var datanotice = $(this).data("ads");
			var datanoticevisible = $(this).data("ads-visible");
			var data = {
				action: 'wb_ads_rotator_enable',
				visible: datanoticevisible,
				datanotice: datanotice,
			};
			$.post(ajaxurl, data, function (response) {
				console.log(data);
				location.reload();
			});
		});
		/**
		 * Image ad uploader
		 */
		$(document).on(
			'click',
			'.select-ads-image #wb_ads_rotator_select_image',
			function (e) {

			e.preventDefault()

			var button = $(this)
				// WP 3.5+ uploader
				var file_frame
				window.formfield = ''
			// If the media frame already exists, reopen it.
			if (file_frame) {
				// file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
				file_frame.open()
				return
			}

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				id: 'advads_type_image_wp_media',
				title: button.data('uploaderTitle'),
				button: {
					text: button.data('uploaderButtonText')
				},
				library: {
					type: 'image'
				},
				multiple: false // only allow one file to be selected
			})

			// When an image is selected, run a callback.
			file_frame.on('select', function () {

				var selection = file_frame.state().get('selection')
				selection.each(function (attachment, index) {
					attachment = attachment.toJSON()
					if (0 === index) {
						// place first attachment in field
						$('#wb_ads_image_id').val(attachment.id)
						$('.wb_ads_rotator-input input[name="wb_ads_rotator[size][width]"]').val(attachment.width)
						$('.wb_ads_rotator-input input[name="wb_ads_rotator[size][height]"]').val(attachment.height)
						// update image preview
						var new_image = '<img width="' + attachment.width + '" height="' + attachment.height +
							'" title="' + attachment.title + '" alt="' + attachment.alt + '" src="' + attachment.url + '"/>'
						$('#wb-ads-preview-image').html(new_image)
						$('#wb-ads-image-edit-link').attr('href', attachment.editLink)
					}
				})
			})

			// Finally, open the modal
			file_frame.open()
		})
		
		wp.codeEditor.initialize($('#wb-ad-rotator-content-plain'), cm_settings);
		
	});
})( jQuery );
