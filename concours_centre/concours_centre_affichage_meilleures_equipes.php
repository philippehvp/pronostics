<?php
	include_once('../commun.php');

	// Affichage des pronostiqueurs et des points marqués par équipe par ordre décroissant

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
	$nombrePronostiqueurs = sizeof($pronostiqueurs);

	// Somme des points marqués pour les pronostiqueurs et les équipes
	$ordreSQL =		'	SELECT		IFNULL(equipes.Equipes_NomCourt, equipes.Equipes_Nom) AS Equipes_NomCourt, SUM(IFNULL(Scores_ScoreMatch, 0) + IFNULL(Scores_ScoreButeur, 0) + IFNULL(Scores_ScoreBonus, 0)) AS Scores' .
					'	FROM		(' .
					'					SELECT		Pronostiqueur, Equipe' .
					'					FROM		pronostiqueurs' .
					'					FULL JOIN	equipes' .
					'					JOIN		engagements' .
					'								ON		Equipe = engagements.Equipes_Equipe' .
					'					WHERE		engagements.Championnats_Championnat = ' . $championnat .
					'								AND		Equipes_L1Europe IS NULL' .
					'				) pronostiqueurs_equipes' .
					'	LEFT JOIN	scores' .
					'				ON		pronostiqueurs_equipes.Pronostiqueur = scores.Pronostiqueurs_Pronostiqueur' .
					'	LEFT JOIN	pronostiqueurs' .
					'				ON		pronostiqueurs_equipes.Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'	LEFT JOIN	equipes' .
					'				ON		pronostiqueurs_equipes.Equipe = equipes.Equipe' .
					'	LEFT JOIN	matches' .
					'				ON		scores.Matches_Match = matches.Match' .
					'						AND	(	pronostiqueurs_equipes.Equipe IN (matches.Equipes_EquipeDomicile, matches.Equipes_EquipeVisiteur)' .
					'								OR		matches.Equipes_EquipeDomicile IS NULL' .
					'								OR		matches.Equipes_EquipeVisiteur IS NULL' .
					'							)' .
					'	LEFT JOIN	journees' .
					'				ON		matches.Journees_Journee = journees.Journee' .
					'	WHERE		journees.Championnats_Championnat = ' . $championnat .
					'				AND		matches.Matches_ScoreEquipeDomicile IS NOT NULL' .
					'				AND		matches.Matches_ScoreEquipeVisiteur IS NOT NULL' .
					'	GROUP BY	pronostiqueurs_equipes.Pronostiqueur, pronostiqueurs_equipes.Equipe' .
					'	ORDER BY	pronostiqueurs.Pronostiqueurs_NomUtilisateur, Scores DESC';
	$req = $bdd->query($ordreSQL);
	$scores = $req->fetchAll();
	$nombreEquipes = sizeof($scores) / $nombrePronostiqueurs;

	if($nombrePronostiqueurs > 0 && $nombreEquipes > 0) {
		echo '<div class="cc--points-par-equipe">';
			echo '<table class="cc--tableau">';
				echo '<thead>';
					echo '<tr>';
						echo '<th class="pas-de-bordure-droite">Joueurs</th>';
						for($i = 0; $i < $nombreEquipes; $i++)
							echo '<th>Equipe ' . ($i + 1) . '</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
					$i = 0;
					foreach($pronostiqueurs as $unPronostiqueur) {
						if($unPronostiqueur["Pronostiqueur"] == $_SESSION["pronostiqueur"])			echo '<tr class="surbrillance">';
						else if($unPronostiqueur["Pronostiqueurs_Rival"] == 1)						echo '<tr class="rival">';
						else																		echo '<tr>';
							echo '<td class="pas-de-bordure-droite">' . $unPronostiqueur["Pronostiqueurs_NomUtilisateur"] . '</td>';

							for($j = 0; $j < $nombreEquipes; $j++) {
								echo '<td>' . $scores[$i * $nombreEquipes + $j]["Equipes_NomCourt"] . ' (' . $scores[$i * $nombreEquipes + $j]["Scores"] . ')</td>';
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
