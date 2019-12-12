<?php
	// Lecture des liens vers les matches d'une journée

	include_once('commun_administrateur.php');


	// Lecture des paramètres passés à la page
	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;

	// Parcours du fichier XML pour détection des différents matches à surveiller
	$ordreSQL =		'	SELECT		Championnats_LienPage' .
					'	FROM		championnats' .
					'	JOIN		journees' .
					'				ON		championnats.Championnat = journees.Championnats_Championnat' .
					'	WHERE		journees.Journee = ' . $journee;
	$req = $bdd->query($ordreSQL);
	$championnats = $req->fetchAll();
	$lienXML = $championnats[0]["Championnats_LienPage"];

	$document = new DOMDocument();
	@$document->load($lienXML);
	$xpathJournee = new DOMXpath($document);

	$matches = $document->getElementsByTagName('item');

	$passageEffectue = 0;

	$tableauMatches = array();
	foreach($matches as $unMatch) {
		$lienMatch = $unMatch->getElementsByTagName('link')->item(0)->nodeValue;
		array_push($tableauMatches, $lienMatch);
	}
	$tableauMatches = array_unique($tableauMatches);

	// Recherche dans la table des matches pour la journée en question
	// La requête de sélection se construit selon les matches lus dans le fichier XML
	$ordreSQL =		'	SELECT		DISTINCT matches.Match, Matches_Date, EquipesDomicile_Nom, EquipesVisiteur_Nom, Matches_LienPage' .
					'	FROM		(';

		$passageEffectue = 0;
		foreach($tableauMatches as $unMatch) {
			if($passageEffectue == 1)
				$ordreSQL .=		'	UNION';
			else
				$passageEffectue = 1;

	$ordreSQL .=	'					SELECT		matches.Match, Matches_Date, equipes_domicile.Equipes_Nom AS EquipesDomicile_Nom, equipes_visiteur.Equipes_Nom AS EquipesVisiteur_Nom' .
					'								,' . $bdd->quote($unMatch) . ' AS Matches_LienPage' .
					'					FROM		matches' .
					'					JOIN		equipes equipes_domicile' .
					'								ON		matches.Equipes_EquipeDomicile = equipes_domicile.Equipe' .
					'					JOIN		equipes equipes_visiteur' .
					'								ON		matches.Equipes_EquipeVisiteur = equipes_visiteur.Equipe' .
					'					WHERE		matches.Journees_Journee = ' . $journee .
					'								AND		LOCATE(IFNULL(equipes_domicile.Equipes_NomCorrespondance, equipes_domicile.Equipes_NomCourt), ' . $bdd->quote($unMatch) . ') > 0' .
					'								AND		LOCATE(IFNULL(equipes_visiteur.Equipes_NomCorrespondance, equipes_visiteur.Equipes_NomCourt), ' . $bdd->quote($unMatch) . ') > 0';
								}
	$ordreSQL .=	'				) matches';
	$req = $bdd->query($ordreSQL);
	$matches = $req->fetchAll();

	// Mise à jour des matches avec le lien vers la page lue
	foreach($matches as $unMatch) {
		$ordreSQL =		'	UPDATE		matches' .
						'	SET			Matches_LienPage = \'' . $unMatch["Matches_LienPage"] . '\'' .
						'	WHERE		matches.Match = ' . $unMatch["Match"];
		$bdd->exec($ordreSQL);
	}
?>
