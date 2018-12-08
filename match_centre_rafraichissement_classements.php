<?php
	include_once('commun.php');
	
	// Page de vérification de la date de mise à jour d'une journée de championnat
	
	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;
	$date_maj_journee = isset($_POST["date_maj_journee"]) ? $_POST["date_maj_journee"] : '';
	
	$ordreSQL =		'	SELECT		Journees_DateMAJ' .
					'	FROM		journees' .
					'	WHERE		Journee = ' . $journee;
	$req = $bdd->query($ordreSQL);
	$journees = $req->fetchAll();
	$dateMAJ = $journees[0]["Journees_DateMAJ"];
	
	$tableau = array();
	// Comparaison avec les données qui viennent d'être lues et celles qui sont contenues en champs cachés dans la page
	if($dateMAJ != $date_maj_journee)
		$tableau['rafraichir'] = '1';
	else
		$tableau['rafraichir'] = '0';
	
	$tableau['date_maj_journee'] = $dateMAJ;
	
	echo json_encode($tableau);
	

?>