var UKMVideresendOvernattingSted = function( key, navn ) {
	var events = emitter('OvernattingSted');
	var netter = new Map();
	
	var self = {
		getAntall: function( dato ) {
			if( !netter.has( dato ) ) {
				return 0;
			}

			return netter.get( dato ).size;
		},
		
		getAntallTotalt: function() {
			var total_count = 0;
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
			//console.log('STED: book '+ leder +' @ '+ self.getNavn() +' '+ dato );
			self.createNatt( dato );
			
			netter.get( dato ).set( leder, true );
			self.emit('changeCount', [ self.getId(), dato, netter.get( dato ).size ]);
		},
		
		remove: function( dato, leder ) {
			//console.log('REMOVE:', leder, dato, netter);
			if( netter.has( dato ) ) {
				if( netter.get( dato ).has( leder ) ) {
					netter.get( dato ).delete( leder );
					self.emit('changeCount', [ self.getId(), dato, netter.get( dato ).size ]);
				}
			}
		},

		// Fjern alle netter i et sted.
		removeAll: function(_leder_id) {
			netter.forEach(function(value, dato){
				var x = netter.get(dato).delete(_leder_id);
				self.emit('changeCount', [ self.getId(), dato, netter.get( dato ).size ]);

			});
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
	var events = emitter('Overnatting');
	var steder = new Map();
	
	var self = {
		addOvernatting: function( _dato, _sted, leder ) {
			var _leder_id = leder.getId();
			
			// Loop alle steder, og 
			// fjern lederens overnatting for valgt dato
			self.getSteder().forEach( function( sted, sted_key ) {
				sted.remove( _dato, _leder_id );
				if((leder.getType() == 'sykerom' || leder.getType() == 'turist') && sted.getId() != 'hotell') {
					sted.removeAll(_leder_id);
				}

			});
			// Legg til lederen på riktig stde
			self.getSted( _sted ).add( _dato, _leder_id );
		},
		
		removeLeder: function( leder ) {
			self.getSteder().forEach( function( sted ) {
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
					var sted = new UKMVideresendOvernattingSted( prop, obj[prop] );
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