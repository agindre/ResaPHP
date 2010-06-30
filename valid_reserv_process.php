<?php
require_once('inc/config.inc.php');
require_once('inc/connec_bdd.inc.php');
require_once('inc/functions.inc.php');

// Dans cette page, on va stocker la nouvelle réservation.
// On doit tout d'abord vérifier que les paramètres de session récupérés sont remplis et valides
// On doit ensuite rentrer la réservation en BDD. Un trigger s'occupera de décrémenter le nombre de places disponibles pour le vol
// On envoie un mail de confirmation
// Enfin, on redirige vers la page confirm_reserv.php en cas de succes

// Avant toute autre chose, on ouvre la session, pour pouvoir accéder aux variables de session
session_start();

if (!isset($_SESSION['id'], $_SESSION['nom'], $_SESSION['prenom'], $_SESSION['mail'])) {
	$_SESSION['liste_err'] = array('Vous devez être connecté avant de réserver une place sur un vol');
	header('Location: register.php');
	exit();
}
if (!isset($_SESSION['num_vol'], $_SESSION['jour'], $_SESSION['mois'])) {
	header('Location: liste_depart.php');
	exit();
}

$id_passager = pg_escape_string($_SESSION['id']);
$mail = pg_escape_string($_SESSION['mail']);
$num_vol = pg_escape_string($_SESSION['num_vol']);
$jour = pg_escape_string($_SESSION['jour']);
$mois = pg_escape_string($_SESSION['mois']);

$flag_err = FALSE;
if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
	$flag_err = TRUE;
} elseif (!is_numeric($num_vol) || $num_vol > 9000000000) {
	// On evite de rentrer une valeur non entière, ou un numéro de vol qui ne peut pas exister, sauf si un nouveau continent apparaît...
	$flag_err = TRUE;
} elseif (!is_numeric($jour) || $jour < 1 || $jour > daysInMonth($mois)) {
	// Si le numero du jour est soit négatif ou nul, soit supérieur au nombre de jour dans le mois
	$flag_err = TRUE;
} elseif (!is_numeric($mois) || $mois < 1 || $mois > 12) {
	$flag_err = TRUE;
}

if ($flag_err) {
	$_SESSION['flag_err'] = $flag_err;
	header('Location: liste_depart.php');
	exit();
}

// Les vérifications d'usages sont faites, on peut commencer le traitement
try {
	pg_begin();
	$req_reserv = 'INSERT INTO reservation(code_passager, num_vol, jour, mois, date_reserv, date_limite_reserv, statut) VALUES (\''. $id_passager .'\', '. $num_vol .', '. $jour .', '. $mois .', \''. date('Y-m-d G:i:sP', time()) .'\', \''. date('Y-m-d G:i:sP', (time() + (14 * 24 * 60 * 60))) .'\', \'OK\');';
	// On ajoute 14 jours en secondes à la date de réservation pour obtenir la date limite de réservation
	$res_reserv = pg_query($req_reserv);
	pg_commit();
} catch (Exception $e) {
	pg_rollback();
	header('Location: error.php');
	exit();
}

// Ici, on trouve la partie responsable de l'envoi des mails, avec vérification de la réception des mails, et système de suppression de la réservation en cas de non-réception du mail. Il faut cependant un serveur smtp intégré pour que cette partie soit fonctionnelle

/* $subject = 'Votre réservation a bien été reçue';
$message = 'Votre réservation pour le vol '. $num_vol .' du '. str_pad($jour, 2, '0', STR_PAD_LEFT) .'/'. str_pad($mois, 2, '0', STR_PAD_LEFT) .' a bien été prise en compte. Merci d\'être passé par notre site. Bon voyage !';

$flag_mail = FALSE;
$nb_test_mail = 0;
do {
	myEcho($mail .' '. $subject .' '. $message .'<hr />');
	$flag_mail = mail($mail, $subject, $message);
	$nb_test_mail++;
} while (!$flag_mail && $nb_test_mail <= 100);

// On redirige vers la page d'erreur si jamais la fonction mail ne fonctionne pas, et on supprime la réservation
// On n'est obligé de passer par une suppression de la réservation, car on ne peut pas envoyer le mail avant d'insérer la réservation, notamment en cas de problème lors de l'insertion de l'enregistrement
if (!$flag_mail) {
	try {
		pg_begin();
		$req_suppr_reserv = 'DELETE FROM reservation WHERE code_passager = \''. $id_passager .'\' AND num_vol = '. $num_vol .' AND jour = '. $jour .' AND mois = '. $mois .';';
		$res_suppr_reserv = pg_query($req_suppr_reserv);
		pg_commit();
	} catch(Exception $e) {
		pg_rollback();
		header('Location: error.php');
		exit();
	}
	header('Location: error.php');
	exit();
} */

header('Location: confirm_reserv.php');
exit();
?>
