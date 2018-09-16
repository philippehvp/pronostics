<?php
	include('commun_administrateur.php');
	include_once('creer_match_fonctions.php');

	// Sauvegarde de la liste des cotes des joueurs

	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$equipeDomicile = isset($_POST["equipe_domicile"]) ? $_POST["equipe_domicile"] : 0;
	$equipeVisiteur = isset($_POST["equipe_visiteur"]) ? $_POST["equipe_visiteur"] : 0;
	$date = isset($_POST["date_debut_match"]) ? $_POST["date_debut_match"] : 0;
	$dateSQL = date('Y-m-d', strtotime(str_replace('/', '-', $date)));
    $listeCoteJoueurs = isset($_POST["liste_cotes_joueurs"]) ? $_POST["liste_cotes_joueurs"] : '';

    // Parcours des données HTML
    $docCotesJoueurs = new DOMDocument();
    @$docCotesJoueurs->loadHTML($listeCoteJoueurs);
    $xpath = new DOMXpath($docCotesJoueurs);

    // Recherche du tableau des cotes
    $listeJoueursEtCotes = $xpath->query("//h3[normalize-space(text())='Quel joueur marquera au moins un but ?']/ancestor::*[contains(@class, 'row')]/following-sibling::node()/descendant::td[not(@colspan)]");

    // Tableau de résultat de l'exécution de la recherche de cotes
    $tableau = array();

    // Liste des joueurs inconnus
    $tableauJoueursInconnusEquipeDomicile = array();
    $tableauJoueursInconnusEquipeVisiteur = array();

    if(!$listeJoueursEtCotes->length) {
        $tableau['nombre_cotes_detectees'] = 0;
    }
    else {
        $tableau['nombre_cotes_detectees'] = count($listeJoueursEtCotes);

        // Effacement d'une éventuelle liste précédente
        $ordreSQL =		'	DELETE FROM		joueurs_cotes ' .
                        '	WHERE			Matches_Match = ' . $match .
                        '   AND             Equipes_Equipe IN (' . $equipeDomicile . ', ' . $equipeVisiteur . ')';
        $bdd->exec($ordreSQL);

        // Parcours de la liste des joueurs et des cotes
        // Dans le parcours des cotes, si elles sont trouvées, la cote qui se trouve à gauche du tableau est celle de l'équipe domicile
        $coteEquipeDomicile = true;
        foreach ($listeJoueursEtCotes as $unJoueurEtUneCote) {
            // On ignore les cellules vides et celles dont le texte est "En voir moins"
            if(strlen(trim($unJoueurEtUneCote->nodeValue)) > 0 && trim($unJoueurEtUneCote->nodeValue) != 'En voir moins') {
                $texteEntier = trim($unJoueurEtUneCote->nodeValue);

                $mots = explode(' ', $texteEntier);
                $cote = array_pop($mots);
                $prenomNomFamille = trim(substr($texteEntier, 0, strpos($texteEntier, $cote)));

                $equipe = $coteEquipeDomicile == true ? $equipeDomicile : $equipeVisiteur;

                $joueur = rechercherJoueurInitialePrenomSansPoint($bdd, $prenomNomFamille, $equipe, $dateSQL, 3);

                if($joueur == -1 || $joueur == 0) {
                    if($coteEquipeDomicile) {
                        array_push($tableauJoueursInconnusEquipeDomicile, array('equipe'=>$equipe, 'joueur'=>$prenomNomFamille));
                    }
                    else
                        array_push($tableauJoueursInconnusEquipeVisiteur, array('equipe'=>$equipe, 'joueur'=>$prenomNomFamille));
                }
                else {
                    $ordreSQL =		'	INSERT INTO	joueurs_cotes(Joueurs_Joueur, Equipes_Equipe, Matches_Match, JoueursCotes_Cote)' .
                                    '	SELECT		' . $joueur . ', ' . $equipe . ', ' . $match . ', FLOOR(' . intval($cote) . ')';

                    $bdd->exec($ordreSQL);
                }
            }
            $coteEquipeDomicile = !$coteEquipeDomicile;
        }

        $tableau['nombre_joueurs_inconnus'] = count($tableauJoueursInconnusEquipeDomicile) + count($tableauJoueursInconnusEquipeVisiteur);
        $tableau['joueurs_inconnus_equipe_domicile'] = $tableauJoueursInconnusEquipeDomicile;
        $tableau['joueurs_inconnus_equipe_visiteur'] = $tableauJoueursInconnusEquipeVisiteur;
    }
    echo json_encode($tableau);
?>
