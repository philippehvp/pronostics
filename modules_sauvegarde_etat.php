<?php
	include_once('commun.php');

	// Sauvegarde de la position d'un module
	$module = isset($_POST["module"]) ? $_POST["module"] : 0;
	$parametre = isset($_POST["parametre"]) ? $_POST["parametre"] : -1;
	$actif = isset($_POST["actif"]) ? $_POST["actif"] : 0;

	$ordreSQL =		'	UPDATE		modules_pronostiqueurs' .
					'	SET			ModulesPronostiqueurs_Actif = ' . $actif .
					'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'				AND		Modules_Module = ' . $module .
					'				AND		ModulesPronostiqueurs_Parametre = ' . $parametre;

	$bdd->exec($ordreSQL);


?>