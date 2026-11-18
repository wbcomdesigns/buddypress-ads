/**
 * Wbcom Designs - BuddyPress Ads: Admin JS (toast + confirm helpers).
 *
 * Skill Part 6 rules 10 + 12: no browser alert()/confirm() anywhere.
 * Exposes window.wbarToast() and window.wbarConfirm() so this file and
 * any future extensions share one feedback surface.
 *
 * Loads only on the BuddyPress Ads settings page + the shared WB
 * Plugins hub. The wb-ads CPT metabox screens keep their own legacy
 * script (admin/js/buddypress-ads-rotator-admin.js).
 *
 * @since 1.6.0
 */
( function ( $ ) {
	'use strict';

	var i18n = ( window.wbarAdmin && window.wbarAdmin.i18n ) || {};

	/* --- Toast ------------------------------------------------ */

	function getToastHost() {
		var host = document.querySelector( '.wbar-toast-host' );
		if ( ! host ) {
			host = document.createElement( 'div' );
			host.className = 'wbar-toast-host';
			document.body.appendChild( host );
		}
		return host;
	}

	function toast( message, tone ) {
		tone = tone || 'info';
		var host = getToastHost();
		var el   = document.createElement( 'div' );
		el.className   = 'wbar-toast wbar-toast--' + tone;
		el.setAttribute( 'role', 'status' );
		el.textContent = String( message );
		host.appendChild( el );

		requestAnimationFrame( function () {
			el.classList.add( 'wbar-toast--visible' );
		} );

		window.setTimeout( function () {
			el.classList.remove( 'wbar-toast--visible' );
			window.setTimeout( function () {
				if ( el.parentNode ) {
					el.parentNode.removeChild( el );
				}
			}, 250 );
		}, 3600 );
	}

	window.wbarToast = toast;

	/* --- Confirm modal (returns a Promise) -------------------- */

	function confirmModal( opts ) {
		opts = opts || {};
		return new Promise( function ( resolve ) {
			var backdrop = document.createElement( 'div' );
			backdrop.className = 'wbar-confirm-backdrop';

			var card = document.createElement( 'div' );
			card.className = 'wbar-confirm';
			card.setAttribute( 'role', 'dialog' );
			card.setAttribute( 'aria-modal', 'true' );

			var title = document.createElement( 'h2' );
			title.className = 'wbar-confirm__title';
			title.textContent = opts.title || '';
			if ( opts.title ) { card.appendChild( title ); }

			var desc = document.createElement( 'p' );
			desc.className = 'wbar-confirm__desc';
			desc.textContent = opts.message || i18n.confirmDanger || '';
			if ( opts.message || i18n.confirmDanger ) { card.appendChild( desc ); }

			var actions = document.createElement( 'div' );
			actions.className = 'wbar-confirm__actions';

			var cancelBtn = document.createElement( 'button' );
			cancelBtn.type = 'button';
			cancelBtn.className = 'wbar-btn wbar-btn-secondary';
			cancelBtn.textContent = opts.cancelLabel || i18n.confirmCancel || 'Cancel';

			var confirmBtn = document.createElement( 'button' );
			confirmBtn.type = 'button';
			confirmBtn.className = 'wbar-btn ' + ( 'danger' === opts.tone ? 'wbar-btn-danger' : 'wbar-btn-primary' );
			confirmBtn.textContent = opts.confirmLabel || i18n.confirmContinue || 'Continue';

			actions.appendChild( cancelBtn );
			actions.appendChild( confirmBtn );
			card.appendChild( actions );
			backdrop.appendChild( card );
			document.body.appendChild( backdrop );

			function cleanup( result ) {
				document.removeEventListener( 'keydown', onKey );
				if ( backdrop.parentNode ) {
					backdrop.parentNode.removeChild( backdrop );
				}
				resolve( result );
			}

			function onKey( e ) {
				if ( 'Escape' === e.key ) { cleanup( false ); }
				if ( 'Enter' === e.key ) { cleanup( true ); }
			}

			cancelBtn.addEventListener( 'click', function () { cleanup( false ); } );
			confirmBtn.addEventListener( 'click', function () { cleanup( true ); } );
			backdrop.addEventListener( 'click', function ( e ) {
				if ( e.target === backdrop ) { cleanup( false ); }
			} );
			document.addEventListener( 'keydown', onKey );
			confirmBtn.focus();
		} );
	}

	window.wbarConfirm = confirmModal;

	/* --- Code editor on the Scripts/Styles textareas ---------- */

	$( function () {
		var cmSettings = window.cm_settings || {};
		[ 'wbar-scripts-header', 'wbar-scripts-body', 'wbar-scripts-footer' ].forEach( function ( id ) {
			var $field = $( '#' + id );
			if ( $field.length && window.wp && wp.codeEditor && cmSettings.codeEditor ) {
				wp.codeEditor.initialize( $field, cmSettings );
			}
		} );

		// Intercept destructive links/buttons that opt in via data-wbar-confirm.
		$( document ).on( 'click', '[data-wbar-confirm]', function ( e ) {
			var $el = $( this );
			if ( $el.attr( 'data-action' ) ) { return; }
			if ( $el.data( 'wbar-confirm-ok' ) ) { return; }
			e.preventDefault();
			var message = $el.data( 'wbar-confirm' ) || i18n.confirmDanger;
			var tone    = $el.data( 'wbar-confirm-tone' ) || 'danger';
			confirmModal( { message: message, tone: tone } ).then( function ( ok ) {
				if ( ! ok ) { return; }
				$el.data( 'wbar-confirm-ok', true );
				if ( $el.is( 'a' ) ) {
					window.location.href = $el.attr( 'href' );
				} else if ( $el.is( 'button' ) || $el.is( 'input' ) ) {
					var form = $el.closest( 'form' ).get( 0 );
					if ( ! form ) { return; }
					var btnName  = $el.attr( 'name' ) || '';
					var btnValue = $el.attr( 'value' ) || '1';
					if ( btnName ) {
						var hidden   = document.createElement( 'input' );
						hidden.type  = 'hidden';
						hidden.name  = btnName;
						hidden.value = btnValue;
						form.appendChild( hidden );
					}
					form.submit();
				}
			} );
		} );
	} );

} )( jQuery );
