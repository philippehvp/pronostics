<?php
	include_once('commun_administrateur.php');

	// Inscription / désinscription d'un pronostiqueur à un championnat
	
	// Lecture des paramètres passés à la page
	$pronostiqueur = isset($_POST["pronostiqueur"]) ? $_POST["pronostiqueur"] : 0;
	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;
	$action = isset($_POST["action"]) ? $_POST["action"] : -1;

	if($action == -1)
		return;
	
	if($action == 1)
		$ordreSQL =		'	CALL		sp_inscriptionpronostiqueur(' . $pronostiqueur . ', ' . $championnat . ')';
	else if($action == 0)
		$ordreSQL =		'	CALL		sp_desinscriptionpronostiqueur(' . $pronostiqueur . ', ' . $championnat . ')';

	$bdd->exec($ordreSQL);

?>