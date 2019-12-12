<?php
	include_once('commun.php');
	include_once('consulter_trophees_fonctions.php');

	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;
	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;

	// Affichage des trophées pour une journée en particulier

	// Lecture des informations d'une journée
	function lireJournee($championnat, $journee) {
		$ordreSQL =		'	SELECT		DISTINCT CASE' .
						'					WHEN	' . $journee . ' = Derniere_Journee' .
						'					THEN	' . $journee .
						'					ELSE	' . ($journee + 1) .
						'				END AS Journee_En_Cours' .
						'				,journees.Journees_Nom' .
						'				,journees.Journees_DateMAJ' .
						'	FROM		(' .
						'					SELECT		(	SELECT		MAX(Journee) AS Journee' .
						'									FROM		matches' .
						'									JOIN		journees' .
						'												ON		matches.Journees_Journee = journees.Journee' .
						'									WHERE		journees.Championnats_Championnat = ' . $championnat .
						'								) AS Derniere_Journee' .
						'					FROM		matches' .
						'					JOIN		journees' .
						'								ON matches.Journees_Journee = journees.Journee' .
						'					JOIN		championnats' .
						'								ON		journees.Championnats_Championnat = championnats.Championnat' .
						'					WHERE		Matches_Date <= NOW()' .
						'								AND matches.Matches_Report = 0' .
						'				) Journees_Max' .
						'	JOIN		journees' .
						'	WHERE		journees.Journee = ' . $journee;

		return $ordreSQL;
	}

	$ordreSQL = lireJournee($championnat, $journee);
	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetch();
	$journeeEnCours = $donnees["Journee_En_Cours"];
	$journeeNom = $donnees["Journees_Nom"];
	$dateMAJJournee = $donnees["Journees_DateMAJ"];
	$dtDateMAJ = new DateTime($dateMAJJournee);
	$req->closeCursor();

	afficherTrophees($bdd, $championnat, $journee, $dtDateMAJ, $journeeNom);

?>
