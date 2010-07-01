<?php
require_once('inc/config.inc.php');
require_once('inc/connec_bdd.inc.php');
require_once('inc/functions.inc.php');

// Dans cette page, on va supprimer la réservation
// Pour cela, on vérifie comme toujours que les paramètres sont bien valides
// Ce sont deux triggers (trig_del_reserv et trig_up_depart_ok_la) qui vont mettre à jour le nombre de places disponibles pour le vol et faire remonter le premier passager de la liste d'attente parmi la liste principale

// Avant toute autre chose, on ouvre la session, pour pouvoir accéder aux variables de session
session_start();

if (!isset($_SESSION['id'], $_SESSION['nom'], $_SESSION['prenom'], $_SESSION['mail'])) {
	$_SESSION['liste_err'] = array('Vous devez &ecirc;tre connect&eacute; avant de r&eacute;server une place sur un vol');
	header('Location: register.php');
	exit();
}
if (!isset($_SESSION['num_vol'], $_SESSION['jour'], $_SESSION['mois'])) {
	header('Location: home.php');
	exit();
}

$code_passager = pg_escape_string($_SESSION['id']);
$mail = pg_escape_string($_SESSION['mail']);
$num_vol = pg_escape_string($_SESSION['num_vol']);
$jour = pg_escape_string($_SESSION['jour']);
$mois = pg_escape_string($_SESSION['mois']);

$flag_err = FALSE;
if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
	$flag_err = TRUE;
} elseif (!isInt($num_vol) || $num_vol > 9000000000) {
	// On evite de rentrer une valeur non entière, ou un numéro de vol qui ne peut pas exister, sauf si un nouveau continent apparaît...
	$flag_err = TRUE;
} elseif (!isInt($jour) || $jour < 1 || $jour > daysInMonth($mois)) {
	// Si le numero du jour est soit négatif ou nul, soit supérieur au nombre de jour dans le mois, soit pas un entier du tout
	$flag_err = TRUE;
} elseif (!isInt($mois) || $mois < 1 || $mois > 12) {
	$flag_err = TRUE;
}

if ($flag_err) {
	header('Location: liste_depart.php');
	exit();
}

// Les vérifications d'usages sont faites, on peut commencer le traitement
try {
	pg_begin();
	$req_del_reserv = 'DELETE FROM reservation WHERE code_passager = \''. $code_passager .'\' AND num_vol = '. $num_vol .' AND jour = '. $jour .' AND mois = '. $mois .';';
	$res_del_reserv = pg_query($req_del_reserv);
	pg_commit();
} catch (Exception $e) {
	pg_rollback();
	header('Location: error.php');
	exit();
}

// Il est noté qu'il faut supprimer le passager lorsqu'il n'y a plus de réservation à son nom.
// Cependant, dans notre cas, donc dans un site d'ecommerce, on ne peut pas se permettre de supprimer un client
// Du coup, le traitement suivant reste en commentaire
/*
try {
	$req_nb_reserv_client = 'SELECT COUNT(*) AS nb_reserv FROM reservation WHERE code_passager = \''. $code_passager .'\';';
	$res_nb_reserv_client = pg_query($req_nb_reserv_client);
	$ret_nb_reserv_client = pg_fetch_assoc($res_nb_reserv_client);
	$nb_reserv_client = $ret_nb_reserv_client['nb_reserv'];
} catch (Exception $e) {
	header('Location: error.php');
	exit();
}
if ($nb_reserv_cient == 0) {
	try {
		pg_begin();
		$req_del_client = 'DELETE FROM client WHERE code_passager = \''. $code_passager .'\';';
		$res_del_client = pg_query($req_del_client);
		pg_commit();
	} catch (Exception $e) {
		pg_rollback();
		header('Location: error.php');
		exit();
	}
	unset($_SESSION);
}
*/ 
?>
