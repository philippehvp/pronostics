<?php
	include_once('commun.php');
	
	// Affichage des pronostiqueurs et des points marqués du match Canal
	
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

	// Somme des points marqués pour le match Canal
	$ordreSQL =		'	SELECT		Scores' .
					'	FROM		vue_scoresmatchescanal' .
					'	ORDER BY	Pronostiqueurs_NomUtilisateur';

	$req = $bdd->query($ordreSQL);
	$scores = $req->fetchAll();
	
	if($nombrePronostiqueurs > 0) {
		echo '<div class="cc--points-par-equipe">';
			echo '<table class="cc--tableau">';
				echo '<thead>';
					echo '<tr>';
						echo '<th>Rang (indicatif)</th>';
						echo '<th class="pas-de-bordure-droite">Joueurs</th>';
						echo '<th>Scores</th>';
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
							echo '<td>' . $scores[$i]["Scores"] . '</td>';
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
