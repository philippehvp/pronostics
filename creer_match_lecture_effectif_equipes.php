<?php
	header('Content-type: text/html; charset=utf-8');
	// Lecture des effectifs des deux équipes d'un match

	include_once('creer_match_fonctions.php');
	$tableauErreurs['joueurs'] = array();

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

		$tableauCompositionDomicile = $xpath->query('div[@class="panel-body"]/table/tbody/tr/td/div/span[@class="rating"]/preceding-sibling::a', $divComposition->item(0));
		$tableauCompositionVisiteur = $xpath->query('div[@class="panel-body"]/table/tbody/tr/td/div/span[@class="rating"]/preceding-sibling::a', $divComposition->item(1));

		// Lecture des joueurs de l'équipe domicile
		foreach($tableauCompositionDomicile as $unJoueur) {
			if ($unJoueur) {
				$nomJoueurModifie = trim($unJoueur->nodeValue);
				$retour = ajouterJoueur($bdd, $nomJoueurModifie, $equipeDomicile, $match, $dateMatch, 1);
				if($retour == -1)
					array_push($tableauErreurs, array('equipe'=>$equipeDomicile, 'joueur'=>$nomJoueurModifie));
				else if($retour == 0)
					array_push($tableauErreurs, array('equipe'=>$equipeDomicile, 'joueur'=>$nomJoueurModifie));
			}
		};

		// Lecture des joueurs de l'équipe visiteur
		foreach($tableauCompositionVisiteur as $unJoueur) {
			$nomJoueurModifie = trim($unJoueur->nodeValue);
			$retour = ajouterJoueur($bdd, $nomJoueurModifie, $equipeVisiteur, $match, $dateMatch, 1);
			if($retour == -1)
				array_push($tableauErreurs, array('equipe'=>$equipeVisiteur, 'joueur'=>$nomJoueurModifie));
			else if($retour == 0)
				array_push($tableauErreurs, array('equipe'=>$equipeVisiteur, 'joueur'=>$nomJoueurModifie));
		};
	}

	echo json_encode($tableauErreurs);
?>