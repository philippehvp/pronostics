<?php
	include_once('commun.php');

	// Mise à jour du mode incrustation pour un module et un pronostiqueur
	$module = isset($_POST["module"]) ? $_POST["module"] : 0;
	$modeIncrustation = isset($_POST["modeIncrustation"]) ? $_POST["modeIncrustation"] : 0;
	$parametre = isset($_POST["parametre"]) ? $_POST["parametre"] : 0;

	$ordreSQL =		'	UPDATE		modules_pronostiqueurs' .
					'	SET			ModulesPronostiqueurs_ModeIncruste = ' . $modeIncrustation .
					'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'				AND		Modules_Module = ' . $module .
					'				AND		ModulesPronostiqueurs_Parametre = ' . $parametre;
	$req = $bdd->exec($ordreSQL);
?>