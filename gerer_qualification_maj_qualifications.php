<?php
	include_once('commun_administrateur.php');
	
	// Mise à jour des équipes qualifiées
	// Lecture des paramètres passés à la page
	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;
	$groupes = isset($_POST["groupes"]) ? $_POST["groupes"] : 0;
	$equipes = isset($_POST["equipes"]) ? $_POST["equipes"] : 0;
	$numeroPremierGroupe = isset($_POST["numeroPremierGroupe"]) ? $_POST["numeroPremierGroupe"] : 0;
	
	$ordreSQL =		'	DELETE		qualifications' .
					'	FROM		qualifications' .
					'	LEFT JOIN	groupes' .
					'				ON		qualifications.Groupes_Groupe = groupes.Groupe' .
					'	WHERE		groupes.Championnats_Championnat = ' . $championnat;

	$bdd->exec($ordreSQL);
	
	// Parcours des groupes
	for($i = 0; $i < $groupes; $i++) {
		// Parcours des équipes
		for($j = 0; $j < $equipes; $j++) {
			$equipe = isset($_POST["groupe" . $i . "equipe" . $j]) ? $_POST["groupe" . $i . "equipe" . $j] : 0;
			
			$ordreSQL =		'	INSERT INTO		qualifications(Groupes_Groupe, Equipes_Equipe, Qualifications_Classement)' .
							'	SELECT			' . ($i + $numeroPremierGroupe) . ', ' . $equipe . ', ' . ($j + 1);

			$bdd->exec($ordreSQL);
		}
	}
?>