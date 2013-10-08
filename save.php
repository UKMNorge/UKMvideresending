<?php

function returnto($steg) {
	if(isset($_GET['returnto']))
		return $_GET['returnto'];
	return $steg;
}

/**
 * UKMV_save * 
 * Lagrer og videresender brukeren til riktig side
 *
 * @return html redirect if save
*/
function UKMV_save() {
	if(!isset($_GET['save']))	return;

	$m = new monstring(get_option('pl_id'));
/*
	if(get_option('site_type')=='fylke')
		$videresendTil = $m->hent_landsmonstring();
	else
		$videresendTil = $m->hent_fylkesmonstring();
*/

	$videresendTil = $m->videresendTil(true);

	switch($_GET['save']) {
		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## 
		##				SAVE :: STEG 1 :: FRIST UTE				 ##
		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ##
		case 'fristute':
			header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL']
					.'?page='.$_GET['page'].'&steg='.returnto(15));
				exit();
		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## 
		##						SAVE :: STEG 1					 ##
		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ##
		case 'steg1':
			ksort($_POST);
#			$videresendTil = $m->hent_landsmonstring()->g('pl_id');
			$videresendTil = $m->videresendTil();


			$pl_from = get_option('pl_id');
			if (!($pl_from > 0) || !($videresendTil > 0))
				die('En feil har oppst&aring;tt, kontakt UKM Norge');
			
			$behandlede_personer = array();
			// Hvis det er f.eks. et arrangørinnslag med flere arrangører, pass på så man ikke sletter relasjonen gjennom loopen
			$slettedeRelasjoner = array();
			// Hvis tittelen (eller arrangørinnslag med mange personer i) allerede er behandlet (videresendt eller avmeldt) skal man ikke avmelde igjen i alle fall
			$behandlede_titler = array();	
			foreach($_POST as $key => $val) {
				if(strpos($key, 'videresendt_')!==false) {
					$key = str_replace('videresendt_','',$key);
					$info = explode('_', $key);

					## FLYTTET 31.01.2013 FRA UNDER DELTAKER-BLOKKA
					if ($info[0] == 'notitle') {
						$tittel = 0;
						$b_key = 1;
					}
					else {
						$tittel = $info[1];
						$b_key = 0;	
					}
					## E.O FLYTTET 31.01.2013
					
					if ($info[0] == 'deltaker' || $info[0] == 'notitle') {
						$person = new person($info[2], $info[1]);
						
						##### LAGT TIL 31.01.2013 ######
						$behandlet_person = 'b'.$info[$b_key].'p'.$person->g('p_id');
						if(in_array( $behandlet_person, $behandlede_personer ))
							continue;
						##### E.O LAGT TIL 31.01.2013 ######
						
												
						if ($val == 1) {
							##### LAGT TIL 31.01.2013 ######
							$behandlede_personer[] = $behandlet_person;
							##### E.O LAGT TIL 31.01.2013 ######
							#echo 'Videresend '.$info[0].' b:'.$info[1].'p:'. $info[2].' ('.$person->g('p_firstname').' '.$person->g('p_lastname').')<br />';
							$person->videresend($pl_from, $videresendTil);
						}
						else {
							#echo 'Avmeld '.$info[0].' b:'.$info[1].'p:'. $info[2].' ('.$person->g('p_firstname').' '.$person->g('p_lastname').')<br />';
							$person->avmeld($pl_from, $videresendTil);
						}

						if ($info[0] == 'deltaker')
							continue;
					}
						
					
					$innslag = new innslag($info[$b_key]);
					if($val == 1) {
						#echo 'Videresend innslag b:'.$info[$b_key].'t:'. $tittel.' ('.$innslag->g('b_name').')<br />';
						$behandlede_titler[] = 'b'.$info[$b_key].'t'.$tittel;	// Hvis denne tittelen i dette bandet er videresendt, skal den heller ikke avmeldes
						$slettedeRelasjoner[] = $info[$b_key];					// Hvis dette innslaget er videresendt, skal ikke relasjonen slettes
						$innslag->videresend($pl_from, $videresendTil, $tittel);
					}
					elseif(!in_array('b'.$info[$b_key].'t'.$tittel, $behandlede_titler)) {
						$behandlede_titler[] = 'b'.$info[$b_key].'t'.$tittel;	// Nå er denne tittelen i dette bandet avmeldt, og da skal den ikke avmeldes flere ganger
						$slett_relasjon = !in_array($info[$b_key], $slettedeRelasjoner);	// Er relasjonen slettet tidligere? (avgjør om den nå skal slettes)
						$slettedeRelasjoner[] = $info[$b_key];					// Tilse at relasjonen ikke slettes flere ganger i samme lagringssekvens
						#echo ($slett_relasjon ? 'SLETT RELASJON OG ' : ' IKKE SLETT RELASJON ' ) . ' Avmeld innslag b:'.$info[$b_key].'t:'. $tittel.' ('.$innslag->g('b_name').')<br />';
						$innslag->avmeld($pl_from, $videresendTil, $tittel, $slett_relasjon);
					}
				}
			}
			
			header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL'].'?page='.$_GET['page'].'&steg='.returnto(15));
			exit();
		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## 
		##					  "SAVE" :: STEG 1,5				 ##
		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ##
		case 'steg15':
			header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL'].'?page='.$_GET['page'].'&steg='.returnto(2));
			exit();
		
		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## 
		##						SAVE :: STEG 2					 ##
		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ##
		case 'steg2':
			foreach($_POST as $key => $val) {
				$key = str_replace('videresendt_','',$key);
				$info = explode('_', $key);
				
				if ($info[0] == 'dummy')
					continue;
				
				switch($info[0]) {
					case 'exhibition':
						$qry = new SQLins('smartukm_titles_exhibition', array('b_id'=>$info[1],'t_id'=>$info[2]));
						$qry->add('t_e_'.$info[3], $val);
						$qry->run();
						#echo $qry->debug() . '<br />';
					break;
					
					case 'demand':
						$qry = new SQLins('smartukm_technical', array('b_id'=>$info[1]));
						$qry->add('td_demand', $val);
						$qry->run();
						#echo $qry->debug() . '<br />';
					break;
					
					case 'video':
						$qry = new SQLins('smartukm_titles_video', array('b_id'=>$info[1], 't_id'=>$info[2]));
						$qry->add('t_v_'.$info[3], $val);
						$qry->run();
						#echo $qry->debug() . '<br />';
					break;					
					case 'mobilnummer':
						$qry = new SQLins('smartukm_participant', array('p_id'=>$info[1]));
						$qry->add('p_phone', $val);
						$qry->run();
					break;
					
					case 'alder':
						$ar = (int)date('Y') - (int)$val;
						$timestamp = mktime(0,30,0,1,1,$ar);
						$qry = new SQLins('smartukm_participant', array('p_id'=>$info[1]));
						$qry->add('p_dob', $timestamp);
						$qry->run();
					break;
										
					case 'scene':
						$qry = new SQLins('smartukm_titles_scene', array('b_id'=>$info[1], 't_id'=>$info[2]));
						$qry->add('t_'.$info[3], $val);
						$qry->run();
						#echo $qry->debug() . '<br />';
					break;
				}
			}
			
			header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL'].'?page='.$_GET['page'].'&steg='.returnto(get_option('site_type')=='fylke'?3:9));
			exit();
		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## 
		##						SAVE :: STEG 3					 ##
		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ##
		case 'steg3':
			$slettede_relasjoner = $slettede_relasjoner_kunstner = array();
			foreach($_POST as $key => $val) {
				$info = str_replace(array('valgt_kunstner_bilde_','valgt_bilde'),'',$key);
				$info = explode('_',$info);
				$b_id = (int)$info[0];
				$t_id = (int)$info[1];
				if(strpos($key,'valgt_bilde')!==false) {
					if(!in_array($b_id, $slettede_relasjoner)) {
						$slett_relasjon = new SQLdel('smartukm_videresending_media',
											array('pl_id'=>$videresendTil->g('pl_id'),
												  'b_id'=>$b_id,
												  'm_type'=>'bilde'));
						$slett_relasjon->run();
						$slettede_relasjoner[] = $b_id;
					}					
					$lagre_ny_relasjon = new SQLins('smartukm_videresending_media');
					$lagre_ny_relasjon->add('pl_id', $videresendTil->g('pl_id'));
					$lagre_ny_relasjon->add('b_id', $b_id);
					$lagre_ny_relasjon->add('t_id', $t_id);
					$lagre_ny_relasjon->add('rel_id', $val);
					$lagre_ny_relasjon->add('m_type', 'bilde');
					#echo $key . '=>'. $lagre_ny_relasjon->debug().'<br />';
					$lagre_ny_relasjon->run();
				}
				elseif(strpos($key,'valgt_kunstner_bilde')!==false) {
					if(empty($val))
						continue;
					if(!in_array($b_id, $slettede_relasjoner_kunstner)) {
						$slett_relasjon = new SQLdel('smartukm_videresending_media',
											array('pl_id'=>$videresendTil->g('pl_id'),
												  'b_id'=>$b_id,
												  'm_type'=>'bilde_kunstner'));
						$slett_relasjon->run();
						$slettede_relasjoner_kunstner[] = $b_id;
					}
					$lagre_ny_relasjon = new SQLins('smartukm_videresending_media');
					$lagre_ny_relasjon->add('pl_id', $videresendTil->g('pl_id'));
					$lagre_ny_relasjon->add('b_id', $b_id);
					$lagre_ny_relasjon->add('t_id', $t_id);
					$lagre_ny_relasjon->add('rel_id', (int)$val);
					$lagre_ny_relasjon->add('m_type', 'bilde_kunstner');
					#echo $key . '=>'. $lagre_ny_relasjon->debug().'<br />';
					$lagre_ny_relasjon->run();
				}

			}
			
			if(isset($_GET['lo_bilde'])) {
				header("Location: http://".$_SERVER['HTTP_HOST'].str_replace('admin.php','upload.php',$_SERVER['REDIRECT_URL']).'?page=UKM_images&c=upload&band='.$_GET['lo_bilde']);
				exit();
			} elseif(isset($_GET['lo_video'])) {
				header("Location: http://".$_SERVER['HTTP_HOST'].str_replace('admin.php','admin.php',$_SERVER['REDIRECT_URL']).'?page=UKM_videos&band='.$_GET['lo_video'].'&return=videresending');
				exit();
			}
			
			header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL'].'?page='.$_GET['page'].'&steg='.returnto(35));
			exit();
		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## 
		##					  "SAVE" :: STEG 3,5				 ##
		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ##
		case 'steg35':
			header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL'].'?page='.$_GET['page'].'&steg='.returnto(4));
			exit();
		
		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## 
		##						SAVE :: STEG 4					 ##
		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ##
		case 'steg4':
			foreach ($_POST['leder'] as $key => $val) {
				if (empty($val['navn']))
					continue;
				
				// Update
				if ($val['id'] > 0)
					$lagre_leder = new SQLins('smartukm_videresending_ledere', array('leder_id'=>$val['id']));
				// Insert
				else
					$lagre_leder = new SQLins('smartukm_videresending_ledere');
					
				foreach ($val as $key2 => $val2) {
					if ($key2 == 'turist')
						continue;
					
					// Overnatting
					if (substr($val2, 0, 5) == 'over') {
						$dag = explode('_', $val2);
						$dag = $dag[1];
						
						$lagre_leder->add('leder_over_'.$dag, $val2);	
						continue;
					}
					// Personalia
					if ($key2 == 'mobilnummer') {
						$lagre_leder->add('leder_'.$key2, str_replace(' ', '', $val2));	
						continue;
					}
					$lagre_leder->add('leder_'.$key2, $val2);
				}

				// Ledertype
				if ($key == 1)
					$lagre_leder->add('leder_type', 'hoved');
				else if ($key == 2)
					$lagre_leder->add('leder_type', 'utstilling');
				else {
					$lagre_leder->add('leder_type', 'reise');
					// Turist
					if (isset($val['turist']) && $val['turist'] === '1')
						$lagre_leder->add('leder_turist', 'j');
					else 
						$lagre_leder->add('leder_turist', 'n');
				}
					
				$lagre_leder->add('pl_season', get_option('season'));
				$lagre_leder->add('pl_id_to', $videresendTil->g('pl_id'));
				$lagre_leder->add('pl_id_from', get_option('pl_id'));
					
 				$lagre_leder->run();
			}
			
			// Stats
			$sql = new SQLdel('smartukm_videresending_ledere_stats',
								array('season'=>get_option('season'), 
									  'pl_to'=>$videresendTil->g('pl_id'),
									  'pl_from'=>get_option('pl_id')));
			$sql->run();
			
			// Middag
			$sql = new SQLdel('smartukm_videresending_ledere_middag',
								array('season'=>get_option('season'), 
									  'pl_to'=>$videresendTil->g('pl_id'),
									  'pl_from'=>get_option('pl_id')));
			$sql->run();
				
			$sql = new SQLins('smartukm_videresending_ledere_stats');
			$sql_middag = new SQLins('smartukm_videresending_ledere_middag');
			foreach ($_POST as $key => $val) {
				if (is_array($val))
					continue;
				else if (substr($key, 0, 11) == 'ledermiddag')
					$sql_middag->add($key, $val);
				else 
					$sql->add($key, $val);
			}
			
			$sql->add('season', get_option('season'));
			$sql->add('pl_to', $videresendTil->g('pl_id'));
			$sql->add('pl_from', get_option('pl_id'));
			
			$sql_middag->add('season', get_option('season'));
			$sql_middag->add('pl_to', $videresendTil->g('pl_id'));
			$sql_middag->add('pl_from', get_option('pl_id'));
			
			$sql->run();
			$sql_middag->run();			
		
			header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL'].'?page='.$_GET['page'].'&steg='.returnto(5));
			exit();


		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## 
		##						SAVE :: STEG 5					 ##
		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ##
		case 'steg5':
			$PLvideresendTil = $m->videresendTil();
		
			$sql_infoskjema_test = new SQL('SELECT count(pl_id) as num FROM smartukm_videresending_infoskjema WHERE pl_id_from=#pl_id_from AND pl_id=#pl_id', array('pl_id_from'=>get_option('pl_id'), 'pl_id'=>$PLvideresendTil));
			$res = $sql_infoskjema_test->run('field', 'num');
					
			
			if ($res != 0) {
				$sql_infoskjema = new SQLins('smartukm_videresending_infoskjema', array('pl_id'=>$PLvideresendTil, 'pl_id_from'=>get_option('pl_id')));
			#	$sql_infoskjema_kunst = new SQLins('smartukm_videresending_infoskjema_kunst', array('pl_id'=>$PLvideresendTil, 'pl_id_from'=>get_option('pl_id')));
			}
			else {
				$sql_infoskjema = new SQLins('smartukm_videresending_infoskjema');
			#	$sql_infoskjema_kunst = new SQLins('smartukm_videresending_infoskjema_kunst');
			}

			
			foreach ($_POST as $key => $val) {
				if (substr($key, 0, 6) != 'kunst_')
					$sql_infoskjema->add($key, $val);
			#	else
			#		$sql_infoskjema_kunst->add($key, $val);
			}	
			
			$sql_infoskjema->add('pl_id_from', get_option('pl_id'));
			#$sql_infoskjema_kunst->add('pl_id_from', get_option('pl_id'));
			
			$sql_infoskjema->add('pl_id', $PLvideresendTil);
			#$sql_infoskjema_kunst->add('pl_id', $PLvideresendTil);
			
			$sql_infoskjema->run();
			#$sql_infoskjema_kunst->run();
			
			header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL'].'?page='.$_GET['page'].'&steg='.returnto(8));
			exit();

		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## 
		##					  "SAVE" :: STEG 6				 ##
		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ##
		case 'steg6':
			header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL'].'?page='.$_GET['page'].'&steg='.returnto(6));
			exit();


		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## 
		##						SAVE :: STEG 8					 ##
		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ##
		case 'steg8':
			$PLvideresendTil = $m->videresendTil();
		
			$sql_infoskjema_test = new SQL('SELECT count(pl_id) as num FROM smartukm_videresending_infoskjema_kunst WHERE pl_id_from=#pl_id_from AND pl_id=#pl_id', array('pl_id_from'=>get_option('pl_id'), 'pl_id'=>$PLvideresendTil));
			$res = $sql_infoskjema_test->run('field', 'num');
						
			if ($res != 0) {
				$sql_infoskjema_kunst = new SQLins('smartukm_videresending_infoskjema_kunst', array('pl_id'=>$PLvideresendTil, 'pl_id_from'=>get_option('pl_id'), 'season'=>get_option('season')));
			}
			else {
				$sql_infoskjema_kunst = new SQLins('smartukm_videresending_infoskjema_kunst');
			}
			
			$sql = new SQLdel('smartukm_videresending_infoskjema_kunst_kolli',
								array('season'=>get_option('season'), 
										'pl_id_from'=>get_option('pl_id'),
										'pl_id'=>$PLvideresendTil));
			$sql->run();
			
			$i = 1;
			foreach ($_POST['kolli'] as $key => $val) {
				if (is_array($val)) {
					if ($val['kolli_vekt'] == '')
						continue;
					
					$sql_infoskjema_kunst_kolli = new SQLins('smartukm_videresending_infoskjema_kunst_kolli');
					foreach ($val as $key2 => $val2) {
						$sql_infoskjema_kunst_kolli->add($key2, $val2);
					}
				}
				
				$sql_infoskjema_kunst_kolli->add('pl_id_from', get_option('pl_id'));
				$sql_infoskjema_kunst_kolli->add('season', get_option('season'));
				$sql_infoskjema_kunst_kolli->add('pl_id', $PLvideresendTil);
				$sql_infoskjema_kunst_kolli->add('kolli_id', $i);
				
				$sql_infoskjema_kunst_kolli->run();
				$i++;
			}

			
			foreach ($_POST as $key => $val) {
				if (substr($key, 0, 6) == 'kunst_')
					$sql_infoskjema_kunst->add($key, $val);
			}
						
			$sql_infoskjema_kunst->add('pl_id_from', get_option('pl_id'));
			$sql_infoskjema_kunst->add('season', get_option('season'));
			$sql_infoskjema_kunst->add('pl_id', $PLvideresendTil);
						
			$sql_infoskjema_kunst->run();
			header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL'].'?page='.$_GET['page'].'&steg='.returnto(9));
			exit();

		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## 
		##					  "SAVE" :: STEG 6				 ##
		## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ##

		case 'steg9':
			$m = new monstring(get_option('pl_id'));
			$_POST['log_current_value_pl_missing'] = (int)$m->g('pl_missing');
			$_POST['log_current_value_pl_public'] = (int)$m->g('pl_public');
			
			$m->update('pl_missing');
			$m->update('pl_public');
			
			header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL'].'?page='.$_GET['page'].'&steg='.returnto(get_option('site_type')=='fylke'?6:1));
			exit();

		case 'steg10':
#			echo '<pre>'; var_dump($_POST); echo '</pre>';
			foreach($_POST as $key => $val) {
				if(strpos($key, 'question_') !== 0)
					continue;
				
				if(is_array($_POST[$key])) {
					$value = implode('__||__', $_POST[$key]);
				} else {
					$value = $val;
				}
				
				$qid = str_replace('question_', '', $key);
				
				$update = new SQL("SELECT `s_id`
								   FROM `smartukm_videresending_fylke_svar`
								   WHERE `q_id` = '#qid'
								   AND `pl_id` = '#plid'",
								   array( 'qid' => $qid, 
								   		  'plid' => get_option('pl_id')));
				#echo $update->debug();
				$update = (int) $update->run('field','s_id');
				if(is_int($update) && $update > 0)
					$sql = new SQLins('smartukm_videresending_fylke_svar', array( 's_id' => $update ));
				else
					$sql = new SQLins('smartukm_videresending_fylke_svar');
				
				$sql->add('q_id', $qid);
				$sql->add('pl_id', get_option('pl_id'));
				$sql->add('answer', $value);
				
				#echo $sql->debug() .'<br />';
				$sql->run();
			}
			header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL'].'?page='.$_GET['page'].'&steg='.returnto(10));
			exit();

	}
	die();
}

