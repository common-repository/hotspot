(function($){

	XoloHotspotInstallTheme = {

		/**
		 * Init
		 */
		init: function() {
			this._bind();
		},

		/**
		 * Binds events for the Hotspot.
		 *
		 * @since 1.3.2
		 * 
		 * @access private
		 * @method _bind
		 */
		_bind: function()
		{
			$( document ).on( 'click', '.xolo-hotspots-theme-not-installed', XoloHotspotInstallTheme._install_and_activate );
			$( document ).on( 'click', '.xolo-hotspots-theme-installed-but-inactive', XoloHotspotInstallTheme._activateTheme );
			$( document ).on('wp-theme-install-success' , XoloHotspotInstallTheme._activateTheme);
		},

		/**
		 * Activate Theme
		 *
		 * @since 1.3.2
		 */
		_activateTheme: function( event, response ) {
			event.preventDefault();

			$('#xolo-theme-activation-xl a').addClass('processing');

			if( response ) {
				$('#xolo-theme-activation-xl a').text( XoloHotspotInstallThemeVars.installed );
			} else {
				$('#xolo-theme-activation-xl a').text( XoloHotspotInstallThemeVars.activating );
			}

			// WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
			setTimeout( function() {

				$.ajax({
					url: XoloHotspotInstallThemeVars.ajaxurl,
					type: 'POST',
					data: {
						'action' : 'xolo-hotspots-activate-theme'
					},
				})
				.done(function (result) {
					if( result.success ) {
						$('#xolo-theme-activation-xl a').text( XoloHotspotInstallThemeVars.activated );
						$('#xolo-theme-activation-xl a').removeClass( 'shake' );

						setTimeout(function() {
							location.reload();
						}, 1000);
					}

				});

			}, 3000 );

		},

		/**
		 * Install and activate
		 *
		 * @since 1.3.2
		 * 
		 * @param  {object} event Current event.
		 * @return void
		 */
		_install_and_activate: function(event ) {
			event.preventDefault();
			var theme_slug = $(this).data('theme-slug') || '';
			console.log( theme_slug );
			console.log( 'yes' );

			var btn = $( event.target );

			if ( btn.hasClass( 'processing' ) ) {
				return;
			}

			btn.text( XoloHotspotInstallThemeVars.installing ).addClass('processing');

			if ( wp.updates.shouldRequestFilesystemCredentials && ! wp.updates.ajaxLocked ) {
				wp.updates.requestFilesystemCredentials( event );
			}
			
			wp.updates.installTheme( {
				slug: theme_slug
			});
		}

	};

	/**
	 * Initialize
	 */
	$(function(){
		XoloHotspotInstallTheme.init();
	});

})(jQuery);