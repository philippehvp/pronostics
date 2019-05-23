<?php
	include_once('commun_administrateur.php');

	// Sauvegarde de la liste des participants à un match
	
	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
	
	// Effacement d'une éventuelle liste précédente
	$ordreSQL =		'DELETE FROM	matches_participants ';
	$ordreSQL .=	'WHERE			Matches_Match = ' . $match;
	$ordreSQL .=	'				AND		Equipes_Equipe = ' . $equipe;

	$bdd->exec($ordreSQL);
	
	// Création de nouvelles lignes dans la table des participants
	$joueurs = isset($_POST["joueurs"]) ? $_POST["joueurs"] : 0;

	for($i = 0; $i < $joueurs; $i++) {
		$nomParametre = 'joueur' . $i;
		$joueur = isset($_POST[$nomParametre]) ? $_POST[$nomParametre] : 0;
		if($joueur != 0) {
			$ordreSQL =		'INSERT INTO		matches_participants(Matches_Match, Joueurs_Joueur, Equipes_Equipe) ';
			$ordreSQL .=	'VALUES(' . $match . ', ' . $joueur . ', ' . $equipe . ')';
			$bdd->exec($ordreSQL);
		}
	}
	
	// On indique qu'une modification a eu lieu au niveau du match
	$ordreSQL =		'	UPDATE		matches' .
					'	SET			Matches_DateMAJ = NOW()' .
					'	WHERE		matches.Match = ' . $match;
	$bdd->exec($ordreSQL);

?>