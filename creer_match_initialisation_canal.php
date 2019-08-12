<?php
	include_once('commun_administrateur.php');
	
	// Initialisation du match Canal pour les pronostiquers pour une journée
	// On vérifie que la journée a la possibilité d'accueillir les matches Canal
	
	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;

	$ordreSQL =		'	SELECT		journees.Journees_MatchCanalSelectionnable' .
					'	FROM		journees' .
					'	WHERE		journees.Journee = ' . $journee;
	$req = $bdd->query($ordreSQL);
	$journees = $req->fetchAll();
	if(sizeof($journees) != 0) {
		$matchCanalSelectionnable = $journees[0]["Journees_MatchCanalSelectionnable"];
	} else {
		return;
	}
	
	if($matchCanalSelectionnable == 1) {
		$ordreSQL =		'	REPLACE INTO	journees_pronostiqueurs_canal' .
						'	SELECT			' . $journee . ', pronostiqueurs.Pronostiqueur' .
						'					,(' .
						'						SELECT		matches.Match' .
						'						FROM		matches' .
						'						WHERE		matches.Journees_Journee = ' . $journee .
						'									AND		matches.Matches_Coefficient = 2' .
						'					) AS Matches_Match' .
						'	FROM			pronostiqueurs' .
						'	LEFT JOIN		journees_pronostiqueurs_canal' .
						'					ON		journees_pronostiqueurs_canal.Journees_Journee = ' . $journee .
						'							AND		pronostiqueurs.Pronostiqueur = journees_pronostiqueurs_canal.Pronostiqueurs_Pronostiqueur' .
						'	WHERE			journees_pronostiqueurs_canal.Journees_Journee IS NULL';
		$bdd->exec($ordreSQL);
	}
?>

