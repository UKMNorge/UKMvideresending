<?php

function ajax_question_remove($id) {
	$id = (int) str_replace('question_','',$id);
	if(!empty($id)) {
		$sql = new SQLdel('smartukm_videresending_fylke_sporsmal', array('q_id' => $id ));
		$sql->run();
	}
}

function ajax_question_order() {
	$order = 0;
	if(is_array($_POST['data'])) {
		foreach($_POST['data'] as $id) {
			$order++;
			$sql = new SQLins('smartukm_videresending_fylke_sporsmal', array('q_id' => str_replace('question_','',$id)));
			$sql->add('order', $order);
			$sql->run();
		}
	}
}

function ajax_question_add($print = false){
	if(is_array($_POST['question_id'])) {
		foreach($_POST['question_id'] as $id ) {
			if(!empty($_POST['question_title_'.$id])){
				if($id == 'new') {
					$ins = new SQLins('smartukm_videresending_fylke_sporsmal');
					$ins->add('order', time());
					$ins->add('f_id', get_option('fylke'));
				} else {
					$ins = new SQLins('smartukm_videresending_fylke_sporsmal', array('q_id' => $id));
				}
				
				if(empty($_POST['question_type_'.$id]))
					$_POST['question_type_'.$id] = 'korttekst';
				
				$ins->add('q_title', $_POST['question_title_'.$id]);
				$ins->add('q_type', $_POST['question_type_'.$id]);
				$ins->add('q_help', $_POST['question_help_'.$id]);
				
				$res = $ins->run();
				
				if($print)
					question_view(question_data($ins->insid()));
			}
		}
	}
}


function question_admin() {
	ajax_question_add();
	?>
	<h1>Videresendingsskjema</h1>
	<div class="message">Spørreskjemaet du lager her må fylles ut av  dine lokalkontakter ved videresending til fylkesmønstringen</div>
	Du kan legge til så mange spørsmål du vil, og endre rekkefølgen ved å dra og slippe etterpå.<br /><br />
	
	<strong>Lurer du på hvordan skjemaet fungerer?</strong> <a href="http://ukm.no/wp-content/plugins/UKMVideresending/videresendingsskjemaforklaring.png" target="_blank">Se et eksempelskjema her</a>
	
	<h2 style="margin-top: 30px;">Legg til nytt spørsmål eller overskrift</h2>
	<form id="new_question">
	<?= question_form('new') ?>
	<input type="button" value="Legg til" id="question_new" />
	</form>
	<form action="#" method="post">
	<h2 style="margin-top: 20px;"></h2>
	<ul class="sporsmal">
		<?php foreach( question_list() as $question ) {
			$q = question_data($question);
			question_view($q);
		}
		if(sizeof(question_list()) == 0) { ?>
			<li id="none">
				<div class="error">Det er ikke lagt til noen spørsmål enda</div>
			</li>
		<?php
		}
		?>
	</ul>
	<div class="clear-fix clear"></div>
	<input type="submit" value="Lagre" name="submitallquestions" id="submitallquestions" />
	</form>
<?php
}

function question_list($fid=false) {
	if(!$fid)
		$fid = get_option('fylke');
	
	$qs = array();
	$qry = new SQL("SELECT `q_id` FROM `smartukm_videresending_fylke_sporsmal`
					WHERE `f_id` = '#fid'
					ORDER BY `order` ASC, `q_title` ASC",
					array('fid' => $fid));
	$res = $qry->run();
	if($res)
		while($r = mysql_fetch_assoc($res))
			$qs[] = $r['q_id'];
	
	return $qs;
}

function question_data($id) {
	$values = array();
	$qry = new SQL("SELECT * FROM `smartukm_videresending_fylke_sporsmal`
					WHERE `q_id` = '#id'",
					array('id' => $id));
	$res = $qry->run('array');
	if(is_array($res)) {
		foreach($res as $key => $val) {
			if(is_string($val))
				$val = utf8_encode($val);
			
			$values[$key] = $val;
		}
	}
	return $values;
}

function question_view($q) { ?>
	<li class="question <?= $q['q_type']=='overskrift' ? 'overskrift' : '' ?>" id="question_<?= $q['q_id'] ?>">
		<div class="handle"><img src="<?= WP_PLUGIN_URL?>/UKMprogram/draogslipp.png" width="25" /></div>
		<?php question_form($q['q_id']) ?>
		<div class="icon"><?= UKMN_icoButton('trash',20, 'Slett'); ?></div>
		<div class="clear-fix clear"></div>
	</li>
	<?php
}

function question_form($id) {
	$data = question_data($id); ?>
<div class="q_form">
<input type="hidden" name="question_id[]" value="<?= $id ?>" />
<label>Tittel/spørsmål: <input type="text" name="question_title_<?= $id ?>" value="<?= $data['q_title'] ?>" placeholder=""></label>
<label>Type <?= ($id == 'new' ? 'svarfelt' : '') ?>: 
	<select name="question_type_<?= $id ?>">
		<option <?= ($id == 'new' ? 'selected="selected"' : '') ?>disabled="disabled">Velg type svarfelt</option>
		<option disabled="disabled"> ----- </option>
		<option <?= ($data['q_type'] == 'overskrift' ? 'selected="selected"' : '') ?>value="overskrift">Ingen, dette er en overskrift</option>
		<option disabled="disabled"> ----- </option>
		<option <?= ($data['q_type'] == 'korttekst' ? 'selected="selected"' : '') ?> value="korttekst">Kort tekstfelt</option>
		<option <?= ($data['q_type'] == 'langtekst' ? 'selected="selected"' : '') ?> value="langtekst">Langt tekstfelt</option>
		<option <?= ($data['q_type'] == 'janei' ? 'selected="selected"' : '') ?>value="janei">Ja / nei</option>
		<option <?= ($data['q_type'] == 'kontakt' ? 'selected="selected"' : '') ?>value="kontakt">Personinfo (navn, mobil, e-post)</option>
	</select>
</label>
<label>Hjelpetekst: <input type="text" name="question_help_<?= $id ?>" value="<?= $data['q_help'] ?>" /></label>
<div class="clear-fix clear"></div>
</div>
<?php
}