<?php

    // Lecture des matches d'une journée pour remplissage automatique en Ligue 1 et en journée de poule des
    // coupes d'Europe (Ligue des Champions et Europa League)
	include_once('creer_match_fonctions.php');

    function verifierSiMatchEstEuropeenL1($bdd, $match) {
        $ordreSQL =     '       SELECT      matches.Matches_L1Europe' .
                        '       FROM        matches' .
                        '       WHERE       matches.Match = ' . $match;
        $req = $bdd->query($ordreSQL);
        $matches = $req->fetchAll();
        if(sizeof($matches) == 1 && $matches[0]["Matches_L1Europe"] == 1) {
            return 1;
        }

        return 0;
    }

	// Lecture des paramètres passés à la page
    $match = isset($_POST["match"]) ? $_POST["match"] : 0;
    $lien = isset($_POST["journeeLienPage"]) ? $_POST["journeeLienPage"] : "";

    if($lien == "") {
        return;
    }

    $document = new DOMDocument();
	@$document->loadHTMLFile($lien);
	$tableauErreurs = array();
	
    $xpath = new DOMXpath($document);

    $divLiveScore = $xpath->query('//div[@id="livescore"]');
    if(!$divLiveScore) {
        return;
    }

    $table = $xpath->query('.//table', $divLiveScore->item(0));
    $tableauDates = $xpath->query('.//thead', $table->item(0));
    $tableauMatches = $xpath->query('.//tr', $table->item(0));
    
    $tableauTR = $xpath->query('.//tr', $table->item(0));
    $indiceMatch = 0;
    foreach($tableauTR as $unTR) {
        if($unTR->getAttribute('data-matchid')) {
            $heure = $xpath->query(".//td[contains(@class, 'lm1')]", $unTR);
            if($dateRemaniee) {
                $dateMatch = DateTime::createFromFormat('d m Y H:i:s', $dateRemaniee . ' ' . $heure->item(0)->nodeValue . ':00');
                $noeudMatch = $xpath->query(".//td[contains(@class, 'lm3')]/a", $unTR);
                $nomDuMatch = $noeudMatch->item(0)->getAttribute('title');
                $nomDuMatch = str_replace('Détail du match : ', '', $nomDuMatch);
                $equipes = explode(' - ', $nomDuMatch);
                $equipe1 = rechercherEquipeDepuisNomCorrespondanceComplementaire($bdd, $equipes[0]);
                $equipe2 = rechercherEquipeDepuisNomCorrespondanceComplementaire($bdd, $equipes[1]);
                if($equipe1 != 0 && $equipe2 != 0) {
                    // Arrivé ici, on vérifie que le match à inscrire n'est pas le match européen de Ligue 1
                    // Auquel cas il faut passer au match suivant
                    $estMatchEuropeenL1 = verifierSiMatchEstEuropeenL1($bdd, ($match + $indiceMatch));

                    // Si le match sur lequel on veut écrire le match en cours de lecture est un match européen de Ligue 1
                    // Il faut alors passer sur le match suivant
                    if($estMatchEuropeenL1 == 1) {
                        $indiceMatch++;
                    }
                    inscrireEquipesDansMatch($bdd, ($match + $indiceMatch), $dateMatch, $equipe1, $equipe2);

                    $indiceMatch++;
                }
            }
        } else {
            $dateLue = trim($unTR->nodeValue);
            if($dateLue != "") {
                $dateRemaniee = str_ireplace(
                    ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'],
                    ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'], $dateLue);
                $dateRemaniee = str_ireplace(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'], '', $dateRemaniee);
                $dateRemaniee = trim($dateRemaniee);
            }
        }
    }

	echo json_encode($tableauErreurs);
?>