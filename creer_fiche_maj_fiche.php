<?php
	include('commun.php');

	// Mise à jour de la fiche d'identité
	
	// Lecture des paramètres passés à la page
	$nom = isset($_POST["nom"]) ? $_POST["nom"] : '';
	$prenom = isset($_POST["prenom"]) ? $_POST["prenom"] : '';
	$mel = isset($_POST["mel"]) ? $_POST["mel"] : '';
	$dateDeNaissance = isset($_POST["dateDeNaissance"]) ? $_POST["dateDeNaissance"] : '';
	$lieuDeResidence = isset($_POST["lieuDeResidence"]) ? $_POST["lieuDeResidence"] : '';
	$equipeFavorite = isset($_POST["equipeFavorite"]) ? $_POST["equipeFavorite"] : '';
	$ambitions = isset($_POST["ambitions"]) ? $_POST["ambitions"] : '';
	$carriere = isset($_POST["carriere"]) ? $_POST["carriere"] : '';
	$commentaire = isset($_POST["commentaire"]) ? $_POST["commentaire"] : '';
	
	$ordreSQL =		'	UPDATE		pronostiqueurs' .
					'	SET			Pronostiqueurs_Nom = ?' .
					'				,Pronostiqueurs_Prenom = ?' .
					'				,Pronostiqueurs_MEL = ?' .
					'				,Pronostiqueurs_DateDeNaissance = STR_TO_DATE(?, \'%d/%m/%Y\')' .
					'				,Pronostiqueurs_LieuDeResidence = ?' .
					'				,Pronostiqueurs_EquipeFavorite = ?' .
					'				,Pronostiqueurs_Ambitions = ?' .
					'				,Pronostiqueurs_Carriere = ?' .
					'				,Pronostiqueurs_Commentaire = ?' .
					'	WHERE		Pronostiqueur = ' . $_SESSION["pronostiqueur"];
					
	$req = $bdd->prepare($ordreSQL);
	$req->execute(array($nom, $prenom, $mel, $dateDeNaissance, $lieuDeResidence, $equipeFavorite, $ambitions, $carriere, $commentaire));
?>