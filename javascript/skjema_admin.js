jQuery(document).ready(function() {
	jQuery('#sporsmalsListe').sortable({
		update: function(event, ui) {
			console.log(event);
			console.log(jQuery(this).sortable('toArray', {attribute: 'id'}));
			jQuery("#q_order").val(jQuery(this).sortable('toArray', {attribute: 'id'}));
			/*jQuery.post(ajaxurl, {
				action: 'UKMVS_question_order', 
				data: jQuery(this).sortable('toArray')
			}, function(response){});*/
		}
	});
});