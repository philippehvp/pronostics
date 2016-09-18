<?php
	include('commun.php');
	// Page d'affichage des statistiques d'un pronostiqueur

	$pronostiqueur = isset($_POST["pronostiqueur"]) ? $_POST["pronostiqueur"] : 0;
	
	// Lecture du numéro de journée en cours et de la phase en cours (phase de poule ou phase finale)
	$ordreSQL =		'	SELECT		cdm_fn_journee_en_cours() AS Journee_EnCours';
	$req = $bdd->query($ordreSQL);
	while($donnees = $req->fetch()) {
		$journeeEnCours = $donnees["Journee_EnCours"];
	}
	$req->closeCursor();
	
	$ordreSQL =		'	SELECT		IFNULL(poules.Scores_ScoreMatch, 0) AS Scores_Poule' .
					'				,IFNULL(BonusClassements_Points, 0) AS BonusClassements_Points' .
					'				,IFNULL(BonusSorties_Points, 0) AS BonusSorties_Points' .
					'				,IFNULL(phase_finale.Scores_ScoreMatch, 0) AS Scores_PhaseFinale' .
					'				,IFNULL(Scores_ScoreBonus, 0) AS Scores_ScoreBonus' .
					'				,Bonus_Podium' .
					'				,Bonus_Buteur' .
					'	FROM		(	SELECT		Pronostiqueurs_Pronostiqueur, SUM(IFNULL(Scores_ScoreMatch, 0)) AS Scores_ScoreMatch' .
					'					FROM		cdm_scores' .
					'					JOIN		cdm_matches_poule' .
					'								ON		cdm_scores.Matches_Match = cdm_matches_poule.Match' .
					'					WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
					'								AND		Scores_Phase = 1' .
					'								AND		Matches_JourneeEnCours <= ' . $journeeEnCours .
					'					GROUP BY	Pronostiqueurs_Pronostiqueur' .
					'				) poules' .
					'	LEFT JOIN	(	SELECT		Pronostiqueurs_Pronostiqueur' .
					'								,SUM(IFNULL(BonusClassements_Points, 0)) AS BonusClassements_Points' .
					'					FROM		cdm_bonus_classements' .
					'					JOIN		(' .
					'									SELECT		DISTINCT' .
					'												CASE' .
					'													WHEN	' . $journeeEnCours . ' = 12 AND Poules_Poule IN (1, 2)' .
					'													THEN	Poules_Poule' .
					'													WHEN	' . $journeeEnCours . ' = 13 AND Poules_Poule IN (1, 2, 3, 4)' .
					'													THEN	Poules_Poule' .
					'													WHEN	' . $journeeEnCours . ' = 14 AND Poules_Poule IN (1, 2, 3, 4, 5, 6)' .
					'													THEN	Poules_Poule' .
					'													WHEN	' . $journeeEnCours . ' = 15 AND Poules_Poule IN (1, 2, 3, 4, 5, 6, 7, 8)' .
					'													THEN	Poules_Poule' .
					'													WHEN	' . $journeeEnCours . ' >= 16' .
					'													THEN	Poules_Poule' .
					'													ELSE	NULL' .
					'												END AS Poules_Poule' .
					'									FROM		cdm_matches_poule matches' .
					'									JOIN		cdm_pronostics_poule resultatsA' .
					'												ON		matches.Match = resultatsA.Matches_Match' .
					'														AND		matches.Equipes_EquipeA = resultatsA.Equipes_Equipe' .
					'									JOIN		cdm_pronostics_poule resultatsB' .
					'												ON		matches.Match = resultatsB.Matches_Match' .
					'														AND		matches.Equipes_EquipeB = resultatsB.Equipes_Equipe' .
					'									WHERE		resultatsA.Pronostiqueurs_Pronostiqueur = 1' .
					'												AND		resultatsB.Pronostiqueurs_Pronostiqueur = 1' .
					'												AND		(	resultatsA.PronosticsPoule_Score IS NOT NULL' .
					'															OR		resultatsB.PronosticsPoule_Score IS NOT NULL' .
					'														)' .
					'									GROUP BY	Poules_Poule' .
					'									HAVING		COUNT(*) = 6' .
					'								) poules_completes' .
					'								ON		cdm_bonus_classements.Poules_Poule = poules_completes.Poules_Poule' .
					'					WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
					'					GROUP BY	Pronostiqueurs_Pronostiqueur' .
					'				) cdm_bonus_classements' .
					'				ON		poules.Pronostiqueurs_Pronostiqueur = cdm_bonus_classements.Pronostiqueurs_Pronostiqueur' .
					'	LEFT JOIN	(	SELECT		Pronostiqueurs_Pronostiqueur, SUM(IFNULL(BonusSorties_Points, 0)) AS BonusSorties_Points' .
					'					FROM		cdm_bonus_sorties' .
					'					JOIN		(' .
					'									SELECT		DISTINCT' .
					'												CASE' .
					'													WHEN	' . $journeeEnCours . ' = 12 AND Poules_Poule IN (1, 2)' .
					'													THEN	Poules_Poule' .
					'													WHEN	' . $journeeEnCours . ' = 13 AND Poules_Poule IN (1, 2, 3, 4)' .
					'													THEN	Poules_Poule' .
					'													WHEN	' . $journeeEnCours . ' = 14 AND Poules_Poule IN (1, 2, 3, 4, 5, 6)' .
					'													THEN	Poules_Poule' .
					'													WHEN	' . $journeeEnCours . ' = 15 AND Poules_Poule IN (1, 2, 3, 4, 5, 6, 7, 8)' .
					'													THEN	Poules_Poule' .
					'													WHEN	' . $journeeEnCours . ' >= 16' .
					'													THEN	Poules_Poule' .
					'													ELSE	NULL' .
					'												END AS Poules_Poule' .
					'									FROM		cdm_matches_poule matches' .
					'									JOIN		cdm_pronostics_poule resultatsA' .
					'												ON		matches.Match = resultatsA.Matches_Match' .
					'														AND		matches.Equipes_EquipeA = resultatsA.Equipes_Equipe' .
					'									JOIN		cdm_pronostics_poule resultatsB' .
					'												ON		matches.Match = resultatsB.Matches_Match' .
					'														AND		matches.Equipes_EquipeB = resultatsB.Equipes_Equipe' .
					'									WHERE		resultatsA.Pronostiqueurs_Pronostiqueur = 1' .
					'												AND		resultatsB.Pronostiqueurs_Pronostiqueur = 1' .
					'												AND		(	resultatsA.PronosticsPoule_Score IS NOT NULL' .
					'															OR		resultatsB.PronosticsPoule_Score IS NOT NULL' .
					'														)' .
					'									GROUP BY	Poules_Poule' .
					'									HAVING		COUNT(*) = 6' .
					'								) poules_completes' .
					'								ON		cdm_bonus_sorties.Poules_Poule = poules_completes.Poules_Poule' .
					'					WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
					'					GROUP BY	Pronostiqueurs_Pronostiqueur' .
					'				) cdm_bonus_sorties' .
					'				ON		poules.Pronostiqueurs_Pronostiqueur = cdm_bonus_sorties.Pronostiqueurs_Pronostiqueur' .
					'	LEFT JOIN	(' .
					'					SELECT		cdm_scores.Pronostiqueurs_Pronostiqueur' .
					'								,SUM(IFNULL(Scores_ScoreMatch, 0) * Scores_Coefficient) AS Scores_ScoreMatch' .
					'								,SUM(IFNULL(Scores_ScoreBonus, 0)) AS Scores_ScoreBonus' .
					'					FROM		cdm_matches_phase_finale' .
					'					LEFT JOIN	(' .
					'									SELECT		cdm_scores.Pronostiqueurs_Pronostiqueur' .
					'												,IFNULL(cdm_affiches_inversees.Pronostiqueurs_Matches_Match, cdm_scores.Matches_Match) AS Matches_Match' .
					'												,Scores_ScoreMatch, Scores_ScoreBonus, Scores_Coefficient' .
					'												,IF(cdm_affiches_inversees.Pronostiqueurs_Matches_Match IS NOT NULL, 1, 0) AS Affiche_Inversee' .
					'									FROM		cdm_scores' .
					'									LEFT JOIN	cdm_affiches_inversees' .
					'												ON		cdm_scores.Matches_Match = cdm_affiches_inversees.Pronostiqueurs_Matches_Match' .
					'														AND		cdm_scores.Pronostiqueurs_Pronostiqueur = cdm_affiches_inversees.Pronostiqueurs_Pronostiqueur' .
					'									WHERE		cdm_scores.Scores_Phase = 2' .
					'												AND		cdm_scores.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
					'								) cdm_scores' .
					'								ON		cdm_matches_phase_finale.Match = cdm_scores.Matches_Match' .
					'					WHERE		Matches_JourneeEnCours <= ' . $journeeEnCours .
					'					GROUP BY	cdm_scores.Pronostiqueurs_Pronostiqueur' .
					'				) phase_finale' .
					'				ON		poules.Pronostiqueurs_Pronostiqueur = phase_finale.Pronostiqueurs_Pronostiqueur' .
					'	LEFT JOIN	(' .
					'					SELECT		Pronostiqueurs_Pronostiqueur' .
					'								,CASE' .
					'									WHEN	25 = ' . $journeeEnCours .
					'									THEN	IFNULL(Bonus_Vainqueur, 0) + IFNULL(Bonus_Deuxieme, 0) + IFNULL(Bonus_Troisieme, 0) + IFNULL(Bonus_Buteur, 0)' .
					'									ELSE	0' .
					'								END AS Bonus_Podium' .
					'					FROM		cdm_bonus' .
					'					WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
					'				) cdm_bonus' .
					'				ON		poules.Pronostiqueurs_Pronostiqueur = cdm_bonus.Pronostiqueurs_Pronostiqueur' .
					'	LEFT JOIN	(' .
					'					SELECT		Pronostiqueurs_Pronostiqueur' .
					'								,CASE' .
					'									WHEN	25 = ' . $journeeEnCours .
					'									THEN	IFNULL(Bonus_Buteur, 0)' .
					'									ELSE	0' .
					'								END AS Bonus_Buteur' .
					'					FROM		cdm_bonus' .
					'					WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
					'				) cdm_bonus_buteur' .
					'				ON		poules.Pronostiqueurs_Pronostiqueur = cdm_bonus_buteur.Pronostiqueurs_Pronostiqueur' .
					'	WHERE		poules.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur;

	$req = $bdd->query($ordreSQL);
	if($req == null)
		return;
	$statsPoints = $req->fetchAll();
	
	if(sizeof($statsPoints)) {
		echo '<table id="tblPoints">';
			echo '<thead>';
				echo '<tr>';
					echo '<th>Type de points</th>';
					echo '<th>Points</th>';
				echo '</tr>';
			echo '</thead>';
			
			echo '<tbody>';
				foreach($statsPoints as $uneStat) {
					echo '<tr>';
						echo '<td class="bordure-droite">Poule</td>';
						echo '<td>' . $uneStat["Scores_Poule"] . '</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td class="bordure-droite">Classements</td>';
						echo '<td>' . $uneStat["BonusClassements_Points"] . '</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td class="bordure-droite">Surprises</td>';
						echo '<td>' . $uneStat["BonusSorties_Points"] . '</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td class="bordure-droite">Phase finale</td>';
						echo '<td>' . $uneStat["Scores_PhaseFinale"] . '</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td class="bordure-droite">Bonus</td>';
						echo '<td>' . $uneStat["Scores_ScoreBonus"] . '</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td class="bordure-droite">Podium</td>';
						echo '<td>' . $uneStat["Bonus_Podium"] . '</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td class="bordure-droite">Buteur</td>';
						echo '<td>' . $uneStat["Bonus_Buteur"] . '</td>';
					echo '</tr>';
				}
			echo '</tbody>';
		echo '</table>';
	}
?>