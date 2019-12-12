<?php
	// Modification du prénom d'un joueur depuis la fenêtre de correction de l'effectif
	include_once('commun_administrateur.php');


	// Lecture des paramètres passés à la page
	$joueur = isset($_POST["joueur"]) ? $_POST["joueur"] : 0;
	$prenom = isset($_POST["prenom"]) ? $_POST["prenom"] : '';

	$prenom = urldecode($prenom);

	if($prenom != '')
		$ordreSQL =		'	UPDATE		joueurs' .
						'	SET			Joueurs_Prenom = ' . $bdd->quote($prenom) .
						'	WHERE		Joueur = ' . $joueur;
	else
		$ordreSQL =		'	UPDATE		joueurs' .
						'	SET			Joueurs_Prenom = NULL' .
						'	WHERE		Joueur = ' . $joueur;

	$bdd->exec($ordreSQL);

?>