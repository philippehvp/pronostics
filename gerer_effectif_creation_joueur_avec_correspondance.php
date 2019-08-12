<?php
	include_once('commun_administrateur.php');

	// Création d'un joueur
	
	// Lecture des paramètres passés à la page
	$nomFamille = isset($_POST["nomFamille"]) ? $_POST["nomFamille"] : '';
	$prenom = isset($_POST["prenom"]) ? $_POST["prenom"] : '';
	$nomCorrespondance = isset($_POST["nomCorrespondance"]) ? $_POST["nomCorrespondance"] : '';
	$poste = isset($_POST["poste"]) ? $_POST["poste"] : 0;
	$dateDebutPresence = isset($_POST["dateDebutPresence"]) ? $_POST["dateDebutPresence"] : 0;
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
	
	$nomFamille = urldecode($nomFamille);
	$prenom = urldecode($prenom);
	$nomCorrespondance = urldecode($nomCorrespondance);

	// Création du joueur
	if($prenom == '') {
		if($nomCorrespondance == '')
			$ordreSQL =	'	INSERT INTO		joueurs(Joueurs_NomFamille, Joueurs_Prenom, Postes_Poste) VALUES (\'' . $nomFamille . '\', NULL, ' . $poste . ')';
		else
			$ordreSQL =	'	INSERT INTO		joueurs(Joueurs_NomFamille, Joueurs_Prenom, Postes_Poste, Joueurs_NomCorrespondance) VALUES (\'' . $nomFamille . '\', NULL, ' . $poste . ', \'' . $nomCorrespondance . '\')';
	}
	else {
		if($nomCorrespondance == '')
			$ordreSQL =	'	INSERT INTO		joueurs(Joueurs_NomFamille, Joueurs_Prenom, Postes_Poste) VALUES (\'' . $nomFamille . '\', \'' . $prenom . '\', ' . $poste . ')';
		else
			$ordreSQL =	'	INSERT INTO		joueurs(Joueurs_NomFamille, Joueurs_Prenom, Postes_Poste, Joueurs_NomCorrespondance) VALUES (\'' . $nomFamille . '\', \'' . $prenom . '\', ' . $poste . ', \'' . $nomCorrespondance . '\')';
	}

	$bdd->exec($ordreSQL);
	
	// On lit le numéro du joueur nouvellement créé
	$ordreSQL =	'	SELECT		MAX(Joueur) AS Joueur FROM joueurs';
	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetch();
	$joueur = $donnees["Joueur"];
	
	// On crée une nouvelle ligne dans la table d'association des joueurs et des équipes
	$ordreSQL =	'	INSERT INTO	joueurs_equipes(Joueurs_Joueur, Equipes_Equipe, JoueursEquipes_Debut)' .
				'	VALUES(' . $joueur . ', ' . $equipe . ', STR_TO_DATE(\'' . $dateDebutPresence . '\', \'%d/%m/%Y\'))';

	$bdd->exec($ordreSQL);
	
?>