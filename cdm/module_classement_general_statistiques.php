<?php
	include('commun.php');
	// Page d'affichage des statistiques d'un pronostiqueur

	$pronostiqueur = isset($_POST["pronostiqueur"]) ? $_POST["pronostiqueur"] : 0;
	
	$ordreSQL =		'	SELECT		Libelle' .
					'				,IFNULL(scores_match_reels.Nombre, 0) AS Nombre' .
					'	FROM		(' .
					'					SELECT		10 AS Scores_ScoreMatch, \'Résultat exact\' AS Libelle																UNION ALL' .
					'					SELECT		8 AS Scores_ScoreMatch, \'Vainqueur et son score exact\' AS Libelle													UNION ALL' .
					'					SELECT		7 AS Scores_ScoreMatch, \'Vainqueur et bonne différence de buts ou match nul sans le bon score\' AS Libelle			UNION ALL' .
					'					SELECT		6 AS Scores_ScoreMatch, \'Vainqueur et score exact du perdant\' AS Libelle											UNION ALL' .
					'					SELECT		5 AS Scores_ScoreMatch, \'Vainqueur uniquement\' AS Libelle															UNION ALL' .
					'					SELECT		3 AS Scores_ScoreMatch, \'Score exact du vainqueur\' AS Libelle														UNION ALL' .
					'					SELECT		2 AS Scores_ScoreMatch, \'Pronostic de victoire mais match nul au final avec un seul score exact\' AS Libelle		UNION ALL' .
					'					SELECT		1 AS Scores_ScoreMatch, \'Score exact du perdant\' AS Libelle														UNION ALL' .
					'					SELECT		0 AS Scores_ScoreMatch, \'Tout faux\' AS Libelle' .
					'				) scores_match_potentiels' .
					'	LEFT JOIN	(' .
					'					SELECT		Scores_ScoreMatch' .
					'								,COUNT(*) AS Nombre' .
					'					FROM		cdm_scores' .
					'					JOIN		(' .
					'									SELECT		cdm_matches_poule.Match, 1 AS Scores_Phase' .
					'									FROM		cdm_matches_poule' .
					'									JOIN		cdm_pronostics_poule equipesA' .
					'												ON			cdm_matches_poule.Match = equipesA.Matches_Match' .
					'												AND		cdm_matches_poule.Equipes_EquipeA = equipesA.Equipes_Equipe' .
					'									JOIN		cdm_pronostics_poule equipesB' .
					'												ON		cdm_matches_poule.Match = equipesB.Matches_Match' .
					'												AND		cdm_matches_poule.Equipes_EquipeB = equipesB.Equipes_Equipe' .
					'									WHERE		equipesA.Pronostiqueurs_Pronostiqueur = 1' .
					'												AND		equipesB.Pronostiqueurs_Pronostiqueur = 1' .
					'												AND		equipesA.PronosticsPoule_Score IS NOT NULL' .
					'												AND		equipesB.PronosticsPoule_Score IS NOT NULL' .
					'									UNION ALL' .
					'									SELECT		cdm_matches_phase_finale.Match, 2 AS Scores_Phase' .
					'									FROM		cdm_matches_phase_finale' .
					'									JOIN		cdm_pronostics_phase_finale' .
					'												ON			cdm_matches_phase_finale.Match = cdm_pronostics_phase_finale.Matches_Match' .
					'									WHERE		cdm_pronostics_phase_finale.Pronostiqueurs_Pronostiqueur = 1' .
					'												AND		cdm_pronostics_phase_finale.Pronostics_ScoreEquipeA IS NOT NULL' .
					'												AND		cdm_pronostics_phase_finale.Pronostics_ScoreEquipeB IS NOT NULL' .
					'								) cdm_matches' .
					'								ON		cdm_scores.Matches_Match = cdm_matches.Match' .
					'										AND		cdm_scores.Scores_Phase = cdm_matches.Scores_Phase' .
					'					JOIN		cdm_pronostiqueurs' .
					'								ON		cdm_scores.Pronostiqueurs_Pronostiqueur = cdm_pronostiqueurs.Pronostiqueur' .
					'					WHERE		Pronostiqueur = ' . $pronostiqueur .
					'					GROUP BY	Scores_ScoreMatch' .
					'				) scores_match_reels' .
					'				ON			scores_match_potentiels.Scores_ScoreMatch = scores_match_reels.Scores_ScoreMatch' .
					'	ORDER BY	scores_match_potentiels.Scores_ScoreMatch DESC';

	$req = $bdd->query($ordreSQL);
	if($req == null)
		return;
	$statsMatches = $req->fetchAll();
	
	if(sizeof($statsMatches)) {
		echo '<table id="tblStatistiques">';
			echo '<thead>';
				echo '<tr>';
					echo '<th class="resultat">Type de résultat</th>';
					echo '<th class="nombre">Nombre</th>';
				echo '</tr>';
			echo '</thead>';
			
			echo '<tbody>';
				foreach($statsMatches as $uneStat) {
					echo '<tr>';
						echo '<td class="resultat bordure-droite">' . $uneStat["Libelle"] . '</td>';
						echo '<td class="nombre">' . $uneStat["Nombre"] . '</td>';
					echo '</tr>';
				}
			echo '</tbody>';
		echo '</table>';
	}
?>