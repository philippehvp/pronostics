<?php
	include_once('commun_administrateur.php');

	// Page de lancement des calculs de points

	// Lecture des paramètres passés à la page

	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;

	$ordreSQL =		'	CALL sp_calcultouslesscores(' . $journee . ')';
	$bdd->exec($ordreSQL);
?>
