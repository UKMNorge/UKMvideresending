var emitter = function( _navn ) {
	let _events = [];
	
	let navn = (_navn !== undefined && _navn !== null) ? _navn.toUpperCase() : 'UKJENT';
	
	let self = {
		/* EVENT HANDLERS */
		emit: function( event, data ) {
			
			console.info( navn + '::emit('+event+')', data);
			if( _events[event] != null ) {
				_events[event].forEach( function( _event ) {
					_event.apply(null, data );
				});
			}
			return self;
		},
		
		on: function( event, callback ) {
			if( _events[event] == null ) {
				_events[ event ] = [callback];
				return;
			}
			_events[ event ].push( callback );
			return self;
		}
	};
	
	return self;
}