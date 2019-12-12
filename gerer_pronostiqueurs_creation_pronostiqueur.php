<?php
	include_once('commun_administrateur.php');

	// Création d'un nouveau pronostiqueur

	// Lecture des paramètres passés à la page
	$nomUtilisateur = isset($_POST["nomUtilisateur"]) ? $_POST["nomUtilisateur"] : '';
	$prenom = isset($_POST["prenom"]) ? $_POST["prenom"] : '';
	$nomFamille = isset($_POST["nomFamille"]) ? $_POST["nomFamille"] : '';
	$motDePasse = isset($_POST["motDePasse"]) ? $_POST["motDePasse"] : '';

  $ordreSQL =   ' CALL    sp_creationpronostiqueur('. $bdd->quote($nomUtilisateur) . ', ' . $bdd->quote($nomFamille) . ', ' . $bdd->quote($prenom) . ', ' . $bdd->quote($motDePasse) . ', NULL, NULL, 3)';

	$bdd->exec($ordreSQL);

?>