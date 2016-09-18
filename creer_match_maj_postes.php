<?php
	include('commun_administrateur.php');

	// Sauvegarde dposte d'un joueur
	
	// Lecture des paramètres passés à la page
	$joueur = isset($_POST["numero_joueur"]) ? $_POST["numero_joueur"] : 0;
	$poste = isset($_POST["poste"]) ? $_POST["poste"] : -1;
	
	$ordreSQL =			'	UPDATE		joueurs' .
						'	SET			Postes_Poste = ' . $poste .
						'	WHERE		Joueur = ' . $joueur;
	$bdd->exec($ordreSQL);

?>