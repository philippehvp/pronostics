<?php
	include_once('commun.php');
	
	// Affichage des pronostiqueurs et des points marqués par équipe
	
	// Lecture des paramètres passés à la page
	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;
	
	// Equipes du championnat
	$ordreSQL =		'	SELECT		IFNULL(Equipes_NomCourt, Equipes_Nom) AS Equipes_NomCourt' .
					'	FROM		equipes' .
					'	JOIN		engagements' .
					'				ON		equipes.Equipe = engagements.Equipes_Equipe' .
					'	WHERE		engagements.Championnats_Championnat = ' . $championnat .
					'				AND		equipes.Equipes_L1Europe IS NULL' .
					'	ORDER BY	IFNULL(Equipes_NomCourt, Equipes_Nom)';
	$req = $bdd->query($ordreSQL);
	$equipes = $req->fetchAll();
	$nombreEquipes = sizeof($equipes);
	
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
	$nombrePronostiqueurs = sizeof($pronostiqueurs);

	// Somme des points marqués pour les pronostiqueurs et les équipes
	$ordreSQL =		'	SELECT		IFNULL(SUM(Scores_ScoreMatch + Scores_ScoreButeur + Scores_ScoreBonus), 0) AS Scores' .
					'	FROM		(' .
					'					SELECT		Pronostiqueur, Equipes_Equipe' .
					'					FROM		pronostiqueurs' .
					'					FULL JOIN	engagements' .
					'					JOIN		equipes' .
					'								ON		Equipes_Equipe = equipes.Equipe' .
					'					WHERE		Championnats_Championnat = ' . $championnat .
					'								AND		equipes.Equipes_L1Europe IS NULL' .
					'				) pronostiqueurs_equipes' .
					'	LEFT JOIN	(' .
					'					SELECT		scores.Pronostiqueurs_Pronostiqueur, matches.Equipes_EquipeDomicile AS Equipes_Equipe' .
					'								,IFNULL(Scores_ScoreMatch, 0) AS Scores_ScoreMatch, IFNULL(Scores_ScoreButeur, 0) AS Scores_ScoreButeur, IFNULL(Scores_ScoreBonus, 0) AS Scores_ScoreBonus' .
					'					FROM		scores' .
					'					JOIN		matches' .
					'								ON		scores.Matches_Match = matches.Match' .
					'					JOIN		journees' .
					'								ON		matches.Journees_Journee = journees.Journee' .
					'					JOIN		equipes' .
					'								ON		matches.Equipes_EquipeDomicile = equipes.Equipe' .
					'					WHERE		journees.Championnats_Championnat = ' . $championnat .
					'								AND		matches.Matches_ScoreEquipeDomicile IS NOT NULL' .
					'								AND		matches.Matches_ScoreEquipeVisiteur IS NOT NULL' .
					'								AND		equipes.Equipes_L1Europe IS NULL' .
					'					UNION ALL' .
					'					SELECT		scores.Pronostiqueurs_Pronostiqueur, matches.Equipes_EquipeVisiteur AS Equipes_Equipe' .
					'								,IFNULL(Scores_ScoreMatch, 0) AS Scores_ScoreMatch, IFNULL(Scores_ScoreButeur, 0) AS Scores_ScoreButeur, IFNULL(Scores_ScoreBonus, 0) AS Scores_ScoreBonus' .
					'					FROM		scores' .
					'					JOIN		matches' .
					'								ON		scores.Matches_Match = matches.Match' .
					'					JOIN		journees' .
					'								ON		matches.Journees_Journee = journees.Journee' .
					'					JOIN		equipes' .
					'								ON		matches.Equipes_EquipeVisiteur = equipes.Equipe' .
					'					WHERE		journees.Championnats_Championnat = ' . $championnat .
					'								AND		matches.Matches_ScoreEquipeDomicile IS NOT NULL' .
					'								AND		matches.Matches_ScoreEquipeVisiteur IS NOT NULL' .
					'								AND		equipes.Equipes_L1Europe IS NULL' .
					'				) scores' .
					'				ON		pronostiqueurs_equipes.Pronostiqueur = scores.Pronostiqueurs_Pronostiqueur' .
					'						AND		pronostiqueurs_equipes.Equipes_Equipe = scores.Equipes_Equipe' .
					'	LEFT JOIN	equipes' .
					'				ON		pronostiqueurs_equipes.Equipes_Equipe = equipes.Equipe' .
					'	LEFT JOIN	pronostiqueurs' .
					'				ON		pronostiqueurs_equipes.Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'	GROUP BY	pronostiqueurs_equipes.Pronostiqueur, pronostiqueurs_equipes.Equipes_Equipe' .
					'	ORDER BY	pronostiqueurs.Pronostiqueurs_NomUtilisateur, IFNULL(Equipes_NomCourt, Equipes_Nom)';

	$req = $bdd->query($ordreSQL);
	$scores = $req->fetchAll();
	
	if($nombrePronostiqueurs > 0 && $nombreEquipes > 0) {
		echo '<div class="cc--points-par-equipe">';
			echo '<table class="cc--tableau">';
				echo '<thead>';
					echo '<tr>';
						echo '<th>Rang (indicatif)</th>';
						echo '<th class="pas-de-bordure-droite">Joueurs</th>';
						foreach($equipes as $uneEquipe)
							echo '<th>' . $uneEquipe["Equipes_NomCourt"] . '</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
					$i = 0;
					foreach($pronostiqueurs as $unPronostiqueur) {
						if($unPronostiqueur["Pronostiqueur"] == $_SESSION["pronostiqueur"])			echo '<tr class="surbrillance">';
						else if($unPronostiqueur["Pronostiqueurs_Rival"] == 1)						echo '<tr class="rival">';
						else																		echo '<tr>';
							echo '<td></td>';
							echo '<td class="pas-de-bordure-droite">' . $unPronostiqueur["Pronostiqueurs_NomUtilisateur"] . '</td>';
							
							for($j = 0; $j < $nombreEquipes; $j++) {
								echo '<td>' . $scores[$i * $nombreEquipes + $j]["Scores"] . '</td>';
							}
						echo '</tr>';
						
						$i++;
					}
					
				echo '</tbody>';
			echo '</table>';
		echo '</div>';
	}
	else {
		echo '<label>Aucune donnée à afficher</label>';
	}


?>
