<?php
	// Lecture des liens vers les matches d'une journée
	
	include('commun_administrateur.php');
	
	// Lecture des paramètres passés à la page
	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;
	
	// Parcours des matches dont les équipes sont connues et pour lesquelles il n'existe pas de lien page
	$ordreSQL =		'	UPDATE		matches' .
					'	JOIN		equipes equipes_domicile' .
					'				ON		matches.Equipes_EquipeDomicile = equipes_domicile.Equipe' .
					'	JOIN		equipes equipes_visiteur' .
					'				ON		matches.Equipes_EquipeVisiteur = equipes_visiteur.Equipe' .
					'	SET			Matches_LienPageComplementaire = CONCAT(equipes_domicile.Equipes_NomCorrespondanceComplementaire, \'-vs-\', equipes_visiteur.Equipes_NomCorrespondanceComplementaire, \'/\', DATE_FORMAT(Matches_Date, \'%d-%m-%Y\'))' .
					'	WHERE		matches.Journees_Journee = ' . $journee .
					'				AND		matches.Equipes_EquipeDomicile IS NOT NULL' .
					'				AND		matches.Equipes_EquipeVisiteur IS NOT NULL' .
					'				AND		IFNULL(matches.Matches_LienPageComplementaire, \'\') = \'\'';
	$bdd->exec($ordreSQL);

?>
