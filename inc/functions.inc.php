<?php
/* =========================================
    Liste des fonctions PHP utilis&eacute;es
========================================= */

/* Fonctions d'aide au deboguage */
function myPrint_r($array, $title = '') {
	if (!empty($title)) {
		echo('<strong>'. $title .'</strong>');
	}
	echo('<pre>');
	print_r($array);
	echo('</pre>');
}

function myEcho($string, $title = '') {
	if (!empty($title)) {
		echo('<strong>'. $title .' : </strong>');
	}
	echo($string .'<br />');
}

/* Fonctions pour les connexions &agrave; la BDD */
/**
 *	On utilise les trois fonctions suivantes seulement en cas de modification des donn&eacute;es de la BDD (INSERT, UPDATE, DELETE).
 *	Or, sur l'ensemble du site, ce type de requ&ecirc;te est toujours entour&eacute; d'un try catch, afin de r&eacute;cup&eacute;rer les exceptions qui pourrait se produire.
 *	Du coup, le mieux dans ces trois fonctions est de laisser le programme appelant g&eacute;rer l'exception. C'est pour cela qu'on lui renvoit.
 *	Si tout se passe bien, alors, on ne renvoit rien, et le programme continue.
 */
function pg_begin() {
	try {
		pg_query('BEGIN');
	} catch (Exception $e) {
		return $e;
	}
}
function pg_commit() {
	try {
		pg_query('COMMIT');
	} catch (Exception $e) {
		return $e;
	}
}
function pg_rollback() {
	try {
		pg_query('ROLLBACK');
	} catch (Exception $e) {
		return $e;
	}
}

function get_position_la($num_vol, $jour, $mois, $code_passager, $_connection) {
	if (empty($num_vol) || empty($jour) || empty($mois) || empty($code_passager)) {
		return FALSE;
	} else {
		$req_position_la = 'SELECT COUNT(*) AS position_la FROM reservation WHERE num_vol = '. $num_vol .' AND jour = '. $jour .' AND mois = '. $mois .' AND date_reserv > (SELECT date_reserv FROM reservation WHERE num_vol = '. $num_vol .' AND jour = '. $jour .' AND mois = '. $mois .' AND code_passager = '. $code_passager .');';
		$res_position_la = pg_query($req_position_la, $_connection);
		$ret_position_la = pg_fetch_assoc($res_position_la);
		if (!$ret_position_la) {
			return FALSE;
		}
		$position_la = $ret_position_la['position_la'] + 1;
		if ($position_la == 1) {
			$position_la .= 'er';
		} else {
			$position_la .= '&egrave;me';
		}
		return $position_la;
	}
}

/* Autres fonctions */
function inversBool($booleen) {
	if ($booleen) {
		$booleen = FALSE;
	} else {
		$booleen = TRUE;
	}
	return $booleen;
}

/* === Merci &agrave; dev@omikrosys.com (Page 'date' de la doc PHP) pour ces deux fonctions === */
function leapYear($year) { 
 	if ($year % 400 == 0 || ($year % 4 == 0 && $year % 100 != 0)) {
 		return TRUE;
 	}
 	return FALSE; 
}

function daysInMonth($month = 0, $year = '') { 
 	$days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	$d = array('Jan' => 31, 'Feb' => 28, 'Mar' => 31, 'Apr' => 30, 'May' => 31, 'Jun' => 30, 'Jul' => 31, 'Aug' => 31, 'Sept' => 30, 'Oct' => 31, 'Nov' => 30, 'Dec' => 31); 
 	if (!is_numeric($year) || strlen($year) != 4) $year = date('Y'); 
 	if ($month == 2 || $month == 'Feb') { 
  		if (leapYear($year)) {
  			return 29;
  		}
 	} 
 	if (is_numeric($month)) { 
  		if ($month < 1 || $month > 12) {
  			return 0;
  		} else {
  			return $days_in_month[$month - 1];
  		}
 	} else { 
  		if (in_array($month, array_keys($d))) {
  			return $d[$month];
  		} else {
  			return 0;
  		}
 	} 
}
/* ====== */

