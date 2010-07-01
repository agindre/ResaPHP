<?php
require_once('inc/config.inc.php');
require_once('inc/connec_bdd.inc.php');
require_once('inc/functions.inc.php');

// Avant toute autre chose, on ouvre la session, pour pouvoir accéder aux variables de session
session_start();
// myPrint_r($_SESSION);

if (!isset($_SESSION['id'], $_SESSION['nom'], $_SESSION['prenom'], $_SESSION['mail'])) {
	$_SESSION['liste_err'] = array('Vous devez être connecté avant de réserver une place sur un vol');
	header('Location: register.php');
	exit();
}

if (!isset($_SESSION['num_vol'], $_SESSION['jour'], $_SESSION['mois'])) {
	header('Location: error.php');
	exit();
}

$num_vol = pg_escape_string($_SESSION['num_vol']);
$jour = pg_escape_string($_SESSION['jour']);
$mois = pg_escape_string($_SESSION['mois']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<title>Page valid_reserv.php du projet IBD</title>
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
		<?php
			$req_donnees_depart = 'SELECT depart.num_vol, jour, mois, nb_places_disp, nb_places, destination, vol_h_depart, frequence
				FROM depart NATURAL JOIN vol NATURAL JOIN type_avion
				WHERE depart.num_vol = '. $num_vol .' AND jour = '. $jour .' AND mois = '. $mois .';';
			$res_donnees_depart = pg_query($req_donnees_depart);
			$ret_donnees_depart = pg_fetch_assoc($res_donnees_depart);
			
			$details_depart = date_parse($ret_donnees_depart['vol_h_depart']);
			
			// On affiche les principales donnees du vol, sans les afficher toutes, puisque cela a deja ete fait dans la page precedente
			echo("\t\t\t".'<p>Vous souhaitez r&eacute;server une place sur le vol '. $ret_donnees_depart['num_vol'] .' du '. strtolower($ret_donnees_depart['frequence']) .' '. str_pad($ret_donnees_depart['jour'], 2, '0', STR_PAD_LEFT) .'/'. str_pad($ret_donnees_depart['mois'], 2, '0', STR_PAD_LEFT) .' '. str_pad($details_depart['hour'], 2, '0', STR_PAD_LEFT) .'h'. str_pad($details_depart['minute'], 2, '0', STR_PAD_LEFT) .' &agrave; destination de '. $ret_donnees_depart['destination'] .'</p>'."\n");
			
			echo("\t\t\t".'<h4>Etat des r&eacute;servations : </h4>'."\n");
			// On doit maintenant récupérer les informations concernant le nombre de personnes dans la liste d'attente, si le nombre de places disponibles est nul
			if ($ret_donnees_depart['nb_places_disp'] != 0) {
				if ($ret_donnees_depart['nb_places_disp'] < $ret_donnees_depart['nb_places'] / 10) {
					$class_div = 'orange';
				} elseif ($ret_donnees_depart['nb_places_disp'] < $ret_donnees_depart['nb_places'] / 5) {
					$class_div = 'jaune';
				} else {
					$class_div = 'verte';
				}
			?>
			<div class="dispo <?php echo($class_div); ?>">
				<p>Il reste <?php echo($ret_donnees_depart['nb_places_disp']); ?> places disponibles sur les <?php echo($ret_donnees_depart['nb_places']); ?> que contient l'appareil</p>
			<?php
			} else {
				// S'il n'y a plus de places, on regarde combien de personnes sont en liste d'attente
				$req_nb_la = 'SELECT COUNT(*) AS nb_attentes FROM reservation WHERE num_vol = '. $num_vol .' AND jour = '. $jour .' AND mois = '. $mois .' AND statut = \'LA\';';
				$res_nb_la = pg_query($req_nb_la);
				$ret_nb_la = pg_fetch_assoc($res_nb_la);
				$ret_nb_la = $ret_nb_la['nb_attentes'];
			?>
			<div class="dispo rouge">
				<p>
					Il n'y a plus de places disponibles. N&eacute;anmoins, vous pouvez r&eacute;server, et passer en liste d'attente.<br />
					<?php if ($ret_nb_la == 0) { ?>
					Il n'y a actuellement personne en liste d'attente. Si vous r&eacute;vervez maintenant, vous serez le premier.
					<?php } elseif ($ret_nb_la == 1) { ?>
					Il y a actuellement une personne en liste d'attente. Si vous r&eacute;servez maintenant, vous aurez une bonne chance d'embarquer sur ce vol.
					<?php } else { ?>
					Ily a actuellement <?php echo($ret_nb_la); ?> personnes en liste d'attente.						
					<?php } ?>						
				</p>
				<?php } ?>
				<a href="liste_depart.php" class="gauche" title="Cliquer ici pour revenir &agrave; la liste des d&eacute;parts et choisir un nouveau vol sur lequel r&eacute;server">Retour &agrave; la liste des d&eacute;parts</a>
				<a href="valid_reserv_process.php" class="droite" title="Cliquer ici pour valider votre r&eacute;servation">Valider la r&eacute;servation</a>
				<br style="clear: both" />
			</div>
		</div>
	<?php include('inc/footer.inc.php'); ?>
	</body>
</html>
