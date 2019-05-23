<?php
	include_once('commun.php');

	// Sauvegarde de l'état d'un groupe de modules
	$parametre = isset($_POST["parametre"]) ? $_POST["parametre"] : 0;
	$groupeActif = isset($_POST["groupe_actif"]) ? $_POST["groupe_actif"] : 0;
	
	$ordreSQL =		'	UPDATE		modules_groupes' .
					'	SET			ModulesGroupes_Actif = ' . $groupeActif .
					'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'				AND		ModulesGroupes_Parametre = ' . $parametre;

	$bdd->exec($ordreSQL);

?>