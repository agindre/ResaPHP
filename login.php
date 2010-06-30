<?php
require_once('inc/config.inc.php');
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
		<title>Page login.php du projet IBD</title>
		<link rel="stylesheet" type="text/css" href="inc/css/base.css" media="all" />
		<link rel="stylesheet" type="text/css" href="inc/css/modele03.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="inc/css/addon.css" media="screen" />
	</head>
	<body>
<?php include('inc/header_nreg.inc.php'); ?>
		<div id="contenu">
			<p>
				Vous devez vous inscrire pour commander sur notre site. Prenez 5 minutes, et rejoignez-nous !
			</p>
			<?php if (isset($_liste_err)) { ?>
			<div class="error">
				<h6>Nous avons rencontr&eacute; des erreurs lors de la pr&eacute;c&eacute;dente tentative d'inscription :</h6> 
				<ul>
					<?php
					foreach ($_liste_err as $erreur) {
						echo("\t\t\t\t\t".'<li>'. $erreur .'</li>'."\n");
					}
					?>
				</ul>
			</div>
			<?php } ?>
			<form method="post" action="login_process.php">
				<label for="prenom">Votre pr&eacute;nom : <strong>*</strong></label><input type="text" id="prenom" name="prenom" /><br />
				<label for="nom">Votre nom : <strong>*</strong></label><input type="text" id="nom" name="nom" /><br />
				<label for="adresse">Votre adresse : <strong>*</strong></label><textarea cols="50" rows="3" id="adresse" name="adresse">Tapez votre adresse ici...</textarea><br />
				<label for="departmt">Votre d&eacute;partement : <strong>*</strong></label>
				<?php ddlDepart(); ?>
				<label for="tel">Votre num&eacute;ro de t&eacute;l&eacute;phone :</label><input type="text" id="tel" name="tel" maxlength="14" /><br />
				<label for="mail">Votre adresse mail : <strong>*</strong></label><input type="text" id="mail" name="mail" /><br />
				<label for="passwd">Votre mot de passe : <strong>*</strong></label><input type="password" id="passwd" name="passwd" /><br />
				<input type="submit" value="Valider l'inscription" />
			</form>
		</div>
<?php include('inc/footer.inc.php'); ?>
	</body>
</html>
