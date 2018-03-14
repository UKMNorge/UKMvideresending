<?php
$monstring = UKMVideresending::getFra();
$skjema = $monstring->getSkjema( isset( $_GET['fylke'] ) ? $_GET['fylke'] : null );
	
if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	$questions = array();
	
	foreach($_POST as $key => $value) {
		if (strpos($key, 'question_') === 0) {
			// value starts with book_
			$str = explode('_', $key); // $str[0] = "question", $str[1] == id, $str[2] (if any) == "navn"/"mobil"/"epost"
			$q_id = $str[1];
	
			if(count($str) > 2) {
				$questions[$q_id][$str[2]] = $value;
			} else {
				$questions[$q_id] = $value;
			}
		}
	}
	
	$results = array();
	
	$numQ = 0;
	foreach ($questions as $q_id => $answer ) {
		$res = $skjema->answerQuestion($q_id, $answer, $debug );
		if( !is_numeric( $res ) ) {
			$errors[] = $res->error();
		}
		$numQ++;
	}
	
	if ( count( $errors ) == 0 ) {
		UKMVideresending::addViewData(
			'message',
			[
				'success' => true,
				'body' => 'Skjema er lagret!'
			]
		);
	}
	else {
		UKMVideresending::addViewData(
			'message',
			[
				'success' => false,
				'body' => 'Ett eller flere av svarene dine ble ikke lagret. Prøv igjen.'
			]
		);
	}
}

UKMVideresending::addViewData('skjema', $skjema);