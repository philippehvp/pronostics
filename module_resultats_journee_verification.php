<?php
	// Module d'affichage des résultats d'une journée
	// Vérification de la nécessité de faire le rafraîchissement ou non
	
	// La page est forcément appelée par de l'Ajax
	include_once('commun.php');
	include_once('classements_pronostiqueurs_fonctions.php');
		
	// Lecture des paramètres passés à la page
	$championnat = isset($_POST["parametre"]) ? $_POST["parametre"] : 0;
	$critereRafraichissement = isset($_POST["critereRafraichissement"]) ? $_POST["critereRafraichissement"] : '';
	
	// Lecture des informations de la journée en cours
	$ordreSQL = lireJournee($championnat);
	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetch();
	$dateEvenement = $donnees["Journees_DateEvenement"];
	$req->closeCursor();
	
	$tableau = array();
	// Le crtitère de rafraîchissement indique la même date que celle de la mise à jour des données
	// Il n'est donc pas nécessaire de continuer le traitement
	if($critereRafraichissement == $dateEvenement)
		$tableau['rafraichir'] = '0';
	else {
		$tableau['rafraichir'] = '1';
		$tableau['critereRafraichissement'] = $dateEvenement;
	}
	
	echo json_encode($tableau);
?>