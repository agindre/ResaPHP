<?php
require_once('inc/config.inc.php');
require_once('inc/functions.inc.php');
require_once('inc/connec_bdd.inc.php');

// Dans cette page, on va permettre à l'utilisateur de se connecter, afin de réserver son billet
// Pour cela, on vérifie tout d'abord que les deux champs nécessaires sont bien remplis, et que les valeurs sont valides
// On récupère ensuite les informations utiles, et on le stocke en variable de session

// Tableau des erreurs des données utilisateurs. Cette liste sera affichée avant la redirection vers la page register.php
$_liste_err = array();

// On teste si TOUTES les variables dont nous avons besoin sont bien passé en paramètres POST
if (empty($_POST['mail'])) {
	$_liste_err[] = 'Il manque l\'adresse mail';
} elseif (empty($_POST['passwd'])) {
	$_liste_err[] = 'Il manque le mot de passe';
} else {
	// On a tous les éléments pour se connecter
	$mail = pg_escape_string($_POST['mail']);
	$passwd = $_POST['passwd'];
	
	// On doit cependant vérifier que l'adresse mail est valide
	if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
		$_liste_err[] = 'Adresse mail non valide';
	}
	
	// On doit maintenant vérifier qu'il y a correspondance entre l'adresse mail et le mot de passe
	try {
		$req_register = 'SELECT code_passager, nom_pass, prenom_pass, mdp_pass FROM passager WHERE mail_pass = \''. $mail .'\';';
		$res_register = pg_query($req_register);
		$ret_register = pg_fetch_assoc($res_register);
		$passwd_bdd = $ret_register['mdp_pass'];
	} catch (Exception $e) {
 		header('Location: error.php');
 		exit();
	}
	
	if (empty($passwd_bdd)) {
 		$_liste_err[] = 'L\'adresse mail n\'existe pas dans notre base de données';
	} elseif ($passwd_bdd != $passwd) {
		$_liste_err[] = 'Mot de passe incorrect';
	}
}

if (count($_liste_err) > 0) {
	session_start();
	$_SESSION['liste_err'] = $_liste_err;
 	header('Location: register.php');
 	exit();
} else {
	// Dans ce cas-ci, on peut supposer que l'utilisateur peut se connecter, puisque tous les tests de vérifications sont passés
	session_start();
	$_SESSION['id'] = $ret_register['code_passager'];
	$_SESSION['nom'] = $ret_register['nom_pass'];
	$_SESSION['prenom'] = $ret_register['prenom_pass'];
	$_SESSION['mail'] = $mail;
	header('Location: home.php');
	exit();
}
?>
