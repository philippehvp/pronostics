<?php
	include('commun.php');
	
	// Page de vérification de la date d'événement ou de mise à jour de la dernière journée d'un championnat
	
	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;
	$date_maj_journee = isset($_POST["date_maj_journee"]) ? $_POST["date_maj_journee"] : '';
	$date_evenement_journee = isset($_POST["date_evenement_journee"]) ? $_POST["date_evenement_journee"] : '';
	
	$ordreSQL =		'	SELECT		Journees_DateMAJ, Journees_DateEvenement' .
					'	FROM		journees' .
					'	WHERE		Journee = ' . $journee;
	$req = $bdd->query($ordreSQL);
	$journees = $req->fetchAll();
	$dateMAJ = $journees[0]["Journees_DateMAJ"];
	$dateEvenement = $journees[0]["Journees_DateEvenement"];
	
	
	$tableau = array();
	// Comparaison avec les données qui viennent d'être lues et celles qui sont contenues en champs cachés dans la page
	if($dateMAJ != $date_maj_journee || $dateEvenement != $date_evenement_journee)
		$tableau['rafraichir'] = '1';
	else
		$tableau['rafraichir'] = '0';
	
	$tableau['date_maj_journee'] = $dateMAJ;
	$tableau['date_evenement_journee'] = $dateEvenement;
	
	echo json_encode($tableau);
	

?>