<?php
	include_once('commun.php');

	// Mise à jour du mode concurrent direct pour un module et un pronostiqueur
	$module = isset($_POST["module"]) ? $_POST["module"] : 0;
	$modeConcurrentDirect = isset($_POST["modeConcurrentDirect"]) ? $_POST["modeConcurrentDirect"] : 0;
	$parametre = isset($_POST["parametre"]) ? $_POST["parametre"] : 0;
	
	$ordreSQL =		'	UPDATE		modules_pronostiqueurs' .
					'	SET			ModulesPronostiqueurs_ModeConcurrentDirect = ' . $modeConcurrentDirect .
					'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'				AND		Modules_Module = ' . $module .
					'				AND		ModulesPronostiqueurs_Parametre = ' . $parametre;
	$req = $bdd->exec($ordreSQL);
?>