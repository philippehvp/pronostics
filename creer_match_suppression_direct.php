<?php
	// Suppression du match de la table des matches en direct
	include_once('commun_administrateur.php');


	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;

	$ordreSQL =		'	DELETE FROM matches_direct WHERE Matches_Match = ' . $match;
	$bdd->exec($ordreSQL);

?>