<?php
/* =========================================
  Script de connection à la BDD PostGreSQL
========================================= */
$_host = 'localhost';
$_port = '5432';
$_db = 'projetibd';
$_user = 'simon';
$_pwd = 'ww6ekh9z';

try {
	$_connection = pg_connect('host='. $_host .' port='. $_port .' dbname='. $_db .' user='. $_user .' password='. $_pwd);
} catch (Exception $e) {
 	header('Location: error.php');
}
?>
