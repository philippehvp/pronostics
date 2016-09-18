<?php
	include('commun.php');
	
	// Affichage de la répartition des points pour un championnat
	
	// Lecture des paramètres passés à la page
	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;
	
	// Liste des pronostiqueurs pour le championnat en question
	$ordreSQL =		'	SELECT		pronostiqueurs.Pronostiqueur, Pronostiqueurs_NomUtilisateur' .
					'				,CASE' .
					'					WHEN		pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur IS NOT NULL' .
					'					THEN		1' .
					'					ELSE		0' .
					'				END AS Pronostiqueurs_Rival' .
					'	FROM		pronostiqueurs' .
					'	JOIN		inscriptions' .
					'				ON		pronostiqueurs.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
					'	LEFT JOIN	pronostiqueurs_rivaux' .
					'				ON		pronostiqueurs_rivaux.Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'						AND		pronostiqueurs.Pronostiqueur = pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur' .
					'	WHERE		inscriptions.Championnats_Championnat = ' . $championnat .
					'	ORDER BY	Pronostiqueurs_NomUtilisateur';
	$req = $bdd->query($ordreSQL);
	$pronostiqueurs = $req->fetchAll();

	$ordreSQL =		'	SELECT		pronostiqueurs_points_marques.Points_Marques, IFNULL(Nombre, 0) AS Nombre' .
					'	FROM		(' .
					'					SELECT		Pronostiqueur, Points_Marques' .
					'					FROM		pronostiqueurs' .
					'					FULL JOIN	(' .
					'									SELECT 10 AS Points_Marques UNION' .
					'									SELECT 8 AS Points_Marques UNION' .
					'									SELECT 7 AS Points_Marques UNION' .
					'									SELECT 6 AS Points_Marques UNION' .
					'									SELECT 5 AS Points_Marques UNION' .
					'									SELECT 3 AS Points_Marques UNION' .
					'									SELECT 2 AS Points_Marques UNION' .
					'									SELECT 1 AS Points_Marques UNION' .
					'									SELECT 0 AS Points_Marques UNION' .
					'									SELECT -1 AS Points_Marques' .			// Utilisé pour le nombre de pronostics oubliés
					'								) points_marques' .
					'					JOIN		inscriptions' .
					'								ON		Pronostiqueur = Pronostiqueurs_Pronostiqueur' .
					'					WHERE		Championnats_Championnat = ' . $championnat .
					'				) pronostiqueurs_points_marques' .
					'	LEFT JOIN	(' .
					'					SELECT		pronostics.Pronostiqueurs_Pronostiqueur' .
					'								,fn_calculpointsmatch	(	IFNULL(matches.Matches_ScoreAPEquipeDomicile, matches.Matches_ScoreEquipeDomicile),' .
					'															IFNULL(matches.Matches_ScoreAPEquipeVisiteur, matches.Matches_ScoreEquipeVisiteur),' .
					'															IFNULL(pronostics.Pronostics_ScoreAPEquipeDomicile, pronostics.Pronostics_ScoreEquipeDomicile),' .
					'															IFNULL(pronostics.Pronostics_ScoreAPEquipeVisiteur, pronostics.Pronostics_ScoreEquipeVisiteur)' .
					'														) AS Points_Marques' .
					'								,COUNT(*) AS Nombre' .
					'								FROM		pronostics' .
					'								JOIN		pronostiqueurs' .
					'											ON		pronostics.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'								JOIN		matches' .
					'											ON		pronostics.Matches_Match = matches.Match' .
					'								JOIN		journees' .
					'											ON		matches.Journees_Journee = journees.Journee' .
					'								WHERE		journees.Championnats_Championnat = ' . $championnat .
					'											AND		pronostics.Pronostics_ScoreEquipeDomicile IS NOT NULL' .
					'											AND		pronostics.Pronostics_ScoreEquipeVisiteur IS NOT NULL' .
					'											AND		matches.Matches_ScoreEquipeDomicile IS NOT NULL' .
					'											AND		matches.Matches_ScoreEquipeVisiteur IS NOT NULL' .
					'								GROUP BY	pronostics.Pronostiqueurs_Pronostiqueur' .
					'											,fn_calculpointsmatch	(	IFNULL(matches.Matches_ScoreAPEquipeDomicile, matches.Matches_ScoreEquipeDomicile),' .
					'																		IFNULL(matches.Matches_ScoreAPEquipeVisiteur, matches.Matches_ScoreEquipeVisiteur),' .
					'																		IFNULL(pronostics.Pronostics_ScoreAPEquipeDomicile, pronostics.Pronostics_ScoreEquipeDomicile),' .
					'																		IFNULL(pronostics.Pronostics_ScoreAPEquipeVisiteur, pronostics.Pronostics_ScoreEquipeVisiteur)' .
					'																	)' .
					'					UNION ALL' .
					'					SELECT		Pronostiqueurs_Pronostiqueur, -1 AS Points_Marques, COUNT(*) AS Nombre' .
					'					FROM		matches' .
					'					JOIN		journees' .
					'								ON		matches.Journees_Journee = journees.Journee' .
					'					LEFT JOIN	pronostics' .
					'								ON		matches.Match = pronostics.Matches_Match' .
					'					WHERE		journees.Championnats_Championnat = ' . $championnat .
					'								AND		matches.Matches_ScoreEquipeDomicile IS NOT NULL' .
					'								AND		matches.Matches_ScoreEquipeVisiteur IS NOT NULL' .
					'								AND		pronostics.Pronostics_ScoreEquipeDomicile IS NULL' .
					'								AND		pronostics.Pronostics_ScoreEquipeVisiteur IS NULL' .
					'					GROUP BY	Pronostiqueurs_Pronostiqueur' .
					'				) points_marques' .
					'				ON		pronostiqueurs_points_marques.Pronostiqueur = points_marques.Pronostiqueurs_Pronostiqueur' .
					'						AND		pronostiqueurs_points_marques.Points_Marques = points_marques.Points_Marques' .
					'	JOIN		pronostiqueurs' .
					'				ON		pronostiqueurs_points_marques.Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'	ORDER BY	pronostiqueurs.Pronostiqueurs_NomUtilisateur, pronostiqueurs_points_marques.Points_Marques DESC';

	$req = $bdd->query($ordreSQL);
	$scoresMarques = $req->fetchAll();


	$NOMBRE_COLONNES = 10;
	$nombrePronostiqueurs = sizeof($scoresMarques) / $NOMBRE_COLONNES;
	
	if(sizeof($scoresMarques) > 0) {
		echo '<div class="cc--repartition-points">';
			echo '<table class="cc--tableau">';
				echo '<thead>';
					echo '<tr>';
						echo '<th>Rang (indicatif)</th>';
						echo '<th>Joueurs</th>';
						echo '<th>Scores exacts</th>';
						echo '<th title="Vainqueur et nombre de buts du vainqueur">8 points</th>';
						echo '<th title="Vainqueur et bon écart ou Match nul sans le score exact">7 points</th>';
						echo '<th title="Vainqueur et nombre de buts du perdant">6 points</th>';
						echo '<th title="Seulement le vainqueur">5 points</th>';
						echo '<th title="Cumul des colonnes précédentes">Résultats corrects</th>';
						echo '<th title="Seulement le nombre de buts du vainqueur">3 points</th>';
						echo '<th title="Nombre de buts d\'une équipe d\'un match nul">2 points</th>';
						echo '<th title="Seulement le nombre de buts du perdant">1 point</th>';
						echo '<th>Tout faux</th>';
						echo '<th title="Nombre de pronostics non effectués">Oublis</th>';
					echo '</tr>';
				echo '<thead>';
				echo '<tbody>';
					for($i = 0; $i < $nombrePronostiqueurs; $i++) {
						$resultatsCorrects = 0;
						if($pronostiqueurs[$i]["Pronostiqueur"] == $_SESSION["pronostiqueur"])			echo '<tr class="surbrillance">';
						else if($pronostiqueurs[$i]["Pronostiqueurs_Rival"] == 1)						echo '<tr class="rival">';
						else																			echo '<tr>';
							echo '<td></td>';
							echo '<td>' . $pronostiqueurs[$i]["Pronostiqueurs_NomUtilisateur"] . '</td>';
							for($j = 0; $j < $NOMBRE_COLONNES; $j++) {
								// On affiche un résumé du nombre de résultats corrects
								// Les résultats corrects correspondent aux points 10, 8, 7, 6 et 5
								if($j <= 4)
									$resultatsCorrects += $scoresMarques[$i * $NOMBRE_COLONNES + $j]["Nombre"];

								echo '<td>' . $scoresMarques[$i * $NOMBRE_COLONNES + $j]["Nombre"] . '</td>';
								if($j == 4)
									echo '<td>' . $resultatsCorrects . '</td>';
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

