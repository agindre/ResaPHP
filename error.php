<?php
require_once('inc/config.inc.php');
require_once('inc/functions.inc.php');

// Avant toute autre chose, on ouvre la session, pour pouvoir accéder aux variables de session
session_start();
unset($_SESSION);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<title>Page error.php du projet IBD</title>
		<link rel="stylesheet" type="text/css" href="inc/css/base.css" media="all" />
		<link rel="stylesheet" type="text/css" href="inc/css/modele03.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="inc/css/addon.css" media="screen" />
	</head>
	<body>
		<?php require_once('inc/header_nreg.inc.php'); ?>
		<div id="contenu">
			<p>
				Le site a rencontr&eacute; un probl&egrave;me et doit ferm&eacute;.<br />Nous vous prions de nous excuser pour la gène occasionn&eacute;e, merci de votre compr&eacute;hension.
			</p>
		</div>
		<?php require_once('inc/footer.inc.php'); ?>
	</body>
</html>
