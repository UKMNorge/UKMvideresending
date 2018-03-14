var UKMVideresendOvernattingSted = function( key, navn ) {
	let events = emitter('OvernattingSted');
	let netter = new Map();
	
	let self = {
		getAntall: function( dato ) {
			if( !netter.has( dato ) ) {
				return 0;
			}

			return netter.get( dato ).size;
		},
		
		getAntallTotalt: function() {
			let total_count = 0;
			netter.forEach(function(value, key){
				total_count += value.size;
			});
			return total_count;
		},
		getNavn: function(){
			return navn;
		},
		
		getId: function() {
			return key;
		},
		
		getAlleNetter: function() {
			return netter;
		},
		
		add: function( dato, leder ) {
			console.log('STED: book '+ leder +' @ '+ self.getNavn() +' '+ dato );
			self.createNatt( dato );
			
			netter.get( dato ).set( leder, true );
			self.emit('changeCount', [ self.getId(), dato, netter.get( dato ).size ]);
		},
		
		remove: function( dato, leder ) {
			console.log('REMOVE:', leder, dato, netter);
			if( netter.has( dato ) ) {
				if( netter.get( dato ).has( leder ) ) {
					netter.get( dato ).delete( leder );
					self.emit('changeCount', [ self.getId(), dato, netter.get( dato ).size ]);
				}
			}
		},
		
		removeLeder: function( leder ) {
			self.getAlleNetter().forEach( function( natt, dato ) {
				if( natt.has( leder.getId() ) ) {
					self.remove( dato, leder.getId() );
				}
			});
		},
		
		createNatt: function( dato ) {
			if( !netter.has( dato ) ) {
				netter.set( dato, new Map() );
			}
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
	
	return self;
};

var UKMVideresendOvernatting = function( _netter, _steder ) {
	let events = emitter('Overnatting');
	let steder = new Map();
	
	let self = {
		addOvernatting: function( _dato, _sted, _leder_id ) {
			console.log('OVERNATTING: add', _dato, _sted, _leder_id );
			// Loop alle steder, og 
			// fjern lederens overnatting for valgt dato
			self.getSteder().forEach( function( sted, sted_key ) {
				sted.remove( _dato, _leder_id );
			});
			// Legg til lederen på riktig stde
			self.getSted( _sted ).add( _dato, _leder_id );
		},
		
		removeLeder: function( leder ) {
			console.log('removeLeder');
			self.getSteder().forEach( function( sted ) {
				console.log( sted.getNavn() );
				sted.removeLeder( leder );
			});
		},

		getSted: function( sted ) {
			return steder.get( sted );
		},
		
		getSteder: function() {
			return steder;
		},
		
		init: function() {
			for (var key in _steder) {
				var obj = _steder[key];
				for (var prop in obj) {
					let sted = new UKMVideresendOvernattingSted( prop, obj[prop] );
					sted.on('changeCount', self.changeCount);
					steder.set( prop, sted );
				}
			}
		},
		
		changeCount: function( sted, dato, count) {
			self.emit('changeCount', [sted, dato, count] );
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