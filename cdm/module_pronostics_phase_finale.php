<?php
	// Cette page est appelée de deux manières :
	// - soit par une page PHP (paramètre appelAjax inconnu)
	// - soit par un appel Ajax (rafraîchissement de la page demandé par elle-même) (appelAjax est égal à 1)
	// Selon l'appelant, il est nécessaire ou non d'inclure l'en-tête, de créer la balise englobante ou non, etc.
	// La détection de l'appel dépend de la présence du paramètre appelAjax

	$JOURNEE_MIN = 16;
	$JOURNEE_MAX = 25;
	
	$appelAjax = isset($_POST["appelAjax"]) ? $_POST["appelAjax"] : 0;
	if($appelAjax == 1) {
		include('commun.php');
	}
	
	// Avec un rafraîchissement de la page, certains paramètres permettent de savoir quelle journée afficher, s'il s'agit d'une journée de poule ou de phase finale
	if($appelAjax == 1) {
		$journeeEnCours = isset($_POST["journeeEnCours"]) ? $_POST["journeeEnCours"] : 0;
	}
	else {
		// S'il ne s'agit pas d'un rafraîchissement de page, on doit déterminer quelle journée afficher
		// Lecture du numéro de journée en cours et de la phase en cours (phase de poule ou phase finale)
		$ordreSQL =		'	SELECT		cdm_fn_journee_en_cours() AS Journee_En_Cours';
		$req = $bdd->query($ordreSQL);
		while($donnees = $req->fetch()) {
			$journeeEnCours = $donnees["Journee_En_Cours"];
		}
		$req->closeCursor();
	}
	
	if($journeeEnCours < $JOURNEE_MIN)
		$journeeEnCours = $JOURNEE_MIN;

	if($journeeEnCours > $JOURNEE_MAX)
		$journeeEnCours = $JOURNEE_MAX;
		
	
	$ordreSQL =		'	SELECT		EquipeA' .
					'				,EquipeB' .
					'				,EquipesA_Nom, EquipesB_Nom, EquipesA_NomCourt, EquipesB_NomCourt' .
					'				,Journees_Journee, Matches_DateLocale' .
					'				,Pronostics_ScoreEquipeA, Pronostics_ScoreAPEquipeA' .
					'				,Pronostics_ScoreEquipeB, Pronostics_ScoreAPEquipeB' .
					'				,Pronostics_Vainqueur' .
					'				,CASE' .
					'					WHEN	cdm_matches_direct.Matches_Match IS NOT NULL' .
					'					THEN	1' .
					'					ELSE	0' .
					'				END AS Matches_EnDirect' .
					'	FROM		cdm_vue_matches_phase_finale' .
					'	LEFT JOIN	cdm_pronostics_phase_finale' .
					'				ON		cdm_vue_matches_phase_finale.Match = cdm_pronostics_phase_finale.Matches_Match' .
					'						AND		cdm_pronostics_phase_finale.Pronostiqueurs_Pronostiqueur = 1' .
					'	LEFT JOIN	cdm_matches_direct' .
					'				ON		cdm_vue_matches_phase_finale.Match = cdm_matches_direct.Matches_Match' .
					'	WHERE		Matches_JourneeEnCours = ' . $journeeEnCours .
					'	ORDER BY	Matches_DateLocale';

	$req = $bdd->query($ordreSQL);
	$matches = $req->fetchAll();
	$nombreMatches = sizeof($matches);
	
	// Tous les pronostics
	$ordreSQL =		'	SELECT		CASE' .
					'					WHEN	cdm_pronostiqueurs.Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"] .
					'					THEN	1' .
					'					ELSE	2' .
					'				END AS Ordre' .
					'				,sequencement.Matches_Match' .
					'				,CASE' .
					'					WHEN	Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"] .
					'					THEN	\'Moi\'' .
					'					ELSE	Pronostiqueurs_Nom' .
					'				END AS Pronostiqueurs_Nom' .
					'				,equipesA.Equipe AS EquipeA' .
					'				,equipesB.Equipe AS EquipeB' .
					'				,equipesA.Equipes_NomCourt AS EquipeA_NomCourt' .
					'				,equipesB.Equipes_NomCourt AS EquipeB_NomCourt' .
					'				,Pronostics_ScoreEquipeA, Pronostics_ScoreAPEquipeA' .
					'				,Pronostics_ScoreEquipeB, Pronostics_ScoreAPEquipeB' .
					'				,Pronostics_Vainqueur' .
					'				,IF(affiches_inversees.Pronostiqueurs_Pronostiqueur, 1, 0) AS Affiche_Inversee' .
					'				,CASE' .
					'					WHEN	(	resultats.Equipes_EquipeA = pronostics.Equipes_EquipeA' .
					'								AND		resultats.Equipes_EquipeB = pronostics.Equipes_EquipeB' .
					'							)' .
					'							OR' .
					'							(	resultats.Equipes_EquipeA = pronostics.Equipes_EquipeB' .
					'								AND		resultats.Equipes_EquipeB = pronostics.Equipes_EquipeA' .
					'							)' .
					'					THEN	1' .
					'					ELSE	0' .
					'				END AS Affiche_Exacte' .
					'				,CASE' .
					'					WHEN	cdm_scores.Scores_Coefficient > 0 OR cdm_scores.Scores_Coefficient IS NOT NULL' .
					'					THEN	cdm_scores.Scores_ScoreMatch' .
					'					ELSE	0' .
					'				END AS Scores_ScoreMatch' .
					'				,cdm_scores.Scores_Coefficient' .
					'				,cdm_scores.Scores_ScoreBonus' .
					'	FROM		cdm_pronostics_sequencement sequencement' .
					'	JOIN		cdm_matches_phase_finale' .
					'				ON		sequencement.Matches_Match = cdm_matches_phase_finale.Match' .
					'	LEFT JOIN	cdm_affiches_inversees affiches_inversees' .
					'				ON		sequencement.Matches_Match = affiches_inversees.Matches_Match' .
					'						AND		sequencement.Pronostiqueurs_Pronostiqueur = affiches_inversees.Pronostiqueurs_Pronostiqueur' .
					'	LEFT JOIN	cdm_pronostics_sequencement pronostics' .
					'				ON		pronostics.Pronostiqueurs_Pronostiqueur = sequencement.Pronostiqueurs_Pronostiqueur' .
					'						AND		pronostics.Matches_Match = IFNULL(affiches_inversees.Pronostiqueurs_Matches_Match, sequencement.Matches_Match)' .
					'	LEFT JOIN	cdm_pronostics_sequencement resultats' .
					'				ON		sequencement.Matches_Match = resultats.Matches_Match' .
					'	LEFT JOIN	cdm_equipes equipesA' .
					'				ON		pronostics.Equipes_EquipeA = equipesA.Equipe' .
					'	LEFT JOIN	cdm_equipes equipesB' .
					'				ON		pronostics.Equipes_EquipeB = equipesB.Equipe' .
					'	LEFT JOIN	cdm_pronostiqueurs' .
					'				ON		pronostics.Pronostiqueurs_Pronostiqueur = cdm_pronostiqueurs.Pronostiqueur' .
					'	LEFT JOIN	cdm_pronostics_phase_finale' .
					'				ON		pronostics.Pronostiqueurs_Pronostiqueur = cdm_pronostics_phase_finale.Pronostiqueurs_Pronostiqueur' .
					'						AND		pronostics.Matches_Match = cdm_pronostics_phase_finale.Matches_Match' .
					'	LEFT JOIN	cdm_scores' .
					'				ON		pronostics.Pronostiqueurs_Pronostiqueur = cdm_scores.Pronostiqueurs_Pronostiqueur' .
					'						AND		pronostics.Matches_Match = cdm_scores.Matches_Match' .
					'	WHERE		sequencement.Pronostiqueurs_Pronostiqueur <> 1' .
					'				AND		resultats.Pronostiqueurs_Pronostiqueur = 1' .
					'				AND		cdm_scores.Scores_Phase = 2' .
					'				AND		cdm_matches_phase_finale.Matches_JourneeEnCours = ' . $journeeEnCours .
					'	ORDER BY	Ordre, cdm_pronostiqueurs.Pronostiqueurs_Nom, Matches_DateLocale';

	$req = $bdd->query($ordreSQL);
	$pronostics = $req->fetchAll();
	
	$nombrePronostics = sizeof($pronostics) / ($nombreMatches == 0 ? 1 : $nombreMatches);
	
	// Affichage d'une zone d'information sur la journée en cours d'affichage
	// Cette zone contient aussi les boutons pour passer aux journées suivante / précédente
	echo '<div id="divEnteteJournees">';
		//echo '<div class="colle-gauche gauche nomModule">Phase finale</div>';
		if($journeeEnCours > $JOURNEE_MIN)
			echo '<label class="bouton" onclick="module_pronosticsPhaseFinale_afficherJournee(' . ($journeeEnCours - 1) . ');">&lt;</label>';
		else
			echo '<label class="bouton" onclick="module_pronosticsPhaseFinale_afficherJournee(' . $JOURNEE_MAX . ');">&lt;</label>';

		setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
		echo '<label class="date">Journée du ' . strftime('%#d %B', strtotime($matches[0]["Matches_DateLocale"])) . '</label>';
		
		if($journeeEnCours < $JOURNEE_MAX)
			echo '<label class="bouton" onclick="module_pronosticsPhaseFinale_afficherJournee(' . ($journeeEnCours + 1) . ');">&gt;</label>';
		else
			echo '<label class="bouton" onclick="module_pronosticsPhaseFinale_afficherJournee(' . $JOURNEE_MIN . ');">&gt;</label>';
		
		echo '<label class="legende" onclick="module_pronosticsPhaseFinale_afficherLegende();">Légende</label>';
	echo '</div>';
	
	// Affichage des matches dans l'en-tête de la table
	echo '<table id="tblPronosticsPhaseFinale">';
		echo '<thead>';
			echo '<tr>';
				echo '<th>&nbsp;</th>';
				for($i = 0; $i < $nombreMatches; $i++) {
				
					$equipeA = $matches[$i]["EquipeA"];
					$equipeB = $matches[$i]["EquipeB"];
					$nomEquipeA = $matches[$i]["EquipesA_NomCourt"];
					$nomEquipeB = $matches[$i]["EquipesB_NomCourt"];
					$scoreEquipeA = $matches[$i]["Pronostics_ScoreEquipeA"];
					$scoreEquipeB = $matches[$i]["Pronostics_ScoreEquipeB"];
					$scoreAPEquipeA = $matches[$i]["Pronostics_ScoreAPEquipeA"];
					$scoreAPEquipeB = $matches[$i]["Pronostics_ScoreAPEquipeB"];
					$vainqueur = $matches[$i]["Pronostics_Vainqueur"];
				
					if($vainqueur != null) {
						if($vainqueur == -1)
							$scoreAffiche = $scoreAPEquipeA . ' AP - ' . $scoreAPEquipeB . ' AP';
						else if($vainqueur == $matches[$i]["EquipeA"])
							$scoreAffiche = $scoreAPEquipeA . ' TAB - ' . $scoreAPEquipeB;
						else if($vainqueur == $matches[$i]["EquipeB"])
							$scoreAffiche = $scoreAPEquipeA . ' - ' . $scoreAPEquipeB . ' TAB';
						else
							$scoreAffiche = 'TAB';
							
					}
					else {
						if($scoreAPEquipeA != null && $scoreAPEquipeB != null) {
							if($scoreAPEquipeA > $scoreAPEquipeB)
								$scoreAffiche = $scoreAPEquipeA . ' AP - ' . $scoreAPEquipeB;
							else if($scoreAPEquipeA > $scoreAPEquipeB)
								$scoreAffiche = $scoreAPEquipeA . ' - ' . $scoreAPEquipeB . ' AP';
							else
								$scoreAffiche = $scoreAPEquipeA . ' AP - ' . $scoreAPEquipeB . ' AP';
						
						}
						else
							$scoreAffiche = $scoreEquipeA . ' - ' . $scoreEquipeB;
					}
					
					$matchEnDirect = $matches[$i]["Matches_EnDirect"] == 1 ? 'matchEnDirect' : '';
					echo '<th class="match" title="' . $matches[$i]["EquipesA_Nom"] . ' vs ' . $matches[$i]["EquipesB_Nom"] . '">';
						// Zone des affiches (exactitude du pronostic et inversion d'affiche)
						echo '<div class="colle-gauche gauche affiches">';
							
							// Affiche inversée
							echo '<div class="colle-gauche">';
							echo '</div>';
						echo '</div>';
						
						// Zone des noms des équipes
						echo '<div class="gauche nomEquipes">';
							// Equipe A
							echo '<div>' . $nomEquipeA . '</div>';
							
							// Equipe B
							echo '<div>' . $nomEquipeB . '</div>';
						echo '</div>';
						
						// Zone des scores des matches
						echo '<div class="gauche scoreEquipes ' . $matchEnDirect . '">';
							// Scores équipes
							echo '<div>' . $scoreAffiche . '</div>';
							
						echo '</div>';
						
						// Zone des points bonus d'affiche
						echo '<div class="gauche pointsBonus">';
							echo '&nbsp;';
						echo '</div>';
						
						// Zone des points match
						echo '<div class="gauche pointsMatch">';
							echo '&nbsp;';
						echo '</div>';
					echo '</th>';
				}
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
			for($j = 0; $j < $nombrePronostics; $j++) {
				echo '<tr>';
					echo '<td class="nomPronostiqueur bordure-droite">' . $pronostics[$j*$nombreMatches]["Pronostiqueurs_Nom"] . '</td>';
					for($k = 0; $k < $nombreMatches; $k++) {
						
						$equipeA = $pronostics[$j*$nombreMatches+$k]["EquipeA"] != null ? $pronostics[$j*$nombreMatches+$k]["EquipeA"] : '&nbsp;';
						$equipeB = $pronostics[$j*$nombreMatches+$k]["EquipeB"] != null ? $pronostics[$j*$nombreMatches+$k]["EquipeB"] : '&nbsp;';
						$scoreEquipeA = $pronostics[$j*$nombreMatches+$k]["Pronostics_ScoreEquipeA"] != null ? $pronostics[$j*$nombreMatches+$k]["Pronostics_ScoreEquipeA"] : '&nbsp;';
						$scoreEquipeB = $pronostics[$j*$nombreMatches+$k]["Pronostics_ScoreEquipeB"] != null ? $pronostics[$j*$nombreMatches+$k]["Pronostics_ScoreEquipeB"] : '&nbsp;';
						$scoreAPEquipeA = $pronostics[$j*$nombreMatches+$k]["Pronostics_ScoreAPEquipeA"] != null ? $pronostics[$j*$nombreMatches+$k]["Pronostics_ScoreAPEquipeA"] : '&nbsp;';
						$scoreAPEquipeB = $pronostics[$j*$nombreMatches+$k]["Pronostics_ScoreAPEquipeB"] != null ? $pronostics[$j*$nombreMatches+$k]["Pronostics_ScoreAPEquipeB"] : '&nbsp;';
						$vainqueur = $pronostics[$j*$nombreMatches+$k]["Pronostics_Vainqueur"] != null ? $pronostics[$j*$nombreMatches+$k]["Pronostics_Vainqueur"] : '&nbsp;';
						
						if($vainqueur != '&nbsp;') {
							if($vainqueur == $equipeA) {
								$scoreAfficheA = $scoreAPEquipeA . ' TAB';
								$scoreAfficheB = $scoreAPEquipeB;
							}
							else if($vainqueur == $equipeB) {
								$scoreAfficheA = $scoreAPEquipeA;
								$scoreAfficheB = $scoreAPEquipeB . ' TAB';
							}
							else {
								$scoreAfficheA = $scoreAPEquipeA;
								$scoreAfficheB = $scoreAPEquipeB;
							}
						}
						else {
							if($scoreAPEquipeA != '&nbsp;') {
								$scoreAfficheA = $scoreAPEquipeA . ' AP';
								$scoreAfficheB = $scoreAPEquipeB . ' AP';
							}
							else {
								$scoreAfficheA = $scoreEquipeA;
								$scoreAfficheB = $scoreEquipeB;
							}
						}
						
						$nomEquipeA = $pronostics[$j*$nombreMatches+$k]["EquipeA_NomCourt"] != null ? $pronostics[$j*$nombreMatches+$k]["EquipeA_NomCourt"] : '';
						$nomEquipeB = $pronostics[$j*$nombreMatches+$k]["EquipeB_NomCourt"] != null ? $pronostics[$j*$nombreMatches+$k]["EquipeB_NomCourt"] : '';
						$scoreMatch = $pronostics[$j*$nombreMatches+$k]["Scores_ScoreMatch"] != null ? $pronostics[$j*$nombreMatches+$k]["Scores_ScoreMatch"] : '';
						$scoreCoefficient = $pronostics[$j*$nombreMatches+$k]["Scores_Coefficient"] != null ? $pronostics[$j*$nombreMatches+$k]["Scores_Coefficient"] : '';
						$scoreMatchAffiche = ($scoreMatch == '' || $scoreCoefficient == '') ? '' : ($scoreMatch * $scoreCoefficient);
						$scoreBonus = $pronostics[$j*$nombreMatches+$k]["Scores_ScoreBonus"] != null ? $pronostics[$j*$nombreMatches+$k]["Scores_ScoreBonus"] : '';
						$afficheExacte = $pronostics[$j*$nombreMatches+$k]["Affiche_Exacte"] != null ? $pronostics[$j*$nombreMatches+$k]["Affiche_Exacte"] : 0;
						$afficheInversee = $pronostics[$j*$nombreMatches+$k]["Affiche_Inversee"] != null ? $pronostics[$j*$nombreMatches+$k]["Affiche_Inversee"] : 0;
						
						// Couleur de chaque cellule selon les résultats
						if($scoreMatch < 5)
							$style = '';
						else if($scoreMatch >= 5 && $scoreMatch < 10)
							$style = 'orange';
						else
							$style = 'vert';

						echo '<td class="match bordure-droite">';
							// Affiche inversée
							$classeAfficheInversee = $afficheInversee ? 'orange' : '';
							echo '<div class="colle-gauche gauche affiches ' . $classeAfficheInversee . '">';
							echo '</div>';
							
							// Zone des noms des équipes
							// Affiche exacte ?
							$classeAfficheExacte = $afficheExacte ? 'vert' : '';

							echo '<div class="gauche nomEquipes ' . $classeAfficheExacte . '">';
								// Equipe A
								echo '<div>' . $nomEquipeA . '</div>';
								
								// Equipe B
								echo '<div>' . $nomEquipeB . '</div>';
							echo '</div>';
							
							// Zone des scores des matches
							echo '<div class="gauche scoreEquipes fondScore">';
								// Scores des équipes
								echo $scoreAfficheA . ' - ' . $scoreAfficheB;
							echo '</div>';
							
							// Zone des points bonus d'affiche
							echo '<div class="gauche pointsBonus">';
								echo $scoreBonus;
							echo '</div>';
							
							// Zone des points match
							echo '<div class="gauche pointsMatch ' . $style . '">';
								echo $scoreMatchAffiche;
							echo '</div>';
						echo '</td>';
						
					}
				echo '</tr>';
			}
		echo '</tbody>';
	echo '</table>';
?>