function ddlDepart() {
	$liste_departements = array(
		'01' => '(01) Ain',
		'02' => '(02) Aisne',
		'03' => '(03) Allier',
		'04' => '(04) Alpes de Haute Provence',
		'05' => '(05) Hautes Alpes',
		'06' => '(06) Alpes Maritimes',
		'07' => '(07) Ard&egrave;che',
		'08' => '(08) Ardennes',
		'09' => '(09) Ari&egrave;ge',
		'10' => '(10) Aube',
		'11' => '(11) Aude',
		'12' => '(12) Aveyron',
		'13' => '(13) Bouches du Rh&ocirc;ne',
		'14' => '(14) Calvados',
		'15' => '(15) Cantal',
		'16' => '(16) Charente',
		'17' => '(17) Charente Maritime',
		'18' => '(18) Cher',
		'19' => '(19) Corr&egrave;ze',
		'2A' => '(2A) Corse du Sud',
		'2B' => '(2B) Haute-Corse',
		'21' => '(21) C&ocirc;te d\'Or',
		'22' => '(22) C&ocirc;tes d\'Armor',
		'23' => '(23) Creuse',
		'24' => '(24) Dordogne',
		'25' => '(25) Doubs',
		'26' => '(26) Dr&ocirc;me',
		'27' => '(27) Eure',
		'28' => '(28) Eure et Loir',
		'29' => '(29) Finist&egrave;re',
		'30' => '(30) Gard',
		'31' => '(31) Haute Garonne',
		'32' => '(32) Gers',
		'33' => '(33) Gironde',
		'34' => '(34) H&eacute;rault',
		'35' => '(35) Ille et Vilaine',
		'36' => '(36) Indre',
		'37' => '(37) Indre et Loire',
		'38' => '(38) Is&egrave;re',
		'39' => '(39) Jura',
		'40' => '(40) Landes',
		'41' => '(41) Loir et Cher',
		'42' => '(42) Loire',
		'43' => '(43) Haute Loire',
		'44' => '(44) Loire Atlantique',
		'45' => '(45) Loiret',
		'46' => '(46) Lot',
		'47' => '(47) Lot et Garonne',
		'48' => '(48) Loz&egrave;re',
		'49' => '(49) Maine et Loire',
		'50' => '(50) Manche',
		'51' => '(51) Marne',
		'52' => '(52) Haute Marne',
		'53' => '(53) Mayenne',
		'54' => '(54) Meurthe et Moselle',
		'55' => '(55) Meuse',
		'56' => '(56) Morbihan',
		'57' => '(57) Moselle',
		'58' => '(58) Ni&egrave;vre',
		'59' => '(59) Nord',
		'60' => '(60) Oise',
		'61' => '(61) Orne',
		'62' => '(62) Pas de Calais',
		'63' => '(63) Puy de D&ocirc;me',
		'64' => '(64) Pyr&eacute;n&eacute;es Atlantiques',
		'65' => '(65) Hautes Pyr&eacute;n&eacute;es',
		'66' => '(66) Pyr&eacute;n&eacute;es Orientales',
		'67' => '(67) Bas Rhin',
		'68' => '(68) Haut Rhin',
		'69' => '(69) Rh&ocirc;ne',
		'70' => '(70) Haute Sa&ocirc;ne',
		'71' => '(71) Sa&ocirc;ne et Loire',
		'72' => '(72) Sarthe',
		'73' => '(73) Savoie',
		'74' => '(74) Haute Savoie',
		'75' => '(75) Paris',
		'76' => '(76) Seine Maritime',
		'77' => '(77) Seine et Marne',
		'78' => '(78) Yvelines',
		'79' => '(79) Deux S&egrave;vres',
		'80' => '(80) Somme',
		'81' => '(81) Tarn',
		'82' => '(82) Tarn et Garonne',
		'83' => '(83) Var',
		'84' => '(84) Vaucluse',
		'85' => '(85) Vend&eacute;e',
		'86' => '(86) Vienne',
		'87' => '(87) Haute Vienne',
		'88' => '(88) Vosges',
		'89' => '(89) Yonne',
		'90' => '(90) Territoire de Belfort',
		'91' => '(91) Essonne',
		'92' => '(92) Hauts de Seine',
		'93' => '(93) Seine Saint Denis',
		'94' => '(94) Val de Marne',
		'95' => '(95) Val d\'Oise',
		'971' => '(971) Guadeloupe',
		'972' => '(972) Martinique',
		'973' => '(973) Guyane',
		'974' => '(974) R&eacute;union',
		'975' => '(975) Saint Pierre et Miquelon',
		'976' => '(976) Mayotte'
	);
	
	echo("\t\t\t".'<select name="departmt">'."\n");
	foreach ($liste_departements as $code => $nom) {
		echo("\t\t\t\t".'<option value="'. $code .'">'. $nom .'</option>'."\n");			
	}
	echo('</select><br />'."\n");
}
?>
