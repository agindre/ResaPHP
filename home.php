<?php
require_once('inc/config.inc.php');
require_once('inc/connec_bdd.inc.php');
require_once('inc/functions.inc.php');

// Avant toute autre chose, on ouvre la session, pour pouvoir accéder aux variables de session
session_start();
unset($_SESSION['num_vol'], $_SESSION['jour'], $_SESSION['mois']);

$flag_reg = FALSE;
if (isset($_SESSION['id'], $_SESSION['nom'], $_SESSION['prenom'], $_SESSION['mail'])) {
	$flag_reg = TRUE;
	$code_passager = pg_escape_string($_SESSION['id']);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<title>Page home.php du projet IBD</title>
		<link rel="stylesheet" type="text/css" href="inc/css/base.css" media="all" />
		<link rel="stylesheet" type="text/css" href="inc/css/modele03.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="inc/css/addon.css" media="screen" />
	</head>
	<body>
		<?php 
			if ($flag_reg) {
				require_once('inc/header_reg.inc.php');
				$class = ' class="gauche credit"';
			} else {
				require_once('inc/header_nreg.inc.php');
				$class = ' class="credit"';
			}
		?>
		<div id="contenu">
			<p>
				Bienvenue sur la page d'accueil du site du projet IBD des FIPA2. Il s'agit d'un site factice de r&eacute;servations de vol d'une compagnie a&eacute;rienne.<br />
				Afin d'utiliser le site, vous pouvez directement vous logguer, sur la page Inscription.
			</p>
			<div<?php echo($class); ?>>
				Personnes ayant travaill&eacute; sur le projet :
				<ul>
					<li>Alexandre Gindre</li>
					<li>Simon Laubet-Xavier</li>
				</ul>
			</div>
			<?php if ($flag_reg) { ?>
			<div class="droite">
				Liste des réservations :
				<?php
				try {
					$req_reserv_passager = 'SELECT reservation.num_vol, reservation.jour, reservation.mois, statut, date_limite_reserv, destination
						FROM reservation NATURAL JOIN depart NATURAL JOIN vol
						WHERE reservation.code_passager = \''. $code_passager .'\'
						ORDER BY date_reserv;';
					$res_reserv_passager = pg_query($req_reserv_passager);
				} catch (Exception $e) {
					echo("\t\t\t\t".'Erreur lors de la connexion &agrave; la base de donn&eacute;es.'."\n");				
				}
				
				echo("\t\t\t\t".'<ul>'."\n");
				while ($ret_reserv_passager = pg_fetch_assoc($res_reserv_passager)) {
					echo("\t\t\t\t\t".'<li><a href="suppr_reserv.php?num='. $ret_reserv_passager['num_vol'] .'&jour='. $ret_reserv_passager['jour'] .'&mois='. $ret_reserv_passager['mois'] .'" title="Cliquer ici pour annuler le vol">Vol vers '. $ret_reserv_passager['destination'] .' du '. str_pad($ret_reserv_passager['jour'], 2, '0', STR_PAD_LEFT) .'/'. str_pad($ret_reserv_passager['mois'], 2, '0', STR_PAD_LEFT) .' ('. $ret_reserv_passager['statut'] .')</a></li>'."\n");
				}
				echo("\t\t\t\t".'</ul>'."\n");
				?>
			</div>
			<?php } ?>
			<br style="clear:both;" />
		</div>
		<?php require_once('inc/footer.inc.php'); ?>
	</body>
</html>
