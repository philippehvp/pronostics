<?php
	include_once('commun.php');

	// Mise à jour du mode rival pour un module et un pronostiqueur
	$module = isset($_POST["module"]) ? $_POST["module"] : 0;
	$modeRival = isset($_POST["modeRival"]) ? $_POST["modeRival"] : 0;
	$parametre = isset($_POST["parametre"]) ? $_POST["parametre"] : 0;

	$ordreSQL =		'	UPDATE		modules_pronostiqueurs' .
					'	SET			ModulesPronostiqueurs_ModeRival = ' . $modeRival .
					'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'				AND		Modules_Module = ' . $module .
					'				AND		ModulesPronostiqueurs_Parametre = ' . $parametre;
	$req = $bdd->exec($ordreSQL);
?>