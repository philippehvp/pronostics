<?php
	include('commun_administrateur.php');
	
	// Mise à jour des équipes qualifiées pour une poule
	// Lecture des paramètres passés à la page
	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;
	$groupe = isset($_POST["groupe"]) ? $_POST["groupe"] : 0;
	$equipes = isset($_POST["equipes"]) ? $_POST["equipes"] : 0;
	$numeroPremierGroupe = isset($_POST["numeroPremierGroupe"]) ? $_POST["numeroPremierGroupe"] : 0;
	
	$ordreSQL =		'	DELETE		qualifications' .
					'	FROM		qualifications' .
					'	LEFT JOIN	groupes' .
					'				ON		qualifications.Groupes_Groupe = groupes.Groupe' .
					'	WHERE		groupes.Championnats_Championnat = ' . $championnat .
                    '               AND     qualifications.Groupes_Groupe = ' . $groupe;
	$bdd->exec($ordreSQL);
	
    // Parcours des équipes
    for($i = 0; $i < $equipes; $i++) {
        $equipe = isset($_POST["groupe" . ($groupe - $numeroPremierGroupe) . "equipe" . $i]) ? $_POST["groupe" . ($groupe - $numeroPremierGroupe) . "equipe" . $i] : 0;
        
        $ordreSQL =		'	INSERT INTO		qualifications(Groupes_Groupe, Equipes_Equipe, Qualifications_Classement)' .
                        '	SELECT			' . $groupe . ', ' . $equipe . ', ' . ($i + 1);


        $bdd->exec($ordreSQL);
    }

	// Lecture de la dernière journée de poule selon le championnat
	$ordreSQL =		'	SELECT		MAX(journees.Journee) AS Journee' .
					'	FROM		journees' .
					'	JOIN		matches' .
					'				ON		journees.Journee = matches.Journees_Journee' .
					'	WHERE		journees.Championnats_Championnat = ' . $championnat .
					'				AND		matches.Matches_MatchLie IS NULL' .
					'				AND		matches.Matches_AvecProlongation = 0';
	$req = $bdd->query($ordreSQL);
	$journees = $req->fetchAll();
	$journee = $journees[0]["Journee"];

	$ordreSQL =		'	CALL sp_calcultouslesscores(' . $journee . ')';

	$bdd->exec($ordreSQL);

?>