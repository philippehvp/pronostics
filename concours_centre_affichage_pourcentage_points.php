<?php
	include('commun.php');
	
	// Affichage des pourcentages de points marqués par journée
	
	// Lecture des paramètres passés à la page
	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;
	
	// Lecture du nombre de points théoriques
	$ordreSQL =		'	SELECT		Scores_Match + Scores_Bonus + Scores_Buteur AS Scores_Match, Scores_Buteur' .
					'	FROM		(' .
					'					SELECT		Journees_Journee, SUM(Scores_Match) AS Scores_Match, SUM(Scores_Bonus) AS Scores_Bonus' .
					'					FROM		(' .
					'									SELECT		matches.Journees_Journee' .
					'												,fn_calculscorematch(' .
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
					'												,fn_calculscorebonus(' .
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
					'													,MatchesAller.Matches_ScoreEquipeDomicile' .
					'													,MatchesAller.Matches_ScoreEquipeVisiteur' .
					'													,MatchesAller.Matches_ScoreEquipeDomicile' .
					'													,MatchesAller.Matches_ScoreEquipeVisiteur' .
					'													,0' .
					'												) AS Scores_Bonus' .
					'									FROM		matches' .
					'									JOIN		journees' .
					'												ON		matches.Journees_Journee = journees.Journee' .
					'									LEFT JOIN	matches MatchesAller' .
					'												ON		matches.Matches_MatchLie = MatchesAller.Match' .
					'									WHERE		journees.Championnats_Championnat = ' . $championnat .
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
					'												AND		journees.Championnats_Championnat = ' . $championnat .
					'												AND		matches.Matches_ScoreEquipeDomicile IS NOT NULL' .
					'												AND		matches.Matches_ScoreEquipeVisiteur IS NOT NULL' .
					'								) scores_buteur' .
					'					GROUP BY	Journees_Journee' .
					'				) scores_buteur' .
					'				ON		scores.Journees_Journee = scores_buteur.Journees_Journee' .
					'	ORDER BY	scores.Journees_Journee DESC';
	$req = $bdd->query($ordreSQL);
	$pointsTheoriques = $req->fetchAll();
	$nombreJournees = sizeof($pointsTheoriques);
	
	// Scores match et scores buteur pour chaque pronostiqueur
	$ordreSQL =		'	SELECT		pronostiqueurs.Pronostiqueur, Pronostiqueurs_NomUtilisateur, classements.Journees_Journee, Classements_PointsJourneeMatch, Classements_PointsJourneeButeur' .
					'				,CASE' .
					'					WHEN		pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur IS NOT NULL' .
					'					THEN		1' .
					'					ELSE		0' .
					'				END AS Pronostiqueurs_Rival' .
					'	FROM		pronostiqueurs' .
					'	JOIN		inscriptions' .
					'				ON		pronostiqueurs.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
					'	JOIN		(' .
					'					SELECT		Pronostiqueurs_Pronostiqueur, Journees_Journee, MAX(Classements_DateReference) AS Classements_DateReference, Classements_PointsJourneeMatch, Classements_PointsJourneeButeur' .
					'					FROM		classements' .
					'					GROUP BY	Pronostiqueurs_Pronostiqueur, Journees_Journee' .
					'				) classements' .
					'				ON		pronostiqueurs.Pronostiqueur = classements.Pronostiqueurs_Pronostiqueur' .
					'	JOIN		journees' .
					'				ON		classements.Journees_Journee = journees.Journee' .
					'						AND		inscriptions.Championnats_Championnat = journees.Championnats_Championnat' .
					'	LEFT JOIN	pronostiqueurs_rivaux' .
					'				ON		pronostiqueurs_rivaux.Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'						AND		pronostiqueurs.Pronostiqueur = pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur' .
					'	WHERE		inscriptions.Championnats_Championnat = ' . $championnat .
					'				AND		classements.Classements_PointsJourneeMatch IS NOT NULL' .
					'	ORDER BY	Pronostiqueurs_NomUtilisateur, classements.Journees_Journee DESC';

	$req = $bdd->query($ordreSQL);
	$resultats = $req->fetchAll();
	$nombrePronostiqueurs = sizeof($resultats) / $nombreJournees;

	if($nombreJournees > 0) {
		echo '<div class="cc--pourcentage-points">';
			echo '<table class="cc--tableau">';
				echo '<thead>';
					echo '<tr>';
						echo '<th>&nbsp;</th>';
						echo '<th>&nbsp;</th>';
						for($i = 0; $i < $nombreJournees; $i++)
							echo '<th>&nbsp;</th><th>J' . ($nombreJournees - $i) . '</th><th>&nbsp;</th>';
					echo '</tr>';
					echo '<tr>';
						echo '<th>&nbsp;</th>';
						echo '<th>Points match</th>';
						for($i = 0; $i < $nombreJournees; $i++)
							echo '<th>&nbsp;</th><th>' . $pointsTheoriques[$i]["Scores_Match"] . '</th><th>&nbsp;</th>';
					echo '</tr>';
					echo '<tr>';
						echo '<th>&nbsp;</th>';
						echo '<th>Points buteur</th>';
						for($i = 0; $i < $nombreJournees; $i++)
							echo '<th>&nbsp;</th><th>' . $pointsTheoriques[$i]["Scores_Buteur"] . '</th><th>&nbsp;</th>';
					echo '</tr>';
					echo '<tr>';
						echo '<th>Rang (indicatif)</th>';
						echo '<th>Joueurs</th>';
						for($i = 0; $i < $nombreJournees; $i++)
							echo '<th>Points</th><th>Ratio match</th><th>Ratio buteur</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
					// Le nombre de lignes dans le tableau est la multiplication entre le nombre de pronostiqueurs et le nombre de journées
					for($i = 0; $i < $nombrePronostiqueurs; $i++) {
						for($j = 0; $j < $nombreJournees; $j++) {
							// Affichage de l'en-tête lorsque l'on arrive sur un nouveau pronostiqueur
							if($j == 0) {
								if($resultats[$i * $nombreJournees]["Pronostiqueur"] == $_SESSION["pronostiqueur"])			echo '<tr class="surbrillance">';
								else if($resultats[$i * $nombreJournees]["Pronostiqueurs_Rival"] == 1)						echo '<tr class="rival">';
								else																						echo '<tr>';
									echo '<td></td>';
									echo '<td>' . $resultats[$i * $nombreJournees]["Pronostiqueurs_NomUtilisateur"] . '</td>';
									
							}
								// Affichage des données des journées
								echo '<td>' . $resultats[$i * $nombreJournees + $j]["Classements_PointsJourneeMatch"] . '</td>';
								echo '<td>' . number_format($resultats[$i * $nombreJournees + $j]["Classements_PointsJourneeMatch"] / $pointsTheoriques[$j]["Scores_Match"] * 100, 2) . '%</td>';
								echo '<td>' . number_format($resultats[$i * $nombreJournees + $j]["Classements_PointsJourneeButeur"] / $pointsTheoriques[$j]["Scores_Buteur"] * 100, 2) . '%</td>';
						}
						echo '</tr>';
					}
				echo '</tbody>';
			echo '</table>';
		echo '</div>';
	}
	else {
		echo '<label>Aucune donnée à afficher</label>';
	}


?>
