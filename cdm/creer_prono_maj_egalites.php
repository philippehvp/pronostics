<?php
	include('commun.php');
	
	// Mise à jour des équipes à égalité (le pronostiqueur choisit un classement basé sur un tirage au sort)
	
	$equipes = isset($_POST["equipes"]) ? $_POST["equipes"] : 0;
	$poule = isset($_POST["poule"]) ? $_POST["poule"] : 0;
	
	for($i = 0; $i < $equipes; $i++) {
		$equipe = isset($_POST["equipe" . $i]) ? $_POST["equipe" . $i] : 0;
		$classement = isset($_POST["classement" . $i]) ? $_POST["classement" . $i] : 0;

		$ordreSQL =		'	UPDATE		cdm_pronostics_poule_classements' .
						'	SET			PronosticsPouleClassements_Classement = ' . $classement .
						'				,PronosticsPouleClassements_ClassementTirage = NULL' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'				AND		Poules_Poule = ' . $poule .
						'				AND		Equipes_Equipe = ' . $equipe;

		$bdd->exec($ordreSQL);
	}
	
	// Effacement des égalités de la poule
	$ordreSQL =		'	DELETE FROM		cdm_pronostics_poule_egalites' .
					'	WHERE			Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'					AND		Poules_Poule = ' . $poule;

	$bdd->exec($ordreSQL);
	
	// Calcul des nouveaux qualifiés pour les 1/8 de finale
	$ordreSQL = 'CALL cdm_sp_calcul_sorties_poule(' . $_SESSION["pronostiqueur"] . ', ' . $poule . ')';
	$bdd->exec($ordreSQL);
	
	
?>