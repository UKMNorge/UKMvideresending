<?php
function UKMV_steg15_inner($m) {
	// Hva kan videresendes?
	$innslagutentitler = array(4,5,8,9);
	
	_ret('<form action="?page='.$_GET['page'].'&save=steg15" id="hugeform" method="post">'
		.'<div id="hugesubmit"><div id="lagre">OK,</div>g&aring; videre</div>'
		);
			
	# Hent videresendte
	$videresendte = $m->videresendte();
	$teller_videresendte_innslag = 0;
	$antall_unike_deltakere = array();
	$tittellose_titler = array();
	$alle_videresendte = array();
	foreach($videresendte as $i => $inn) {
		$innslag = new innslag($inn['b_id']);
		$innslag->loadGEO();
		
		$titler = new titleInfo( $innslag->g('b_id'), $innslag->g('bt_form'), 'land', $m->videresendTil());
		$titler = $titler->getTitleArray();

		$innslag->videresendte($m->videresendTil());
		$personer = $innslag->personer();
		$antall_innslag[$innslag->g('bt_name')] += 1;
		if(in_array($innslag->g('bt_id'), $innslagutentitler)) {
			$tittellose_titler[] = $innslag->g('bt_name');
			foreach($personer as $trash3 => $p) {
				if(empty($p['p_firstname']) && empty($p['p_firstname']))
					continue;
				$id = 'notitle_'.$inn['b_id'].'_'.$p['p_id'];
				$alle_videresendte[$innslag->g('bt_name')] .= 
					'<div class="kontroll_innslag">'
						.$p['p_firstname'].' '.$p['p_lastname']
					.'</div>'
					;


					$antall_deltakere[$innslag->g('bt_name')][$p['p_id']] = $p['p_firstname']; 
					
					if(isset($antall_unike_deltakere[$p['p_id']]))
						$forklaringunike = str_replace($p['p_firstname'].' '.$p['p_lastname'].', ','', $forklaringunike)
										  . $p['p_firstname'].' '.$p['p_lastname'].', ';
					$antall_unike_deltakere[$p['p_id']][] = $p['p_firstname'];




			}
		} else {
			$teller_videresendte_innslag++;
			foreach($titler as $trash2 => $t) {
				$katogsjan = $innslag->g('kategori_og_sjanger');

				/// DELTAKERE
				$deltakere = '';
				foreach($personer as $j => $p){
					$deltakere .= $p['p_firstname'] . ' ' . $p['p_lastname'].'<br /> ';		
					$antall_deltakere[$innslag->g('bt_name')][$p['p_id']] = $p['p_firstname']; 
					
					if(isset($antall_unike_deltakere[$p['p_id']]))
						$forklaringunike = str_replace($p['p_firstname'].' '.$p['p_lastname'].', ','', $forklaringunike)
										  . $p['p_firstname'].' '.$p['p_lastname'].', ';
					$antall_unike_deltakere[$p['p_id']][] = $p['p_firstname'];
				}
				if(!is_array($personer) || sizeof($personer)==0)
					$deltakereERROR = '<div class="kontroll_deltakereFeil">'
									. UKMN_ico('error_utrop', 20)
									. '<div>Det er ikke videresendt noen deltakere i dette innslaget. Det er feil!'
									. '<br />'
									. '<a href="?page='.$_GET['page'].'&steg=1">G&aring; tilbake til steg 1 for &aring; rette feilen</a></div>'
									. '<br clear="all" />'
									. '</div>';
				else
					$deltakereERROR = '';
	
				$alle_videresendte[$innslag->g('bt_name')] .= 
					'<div class="kontroll_innslag">'
						.$innslag->g('b_name')
						.'<span class="kontroll_tittel"> - '.utf8_encode($t['name']).'</span>'
						.'<div class="kontroll_deltakere">'.$deltakere.'</div>'
						.$deltakereERROR
					.'</div>'
					;
			}
		}
	}
	
	_ret('<div class="UKMV_steg_title" id="subnavtitle">STEG 2: Kontrolliste</div>');
	
	$forklaringunike = substr($forklaringunike, 0, strlen($forklaringunike)-2);
	if(!empty($forklaringunike))
		$forklaringunike .= ' deltar i flere innslag men telles som én';

	_ret('<div id="forklaring_innslag">'
			.'<div style="float:left; width: 80px; vertical-align: bottom; '
			. 'height: 100%; text-align:center; margin-right: 20px;">'	
				. UKMN_ico('chart',32) 
			. '</div>'
			
			.'<strong>Kontroller at du har f&aring;tt med alle!</strong>'
			.'<br />'
			.'Du har videresendt '.$teller_videresendte_innslag.' innslag og '.sizeof($antall_unike_deltakere).' unike deltakere'
			.'<div class="deltakereforklaring">'.$forklaringunike.'</div>'
			#.'<div class="close">[skjul]</div>'
			. '<br clear="all" />'
			.'</div>'
		);
	ksort($alle_videresendte);
	
	foreach($alle_videresendte as $tittel => $html)
		_ret('<h2>'.$tittel
			.(!in_array($tittel, $tittellose_titler)
				? '<span class="kontroll_antall">('.$antall_innslag[$tittel].' innslag, '.sizeof($antall_deltakere[$tittel]).' deltakere)</span>'
				: '<span class="kontroll_antall">('.sizeof($antall_deltakere[$tittel]).' deltaker'.(sizeof($antall_deltakere[$tittel])!=1?'e':'').')</span>'
				)
			.'</h2>'
			. $html);
		
#	_ret('<a href="#top">Til toppen</a>');
		
	_ret('</form>');
}
?>