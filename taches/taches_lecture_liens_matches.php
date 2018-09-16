<?php
	// Lecture des liens vers les pages des matches d'une journée ainsi que les compositions
	
	// Dans un premier temps, on regarde si des matches vont commencer d'ici 15 minutes et pour lesquels
	// aucun lien vers la page de match sur le site extérieur n'existe
	// Dans un deuxième temps, on regarde si :
	// - des matches vont commencer d'ici 15 minutes et pour lesquels la composition des équipes n'a pas encore été déterminée
	// - des matches sont en direct et dont la composition n'a pas encore été remplie automatiquement
	// Dans un troisième temps, on regarde si des matches vont commencer d'ici 5 minutes et qui ne trouvent pas
	// dans la liste des matches en direct
	
	
	// La page peut être appelée de deux manières :
	// - soit par une inclusion
	// - soit par un rafraîchissement (Ajax)
	
	$rafraichissement = isset($_POST["rafraichissement"]) ? $_POST["rafraichissement"] : 0;

	if($rafraichissement) {
		include_once('../commun_administrateur.php');
		include_once('../creer_match_fonctions.php');
	} else {
		include_once('commun_administrateur.php');
		include_once('creer_match_fonctions.php');
	}

	// Premier temps : lecture de la page de lien pour les matches qui commencent d'ici 15 minutes et qui n'en possèdent pas encore
	$ordreSQL =		'	SELECT		DISTINCT Journees_Journee' .
								'	FROM			matches' .
								'	WHERE			DATE_ADD(NOW(), INTERVAL 15 MINUTE) >= matches.Matches_Date' .
								'						AND		matches.Matches_Date >= NOW()' .
								'						AND		LENGTH(LTRIM(RTRIM(IFNULL(Matches_LienPage, \'\')))) = 0';

	$req = $bdd->query($ordreSQL);
	$journees = $req->fetchAll();
	
	foreach($journees as $uneJournee) {
		// Parcours du fichier XML pour détection des différents matches à surveiller
		$ordreSQL =		'	SELECT		Championnats_LienPage' .
									'	FROM			championnats' .
									'	JOIN			journees' .
									'						ON		championnats.Championnat = journees.Championnats_Championnat' .
									'	WHERE			journees.Journee = ' . $uneJournee["Journees_Journee"];
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
									'	FROM			(';
						
		$passageEffectue = 0;
		foreach($tableauMatches as $unMatch) {
			if($passageEffectue == 1)
				$ordreSQL .=		'	UNION';
			else
				$passageEffectue = 1;
					
			$ordreSQL .=	'			SELECT		matches.Match, Matches_Date, equipes_domicile.Equipes_Nom AS EquipesDomicile_Nom, equipes_visiteur.Equipes_Nom AS EquipesVisiteur_Nom' .
										'								,' . $bdd->quote($unMatch) . ' AS Matches_LienPage' .
										'			FROM			matches' .
										'			JOIN			equipes equipes_domicile' .
										'								ON		matches.Equipes_EquipeDomicile = equipes_domicile.Equipe' .
										'			JOIN			equipes equipes_visiteur' .
										'								ON		matches.Equipes_EquipeVisiteur = equipes_visiteur.Equipe' .
										'			WHERE			matches.Journees_Journee = ' . $uneJournee["Journees_Journee"] .
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
										'	WHERE		matches.Match = ' . $unMatch["Match"] .
										'				AND		LENGTH(LTRIM(RTRIM(IFNULL(Matches_LienPage, \'\')))) = 0';
			$bdd->exec($ordreSQL);
		}
	}

	// Deuxième temps, on regarde si :
	// - des matches vont commencer d'ici 15 minutes et pour lesquels la composition des équipes n'a pas encore été déterminée
	// - des matches sont en direct et dont la composition n'a pas encore été remplie automatiquement
	$ordreSQL =		'	SELECT		matches.Match, Matches_Date, Equipes_EquipeDomicile, Equipes_EquipeVisiteur, IFNULL(Matches_LienPage, \'\') AS Matches_LienPage' .
								'	FROM			matches' .
								'	WHERE			DATE_ADD(NOW(), INTERVAL 15 MINUTE) >= matches.Matches_Date' .
								'						AND		matches.Matches_Date >= NOW()' .
								'						AND		LENGTH(LTRIM(RTRIM(IFNULL(Matches_LienPage, \'\')))) > 0' .
								'						AND		IFNULL(Matches_CompositionLue, 0) = 0' .
								'	UNION ALL' .
								'	SELECT		matches.Match, Matches_Date, Equipes_EquipeDomicile, Equipes_EquipeVisiteur, IFNULL(Matches_LienPage, \'\') AS Matches_LienPage' .
								'	FROM			matches' .
								'	JOIN			matches_direct' .
								'						ON		matches.Match = matches_direct.Matches_Match' .
								'	WHERE			LENGTH(LTRIM(RTRIM(IFNULL(Matches_LienPage, \'\')))) > 0' .
								'						AND		IFNULL(Matches_CompositionLue, 0) = 0';
	$req = $bdd->query($ordreSQL);
	$matches = $req->fetchAll();

	foreach($matches as $unMatch) {
		$document = new DOMDocument();
		@$document->loadHTMLFile($unMatch["Matches_LienPage"]);
		$dateMatch = $unMatch["Matches_Date"];
		$equipeDomicile = $unMatch["Equipes_EquipeDomicile"];
		$equipeVisiteur = $unMatch["Equipes_EquipeVisiteur"];
		
		$xpath = new DOMXpath($document);

		$divComposition = $xpath->query('//div[contains(@class, "MEDpanelcomposition")]');
		if(!$divComposition) {
			return;
		}

		$tableauComposition = $xpath->query('div[@class="panel-body"]/table/tbody/tr/td', $divComposition->item(0));

		// Equipe domicile
		$htmlEquipeDomicile = remplacerCaracteres(my_utf8_decode(trim($document->saveHTML($tableauComposition->item(0)))));
		$htmlEquipeDomicile = preg_replace('/<[^>]*>/', ',', $htmlEquipeDomicile);
		$joueursEquipeDomicile = explode(",", $htmlEquipeDomicile);

		// Equipe visiteur
		$htmlEquipeVisiteur = remplacerCaracteres(my_utf8_decode(trim($document->saveHTML($tableauComposition->item(1)))));
		$htmlEquipeVisiteur = preg_replace('/<[^>]*>/', ',', $htmlEquipeVisiteur);
		$joueursEquipeVisiteur = explode(",", $htmlEquipeVisiteur);

		// Lecture des joueurs de l'équipe domicile
		$i = 0;
		foreach($joueursEquipeDomicile as $unJoueur) {
			if($unJoueur && trim($unJoueur) != "") {
				$nomJoueurModifie = trim($unJoueur);
				$retour = ajouterJoueur($bdd, $nomJoueurModifie, $equipeDomicile, $unMatch["Match"], $dateMatch, 1);
				if($retour == -1)
					array_push($tableauErreurs, array('equipe'=>$equipeDomicile, 'joueur'=>$nomJoueurModifie));
				else if($retour == 0)
					array_push($tableauErreurs, array('equipe'=>$equipeDomicile, 'joueur'=>$nomJoueurModifie));
				if(++$i == 11)
					break;
			}
		}
		
		// Lecture des joueurs de l'équipe visiteur
		$i = 0;
		foreach($joueursEquipeVisiteur as $unJoueur) {
			if($unJoueur && trim($unJoueur) != "") {
				$nomJoueurModifie = trim($unJoueur);
				$retour = ajouterJoueur($bdd, $nomJoueurModifie, $equipeVisiteur, $unMatch["Match"], $dateMatch, 1);
				if($retour == -1)
					array_push($tableauErreurs, array('equipe'=>$equipeVisiteur, 'joueur'=>$nomJoueurModifie));
				else if($retour == 0)
					array_push($tableauErreurs, array('equipe'=>$equipeVisiteur, 'joueur'=>$nomJoueurModifie));
				if(++$i == 11)
					break;
			}
		}
		
		// On indique que la composition a été lue, même si des erreurs sont survenues
		finaliserCompositionEquipes($bdd, $unMatch["Match"]);
	}
	
	// Troisième temps : lecture des matches qui vont commencer d'ici 5 minutes
	$ordreSQL =		'	INSERT INTO	matches_direct(Matches_Match)' .
					'	SELECT		matches.Match' .
					'	FROM		matches' .
					'	LEFT JOIN	matches_direct matches_direct_actuels' .
					'				ON		matches.Match = matches_direct_actuels.Matches_Match' .
					'	WHERE		DATE_ADD(NOW(), INTERVAL 5 MINUTE) >= matches.Matches_Date' .
					'				AND		matches.Matches_Date >= NOW()' .
					'				AND		LENGTH(LTRIM(RTRIM(IFNULL(Matches_LienPage, \'\')))) > 0' .
					'				AND		matches_direct_actuels.Matches_Match IS NULL';
	
	$bdd->exec($ordreSQL);
	
?>
