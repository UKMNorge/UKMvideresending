/**
 * CLASS UKMVideresending
 * Binder sammen GUI og VideresendingItems
**/
var UKMVideresendLeder = function( $, overnattingssteder, antall_deltakere, pris_hotelldogn ) {
	let _leder = new Map();
	
	let self = {
		bindGUI: function() {
			$(document).on('keyup', '.leder_navn', function(){
				self.findParent( $(this) ).setNavn( $(this).val() );
			});
			
			$(document).on('keyup', '.leder_mobil', function(){
				self.findParent( $(this) ).setMobil( $(this).val() );
			});
			
			$(document).on('keyup', '.leder_epost', function(){
				self.findParent( $(this) ).setEpost( $(this).val() );
			});

			
			$(document).on('change', '.leder_type', function(){
				self.findParent( $(this) ).setType( $(this).val() );
			});

			$(document).on('change', 'input.natt', function(){
				self.findParent( $(this) )
				.setNatt( 
					$(this).attr('data-dag') +'_'+ $(this).attr('data-mnd'),
					$(this).val()
				);
			});
			
			$(document).on('click', 'button.leder_save', function(){
				self.findParent( $(this) ).save();
			});
			$(document).on('click', 'button.leder_delete', function(e){
				e.preventDefault();
				
				var sikker = confirm('Er du sikker på at du vil slette denne lederen?');
				if( sikker ) {
					self.remove( $( this ) );
				}
			});
			
			$(document).on('click', 'button#leder_create', function(){
				self.createLeder();
			});
			
			$(document).on('click', 'button#lagre_kommentarer_overnatting', function(){
				self.lagreKommentar();
			});
			
			$(document).on('click', 'button#hovedleder_save', function(){
				self.saveHovedledere();
			});
		},
		
		bind: function( leder ) {
			// Følg med antall netter totalt
			leder.on('valgtNatt', function( dato, sted, leder_id ) {
				overnattingssteder.addOvernatting( dato, sted, leder_id );
			});
			leder.on('changeNavn', function(){ self.updateHovedleder( leder ) } );
			leder.on('changeType', function(){ self.updateHovedleder( leder ) } );
		},
		
		ready: function(){
			$('.leder').each(function(){
				self.registerLeder( $(this) );
			});
			
			self.showAntallDeltakere();
			self.populateHovedLeder();
			return self;
		},
		
		findParent: function( jQueryItem ) {
			return self.find( jQueryItem.parents('li.leder').attr('data-id') );
		},
		
		find: function( id ) {
			if( _leder.has( id ) ) {
				return _leder.get( id );
			}
			return false;
		},
		
		remove: function( jQueryItem ){
			let leder = self.findParent( jQueryItem );
			self.ajax( 
				'lederDelete', 
				{
					leder: leder.getId()
				}
			);
		},
		
		handleLederDelete: function( response ) {
			let leder = self.find( response.POST.leder );
			// Fjern leder fra alle overnattingssteder
			// Oppdaterer automatisk summering
			overnattingssteder.removeLeder( leder );
			
			// Skjul fra listen over ldere
			$( leder.getSelector() ).slideUp( function(){ 
				$(this).remove();
			});
			
			// Fjern fra nattleder-listen
			self.removeHovedleder( leder );
			// Select-listen velger det første objektet (hovedleder)
			// automatisk. Lagre derfor listen for å unngø døde koblinger
			// i rapporter osv. Hovedleder, you just got upgraded!
			self.saveHovedledere();
			
			// Slett objektet
			_leder.delete( leder.getId() );
		},
		
		init: function() {
			overnattingssteder.on('changeCount', self.showNattStatus );
			overnattingssteder.on('changeCount', self.showHotelldognStatus );
			self.bindGUI();
		},
		
		showNattStatus: function( sted, natt, count ) {
			$('#status-'+ sted +'-'+ natt ).html( count );
			
			if( sted == 'deltakere' ){
				if( count >= self.getLederePerNatt() ) {
					$('#status-'+ sted +'-'+ natt ).removeClass('alert-danger').addClass('alert-success');
				} else {
					$('#status-'+ sted +'-'+ natt ).removeClass('alert-success').addClass('alert-danger');
				}
			}
		},
		
		getLederePerNatt: function() {
			if( antall_deltakere == 0 ) {
				return 1;
			}
			return Math.ceil( antall_deltakere / 10 );
		},
		
		showAntallDeltakere: function() {
			$('.deltakere_i_landsbyen').html( antall_deltakere +' deltaker'+ (antall_deltakere != 1 ? 'e' : '') );
			$('.ledere_per_natt').html( self.getLederePerNatt() +' leder'+ (self.getLederePerNatt() != 1 ? 'e' : '') );
		},
		
		showHotelldognStatus: function() {
			let antall_netter = overnattingssteder.getSted('hotell').getAntallTotalt();
			$('.hotelldogn .pris').html( pris_hotelldogn );
			$('.hotelldogn .antall').html( antall_netter );
			$('.hotelldogn .total').html( pris_hotelldogn * antall_netter );
		},
		
		/**
		 * REGISTRER DOM-LEDER I JAVASCRIPT
		**/
		registerLeder: function( jQueryItem ) {
			leder = new UKMLederItem(
				$,
				jQueryItem.attr('data-id'),
				jQueryItem.attr('data-type'),
				jQueryItem.find('input.leder_navn').val(),
				jQueryItem.find('input.leder_mobil').val(),
				jQueryItem.find('input.leder_epost').val(),
			);
			self.bind( leder );
			leder.loadNetter();
			
			_leder.set( leder.getId(), leder );	
			
			return leder;
		},
		
		/**
		 * OPPRETT LEDER
		**/
		createLeder: function(){
			$( 'button#leder_create' ).html('Oppretter, vennligst vent..').addClass('btn-warning').removeClass('btn-success');
			self.ajax('lederCreate');
		},
		
		handleLederCreate: function( response ) {
			$('#alle_ledere').append( twigJSledereleder.render( response ) );
			$( 'button#leder_create' ).html('Legg til leder.').addClass('btn-success').removeClass('btn-warning');

			let leder = self.registerLeder( $('#leder_'+ response.leder.ID ) );
			self.addHovedLeder( leder );
		},
		
		/**
		 * LAGRE OVERNATTING-KOMMENTAR
		**/
		lagreKommentar: function() {
			$( '#lagre_kommentarer_overnatting' ).html('Lagrer..').addClass('btn-primary').removeClass('btn-success');
			self.ajax(
				'kommentarOvernatting', 
				{
					kommentar: $('#kommentarer_overnatting').val()
				}
			);
		},
		
		handleKommentarOvernatting: function( response ){
			$( '#lagre_kommentarer_overnatting' ).html('Lagret!').addClass('btn-success').removeClass('btn-primary');
			setTimeout(
				function(){
					$( '#lagre_kommentarer_overnatting' ).html('Lagre');
				}, 
				2100
			);
		},

		/**
		 * HOVEDLEDERE
		**/
		updateHovedleder: function( leder ) {
			$('select.hovedleder > option.hovedleder-'+ leder.getId() ).each(function(){
				$(this).text( leder.getNavn() +' ('+ leder.getTypeNice() +')' )
			});
		},

		populateHovedLeder: function(){
			_leder.forEach( function( leder ) {
				self.addHovedLeder( leder );
			});
		},
		
		addHovedLeder: function( leder ) {
			$('select.hovedleder').each(function(){
				if( $(this).find('option.hovedleder-'+ leder.getId()).length == 0 ) {
					$(this).append(
						$('<option />')
							.addClass('hovedleder-'+ leder.getId() )
							.val(leder.getId() )
							.text( leder.getNavn() +' ('+ leder.getTypeNice() +')')
					);
				}
			});
		},
		
		removeHovedleder: function( leder ) {
			console.warn('Fjern alle', 'option.hovedleder-'+ leder.getId());
			$( 'option.hovedleder-'+ leder.getId() ).each(function(){
				$(this).remove();
			});
		},
		
		/**
		 * OPPRETT LEDER
		**/
		saveHovedledere: function(){
			$( 'button#hovedleder_save' ).html('Lagrer..').addClass('btn-primary').removeClass('btn-success');
			self.ajax(
				'lederSaveHoved',
				$('form#hovedledere').serializeArray()
			);
		},
		
		handleLederSaveHoved: function( response ) {
			$( 'button#hovedleder_save' ).html('Lagret.').addClass('btn-success').removeClass('btn-primary');
			setTimeout(
				function(){
					$( 'button#hovedleder_save' ).html('Lagre ansvarlig leder');
				}, 
				2100
			);
		},

		
		/**
		 * Faktisk gjør AJAX-kall
		**/
		ajax: function( action, param_data ) {
			var data = {
				action: 'UKMVideresending_ajax',
				subaction: action,
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

	};
	
	self.init();

	return self;
}
jQuery(document).ready(function(){
	UKMVideresendLeder( 
		jQuery, 
		UKMVideresendOvernatting(
			netter,
			steder
		),
		deltakere,
		pris_hotelldogn
	).ready();
});