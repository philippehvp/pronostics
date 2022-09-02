<?php

	// Lecture des matches d'une journée pour remplissage automatique des matches AR de coupes européennes
	include_once('creer_match_fonctions.php');

	// Lecture des paramètres passés à la page
    $match = isset($_POST["match"]) ? $_POST["match"] : 0;
    $lienAller = isset($_POST["journeeLienPage"]) ? $_POST["journeeLienPage"] : "";
    $lienRetour = isset($_POST["journeeLienPageRetour"]) ? $_POST["journeeLienPageRetour"] : "";

    if($lienAller == "" || $lienRetour == "") {
        return;
    }

    $documentAller = new DOMDocument();
    @$documentAller->loadHTMLFile($lienAller);
    $documentRetour = new DOMDocument();
	@$documentRetour->loadHTMLFile($lienRetour);

	$tableauErreurs = array();

    $xpathAller = new DOMXpath($documentAller);
    $xpathRetour = new DOMXpath($documentRetour);

    $divLiveScoreAller = $xpathAller->query('//div[@id="livescore"]');
    $divLiveScoreRetour = $xpathRetour->query('//div[@id="livescore"]');
    if(!$divLiveScoreAller || !$divLiveScoreRetour) {
        return;
    }

    $tableAller = $xpathAller->query('.//table', $divLiveScoreAller->item(0));
    $tableRetour = $xpathRetour->query('.//table', $divLiveScoreRetour->item(0));
    $tableauDatesAller = $xpathAller->query('.//thead', $tableAller->item(0));
    $tableauDatesRetour = $xpathRetour->query('.//thead', $tableRetour->item(0));
    $tableauMatchesAller = $xpathAller->query('.//tr', $tableAller->item(0));
    $tableauMatchesRetour = $xpathRetour->query('.//tr', $tableRetour->item(0));

    $tableauTRAller = $xpathAller->query('.//tr', $tableAller->item(0));
    $tableauTRRetour = $xpathRetour->query('.//tr', $tableRetour->item(0));

    // Match aller
    $tableauMatchesAller = array();
    foreach($tableauTRAller as $unTRAller) {
        if($unTRAller->getAttribute('data-matchid')) {
            $heureAller = $xpathAller->query(".//td[contains(@class, 'lm1')]", $unTRAller);
            if($dateRemanieeAller) {
                $dateMatchAller = DateTime::createFromFormat('d m Y H:i:s', $dateRemanieeAller . ' ' . $heureAller->item(0)->nodeValue . ':00');
                $noeudMatchAller = $xpathAller->query(".//td[contains(@class, 'lm3')]/a", $unTRAller);
                $nomDuMatchAller = $noeudMatchAller->item(0)->getAttribute('title');
                $nomDuMatchAller = str_replace('Détail du match : ', '', $nomDuMatchAller);
                $equipesAller = explode(' - ', $nomDuMatchAller);
                $equipe1Aller = rechercherEquipeDepuisNomCorrespondanceComplementaire($bdd, $equipesAller[0]);
                $equipe2Aller = rechercherEquipeDepuisNomCorrespondanceComplementaire($bdd, $equipesAller[1]);
                if($equipe1Aller != 0 && $equipe2Aller != 0) {
                    // Ajout du match aller pour parcours ultérieur
                    array_push($tableauMatchesAller, array('dateMatch'=>$dateMatchAller, 'equipe1'=>$equipe1Aller, 'equipe2'=>$equipe2Aller));
                } else {
                    if($equipe1Aller == 0)
                        echo 'Pas de correspondance pour ' . $equipesAller[0];

                    if($equipe2Aller == 0)
                        echo 'Pas de correspondance pour ' . $equipesAller[1];
                }
            }
        } else {
            $dateLueAller = trim($unTRAller->nodeValue);
            if($dateLueAller != "") {
                $dateRemanieeAller = str_ireplace(
                    ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'],
                    ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'], $dateLueAller);
                $dateRemanieeAller = str_ireplace(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'], '', $dateRemanieeAller);
                $dateRemanieeAller = trim($dateRemanieeAller);
            }
        }
    }

    // Match retour
    $tableauMatchesRetour = array();
    foreach($tableauTRRetour as $unTRRetour) {
        if($unTRRetour->getAttribute('data-matchid')) {
            $heureRetour = $xpathRetour->query(".//td[contains(@class, 'lm1')]", $unTRRetour);
            if($dateRemanieeRetour) {
                $dateMatchRetour = DateTime::createFromFormat('d m Y H:i:s', $dateRemanieeRetour . ' ' . $heureRetour->item(0)->nodeValue . ':00');
                $noeudMatchRetour = $xpathRetour->query(".//td[contains(@class, 'lm3')]/a", $unTRRetour);
                $nomDuMatchRetour = $noeudMatchRetour->item(0)->getAttribute('title');
                $nomDuMatchRetour = str_replace('Détail du match : ', '', $nomDuMatchRetour);
                $equipesRetour = explode(' - ', $nomDuMatchRetour);
                $equipe1Retour = rechercherEquipeDepuisNomCorrespondanceComplementaire($bdd, $equipesRetour[0]);
                $equipe2Retour = rechercherEquipeDepuisNomCorrespondanceComplementaire($bdd, $equipesRetour[1]);
                if($equipe1Retour != 0 && $equipe2Retour != 0) {
                    // Ajout du match retour pour parcours ultérieur
                    array_push($tableauMatchesRetour, array('dateMatch'=>$dateMatchRetour, 'equipe1'=>$equipe1Retour, 'equipe2'=>$equipe2Retour));
                } else {
                    if($equipe1Retour == 0)
                        echo 'Pas de correspondance pour ' . $equipesRetour[0];

                    if($equipe2Retour == 0)
                        echo 'Pas de correspondance pour ' . $equipesRetour[1];
                }
            }
        } else {
            $dateLueRetour = trim($unTRRetour->nodeValue);
            if($dateLueRetour != "") {
                $dateRemanieeRetour = str_ireplace(
                    ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'],
                    ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'], $dateLueRetour);
                $dateRemanieeRetour = str_ireplace(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'], '', $dateRemanieeRetour);
                $dateRemanieeRetour = trim($dateRemanieeRetour);
            }
        }
    }

    // Arrivé ici, on assemble les matches aller et retour en combinant les deux tableaux de match
    $indiceMatch = 0;
    foreach($tableauMatchesAller as $matchAller) {
        // Recherche du match retour correspondant au match aller en cours
        $matchRetourTrouve = false;
        for($i = 0; $i < sizeof($tableauMatchesRetour) && $matchRetourTrouve == false; $i++) {
            if($tableauMatchesRetour[$i]["equipe2"] == $matchAller["equipe1"] || $tableauMatchesRetour[$i]["equipe1"] == $matchAller["equipe2"]) {
                $matchRetourTrouve = true;
                $matchRetour = $tableauMatchesRetour[$i];
            }
        }

        if($matchRetourTrouve) {
            inscrireEquipesDansMatch($bdd, ($match + $indiceMatch), $matchAller["dateMatch"], $matchAller["equipe1"], $matchAller["equipe2"]);
            $indiceMatch++;
            inscrireEquipesDansMatch($bdd, ($match + $indiceMatch), $matchRetour["dateMatch"], $matchAller["equipe2"], $matchAller["equipe1"]);
            $indiceMatch++;
        }
    }

	echo json_encode($tableauErreurs);
?>