$slettede_notitles = array();
function videresendNoTitleDeltaker($videresendTil, $innslag, $person, $value) {
	if(empty($innslag)||empty($person)||empty($videresendTil))
		return;
	global $slettede_notitles;
	# Innslaget er ikke slettet tidligere denne runden, start derfor med dette
	if(!in_array($innslag, $slettede_notitles)) {
		$slett_personer = new SQLdel('smartukm_fylkestep_p',
							array('pl_id'=>$videresendTil,
								  'b_id'=>$innslag));
	
		$slett_innslag = new SQLdel('smartukm_fylkestep',
							array('pl_id'=>$videresendTil,
								  'pl_from'=>get_option('pl_id'),
								  'b_id'=>$innslag,
								  't_id'=>$tittel));
	
		$slett_relasjon = new SQLdel('smartukm_rel_pl_b',
							array('pl_id'=>$videresendTil,
								  'b_id'=>$innslag,
								  'season'=>get_option('season')));		# Slett fra rel_pl_b
		$slett_personer->run();
		$slett_innslag->run();
		$slett_relasjon->run();
	
		$slettede_notitles[] = $innslag;
	} 
	# ELSE: Dette er slettet tidligere i denne runden, og skal derfor være i den tilstand det er i
	
	# Skal videresendes
	if($value == 1) {
		## Finnes det en relasjon til mønstringen? Hvis ikke sett inn denne nå
		$finnes_rel_pl_b = new SQL("SELECT * 
									FROM `smartukm_rel_pl_b`
									WHERE `pl_id` = '#videresendtil' 
									AND `b_id` = '#innslag'
									AND `season` = '#season'",
									array('videresendtil'=>$videresendTil, 'innslag'=>$innslag, 'season'=>get_option('season')));
		$finnes_rel_pl_b = mysql_num_rows($finnes_rel_pl_b->run());
		if($finnes_rel_pl_b==0) {
			$qry = new SQLins('smartukm_rel_pl_b');
			$qry->add('pl_id', $videresendTil);
			$qry->add('b_id', $innslag);
			$qry->add('season', get_option('season'));
			$qry->run();
		}
			
		## Finnes det en relasjon til fylkestep? Hvis ikke, sett inn nå
		$finnes_fylkestep = new SQL("SELECT *
									 FROM `smartukm_fylkestep`
									 WHERE `pl_id` = '#videresendtil'
									 AND `b_id` = '#innslag'
									 AND `t_id` = '0'",
									array('videresendtil'=>$videresendTil, 'innslag'=>$innslag));
		$finnes_fylkestep = mysql_num_rows($finnes_fylkestep->run());
		if($finnes_fylkestep == 0) {
			$qry = new SQLins('smartukm_fylkestep');
			$qry->add('pl_id', $videresendTil);
			$qry->add('pl_from', get_option('pl_id'));
			$qry->add('b_id', $innslag);
			$qry->add('t_id', 0);
			$qry->run();
		}									 
		# Sett inn i fylkestep_p
		$qry = new SQLins('smartukm_fylkestep_p');
		$qry->add('pl_id', $videresendTil);
		$qry->add('b_id', $innslag);
		$qry->add('p_id', $person);
		$qry->run();	
	}
}

