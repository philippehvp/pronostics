<?php
	include('commun.php');
	
	$championnat = isset($_GET["championnat"]) ? $_GET["championnat"] : 0;
	$pronostiqueur = isset($_GET["pronostiqueur"]) ? $_GET["pronostiqueur"] : $_SESSION["pronostiqueur"];
	
	// Lecture des classements au général des différentes journées
	$ordreSQL =		'	SELECT		Journees_Journee - Min_Journee + 1 AS J' .
					'				,Classements_ClassementGeneralMatch AS CG, Classements_ClassementJourneeMatch AS CJ' .
					'				,Classements_PointsGeneralMatch AS PG, Classements_PointsJourneeMatch AS PJ' .
					'	FROM		vue_classements_uniques' .
					'	JOIN		journees' .
					'				ON		vue_classements_uniques.Journees_Journee = journees.Journee' .
					'	JOIN		(' .
					'					SELECT		MIN(Journee) AS Min_Journee' .
					'					FROM		journees' .
					'					WHERE		Championnats_Championnat = ' . $championnat .
					'				) min_journees' .
					'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
					'				AND		Classements_ClassementGeneralMatch IS NOT NULL' .
					'				AND		journees.Championnats_Championnat = ' . $championnat .
					'	ORDER BY	Journees_Journee';
	$req = $bdd->query($ordreSQL);
	$classements = $req->fetchAll(PDO::FETCH_ASSOC);

	echo json_encode($classements);
?>