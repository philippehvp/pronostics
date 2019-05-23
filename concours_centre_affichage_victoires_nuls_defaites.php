<?php
	include_once('commun.php');
	
	// Affichage des statistiques de Ligue 1
	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;
	
	// Nombre de buteurs pronostiqués et trouvés
	$ordreSQL =		'	SELECT		pronostiqueurs.Pronostiqueur, Pronostiqueurs_NomUtilisateur' .
					'				,Nombre_Victoires_Reelles / Nombre_Victoires_Pronostiquees * 100 AS Ratio_Victoires, Nombre_Victoires_Reelles, Nombre_Victoires_Pronostiquees' .
					'				,Nombre_Nuls_Reels / Nombre_Nuls_Pronostiques * 100 AS Ratio_Nuls, Nombre_Nuls_Reels, Nombre_Nuls_Pronostiques' .
					'				,Nombre_Defaites_Reelles / Nombre_Defaites_Pronostiquees * 100 AS Ratio_Defaites, Nombre_Defaites_Reelles, Nombre_Defaites_Pronostiquees' .
					'				,CASE' .
					'					WHEN		pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur IS NOT NULL' .
					'					THEN		1' .
					'					ELSE		0' .
					'				END AS Pronostiqueurs_Rival' .
					'	FROM		(' .
					'					SELECT		pronostics.Pronostiqueurs_Pronostiqueur, SUM(pronostics.Victoires_Pronostiquees) AS Nombre_Victoires_Pronostiquees, SUM(Victoires_Reelles) AS Nombre_Victoires_Reelles' .
					'					FROM		(' .
					'									SELECT		Pronostiqueurs_Pronostiqueur, Matches_Match, 1 AS Victoires_Pronostiquees' .
					'												,CASE' .
					'													WHEN	(	Matches_ScoreEquipeDomicile > Matches_ScoreEquipeVisiteur' .
					'																OR		Matches_ScoreAPEquipeDomicile > Matches_ScoreAPEquipeVisiteur' .
					'																OR		Matches_Vainqueur = 1' .
					'															)' .
					'													THEN	1' .
					'													ELSE	0' .
					'												END AS Victoires_Reelles' .
					'									FROM		pronostics' .
					'									JOIN		matches' .
					'												ON		pronostics.Matches_Match = matches.Match' .
					'									JOIN		journees' .
					'												ON		matches.Journees_Journee = journees.Journee' .
					'									WHERE		journees.Championnats_Championnat = ' . $championnat .
					'												AND		Pronostics_ScoreEquipeDomicile IS NOT NULL' .
					'												AND		Pronostics_ScoreEquipeVisiteur IS NOT NULL' .
					'												AND		(	Pronostics_ScoreEquipeDomicile > Pronostics_ScoreEquipeVisiteur' .
					'															OR		Pronostics_ScoreAPEquipeDomicile > Pronostics_ScoreAPEquipeVisiteur' .
					'															OR		Pronostics_Vainqueur = 1' .
					'														)' .
					'								) pronostics' .
					'					GROUP BY	pronostics.Pronostiqueurs_Pronostiqueur' .
					'				) pronostics_victoire' .
					'	JOIN		(' .
					'					SELECT		pronostics.Pronostiqueurs_Pronostiqueur, SUM(pronostics.Nuls_Pronostiques) AS Nombre_Nuls_Pronostiques, SUM(Nuls_Reels) AS Nombre_Nuls_Reels' .
					'					FROM		(' .
					'									SELECT		Pronostiqueurs_Pronostiqueur, Matches_Match, 1 AS Nuls_Pronostiques' .
					'												,CASE' .
					'													WHEN	(	Matches_ScoreEquipeDomicile = Matches_ScoreEquipeVisiteur' .
					'																AND		Matches_ScoreAPEquipeDomicile IS NULL' .
					'																AND		Matches_ScoreAPEquipeVisiteur IS NULL' .
					'																AND		Matches_Vainqueur IS NULL' .
					'															)' .
					'													THEN	1' .
					'													ELSE	0' .
					'												END AS Nuls_Reels' .
					'									FROM		pronostics' .
					'									JOIN		matches' .
					'												ON		pronostics.Matches_Match = matches.Match' .
					'									JOIN		journees' .
					'												ON		matches.Journees_Journee = journees.Journee' .
					'									WHERE		journees.Championnats_Championnat = ' . $championnat .
					'												AND		Pronostics_ScoreEquipeDomicile IS NOT NULL' .
					'												AND		Pronostics_ScoreEquipeVisiteur IS NOT NULL' .
					'												AND		(	Pronostics_ScoreEquipeDomicile = Pronostics_ScoreEquipeVisiteur' .
					'															AND		Pronostics_ScoreAPEquipeDomicile IS NULL' .
					'															AND		Pronostics_ScoreAPEquipeVisiteur IS NULL' .
					'															AND		Pronostics_Vainqueur IS NULL' .
					'														)' .
					'								) pronostics' .
					'					GROUP BY	pronostics.Pronostiqueurs_Pronostiqueur' .
					'				) pronostics_nul' .
					'				ON		pronostics_victoire.Pronostiqueurs_Pronostiqueur = pronostics_nul.Pronostiqueurs_Pronostiqueur' .
					'	JOIN		(' .
					'					SELECT		pronostics.Pronostiqueurs_Pronostiqueur, SUM(pronostics.Defaites_Pronostiquees) AS Nombre_Defaites_Pronostiquees, SUM(Defaites_Reelles) AS Nombre_Defaites_Reelles' .
					'					FROM		(' .
					'									SELECT		Pronostiqueurs_Pronostiqueur, Matches_Match, 1 AS Defaites_Pronostiquees' .
					'												,CASE' .
					'													WHEN	(	Matches_ScoreEquipeDomicile > Matches_ScoreEquipeVisiteur' .
					'																OR		Matches_ScoreAPEquipeDomicile > Matches_ScoreAPEquipeVisiteur' .
					'																OR		Matches_Vainqueur = 1' .
					'															)' .
					'													THEN	1' .
					'													ELSE	0' .
					'												END AS Defaites_Reelles' .
					'									FROM		pronostics' .
					'									JOIN		matches' .
					'												ON		pronostics.Matches_Match = matches.Match' .
					'									JOIN		journees' .
					'												ON		matches.Journees_Journee = journees.Journee' .
					'									WHERE		journees.Championnats_Championnat = ' . $championnat .
					'												AND		Pronostics_ScoreEquipeDomicile IS NOT NULL' .
					'												AND		Pronostics_ScoreEquipeVisiteur IS NOT NULL' .
					'												AND		(	Pronostics_ScoreEquipeDomicile < Pronostics_ScoreEquipeVisiteur' .
					'															OR		Pronostics_ScoreAPEquipeDomicile < Pronostics_ScoreAPEquipeVisiteur' .
					'															OR		Pronostics_Vainqueur = 2' .
					'														)' .
					'								) pronostics' .
					'					GROUP BY	pronostics.Pronostiqueurs_Pronostiqueur' .
					'				) pronostics_defaite' .
					'				ON		pronostics_victoire.Pronostiqueurs_Pronostiqueur = pronostics_defaite.Pronostiqueurs_Pronostiqueur' .
					'	JOIN		pronostiqueurs' .
					'				ON		pronostics_victoire.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'	LEFT JOIN	pronostiqueurs_rivaux' .
					'				ON		pronostiqueurs_rivaux.Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'						AND		pronostiqueurs.Pronostiqueur = pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur' .
					'	ORDER BY	pronostiqueurs.Pronostiqueurs_NomUtilisateur';

	$req = $bdd->query($ordreSQL);
	$pronosticsVictoiresNulsDefaites = $req->fetchAll();

	if(sizeof($pronosticsVictoiresNulsDefaites) > 0) {
		echo '<div class="cc--statistiques-l1">';
			echo '<table class="cc--tableau">';
				echo '<thead>';
					echo '<tr>';
						echo '<th></th>';
						echo '<th>&nbsp;</th>';
						echo '<th colspan="3">Victoires à domicile</th>';
						echo '<th colspan="3">Matches nuls</th>';
						echo '<th colspan="3">Défaites à domicile</th>';
					echo '</tr>';
					echo '<tr>';
						echo '<th>Rang (indicatif)</th>';
						echo '<th>Joueurs</th>';
						echo '<th>Pronostics</th>';
						echo '<th>Réalité</th>';
						echo '<th>Ratio</th>';
						echo '<th>Pronostics</th>';
						echo '<th>Réalité</th>';
						echo '<th>Ratio</th>';
						echo '<th>Pronostics</th>';
						echo '<th>Réalité</th>';
						echo '<th>Ratio</th>';
					echo '</tr>';
				echo '<thead>';
				echo '<tbody>';
					foreach($pronosticsVictoiresNulsDefaites as $unPronostic) {
						if($unPronostic["Pronostiqueur"] == $_SESSION["pronostiqueur"])			echo '<tr class="surbrillance">';
						else if($unPronostic["Pronostiqueurs_Rival"] == 1)						echo '<tr class="rival">';
						else																			echo '<tr>';
							echo '<td></td>';
							echo '<td>' . $unPronostic["Pronostiqueurs_NomUtilisateur"] . '</td>';
							echo '<td>' . $unPronostic["Nombre_Victoires_Pronostiquees"] . '</td>';
							echo '<td>' . $unPronostic["Nombre_Victoires_Reelles"] . '</td>';
							echo '<td>' . number_format($unPronostic["Ratio_Victoires"], 2) . '%</td>';

							echo '<td>' . $unPronostic["Nombre_Nuls_Pronostiques"] . '</td>';
							echo '<td>' . $unPronostic["Nombre_Nuls_Reels"] . '</td>';
							echo '<td>' . number_format($unPronostic["Ratio_Nuls"], 2) . '%</td>';

							echo '<td>' . $unPronostic["Nombre_Defaites_Pronostiquees"] . '</td>';
							echo '<td>' . $unPronostic["Nombre_Defaites_Reelles"] . '</td>';
							echo '<td>' . number_format($unPronostic["Ratio_Defaites"], 2) . '%</td>';
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
