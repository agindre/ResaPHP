<?php
require_once('inc/config.inc.php');
require_once('inc/functions.inc.php');
require_once('inc/connec_bdd.inc.php');

// Avant toute autre chose, on ouvre la session, pour pouvoir accéder aux variables de session
session_start();
unset($_SESSION['num_vol'], $_SESSION['jour'], $_SESSION['mois']);

$flag_erreur_get = FALSE;
// On recupere les variables normalement passees en GET
if (empty($_GET['num']) || empty($_GET['jour']) || empty($_GET['mois'])) {
	$flag_erreur_get = TRUE;
} else {
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
			$req_donnees_depart = 'SELECT depart.num_vol, jour, mois, nb_places_disp, nb_places, destination, vol_h_depart, vol_h_arrivee, frequence, duree_vol, fabriquant, modele
				FROM depart NATURAL JOIN vol NATURAL JOIN type_avion
				WHERE depart.num_vol = '. $num_vol .' AND jour = '. $jour .' AND mois = '. $mois .';';
			$res_donnees_depart = pg_query($req_donnees_depart);
			$ret_donnees_depart = pg_fetch_assoc($res_donnees_depart);
		} catch (Exception $e) {
			header('Location: error.php');
			exit();
		}
		if (!$ret_donnees_depart) {
			// La fonction pg_fetch_assoc renvoit FALSE si la requete n'obtient aucune donnee
			$flag_erreur_get = TRUE;
		}
	}
}
// S'il n'y a pas d'erreurs, on initialise les variables de session, afin de les envoyer à la page de validation
if (!$flag_erreur_get) {
	$_SESSION['num_vol'] = $ret_donnees_depart['num_vol'];
	$_SESSION['jour'] = $ret_donnees_depart['jour'];
	$_SESSION['mois'] = $ret_donnees_depart['mois'];
} else {
	header('Location: error.php');
	exit();
}

$flag_reg = FALSE;
if (isset($_SESSION['id'], $_SESSION['nom'], $_SESSION['prenom'], $_SESSION['mail'])) {
	$flag_reg = TRUE;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<title>Page fiche_depart.php du projet IBD</title>
		<link rel="stylesheet" type="text/css" href="inc/css/base.css" media="all" />
		<link rel="stylesheet" type="text/css" href="inc/css/modele03.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="inc/css/addon.css" media="screen" />
	</head>
	<body>
	<?php 
		if ($flag_reg) {
			require_once('inc/header_reg.inc.php'); 
		} else {
			require_once('inc/header_nreg.inc.php'); 
		}	
	?>
		<div id="contenu">
		<?php
			if (!$flag_erreur_get) {
				// On doit calculer précisemment les dates d'arrivée, en tenant compte du changement de jour possible pour les vols de nuit
				$details_depart = date_parse($ret_donnees_depart['vol_h_depart']);
				$details_arrivee = date_parse($ret_donnees_depart['vol_h_arrivee']);
			
				$jour_arrivee = strtolower($ret_donnees_depart['frequence']);
				$num_jour_arrivee = $ret_donnees_depart['jour'];
				$num_mois_arrivee = $ret_donnees_depart['mois'];
			
				if ($details_arrivee['hour'] < $details_depart['hour']) {
					$num_jour_arrivee = ($ret_donnees_depart['jour'] + 1) % daysInMonth($ret_donnees_depart['mois'] - 1);
					$jour_arrivee = $liste_jours[array_search($jour_arrivee, $liste_jours) + 1];
					if ($num_jour_arrivee == 1) {
						$num_mois_arrivee = ($ret_donnees_depart['mois'] + 1) % 12;
					}
				}
				?>
				<h4>Informations du vol :</h4>
				<div class="gauche">
					<p>
						Numero du vol : <?php echo($ret_donnees_depart['num_vol']); ?><br />
						Destination : <?php echo($ret_donnees_depart['destination']); ?><br />
						Horaires de départ : <?php echo(strtolower($ret_donnees_depart['frequence']) .' '. str_pad($ret_donnees_depart['jour'], 2, '0', STR_PAD_LEFT) .'/'. str_pad($ret_donnees_depart['mois'], 2, '0', STR_PAD_LEFT) .' '. str_pad($details_depart['hour'], 2, '0', STR_PAD_LEFT) .':'. str_pad($details_depart['minute'], 2, '0', STR_PAD_LEFT)); ?><br />
						Horaires d'arriv&eacute;e : <?php echo($jour_arrivee .' '. str_pad($num_jour_arrivee, 2, '0', STR_PAD_LEFT) .'/'. str_pad($num_mois_arrivee, 2, '0', STR_PAD_LEFT) .' '. str_pad($details_arrivee['hour'], 2, '0', STR_PAD_LEFT) .':'. str_pad($details_arrivee['minute'], 2, '0', STR_PAD_LEFT)); ?><br />
						Dur&eacute;e du vol : <?php echo($ret_donnees_depart['duree_vol']); ?>
					</p>
				</div>
				<div class="droite">
					<p>
						Modele de l'avion : <?php echo($ret_donnees_depart['fabriquant'] .' '. $ret_donnees_depart['modele']); ?><br />
						Nombre de places disponibles : <?php echo($ret_donnees_depart['nb_places_disp'] .' sur '. $ret_donnees_depart['nb_places']); ?><br /><br />
						<a href="liste_depart.php" title="Cliquer ici pour revenir à la liste des départs" class="block">Revenir &agrave; la liste des d&eacute;parts</a>
						<a href="valid_reserv.php" title="Cliquer ici pour réserver sur ce départ" class="block">R&eacute;server sur ce vol</a>
					</p>
				</div>
				<br style="clear:both" />
				<?php
			} else { ?>
				<p>Le vol que vous recherchez n'existe pas</p>
				<a href="liste_depart.php" title="Cliquer ici pour revenir à la liste des départs">Revenir &agrave; la liste des d&eacute;parts</a>
			<?php }
		?>
		</div>
		<?php require_once('inc/footer.inc.php'); ?>
	</body>
</html>
