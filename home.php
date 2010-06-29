<?php
require_once('inc/config.inc.php');
require_once('inc/functions.inc.php');

// Avant toute autre chose, on ouvre la session, pour pouvoir accéder aux variables de session
session_start();
unset($_SESSION['num_vol'], $_SESSION['jour'], $_SESSION['mois']);

$flag_reg = FALSE;
if (isset($_SESSION['id'], $_SESSION['nom'], $_SESSION['prenom'], $_SESSION['mail'])) {
	$flag_reg = TRUE;
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
			} else {
				require_once('inc/header_nreg.inc.php'); 
			}
		?>
		<div id="contenu">
			<p>
				Bienvenue sur la page d'accueil du site du projet IBD des FIPA2. Il s'agit d'un site factice de r&eacute;servations de vol d'une compagnie a&eacute;rienne.<br />
				Afin d'utiliser le site, vous pouvez directement vous logguer, sur la page Inscription
				<br /><br />
				Personnes ayant travaill&eacute; sur le projet :
				<ul>
					<li>Alexandre Gindre</li>
					<li>Simon Laubet-Xavier</li>
				</ul>
			</p>
		</div>
		<?php require_once('inc/footer.inc.php'); ?>
	</body>
</html>
