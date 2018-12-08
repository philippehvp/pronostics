<?php
	// Page d'affichage du classement général

	$JOURNEE_MIN = 1;
	$JOURNEE_MAX = 25;

	$appelAjax = isset($_POST["appelAjax"]) ? $_POST["appelAjax"] : 0;
	if($appelAjax == 1) {
		include_once('commun.php');
	}

	// Lecture du numéro de journée en cours et de la phase en cours (phase de poule ou phase finale)
	$ordreSQL =		'	SELECT		cdm_fn_journee_en_cours() AS Journee_Max';
	$req = $bdd->query($ordreSQL);
	while($donnees = $req->fetch()) {
		$journeeMax = $donnees["Journee_Max"];
	}
	$req->closeCursor();

	// Lecture du numéro de journée à afficher
	$journeeEnCours = isset($_POST["journeeEnCours"]) ? $_POST["journeeEnCours"] : 0;
	
	
	if($journeeEnCours < $JOURNEE_MIN)
		$journeeEnCours = $journeeMax;

	if($journeeEnCours > $journeeMax || $journeeEnCours > $JOURNEE_MAX)
		$journeeEnCours = $JOURNEE_MIN;

	if(!$journeeEnCours)
		$journeeEnCours = 1;
	
	// Meilleure progression, plus grosse chute (en terme de place) et idem pour le nombre de points (points max et points min)
	$ordreSQL =		'		SELECT		MAX(classements_veille.Classements_Classement - cdm_classements.Classements_Classement) AS Progression_Max' .
								'							,MIN(classements_veille.Classements_Classement - cdm_classements.Classements_Classement) AS Progression_Min' .
								'							,MAX(cdm_classements.Classements_Points - classements_veille.Classements_Points) AS Points_Max' .
								'							,MIN(cdm_classements.Classements_Points - classements_veille.Classements_Points) AS Points_Min' .
								'		FROM			cdm_classements' .
								'		LEFT JOIN	cdm_classements classements_veille' .
								'							ON		cdm_classements.Pronostiqueurs_Pronostiqueur = classements_veille.Pronostiqueurs_Pronostiqueur' .
								'										AND		cdm_classements.Classements_JourneeEnCours = classements_veille.Classements_JourneeEnCours + 1' .
								'		WHERE			cdm_classements.Classements_JourneeEnCours = ' . $journeeEnCours .
								'		GROUP BY	cdm_classements.Classements_JourneeEnCours';

	$req = $bdd->query($ordreSQL);
	$stats = $req->fetchAll();
	if(sizeof($stats)) {
		$progressionMax = $stats[0]["Progression_Max"];
		$progressionMin = $stats[0]["Progression_Min"];
		$pointsMax = $stats[0]["Points_Max"];
		$pointsMin = $stats[0]["Points_Min"];
	}
	else {
		$progressionMax = 0;
		$progressionMin = 0;
		$pointsMax = 0;
		$pointsMin = 0;
	}

	// Tous les classements
	$ordreSQL =		'	SELECT		Pronostiqueur' .
					'				,cdm_classements.Classements_Classement' .
					'				,CASE' .
					'					WHEN	classements_veille.Pronostiqueurs_Pronostiqueur IS NOT NULL' .
					'					THEN	classements_veille.Classements_Classement - cdm_classements.Classements_Classement' .
					'					ELSE	-1000' .
					'				END AS Evolution' .
					'				,CASE' .
					'					WHEN	classements_veille.Pronostiqueurs_Pronostiqueur IS NOT NULL' .
					'					THEN	cdm_classements.Classements_Points - classements_veille.Classements_Points' .
					'					ELSE	-1000' .
					'				END AS Evolution_Points' .
					'				,cdm_classements.Pronostiqueurs_Pronostiqueur' .
					'				,CASE' .
					'					WHEN	Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"] .
					'					THEN	\'Moi\'' .
					'					ELSE	Pronostiqueurs_Nom' .
					'				END AS Pronostiqueurs_Nom' .
					'				,cdm_classements.Classements_Points' .
					'	FROM		cdm_classements' .
					'	JOIN		cdm_pronostiqueurs' .
					'				ON		cdm_classements.Pronostiqueurs_Pronostiqueur = cdm_pronostiqueurs.Pronostiqueur' .
					'	LEFT JOIN	cdm_classements classements_veille' .
					'				ON		cdm_classements.Pronostiqueurs_Pronostiqueur = classements_veille.Pronostiqueurs_Pronostiqueur' .
					'						AND		cdm_classements.Classements_JourneeEnCours = classements_veille.Classements_JourneeEnCours + 1' .
					'	WHERE		cdm_classements.Classements_JourneeEnCours = ' . $journeeEnCours .
					'	ORDER BY	Classements_Classement';

	$req = $bdd->query($ordreSQL);
	$classements = $req->fetchAll();

	if(sizeof($classements)) {
		echo '<div id="divStats"></div>';
		echo '<div id="divEvolution"></div>';
		echo '<div id="divPoints"></div>';
		
		
		echo '<div id="divEnteteClassementGeneral">';
			//echo '<div class="colle-gauche gauche nomModule">Classement général</div>';
			if($journeeEnCours > $JOURNEE_MIN)
				echo '<label class="bouton" onclick="module_classementGeneral_afficherJournee(' . ($journeeEnCours - 1) . ');">&lt;</label>';
			else
				echo '<label class="bouton" onclick="module_classementGeneral_afficherJournee(' . $journeeMax. ');">&lt;</label>';

			echo '<label class="journee">Classement de la journée ' . $journeeEnCours . '</label>';
			
			if($journeeEnCours < $journeeMax)
				echo '<label class="bouton" onclick="module_classementGeneral_afficherJournee(' . ($journeeEnCours + 1) . ');">&gt;</label>';
			else
				echo '<label class="bouton" onclick="module_classementGeneral_afficherJournee(' . $JOURNEE_MIN . ');">&gt;</label>';
		echo '</div>';
		
		echo '<table id="tblClassementGeneral">';
			echo '<thead>';
				echo '<th class="classement" title="Cliquez dans cette colonne pour voir l\'évolution du classement d\'un joueur">?</th>';
				echo '<th class="evolution">+/-</th>';
				echo '<th class="noms">Noms</th>';
				echo '<th class="points">Points</th>';
			echo '</thead>';
			
			echo '<tbody>';
				$classementsPrecedent = '';
				foreach($classements as $unClassement) {
					if($classementsPrecedent == '')
						$classementsAffiche = $unClassement["Classements_Classement"];
					else if($classementsPrecedent == $unClassement["Classements_Classement"])
						$classementsAffiche = '-';
					else
						$classementsAffiche = $unClassement["Classements_Classement"];
					
					$classementsPrecedent = $unClassement["Classements_Classement"];

					if($unClassement["Pronostiqueurs_Pronostiqueur"] == $_SESSION["cdm_pronostiqueur"])
						echo '<tr class="surbrillance">';
					else
						echo '<tr>';

							$evolution = isset($unClassement["Evolution"]) ? $unClassement["Evolution"] : '';
							$evolutionPoints = isset($unClassement["Evolution_Points"]) ? $unClassement["Evolution_Points"] : '';
							
							echo '<td class="bordure-gauche bordure-droite" onclick="module_classementGeneral_afficherEvolution(' . $unClassement["Pronostiqueur"] .', \'' . $unClassement["Pronostiqueurs_Nom"] . '\');">' . $classementsAffiche . '</td>';
							
							$photo = $unClassement["Pronostiqueurs_Photo"];
							if($photo != null)
								$balisePhoto = '<div class="gauche photo"><img src="images/pronostiqueurs/' . $photo . '" alt="" /></div>';
							else
								$balisePhoto = '<div class="gauche photo">&nbsp;</div>';

							if($evolution == -1000)
								echo '<td class="bordure-droite">' . $balisePhoto . '</td>';
							else {
								// Si l'évolution est égale à l'évolution la plus importante ou la plus faible, il faut le noter
								$classeEvolutionMax = $evolution == $progressionMax ? ' evolutionMax' : '';
								$classeEvolutionMin = $evolution == $progressionMin ? ' evolutionMin' : '';
								
								if($evolution > 0)
									echo '<td class="bordure-droite evolution' . $classeEvolutionMax . '" onclick="module_classementGeneral_afficherStats(' . $unClassement["Pronostiqueur"] .', \'' . $unClassement["Pronostiqueurs_Nom"] . '\');">' . $balisePhoto . '<div class="gauche evolution"><img src="images/positif.gif" alt="" /> +' . $evolution . '</div></td>';
								else if($evolution < 0)
									echo '<td class="bordure-droite evolution' . $classeEvolutionMin . '" onclick="module_classementGeneral_afficherStats(' . $unClassement["Pronostiqueur"] .', \'' . $unClassement["Pronostiqueurs_Nom"] . '\');">' . $balisePhoto . '<div class="gauche evolution"><img src="images/negatif.gif" alt="" /> ' . $evolution . '</div></td>';
								else
									echo '<td class="bordure-droite evolution" onclick="module_classementGeneral_afficherStats(' . $unClassement["Pronostiqueur"] .', \'' . $unClassement["Pronostiqueurs_Nom"] . '\');">' . $balisePhoto . '<div class="gauche evolution"><img src="images/identique.gif" alt="" />&nbsp;</div></td>';
							}

							echo '<td class="bordure-droite" onclick="module_classementGeneral_afficherStats(' . $unClassement["Pronostiqueur"] .', \'' . $unClassement["Pronostiqueurs_Nom"] . '\');">' . $unClassement["Pronostiqueurs_Nom"] . '</td>';

							if($evolutionPoints == -1000)
								echo '<td class="bordure-droite" onclick="module_classementGeneral_afficherStats(' . $unClassement["Pronostiqueur"] .', \'' . $unClassement["Pronostiqueurs_Nom"] . '\');">' . $unClassement["Classements_Points"] . '</td>';
							else {
								$classePointsMax = $evolutionPoints == $pointsMax ? ' evolutionPointsMax' : '';
								$classePointsMin = $evolutionPoints == $pointsMin ? ' evolutionPointsMin' : '';
								if($evolutionPoints == $pointsMax)
									echo '<td class="bordure-droite' . $classePointsMax . '" onclick="module_classementGeneral_afficherPoints(' . $unClassement["Pronostiqueur"] .', \'' . $unClassement["Pronostiqueurs_Nom"] . '\');">' . $unClassement["Classements_Points"] . ' (+' . $evolutionPoints . ')</td>';
								else if($evolutionPoints == $pointsMin)
									echo '<td class="bordure-droite' . $classePointsMin . '" onclick="module_classementGeneral_afficherPoints(' . $unClassement["Pronostiqueur"] .', \'' . $unClassement["Pronostiqueurs_Nom"] . '\');">' . $unClassement["Classements_Points"] . ' (+' . $evolutionPoints . ')</td>';
								else
									echo '<td class="bordure-droite" onclick="module_classementGeneral_afficherPoints(' . $unClassement["Pronostiqueur"] .', \'' . $unClassement["Pronostiqueurs_Nom"] . '\');">' . $unClassement["Classements_Points"] . ' (+' . $evolutionPoints . ')</td>';
							}
							
					echo '</tr>';
				}
			echo '</tbody>';
		echo '</table>';
	}
?>

