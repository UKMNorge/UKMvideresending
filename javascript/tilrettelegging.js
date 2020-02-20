jQuery(document).on('click', 'li.person .header', function() {
    var person = jQuery(this).parents('li.person');
    var state = person.attr('data-state');
    if (state == 'hidden') {
        person.addClass('selected').attr('data-state', 'visible');
        person.find('.data.row').slideDown();
    } else {
        person.find('.data.row').slideUp(
            function() {
                jQuery(this).parents('li.person').removeClass('selected');
            }
        );
        person.attr('data-state', 'hidden');
    }
});

jQuery(document).on('click', '.intoleranse_update', function(e) {
    e.preventDefault();
    jQuery(this).html('Lagrer...').addClass('btn-primary').removeClass('btn-success');
    var person = jQuery(this).parents('li.person');

    var allergener = [];
    person.find('input[type="checkbox"]:checked').each(function() {
        allergener.push(jQuery(this).val());
    });


    var data = {
        action: 'UKMVideresending_ajax',
        subaction: 'tilrettelegging',
        id: person.attr('data-id'),
        tekst: person.find('.intoleranse_tekst').val(),
        liste: allergener
    };

    jQuery.post(
        ajaxurl,
        data,
        function(response) {
            if (response !== null && response !== undefined) {
                try {
                    response = JSON.parse(response);
                } catch (error) {
                    response = null;
                }
            }

            /* HANDLING GJENNOMFØRT. HÅNDTER RESPONS */
            if (response !== null && response.success) {
                handleTilretteleggUpdate(response);
            } else {
                alert('Beklager, kunne ikke hente informasjon fra server');
            }
        }
    );
});


function handleTilretteleggUpdate(response) {
    var person = jQuery('li.person#' + response.data.id);

    if (response.data.intoleranse_human.length == 0) {
        person.slideUp(
            function() {
                jQuery(this).remove();
            }
        );
    } else {
        person.find('.header .status').html(response.data.intoleranse_human);
    }

    var knapp = person.find('.intoleranse_update');
    knapp.html('Lagret!').addClass('btn-success').removeClass('btn-primary');
    setTimeout(
        () => {
            knapp.html('Lagre');
        },
        2200
    );
}


jQuery(document).on('click', '#intoleranse_add', function(e) {
    e.preventDefault();
    if (!jQuery('#intoleranse_ny').val()) {
        alert('Velg en person fra listen før du trykker "legg til"');
        return false;
    }
    var data = {
        person: {
            ID: jQuery('#intoleranse_ny').val(),
            navn: jQuery('#intoleranse_ny option:selected').html(),
            mobil: jQuery('#intoleranse_ny option:selected').data('mobil'),
            intoleranse_liste: [],
            intoleranse_tekst: ''
        },
        allergener_kulturelle: JSON.parse(allergener_kulturelle),
        allergener_standard: JSON.parse(allergener_standard),
    };
    jQuery('#intoleranser').prepend(
        twigJS_intoleransedeltaker.render(data)
    );
    jQuery('#intoleranse_ny option[value="' + data.person.ID + '"]').remove();
    jQuery('li.person#' + data.person.ID + ' .header').click();

});