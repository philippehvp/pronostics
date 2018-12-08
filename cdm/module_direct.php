
	<?php
		// Cette page affiche les matches en direct
		
		$appelAjax = isset($_POST["appelAjax"]) ? 1 : 0;

		if($appelAjax == 1) {
			include_once('commun.php');
		}

		// Lecture des matches en direct
		$ordreSQL =		'	SELECT		cdm_matches_poule.Match, cdm_matches_poule.Poules_Poule' .
						'				,equipesA.Equipe AS EquipeA, equipesB.Equipe AS EquipeB' .
						'				,equipesA.Equipes_Nom AS EquipeA_Nom, equipesB.Equipes_Nom AS EquipeB_Nom' .
						'				,equipesA.Equipes_Fanion AS EquipesA_Fanion, equipesB.Equipes_Fanion AS EquipesB_Fanion' .
						'				,pronosticsA.PronosticsPoule_Score AS EquipeA_Score, pronosticsB.PronosticsPoule_Score AS EquipeB_Score' .
						'				,(' .
						'					SELECT		COUNT(*) AS NombreVictoires' .
						'					FROM		cdm_pronostics_poule equipesA' .
						'					JOIN		cdm_pronostics_poule equipesB' .
						'								ON		equipesA.Matches_Match = equipesB.Matches_Match' .
						'										AND		equipesA.Pronostiqueurs_Pronostiqueur = equipesB.Pronostiqueurs_Pronostiqueur' .
						'					WHERE		equipesA.Matches_Match = cdm_matches_poule.Match' .
						'								AND		equipesA.Equipes_Equipe = cdm_matches_poule.Equipes_EquipeA' .
						'								AND		equipesB.Equipes_Equipe = cdm_matches_poule.Equipes_EquipeB' .
						'								AND		equipesA.Pronostiqueurs_Pronostiqueur <> 1' .
						'								AND		equipesA.PronosticsPoule_Score > equipesB.PronosticsPoule_Score' .
							'			) AS Victoires_EquipeA' .
						'				,(' .
						'					SELECT		COUNT(*) AS NombreVictoires' .
						'					FROM		cdm_pronostics_poule equipesA' .
						'					JOIN		cdm_pronostics_poule equipesB' .
						'								ON		equipesA.Matches_Match = equipesB.Matches_Match' .
						'										AND		equipesA.Pronostiqueurs_Pronostiqueur = equipesB.Pronostiqueurs_Pronostiqueur' .
						'					WHERE		equipesA.Matches_Match = cdm_matches_poule.Match' .
						'								AND		equipesA.Equipes_Equipe = cdm_matches_poule.Equipes_EquipeA' .
						'								AND		equipesB.Equipes_Equipe = cdm_matches_poule.Equipes_EquipeB' .
						'								AND		equipesA.Pronostiqueurs_Pronostiqueur <> 1' .
						'								AND		equipesA.PronosticsPoule_Score < equipesB.PronosticsPoule_Score' .
						'				) AS Victoires_EquipeB' .
						'				,(' .
						'					SELECT		COUNT(*) AS NombreVictoires' .
						'					FROM		cdm_pronostics_poule equipesA' .
						'					JOIN		cdm_pronostics_poule equipesB' .
						'								ON		equipesA.Matches_Match = equipesB.Matches_Match' .
						'										AND		equipesA.Pronostiqueurs_Pronostiqueur = equipesB.Pronostiqueurs_Pronostiqueur' .
						'					WHERE		equipesA.Matches_Match = cdm_matches_poule.Match' .
						'								AND		equipesA.Equipes_Equipe = cdm_matches_poule.Equipes_EquipeA' .
						'								AND		equipesB.Equipes_Equipe = cdm_matches_poule.Equipes_EquipeB' .
						'								AND		equipesA.Pronostiqueurs_Pronostiqueur <> 1' .
						'								AND		equipesA.PronosticsPoule_Score = equipesB.PronosticsPoule_Score' .
						'				) AS Match_Nul' .
						'	FROM		cdm_matches_direct' .
						'	JOIN		cdm_matches_poule' .
						'				ON		Matches_Match = cdm_matches_poule.Match' .
						'	JOIN		cdm_equipes equipesA' .
						'				ON		cdm_matches_poule.Equipes_EquipeA = equipesA.Equipe' .
						'	JOIN		cdm_equipes equipesB' .
						'				ON		cdm_matches_poule.Equipes_EquipeB = equipesB.Equipe' .
						'	LEFT JOIN	cdm_pronostics_poule pronosticsA' .
						'				ON		cdm_matches_direct.Matches_Match = pronosticsA.Matches_Match' .
						'						AND		cdm_matches_poule.Equipes_EquipeA = pronosticsA.Equipes_Equipe' .
						'						AND		pronosticsA.Pronostiqueurs_Pronostiqueur = 1' .
						'	LEFT JOIN	cdm_pronostics_poule pronosticsB' .
						'				ON		cdm_matches_direct.Matches_Match = pronosticsB.Matches_Match' .
						'						AND		cdm_matches_poule.Equipes_EquipeB = pronosticsB.Equipes_Equipe' .
						'	WHERE		pronosticsA.Pronostiqueurs_Pronostiqueur = 1' .
						'				AND		pronosticsB.Pronostiqueurs_Pronostiqueur = 1';

		$req = $bdd->query($ordreSQL);
		if($req) {
			$matches = $req->fetchAll();
			$nombreMatches = sizeof($matches);
			
			if($nombreMatches) {
				foreach($matches as $unMatch) {
					echo '<div class="entete">';
						echo '<div class="colle-gauche gauche equipeA">Victoire : ' . $unMatch["Victoires_EquipeA"] . '</div>';
						echo '<div class="gauche matchNul">Nul : ' . $unMatch["Match_Nul"] . '</div>';
						echo '<div class="gauche equipeB">Victoire : ' . $unMatch["Victoires_EquipeB"] . '</div>';
					echo '</div>';
					echo '<div class="match">';
						echo '<div class="colle-gauche gauche drapeau"><img src="images/equipes/' . $unMatch["EquipesA_Fanion"] . '" alt="" /></div>';
						echo '<div class="gauche equipe"><label>' . $unMatch["EquipeA_Nom"] . '</label></div>';
						
						if($administrateur == 1)
							echo '<div class="gauche butActif" onclick="module_direct__marquerBut(' . $unMatch["Poules_Poule"] . ', ' . $unMatch["Match"] . ', ' . $unMatch["EquipeA"]  .', 1);"></div>';
						else
							echo '<div class="gauche butInactif"></div>';
						
						if($administrateur == 1) {
							echo '<input type="hidden" id="txtScoreEquipeA_' . $unMatch["Match"] . '" value="' . $unMatch["EquipeA_Score"] . '" />';
							echo '<input type="hidden" id="txtScoreEquipeB_' . $unMatch["Match"] . '" value="' . $unMatch["EquipeB_Score"] . '" />';
						}
						
						echo '<div class="gauche score">' . $unMatch["EquipeA_Score"] . ' - ' . $unMatch["EquipeB_Score"] . '</div>';
						
						if($administrateur == 1)
							echo '<div class="gauche butActif" onclick="module_direct__marquerBut(' . $unMatch["Poules_Poule"] . ', ' . $unMatch["Match"] . ', ' . $unMatch["EquipeB"]  .', 2);"></div>';
						else
							echo '<div class="gauche butInactif"></div>';
						
						echo '<div class="gauche equipe"><label>' . $unMatch["EquipeB_Nom"] . '</label></div>';
						echo '<div class="gauche drapeau"><img src="images/equipes/' . $unMatch["EquipesB_Fanion"] . '" alt="" /></div>';
					echo '</div>';
				}
			}
		}
	?>

