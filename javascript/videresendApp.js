/**
 * CLASS UKMVideresending
 * Binder sammen GUI og VideresendingItems
**/
var UKMVideresending = function( $ ) {
	let _events = emitter();
	let _innslag = new Map();
	
	let self = {
		bindGUI: function() {
			/** ENDRING AV CHECKBOX INNSLAG/TITTEL **/
			$(document).on('change', '.videresend_item input.videresend', function(){
				console.info('CHANGE: videresendPerson('+ $(this).parents('li.videresend_item').attr('data-id') +')');
				var item = $(this).parents('li.videresend_item');
				_events.emit(
					'toggle',
					[
						item.attr('data-type'),		// type
						item.attr('data-innslag'),	// innslag-id
						item.attr('data-id'),		// tittel-id | false
						$(this).is(':checked')		// state
					]
				);
			});

			/** ENDRING AV CHECKBOX PERSON **/
			$(document).on('change', '.videresend_item input.videresendPerson', function(){
				console.info('CHANGE: videresendPerson('+ $(this).attr('data-person') +')');
				var item = $(this).parents('li.videresend_item');
				self.find(
					item.attr('data-type'),		// type
					item.attr('data-innslag'),	// innslag-id
					item.attr('data-id')		// tittel-id | false
				).togglePerson(
					$(this).attr('data-person'),// Person-id
					$(this).is(':checked')		// state
				);
			});

			/** ENDRING AV KONTROLL-DATA **/
			$(document).on('click', '.submitKontroll', function(){
				console.info('SAVE: kontrolldata');
				var item = $(this).parents('li.videresend_item');
				self.find(
					item.attr('data-type'),		// type
					item.attr('data-innslag'),	// innslag-id
					item.attr('data-id')		// tittel-id | false
				).saveKontroll();
			});
		},
		
		on: function( event, callback ) {
			return _events.on( event, callback );
		},
		emit: function( event, data ) {
			return _events.emit( event, data );
		},
		
		init: function() {
			self.bindGUI();
		},
		
		/**
		 * document.ready: loop alle videresendingsobjekter og registrer i app
		**/
		ready: function() {
			$('.videresend_item').each( function() {
				item = self.add(
					$(this).attr('data-type'),
					$(this).attr('data-innslag'),
					$(this).attr('data-id')
				);
				if( item != false && item.erVideresendt() ) {
					item.setVideresendt();
				}
			});
			console.info('ReadyCalled!');
			return self;
		},
		
		/**
		 * Finn et gitt videresendingsobjekt (tittelløst innslag, eller tittel)
		 *
		 * @return UKMVideresendItem
		**/
		find: function( type, innslag, id ) {
			if( _innslag.has( type + '_'+ innslag +'_'+ id ) ) {
				return _innslag.get( type + '_'+ innslag +'_'+ id );
			}
			return false;
		},
		
		/**
		 * Legg til et videresendingsobjekt i collection
		**/
		add: function( type, innslag, id ) {
			var item = new UKMVideresendItem( $, type, innslag, id );
			_innslag.set(
				type + '_'+ innslag +'_'+ id, 
				item
			);
			return item;
		},
		
		/**
		 * Hent Collection videresendingsobjekter
		 *
		 * @return Map( videresendingsobjekter )
		**/
		all: function() {
			return _innslag;
		},
	};
	
	self.init();
	
	return self;
}(jQuery);