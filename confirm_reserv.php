<?php
require_once('inc/config.inc.php');
require_once('inc/connec_bdd.inc.php');
require_once('inc/functions.inc.php');

// Avant toute autre chose, on ouvre la session, pour pouvoir accéder aux variables de session
session_start();

if (!isset($_SESSION['id'], $_SESSION['nom'], $_SESSION['prenom'], $_SESSION['mail'])) {
	$_SESSION['liste_err'] = array('Vous devez &ecirc;tre connect&eacute; avant de r&eacute;server une place sur un vol');
	header('Location: register.php');
	exit();
}

$code_passager = pg_escape_string($_SESSION['id']);
$num_vol = pg_escape_string($_SESSION['num_vol']);
$jour = pg_escape_string($_SESSION['jour']);
$mois = pg_escape_string($_SESSION['mois']);
unset($_SESSION['num_vol'], $_SESSION['jour'], $_SESSION['mois']);

$position_la = get_position_la($num_vol, $jour, $mois, $code_passager, $_connection);
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
				<?php if ($position_la != TRUE) { 
				// On est obligé de passer par ce test un peu spécial, car la fonction renvoit un booléen, seulement si il y a eu une erreur, et une chaîne de caractère dans le cas contraire
				?>
				Vous &ecirc;tes <?php echo($position_la); ?> dans la liste d'attente.<br />
				<?php } ?>
				Vous allez recevoir d'ici peu un mail de r&eacute;capitulatif des donn&eacute;es du d&eacute;part.<br />
				Nous vons remer&ccedil;ions pour votre confiance, et vous souhaitons un agr&eacute;able voyage avec notre compagnie.
			</p>
			<a href="home.php" class="gauche" title="Cliquer ici pour revenir &agrave; la page d'accueil">Revenir &agrave; la page d'accueil</a>
			<a href="liste_depart.php" class="droite" title="Cliquer ici pour revenir &agrave; la liste des prochains d&eacute;parts">Revenir &agrave; la liste des prochains d&eacute;parts</a>
			<br style="clear:both;" />
		</div>
		<?php require_once('inc/footer.inc.php'); ?>
	</body>
</html>
