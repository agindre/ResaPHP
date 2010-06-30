<?php
require_once('inc/config.inc.php');
require_once('inc/connec_bdd.inc.php');
require_once('inc/functions.inc.php');

// Avant toute autre chose, on ouvre la session, pour pouvoir accéder aux variables de session
session_start();

if (!isset($_SESSION['id'], $_SESSION['nom'], $_SESSION['prenom'], $_SESSION['mail'])) {
	$_SESSION['liste_err'] = array('Vous devez être connecté avant de réserver une place sur un vol');
	header('Location: register.php');
	exit();
}

$flag_erreur_get = FALSE;
// On recupere les variables normalement passees en GET
if (empty($_GET['num']) || empty($_GET['jour']) || empty($_GET['mois'])) {
	$flag_erreur_get = TRUE;
} else {
	$code_passager = pg_escape_string($_SESSION['id']);
	$num_vol = pg_escape_string($_GET['num']);
	$jour = pg_escape_string($_GET['jour']);
	$mois = pg_escape_string($_GET['mois']);
	
	// On fait les vérifications d'usage sur les variables jour et mois
	if ($jour < 0 || $jour > daysInMonth($mois - 1)) {
		$flag_erreur_get = TRUE;
	} elseif ($mois < 0 || $mois > 12) {
		$flag_erreur_get = TRUE;
	}
	
	// On recupere maintenant les informations du vol
	if (!$flag_erreur_get) {
		try {
			$req_donnees_reserv = 'SELECT depart.num_vol, jour, mois, destination, vol_h_depart, frequence, statut
				FROM depart NATURAL JOIN vol NATURAL JOIN reservation
				WHERE code_passager = \''. $code_passager .'\' reservation.num_vol = '. $num_vol .' AND jour = '. $jour .' AND mois = '. $mois .';';
			$res_donnees_reserv = pg_query($req_donnees_reserv);
			$ret_donnees_reserv = pg_fetch_assoc($res_donnees_reserv);
		} catch (Exception $e) {
			header('Location: error.php');
			exit();
		}
		if (!$ret_donnees_reserv) {
			// La fonction pg_fetch_assoc renvoit FALSE si la requete n'obtient aucune donnee
			$flag_erreur_get = TRUE;
		}
	}
}

// S'il n'y a pas d'erreurs, on initialise les variables de session, afin de les envoyer à la page de validation
if (!$flag_erreur_get) {
	$_SESSION['num_vol'] = $ret_donnees_reserv['num_vol'];
	$_SESSION['jour'] = $ret_donnees_reserv['jour'];
	$_SESSION['mois'] = $ret_donnees_reserv['mois'];
} else {
	header('Location: home.php');
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<title>Page suppr_reserv.php du projet IBD</title>
		<link rel="stylesheet" type="text/css" href="inc/css/base.css" media="all" />
		<link rel="stylesheet" type="text/css" href="sinc/css/modele03.css" media="screen" />

	</head>
	<body>
		<?php require_once('inc/header_reg.inc.php'); ?>
		<div id="contenu">
			<p>
				Voulez-vous vraiment annuler votre r&eacute;servation sur le vol <?php echo($num_vol); ?> du <?php echo(str_pad($jour, 2, '0', STR_PAD_LEFT) .'/'. str_pad($mois, 2, '0', STR_PAD_LEFT)); ?> ?<br />
				<?php
				if ($res_donnees_reserv['statut'] == 'OK') {
				?>
				Vous êtes pour l'instant listé parmi les passagers, en liste principale
				<?php } else { ?>
				Vous êtes pour l'instant <?php echo(); ?> sur la liste d'attente
				<?php } ?>
			</p>
			<a href="suppr_reserv_process.php" class="gauche" title="Cliquer ici pour annuler votre commande">Annuler ma commande</a>
			<a href="home.php" class="droite" title="Cliquer ici pour revenir à la page d'accueil">Revenir &agrave; la page d'accueil</a>
			<br style="clear:both" />
		</div>
		<?php require_once('inc/footer.inc.php'); ?>
	</body>
</html>
