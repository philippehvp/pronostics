<?php
	include_once('commun_administrateur.php');

	// Sauvegarde des informations d'une journée
	
	// Lecture des paramètres passés à la page
	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;
	$journeeLienPage = isset($_POST["journeeLienPage"]) ? $_POST["journeeLienPage"] : '';
	
	// Pour mise à jour du match avec tous les paramètres (même s'ils n'ont pas été modifiés)
	$ordreSQL =		'	UPDATE		journees' .
					'	SET			Journees_LienPage = ' . $bdd->quote($journeeLienPage) .
					'	WHERE		journees.Journee = ' . $journee;

	$bdd->exec($ordreSQL);


?>