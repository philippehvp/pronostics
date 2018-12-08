<?php
	include_once('commun.php');

	// Sauvegarde de la position d'un module
	$module = isset($_POST["module"]) ? $_POST["module"] : 0;
	$parametre = isset($_POST["parametre"]) ? $_POST["parametre"] : 0;
	$largeur = isset($_POST["largeur"]) ? $_POST["largeur"] : 0;
	$hauteur = isset($_POST["hauteur"]) ? $_POST["hauteur"] : 0;
	
	
	$ordreSQL =		'	UPDATE		modules_pronostiqueurs' .
					'	SET			ModulesPronostiqueurs_Largeur = ' . $largeur .
					'				,ModulesPronostiqueurs_Hauteur = ' . $hauteur .
					'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'				AND		Modules_Module = ' . $module .
					'				AND		ModulesPronostiqueurs_Parametre = ' . $parametre;
					
	$bdd->exec($ordreSQL);
?>