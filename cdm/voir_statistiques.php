<?php
	include('commun.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include('commun_entete.php');
?>
</head>

<body>
	<?php
		$nomPage = 'voir_statistiques.php';
		include('bandeau.php');
		
		// Podiums du tournoi pronostiqués par les joueurs
		function podiumsEquipes() {
			echo '<div>';
				echo '<table>';
					echo '<thead>';
						echo '<tr>';
							echo '<th>Podiums</th>';
							echo '<th>Vainqueur</th>';
							echo '<th>Finaliste</th>';
							echo '<th>Troisième</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
						echo '<tr>';
							echo '<td>Brésil</td><td>30</td><td>13</td><td>22</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Argentine</td><td>13</td><td>27</td><td>3</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Allemagne</td><td>12</td><td>6</td><td>22</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>France</td><td>7</td><td>2</td><td>12</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Espagne</td><td>4</td><td>11</td><td>4</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Italie</td><td>3</td><td>4</td><td>3</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Belgique</td><td>0</td><td>3</td><td>2</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Portugal</td><td>0</td><td>2</td><td>1</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Uruguay</td><td>0</td><td>1</td><td>0</td>';
						echo '</tr>';
					echo '</tbody>';
				echo '</table>';
			echo '</div>';
		}
		
		// Meilleurs buteurs pronostiqués par les joueurs
		function meilleursButeurs() {
			echo '<div>';
				echo '<table>';
					echo '<thead>';
						echo '<tr>';
							echo '<th>Meilleurs buteurs</th>';
							echo '<th>Nombre</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
						echo '<tr>';
							echo '<td>Lionel Messi</td><td>30</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Neymar</td><td>13</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Cristiano Ronaldo</td><td>6</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Fred</td><td>5</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Thomas Muller</td><td>3</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Karim Benzema</td><td>3</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Sergio Agüero</td><td>2</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Romelu Lukaku</td><td>1</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Miroslav Klose</td><td>1</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Mario Balotelli</td><td>1</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Edinson Cavani</td><td>1</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Arjen Roben</td><td>1</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Luis Suarez</td><td>1</td>';
						echo '</tr>';
					echo '</tbody>';
				echo '</table>';
			echo '</div>';
		}
		
		// Nombre d'affiches exactes trouvées à partir des huitièmes de finale
		function affichesExactes($bdd) {
			$ordreSQL =		'	SELECT		(' .
							'					SELECT		COUNT(DISTINCT pronostics.Pronostiqueurs_Pronostiqueur)' .
							'					FROM		cdm_pronostics_sequencement resultats' .
							'					JOIN		cdm_pronostics_sequencement pronostics' .
							'								ON		resultats.Matches_Match IN (pronostics.Matches_Match, cdm_fn_match_lie(pronostics.Matches_Match))' .
							'										AND		(' .
							'													(' .
							'														resultats.Equipes_EquipeA = pronostics.Equipes_EquipeA' .
							'														AND		resultats.Equipes_EquipeB = pronostics.Equipes_EquipeB' .
							'													)' .
							'													OR' .
							'													(' .
							'														resultats.Equipes_EquipeA = pronostics.Equipes_EquipeB' .
							'														AND		resultats.Equipes_EquipeB = pronostics.Equipes_EquipeA' .
							'													)' .
							'												)' .
							'					WHERE		resultats.Pronostiqueurs_Pronostiqueur = 1' .
							'								AND		pronostics.Pronostiqueurs_Pronostiqueur <> 1' .
							'								AND		resultats.Matches_Match <= 8' .
							'				) AS Nombre_Pronostiqueurs_Huitiemes' .
							'				,(' .
							'					SELECT		COUNT(DISTINCT pronostics.Pronostiqueurs_Pronostiqueur)' .
							'					FROM		cdm_pronostics_sequencement resultats' .
							'					JOIN		cdm_pronostics_sequencement pronostics' .
							'								ON		resultats.Matches_Match IN (pronostics.Matches_Match, cdm_fn_match_lie(pronostics.Matches_Match))' .
							'										AND		(' .
							'													(' .
							'														resultats.Equipes_EquipeA = pronostics.Equipes_EquipeA' .
							'														AND		resultats.Equipes_EquipeB = pronostics.Equipes_EquipeB' .
							'													)' .
							'													OR' .
							'													(' .
							'														resultats.Equipes_EquipeA = pronostics.Equipes_EquipeB' .
							'														AND		resultats.Equipes_EquipeB = pronostics.Equipes_EquipeA' .
							'													)' .
							'												)' .
							'					WHERE		resultats.Pronostiqueurs_Pronostiqueur = 1' .
							'								AND		pronostics.Pronostiqueurs_Pronostiqueur <> 1' .
							'								AND		resultats.Matches_Match > 8' .
							'								AND		resultats.Matches_Match <= 12' .
							'				) AS Nombre_Pronostiqueurs_Quarts' .
							'				,(' .
							'					SELECT		COUNT(DISTINCT pronostics.Pronostiqueurs_Pronostiqueur)' .
							'					FROM		cdm_pronostics_sequencement resultats' .
							'					JOIN		cdm_pronostics_sequencement pronostics' .
							'								ON		resultats.Matches_Match IN (pronostics.Matches_Match, cdm_fn_match_lie(pronostics.Matches_Match))' .
							'										AND		(' .
							'													(' .
							'														resultats.Equipes_EquipeA = pronostics.Equipes_EquipeA' .
							'														AND		resultats.Equipes_EquipeB = pronostics.Equipes_EquipeB' .
							'													)' .
							'													OR' .
							'													(' .
							'														resultats.Equipes_EquipeA = pronostics.Equipes_EquipeB' .
							'														AND		resultats.Equipes_EquipeB = pronostics.Equipes_EquipeA' .
							'													)' .
							'												)' .
							'					WHERE		resultats.Pronostiqueurs_Pronostiqueur = 1' .
							'								AND		pronostics.Pronostiqueurs_Pronostiqueur <> 1' .
							'								AND		resultats.Matches_Match > 12' .
							'								AND		resultats.Matches_Match <= 14' .
							'				) AS Nombre_Pronostiqueurs_Demi' .
							'				,(' .
							'					SELECT		COUNT(DISTINCT pronostics.Pronostiqueurs_Pronostiqueur)' .
							'					FROM		cdm_pronostics_sequencement resultats' .
							'					JOIN		cdm_pronostics_sequencement pronostics' .
							'								ON		resultats.Matches_Match IN (pronostics.Matches_Match, cdm_fn_match_lie(pronostics.Matches_Match))' .
							'										AND		(' .
							'													(' .
							'														resultats.Equipes_EquipeA = pronostics.Equipes_EquipeA' .
							'														AND		resultats.Equipes_EquipeB = pronostics.Equipes_EquipeB' .
							'													)' .
							'													OR' .
							'													(' .
							'														resultats.Equipes_EquipeA = pronostics.Equipes_EquipeB' .
							'														AND		resultats.Equipes_EquipeB = pronostics.Equipes_EquipeA' .
							'													)' .
							'												)' .
							'					WHERE		resultats.Pronostiqueurs_Pronostiqueur = 1' .
							'								AND		pronostics.Pronostiqueurs_Pronostiqueur <> 1' .
							'								AND		resultats.Matches_Match = 16' .
							'				) AS Nombre_Pronostiqueurs_Troisieme_Place' .
							'				,(' .
							'					SELECT		COUNT(DISTINCT pronostics.Pronostiqueurs_Pronostiqueur)' .
							'					FROM		cdm_pronostics_sequencement resultats' .
							'					JOIN		cdm_pronostics_sequencement pronostics' .
							'								ON		resultats.Matches_Match IN (pronostics.Matches_Match, cdm_fn_match_lie(pronostics.Matches_Match))' .
							'										AND		(' .
							'													(' .
							'														resultats.Equipes_EquipeA = pronostics.Equipes_EquipeA' .
							'														AND		resultats.Equipes_EquipeB = pronostics.Equipes_EquipeB' .
							'													)' .
							'													OR' .
							'													(' .
							'														resultats.Equipes_EquipeA = pronostics.Equipes_EquipeB' .
							'														AND		resultats.Equipes_EquipeB = pronostics.Equipes_EquipeA' .
							'													)' .
							'												)' .
							'					WHERE		resultats.Pronostiqueurs_Pronostiqueur = 1' .
							'								AND		pronostics.Pronostiqueurs_Pronostiqueur <> 1' .
							'								AND		resultats.Matches_Match = 15' .
							'				) AS Nombre_Pronostiqueurs_Finale';

			$req = $bdd->query($ordreSQL);
			$statistiquesAffichesExactes = $req->fetchAll();
			
			echo '<div>';
				echo '<table>';
					echo '<thead>';
						echo '<tr>';
							echo '<th>Affiches exactes</th>';
							echo '<th>Nombre de joueurs</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
						foreach($statistiquesAffichesExactes as $uneStat) {
							echo '<tr>';
								echo '<td>Huitièmes de finale</td>';
								echo '<td>' . $uneStat["Nombre_Pronostiqueurs_Huitiemes"] . '</td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td>Quarts de finale</td>';
								echo '<td>' . $uneStat["Nombre_Pronostiqueurs_Quarts"] . '</td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td>Demi-finales</td>';
								echo '<td>' . $uneStat["Nombre_Pronostiqueurs_Demi"] . '</td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td>Troisième place</td>';
								echo '<td>' . $uneStat["Nombre_Pronostiqueurs_Troisieme_Place"] . '</td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td>Finale</td>';
								echo '<td>' . $uneStat["Nombre_Pronostiqueurs_Finale"] . '</td>';
							echo '</tr>';
						}
					echo '</tbody>';
				echo '</table>';
			echo '</div>';		
		}

		// Nombre de pronostiqueurs ayant trouvé chaque équipe qualifiée en huitièmes, en quarts, etc.	
		function pronosticsEquipesQualifiees($bdd) {
			// Equipes en huitièmes de finale
			$ordreSQL =		'	SELECT		Equipe, Equipes_Nom' .
							'				,CASE' .
							'					WHEN	pronostics.Equipes_Equipe IS NULL' .
							'					THEN	0' .
							'					ELSE	COUNT(*)' .
							'				END AS Nombre' .
							'	FROM		(' .
							'					SELECT		DISTINCT Equipes_EquipeA AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur = 1' .
							'								AND		Matches_Match <= 8' .
							'					UNION ALL' .
							'					SELECT		DISTINCT Equipes_EquipeB AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur = 1' .
							'								AND		Matches_Match <= 8' .
							'				) resultats' .
							'	LEFT JOIN	(' .
							'					SELECT		Pronostiqueurs_Pronostiqueur, Equipes_EquipeA AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur <> 1' .
							'								AND		Matches_Match <= 8' .
							'					UNION ALL' .
							'					SELECT		Pronostiqueurs_Pronostiqueur, Equipes_EquipeB AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur <> 1' .
							'								AND		Matches_Match <= 8' .
							'				) pronostics' .
							'				ON		resultats.Equipes_Equipe = pronostics.Equipes_Equipe' .
							'	JOIN		cdm_equipes' .
							'				ON		resultats.Equipes_Equipe = cdm_equipes.Equipe' .
							'	GROUP BY	resultats.Equipes_Equipe' .
							'	ORDER BY	Nombre DESC';
			$req = $bdd->query($ordreSQL);
			$statistiquesHuitiemes = $req->fetchAll();

			echo '<div class="colle-gauche gauche">';
				echo '<table>';
					echo '<thead>';
						echo '<tr>';
							echo '<th>Equipes en huitièmes</th>';
							echo '<th>Nombre de joueurs</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
						foreach($statistiquesHuitiemes as $uneStat) {
							if($uneStat["Nombre"])
								echo '<tr class="curseur-main" onclick="voirStatistiques_afficherPronostiqueursHuitiemes(' . $uneStat["Equipe"] . ')">';
							else
								echo '<tr>';
								echo '<td>' . $uneStat["Equipes_Nom"] . '</td>';
								echo '<td>' . $uneStat["Nombre"] . '</td>';
							echo '</tr>';
						}
					echo '</tbody>';
				echo '</table>';
			echo '</div>';

			// Equipes en quarts de finale
			$ordreSQL =		'	SELECT		Equipe, Equipes_Nom' .
							'				,CASE' .
							'					WHEN	pronostics.Equipes_Equipe IS NULL' .
							'					THEN	0' .
							'					ELSE	COUNT(*)' .
							'				END AS Nombre' .
							'	FROM		(' .
							'					SELECT		DISTINCT Equipes_EquipeA AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur = 1' .
							'								AND		Matches_Match > 8' .
							'								AND		Matches_Match <= 12' .
							'					UNION ALL' .
							'					SELECT		DISTINCT Equipes_EquipeB AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur = 1' .
							'								AND		Matches_Match > 8' .
							'								AND		Matches_Match <= 12' .
							'				) resultats' .
							'	LEFT JOIN	(' .
							'					SELECT		Pronostiqueurs_Pronostiqueur, Equipes_EquipeA AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur <> 1' .
							'								AND		Matches_Match > 8' .
							'								AND		Matches_Match <= 12' .
							'					UNION ALL' .
							'					SELECT		Pronostiqueurs_Pronostiqueur, Equipes_EquipeB AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur <> 1' .
							'								AND		Matches_Match > 8' .
							'								AND		Matches_Match <= 12' .
							'				) pronostics' .
							'				ON		resultats.Equipes_Equipe = pronostics.Equipes_Equipe' .
							'	JOIN		cdm_equipes' .
							'				ON		resultats.Equipes_Equipe = cdm_equipes.Equipe' .
							'	GROUP BY	resultats.Equipes_Equipe' .
							'	ORDER BY	Nombre DESC';
			$req = $bdd->query($ordreSQL);
			$statistiquesQuarts = $req->fetchAll();

			echo '<div class="gauche" style="margin-left: 20px;">';
				echo '<table>';
					echo '<thead>';
						echo '<tr>';
							echo '<th>Equipes en quarts</th>';
							echo '<th>Nombre de joueurs</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
						foreach($statistiquesQuarts as $uneStat) {
							if($uneStat["Nombre"])
								echo '<tr class="curseur-main" onclick="voirStatistiques_afficherPronostiqueursQuarts(' . $uneStat["Equipe"] . ')">';
							else
								echo '<tr>';
								echo '<td>' . $uneStat["Equipes_Nom"] . '</td>';
								echo '<td>' . $uneStat["Nombre"] . '</td>';
							echo '</tr>';
						}
					echo '</tbody>';
				echo '</table>';
			echo '</div>';

			// Equipes en demi-finales
			$ordreSQL =		'	SELECT		Equipe, Equipes_Nom' .
							'				,CASE' .
							'					WHEN	pronostics.Equipes_Equipe IS NULL' .
							'					THEN	0' .
							'					ELSE	COUNT(*)' .
							'				END AS Nombre' .
							'	FROM		(' .
							'					SELECT		DISTINCT Equipes_EquipeA AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur = 1' .
							'								AND		Matches_Match IN (13, 14)' .
							'					UNION ALL' .
							'					SELECT		DISTINCT Equipes_EquipeB AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur = 1' .
							'								AND		Matches_Match IN (13, 14)' .
							'				) resultats' .
							'	LEFT JOIN	(' .
							'					SELECT		Pronostiqueurs_Pronostiqueur, Equipes_EquipeA AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur <> 1' .
							'								AND		Matches_Match IN (13, 14)' .
							'					UNION ALL' .
							'					SELECT		Pronostiqueurs_Pronostiqueur, Equipes_EquipeB AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur <> 1' .
							'								AND		Matches_Match IN (13, 14)' .
							'				) pronostics' .
							'				ON		resultats.Equipes_Equipe = pronostics.Equipes_Equipe' .
							'	JOIN		cdm_equipes' .
							'				ON		resultats.Equipes_Equipe = cdm_equipes.Equipe' .
							'	GROUP BY	resultats.Equipes_Equipe' .
							'	ORDER BY	Nombre DESC';
			$req = $bdd->query($ordreSQL);
			$statistiquesDemi = $req->fetchAll();

			echo '<div class="gauche" style="margin-left: 20px;">';
				echo '<table>';
					echo '<thead>';
						echo '<tr>';
							echo '<th>Equipes en demi</th>';
							echo '<th>Nombre de joueurs</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
						foreach($statistiquesDemi as $uneStat) {
							if($uneStat["Nombre"])
								echo '<tr class="curseur-main" onclick="voirStatistiques_afficherPronostiqueursDemi(' . $uneStat["Equipe"] . ')">';
							else
								echo '<tr>';
								echo '<td>' . $uneStat["Equipes_Nom"] . '</td>';
								echo '<td>' . $uneStat["Nombre"] . '</td>';
							echo '</tr>';
						}
					echo '</tbody>';
				echo '</table>';
			echo '</div>';
			
			// Equipes de la petite finale
			$ordreSQL =		'	SELECT		Equipe, Equipes_Nom' .
							'				,CASE' .
							'					WHEN	pronostics.Equipes_Equipe IS NULL' .
							'					THEN	0' .
							'					ELSE	COUNT(*)' .
							'				END AS Nombre' .
							'	FROM		(' .
							'					SELECT		DISTINCT Equipes_EquipeA AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur = 1' .
							'								AND		Matches_Match = 16' .
							'					UNION ALL' .
							'					SELECT		DISTINCT Equipes_EquipeB AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur = 1' .
							'								AND		Matches_Match = 16' .
							'				) resultats' .
							'	LEFT JOIN	(' .
							'					SELECT		Pronostiqueurs_Pronostiqueur, Equipes_EquipeA AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur <> 1' .
							'								AND		Matches_Match = 16' .
							'					UNION ALL' .
							'					SELECT		Pronostiqueurs_Pronostiqueur, Equipes_EquipeB AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur <> 1' .
							'								AND		Matches_Match = 16' .
							'				) pronostics' .
							'				ON		resultats.Equipes_Equipe = pronostics.Equipes_Equipe' .
							'	JOIN		cdm_equipes' .
							'				ON		resultats.Equipes_Equipe = cdm_equipes.Equipe' .
							'	GROUP BY	resultats.Equipes_Equipe' .
							'	ORDER BY	Nombre DESC';
			$req = $bdd->query($ordreSQL);
			$statistiquesPetiteFinale = $req->fetchAll();

			echo '<div class="colle-gauche gauche">';
				echo '<table>';
					echo '<thead>';
						echo '<tr>';
							echo '<th>Equipes 3ème place</th>';
							echo '<th>Nombre de joueurs</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
						foreach($statistiquesPetiteFinale as $uneStat) {
							if($uneStat["Nombre"])
								echo '<tr class="curseur-main" onclick="voirStatistiques_afficherPronostiqueursPetiteFinale(' . $uneStat["Equipe"] . ')">';
							else
								echo '<tr>';
								echo '<td>' . $uneStat["Equipes_Nom"] . '</td>';
								echo '<td>' . $uneStat["Nombre"] . '</td>';
							echo '</tr>';
						}
					echo '</tbody>';
				echo '</table>';
			echo '</div>';
			
			// Equipes en finale
			$ordreSQL =		'	SELECT		Equipe, Equipes_Nom' .
							'				,CASE' .
							'					WHEN	pronostics.Equipes_Equipe IS NULL' .
							'					THEN	0' .
							'					ELSE	COUNT(*)' .
							'				END AS Nombre' .
							'	FROM		(' .
							'					SELECT		DISTINCT Equipes_EquipeA AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur = 1' .
							'								AND		Matches_Match = 15' .
							'					UNION ALL' .
							'					SELECT		DISTINCT Equipes_EquipeB AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur = 1' .
							'								AND		Matches_Match = 15' .
							'				) resultats' .
							'	LEFT JOIN	(' .
							'					SELECT		Pronostiqueurs_Pronostiqueur, Equipes_EquipeA AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur <> 1' .
							'								AND		Matches_Match = 15' .
							'					UNION ALL' .
							'					SELECT		Pronostiqueurs_Pronostiqueur, Equipes_EquipeB AS Equipes_Equipe' .
							'					FROM		cdm_pronostics_sequencement' .
							'					WHERE		Pronostiqueurs_Pronostiqueur <> 1' .
							'								AND		Matches_Match = 15' .
							'				) pronostics' .
							'				ON		resultats.Equipes_Equipe = pronostics.Equipes_Equipe' .
							'	JOIN		cdm_equipes' .
							'				ON		resultats.Equipes_Equipe = cdm_equipes.Equipe' .
							'	GROUP BY	resultats.Equipes_Equipe' .
							'	ORDER BY	Nombre DESC';
			$req = $bdd->query($ordreSQL);
			$statistiquesFinale = $req->fetchAll();

			echo '<div class="gauche" style="margin-left: 20px;">';
				echo '<table>';
					echo '<thead>';
						echo '<tr>';
							echo '<th>Equipes en finale</th>';
							echo '<th>Nombre de joueurs</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
						foreach($statistiquesFinale as $uneStat) {
							if($uneStat["Nombre"])
								echo '<tr class="curseur-main" onclick="voirStatistiques_afficherPronostiqueursFinale(' . $uneStat["Equipe"] . ')">';
							else
								echo '<tr>';
								echo '<td>' . $uneStat["Equipes_Nom"] . '</td>';
								echo '<td>' . $uneStat["Nombre"] . '</td>';
							echo '</tr>';
						}
					echo '</tbody>';
				echo '</table>';
			echo '</div>';

		}

		// Meilleure progression
		function pronostiqueursProgressions($bdd) {
			// Meilleure progression
			$ordreSQL =		'	SELECT		cdm_pronostiqueurs.Pronostiqueurs_Nom, delta.Delta, GROUP_CONCAT(aujourdhui.Classements_JourneeEnCours SEPARATOR \', \') AS Journees' .
							'	FROM		cdm_classements aujourdhui' .
							'	LEFT JOIN	cdm_classements hier' .
							'				ON		aujourdhui.Pronostiqueurs_Pronostiqueur = hier.Pronostiqueurs_Pronostiqueur' .
							'						AND		aujourdhui.Classements_JourneeEnCours - 1 = hier.Classements_JourneeEnCours' .
							'	JOIN		(' .
							'					SELECT		aujourdhui.Pronostiqueurs_Pronostiqueur, MAX(aujourdhui.Classements_Classement - hier.Classements_Classement) AS Delta' .
							'					FROM		cdm_classements aujourdhui' .
							'					LEFT JOIN	cdm_classements hier' .
							'								ON		aujourdhui.Pronostiqueurs_Pronostiqueur = hier.Pronostiqueurs_Pronostiqueur' .
							'										AND		aujourdhui.Classements_JourneeEnCours - 1 = hier.Classements_JourneeEnCours' .
							'					GROUP BY	aujourdhui.Pronostiqueurs_Pronostiqueur' .
							'				) delta' .
							'				ON		aujourdhui.Pronostiqueurs_Pronostiqueur = delta.Pronostiqueurs_Pronostiqueur' .
							'						AND		(aujourdhui.Classements_Classement - hier.Classements_Classement) = delta.Delta' .
							'	JOIN		cdm_pronostiqueurs' .
							'				ON		aujourdhui.Pronostiqueurs_Pronostiqueur = cdm_pronostiqueurs.Pronostiqueur' .
							'	GROUP BY	aujourdhui.Pronostiqueurs_Pronostiqueur, delta.Delta' .
							'	ORDER BY	Delta DESC';
			
			$req =$bdd->query($ordreSQL);
			$progressions = $req->fetchAll();

			echo '<div>';
				echo '<table>';
					echo '<thead>';
						echo '<tr>';
							echo '<th>Joueurs</th>';
							echo '<th>Gain de places</th>';
							echo '<th>Journées</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
						foreach($progressions as $progression) {
							echo '<tr>';
								echo '<td>' . $progression["Pronostiqueurs_Nom"] . '</td>';
								echo '<td>' . $progression["Delta"] . '</td>';
								echo '<td>' . $progression["Journees"] . '</td>';
							echo '</tr>';
						}
					echo '</tbody>';
				echo '</table>';
			echo '</div>';
		}
		
		// Régression plus importante
		function pronostiqueursRegressions($bdd) {
			// Plus grosse chute
			$ordreSQL =		'	SELECT		cdm_pronostiqueurs.Pronostiqueurs_Nom, delta.Delta, GROUP_CONCAT(aujourdhui.Classements_JourneeEnCours SEPARATOR \', \') AS Journees' .
							'	FROM		cdm_classements aujourdhui' .
							'	LEFT JOIN	cdm_classements hier' .
							'				ON		aujourdhui.Pronostiqueurs_Pronostiqueur = hier.Pronostiqueurs_Pronostiqueur' .
							'						AND		aujourdhui.Classements_JourneeEnCours - 1 = hier.Classements_JourneeEnCours' .
							'	JOIN		(' .
							'					SELECT		aujourdhui.Pronostiqueurs_Pronostiqueur, MIN(aujourdhui.Classements_Classement - hier.Classements_Classement) AS Delta' .
							'					FROM		cdm_classements aujourdhui' .
							'					LEFT JOIN	cdm_classements hier' .
							'								ON		aujourdhui.Pronostiqueurs_Pronostiqueur = hier.Pronostiqueurs_Pronostiqueur' .
							'										AND		aujourdhui.Classements_JourneeEnCours - 1 = hier.Classements_JourneeEnCours' .
							'					GROUP BY	aujourdhui.Pronostiqueurs_Pronostiqueur' .
							'				) delta' .
							'				ON		aujourdhui.Pronostiqueurs_Pronostiqueur = delta.Pronostiqueurs_Pronostiqueur' .
							'						AND		(aujourdhui.Classements_Classement - hier.Classements_Classement) = delta.Delta' .
							'	JOIN		cdm_pronostiqueurs' .
							'				ON		aujourdhui.Pronostiqueurs_Pronostiqueur = cdm_pronostiqueurs.Pronostiqueur' .
							'	GROUP BY	aujourdhui.Pronostiqueurs_Pronostiqueur, delta.Delta' .
							'	ORDER BY	Delta ASC';
			
			$req =$bdd->query($ordreSQL);
			$regressions = $req->fetchAll();
			
			echo '<div>';
				echo '<table>';
					echo '<thead>';
						echo '<tr>';
							echo '<th>Joueurs</th>';
							echo '<th>Perte de places</th>';
							echo '<th>Journées</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
						foreach($regressions as $regression) {
							echo '<tr>';
								echo '<td>' . $regression["Pronostiqueurs_Nom"] . '</td>';
								echo '<td>' . $regression["Delta"] . '</td>';
								echo '<td>' . $regression["Journees"] . '</td>';
							echo '</tr>';
						}
					echo '</tbody>';
				echo '</table>';
			echo '</div>';
		}
		
		// Nombre de fois où un pronostiqueur est dans le top 3
		function pronostiqueursTop3($bdd) {
			$ordreSQL =		'	SELECT		Pronostiqueurs_Nom, Nombre_Premier, Nombre_Deuxieme, Nombre_Troisieme' .
							'	FROM		(' .
							'					SELECT		Pronostiqueur' .
							'								,(' .
							'									SELECT		COUNT(*)' .
							'									FROM		cdm_classements' .
							'									WHERE		Pronostiqueurs_Pronostiqueur = cdm_pronostiqueurs.Pronostiqueur' .
							'												AND		Classements_Classement = 1' .
							'								) AS Nombre_Premier' .
							'								,(' .
							'									SELECT		COUNT(*)' .
							'									FROM		cdm_classements' .
							'									WHERE		Pronostiqueurs_Pronostiqueur = cdm_pronostiqueurs.Pronostiqueur' .
							'												AND		Classements_Classement = 2' .
							'								) AS Nombre_Deuxieme' .
							'								,(' .
							'									SELECT		COUNT(*)' .
							'									FROM		cdm_classements' .
							'									WHERE		Pronostiqueurs_Pronostiqueur = cdm_pronostiqueurs.Pronostiqueur' .
							'												AND		Classements_Classement = 3' .
							'								) AS Nombre_Troisieme' .
							'					FROM		cdm_pronostiqueurs' .
							'					WHERE		Pronostiqueur <> 1' .
							'				) classements' .
							'	JOIN		cdm_pronostiqueurs' .
							'				ON		classements.Pronostiqueur = cdm_pronostiqueurs.Pronostiqueur' .
							'	WHERE		Nombre_Premier <> 0' .
							'				OR		Nombre_Deuxieme <> 0' .
							'				OR		Nombre_Troisieme <> 0' .
							'	ORDER BY	Nombre_Premier DESC, Nombre_Deuxieme DESC, Nombre_Troisieme DESC';
			$req = $bdd->query($ordreSQL);
			$podiums = $req->fetchAll();

			echo '<div class="colle-gauche">';
				echo '<table>';
					echo '<thead>';
						echo '<tr>';
							echo '<th>Podium</th>';
							echo '<th>Première place</th>';
							echo '<th>Deuxième place</th>';
							echo '<th>Troisième place</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
						foreach($podiums as $podium) {
							echo '<tr>';
								echo '<td>' . $podium["Pronostiqueurs_Nom"] . '</td>';
								echo '<td>' . $podium["Nombre_Premier"] . '</td>';
								echo '<td>' . $podium["Nombre_Deuxieme"] . '</td>';
								echo '<td>' . $podium["Nombre_Troisieme"] . '</td>';
							echo '</tr>';
						}
					echo '</tbody>';
				echo '</table>';
			echo '</div>';
		}
		
		echo '<div id="divStatistiques">';
			echo '<ul>';
				echo '<li><a href="#divStatistiques-1">Statistiques sur les équipes</a></li>';
				echo '<li><a href="#divStatistiques-2">Affiches et équipes</a></li>';
				echo '<li><a href="#divStatistiques-3">Statistiques des pronostiqueurs</a></li>';
			echo '</ul>';
			echo '<div id="divStatistiques-1">';
				podiumsEquipes();
				meilleursButeurs();
			echo '</div>';
			
			echo '<div id="divStatistiques-2">';
				affichesExactes($bdd);
				pronosticsEquipesQualifiees($bdd);
			echo '</div>';
			
			echo '<div id="divStatistiques-3">';
				pronostiqueursTop3($bdd);
				pronostiqueursProgressions($bdd);
				pronostiqueursRegressions($bdd);
			echo '</div>';
			
		echo '</div>';

?>
	
	<div id="divInfo"></div>
	<div id="divReinitialisationPronostics"></div>
	<div id="divPronostiqueursEquipesQualifiees"></div>
	
	<script>
		$(function() {
			afficherTitrePage('divStatistiques', 'Statistiques');
			$('#divStatistiques').tabs();
		});

	</script>
</body>
</html>