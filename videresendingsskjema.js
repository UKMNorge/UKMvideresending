jQuery(document).ready(function(){
	jQuery('input#question_new').click(function(){
		data = 'action=UKMVS_question_add&'+jQuery(this).parents('form').serialize();
		jQuery('input#question_new').attr('disabled','disabled');
		jQuery.post(ajaxurl, data, function(response){
			jQuery('ul.sporsmal li:last').after(response);			
			jQuery('ul.sporsmal li#none').slideUp();
			jQuery('input#question_new').attr('disabled','');
			jQuery('input#question_new').removeAttr('disabled');
		});
	});
	
	
	
	jQuery('ul.sporsmal').sortable({
		update: function(event, ui) {
			jQuery.post(ajaxurl, {action: 'UKMVS_question_order', data: jQuery(this).sortable('toArray')}, 
				function(response){});
		}
	});
	
	jQuery('ul.sporsmal li div.icon').live('click',function(){
		test = confirm('Sikker på at du vil slette dette spørsmålet?');
		if(test) {
			id = jQuery(this).parents('li').attr('id');
			data = 'action=UKMVS_question_remove&removethisquestion='+id;
			jQuery('li#'+id).slideUp();
			jQuery.post(ajaxurl, data, function(response){});
		}
	});
});