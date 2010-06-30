<?php
/* =========================================
  Script de connection à la BDD PostGreSQL
========================================= */
$_host = 'localhost';
$_port = '5432';
$_db = 'projetibd';
$_user = 'grimm';
$_pwd = '123456';

try {
	$_connection = pg_connect('host='. $_host .' port='. $_port .' dbname='. $_db .' user='. $_user .' password='. $_pwd);
} catch (Exception $e) {
 	header('Location: error.php');
        exit();
}
?>
