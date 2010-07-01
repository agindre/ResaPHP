<?php
require_once('inc/config.inc.php');
require_once('inc/connec_bdd.inc.php');
require_once('inc/functions.inc.php');

// Dans cette page, on va inscrire le nouveau client.
// Mais avant cela, il faut vérifier que les données qu'il a transmis sont exactes, et sous une forme correcte.
// On doit également, avant toute insertion, créer l'id du client, à partir de son nom, de son département, et d'un numéro incrémentiel

// Tableau des erreurs des données utilisateurs. Cette liste sera affichée avant la redirection vers la page login.php
$_liste_err = array();

// On teste si TOUTES les variables dont nous avons besoin sont bien passé en paramètres POST
if (empty($_POST['prenom']) || empty($_POST['nom']) || empty($_POST['adresse']) || empty($_POST['mail']) || empty($_POST['passwd'])) {
	$_liste_err[] = 'Trop peu de param&egrave;tres reçus';
} else {
	$prenom = pg_escape_string($_POST['prenom']);
	$nom = pg_escape_string($_POST['nom']);
	$adresse = pg_escape_string(trim($_POST['adresse']));
	$departmt = pg_escape_string($_POST['departmt']);
	
	if (!empty($_POST['tel'])) {
		$tel = '\''. pg_escape_string($_POST['tel']) .'\'';
	} else {
		$tel = 'NULL';
	}
	
	$mail = pg_escape_string($_POST['mail']);
	$passwd = pg_escape_string($_POST['passwd']);
		
	// On peut désormais tester la validité des valeurs envoyées par l'utilisateur
	if ($adresse == 'Tapez votre adresse ici...') {
		$_liste_err[] = 'Adresse non valide';
	}
	if (!isset($liste_departements[$departmt])) {
		$_liste_err[] = 'Probl&egrave;me lors de la réception du département';
	}
	if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
		$_liste_err[] = 'Adresse mail non valide';
	}
}

if (count($_liste_err) > 0) {
	session_start();
	$_SESSION['liste_err'] = $_liste_err;
 	header('Location: login.php');
 	exit();
} else {
	// On peut s'occuper de la création de l'id du client
	// Pour cela, on doit surtout récupérer le max du num séquentiel dans l'id passager, pour le departement et les 3 premières lettres du nom
	try {
		$req_id_client = 'SELECT code_passager FROM passager WHERE SUBSTRING(code_passager from 1 for '. (strlen($departmt) == 2 ? 5 : 6) .') = \''. strtoupper(substr($nom, 0, 3)) .'\' || \''. $departmt .'\' ORDER BY code_passager DESC;';
		$res_id_client = pg_query($req_id_client);
		$ret_id_client = pg_fetch_assoc($res_id_client);
		$ret_id_client = $ret_id_client['code_passager'];
	} catch (Exception $e) {
 		header('Location: error.php');
 		exit();
	}
	
	// On crée maintenant l'id passager pour notre nouveau client
	if (strlen($departmt) == 2) {
		$num_id = substr($ret_id_client, 5, 5);
		$new_num_id = strtoupper(substr($nom, 0, 3)) . $departmt . substr("00000". ($num_id + 1), -5);
	} else {
		$num_id = substr($ret_id_client, 6, 4);
		$new_num_id = strtoupper(substr($nom, 0, 3)) . $departmt . substr("0000". ($num_id + 1), -4);
	}
	
	// Maintenant que le nouvel ID passager est cree, on peut tenter d'inserer le nouveau client
	try {
		pg_begin();
		$req_new_client = 'INSERT INTO passager (code_passager, nom_pass, prenom_pass, adresse_pass, departmt_pass, tel_pass, mail_pass, mdp_pass) VALUES (\''. $new_num_id .'\', \''. $nom .'\', \''. $prenom .'\', \''. $adresse .'\', \''. $departmt .'\', '. $tel .', \''. $mail .'\', \''. $passwd .'\');';
		$res_new_client = pg_query($req_new_client);
		pg_commit();
	} catch (Exception $e) {
		pg_rollback();
 		header('Location: error.php');
 		exit();
	}
	
	// Si on arrive dans cette partie du code, c'est que l'inscription s'est bien passee
	// On peut donc créer la session, avec les variables utiles
	session_start();
	$_SESSION['id'] = $new_num_id;
	$_SESSION['nom'] = $nom;
	$_SESSION['prenom'] = $prenom;
	$_SESSION['mail'] = $mail;
	header('Location: home.php');
	exit();
}
?>
