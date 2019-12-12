<?php
	include_once('commun.php');

	// Page de vérification de la date d'événement ou de mise à jour de la dernière journée d'un championnat

	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;
	$dateMAJJournee = isset($_POST["dateMAJJournee"]) ? $_POST["dateMAJJournee"] : '';
	$dateEvenementJournee = isset($_POST["dateEvenementJournee"]) ? $_POST["dateEvenementJournee"] : '';

	$ordreSQL =		'	SELECT		Journees_DateMAJ, Journees_DateEvenement' .
					'	FROM		journees' .
					'	WHERE		Journee = ' . $journee;
	$req = $bdd->query($ordreSQL);
	$journees = $req->fetchAll();
	$dateMAJ = $journees[0]["Journees_DateMAJ"];
	$dateEvenement = $journees[0]["Journees_DateEvenement"];


	$tableau = array();
	// Comparaison avec les données qui viennent d'être lues et celles qui sont contenues en champs cachés dans la page
	if($dateMAJ != $dateMAJJournee || $dateEvenement != $dateEvenementJournee)
		$tableau['rafraichir'] = '1';
	else
		$tableau['rafraichir'] = '0';

	$tableau['dateMAJJournee'] = $dateMAJ;
	$tableau['dateEvenementJournee'] = $dateEvenement;

	echo json_encode($tableau);


?>