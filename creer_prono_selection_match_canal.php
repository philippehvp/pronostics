<?php
	include_once('commun.php');

	// Mise à jour du match Canal d'une journée

    // Lecture des paramètres passés à la page
    $journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	
	// On vérifie avant tout qu'il n'est pas trop tard pour faire la modification
	$ordreSQL =		'	SELECT		fn_matchpronostiquable(matches.Match, ' . $pronostiqueur . ') AS Matches_Pronostiquable' .
					'				,fn_matchcanalmodifiable(matches.Match, ' . $pronostiqueur . ') AS Matches_MatchCanalModifiable' .
                    '	FROM		matches' .
                    '	JOIN		journees' .
                    '				ON		matches.Journees_Journee = journees.Journee' .
                    '	WHERE		matches.Match = ' . $match;

	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetch();
	$matchPronostiquable = $donnees["Matches_Pronostiquable"];
	$matchCanalSelectionnable = $donnees["Matches_MatchCanalModifiable"];
	$req->closeCursor();

	if($matchPronostiquable == 0 || $matchCanalSelectionnable == 0) {
		echo 'DEPASSE';
		exit();
	}

	$ordreSQL =		'	REPLACE INTO	journees_pronostiqueurs_canal' .
					'	SELECT			' . $journee . ', ' . $_SESSION["pronostiqueur"] . ', ' . $match;
	$bdd->exec($ordreSQL);
	
	// Création de la trace
	$nomFichier = '../traces/canal/' . $journee . '_' . $_SESSION["pronostiqueur"] . '.txt';
	file_put_contents($nomFichier, $match);
?>
