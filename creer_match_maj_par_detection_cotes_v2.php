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

    // Liste des joueurs inconnus des deux équipes
    $tableauJoueursInconnusEquipeDomicile = array();
	$tableauJoueursInconnusEquipeVisiteur = array();

    // Effacement d'une éventuelle liste précédente
    $ordreSQL =		'	DELETE FROM		joueurs_cotes ' .
                    '	WHERE			Matches_Match = ' . $match .
                    '   AND             Equipes_Equipe IN (' . $equipeDomicile . ', '. $equipeVisiteur . ')';
    $bdd->exec($ordreSQL);

	$tableau['nombreCotesDetectees'] = 0;
	$tableau['nombreJoueursInconnus'] = 0;

	// Recherche du tableau des cotes de l'équipe domicile
	$listeJoueursEtCotes = $xpath->query("//tr[contains(@class, 'home-team-outcome')]");

	if($listeJoueursEtCotes->length) {
		$tableau['nombreCotesDetectees'] += $listeJoueursEtCotes->length;

	    // Parcours de la liste des joueurs et des cotes
	    foreach ($listeJoueursEtCotes as $unJoueurEtCotes) {
			// Parcours de tous les noeuds enfants
			$cotes = $unJoueurEtCotes;
			$i = 0;
			$cote = 0;
			foreach($cotes->childNodes as $unNoeud) {
				if($unNoeud->nodeName == 'td' && trim($unNoeud->nodeValue) != '') {
					if($i == 0)
						// Lecture du nom du joueur
						$prenomNomFamille = trim($unNoeud->nodeValue);
					else if($i == $colonneCote) {
						// Cote buteur
						$cote = trim($unNoeud->nodeValue);
						break;
					}
					$i++;
				}
			}

	        $joueur = rechercherJoueurInitialePrenomSansPoint($bdd, $prenomNomFamille, $equipeDomicile, $dateSQL, 3);

	        if($joueur == -1 || $joueur == 0) {
	        	array_push($tableauJoueursInconnusEquipeDomicile, array('equipe'=>$equipeDomicile, 'joueur'=>$prenomNomFamille));
	        }
	        else {
	            $ordreSQL =		'	INSERT INTO	joueurs_cotes(Joueurs_Joueur, Equipes_Equipe, Matches_Match, JoueursCotes_Cote)' .
	                            '	SELECT		' . $joueur . ', ' . $equipeDomicile . ', ' . $match . ', FLOOR(' . intval($cote) . ')';

	            $bdd->exec($ordreSQL);
	        }
	    }

	    $tableau['nombreJoueursInconnus'] += count($tableauJoueursInconnusEquipeDomicile);
	    $tableau['joueursInconnusEquipeDomicile'] = $tableauJoueursInconnusEquipeDomicile;
	}

	// Recherche du tableau des cotes de l'équipe visiteur
	$listeJoueursEtCotes = $xpath->query("//tr[contains(@class, 'away-team-outcome')]");

	if($listeJoueursEtCotes->length) {
		$tableau['nombreCotesDetectees'] += $listeJoueursEtCotes->length;

	    // Parcours de la liste des joueurs et des cotes
	    foreach ($listeJoueursEtCotes as $unJoueurEtCotes) {
			// Parcours de tous les noeuds enfants
			$cotes = $unJoueurEtCotes;
			$i = 0;
			$cote = 0;
			foreach($cotes->childNodes as $unNoeud) {
				if($unNoeud->nodeName == 'td' && trim($unNoeud->nodeValue) != '') {
					if($i == 0)
						// Lecture du nom du joueur
						$prenomNomFamille = trim($unNoeud->nodeValue);
					else if($i == $colonneCote) {
						// Cote buteur
						$cote = trim($unNoeud->nodeValue);
						break;
					}
					$i++;
				}
			}
			
	        $joueur = rechercherJoueurInitialePrenomSansPoint($bdd, $prenomNomFamille, $equipeVisiteur, $dateSQL, 3);

	        if($joueur == -1 || $joueur == 0) {
	        	array_push($tableauJoueursInconnusEquipeVisiteur, array('equipe'=>$equipeVisiteur, 'joueur'=>$prenomNomFamille));
	        }
	        else {
	            $ordreSQL =		'	INSERT INTO	joueurs_cotes(Joueurs_Joueur, Equipes_Equipe, Matches_Match, JoueursCotes_Cote)' .
	                            '	SELECT		' . $joueur . ', ' . $equipeVisiteur . ', ' . $match . ', FLOOR(' . intval($cote) . ')';

	            $bdd->exec($ordreSQL);
	        }
	    }

	    $tableau['nombreJoueursInconnus'] += count($tableauJoueursInconnusEquipeVisiteur);
	    $tableau['joueursInconnusEquipeVisiteur'] = $tableauJoueursInconnusEquipeVisiteur;
	}

    echo json_encode($tableau);

?>
