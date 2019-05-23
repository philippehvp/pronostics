<?php
	include_once('commun.php');

	// Sauvegarde de la position d'un module
	$module = isset($_POST["module"]) ? $_POST["module"] : 0;
	$parametre = isset($_POST["parametre"]) ? $_POST["parametre"] : 0;
	$x = isset($_POST["x"]) ? $_POST["x"] : 0;
	$y = isset($_POST["y"]) ? $_POST["y"] : 0;
	
	
	$ordreSQL =		'	UPDATE		modules_pronostiqueurs' .
					'	SET			ModulesPronostiqueurs_X = ' . $x .
					'				,ModulesPronostiqueurs_Y = ' . $y .
					'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'				AND		Modules_Module = ' . $module .
					'				AND		ModulesPronostiqueurs_Parametre = ' . $parametre;
					

	$bdd->exec($ordreSQL);
?>