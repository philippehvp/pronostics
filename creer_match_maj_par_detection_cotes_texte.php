<?php
	include('commun_administrateur.php');
	include_once('creer_match_fonctions.php');

	// Sauvegarde de la liste des cotes des joueurs

	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
	$date = isset($_POST["date_debut_match"]) ? $_POST["date_debut_match"] : 0;
	$dateSQL = date('Y-m-d', strtotime(str_replace('/', '-', $date)));
    $listeCoteJoueurs = isset($_POST["liste_cotes_joueurs"]) ? $_POST["liste_cotes_joueurs"] : '';

	// Le numéro de la colonne contenant la cote utile est contenu dans une table de configuration
	$ordreSQL =		'	SELECT		Configurations_ColonneCote' .
					'	FROM		configurations' .
					'	WHERE		Configuration = 1';
	$req = $bdd->query($ordreSQL);
	$colonneCote = $req->fetchAll();

    // Parcours des données HTML
    $docCotesJoueurs = new DOMDocument();
    @$docCotesJoueurs->loadHTML($listeCoteJoueurs);
    $xpath = new DOMXpath($docCotesJoueurs);

    // Recherche du tableau des cotes
    //$listeJoueursEtCotes = $xpath->query("//h3[normalize-space(text())='Quel joueur marquera au moins un but ?']/ancestor::*[contains(@class, 'row')]/following-sibling::node()/descendant::td[not(@colspan)]");
    //$listeJoueursEtCotes = $xpath->query("//tr[contains(@class, 'home-team-outcome') or contains(@class, 'away-team-outcome')]/td[position()='3']");
    $listeJoueursEtCotes = $xpath->query("//tr[contains(@class, 'home-team-outcome') or contains(@class, 'away-team-outcome')]/td/em");

    // Tableau de résultat de l'exécution de la recherche de cotes
    $tableau = array();

    // Liste des joueurs inconnus
    $tableauJoueursInconnus = array();

    if(!$listeJoueursEtCotes->length) {
        $tableau['nombre_cotes_detectees'] = 0;
    }
    else {
        $tableau['nombre_cotes_detectees'] = count($listeJoueursEtCotes);

        // Effacement d'une éventuelle liste précédente
        $ordreSQL =		'	DELETE FROM		joueurs_cotes ' .
                        '	WHERE			Matches_Match = ' . $match .
                        '   AND             Equipes_Equipe =' . $equipe;
        //$bdd->exec($ordreSQL);

        // Parcours de la liste des joueurs et des cotes
        foreach ($listeJoueursEtCotes as $unJoueurEtUneCote) {
			//echo trim($unJoueurEtUneCote->nodeName);
			//$cotes = $unJoueurEtUneCote->parentNode->nextSibling;
			$cotes = $xpath->query('/parent::*', $unJoueurEtUneCote);
			foreach($cotes as $uneCote)
				echo $uneCote->nodeName;
			/*foreach($cotes as $uneCote)
				echo 'DEBUT-' . trim($uneCote->nodeValue) . '-FIN';*/

			continue;
            /*$texteEntier = trim($unJoueurEtUneCote->nodeValue);

            $mots = explode(' ', $texteEntier);
            $cote = array_pop($mots);
            $prenomNomFamille = trim(substr($texteEntier, 0, strpos($texteEntier, $cote)));

            $joueur = rechercherJoueurInitialePrenomSansPoint($bdd, $prenomNomFamille, $equipe, $dateSQL, 3);

            if($joueur == -1 || $joueur == 0) {
                if($coteEquipeDomicile) {
                    array_push($tableauJoueursInconnusDomicile, array('equipe'=>$equipe, 'joueur'=>$prenomNomFamille));
                }
                else
                    array_push($tableauJoueursInconnusVisiteur, array('equipe'=>$equipe, 'joueur'=>$prenomNomFamille));
            }
            else {
                $ordreSQL =		'	INSERT INTO	joueurs_cotes(Joueurs_Joueur, Equipes_Equipe, Matches_Match, JoueursCotes_Cote)' .
                                '	SELECT		' . $joueur . ', ' . $equipe . ', ' . $match . ', FLOOR(' . intval($cote) . ')';

                $bdd->exec($ordreSQL);
            }*/
        }

        $tableau['nombre_joueurs_inconnus'] = count($tableauJoueursInconnus);
        $tableau['joueurs_inconnus_equipe'] = $tableauJoueursInconnus;
    }

    //echo json_encode($tableau);

?>
