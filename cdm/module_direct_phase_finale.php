
	<?php
		// Cette page affiche les matches en direct
		
		$appelAjax = isset($_POST["appelAjax"]) ? 1 : 0;

		if($appelAjax == 1) {
			include('commun.php');
		}

		// Lecture des matches en direct
		$ordreSQL =		'	SELECT		cdm_matches_phase_finale.Match' .
						'				,equipesA.Equipe AS EquipeA, equipesB.Equipe AS EquipeB' .
						'				,equipesA.Equipes_Nom AS EquipeA_Nom, equipesB.Equipes_Nom AS EquipeB_Nom' .
						'				,equipesA.Equipes_Fanion AS EquipesA_Fanion, equipesB.Equipes_Fanion AS EquipesB_Fanion' .
						'				,pronostics.Pronostics_ScoreEquipeA, pronostics.Pronostics_ScoreEquipeB' .
						'				,pronostics.Pronostics_ScoreAPEquipeA, pronostics.Pronostics_ScoreAPEquipeB' .
						'				,pronostics.Pronostics_Vainqueur' .
						'				,(' .
						'					SELECT		COUNT(*) AS NombreVictoires' .
						'					FROM		cdm_pronostics_sequencement resultats' .
						'					JOIN		cdm_pronostics_sequencement pronostics' .
						'								ON		(' .
						'											resultats.Equipes_EquipeA = pronostics.Equipes_EquipeA' .
						'											AND		resultats.Equipes_EquipeB = pronostics.Equipes_EquipeB' .
						'										)' .
						'										OR' .
						'										(' .
						'											resultats.Equipes_EquipeA = pronostics.Equipes_EquipeB' .
						'											AND		resultats.Equipes_EquipeB = pronostics.Equipes_EquipeA' .
						'										)' .
						'					LEFT JOIN	cdm_affiches_inversees ON pronostics.Pronostiqueurs_Pronostiqueur = cdm_affiches_inversees.Pronostiqueurs_Pronostiqueur' .
						'					WHERE		resultats.Pronostiqueurs_Pronostiqueur = 1' .
						'								AND		pronostics.Pronostiqueurs_Pronostiqueur <> 1' .
						'								AND		cdm_matches_phase_finale.Match = IFNULL(cdm_affiches_inversees.Pronostiqueurs_Matches_Match, pronostics.Matches_Match)' .
						'								AND		cdm_matches_phase_finale.Match = IFNULL(cdm_affiches_inversees.Pronostiqueurs_Matches_Match, pronostics.Matches_Match)' .
						'								AND		cdm_fn_vainqueur(pronostics.Pronostiqueurs_Pronostiqueur, pronostics.Matches_Match) = resultats.Equipes_EquipeA' .
						'				) AS Victoires_EquipeA' .
						'				,(' .
						'					SELECT		COUNT(*) AS NombreVictoires' .
						'					FROM		cdm_pronostics_sequencement resultats' .
						'					JOIN		cdm_pronostics_sequencement pronostics' .
						'								ON		(' .
						'											resultats.Equipes_EquipeA = pronostics.Equipes_EquipeA' .
						'											AND		resultats.Equipes_EquipeB = pronostics.Equipes_EquipeB' .
						'										)' .
						'										OR' .
						'										(' .
						'											resultats.Equipes_EquipeA = pronostics.Equipes_EquipeB' .
						'											AND		resultats.Equipes_EquipeB = pronostics.Equipes_EquipeA' .
						'										)' .
						'					WHERE		resultats.Pronostiqueurs_Pronostiqueur = 1' .
						'								AND		pronostics.Pronostiqueurs_Pronostiqueur <> 1' .
						'								AND		cdm_matches_phase_finale.Match IN (resultats.Matches_Match, cdm_fn_match_lie(resultats.Matches_Match))' .
						'								AND		cdm_matches_phase_finale.Match IN (pronostics.Matches_Match, cdm_fn_match_lie(pronostics.Matches_Match))' .
						'								AND		cdm_fn_vainqueur(pronostics.Pronostiqueurs_Pronostiqueur, pronostics.Matches_Match) = resultats.Equipes_EquipeB' .
						'				) AS Victoires_EquipeB' .
						'	FROM		cdm_matches_direct' .
						'	JOIN		cdm_matches_phase_finale' .
						'				ON		Matches_Match = cdm_matches_phase_finale.Match' .
						'	JOIN		cdm_pronostics_sequencement' .
						'				ON		cdm_matches_phase_finale.Match = cdm_pronostics_sequencement.Matches_Match' .
						'	JOIN		cdm_equipes equipesA' .
						'				ON		cdm_pronostics_sequencement.Equipes_EquipeA = equipesA.Equipe' .
						'	JOIN		cdm_equipes equipesB' .
						'				ON		cdm_pronostics_sequencement.Equipes_EquipeB = equipesB.Equipe' .
						'	LEFT JOIN	cdm_pronostics_phase_finale pronostics' .
						'				ON		cdm_matches_direct.Matches_Match = pronostics.Matches_Match' .
						'						AND		cdm_pronostics_sequencement.Pronostiqueurs_Pronostiqueur = pronostics.Pronostiqueurs_Pronostiqueur' .
						'	WHERE		cdm_pronostics_sequencement.Pronostiqueurs_Pronostiqueur = 1';

		$req = $bdd->query($ordreSQL);
		if($req) {
			$matches = $req->fetchAll();
			$nombreMatches = sizeof($matches);
			
			if($nombreMatches) {
				foreach($matches as $unMatch) {
					echo '<div class="entete">';
						echo '<div class="colle-gauche gauche equipeA">Victoire : ' . $unMatch["Victoires_EquipeA"] . '</div>';
						echo '<div class="gauche matchNul">&nbsp;</div>';
						echo '<div class="gauche equipeB">Victoire : ' . $unMatch["Victoires_EquipeB"] . '</div>';
					echo '</div>';
					echo '<div class="match">';
						echo '<div class="colle-gauche gauche drapeau"><img src="images/equipes/' . $unMatch["EquipesA_Fanion"] . '" alt="" /></div>';
						echo '<div class="gauche equipe"><label>' . $unMatch["EquipeA_Nom"] . '</label></div>';
						
						echo '<div class="gauche butInactif"></div>';
						
						$scoreEquipeA = $unMatch["Pronostics_ScoreEquipeA"];
						$scoreEquipeB = $unMatch["Pronostics_ScoreEquipeB"];
						$scoreAPEquipeA = $unMatch["Pronostics_ScoreAPEquipeA"];
						$scoreAPEquipeB = $unMatch["Pronostics_ScoreAPEquipeB"];
						$vainqueur = $unMatch["Pronostics_Vainqueur"];

						/*	En priorit�, on affiche :
							- le vainqueur des TAB (mention TAB � c�t� du score)
							- le perdant des TAB
							- le score AP
							- le score
						*/

						if($vainqueur != null) {
							if($vainqueur == -1)
								$scoreAffiche = $scoreAPEquipeA . ' AP - ' . $scoreAPEquipeB . ' AP';
							else if($vainqueur == $unMatch["EquipeA"])
								$scoreAffiche = $scoreAPEquipeA . ' TAB - ' . $scoreAPEquipeB;
							else if($vainqueur == $unMatch["EquipeB"])
								$scoreAffiche = $scoreAPEquipeA . ' - ' . $scoreAPEquipeB . ' TAB';
							else
								$scoreAffiche = 'TIRS AU BUT';
								
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
						
						if($administrateur == 1)
							echo '<div class="gauche score" onclick="module_directPhaseFinale_changerScoreMatch(' . $unMatch["Match"] . ');">' . $scoreAffiche . '</div>';
						else
							echo '<div class="gauche score">' . $scoreAffiche . '</div>';
						
						echo '<div class="gauche butInactif"></div>';
						
						echo '<div class="gauche equipe"><label>' . $unMatch["EquipeB_Nom"] . '</label></div>';
						echo '<div class="gauche drapeau"><img src="images/equipes/' . $unMatch["EquipesB_Fanion"] . '" alt="" /></div>';
					echo '</div>';
				}
			}
		}
		
		echo '<div id="divScoreMatch"></div>';
	?>

