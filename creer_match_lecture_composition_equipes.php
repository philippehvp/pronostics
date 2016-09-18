<?php

	// Lecture des compositions des deux équipes d'un match
	include_once('creer_match_fonctions.php');
	
	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	
	$ordreSQL =		'	SELECT		Matches_Date, Equipes_EquipeDomicile, Equipes_EquipeVisiteur, Matches_LienPage' .
					'	FROM		matches' .
					'	WHERE		matches.Match = ' . $match;
	$req = $bdd->query($ordreSQL);
	$matches = $req->fetchAll();
	
	$tableauErreurs = array();
	
	if(sizeof($matches) == 1 && strlen($matches[0]["Matches_LienPage"]) > 0) {
		$document = new DOMDocument();
		@$document->loadHTMLFile($matches[0]["Matches_LienPage"]);
		$dateMatch = $matches[0]["Matches_Date"];
		$equipeDomicile = $matches[0]["Equipes_EquipeDomicile"];
		$equipeVisiteur = $matches[0]["Equipes_EquipeVisiteur"];
		
		$xpath = new DOMXpath($document);

		// Lecture des joueurs de l'équipe domicile
		$baliseCompo1 = $xpath->query('//td[@class="compo1"]');
		$joueurs = $baliseCompo1->item(0)->childNodes;
		$i = 0;
		foreach($joueurs as $unJoueur) {
			if($unJoueur->textContent != null) {
				$nomJoueur = trim(str_replace('\\u00ef', '&iuml;', $unJoueur->textContent));
				$retour = ajouterJoueur($bdd, $nomJoueur, $equipeDomicile, $match, $dateMatch, 1);
				if($retour == -1)
					array_push($tableauErreurs, array('equipe'=>$equipeDomicile, 'joueur'=>trim($unJoueur->textContent)));
				else if($retour == 0)
					array_push($tableauErreurs, array('equipe'=>$equipeDomicile, 'joueur'=>trim($unJoueur->textContent)));
				if(++$i == 11)
					break;
			}
		}
		
		// Lecture des joueurs de l'équipe visiteur
		$baliseCompo2 = $xpath->query('//td[@class="compo2"]');
		$joueurs = $baliseCompo2->item(0)->childNodes;
		$i = 0;
		foreach($joueurs as $unJoueur) {
			if($unJoueur->textContent != null) {
				$nomJoueur = trim(str_replace('\\u00ef', '&iuml;', $unJoueur->textContent));
				$retour = ajouterJoueur($bdd, $nomJoueur, $equipeVisiteur, $match, $dateMatch, 1);
				if($retour == -1)
					array_push($tableauErreurs, array('equipe'=>$equipeVisiteur, 'joueur'=>trim($unJoueur->textContent)));
				else if($retour == 0)
					array_push($tableauErreurs, array('equipe'=>$equipeVisiteur, 'joueur'=>trim($unJoueur->textContent)));
				if(++$i == 11)
					break;
			}
		}
	}
	echo json_encode($tableauErreurs);
?>