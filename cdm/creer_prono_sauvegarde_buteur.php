<?php
	include_once('commun.php');

	// Sauvegarde du pronostic de meilleur buteur
	
	// Lecture des paramètres passés à la page
	$joueur = isset($_POST["joueur"]) ? $_POST["joueur"] : 0;

	if($_SESSION["cdm_pronostiqueur"] != 1 && time() > 1668960000) {
		echo 'Heure de pronostic dépassée';
		exit();
	}

	
	// Création de la ligne si elle n'existe pas encore en base
	$ordreSQL =		'	INSERT INTO	cdm_pronostics_buteur(Pronostiqueurs_Pronostiqueur)' .
					'	SELECT		' . $_SESSION["cdm_pronostiqueur"] . ' AS Pronostiqueurs_Pronostiqueur' .
					'	WHERE		NOT EXISTS (SELECT * FROM cdm_pronostics_buteur WHERE Pronostiqueurs_Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"] . ')' .
					'	LIMIT		1';

	// Mise à jour des données dans la table
	$ordreSQL =		'	UPDATE		cdm_pronostics_buteur' .
					'	SET			Joueurs_Joueur = ' . $joueur .
					'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"];

	$bdd->exec($ordreSQL);

?>