<?php
	include_once('../commun.php');
	
	// Affichage des palmarès pour un championnat
	
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
	
	$ordreSQL =		'	SELECT		pronostiqueurs_trophees.Trophees_CodeTrophee, IFNULL(Nombre_Trophees, 0) AS Nombre_Trophees' .
					'	FROM		(' .
					'					SELECT		Pronostiqueur, Trophees_CodeTrophee' .
					'					FROM		pronostiqueurs' .
					'					FULL JOIN	(' .
					'									SELECT DISTINCT Trophees_CodeTrophee FROM trophees' .
					'								) trophees' .
					'					JOIN		inscriptions' .
					'								ON		Pronostiqueur = Pronostiqueurs_Pronostiqueur' .
					'					WHERE		Championnats_Championnat = ' . $championnat .
					'				) pronostiqueurs_trophees' .
					'	LEFT JOIN	(' .
					'					SELECT		trophees.Pronostiqueurs_Pronostiqueur, Trophees_CodeTrophee, COUNT(*) AS Nombre_Trophees' .
					'					FROM		trophees' .
					'					JOIN		journees' .
					'								ON		trophees.Journees_Journee = journees.Journee' .
					'					WHERE		journees.Championnats_Championnat = ' . $championnat .
					'					GROUP BY	Pronostiqueurs_Pronostiqueur, Trophees_CodeTrophee' .
					'				) trophees' .
					'				ON		pronostiqueurs_trophees.Pronostiqueur = trophees.Pronostiqueurs_Pronostiqueur' .
					'						AND		pronostiqueurs_trophees.Trophees_CodeTrophee = trophees.Trophees_CodeTrophee' .
					'	JOIN		pronostiqueurs' .
					'				ON		pronostiqueurs_trophees.Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'	ORDER BY	pronostiqueurs.Pronostiqueurs_NomUtilisateur, pronostiqueurs_trophees.Trophees_CodeTrophee';

	$req = $bdd->query($ordreSQL);
	$palmares = $req->fetchAll(PDO::FETCH_ASSOC);

	$NOMBRE_TROPHEES = 8;
	$nombrePronostiqueurs = sizeof($palmares) / $NOMBRE_TROPHEES;
	
	if(sizeof($palmares) > 0) {
		echo '<div class="cc--palmares">';
			echo '<table class="cc--tableau">';
				echo '<thead>';
					echo '<tr>';
						echo '<th>Rang (indicatif)</th>';
						echo '<th>Joueurs</th>';
						echo '<th>Poulpe d\'Or</th>';
						echo '<th>Poulpe d\'Argent</th>';
						echo '<th>Poulpe de Bronze</th>';
						echo '<th>Soulier d\'Or</th>';
						echo '<th>Brandao</th>';
						echo '<th>Jérémy Morel</th>';
						echo '<th>Record de points</th>';
						echo '<th>Record de points buteur</th>';
					echo '</tr>';
				echo '<thead>';
				echo '<tbody>';
					for($i = 0; $i < $nombrePronostiqueurs; $i++) {
						if($pronostiqueurs[$i]["Pronostiqueur"] == $_SESSION["pronostiqueur"])			echo '<tr class="surbrillance">';
						else if($pronostiqueurs[$i]["Pronostiqueurs_Rival"] == 1)						echo '<tr class="rival">';
						else																			echo '<tr>';
							echo '<td></td>';
							echo '<td>' . $pronostiqueurs[$i]["Pronostiqueurs_NomUtilisateur"] . '</td>';
							for($j = 0; $j < $NOMBRE_TROPHEES; $j++) {
								echo '<td>' . $palmares[$i * $NOMBRE_TROPHEES + $j]["Nombre_Trophees"] . '</td>';
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

