/**
 * CLASS UKMVideresendItem
 * Håndterer alt for et gitt videresendingsitem (tittel, innslag uten titler)
**/
var UKMMedieItem = function( $, innslag, tittel ) {
	let self = {
		/****************************************/
		/** ID OG GUI-ID TIL AKTUELT OBJEKT		*/
		/****************************************/

		/**
		 * Hent ID til gitt innslag som concat streng
		**/
		getId: function() {
			return innslag +'-'+ tittel;
		},

		/**
		 * Hent ID med #-prefix for GUI-referanse
		**/
		getGUIId: function() {
			return '#' + self.getId();
		},

		/****************************************/
		/** AJAX REQUESTS AND GUI LOAD STATUS	*/
		/****************************************/

		/**
		 * Faktisk gjør AJAX-kall
		**/
		ajax: function( action, param_data ) {
			var data = {
				action: 'UKMVideresending_ajax',
				subaction: action,
				innslag: innslag,
				tittel: tittel
			};

			if( null !== param_data && undefined !== param_data ) {
				for (var attrname in param_data) {
					data[ attrname ] = param_data[ attrname ];
				}
			}
			
			$.post(
				ajaxurl, 
				data, 
				function(response) {
					if( response !== null && response !== undefined ) {
						try {
							response = JSON.parse( response );
						} catch( error ) {
							response = null;
						}
					}
					
					/* HANDLING GJENNOMFØRT. HÅNDTER RESPONS */
					if( response !== null && response.success ) {
						self['handle'+ action.charAt(0).toUpperCase() + action.slice(1) ].call( null, response );
					} else {
						alert('Beklager, kunne ikke hente informasjon fra server');
						self.cancelShow();
					}
				}
			);	
		},
		
		show: function( html ) {
			$( self.getGUIId() ).addClass('selected');
			$( self.getGUIId() +' .kontroll').html( html );
			$( self.getGUIId() +' .kontroll').append( twigJSavbryt.render() );
			$( self.getGUIId() +' .header .actions').fadeOut(150, function(){
				$( self.getGUIId() +' .kontroll').slideDown();
			});
		},
		
		cancelShow: function() {
			$( self.getGUIId() ).removeClass('selected');

			$( self.getGUIId() +' .kontroll').slideUp( 150, function(){
				$( self.getGUIId() +' .header .actions').fadeIn();
			});
		},
		
		
		/* BILDER KNYTTET TIL INNSLAGET */		
		showBildeOpplasting: function(){
			self.show( twigJSbildelastopp.render() );
		},

		showBilder: function( kunstverk ){
			self.show( 'Vennligst vent, laster inn bilder...' );
			self.ajax('bilderShow', { kunstverk: kunstverk });
		},
		
		handleBilderShow: function( response ) {
			self.show( twigJSbildevelg.render( response ) );
		},
		
		velgBildeInnslag: function( bilde_id ) {
			self.ajax('bildeSet', {'bilde': bilde_id } );
		},
		
		velgBildeKunstverk: function( bilde_id ) {
			self.ajax('bildeSet', {'bilde': bilde_id, 'kunstverk': true } );
		},
		handleBildeSet: function( response ) {
			self.cancelShow();
			
			if( response.POST.kunstverk ) {
				$( '.bilder.kunstverk-'+ response.POST.tittel ).each(function(){
					$(this).find( '.valgt_bilde' ).html( twigJSbildevalgt.render( response ) );
				});
			} else {
				$( '.bilder.innslag-'+ response.POST.innslag ).each(function(){
					$(this).find( '.valgt_bilde' ).html( twigJSbildevalgt.render( response ) );
				});
			}
		},

		/* FILMER (UKM-TV) */
		showFilmInfo: function() {
			self.show( twigJSfilmlastopp.render() );
		},
		
		showFilmer: function() {
			self.show( 'Vennligst vent, laster inn filmer...' );
			self.ajax('filmerShow');
		},
		
		handleFilmerShow: function( response ){
			self.show( twigJSfilmer.render( response ) );
		},
		
		/* PLAYBACK */
		showPlaybackInfo: function() {
			self.show( twigJSplayback.render() );
		},
		
		showPlaybackFiles: function() {
			self.show( 'Vennligst vent, laster inn playbackfiler...' );
			self.ajax('playbackShow');
		},

		handlePlaybackShow: function( response ) {
			self.show( twigJSplaybackfiler.render( response ) ) ;
		},
		
		/* FILM (ORIGINAL-FIL ) */
		showFilminfo: function() {
			self.show( twigJSfilmoriginal.render() );
		}
		
	};

	
	return self;
}