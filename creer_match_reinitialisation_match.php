<?php
	include('commun_administrateur.php');

	// Réinitialisation d'un match
	
	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	
	if($match) {
		$ordreSQL =		'	UPDATE		matches' .
						'	SET			Matches_ScoreEquipeDomicile = NULL' .
						'				,Matches_ScoreEquipeVisiteur = NULL' .
						'				,Matches_ScoreAPEquipeDomicile = NULL' .
						'				,Matches_ScoreAPEquipeVisiteur = NULL' .
						'				,Matches_Vainqueur = NULL' .
						'				,Matches_CompositionLue = 0' .
						'	WHERE		matches.Match = ' . $match;
		$bdd->exec($ordreSQL);
		
		$ordreSQL =		'	DELETE' .
						'	FROM		matches_participants' .
						'	WHERE		Matches_Match = ' . $match;
		$bdd->exec($ordreSQL);
		
		$ordreSQL =		'	DELETE' .
						'	FROM		matches_buteurs' .
						'	WHERE		Matches_Match = ' . $match;
		$bdd->exec($ordreSQL);
		
		$ordreSQL =		'	UPDATE		scores' .
						'	SET			Scores_ScoreMatch = NULL' .
						'				,Scores_ScoreButeur = NULL' .
						'				,Scores_ScoreBonus = NULL' .
						'				,Scores_ScoreQualification = NULL' .
						'				,Scores_ScoreCarreFinalCoefficient = NULL' .
						'	WHERE		Matches_Match = ' . $match;
		$bdd->exec($ordreSQL);
	}
?>