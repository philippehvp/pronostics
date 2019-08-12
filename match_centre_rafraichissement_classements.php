<?php
	include_once('commun.php');
	
	// Page de vérification de la date de mise à jour d'une journée de championnat
	
	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;
	$dateMAJJournee = isset($_POST["dateMAJJournee"]) ? $_POST["dateMAJJournee"] : '';
	
	$ordreSQL =		'	SELECT		Journees_DateMAJ' .
					'	FROM		journees' .
					'	WHERE		Journee = ' . $journee;
	$req = $bdd->query($ordreSQL);
	$journees = $req->fetchAll();
	$dateMAJ = $journees[0]["Journees_DateMAJ"];
	
	$tableau = array();
	// Comparaison avec les données qui viennent d'être lues et celles qui sont contenues en champs cachés dans la page
	if($dateMAJ != $dateMAJJournee)
		$tableau['rafraichir'] = '1';
	else
		$tableau['rafraichir'] = '0';
	
	$tableau['dateMAJJournee'] = $dateMAJ;
	
	echo json_encode($tableau);
	

?>