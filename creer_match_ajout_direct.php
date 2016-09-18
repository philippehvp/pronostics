<?php
	// Ajout du match dans la table des matches en direct
	include_once('commun_administrateur.php');
	
	
	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	
	$ordreSQL =		'	REPLACE INTO	matches_direct(Matches_Match)' .
					'	SELECT ' . $match;
	$bdd->exec($ordreSQL);

?>