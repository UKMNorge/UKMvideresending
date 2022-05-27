/**
 * CLASS UKMVideresendItem
 * Håndterer alt for et gitt videresendingsitem (tittel, innslag uten titler)
**/
var UKMLederItem = function( $, id, type, navn, mobil, epost ) {
	var events = emitter('leder');
	var overnatting = new Map();
	
	var self = {
		/****************************************/
		/** ID OG GUI-ID TIL AKTUELT OBJEKT		*/
		/****************************************/

		/**
		 * Hent ID til gitt innslag som concat streng
		**/
		getId: function() {
			return id;
		},

		/**
		 * Hent ID med #-prefix for GUI-referanse
		**/
		getSelector: function() {
			return '#leder_' + self.getId();
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
				leder: self.getId(),
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
					}
				}
			);	
		},
		
		init: function() {
			self.setNavn( navn );
			self.setType( type );
			self.showStatus();
			
			self.on('changeType', () => {self.showStatus; self.loadNetter()});
			self.on('changeNavn', self.showStatus);
			self.on('changeMobil', self.showStatus);
			self.on('changeEpost', self.showStatus);
		},
		
		setNavn: function( _navn ) {
			$( self.getSelector() + ' .row.header .navn').html( _navn );
			navn = _navn;
			self.emit('changeNavn');
		},
		getNavn: function() {
			return navn;
		},
		
		setType: function( _type ) {
			type = _type;
			$( self.getSelector() + ' .row.header .type').html( self.getTypeNice() +':' );
			self.emit('changeType');
		},
		getType: function(){
			return type;
		},
		getTypeNice: function(){
			switch( type ) {
				case 'hoved':
				case 'utstilling':
				case 'reise':
					return type.capitalize() +'leder';
				case 'turist':
				case 'ledsager':
					return type.capitalize();
				case 'sykerom':
					return type.capitalize();
			}
		},
		
	
		setMobil: function( _mobil ) {
			mobil = _mobil;
			self.emit('changeMobil');
		},
		getMobil: function(){
			return mobil;
		},
		
		setEpost: function( _epost ) {
			epost = _epost;
			self.emit('changeEpost');
		},
		getEpost: function(){
			return epost;
		},


		loadNetter: function(){
			$( self.getSelector() +' input.natt:checked').each(function(){
				self.setNatt( 
					$(this).attr('data-dag') +'_'+ $(this).attr('data-mnd'),
					$(this).val()
				);
			});
		},
		
		setNatt: function( dato, sted ) {
			self.emit('valgtNatt', [dato, sted, self]);
			overnatting.set( dato, sted );
			self.saveNatt( dato, sted, self.getType() );
			self.showStatus();
		},
		
		showStatus: function() {
			if(self.getType() == 'sykerom') { 
				$( self.getSelector() + ' .row.header .status').addClass('text-danger').html('');
				return;
			}

			var mangler = [];

			if( navn == null || navn == undefined || navn == '' ) {
				mangler.push('navn');
			}
			
			if( mobil == null || mobil == undefined || mobil == '' || mobil.length != 8 ) {
				mangler.push('mobilnummer');
			}
			
			if( epost == null || epost == undefined || epost == '' ) {
				mangler.push('e-postadresse');
			}

			// netter peker til den globale netter-variabelen
			// som er et array med datoer
			var status_natt = netter.length - overnatting.size;
			if( status_natt > 0 ) {
				mangler.push( (netter.length - overnatting.size) +' overnatting' + (status_natt != 1 ? 'er' : '' ) );
			}
			
			var setning = 'Mangler ';
			for( i=0; i<mangler.length; i++) {
				setning += mangler[ i ];
				if ( i == mangler.length-1 ) {
					setning += '.';
				} else if( i == mangler.length-2  ) {
					setning += ' og ';
				} else {
					setning += ', ';
				}
			}
			
			if( mangler.length > 0 ) {
				$( self.getSelector() + ' .row.header .status').addClass('text-danger').html( setning );
			} else {
				$( self.getSelector() + ' .row.header .status').removeClass('text-danger').addClass('text-success').html('');
			}
		},

		/**
		 * LAGRE LEDER
		**/
		save: function() {
			$( self.getSelector() +' button.leder_save' ).html('Lagrer..').addClass('btn-primary').removeClass('btn-success');
			var data = $( self.getSelector() ).find('form.leder_edit_form').serializeArray();
			self.ajax('lederSave', data);
		},
		
		handleLederSave: function( response ){
			self.loadNetter();
			$( self.getSelector() +' button.leder_save' ).html('Lagret!').addClass('btn-success').removeClass('btn-primary');
			setTimeout(
				function(){
					$( self.getSelector() +' button.leder_save' ).html('Lagre');
				}, 
				2100
			);
		},

		/**
		 * LAGRE OVERNATTING
		**/
		saveNatt: function( dato, sted, lederType ) {
			self.ajax('lederSaveNatt', {
				dato: dato, 
				sted: sted,
				leder_type: lederType,
			});
		},
		
		handleLederSaveNatt: function( response ) {
			// do nothing
		},
		
		/****************************************/
		/** EVENT HANDLERS						*/
		/****************************************/
		on: function( event, callback ) {
			return events.on( event, callback );
		},
		emit: function( event, data ) {
			return events.emit( event, data );
		},

		
	};
	
	self.init();
	
	return self;
}