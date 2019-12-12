<?php
	include_once('commun_administrateur.php');

	// Mise à jour de la fiche d'identité par l'administrateur
	$pronostiqueurConsulte = isset($_POST["pronostiqueurConsulte"]) ? $_POST["pronostiqueurConsulte"] : 0;

	// Lecture des paramètres passés à la page
	$palmares = isset($_POST["palmares"]) ? $_POST["palmares"] : '';
	$carriere = isset($_POST["carriere"]) ? $_POST["carriere"] : '';

	$ordreSQL =		'	UPDATE		pronostiqueurs' .
					'	SET			Pronostiqueurs_Palmares = ?' .
					'				,Pronostiqueurs_Carriere = ?' .
					'	WHERE		Pronostiqueur = ' . $pronostiqueurConsulte;

	$req = $bdd->prepare($ordreSQL);
	$req->execute(array($palmares, $carriere));

?>