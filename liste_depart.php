<?php
require_once('inc/config.inc.php');
require_once('inc/functions.inc.php');
require_once('inc/connec_bdd.inc.php');

// Avant toute autre chose, on ouvre la session, pour pouvoir accéder aux variables de session
session_start();
myPrint_r($_SESSION);
unset($_SESSION['num_vol'], $_SESSION['jour'], $_SESSION['mois']);

$flag_reg = FALSE;
if (isset($_SESSION['id'], $_SESSION['nom'], $_SESSION['prenom'], $_SESSION['mail'])) {
	$flag_reg = TRUE;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<title>Page liste_depart.php du projet IBD</title>
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
			<table summary="Voici la liste des prochains departs">
				<caption>Voici la liste des prochains d&eacute;parts : </caption>
				<thead>
					<tr>
						<th>N&deg; du vol</th>
						<th>Date</th>
						<th>Horaires</th>
						<th>Destination</th>
						<th>Places disponibles</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$req_departs = 'SELECT depart.num_vol, jour, mois, nb_places_disp, destination, vol_h_depart, vol_h_arrivee, frequence
						FROM depart NATURAL JOIN vol
						WHERE mois > '. date('n') .' OR (mois = '. date('n') .' AND jour > '. date('j') .')
						ORDER BY mois ASC, jour ASC;';
					$res_departs = pg_query($req_departs);
					
					// On créé une variable booleenne pour changer le style en fonction des lignes
					$flag_pair = TRUE;
					while ($ret_departs = pg_fetch_assoc($res_departs)) {
						//myPrint_r($ret_departs, 'Vol numero '. $ret_departs['num_vol'] .' : ');
						if ($flag_pair) {
							echo("\t\t\t\t\t".'<tr class="pair">'."\n");
						} else {
							echo("\t\t\t\t\t".'<tr>'."\n");
						}
						$flag_pair = inversBool($flag_pair);
						echo("\t\t\t\t\t\t".'<td>'. $ret_departs['num_vol'] .'</td>'."\n");
						echo("\t\t\t\t\t\t".'<td>'. strtolower($ret_departs['frequence']) .' '. str_pad($ret_departs['jour'], 2, '0', STR_PAD_LEFT) .'/'. str_pad($ret_departs['mois'], 2, '0', STR_PAD_LEFT) .'</td>'."\n");
						$details_depart = date_parse($ret_departs['vol_h_depart']);
						$details_arrivee = date_parse($ret_departs['vol_h_arrivee']);
						echo("\t\t\t\t\t\t".'<td>Depart : '. str_pad($details_depart['hour'], 2, '0', STR_PAD_LEFT) .':'. str_pad($details_depart['minute'], 2, '0', STR_PAD_LEFT) .'<br />Arriv&eacute;e : '. str_pad($details_arrivee['hour'], 2, '0', STR_PAD_LEFT) .':'. str_pad($details_arrivee['minute'], 2, '0', STR_PAD_LEFT) .'</td>'."\n");
						echo("\t\t\t\t\t\t".'<td>'. $ret_departs['destination'] .'</td>'."\n");
						echo("\t\t\t\t\t\t".'<td>'. $ret_departs['nb_places_disp'] .'</td>'."\n");
						echo("\t\t\t\t\t\t".'<td><a href="fiche_depart.php?num='. $ret_departs['num_vol'] .'&jour='. $ret_departs['jour'] .'&mois='. $ret_departs['mois'] .'" title="Cliquez ici pour reserver sur ce depart">R&eacute;server</a></td>'."\n");
						echo("\t\t\t\t\t".'</tr>'."\n");
					}
				?>
				</tbody>
				<tfoot>
					<tr>
						<th>N&deg; du vol</th>
						<th>Date</th>
						<th>Horaires</th>
						<th>Destination</th>
						<th>Places disponibles</th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</div>
		<?php require_once('inc/footer.inc.php'); ?>
	</body>
</html>
