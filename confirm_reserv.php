<?php
require_once('inc/config.inc.php');
require_once('inc/functions.inc.php');

// Avant toute autre chose, on ouvre la session, pour pouvoir accéder aux variables de session
session_start();

if (!isset($_SESSION['id'], $_SESSION['nom'], $_SESSION['prenom'], $_SESSION['mail'])) {
	$_SESSION['liste_err'] = array('Vous devez être connecté avant de réserver une place sur un vol');
	header('Location: register.php');
	exit();
}

$num_vol = $_SESSION['num_vol'];
$jour = $_SESSION['jour'];
$mois = $_SESSION['mois'];
unset($_SESSION['num_vol'], $_SESSION['jour'], $_SESSION['mois']);

if (!isset($_SESSION['id'], $_SESSION['nom'], $_SESSION['prenom'], $_SESSION['mail'])) {
	header('Location: error.php');
	exit();	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<title>Page confirm_reserv.php du projet IBD</title>
		<link rel="stylesheet" type="text/css" href="inc/css/base.css" media="all" />
		<link rel="stylesheet" type="text/css" href="inc/css/modele03.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="inc/css/addon.css" media="screen" />
	</head>
	<body>
		<?php 
			// Desormais, on est sur que l'utilisateur est connecte, puisqu'il a ete redirige si ce n'est pas le cas
			require_once('inc/header_reg.inc.php');
		?>
		<div id="contenu">
			<p>
				La commande sur le vol <?php echo($num_vol); ?> du <?php echo(str_pad($jour, 2, '0', STR_PAD_LEFT) .'/'. str_pad($mois, 2, '0', STR_PAD_LEFT)); ?> au nom de <?php echo($_SESSION['prenom'] .' '. strtoupper($_SESSION['nom'])); ?> a bien été validé.<br />
				Vous allez recevoir d'ici peu un mail de r&eacute;capitulatif des donn&eacute;es du d&eacute;part.<br />
				Nous vons remer&ccedil;ions pour votre confiance, et vous souhaitons un agr&eacute;able voyage en notre compagnie.
			</p>
			<a href="home.php" class="gauche" title="Cliquer ici pour revenir à la page d'accueil">Revenir à la page d'accuei</a>
			<a href="liste_depart.php" class="droite" title="Cliquer ici pour revenir à la liste des prochains départs">Revenir à la liste des prochains départs</a>
		</div>
		<?php require_once('inc/footer.inc.php'); ?>
	</body>
</html>
