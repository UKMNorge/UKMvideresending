$(document).on('click', '#tilrettelegg_person_submit', function(e){
    e.preventDefault();
    
    var action = 'tilrettelegging';
    
    var id = $('#tilrettelegg_person').val();

    if( null == id || id == 'false' ) {
        alert('Du må velge en deltaker først');
        return false;
    }

    var data = {
        action: 'UKMVideresending_ajax',
        subaction: action,
        id: id,
        navn: $('#tilrettelegg_person_option_'+ id).attr('data-navn'),
        intoleranse: $('#tilrettelegg_person_intoleranse').val()
    };

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
                handleTilrettelegg( response );
            } else {
                alert('Beklager, kunne ikke hente informasjon fra server');
            }
        }
    );
});

function handleTilrettelegg( response ) {
    console.log('Handle tilrettelegg');
    console.log( response );
    $('#tilrettelegg_personer tbody').append( 
        twigJStilrettelegg.render( response.POST )
    );

    $('#tilrettelegg_person_option_'+ response.POST.id ).remove();
    $('#tilrettelegg_person').find('option:first').prop('selected', true);
    $('#tilrettelegg_person_intoleranse').val('');
}