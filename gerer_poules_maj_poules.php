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

    // On remet à 0 également la table des équipes en phase de qualification
	$ordreSQL =		'	DELETE		phase' .
                    '   FROM        phase' .
					'	WHERE		phase.Championnats_Championnat = ' . $championnat;
	$bdd->exec($ordreSQL);

	$ordreSQL =		'	INSERT INTO phase(Equipes_Equipe, Championnats_Championnat, Phase_Qualification)' .
					'	SELECT		equipes.Equipe, ' . $championnat . ', 0' .
					'	FROM		engagements' .
					'	JOIN		equipes' .
					'				ON		engagements.Equipes_Equipe = equipes.Equipe'.
        			'						AND		engagements.Championnats_Championnat = ' . $championnat;
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
	$ordreSQL =		'	DELETE		pronostics_phase' .
					'	FROM		pronostics_phase' .
					'	JOIN		inscriptions' .
					'				ON		pronostics_phase.Pronostiqueurs_Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
					'	WHERE		inscriptions.Championnats_Championnat = ' . $championnat;
	$bdd->exec($ordreSQL);

	$ordreSQL =		'	INSERT INTO pronostics_phase(Pronostiqueurs_Pronostiqueur, Equipes_Equipe, Championnats_Championnat, PronosticsPhase_Qualification)' .
					'	SELECT		pronostiqueurs.Pronostiqueur, equipes.Equipe, inscriptions.Championnats_Championnat, 0' .
					'	FROM		pronostiqueurs' .
					'	JOIN		inscriptions' .
					'				ON		pronostiqueurs.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
					'	JOIN		engagements' .
					'				ON		inscriptions.Championnats_Championnat = engagements.Championnats_Championnat' .
					'	JOIN		equipes'.
					'				ON		engagements.Equipes_Equipe = equipes.Equipe'.
					'	JOIN	    ('.
            		'					SELECT 2 AS Championnat'.
            		'					UNION'.
            		'					SELECT 3 AS Championnat'.
        			'				) championnats'.
        			'				ON      inscriptions.Championnats_Championnat = championnats.Championnat'.
					'	WHERE		inscriptions.Championnats_Championnat = ' . $championnat;
	$bdd->exec($ordreSQL);
?>