/**
 * videresendDeltaker * 
 * Videresender ett innslag, og fjerner videresending av alle deltakere i innslaget!
 *
 * @param int $videresendTil PL_ID til mottakermønstring
 * @param int $innslag B_ID for gjeldende innslag
 * @param int $person P_ID for gjeldende person
 * @param int(0-1) $value for videresendt eller ikke
 * @return void
*/
$personer_slettet = array();
function videresendDeltaker($videresendTil, $innslag, $person, $value) {
	global $personer_slettet;
	## Hvis personen skal videresendes, slett alle eventuelle tidligere videresendinger
	## av samme person i samme innslag (kan jo være flere titler..!)
	if(!in_array($person,$personer_slettet) && !empty($innslag) && !empty($person) && !empty($videresendTil)) {
		$slett_person = new SQLdel('smartukm_fylkestep_p',
						array('pl_id'=>$videresendTil,
							  'b_id'=>$innslag,
							  'p_id'=>$person));
		$slett_person->run();
		$personer_slettet[] = $person;
	}
	if($value==1) {		
		$videresend_person = new SQLins('smartukm_fylkestep_p');
		$videresend_person->add('pl_id', $videresendTil);
		$videresend_person->add('b_id', $innslag);
		$videresend_person->add('p_id', $person);
		$videresend_person->run();
	}
}

