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
				$retour = ajouterJoueur($bdd, $nomJoueurModifie, $equipeDomicile, $match, $dateMatch, 1);
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
				$retour = ajouterJoueur($bdd, $nomJoueurModifie, $equipeVisiteur, $match, $dateMatch, 1);
				if($retour == -1)
					array_push($tableauErreurs, array('equipe'=>$equipeVisiteur, 'joueur'=>$nomJoueurModifie));
				else if($retour == 0)
					array_push($tableauErreurs, array('equipe'=>$equipeVisiteur, 'joueur'=>$nomJoueurModifie));
				if(++$i == 11)
					break;
			}
		}
	}
	echo json_encode($tableauErreurs);
?>