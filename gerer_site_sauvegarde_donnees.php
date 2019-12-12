<?php
	include_once('commun_administrateur.php');

	// Sauvegarde des données de la saison vers les tables d'archive

	// Lecture des paramètres passés à la page
	$saison = isset($_POST["saison"]) ? $_POST["saison"] : 0;

	if($saison == 0)
		return;

	$ordreSQL =		'	CALL		sp_sauvegardedonnees(' . $saison . ')';

	$bdd->exec($ordreSQL);

?>