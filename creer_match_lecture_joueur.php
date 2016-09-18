<?php
	include_once('commun_administrateur.php');
	include_once('creer_match_fonctions.php');

	// Lecture des informations d'un joueur
	
	// Lecture des paramètres passés à la page
	$joueur = isset($_POST["joueur"]) ? $_POST["joueur"] : 0;
	$origine = isset($_POST["origine"]) ? $_POST["origine"] : 0;
	
	switch($origine) {
		case 1: $champ = 'Joueurs_NomCorrespondance'; break;
		case 2: $champ = 'Joueurs_NomCorrespondanceComplementaire'; break;
		case 3: $champ = 'Joueurs_NomCorrespondanceCote'; break;
	}
	
	$ordreSQL =		'	SELECT		Joueurs_Prenom, Joueurs_NomFamille, ' . $champ .
					'	FROM		joueurs' .
					'	WHERE		Joueur = ' . $joueur;
	$req = $bdd->query($ordreSQL);
	$joueurs = $req->fetchAll();
	
	$tableau = array();
	
	if(sizeof($joueurs) == 1) {
		$tableau['erreur'] = '0';
		$tableau['prenom'] = $joueurs[0]["Joueurs_Prenom"];
		$tableau['nom'] = $joueurs[0]["Joueurs_NomFamille"];
		$tableau['correspondance'] = remplacerCaracteres($joueurs[0][$champ]);
	}
	else {
		$tableau['erreur'] = '1';
	}
	
	echo json_encode($tableau);
?>