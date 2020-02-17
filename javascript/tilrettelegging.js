$(document).on('click', 'li.person .header', function() {
    var person = $(this).parents('li.person');
    var state = person.attr('data-state');
    if (state == 'hidden') {
        person.addClass('selected').attr('data-state', 'visible');
        person.find('.data.row').slideDown();
    } else {
        person.find('.data.row').slideUp(
            function() {
                $(this).parents('li.person').removeClass('selected');
            }
        );
        person.attr('data-state', 'hidden');
    }
});

$(document).on('click', '.intoleranse_update', function(e) {
    e.preventDefault();
    $(this).html('Lagrer...').addClass('btn-primary').removeClass('btn-success');
    var person = $(this).parents('li.person');

    var allergener = [];
    person.find('input[type="checkbox"]:checked').each(function() {
        allergener.push($(this).val());
    });


    var data = {
        action: 'UKMVideresending_ajax',
        subaction: 'tilrettelegging',
        id: person.attr('data-id'),
        tekst: person.find('.intoleranse_tekst').val(),
        liste: allergener
    };

    $.post(
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
    var person = $('li.person#' + response.data.id);

    if (response.data.intoleranse_human.length == 0) {
        person.slideUp(
            function() {
                $(this).remove();
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


$(document).on('click', '#intoleranse_add', function(e) {
    e.preventDefault();
    var data = {
        person: {
            ID: $('#intoleranse_ny').val(),
            navn: $('#intoleranse_ny option:selected').html(),
            intoleranse_liste: [],
            intoleranse_tekst: ''
        },
        allergener_kulturelle: JSON.parse(allergener_kulturelle),
        allergener_standard: JSON.parse(allergener_standard),
    };
    $('#intoleranser').prepend(
        twigJS_intoleransedeltaker.render(data)
    );
    $('#intoleranse_ny option[value="' + data.person.ID + '"]').remove();
    $('li.person#' + data.person.ID + ' .header').click();

});