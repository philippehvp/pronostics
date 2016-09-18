<?php

	// Lecture des compositions des deux équipes d'un match
	include_once('creer_match_fonctions.php');
	
	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	
	$tableauErreurs = array();
	
	$ordreSQL =		'	SELECT		Matches_Date, Equipes_EquipeDomicile, Equipes_EquipeVisiteur' .
					'				,IFNULL(Matches_LienPageComplementaire, \'\') AS Matches_LienPageComplementaire' .
					'	FROM		matches' .
					'	JOIN		equipes equipes_domicile' .
					'				ON		matches.Equipes_EquipeDomicile = equipes_domicile.Equipe' .
					'	JOIN		equipes equipes_visiteur' .
					'				ON		matches.Equipes_EquipeVisiteur = equipes_visiteur.Equipe' .
					'	WHERE		matches.Match = ' . $match;
	$req = $bdd->query($ordreSQL);
	$matches = $req->fetchAll();
	
	$adresseComposition = 'http://www.scorespro.com/soccer/ajax-matchcenter.php?link=';
	
	if(strlen(trim($matches[0]["Matches_LienPageComplementaire"])) > 0) {

		$documentComposition = new DOMDocument();
		@$documentComposition->loadHTMLFile($adresseComposition . $matches[0]["Matches_LienPageComplementaire"]);


		$dateMatch = $matches[0]["Matches_Date"];
		$equipeDomicile = $matches[0]["Equipes_EquipeDomicile"];
		$equipeVisiteur = $matches[0]["Equipes_EquipeVisiteur"];
		
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
					if($retour == -1)
						array_push($tableauErreurs, array('equipe'=>$equipeDomicile, 'joueur'=>trim($unJoueur->textContent)));
					else if($retour == 0)
						array_push($tableauErreurs, array('equipe'=>$equipeDomicile, 'joueur'=>trim($unJoueur->textContent)));
					
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
					if($retour == -1)
						array_push($tableauErreurs, array('equipe'=>$equipeVisiteur, 'joueur'=>trim($unJoueur->textContent)));
					else if($retour == 0)
						array_push($tableauErreurs, array('equipe'=>$equipeVisiteur, 'joueur'=>trim($unJoueur->textContent)));
					
					$i++;
				}
			}
			if($i == 11)
				break;
		}
	}
	
	echo json_encode($tableauErreurs);
?>