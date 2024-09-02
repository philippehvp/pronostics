<?php
	include_once('commun.php');

	function verifierAutorisationAjout($bdd, $pronostiqueur, $championnat, $phase) {
		$ordreSQL =	'	SELECT		fn_autorisationajoutqualification(' .
					'					' . $pronostiqueur . ', ' .
					'					' . $championnat . ', ' .
					'					' . $phase .
					'				) AS Ajout_Autorise';
		$req = $bdd->query($ordreSQL);
		
		$nombreEquipesQualification = $req->fetchAll();
        $ajoutAutorise = $nombreEquipesQualification[0]["Ajout_Autorise"];
		return $ajoutAutorise;
	}
	
	function sauvegarderPronostic($bdd, $pronostiqueur, $equipe, $championnat, $phase) {
		$ordreSQL =		'	REPLACE INTO	pronostics_phase(' .
						'						Pronostiqueurs_Pronostiqueur' .
						'						,Equipes_Equipe' .
						'						,Championnats_Championnat' .
						'						,PronosticsPhase_Qualification' .
						'					)' .
						'	SELECT			' . $pronostiqueur .
						'					,' . $equipe .
						'					,' . $championnat .
						'					,' . $phase .
						'	FROM			qualifications_date_max' .
						'	WHERE			NOW() < qualifications_date_max.Qualifications_Date_Max' .
						'					AND		qualifications_date_max.Championnats_Championnat = ' . $championnat;
		$bdd->exec($ordreSQL);

		lireNombreEquipesPronostiqueesPhaseQualification($bdd, $pronostiqueur, $championnat);
	}

	function lireNombreEquipesPronostiqueesPhaseQualification($bdd, $pronostiqueur, $championnat) {
		$ordreSQL =	'	SELECT		fn_nombreequipesphasequalification(' .
					'					' . $pronostiqueur . ', ' .
					'					' . $championnat .
					'				) AS Nombre';
		$req = $bdd->query($ordreSQL);
		
		$nombreEquipesQualification = $req->fetchAll();
        $nombre = $nombreEquipesQualification[0]["Nombre"];

		$tableau = array();
		$tableau["ajoutAutorise"] = 1;
		$tableau["phaseComplete"] = $nombre == 36 ? 1 : 0;
		$tableau["message"] = $nombre == 36 ? 'Tous vos pronostics ont été saisis et sauvegardés avec succès' : '';
		echo json_encode($tableau);
	}

	// Sauvegarde du pronostic de phase de qualification

	// Lecture des paramètres passés à la page
	$pronostiqueur = $_SESSION["pronostiqueur"];
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;
	$phase = isset($_POST["phase"]) ? $_POST["phase"] : 0;

	// On vérifie auparavant qu'il soit possible de mettre à jour la phase (nombre d'équipes max non atteint)
	// Uniquement dans le cas où la phase vaut 1, 2 ou 3
	// La phase vaut 0 lorsque le pronostiqueur efface son pronostic, ce qu'il a toujours le droit de faire
	if($phase != 0) {
		$ajoutAutorise = verifierAutorisationAjout($bdd, $pronostiqueur, $championnat, $phase);

		if ($ajoutAutorise) {
			sauvegarderPronostic($bdd, $pronostiqueur, $equipe, $championnat, $phase);
		} else {
			$tableau = array();
			$tableau["ajoutAutorise"] = 0;
			$tableau["message"] = "Le nombre maximal d'équipes a été atteint";
			echo json_encode($tableau);
		}

	} else {
		// Remise à zéro du pronostic de phase de qualification
		sauvegarderPronostic($bdd, $pronostiqueur, $equipe, $championnat, $phase);
	}


?>