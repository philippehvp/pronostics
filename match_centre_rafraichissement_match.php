<?php
	include('commun.php');
	
	// Page de vérification de la date d'événement ou de mise à jour de la dernière journée d'un championnat
	
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$date_maj_match = isset($_POST["date_maj_match"]) ? $_POST["date_maj_match"] : '';
	
	$ordreSQL =		'	SELECT		Matches_DateMAJ' .
					'	FROM		matches' .
					'	WHERE		matches.Match = ' . $match;
	$req = $bdd->query($ordreSQL);
	$matches = $req->fetchAll();
	$dateMAJ = $matches[0]["Matches_DateMAJ"];
	
	
	$tableau = array();
	// Comparaison avec les données qui viennent d'être lues et celles qui sont contenues en champs cachés dans la page
	if($dateMAJ != $date_maj_match)
		$tableau['rafraichir'] = '1';
	else
		$tableau['rafraichir'] = '0';
	
	$tableau['date_maj_match'] = $dateMAJ;
	
	echo json_encode($tableau);
	

?>