<?php
	include('commun.php');
	include_once('classements_pronostiqueurs_fonctions.php');
	
	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;
	$dateReference = isset($_POST["date_reference"]) ? $_POST["date_reference"] : 0;
	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;
	$sansButeur = isset($_POST["sans_buteur"]) ? $_POST["sans_buteur"] : 0; 

	// Affichage des classements pour une journée en particulier
	
	$ordreSQL = lireUneJournee($championnat, $journee);
	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetch();
	$journeeEnCours = $donnees["Journee_En_Cours"];
	$journeeNom = $donnees["Journees_Nom"];
	$dateMAJJournee = $donnees["Journees_DateMAJ"];
	$dtDateMAJ = new DateTime($dateMAJJournee);
	$req->closeCursor();

	$affichageClassementButeur = 1;
	$affichageNeutre = 0;
	$affichageJourneeSuivante = 0;		// On ne doit jamais afficher la journée suivante
	afficherClassements($bdd, $championnat, $journee, $dateReference, $dtDateMAJ, $journeeNom, $affichageClassementButeur, $affichageNeutre, $affichageJourneeSuivante, $sansButeur);

?>