<?php
	include('commun.php');

	// Sauvegarde de l'état d'affichage d'un module
	$module = isset($_POST["module"]) ? $_POST["module"] : 0;
	$etat = isset($_POST["etat"]) ? $_POST["etat"] : 0;
	
	
	$ordreSQL =		'	UPDATE		cdm_pronostiqueurs_modules' .
					'	SET			PronostiqueursModules_Actif = ' . $etat .
					'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"] .
					'				AND		Modules_Module = ' . $module;
					
	$bdd->exec($ordreSQL);
?>