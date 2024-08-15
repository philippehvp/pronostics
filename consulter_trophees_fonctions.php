<?php
	// Affichage des trophées pour un championnat donné et une journée donnée
	function afficherTrophees($bdd, $championnat, $journee, $dtDateMAJ, $journeeNom) {
		// Trophées de la journée
		$ordreSQL =		'	SELECT		(' .
						'					/* Premier de la journée score total */' .
						'					SELECT		CONCAT(GROUP_CONCAT(Pronostiqueurs_NomUtilisateur SEPARATOR \', \'), \' (\', classements.Classements_PointsJourneeMatch, \')\') AS Pronostiqueurs_NomUtilisateur' .
						'	FROM		classements' .
						'	JOIN		pronostiqueurs' .
						'				ON		classements.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'	WHERE		Classements_ClassementJourneeMatch = 1' .
						'				AND		Journees_Journee = ' . $journee .
						'				) Poulpe' .
						'				,(' .
						'					/* Premier de la journée en buteur */' .
						'					SELECT		CONCAT(GROUP_CONCAT(Pronostiqueurs_NomUtilisateur SEPARATOR \', \'), \' (\', classements.Classements_PointsJourneeButeur, \')\') AS Pronostiqueurs_NomUtilisateur' .
						'					FROM		classements' .
						'					JOIN		pronostiqueurs' .
						'								ON		classements.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'					WHERE		Classements_ClassementJourneeButeur = 1' .
						'								AND		Journees_Journee = ' . $journee .
						'				) Soulier' .
						'				,(' .
						'					/* Dernier buteur (sauf si tout le monde est positif) */' .
						'					SELECT		CONCAT(GROUP_CONCAT(Pronostiqueurs_NomUtilisateur SEPARATOR \', \'), \' (\', classements.Classements_PointsJourneeButeur, \')\') AS Pronostiqueurs_NomUtilisateur' .
						'					FROM		classements' .
						'					JOIN		pronostiqueurs' .
						'								ON		classements.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'					JOIN		(' .
						'									SELECT		MIN(Classements_PointsJourneeButeur) AS Classements_PointsJourneeButeur' .
						'									FROM		classements' .
						'									WHERE		Journees_Journee = ' . $journee .
						'									HAVING		MIN(Classements_PointsJourneeButeur) < 0' .
						'								) minimum' .
						'								ON		classements.Classements_PointsJourneeButeur = minimum.Classements_PointsJourneeButeur' .
						'					WHERE		classements.Journees_Journee = ' . $journee .
						'				) Choupo' .
						'				,(' .
						'					/* Dernier de la journée ayant tout saisi */' .
						'					SELECT		CONCAT(GROUP_CONCAT(Pronostiqueurs_NomUtilisateur SEPARATOR \', \'), \' (\', classements.Classements_PointsJourneeMatch, \')\') AS Pronostiqueurs_Pronostiqueur' .
						'					FROM		classements' .
						'					JOIN		pronostiqueurs' .
						'								ON		classements.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'					JOIN		(' .
						'									SELECT		MAX(Classements_PointsJourneeButeur) AS Classements_PointsJourneeButeur, classements.Classements_PointsJourneeMatch' .
						'									FROM		(' .
						'													SELECT		Pronostiqueur, classements.Classements_PointsJourneeMatch, classements.Classements_PointsJourneeButeur' .
						'													FROM		pronostiqueurs' .
						'													JOIN		classements' .
						'																ON		pronostiqueurs.Pronostiqueur = classements.Pronostiqueurs_Pronostiqueur' .
						'													LEFT JOIN	(' .
						'																	SELECT		Pronostiqueurs_Pronostiqueur' .
						'																	FROM		pronostics' .
						'																	JOIN		matches' .
						'																				ON		pronostics.Matches_Match = matches.Match' .
						'																	WHERE		matches.Journees_Journee = ' . $journee .
						'																				AND		(Pronostics_ScoreEquipeDomicile IS NULL OR Pronostics_ScoreEquipeVisiteur IS NULL)' .
						'																	GROUP BY	Pronostiqueurs_Pronostiqueur' .
						'																) pronostics_mal_saisis' .
						'																ON		Pronostiqueur = pronostics_mal_saisis.Pronostiqueurs_Pronostiqueur' .
						'													WHERE		classements.Journees_Journee = ' . $journee .
						'																AND		pronostics_mal_saisis.Pronostiqueurs_Pronostiqueur IS NULL' .
						'												) classements' .
						'									JOIN		(' .
						'													SELECT		MIN(Classements_PointsJourneeMatch) AS Classements_PointsJourneeMatch' .
						'													FROM		(' .
						'																	SELECT		Pronostiqueur, classements.Classements_PointsJourneeMatch' .
						'																	FROM		pronostiqueurs' .
						'																	JOIN		classements' .
						'																				ON		pronostiqueurs.Pronostiqueur = classements.Pronostiqueurs_Pronostiqueur' .
						'																	LEFT JOIN	(' .
						'																					SELECT		Pronostiqueurs_Pronostiqueur' .
						'																					FROM		pronostics' .
						'																					JOIN		matches' .
						'																								ON		pronostics.Matches_Match = matches.Match' .
						'																					WHERE		matches.Journees_Journee = ' . $journee .
						'																								AND		(Pronostics_ScoreEquipeDomicile IS NULL OR Pronostics_ScoreEquipeVisiteur IS NULL)' .
						'																					GROUP BY	Pronostiqueurs_Pronostiqueur' .
						'																				) pronostics_mal_saisis' .
						'																				ON		Pronostiqueur = pronostics_mal_saisis.Pronostiqueurs_Pronostiqueur' .
						'																	WHERE		classements.Journees_Journee = ' . $journee .
						'																				AND		pronostics_mal_saisis.Pronostiqueurs_Pronostiqueur IS NULL' .
						'																) classements' .
						'												) minimum' .
						'												ON		classements.Classements_PointsJourneeMatch = minimum.Classements_PointsJourneeMatch' .
						'									GROUP BY	classements.Classements_PointsJourneeMatch' .
						'								) maximum' .
						'								ON		classements.Classements_PointsJourneeButeur = maximum.Classements_PointsJourneeButeur' .
						'										AND		classements.Classements_PointsJourneeMatch = maximum.Classements_PointsJourneeMatch' .
						'					WHERE		classements.Journees_Journee = ' . $journee .
						'				) DjaDjeDje';

		$req = $bdd->query($ordreSQL);
		$trophees = $req->fetchAll();

		$ordreSQL =		'	SELECT		GROUP_CONCAT(pronostiqueurs.Pronostiqueurs_NomUtilisateur, \' (\', journees.Journees_Nom, \')\' SEPARATOR \', \') AS Nom_Record' .
						'				,classements.Classements_PointsJourneeMatch' .
						'	FROM		classements' .
						'	JOIN		pronostiqueurs' .
						'				ON		classements.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'	JOIN		journees' .
						'				ON		classements.Journees_Journee = journees.Journee' .
						'	JOIN		(' .
						'					SELECT		MAX(Classements_PointsJourneeMatch) AS Classements_PointsJourneeMatch' .
						'					FROM		classements' .
						'					JOIN		journees' .
						'								ON		classements.Journees_Journee = journees.Journee' .
						'					WHERE		Journees_Journee <= ' . $journee .
						'								AND		journees.Championnats_Championnat = ' . $championnat .
						'				) record' .
						'				ON		classements.Classements_PointsJourneeMatch = record.Classements_PointsJourneeMatch' .
						'	WHERE		Journees_Journee <= ' . $journee .
						'				AND		journees.Championnats_Championnat = ' . $championnat;
		$req = $bdd->query($ordreSQL);
		$recordJourneeMatch = $req->fetchAll();

		$ordreSQL =		'	SELECT		GROUP_CONCAT(pronostiqueurs.Pronostiqueurs_NomUtilisateur, \' (\', journees.Journees_Nom, \')\' SEPARATOR \', \') AS Nom_Record' .
						'				,classements.Classements_PointsJourneeButeur' .
						'	FROM		classements' .
						'	JOIN		pronostiqueurs' .
						'				ON		classements.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'	JOIN		journees' .
						'				ON		classements.Journees_Journee = journees.Journee' .
						'	JOIN		(' .
						'					SELECT		MAX(Classements_PointsJourneeButeur) AS Classements_PointsJourneeButeur' .
						'					FROM		classements' .
						'					JOIN		journees' .
						'								ON		classements.Journees_Journee = journees.Journee' .
						'					WHERE		Journees_Journee <= ' . $journee .
						'								AND		journees.Championnats_Championnat = ' . $championnat .
						'				) record' .
						'				ON		classements.Classements_PointsJourneeButeur = record.Classements_PointsJourneeButeur' .
						'	WHERE		Journees_Journee <= ' . $journee .
						'				AND		journees.Championnats_Championnat = ' . $championnat;
		$req = $bdd->query($ordreSQL);
		$recordJourneeButeur = $req->fetchAll();

		$ordreSQL =		'	SELECT		GROUP_CONCAT(Pronostiqueurs_NomUtilisateur SEPARATOR \', \') AS Nom_Record' .
						'	FROM		trophees' .
						'	JOIN		pronostiqueurs' .
						'				ON		trophees.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'	WHERE		trophees.Journees_Journee = ' . $journee .
						'				AND		trophees.Trophees_CodeTrophee = 9';
		$req = $bdd->query($ordreSQL);
		$dixOuOnze = $req->fetchAll();

		echo '<div class="tableau">';
			echo '<br />';

			echo '<table class="tableau--trophees" style="position: absolute;">';
				echo '<thead>';
					echo '<tr>';
						echo '<th colspan="2">';
							echo '<b>' . $journeeNom . '</b> (MAJ le ' . $dtDateMAJ->format('d/m/Y à H:i') . ')';
						echo '</th>';
					echo '</tr>';
					echo '<tr>';
						echo '<th>Trophées</th>';
						echo '<th class="aligne-gauche">Joueurs</th>';
					echo '</tr>';
				echo '</thead>';

				echo '<tbody>';
					echo '<tr>';
						echo '<td>Poulpe d\'Or</td>';
						echo '<td class="aligne-gauche">' . $trophees[0]["Poulpe"] . '</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>Soulier d\'Or</td>';
						echo '<td class="aligne-gauche">' . $trophees[0]["Soulier"] . '</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>Choupo</td>';
						echo '<td class="aligne-gauche">' . $trophees[0]["Choupo"] . '</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>Jérémy Morel</td>';
						echo '<td class="aligne-gauche">' . $trophees[0]["DjaDjeDje"] . '</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>Record de points</td>';
						echo '<td class="aligne-gauche">' . $recordJourneeMatch[0]["Nom_Record"] . ' avec ' . $recordJourneeMatch[0]["Classements_PointsJourneeMatch"] . ' points</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>Record de points buteur</td>';
						echo '<td class="aligne-gauche">' . $recordJourneeButeur[0]["Nom_Record"] . ' avec ' . $recordJourneeButeur[0]["Classements_PointsJourneeButeur"] . ' points</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>Neuf ou dix sur dix</td>';
						echo '<td class="aligne-gauche">' . $dixOuOnze[0]["Nom_Record"] . '</td>';
					echo '</tr>';
				echo '</tbody>';
			echo '</table>';
		echo '</div>';
	}
?>

<script>
	$(function() {
		/* Positionnement du tableau au centre de la page (horizontal uniquement) */
		centrerObjet('.tableau--trophees', 1, 1);

	});
</script>
