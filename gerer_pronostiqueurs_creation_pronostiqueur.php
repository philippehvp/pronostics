<?php
	include('commun_administrateur.php');

	// Création d'un nouveau pronostiqueur
	
	// Lecture des paramètres passés à la page
	$nomUtilisateur = isset($_POST["nom_utilisateur"]) ? $_POST["nom_utilisateur"] : '';
	$prenom = isset($_POST["prenom"]) ? $_POST["prenom"] : '';
	$nomFamille = isset($_POST["nom_famille"]) ? $_POST["nom_famille"] : '';
	$motDePasse = isset($_POST["mot_de_passe"]) ? $_POST["mot_de_passe"] : '';
	
  $ordreSQL =   ' CALL    sp_creationpronostiqueur('. $bdd->quote($nomUtilisateur) . ', ' . $bdd->quote($nomFamille) . ', ' . $bdd->quote($prenom) . ', ' . $bdd->quote($motDePasse) . ', NULL, NULL, 3)';

	$bdd->exec($ordreSQL);

?>