/**
 * videresendInnslag * 
 * Videresender ett innslag, og fjerner videresending av alle deltakere i innslaget!
 * !!! Krever at videresendDeltaker kjøres etterpå !!!
 *
 * @param int $videresendTil PL_ID til mottakermønstring
 * @param int $innslag B_ID for gjeldende innslag
 * @param int $tittel T_ID for gjeldende tittel
 * @param int(0-1) $value for videresendt eller ikke
 * @return void
*/
$innslag_slettet = array();
function videresendInnslag($videresendTil, $innslag, $tittel, $value) {
	global $innslag_slettet;
	if(!in_array($innslag,$innslag_slettet) && !empty($innslag) && !empty($videresendTil)) {
		$slett_personer = new SQLdel('smartukm_fylkestep_p',
							array('pl_id'=>$videresendTil,
								  'b_id'=>$innslag));
		$slett_personer->run();
			
		$slett_relasjon = new SQLdel('smartukm_rel_pl_b',
							array('pl_id'=>$videresendTil,
								  'b_id'=>$innslag,
								  'season'=>get_option('season')));
		$slett_relasjon->run();
		$innslag_slettet[] = $innslag;
	}

	$slett_innslag = new SQLdel('smartukm_fylkestep',
						array('pl_id'=>$videresendTil,
							  'b_id'=>$innslag,
							  't_id'=>$tittel));
	$slett_innslag->run();

	if($value == 1) {
		$videresend_innslag = new SQLins('smartukm_fylkestep');
		$videresend_innslag->add('pl_id', $videresendTil);
		$videresend_innslag->add('pl_from', get_option('pl_id'));
		$videresend_innslag->add('b_id', $innslag);
		$videresend_innslag->add('t_id', $tittel);
		$videresend_innslag->run();
		
		$test_relasjon = new SQL("SELECT * FROM `smartukm_rel_pl_b`
								  WHERE `pl_id` = '#plid'
								  AND `b_id` = '#bid'
								  AND `season` = '#season'",
								  array('plid'=>$videresendTil, 'bid'=>$innslag, 'season'=>get_option('season')));
		$test_relasjon = $test_relasjon->run();
		
		if(mysql_num_rows($test_relasjon)==0) {		
			$videresend_innslag_relasjon = new SQLins('smartukm_rel_pl_b');
			$videresend_innslag_relasjon->add('pl_id', $videresendTil);
			$videresend_innslag_relasjon->add('b_id', $innslag);
			$videresend_innslag_relasjon->add('season', get_option('season'));
			$videresend_innslag_relasjon->run();
		}
	}
}

?>