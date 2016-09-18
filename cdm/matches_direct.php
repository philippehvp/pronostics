<?php
	include('commun_administrateur.php');
	
	// Cette page affiche la liste des matches de la journée en cours
	// Ce qui permet de basculer un match en direct
	$ordreSQL =		'	SELECT		cdm_fn_journee_en_cours() AS Journee_En_Cours';

	$req = $bdd->query($ordreSQL);
	$journeeEnCours = 0;
	while($donnees = $req->fetch()) {
		$journeeEnCours = $donnees["Journee_En_Cours"];
	}
	$req->closeCursor();
	
	if($journeeEnCours) {
		if(/*$journeeEnCours >= 16*/1) {
			/* Matches en direct de la phase finale */
			$ordreSQL =		'	SELECT		cdm_matches_phase_finale.Match, equipesA.Equipes_NomCourt AS EquipesA_NomCourt, equipesB.Equipes_NomCourt AS EquipesB_NomCourt' .
							'				,CASE' .
							'					WHEN		cdm_matches_direct.Matches_Match IS NOT NULL' .
							'					THEN		1' .
							'					ELSE		0' .
							'				END AS En_Direct' .
							'	FROM		cdm_matches_phase_finale' .
							'	JOIN		cdm_pronostics_sequencement' .
							'				ON		cdm_matches_phase_finale.Match = cdm_pronostics_sequencement.Matches_Match' .
							'	JOIN		cdm_equipes equipesA' .
							'				ON		cdm_pronostics_sequencement.Equipes_EquipeA = equipesA.Equipe' .
							'	JOIN		cdm_equipes equipesB' .
							'				ON		cdm_pronostics_sequencement.Equipes_EquipeB = equipesB.Equipe' .
							'	LEFT JOIN	(' .
							'					SELECT		Matches_Match' .
							'					FROM		cdm_matches_direct' .
							'				) cdm_matches_direct' .
							'				ON		cdm_matches_phase_finale.Match = cdm_matches_direct.Matches_Match' .
							'	WHERE		cdm_pronostics_sequencement.Pronostiqueurs_Pronostiqueur = 1' .
							'	ORDER BY	cdm_matches_phase_finale.Matches_DateLocale, cdm_matches_phase_finale.Match';

		}
		else {
			/* Matches en direct de poule */
			$ordreSQL =		'	SELECT		cdm_matches_poule.Match, equipesA.Equipes_NomCourt AS EquipesA_NomCourt, equipesB.Equipes_NomCourt AS EquipesB_NomCourt' .
							'				,CASE' .
							'					WHEN		cdm_matches_direct.Matches_Match IS NOT NULL' .
							'					THEN		1' .
							'					ELSE		0' .
							'				END AS En_Direct' .
							'	FROM		cdm_matches_poule' .
							'	JOIN		cdm_equipes equipesA' .
							'				ON		cdm_matches_poule.Equipes_EquipeA = equipesA.Equipe' .
							'	JOIN		cdm_equipes equipesB' .
							'				ON		cdm_matches_poule.Equipes_EquipeB = equipesB.Equipe' .
							'	LEFT JOIN	(' .
							'					SELECT		Matches_Match' .
							'					FROM		cdm_matches_direct' .
							'				) cdm_matches_direct' .
							'				ON		cdm_matches_poule.Match = cdm_matches_direct.Matches_Match' .
							'	WHERE		cdm_matches_poule.Matches_JourneeEnCours IN (' . ($journeeEnCours - 1) . ', ' . $journeeEnCours . ', ' . ($journeeEnCours + 1) . ')' .
							'	ORDER BY	cdm_matches_poule.Matches_DateLocale, cdm_matches_poule.Match';
		}
		$req = $bdd->query($ordreSQL);
		$matches = $req->fetchAll();
		
		if(sizeof($matches)) {
			echo '<ul>';
				foreach($matches as $unMatch) {
					echo '<li onclick="matches_direct_basculer_match(' . $unMatch["Match"] . ');">' . $unMatch["EquipesA_NomCourt"] . ' - ' . $unMatch["EquipesB_NomCourt"] . ( $unMatch["En_Direct"] == 1 ? ' (en direct)' : '');
					echo '</li>';
				}
			echo '</ul>';
		}
		else
			echo '<ul><li>Aucun match</li></ul>';

	}
?>