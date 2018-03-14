jQuery(document).ready(function(){
	UKMVideresending.ready();
		
	UKMVideresending.on('toggle', 
			function( type, innslag, id, state ) {
				var item = UKMVideresending.find( type, innslag, id );
				console.warn('TOGGLE ITEM', item );
				
				if( item ) {
					if( state ) {
						item.videresend();
					} else {
						item.avmeld();
					}
				} else {
					console.warn('Kunne ikke finne item '+ type +'_'+ innslag +'_' + id );
				}
			}
		);
});

String.prototype.capitalize = function() {
	return this.charAt(0).toUpperCase() + this.slice(1);
}