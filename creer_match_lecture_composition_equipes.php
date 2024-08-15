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

		$divComposition = $xpath->query('//div[contains(@class, "MEDpanelcomposition")]');
		if(!$divComposition)
			return;

		$tableauCompositionDomicile = $xpath->query('div[@class="panel-body"]/table/tbody/tr/td/div/span[@class="rating"]/preceding-sibling::a', $divComposition->item(0));
		$tableauCompositionVisiteur = $xpath->query('div[@class="panel-body"]/table/tbody/tr/td/div/span[@class="rating"]/preceding-sibling::a', $divComposition->item(1));

		// Lecture des joueurs de l'équipe domicile
		$i = 0;
		foreach($tableauCompositionDomicile as $unJoueur) {
			if ($unJoueur && $i < 11) {
				$nomJoueurModifie = trim($unJoueur->nodeValue);
				$retour = ajouterJoueur($bdd, $nomJoueurModifie, $equipeDomicile, $match, $dateMatch, 1);
				if($retour == -1)
					array_push($tableauErreurs, array('equipe'=>$equipeDomicile, 'joueur'=>$nomJoueurModifie));
				else if($retour == 0)
					array_push($tableauErreurs, array('equipe'=>$equipeDomicile, 'joueur'=>$nomJoueurModifie));
				$i++;
			}
		};

		// Lecture des joueurs de l'équipe visiteur
		$i = 0;
		foreach($tableauCompositionVisiteur as $unJoueur) {
			if ($unJoueur && $i < 11) {
				$nomJoueurModifie = trim($unJoueur->nodeValue);
				$retour = ajouterJoueur($bdd, $nomJoueurModifie, $equipeVisiteur, $match, $dateMatch, 1);
				if($retour == -1)
					array_push($tableauErreurs, array('equipe'=>$equipeVisiteur, 'joueur'=>$nomJoueurModifie));
				else if($retour == 0)
					array_push($tableauErreurs, array('equipe'=>$equipeVisiteur, 'joueur'=>$nomJoueurModifie));
				$i++;
			}
		};
	}
	echo json_encode($tableauErreurs);
?>