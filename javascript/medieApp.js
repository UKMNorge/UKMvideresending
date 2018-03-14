/**
 * CLASS UKMVideresending
 * Binder sammen GUI og VideresendingItems
**/
var UKMVideresendMedie = function( $ ) {
	let _innslag = new Map();

	let self = {
		bindGUI: function() {
			/** ENDRING AV CHECKBOX INNSLAG/TITTEL **/
			$(document).on('click', '.videresendingMedie_item .action', function( e ){
				e.preventDefault();
				self.bind( $(this) );
			});
			
			/**
			 * Velg et bilde
			 * kaller UKMMedieItem::velgBildeInnslag( bilde_id )
			**/
			$(document).on('click', '.videresendingMedie_item .imageSelect', function(){
				var data = [
					$(this).attr('data-id')
				];
				self.bind( $(this), data );
			});

			/**
			 * Velg et bilde
			 * kaller UKMMedieItem::velgBildeInnslag( bilde_id )
			**/
			$(document).on('click', '.videresendingMedie_item .showImages', function(){
				var data = [
					$(this).attr('data-kunstverk')
				];
				self.bind( $(this), data );
			});
		},
		
		bind: function( clicked, data ) {
			item = clicked.parents('li.videresendingMedie_item');
			object = self.find(
				item.attr('data-innslag'),	// innslag-id
				item.attr('data-tittel')
			);

			if( object == false ) {
			} else {
				object[ clicked.attr('data-action') ].apply( null, data );
			}
		},
		
		ready: function(){
			$('.videresendingMedie_item').each(function(){
				item = $(this);
				_innslag.set(
					item.attr('data-innslag') +'-'+ item.attr('data-tittel'),
					new UKMMedieItem(
						$,							// inject jQuery
						item.attr('data-innslag'),	// innslag-id
						item.attr('data-tittel'),	// tittel-id | false
					)
				);
			});
		},
		
		find: function( innslag, tittel ) {
			if( _innslag.has( innslag +'-'+ tittel ) ) {
				return _innslag.get( innslag +'-'+ tittel );
			}
			return false;
		},
		
		init: function() {
			self.bindGUI();
		},
	};
	
	self.init();

	return self;
}

jQuery(document).ready(function(){
	UKMVideresendMedie( jQuery ).ready();
});