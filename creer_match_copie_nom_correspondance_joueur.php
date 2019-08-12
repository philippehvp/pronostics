<?php
	// Copie du nom de correspondance d'un joueur depuis la fenêtre de correction de l'effectif
	include_once('commun_administrateur.php');
	include_once('creer_match_fonctions.php');
	
	
	// Lecture des paramètres passés à la page
	$joueur = isset($_POST["joueur"]) ? $_POST["joueur"] : 0;
	$nomCorrespondance = isset($_POST["nomCorrespondance"]) ? $_POST["nomCorrespondance"] : '';
	$origine = isset($_POST["origine"]) ? $_POST["origine"] : 0;
	
	if($origine == 0)
		return;
	
	$nomCorrespondance = urldecode($nomCorrespondance);
	
	switch($origine) {
		case 1: $champ = 'Joueurs_NomCorrespondance'; break;
		case 2: $champ = 'Joueurs_NomCorrespondanceComplementaire'; break;
		case 3: $champ = 'Joueurs_NomCorrespondanceCote'; break;
	}
	
	
	$ordreSQL =		'	UPDATE		joueurs' .
					'	SET			' . $champ . ' = ' . $bdd->quote(remplacerCaracteres($nomCorrespondance)) .
					'	WHERE		Joueur = ' . $joueur;

	$bdd->exec($ordreSQL);

?>