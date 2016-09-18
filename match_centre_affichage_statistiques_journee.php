<?php


	// La page peut être appelée de deux manières :
	// - par une inclusion
	// - par un appel Ajax (cas du rafraîchissement)

	$rafraichissementSection = isset($_POST["rafraichissementSection"]) ? $_POST["rafraichissementSection"] : 0;
	if($rafraichissementSection == 1) {
		// Rafraîchissement automatique de la section
		include('commun.php');
		
		// Lecture des paramètres passés à la page
		$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;
	}

	// Nombre de points théoriques
	// Le coefficient de multiplication est le suivant :
	// - pour tous les matches de ligue 1, 1 sauf le match Canal
	// - pour tous les matches des coupes européennes, 1 sauf 2 pour les demi-finales et 3 pour la finale
	$ordreSQL =		'	SELECT		Scores_Match + Scores_Bonus + Scores_Buteur AS Scores_Match, Scores_Buteur' .
					'	FROM		(' .
					'					SELECT		Journees_Journee, SUM(IFNULL(Scores_Match, 0)) AS Scores_Match, SUM(IFNULL(Scores_Bonus, 0)) AS Scores_Bonus' .
					'					FROM		(' .
					'									SELECT		matches.Journees_Journee' .
					'												,CASE' .
					'													WHEN	matches.Matches_DemiFinaleEuropeenne = 1' .
					'													THEN	2' .
					'													WHEN	matches.Matches_FinaleEuropeenne = 1' .
					'													THEN	3' .
					'													ELSE	1' .
					'												END * fn_calculscorematch(' .
					'													CASE' .
					'														WHEN	matches.Matches_MatchCS = 1' .
					'														THEN	5' .
					'														WHEN	matches.Matches_AvecProlongation = 0 AND IFNULL(matches.Matches_MatchLie, 0) = 0' .
					'														THEN	1' .
					'														WHEN	matches.Matches_AvecProlongation = 0 AND IFNULL(matches.Matches_MatchLie, 0) <> 0' .
					'														THEN	2' .
					'														WHEN	matches.Matches_AvecProlongation = 1 AND IFNULL(matches.Matches_MatchLie, 0) <> 0' .
					'														THEN	3' .
					'														ELSE	4' .
					'													END' .
					'													,matches.Matches_ScoreEquipeDomicile' .
					'													,matches.Matches_ScoreEquipeVisiteur' .
					'													,matches.Matches_ScoreAPEquipeDomicile' .
					'													,matches.Matches_ScoreAPEquipeVisiteur' .
					'													,matches.Matches_AvecProlongation' .
					'													,matches.Matches_Vainqueur' .
					'													,matches.Matches_MatchCS' .
					'													,matches.Matches_MatchLie' .
					'													,matches.Matches_CoteEquipeDomicile' .
					'													,matches.Matches_CoteNul' .
					'													,matches.Matches_CoteEquipeVisiteur' .
					'													,matches.Matches_Coefficient' .
					'													/* Partie normalement réservée aux pronostics */' .
					'													,matches.Matches_ScoreEquipeDomicile' .
					'													,matches.Matches_ScoreEquipeVisiteur' .
					'													,matches.Matches_ScoreAPEquipeDomicile' .
					'													,matches.Matches_ScoreAPEquipeVisiteur' .
					'													,matches.Matches_Vainqueur' .
					'												) AS Scores_Match' .
					'												,CASE' .
					'													WHEN	matches.Matches_DemiFinaleEuropeenne = 1' .
					'													THEN	2' .
					'													WHEN	matches.Matches_FinaleEuropeenne = 1' .
					'													THEN	3' .
					'													ELSE	1' .
					'												END * fn_calculscorebonus(' .
					'													matches.Match' .
					'													,CASE' .
					'														WHEN	matches.Matches_MatchCS = 1' .
					'														THEN	5' .
					'														WHEN	matches.Matches_AvecProlongation = 0 AND IFNULL(matches.Matches_MatchLie, 0) = 0' .
					'														THEN	1' .
					'														WHEN	matches.Matches_AvecProlongation = 0 AND IFNULL(matches.Matches_MatchLie, 0) <> 0' .
					'														THEN	2' .
					'														WHEN	matches.Matches_AvecProlongation = 1 AND IFNULL(matches.Matches_MatchLie, 0) <> 0' .
					'														THEN	3' .
					'														ELSE	4' .
					'													END' .
					'													,matches.Matches_ScoreEquipeDomicile' .
					'													,matches.Matches_ScoreEquipeVisiteur' .
					'													,matches.Matches_ScoreAPEquipeDomicile' .
					'													,matches.Matches_ScoreAPEquipeVisiteur' .
					'													,matches.Matches_AvecProlongation' .
					'													,matches.Matches_Vainqueur' .
					'													,matches.Matches_MatchCS' .
					'													,matches.Matches_MatchLie' .
					'													,matches.Matches_CoteEquipeDomicile' .
					'													,matches.Matches_CoteNul' .
					'													,matches.Matches_CoteEquipeVisiteur' .
					'													,matches.Matches_Coefficient' .
					'													,matches.Matches_PointsQualificationEquipeDomicile' .
					'													,matches.Matches_PointsQualificationEquipeVisiteur' .
					'													,matches.Matches_FinaleEuropeenne' .
					'													/* Partie normalement réservée aux pronostics */' .
					'													,matches.Matches_ScoreEquipeDomicile' .
					'													,matches.Matches_ScoreEquipeVisiteur' .
					'													,matches.Matches_ScoreAPEquipeDomicile' .
					'													,matches.Matches_ScoreAPEquipeVisiteur' .
					'													,matches.Matches_Vainqueur' .
					'													,matchesAller.Matches_ScoreEquipeDomicile' .
					'													,matchesAller.Matches_ScoreEquipeVisiteur' .
					'													,matchesAller.Matches_ScoreEquipeDomicile' .
					'													,matchesAller.Matches_ScoreEquipeVisiteur' .
					'													,0' .
					'												) AS Scores_Bonus' .
					'									FROM		matches' .
					'									JOIN		journees' .
					'												ON		matches.Journees_Journee = journees.Journee' .
					'									LEFT JOIN	matches matchesAller' .
					'												ON		matches.Matches_MatchLie = matchesAller.Match' .
					'									WHERE		journees.Journee = ' . $journee .
					'												AND		matches.Matches_ScoreEquipeDomicile IS NOT NULL' .
					'												AND		matches.Matches_ScoreEquipeVisiteur IS NOT NULL' .
					'								) scores' .
					'					GROUP BY	Journees_Journee' .
					'				) scores' .
					'	JOIN		(' .
					'					SELECT		Journees_Journee, SUM(Scores_Buteur) AS Scores_Buteur' .
					'					FROM		(' .
					'									SELECT		Journees_Journee' .
					'												,fn_calculcotebuteur(Buteurs_Cote) * matches.Matches_Coefficient AS Scores_Buteur' .
					'									FROM		matches_buteurs' .
					'									JOIN		matches' .
					'												ON		matches_buteurs.Matches_Match = matches.Match' .
					'									JOIN		journees' .
					'												ON		matches.Journees_Journee = journees.Journee' .
					'									WHERE		Buteurs_CSC = 0' .
					'												AND		journees.Journee = ' . $journee .
					'												AND		matches.Matches_ScoreEquipeDomicile IS NOT NULL' .
					'												AND		matches.Matches_ScoreEquipeVisiteur IS NOT NULL' .
					'								) scores_buteur' .
					'					GROUP BY	Journees_Journee' .
					'				) scores_buteur' .
					'				ON		scores.Journees_Journee = scores_buteur.Journees_Journee' .
					'	ORDER BY	scores.Journees_Journee DESC';
	$req = $bdd->query($ordreSQL);
	$pointsTheoriques = $req->fetchAll();
	
	
	//echo $pointsTheoriques[0]["Scores_Match"] . ' (' . $pointsTheoriques[0]["Scores_Buteur"] .')';
?>