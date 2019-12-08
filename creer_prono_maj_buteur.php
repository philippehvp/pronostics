<?php
	include_once('commun.php');

	// Mise à jour des buteurs
	
	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$joueurs = isset($_POST["joueurs"]) ? $_POST["joueurs"] : 0;
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;

	// On vérifie avant tout qu'il n'est pas trop tard pour faire la modification
	$ordreSQL =		'	SELECT		CASE' .
						'				WHEN	matches.Matches_Date > NOW() AND (pronostics_carrefinal.PronosticsCarreFinal_Coefficient IS NULL OR pronostics_carrefinal.PronosticsCarreFinal_Coefficient <> 0)' .
						'				THEN	1' .
						'				ELSE	0' .
						'			END AS Buteurs_Pronostiquables' .
					'	FROM		matches' .
					'	LEFT JOIN	pronostics_carrefinal' .
					'				ON		matches.Match = pronostics_carrefinal.Matches_Match' .
					'						AND		pronostics_carrefinal.Pronostiqueurs_Pronostiqueur = ' . $_SESSION['pronostiqueur'] .
					'	WHERE		matches.Match = ' . $match;

	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetch();
	$buteursPronostiquables = $donnees["Buteurs_Pronostiquables"];
	$req->closeCursor();

	if($buteursPronostiquables == 0) {
		exit();
	}
	
	// Suppression des buteurs déjà existants
	$ordreSQL =		'	DELETE FROM		pronostics_buteurs' .
					'	WHERE			Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
					' 					AND		Matches_Match = ' . $match .
					'					AND		Equipes_Equipe = ' . $equipe;
	
	$bdd->exec($ordreSQL);
	
	// Ajout des buteurs
	// Pour les traces, les numéros de buteurs sont stockés dans une chaîne de caractères
	$trace = "";
	for($i = 0; $i < $joueurs; $i++) {
		$joueur = isset($_POST["joueur" . $i]) ? $_POST["joueur" . $i] : 0;
		$trace .= $joueur . ';';

		$ordreSQL =		'	INSERT INTO		pronostics_buteurs(Pronostiqueurs_Pronostiqueur, Matches_Match, Joueurs_Joueur, Equipes_Equipe) ' .
						'	VALUES(' . $pronostiqueur . ', ' . $match . ', ' . $joueur . ', ' . $equipe . ')';
		$bdd->exec($ordreSQL);

	}
	
	// On doit afficher à l'utilisateur un récapitulatif des buteurs qu'il a sélectionnés
	$ordreSQL =		'	SELECT		joueurs.Joueurs_NomFamille, IFNULL(joueurs.Joueurs_Prenom, \'\') AS Joueurs_Prenom, COUNT(*) AS Buteurs_NombreButs' .
					'	FROM		pronostics_buteurs' .
					'	JOIN		joueurs_equipes' .
					'				ON		pronostics_buteurs.Joueurs_Joueur = joueurs_equipes.Joueurs_Joueur' .
					'				AND		pronostics_buteurs.Equipes_Equipe = joueurs_equipes.Equipes_Equipe' .
					'	JOIN		joueurs' .
					'				ON		joueurs_equipes.Joueurs_Joueur = joueurs.Joueur' .
					'	JOIN		matches' .
					'				ON		pronostics_buteurs.Matches_Match = matches.Match' .
					'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
					'				AND		Matches_Match = ' . $match .
					'				AND		pronostics_buteurs.Equipes_Equipe = ' . $equipe .
					'				AND		JoueursEquipes_Debut <= matches.Matches_Date' .
					'				AND		(' .
					'							JoueursEquipes_Fin IS NULL' .
					'							OR		JoueursEquipes_Fin > matches.Matches_Date' .
					'						)' .
					'	GROUP BY	joueurs.Joueur';

	$req = $bdd->query($ordreSQL);
	$buteurs = $req->fetchAll();
	$listeButeurs = '';
	foreach($buteurs as $unButeur) {
		if($unButeur["Buteurs_NombreButs"] == 1)
			$listeButeurs .= $unButeur["Joueurs_NomFamille"] . ($unButeur["Joueurs_Prenom"] != '' ? (' ' . $unButeur["Joueurs_Prenom"]) : '') . ', ';
		else
			$listeButeurs .= $unButeur["Joueurs_NomFamille"] . ($unButeur["Joueurs_Prenom"] != '' ? (' ' . $unButeur["Joueurs_Prenom"]) : '') . ' (x' . $unButeur["Buteurs_NombreButs"] . '), ';
	}
	
	if($listeButeurs != '')
		$listeButeurs = substr($listeButeurs, 0, strlen($listeButeurs) - 2);
	
	$tableau = array();
	
	$tableau['buteurs'] = $listeButeurs;
	
	// Création de la trace
	$nomFichier = '../traces/buteurs/' . $match . '_' . $_SESSION["pronostiqueur"] . '_' . $equipe . '.txt';
	file_put_contents($nomFichier, $trace);

	echo json_encode($tableau);

?>