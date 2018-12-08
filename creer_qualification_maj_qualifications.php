<?php
	include_once('commun.php');
	
	// Mise à jour des équipes qualifiées
	// Lecture des paramètres passés à la page
	$groupes = isset($_POST["groupes"]) ? $_POST["groupes"] : 0;
	$equipes = isset($_POST["equipes"]) ? $_POST["equipes"] : 0;
	$numeroPremierGroupe = isset($_POST["numeroPremierGroupe"]) ? $_POST["numeroPremierGroupe"] : 0;
	
	$ordreSQL =		'	SELECT		Championnats_Championnat' .
					'	FROM		inscriptions' .
					'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'				AND		inscriptions.Championnats_Championnat IN (2, 3)';
	
	$req = $bdd->query($ordreSQL);
	$championnats = $req->fetchAll();
	if(sizeof($championnats) == 0)
		$championnat = 0;
	else
		$championnat = $championnats[0]["Championnats_Championnat"];
	
	$ordreSQL =		'	DELETE		pronostics_qualifications' .
					'	FROM		pronostics_qualifications' .
					'	JOIN		qualifications_date_max' .
					'				ON		pronostics_qualifications.Championnats_Championnat = qualifications_date_max.Championnats_Championnat' .
					'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
					'				AND		NOW() < Qualifications_Date_Max' .
					'				AND		pronostics_qualifications.Championnats_Championnat = ' . $championnat;

	$bdd->exec($ordreSQL);

	// Parcours des groupes
	for($i = 0; $i < $groupes; $i++) {
		for($j = 0; $j < $equipes; $j++) {
			$equipe = isset($_POST["groupe" . ($i . "equipe" . $j)]) ? $_POST["groupe" . $i . "equipe" . $j] : 0;
			
			$ordreSQL =		'	INSERT INTO		pronostics_qualifications(Pronostiqueurs_Pronostiqueur, Championnats_Championnat, Groupes_Groupe, Equipes_Equipe, PronosticsQualifications_Classement)' .
							'	SELECT			' . $pronostiqueur . ', ' . $championnat . ', ' . ($i + $numeroPremierGroupe) . ', ' . $equipe . ', ' . ($j + 1) .
							'	FROM			qualifications_date_max' .
							'	WHERE			NOW() < qualifications_date_max.Qualifications_Date_Max' .
							'					AND		qualifications_date_max.Championnats_Championnat = ' . $championnat;
			$bdd->exec($ordreSQL);
		}
	}

	echo 'Vos pronostics de qualification ont été sauvegardés avec succès';
?>