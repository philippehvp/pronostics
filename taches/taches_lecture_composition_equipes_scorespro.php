<?php

	// Lecture des compositions des deux équipes d'un match
	// La page peut être appelée de deux manières :
	// - soit par une inclusion
	// - soit par un rafraîchissement (Ajax)
	
	// Dans un premier temps, on regarde si des matches vont commencer d'ici 15 minutes et pour lesquels
	// aucun lien vers la page de match sur le site extérieur n'existe
	// Dans un deuxième temps, on regarde si :
	// - des matches vont commencer d'ici 15 minutes et pour lesquels la composition des équipes n'a pas encore été déterminée
	// - des matches sont en direct et dont la composition n'a pas encore été remplie automatiquement
	// Dans un troisième temps, on regarde si des matches vont commencer d'ici 5 minutes et qui ne trouvent pas
	// dans la liste des matches en direct
	
	$rafraichissement = isset($_POST["rafraichissement"]) ? $_POST["rafraichissement"] : 0;

	if($rafraichissement) {
		include_once('../commun_administrateur.php');
		include_once('../creer_match_fonctions.php');
	}
	else {
		include_once('commun_administrateur.php');
		include_once('creer_match_fonctions.php');
	}
	
	$tableauErreurs = array();
	
	// Premier temps : lecture de la page de lien pour les matches qui commencent d'ici 15 minutes et qui n'en possèdent pas encore
	$ordreSQL =		'	UPDATE		matches' .
					'	JOIN		equipes equipes_domicile' .
					'				ON		matches.Equipes_EquipeDomicile = equipes_domicile.Equipe' .
					'	JOIN		equipes equipes_visiteur' .
					'				ON		matches.Equipes_EquipeVisiteur = equipes_visiteur.Equipe' .
					'	SET			Matches_LienPageComplementaire = CONCAT(equipes_domicile.Equipes_NomCorrespondance, \'-vs-\', equipes_visiteur.Equipes_NomCorrespondance, \'/\', DATE_FORMAT(Matches_Date, \'%d-%m-%Y\'))' .
					'	WHERE		DATE_ADD(NOW(), INTERVAL 15 MINUTE) >= matches.Matches_Date' .
					'				AND		matches.Matches_Date >= NOW()' .
					'				AND		LENGTH(LTRIM(RTRIM(IFNULL(Matches_LienPageComplementaire, \'\')))) = 0' .
					'				AND		matches.Equipes_EquipeDomicile IS NOT NULL' .
					'				AND		matches.Equipes_EquipeVisiteur IS NOT NULL' .
					'				AND		IFNULL(matches.Matches_LienPageComplementaire, \'\') = \'\'';
	$bdd->exec($ordreSQL);

	// Deuxième temps, on regarde si :
	// - des matches vont commencer d'ici 15 minutes et pour lesquels la composition des équipes n'a pas encore été déterminée
	// - des matches sont en direct et dont la composition n'a pas encore été remplie automatiquement
	$ordreSQL =		'	SELECT		matches.Match, Matches_Date, Equipes_EquipeDomicile, Equipes_EquipeVisiteur, IFNULL(Matches_LienPageComplementaire, \'\') AS Matches_LienPageComplementaire' .
					'	FROM		matches' .
					'	WHERE		DATE_ADD(NOW(), INTERVAL 15 MINUTE) >= matches.Matches_Date' .
					'				AND		matches.Matches_Date >= NOW()' .
					'				AND		LENGTH(LTRIM(RTRIM(IFNULL(Matches_LienPageComplementaire, \'\')))) > 0' .
					'				AND		IFNULL(Matches_CompositionLue, 0) = 0' .
					'	UNION ALL' .
					'	SELECT		matches.Match, Matches_Date, Equipes_EquipeDomicile, Equipes_EquipeVisiteur, IFNULL(Matches_LienPageComplementaire, \'\') AS Matches_LienPageComplementaire' .
					'	FROM		matches' .
					'	JOIN		matches_direct' .
					'				ON		matches.Match = matches_direct.Matches_Match' .
					'	WHERE		LENGTH(LTRIM(RTRIM(IFNULL(Matches_LienPageComplementaire, \'\')))) > 0' .
					'				AND		IFNULL(Matches_CompositionLue, 0) = 0';

	$req = $bdd->query($ordreSQL);
	$matches = $req->fetchAll();
	
	$adresseComposition = 'http://www.scorespro.com/soccer/ajax-matchcenter.php?link=';
	
	foreach($matches as $unMatch) {
		if(strlen(trim($unMatch["Matches_LienPageComplementaire"])) > 0) {

			$documentComposition = new DOMDocument();
			@$documentComposition->loadHTMLFile($adresseComposition . $unMatch["Matches_LienPageComplementaire"]);

			$match = $unMatch["Match"];
			$dateMatch = $unMatch["Matches_Date"];
			$equipeDomicile = $unMatch["Equipes_EquipeDomicile"];
			$equipeVisiteur = $unMatch["Equipes_EquipeVisiteur"];
			
			$xpathComposition = new DOMXpath($documentComposition);
			
			// Lecture des joueurs de l'équipe domicile
			$baliseCompo1 = $xpathComposition->query('//td[@class="h_player"]');
			$i = 0;
			foreach($baliseCompo1 as $uneLigneDeCompo) {
				$joueurs = $uneLigneDeCompo->getElementsByTagName('span');
				foreach($joueurs as $unJoueur) {
					$class = $unJoueur->getAttribute('class');
					if($class == '') {
						$retour = ajouterJoueur($bdd, trim($unJoueur->textContent), $equipeDomicile, $match, $dateMatch, 2);
						if($retour == -1) {
							array_push($tableauErreurs, array('equipe'=>$equipeDomicile, 'joueur'=>trim($unJoueur->textContent)));
							echo 'Joueur ' . trim($unJoueur->textContent) . ' non trouvé en base<br />';
						}
						else if($retour == 0) {
							array_push($tableauErreurs, array('equipe'=>$equipeDomicile, 'joueur'=>trim($unJoueur->textContent)));
							echo 'Joueur ' . trim($unJoueur->textContent) . ' trouvé en base avec doublon<br />';
						}
						
						$i++;
					}
				}
				if($i == 11)
					break;
			}

			// Lecture des joueurs de l'équipe visiteur
			$baliseCompo2 = $xpathComposition->query('//td[@class="a_player"]');
			$i = 0;
			foreach($baliseCompo2 as $uneLigneDeCompo) {
				$joueurs = $uneLigneDeCompo->getElementsByTagName('span');
				foreach($joueurs as $unJoueur) {
					$class = $unJoueur->getAttribute('class');
					if($class == '') {
						$retour = ajouterJoueur($bdd, trim($unJoueur->textContent), $equipeVisiteur, $match, $dateMatch, 2);
						if($retour == -1) {
							array_push($tableauErreurs, array('equipe'=>$equipeVisiteur, 'joueur'=>trim($unJoueur->textContent)));
							echo 'Joueur ' . trim($unJoueur->textContent) . ' non trouvé en base<br />';
						}
						else if($retour == 0) {
							array_push($tableauErreurs, array('equipe'=>$equipeVisiteur, 'joueur'=>trim($unJoueur->textContent)));
							echo 'Joueur ' . trim($unJoueur->textContent) . ' trouvé en base avec doublon<br />';
						}
						
						$i++;
					}
				}
				if($i == 11)
					break;
			}
			
			// On indique que la composition a été lue, même si des erreurs sont survenues
			finaliserCompositionEquipes($bdd, $match);
		}
	}
	
	// Troisième temps : lecture des matches qui vont commencer d'ici 5 minutes
	$ordreSQL =		'	INSERT INTO	matches_direct(Matches_Match)' .
					'	SELECT		matches.Match' .
					'	FROM		matches' .
					'	LEFT JOIN	matches_direct' .
					'				ON		matches.Match = matches_direct.Matches_Match' .
					'	WHERE		DATE_ADD(NOW(), INTERVAL 5 MINUTE) >= matches.Matches_Date' .
					'				AND		matches.Matches_Date >= NOW()' .
					'				AND		LENGTH(LTRIM(RTRIM(IFNULL(Matches_LienPageComplementaire, \'\')))) > 0' .
					'				AND		matches_direct.Matches_Match IS NULL';

	$bdd->exec($ordreSQL);

?>