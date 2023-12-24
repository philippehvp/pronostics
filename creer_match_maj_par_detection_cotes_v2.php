<?php
	include_once('commun_administrateur.php');
	include_once('creer_match_fonctions.php');

	// Sauvegarde de la liste des cotes des joueurs

	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$equipeDomicile = isset($_POST["equipeDomicile"]) ? $_POST["equipeDomicile"] : 0;
	$equipeVisiteur = isset($_POST["equipeVisiteur"]) ? $_POST["equipeVisiteur"] : 0;
	$date = isset($_POST["dateDebutMatch"]) ? $_POST["dateDebutMatch"] : 0;
	$dateSQL = date('Y-m-d', strtotime(str_replace('/', '-', $date)));
    $listeCoteJoueurs = isset($_POST["listeCotesJoueurs"]) ? $_POST["listeCotesJoueurs"] : '';

	// Le numéro de la colonne contenant la cote utile est contenu dans une table de configuration
	$ordreSQL =		'	SELECT		Configurations_ColonneCote' .
					'	FROM		configurations' .
					'	WHERE		Configuration = 1';
	$req = $bdd->query($ordreSQL);
	$colonneCote = $req->fetchAll()[0]["Configurations_ColonneCote"];

    // Parcours des données HTML
    $docCotesJoueurs = new DOMDocument();
    @$docCotesJoueurs->loadHTML($listeCoteJoueurs);
    $xpath = new DOMXpath($docCotesJoueurs);

	// Tableau de résultat de l'exécution de la recherche de cotes
	$tableau = array();

    // Liste des joueurs inconnus
    $tableauJoueursInconnus = array();

	// Liste des joueurs présents dans les deux équipes
	$tableauJoueursDoublon = array();

    // Effacement d'une éventuelle liste précédente
    $ordreSQL =		'	DELETE FROM		joueurs_cotes ' .
                    '	WHERE			Matches_Match = ' . $match .
                    '   AND             Equipes_Equipe IN (' . $equipeDomicile . ', '. $equipeVisiteur . ')';
    $bdd->exec($ordreSQL);

	// Recherche du tableau des cotes de l'équipe domicile
	$listeJoueurs = $xpath->query("//div[contains(@class, 'sb-event-view__outcome-name')]");
	$listeCotes = $xpath->query("//div[contains(@class, 'sb-event-view__outcome-value')]");

	// On fabrique les cotes dans le même ordre que celui des joueurs
	$cotes = array();
	foreach($listeCotes as $uneCote) {
		array_push($cotes, trim($uneCote->textContent));
	}

	// Parcours des joueurs
	$i = 0;
	foreach($listeJoueurs as $unJoueur) {
		$equipe = 0;
		$joueur = 0;
		$nom = trim($unJoueur->textContent);

		// Recherche du joueur dans les deux équipes
		$joueurDomicile = rechercherJoueur($bdd, $nom, $equipeDomicile, $dateSQL, 3);
		$joueurVisiteur = rechercherJoueur($bdd, $nom, $equipeVisiteur, $dateSQL, 3);

		if($joueurDomicile > 0 && $joueurVisiteur > 0) {
			// Joueur trouvé dans les deux équipes
			array_push($tableauJoueursDoublon, array('joueur'=>$nom));
		} else if($joueurDomicile > 0 && $joueurVisiteur <= 0) {
			// Joueur trouvé dans l'équipe domicile seulement
			$equipe = $equipeDomicile;
			$joueur = $joueurDomicile;
		} else if($joueurDomicile <= 0 && $joueurVisiteur > 0) {
			// Joueur trouvé dans l'équipe visiteur seulement
			$equipe = $equipeVisiteur;
			$joueur = $joueurVisiteur;
		} else {
			// Joueur non trouvé
			array_push($tableauJoueursInconnus, array('joueur'=>$nom));
		}

		// Le fait d'avoir une équipe connue permet de savoir qu'il n'y a pas de souci
		if($equipe != 0) {
			// Le joueur a été trouvé et on sait dans quelle équipe
			// Il faut déterminer sa cote
			if ($i < count($cotes)) {
				$cote = $cotes[$i];
	
				if ($cote) {
					$ordreSQL =		'	INSERT INTO	joueurs_cotes(Joueurs_Joueur, Equipes_Equipe, Matches_Match, JoueursCotes_Cote)' .
									'	SELECT		' . $joueur . ', ' . $equipe . ', ' . $match . ', FLOOR(' . intval($cote) . ')';
					$bdd->exec($ordreSQL);
				}
			}
		}

		$i++;
	}

	$tableau['nombreCotesDetectees'] = $listeJoueurs->length;

	$tableau['nombreJoueursInconnus'] = count($tableauJoueursInconnus);
	$tableau['joueursInconnus'] = $tableauJoueursInconnus;

	$tableau['nombreJoueursDoublon'] = count($tableauJoueursDoublon);
	$tableau['joueursDoublon'] = $tableauJoueursDoublon;

    echo json_encode($tableau);

?>
