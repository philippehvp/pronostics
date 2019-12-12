<?php
	include_once('commun_administrateur.php');

	// Suppression d'un pronostiqueur de la table des pronostiqueurs

	// Lecture des paramètres passés à la page
	$pronostiqueur = isset($_POST["pronostiqueur"]) ? $_POST["pronostiqueur"] : 0;
	$deplacement = isset($_POST["deplacement"]) ? $_POST["deplacement"] : 1;

	if($pronostiqueur == 0)
		return;

	$ordreSQL =		'	CALL		sp_effacementpronostiqueur(' . $pronostiqueur . ' , ' . $deplacement . ')';

	$bdd->exec($ordreSQL);

?>