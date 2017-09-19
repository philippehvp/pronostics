<?php
	header('Content-type: text/html; charset=utf-8');
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

		$divComposition = $xpath->query('//div[contains(@class, "MEDpanelcomposition")]');
		if(!$divComposition)
			return;

		$tableauComposition = $xpath->query('div[@class="panel-body"]/table/tbody/tr/td', $divComposition->item(0));

		// Equipe domicile
		$htmlEquipeDomicile = remplacerCaracteres(utf8_decode(trim($document->saveHTML($tableauComposition->item(0)))));
		$htmlEquipeDomicile = preg_replace('/<[^>]*>/', ',', $htmlEquipeDomicile);
		$joueursEquipeDomicile = explode(",", $htmlEquipeDomicile);

		// Equipe visiteur
		$htmlEquipeVisiteur = remplacerCaracteres(utf8_decode(trim($document->saveHTML($tableauComposition->item(1)))));
		$htmlEquipeVisiteur = preg_replace('/<[^>]*>/', ',', $htmlEquipeVisiteur);
		$joueursEquipeVisiteur = explode(",", $htmlEquipeVisiteur);

		// Lecture des joueurs de l'équipe domicile
		foreach($joueursEquipeDomicile as $unJoueur) {
			if($unJoueur && trim($unJoueur) != "") {
				$nomJoueurModifie = remplacerCaracteres(trim($unJoueur));
				
				$retour = rechercherJoueur($bdd, $nomJoueurModifie, $equipeDomicile, $dateMatch, 1);
				if($retour == -1)
					$retour = rechercherJoueurInitialePrenom($bdd, $nomJoueurModifie, $equipeDomicile, $dateMatch, 1);
				if($retour == -1)
					array_push($tableauErreurs, array('equipe'=>$equipeDomicile, 'joueur'=>$nomJoueurModifie));
				else if($retour == 0)
					array_push($tableauErreurs, array('equipe'=>$equipeDomicile, 'joueur'=>$nomJoueurModifie));
			}
		}

		// Lecture des joueurs de l'équipe visiteur
		foreach($joueursEquipeVisiteur as $unJoueur) {
			if($unJoueur && trim($unJoueur) != "") {
				$nomJoueurModifie = remplacerCaracteres(trim($unJoueur));

				$retour = rechercherJoueur($bdd, $nomJoueurModifie, $equipeVisiteur, $dateMatch, 1);
				if($retour <= 0)
					$retour = rechercherJoueurInitialePrenom($bdd, $nomJoueurModifie, $equipeVisiteur, $dateMatch, 1);
				if($retour == -1)
					array_push($tableauErreurs, array('equipe'=>$equipeVisiteur, 'joueur'=>$nomJoueurModifie));
				else if($retour == 0)
					array_push($tableauErreurs, array('equipe'=>$equipeVisiteur, 'joueur'=>$nomJoueurModifie));
			}
		}
	}
	
	echo json_encode($tableauErreurs);
?>