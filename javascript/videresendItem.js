/**
 * CLASS UKMVideresendItem
 * Håndterer alt for et gitt videresendingsitem (tittel, innslag uten titler)
**/
var UKMVideresendItem = function( $, type, innslag, id ) {
	var events = emitter();

	var _status = [];

	var self = {

		/****************************************/
		/** ID OG GUI-ID TIL AKTUELT OBJEKT		*/
		/****************************************/

		/**
		 * Hent ID til gitt innslag som concat streng
		**/
		getId: function() {
			return type +'-'+ innslag +'-'+ id;
		},

		/**
		 * Hent ID med #-prefix for GUI-referanse
		**/
		getGUIId: function() {
			return '#' + self.getId();
		},

		getFylkeParam: function(){
			var url = new URL( window.location.href );
			return url.searchParams.get("fylke");
		},

		/****************************************/
		/** VIDERESENDING OG AVMELDING: INNSLAG	*/
		/****************************************/

		/**
		 * Endre state på checkbox'en
		**/
		setCheckboxState: function( checked ) {
			if( checked ) {
				$( self.getGUIId() + ' input.videresend' ).prop('checked', true);
			} else {
				$( self.getGUIId() + ' input.videresend' ).removeProp('checked');
			}
		},
		
		/**
		 * Hent state for checkbox (og derav, er innslaget videresendt?)
		**/
		getCheckboxState: function() {
			return $( self.getGUIId() + ' input.videresend' ).is(':checked');
		},

		/**
		 * Alias for getCheckboxState
		**/
		erVideresendt: function() {
			return self.getCheckboxState();
		},

		/**
		 * Send ajax-kall for å videresende tittel/innslag
		**/
		videresend: function() {
			self.setStatus('alert-warning', 'Vennligst vent, videresender...');
			
			var options = self.getFylkeParam() != null ? {'fylke': self.getFylkeParam()} : null;
			self.ajax('videresend', options );
			
			//console.warn('ajax:videresend:'+ self.getId());
		},

		/**
		 * Send ajax-kall for å avmelde tittel/innslag
		**/
		avmeld: function() {
			self.setStatus('alert-warning', 'Vennligst vent, melder av...');
			
			var options = self.getFylkeParam() != null ? {'fylke': self.getFylkeParam()} : null;
			self.ajax( 'avmeld', options );
			//console.warn('ajax:avmeld:'+ self.getId());
		},

		/**
		 * ResponsHandler ajax: innslaget er videresendt
		**/
		setVideresendt: function() {
			$( self.getGUIId() ).addClass('selected');
			$( self.getGUIId() + ' .header').addClass('alert-success', 300);
			$( self.getGUIId() + ' .row.data .kontroll').html('Laster inn..');
			$( self.getGUIId() + ' .row.data' ).slideDown(500);
			self.loadKontroll();
		},

		/**
		 * ResponsHandler ajax: innslaget er avmeldt
		**/
		setAvmeldt: function(){
			$( self.getGUIId() ).removeClass('selected');
			$( self.getGUIId() + ' .header').removeClass('alert-success');

			$( self.getGUIId() + ' .row.data' ).slideUp();
		},

		
		/****************************************/
		/** VIDERESENDING OG AVMELDING: PERSON	*/
		/****************************************/

		/**
		 * Hent GUI-Id for gitt person
		**/
		getPersonGUIId: function( person ) {
			return '.person-'+ person +'-i-'+ innslag;
		},

		/**
		 * Send ajax-kall for å videresende person
		**/
		videresendPerson: function( person ) {
			self.setStatus('alert-warning', 'Vennligst vent, videresender person...');
			self.ajax('videresendPerson', {'person': person} );
			//console.warn('ajax:videresend('+ self.getId() +'):person('+ person +')');
		},

		/**
		 * Send ajax-kall for å avmelde person
		**/
		avmeldPerson: function( person ) {
			self.setStatus('alert-warning', 'Vennligst vent, melder av person...');
			self.ajax('avmeldPerson', {'person': person} );
			//console.warn('ajax:videresend('+ self.getId() +'):person('+ person +')');
		},
		
		/**
		 * Endre status for personen, inngang for GUI-handler
		**/
		togglePerson: function( person, checked ) {
			if( checked ) {
				self.videresendPerson( person );
			} else {
				self.avmeldPerson( person );
			}
		},

		/**
		 * ResponsHandler ajax: personen er videresendt
		**/		
		setVideresendtPerson: function( person ) {
			$( self.getPersonGUIId( person ) + ' input.videresendPerson' ).prop('checked', true);
			$( self.getPersonGUIId( person ) ).each( function(){
				$(this).addClass('alert-success');
			});

		},

		/**
		 * ResponsHandler ajax: personen er avmeldt
		**/		
		setAvmeldtPerson: function( person ) {
			$( self.getPersonGUIId( person ) + ' input.videresendPerson' ).removeProp('checked');
			$( self.getPersonGUIId( person ) ).each( function(){
				$(this).removeClass('alert-success');
			});

		},
		

		/****************************************/
		/** KONTROLL-SKJEMAET					*/
		/****************************************/

		getKontrollGUIId: function() {
			return self.getGUIId() + ' .row.data .kontroll .submitKontroll';
		},
		/**
		 * Hent kontroll-skjema
		**/
		loadKontroll: function() {
			self.ajax('kontroll');
		},
		/**
		 * Rendre kontroll-skjema (ajax response handler)
		**/
		renderKontroll: function( data ){
			$( self.getGUIId() + ' .row.data .kontroll').html( twigJSkontroll.render( data ) );
		},
		
		/**
		 * Lagre skjemaet for gitt innslag/tittel
		**/
		saveKontroll: function() {
			$( self.getKontrollGUIId() ).html('Lagrer...').addClass('btn-primary');
			self.setStatus('alert-warning', 'Vennligst vent, lagrer detaljer...');
			var form = $( self.getGUIId() + ' .row.data .kontroll form');
			var data = form.serializeArray();
			self.ajax('kontrollSave', data );
		},
		
		saveKontrollFeedback: function(){
			$( self.getKontrollGUIId() ).html('Lagret!').addClass('btn-success').removeClass('btn-primary');
			setTimeout(
				function(){
					$( self.getKontrollGUIId() ).html('Lagre endringer').removeClass('btn-success');
				}, 
				2100
			);
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
				type: type,
				innslag: innslag,
				id: id
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
					self.emit('ajax', [action, response]);
				}
			);	
		},

		/**
		 * Sett en status på et innslag (brukes ved ajax-kall)
		**/
		setStatus: function( status, melding) {
			self.resetStatus();
			$( self.getGUIId() ).find('span.navn').hide();
			$( self.getGUIId() ).find('span.status').show();

			$( self.getGUIId() ).addClass(status).find('span.status').html(melding);
			_status.push( status );
		},
		
		/**
		 * Fjern alle statuser på et innslag (fullført/feivar ajax-kall)
		**/
		resetStatus: function() {
			_status.forEach( function( css ) {
				$( self.getGUIId() ).removeClass( css );
			});
			
			$( self.getGUIId() ).find('span.status').hide();
			$( self.getGUIId() ).find('span.navn').show();
			
		},
	};

	/**
	 * FAKTISK AJAX-HANDLER
	**/	
	self.on('ajax', function( action, response ) {
		//console.log('AJAX RESPONSE FOR '+ action, response);
		/* DECODE JSON DATA OR SET RESPONSE FAIL */
		if( response !== null && response !== undefined ) {
			try {
				response = JSON.parse( response );
			} catch( error ) {
				response = null;
			}
		}
		
		/* HANDLING GJENNOMFØRT. HÅNDTER RESPONS */
		if( response !== null && response.success ) {
			self.resetStatus();
			switch( action ) {
				case 'videresend':
					self.setVideresendt();
				break;
				case 'avmeld':
					self.setAvmeldt();
				break;
				case 'kontroll':
					self.renderKontroll( response.data );
				break;
				case 'videresendPerson':
					self.setVideresendtPerson( response.POST.person );
				break;
				case 'avmeldPerson':
					self.setAvmeldtPerson( response.POST.person );
				break;
				case 'kontrollSave':
					self.saveKontrollFeedback();
				break;

			}
		}
		/* FEIL I MOTTATT RESPONS */
		else {
			self.setStatus('alert-danger', 'Beklager, en feil oppsto ved lagring!' );
			if( response != null && response != undefined && response.message != null && response.message != undefined ) {
				alert( response.message );
			}
			setTimeout(
				function(){
					self.resetStatus();
				}, 
				2100
			);
			/* HVIS EKSTRA-HANDLINGER KREVES SOM FØLGE AV FEIvar HANDLING */
			switch( action ) {
				case 'videresend':
					self.setCheckboxState( false );
				break;
				case 'videresendPerson':
					if( null !== response.POST && undefined !== response.POST && null !== response.POST.person && undefined !== response.POST.person ) {
						self.setAvmeldtPerson( response.POST.person );
					} else {
						alert('Selv om det er krysset av for at personen er videresendt, stemmer ikke dette! En feil oppsto ved lagring, og systemet klarte ikke å fjerne krysset. Prøv igjen, eller kontakt UKM Norge.');
					}
				break;
				case 'save':
					$( self.getKontrollGUIId() ).html('Lagre endringer').removeClass('btn-primary');
				break;
			}
		}
	});
	
	return self;
}