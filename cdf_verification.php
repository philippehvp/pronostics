<?php
	// Coupe de France
	// Vérification de la nécessité de faire le rafraîchissement ou non
	
	// La page est forcément appelée par de l'Ajax
	include_once('commun.php');
	
	// Lecture des paramètres passés à la page
	$critereRafraichissement = isset($_POST["critereRafraichissement"]) ? $_POST["critereRafraichissement"] : '';
	
	
	// Recherche de la journée en cours
	$ordreSQL =		'	SELECT		MAX(Journees_DateEvenement) AS Journees_DateEvenement' .
					'	FROM		journees' .
					'	WHERE		Championnats_Championnat = 5';
	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetchAll();
	$dateEvenement = $donnees[0]["Journees_DateEvenement"];
	
	$tableau = array();
	// Le crtitère de rafraîchissement indique la même date que celle de la mise à jour des données
	// Il n'est donc pas nécessaire de continuer le traitement
	if($dateEvenement == null || $critereRafraichissement != $dateEvenement) {
		$tableau['rafraichir'] = '1';
		$tableau['critereRafraichissement'] = $dateEvenement;
	}
	else
		$tableau['rafraichir'] = '0';

	echo json_encode($tableau);
?>