<?php
	include('commun.php');

	// Mise à jour d'un score de match de phase finale

	// Affichage des informations de l'équipe (nom, fanion)
	function afficherEquipe($unMatch, $equipeAB) {
		$nomEquipe = $equipeAB == 'A' ? $unMatch["EquipeA_Nom"] : $unMatch["EquipeB_Nom"];
		$fanion = $equipeAB == 'A' ? $unMatch["EquipeA_Fanion"] : $unMatch["EquipeB_Fanion"];
		if($fanion == null)
			$fanion = '_inconnu.png';
	
		echo '<label>' . $nomEquipe . '</label>';
		echo '<br />';
		echo '<img src="images/equipes/' . $fanion . '" alt="Fanion" />';
	}
	
	// Affichage des scores de l'équipe
	function afficherScoreEquipe($unMatch, $equipeAB) {
		if($_SESSION["pronostiqueur"] != 1 && time() > 1528977600)
			$disabled = ' disabled';
		else
			$disabled = '';
		
		if($equipeAB == 'A')
			$scoreEquipe = $unMatch["Pronostics_ScoreEquipeA"] != null ? $unMatch["Pronostics_ScoreEquipeA"] : -1;
		else
			$scoreEquipe = $unMatch["Pronostics_ScoreEquipeB"] != null ? $unMatch["Pronostics_ScoreEquipeB"] : -1;

		echo '<select id="selectScoreEquipe' . $equipeAB . '" onchange="creerProno_sauvegarderScoreMatch(this, \'score\', ' . $unMatch["Matches_Match"] . ', \'' . $equipeAB . '\');" id="selectButs' . $equipeAB . '_match_' . $unMatch["Matches_Match"] . '"' . $disabled . '>';
			for($i = -1; $i <= 15; $i++) {
				$selected = $i == $scoreEquipe ? ' selected="selected"' : '';
				echo '<option value="' . $i . '"' . $selected . '>' . ($i == -1 ? 'Score' : $i) . '</option>';
			}
		echo '</select>';
	}

	// Affichage des scores AP de l'équipe
	function afficherScoreAPEquipe($unMatch, $equipeAB) {
		$style = ' style="visibility: hidden;"';
		if($_SESSION["pronostiqueur"] != 1 && time() > 1528977600)
			$disabled = ' disabled';
		else
			$disabled = '';
			
		if($equipeAB == 'A')
			$scoreAPEquipe = $unMatch["Pronostics_ScoreAPEquipeA"] != null ? $unMatch["Pronostics_ScoreAPEquipeA"] : -1;
		else
			$scoreAPEquipe = $unMatch["Pronostics_ScoreAPEquipeB"] != null ? $unMatch["Pronostics_ScoreAPEquipeB"] : -1;
			
		$pronostics_ScoreEquipeA = $unMatch["Pronostics_ScoreEquipeA"] != null ? $unMatch["Pronostics_ScoreEquipeA"] : -1;
		$pronostics_ScoreEquipeB = $unMatch["Pronostics_ScoreEquipeB"] != null ? $unMatch["Pronostics_ScoreEquipeB"] : -1;
		if($pronostics_ScoreEquipeA != -1 && $pronostics_ScoreEquipeB != -1)
			if($pronostics_ScoreEquipeA == $pronostics_ScoreEquipeB)
				$style = '';

		$scoreAPMinimumEquipe = $equipeAB == 'A' ? $pronostics_ScoreEquipeA : $pronostics_ScoreEquipeB;
				
		echo '<span id="spanProlongation' . $equipeAB . '_match_' . $unMatch["Matches_Match"] . '"' . $style . '>';
			echo '<select id="selectScoreAPEquipe' . $equipeAB . '" onchange="creerProno_sauvegarderScoreMatch(this, \'scoreAP\', ' . $unMatch["Matches_Match"] . ', \'' . $equipeAB . '\');" id="selectButsAP' . $equipeAB . '_match_' . $unMatch["Matches_Match"] . '"' . $disabled . '>';
				for($i = $scoreAPMinimumEquipe; $i <= 15; $i++) {
					$selected = $i == $scoreAPEquipe ? ' selected="selected"' : '';
					echo '<option value="' . $i . '"' . $selected . '>' . ($i == -1 ? 'Score' : $i) . '</option>';
				}
			echo '</select>';
		echo '</span>';
	}

	// Affichage de la zone TAB
	function afficherTAB($unMatch) {
		// TAB
		$style = ' style="visibility: hidden;"';
		if($_SESSION["pronostiqueur"] != 1 && time() > 1528977600)
			$disabled = ' disabled';
		else
			$disabled = '';
		$pronostics_ScoreEquipeA = $unMatch["Pronostics_ScoreEquipeA"] != null ? $unMatch["Pronostics_ScoreEquipeA"] : -1;
		$pronostics_ScoreEquipeB = $unMatch["Pronostics_ScoreEquipeB"] != null ? $unMatch["Pronostics_ScoreEquipeB"] : -1;
		$pronostics_ScoreAPEquipeA = $unMatch["Pronostics_ScoreAPEquipeA"] != null ? $unMatch["Pronostics_ScoreAPEquipeA"] : -1;
		$pronostics_ScoreAPEquipeB = $unMatch["Pronostics_ScoreAPEquipeB"] != null ? $unMatch["Pronostics_ScoreAPEquipeB"] : -1;
		$pronostics_Vainqueur = $unMatch["Pronostics_Vainqueur"];

		if($pronostics_ScoreEquipeA != -1 && $pronostics_ScoreEquipeB != -1)
			if($pronostics_ScoreEquipeA == $pronostics_ScoreEquipeB)
				if($pronostics_ScoreAPEquipeA != -1 && $pronostics_ScoreAPEquipeB != -1)
					if($pronostics_ScoreAPEquipeA == $pronostics_ScoreAPEquipeB)
						$style = '';

		echo '<span id="spanVainqueur_match_' . $unMatch["Matches_Match"] . '"' . $style . '>';
			echo '<select id="selectVainqueur" onchange="creerProno_sauvegarderScoreMatch(this, \'vainqueur\',' . $unMatch["Matches_Match"] . ', \'V\');" id="selectVainqueur_match_' . $unMatch["Matches_Match"] . '"' . $disabled . '>';
				$selected0 = $pronostics_Vainqueur == null ? ' selected="selected"' : '';
				$selected1 = $pronostics_Vainqueur == $unMatch["EquipeA"] ? ' selected="selected"' : '';
				$selected2 = $pronostics_Vainqueur == $unMatch["EquipeB"] ? ' selected="selected"' : '';
				echo '<option value="0"' . $selected0 . '>Vainqueur</option>';

				echo '<option value="' . $unMatch["EquipeA"] . '"' . $selected1 . '>' . $unMatch["EquipeA_Nom"] . '</option>';
				
				echo '<option value="' . $unMatch["EquipeB"] . '"' . $selected2 . '>' . $unMatch["EquipeB_Nom"] . '</option>';
			echo '</select>';
		echo '</span>';
	}

	
	if($_SESSION["pronostiqueur"] != 1 && time() > 1528977600) {
		echo 'Désolé, il n\'est plus possible d\'effectuer de pronostic';
		exit();
	}
	
	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	//$ordre = isset($_POST["ordre"]) ? $_POST["ordre"] : 0;

	$ordreSQL =		'	SELECT		cdm_pronostics_sequencement.Matches_Match' .
					'				,equipesA.Equipe AS EquipeA' .
					'				,equipesB.Equipe AS EquipeB' .
					'				,equipesA.Equipes_Nom AS EquipeA_Nom' .
					'				,equipesB.Equipes_Nom AS EquipeB_Nom' .
					'				,equipesA.Equipes_Fanion AS EquipeA_Fanion' .
					'				,equipesB.Equipes_Fanion AS EquipeB_Fanion' .
					'				,cdm_pronostics_phase_finale.Pronostics_ScoreEquipeA' .
					'				,cdm_pronostics_phase_finale.Pronostics_ScoreEquipeB' .
					'				,cdm_pronostics_phase_finale.Pronostics_ScoreAPEquipeA' .
					'				,cdm_pronostics_phase_finale.Pronostics_ScoreAPEquipeB' .
					'				,cdm_pronostics_phase_finale.Pronostics_Vainqueur' .
					'	FROM		cdm_pronostics_sequencement' .
					'	LEFT JOIN	cdm_equipes equipesA' .
					'				ON		cdm_pronostics_sequencement.Equipes_EquipeA = equipesA.Equipe' .
					'	LEFT JOIN	cdm_equipes equipesB' .
					'				ON		cdm_pronostics_sequencement.Equipes_EquipeB = equipesB.Equipe' .
					'	LEFT JOIN	cdm_pronostics_phase_finale' .
					'				ON		cdm_pronostics_sequencement.Pronostiqueurs_Pronostiqueur = cdm_pronostics_phase_finale.Pronostiqueurs_Pronostiqueur' .
					'						AND		cdm_pronostics_sequencement.Matches_Match = cdm_pronostics_phase_finale.Matches_Match' .
					'	WHERE		cdm_pronostics_sequencement.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'				AND		cdm_pronostics_sequencement.Matches_Match = ' . $match .
					'	LIMIT		1';

	$req = $bdd->query($ordreSQL);
	while($unMatch = $req->fetch()) {
		echo '<div class="colle-gauche gauche equipe">';
			echo '<div>';
				afficherEquipe($unMatch, 'A');
			echo '</div>';
			echo '<div>';
				afficherScoreEquipe($unMatch, 'A');
			echo '</div>';
			echo '<div>';
				afficherScoreAPEquipe($unMatch, 'A');
			echo '</div>';
		echo '</div>';
		
		echo '<div class="gauche equipe">';
			echo '<div>';
				afficherEquipe($unMatch, 'B');
			echo '</div>';
			echo '<div>';
				afficherScoreEquipe($unMatch, 'B');
			echo '</div>';
			echo '<div>';
				afficherScoreAPEquipe($unMatch, 'B');
			echo '</div>';
		echo '</div>';
		
		echo '<div class="vainqueur colle-gauche">';
			afficherTAB($unMatch);
		echo '</div>';
	}
	$req->closeCursor();


?>
