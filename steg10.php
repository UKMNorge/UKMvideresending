<?php
require_once('videresendingsskjema.php');

$fm = $m->videresendTil(true);

if(sizeof(question_list($fm->g('fylke_id')))==0) {
	_ret('<h3>Fylkesmønstringen har ikke et elektronisk infoskjema..</h3> .. og du trenger derfor ikke fylle ut noe her');
}
_ret('<ul class="videresendingsskjema_svar">');
foreach(question_list($fm->g('fylke_id')) as $q_id) {
	question_answer_gui($q_id);
}
_ret('</ul>');


function question_answer_gui($q_id) {
	$q = question_data($q_id);
	$d = question_values($q_id, get_option('pl_id'));
	
	if($q['q_type'] == 'overskrift') {
		_ret('</ul>
	 		<ul class="videresendingsskjema_svar">
	 			<li>
	 				<h2>'. $q['q_title'] .'</h2>
	 				<span class="forklaring">'. $q['q_help'] .'</span>
	 			</li>');
	} else {
		_ret('<li class="answer">
			<div class="group">');
			if($q['q_type'] != 'overskrift') {
				_ret('<div class="question_title">'. $q['q_title'] .'</div>
					<div class="question_help">'. $q['q_help'] .'</div>');
			}
			_ret('
			</div>
			<div class="group">
				'. question_answer_form($q, $d) .'
			</div>
			<div class="clear-fix clear"></div>
		</li>');
	}
}


function question_values($q_id, $pl_id) {
	$sql = new SQL("SELECT `answer` 
					FROM `smartukm_videresending_fylke_svar`
					WHERE `q_id` = '#qid'
					AND `pl_id` = '#plid'",
					array( 'qid' => $q_id,
						   'plid' => $pl_id));
	$res = $sql->run('field', 'answer');
	
	if(!$res)
		return '';
	
	if( strpos($res, '__||__') !== false)
		return explode('__||__', $res);
	
	return $res;
}

function question_answer_form($q, $d) {
	switch($q['q_type']) {
		case 'korttekst':
			_ret('<input class="inputwide" type="text" name="question_'. $q['q_id'] .'" value="'. utf8_encode($d) .'" />');
			break;
		case 'langtekst':
			_ret('<textarea class="inputwide" name="question_'. $q['q_id'] .'">'. utf8_encode($d) .'</textarea>');
			break;
		case 'janei':
			_ret('<label>
				<input type="radio" name="question_'. $q['q_id'] .'" value="true" '. ($d=='true' ? 'checked="checked"':'') .' /> ja
			</label>
			<label>
				<input type="radio" name="question_'. $q['q_id'] .'" value="false" '. ($d=='false' ? 'checked="checked"':'') .' /> nei
			</label>');
			break;
		case 'kontakt':
			_ret('Navn <input type="text" name="question_'. $q['q_id'] .'[]" value="'. utf8_encode($d[0]) .'" /><br />
			Mobil <input type="text" name="question_'. $q['q_id'] .'[]" value="'. utf8_encode($d[1]) .'" /><br />
			E-post <input type="text" name="question_'. $q['q_id'] .'[]" value="'. utf8_encode($d[2]) .'" />');
			break;
	}
}
?>