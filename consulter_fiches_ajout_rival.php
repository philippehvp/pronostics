<?php
	include('commun.php');

	// Ajout ou suppression d'un pronostiqueur dans la liste des rivaux
	$pronostiqueurConsulte = isset($_POST["pronostiqueurConsulte"]) ? $_POST["pronostiqueurConsulte"] : 0;
	$mode = isset($_POST["mode"]) ? $_POST["mode"] : -1;

	if($mode == -1)
		return;

	if($mode == 1)
		$ordreSQL =		'	INSERT INTO		pronostiqueurs_rivaux(Pronostiqueur, PronostiqueursRivaux_Pronostiqueur)' .
						'	SELECT			' . $_SESSION["pronostiqueur"] . ', ' . $pronostiqueurConsulte;
	
	else if($mode == 0) 
		$ordreSQL =		'	DELETE FROM		pronostiqueurs_rivaux' .
						'	WHERE			Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'					AND		PronostiqueursRivaux_Pronostiqueur = ' . $pronostiqueurConsulte;

	
	$req = $bdd->exec($ordreSQL);
?>