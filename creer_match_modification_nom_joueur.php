<?php
	// Modification du prénom d'un joueur depuis la fenêtre de correction de l'effectif
	include_once('commun_administrateur.php');


	// Lecture des paramètres passés à la page
	$joueur = isset($_POST["joueur"]) ? $_POST["joueur"] : 0;
	$nom = isset($_POST["nom"]) ? $_POST["nom"] : '';

	$nom = urldecode($nom);

	$ordreSQL =		'	UPDATE		joueurs' .
					'	SET			Joueurs_NomFamille = ' . $bdd->quote($nom) .
					'	WHERE		Joueur = ' . $joueur;

	$bdd->exec($ordreSQL);

?>