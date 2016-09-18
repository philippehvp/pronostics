<?php
	include('commun.php');
	
	// Affichage des classements comparés pour un championnat
	// Affichage du classement secondaire

	// Lecture des paramètres passés à la page
	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;
	$zoneDessinLargeur = isset($_POST["zoneDessinLargeur"]) ? $_POST["zoneDessinLargeur"] : 0;
	$zoneDessinHauteur = isset($_POST["zoneDessinHauteur"]) ? $_POST["zoneDessinHauteur"] : 0;
	$pronostiqueurConsulte = isset($_POST["pronostiqueurConsulte"]) ? $_POST["pronostiqueurConsulte"] : 0;
	$nombrePronostiqueurs = isset($_POST["nombrePronostiqueurs"]) ? $_POST["nombrePronostiqueurs"] : 0;
	$generalJournee = isset($_POST["generalJournee"]) ? $_POST["generalJournee"] : 1;

	// Création du graphique du pronostiqueur connecté
	// Meilleur et plus mauvais classements
	if($generalJournee == 1)
		$ordreSQL =		'	SELECT		MAX(Classements_ClassementGeneralMatch) AS Classement_Max' .
						'				,MIN(Classements_ClassementGeneralMatch) AS Classement_Min' .
						'	FROM		classements' .
						'	JOIN		journees' .
						'				ON		classements.Journees_Journee = journees.Journee' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'				AND		journees.Championnats_Championnat = ' . $championnat .
						'				AND		Classements_ClassementGeneralMatch IS NOT NULL';
	else
		$ordreSQL =		'	SELECT		MAX(Classements_ClassementJourneeMatch) AS Classement_Max' .
						'				,MIN(Classements_ClassementJourneeMatch) AS Classement_Min' .
						'	FROM		classements' .
						'	JOIN		journees' .
						'				ON		classements.Journees_Journee = journees.Journee' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'				AND		journees.Championnats_Championnat = ' . $championnat .
						'				AND		Classements_ClassementJourneeMatch IS NOT NULL';
	
	$req = $bdd->query($ordreSQL);
	$classementsMinEtMax = $req->fetchAll();
	$classementMin = $classementsMinEtMax[0]["Classement_Min"];
	$classementMax = $classementsMinEtMax[0]["Classement_Max"];

	// Classements occupés
	if($generalJournee == 1)
		/*$ordreSQL =		'	SELECT		Classements_ClassementGeneralMatch AS Valeur' .
						'	FROM		classements' .
						'	JOIN		journees' .
						'				ON		classements.Journees_Journee = journees.Journee' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'				AND		journees.Championnats_Championnat = ' . $championnat .
						'				AND		Classements_ClassementGeneralMatch IS NOT NULL' .
						'	ORDER BY	Journee';
	else
		$ordreSQL =		'	SELECT		Classements_ClassementJourneeMatch AS Valeur' .
						'	FROM		classements' .
						'	JOIN		journees' .
						'				ON		classements.Journees_Journee = journees.Journee' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'				AND		journees.Championnats_Championnat = ' . $championnat .
						'				AND		Classements_ClassementJourneeMatch IS NOT NULL' .
						'	ORDER BY	Journee';*/

		$ordreSQL =		'	SELECT		Classements_ClassementGeneralMatch AS Valeur' .
						'	FROM		(' .
						'					SELECT		classements.Journees_Journee, classements.Classements_ClassementGeneralMatch, classements.Pronostiqueurs_Pronostiqueur' .
						'					FROM		classements' .
						'					JOIN		(' .
						'									SELECT		Journees_Journee, MAX(Classements_DateReference) AS Classements_DateReference' .
						'									FROM		classements' .
						'									GROUP BY	Journees_Journee' .
						'								) classements_max' .
						'								ON		classements.Journees_Journee = classements_max.Journees_Journee' .
						'										AND		classements.Classements_DateReference = classements_max.Classements_DateReference' .
						'				) classements' .
						'	JOIN		journees' .
						'				ON		classements.Journees_Journee = journees.Journee' .
						'	WHERE		journees.Championnats_Championnat = ' . $championnat .
						'				AND		classements.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'	ORDER BY	classements.Journees_Journee';
	else
		$ordreSQL =		'	SELECT		Classements_ClassementJourneeMatch AS Valeur' .
						'	FROM		(' .
						'					SELECT		classements.Journees_Journee, classements.Classements_ClassementJourneeMatch, classements.Pronostiqueurs_Pronostiqueur' .
						'					FROM		classements' .
						'					JOIN		(' .
						'									SELECT		Journees_Journee, MAX(Classements_DateReference) AS Classements_DateReference' .
						'									FROM		classements' .
						'									GROUP BY	Journees_Journee' .
						'								) classements_max' .
						'								ON		classements.Journees_Journee = classements_max.Journees_Journee' .
						'										AND		classements.Classements_DateReference = classements_max.Classements_DateReference' .
						'				) classements' .
						'	JOIN		journees' .
						'				ON		classements.Journees_Journee = journees.Journee' .
						'	WHERE		journees.Championnats_Championnat = ' . $championnat .
						'				AND		classements.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'	ORDER BY	classements.Journees_Journee';
						
						

	$req = $bdd->query($ordreSQL);
	$classements = $req->fetchAll();
	$nombrePoints = sizeof($classements);

	$nomFichier = '';
	if($generalJournee == 1)
		$dossierImages = 'images/classements/general/';
	else
		$dossierImages = 'images/classements/journee/';
	
	// Effacement d'images qui pourraient exister dans ce dossier pour ce pronostiqueur
	foreach(glob($dossierImages . $championnat . '/' . $pronostiqueurConsulte . '_*.png') as $f) {
		unlink($f);
	}

	include('concours_centre_affichage_classements_creation_graphique_secondaire.php');
	echo '<img src="' . $nomFichier . '" alt="" />';

?>
