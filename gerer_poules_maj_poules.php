<?php
	include_once('commun_administrateur.php');
	
	// Mise à jour des équipes dans les poules
	// Lecture des paramètres passés à la page
	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;
	$groupes = isset($_POST["groupes"]) ? $_POST["groupes"] : 0;
	$equipes = isset($_POST["equipes"]) ? $_POST["equipes"] : 0;
	$numeroPremierGroupe = isset($_POST["numeroPremierGroupe"]) ? $_POST["numeroPremierGroupe"] : 0;
	
	$ordreSQL =		'	DELETE		equipes_groupes' .
                    '   FROM        equipes_groupes' .
                    '   JOIN        groupes' .
                    '               ON      equipes_groupes.Groupes_Groupe = groupes.Groupe' .
					'	WHERE		groupes.Championnats_Championnat = ' . $championnat;
	$bdd->exec($ordreSQL);
    
    // On remet à 0 également la table des équipes qualifiées
	$ordreSQL =		'	DELETE		qualifications' .
                    '   FROM        qualifications' .
                    '   JOIN        groupes' .
                    '               ON      qualifications.Groupes_Groupe = groupes.Groupe' .
					'	WHERE		groupes.Championnats_Championnat = ' . $championnat;
	$bdd->exec($ordreSQL);
	
	// Parcours des groupes
	for($i = 0; $i < $groupes; $i++) {
		// Parcours des équipes
		for($j = 0; $j < $equipes; $j++) {
			$equipe = isset($_POST["groupe" . $i . "equipe" . $j]) ? $_POST["groupe" . $i . "equipe" . $j] : 0;
			
			$ordreSQL =		'	INSERT INTO		equipes_groupes(Equipes_Equipe, Groupes_Groupe, EquipesGroupes_Chapeau)' .
							'	SELECT			' . $equipe . ', ' . ($i + $numeroPremierGroupe) . ', ' . ($j + 1);
			$bdd->exec($ordreSQL);
		}
	}

	// Arrivé ici, on met à jour la table des pronostics de qualifications
	// Pour éviter qu'un pronostiqueur n'ait de données dans différentes compétitions européennes, il faut :
	// - effacer toutes les lignes de pronostics_qualifications des pronostiqueurs de ce championnat (éviter éventuellement des lignes de la saison précédente)
	// - recréer les lignes
	$ordreSQL =		'	DELETE		pronostics_qualifications' .
					'	FROM		pronostics_qualifications' .
					'	JOIN		inscriptions' .
					'				ON		pronostics_qualifications.Pronostiqueurs_Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
					'	WHERE		inscriptions.Championnats_Championnat = ' . $championnat;
	$bdd->exec($ordreSQL);

	$ordreSQL =		'	INSERT INTO pronostics_qualifications(Pronostiqueurs_Pronostiqueur, Championnats_Championnat, Groupes_Groupe, Equipes_Equipe, PronosticsQualifications_Classement)' .
					'	SELECT		inscriptions.Pronostiqueurs_Pronostiqueur, inscriptions.Championnats_Championnat, equipes_groupes.Groupes_Groupe, equipes_groupes.Equipes_Equipe, equipes_groupes.EquipesGroupes_Chapeau' .
					'	FROM		inscriptions' .
					'	JOIN		groupes' .
					'				ON		inscriptions.Championnats_Championnat = groupes.Championnats_Championnat' .
					'	JOIN		equipes_groupes' .
					'				ON		groupes.Groupe = equipes_groupes.Groupes_Groupe' .
					'	WHERE		inscriptions.Championnats_Championnat = ' . $championnat;
	$bdd->exec($ordreSQL);
?>