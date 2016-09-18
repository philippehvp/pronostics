<?php

	// Lecture des effectifs des deux équipes d'un match
	
	include_once('creer_match_fonctions.php');
	$tableauErreurs = array();
	
	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	
	$ordreSQL =		'	SELECT		Matches_Date, Equipes_EquipeDomicile, Equipes_EquipeVisiteur, IFNULL(Matches_LienPage, \'\') AS Matches_LienPage' .
					'	FROM		matches' .
					'	JOIN		equipes equipes_domicile' .
					'				ON		matches.Equipes_EquipeDomicile = equipes_domicile.Equipe' .
					'	JOIN		equipes equipes_visiteur' .
					'				ON		matches.Equipes_EquipeVisiteur = equipes_visiteur.Equipe' .
					'	WHERE		matches.Match = ' . $match;
	$req = $bdd->query($ordreSQL);
    $matches = $req->fetchAll();
    
	if(strlen($matches[0]["Matches_LienPage"]) > 0) {
		$document = new DOMDocument();
		@$document->loadHTMLFile($matches[0]["Matches_LienPage"]);
		$dateMatch = $matches[0]["Matches_Date"];
		$equipeDomicile = $matches[0]["Equipes_EquipeDomicile"];
		$equipeVisiteur = $matches[0]["Equipes_EquipeVisiteur"];
		
		$xpath = new DOMXpath($document);
        
		// Lecture des joueurs de l'équipe domicile
		$baliseCompo1 = $xpath->query('//td[@class="compo1"]');
		$joueurs = $baliseCompo1->item(0)->childNodes;
		foreach($joueurs as $unJoueur) {
			if($unJoueur->textContent != null && substr(trim($unJoueur->textContent), 0, 5) != 'cache') {
				$retour = rechercherJoueur($bdd, trim($unJoueur->textContent), $equipeDomicile, $dateMatch, 1);
				if($retour == -1)
					$retour = rechercherJoueurInitialePrenom($bdd, trim($unJoueur->textContent), $equipeDomicile, $dateMatch, 1);
				if($retour == -1)
					array_push($tableauErreurs, array('equipe'=>$equipeDomicile, 'joueur'=>trim($unJoueur->textContent)));
				else if($retour == 0)
					array_push($tableauErreurs, array('equipe'=>$equipeDomicile, 'joueur'=>trim($unJoueur->textContent)));
			}
		}

		// Lecture des joueurs de l'équipe visiteur
		$baliseCompo2 = $xpath->query('//td[@class="compo2"]');
		$joueurs = $baliseCompo2->item(0)->childNodes;
		foreach($joueurs as $unJoueur) {
			if($unJoueur->textContent != null && substr(trim($unJoueur->textContent), 0, 5) != 'cache') {
				$retour = rechercherJoueur($bdd, trim($unJoueur->textContent), $equipeVisiteur, $dateMatch, 1);
				if($retour <= 0)
					$retour = rechercherJoueurInitialePrenom($bdd, trim($unJoueur->textContent), $equipeVisiteur, $dateMatch, 1);
				if($retour == -1)
					array_push($tableauErreurs, array('equipe'=>$equipeVisiteur, 'joueur'=>trim($unJoueur->textContent)));
				else if($retour == 0)
					array_push($tableauErreurs, array('equipe'=>$equipeVisiteur, 'joueur'=>trim($unJoueur->textContent)));
			}
		}
	}

	echo json_encode($tableauErreurs);
?>