<?php
	include_once('commun_administrateur.php');

	// Sauvegarde de la cote buteur d'un joueur

	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
	$joueur = isset($_POST["joueur"]) ? $_POST["joueur"] : 0;
	$cote = isset($_POST["cote"]) ? $_POST["cote"] : '';
	$poste = isset($_POST["poste"]) ? $_POST["poste"] : -1;

	// Si la ligne existe déjà, il faut la mettre à jour
	// Dans le cas inverse, il faut créer la ligne
	// Cas spécifique : si la cote est vide, on la supprime de la table
	if($cote == '')
		$ordreSQL =		'	DELETE FROM	joueurs_cotes' .
						'	WHERE		Joueurs_Joueur = ' . $joueur .
						'				AND		Equipes_Equipe = ' . $equipe .
						'				AND		Matches_Match = ' . $match;
	else
		$ordreSQL =		'	REPLACE INTO joueurs_cotes(Joueurs_Joueur, Equipes_Equipe, Matches_Match, JoueursCotes_Cote)' .
						'	VALUES(' . $joueur . ', ' . $equipe . ', ' . $match . ', ' . $cote . ')';

	$bdd->exec($ordreSQL);

	// Mise à jour du poste
	$ordreSQL =			'	UPDATE		joueurs' .
						'	JOIN		(' .
						'					SELECT		Poste, ' . $joueur . ' AS Joueurs_Joueur' .
						'					FROM		postes' .
						'					WHERE		Poste = ' . $poste .
						'				) postes' .
						'				ON		Joueur = Joueurs_Joueur' .
						'	SET			Postes_Poste = postes.Poste' .
						'	WHERE		Joueur = ' . $joueur;
	$bdd->exec($ordreSQL);

?>