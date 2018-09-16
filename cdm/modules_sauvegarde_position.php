<?php
	include('commun.php');

	// Sauvegarde de la position d'un module
	$module = isset($_POST["module"]) ? $_POST["module"] : 0;
	$x = isset($_POST["x"]) ? $_POST["x"] : 0;
	$y = isset($_POST["y"]) ? $_POST["y"] : 0;
	
	
	$ordreSQL =		'	UPDATE		cdm_pronostiqueurs_modules' .
					'	SET			PronostiqueursModules_X = ' . $x .
					'				,PronostiqueursModules_Y = ' . $y .
					'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"] .
					'				AND		Modules_Module = ' . $module;
					
	$bdd->exec($ordreSQL);
?>