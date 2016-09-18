<?php
	include('commun.php');

	// Affichage de l'en-tête d'un pronostiqueur
	// Lecture des paramètres passés à la page
	$pronostiqueurConsulte = isset($_POST["pronostiqueurConsulte"]) ? $_POST["pronostiqueurConsulte"] : 0;
	
	// Affichage du nom du pronostiqueur
	$ordreSQL =		'	SELECT		Pronostiqueurs_NomUtilisateur, Pronostiqueurs_Nom, Pronostiqueurs_Prenom' .
					'	FROM		pronostiqueurs' .
					'	WHERE		pronostiqueurs.Pronostiqueur = ' . $pronostiqueurConsulte;
	$req = $bdd->query($ordreSQL);
	$fiche = $req->fetchAll();
	
	$pronostiqueurNomUtilisateur = $fiche[0]["Pronostiqueurs_NomUtilisateur"] != null ? $fiche[0]["Pronostiqueurs_NomUtilisateur"] : '';
	$pronostiqueurNom = $fiche[0]["Pronostiqueurs_Nom"] != null ? $fiche[0]["Pronostiqueurs_Nom"] : '';
	$pronostiqueurPrenom = $fiche[0]["Pronostiqueurs_Prenom"] != null ? $fiche[0]["Pronostiqueurs_Prenom"] : '';
	
	echo '<div class="colle-gauche">';
		echo '<label class="cc--pronostiqueurs-detail--nom">' . $pronostiqueurNomUtilisateur . ' (' . $pronostiqueurPrenom . ' ' . $pronostiqueurNom . ')</label>';
	echo '</div>';
?>