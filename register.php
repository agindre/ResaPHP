<?php
require_once('inc/config.inc.php');
require_once('inc/connec_bdd.inc.php');
require_once('inc/functions.inc.php');

// Avant toute autre chose, on ouvre la session, pour pouvoir accéder aux variables de session
session_start();
unset($_SESSION['num_vol'], $_SESSION['jour'], $_SESSION['mois']);

if (isset($_SESSION['liste_err'])) {
	$_liste_err = $_SESSION['liste_err'];
	unset($_SESSION['liste_err']);
}

if (isset($_SESSION['id'], $_SESSION['nom'], $_SESSION['prenom'], $_SESSION['mail'])) {
	header('Location: home.php');
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<title>Page register.php du projet IBD</title>
		<link rel="stylesheet" type="text/css" href="inc/css/base.css" media="all" />
		<link rel="stylesheet" type="text/css" href="inc/css/modele03.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="inc/css/addon.css" media="screen" />
	</head>
	<body>
		<?php require_once('inc/header_nreg.inc.php'); ?>
		<div id="contenu">
			<p>Vous avez un compte sur le site ? Ou alors vous &ecirc;tes un nouveau client ? Enregistrez-vous sur cette page.</p>
			<?php if (isset($_liste_err)) { ?>
			<div class="error">
				<h6>Nous avons rencontr&eacute; des erreurs lors de la pr&eacute;c&eacute;dente tentative de connexion :</h6> 
				<ul>
					<?php
					foreach ($_liste_err as $erreur) {
						echo("\t\t\t\t\t".'<li>'. $erreur .'</li>'."\n");
					}
					?>
				</ul>
			</div>
			<?php } ?>
			<form action="register_process.php" method="post" class="droite">
				<h3>Pour vous connecter :</h3>
				<label for="mail">Votre adresse email :</label><input type="text" name="mail" id="mail" /><br />
				<label for="passwd">Votre mot de passe :</label><input type="password" name="passwd" id="passwd" />
				<input type="submit" value="Se connecter" />
			</form>
			<div class="droite">
				<h3>Pour vous inscrire</h3>
				<a href="login.php" title="Cliquer ici pour acc&eacute;der directement au formulaire d'inscription">Cliquer ici</a>
			</div>
			<br style="clear:both" />
		</div>
		<?php require_once('inc/footer.inc.php'); ?>
	</body>
</html>
