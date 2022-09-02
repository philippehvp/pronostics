<?php
    include_once('commun_administrateur.php');

    function mettreAJourScoreDomicile($pronostiqueur, $match, $score) {
        $ordreSQL =     '   INSERT INTO pronostics      (   Pronostiqueurs_Pronostiqueur,' .
                        '                                   Matches_Match,' .
                        '                                   Pronostics_ScoreEquipeDomicile' .
                        '                               )' .
                        '   VALUES( ' . $pronostiqueur . ',' . $match . ',' . $score . ')' .
                        '   ON DUPLICATE KEY' .
                        '   UPDATE Pronostics_ScoreEquipeDomicile = ' . $score;
        return $ordreSQL;
    }

    function mettreAJourScoreVisiteur($pronostiqueur, $match, $score) {
        $ordreSQL =     '   INSERT INTO pronostics      (   Pronostiqueurs_Pronostiqueur,' .
                        '                                   Matches_Match,' .
                        '                                   Pronostics_ScoreEquipeVisiteur' .
                        '                               )' .
                        '   VALUES( ' . $pronostiqueur . ',' . $match . ',' . $score . ')' .
                        '   ON DUPLICATE KEY' .
                        '   UPDATE Pronostics_ScoreEquipeVisiteur = ' . $score;
        return $ordreSQL;
    }

    function mettreAJourScoreAPDomicile($pronostiqueur, $match, $score) {
        $ordreSQL =     '   INSERT INTO pronostics      (   Pronostiqueurs_Pronostiqueur,' .
                        '                                   Matches_Match,' .
                        '                                   Pronostics_ScoreAPEquipeDomicile' .
                        '                               )' .
                        '   VALUES( ' . $pronostiqueur . ',' . $match . ',' . $score . ')' .
                        '   ON DUPLICATE KEY' .
                        '   UPDATE Pronostics_ScoreAPEquipeDomicile = ' . $score;
        return $ordreSQL;
    }

    function mettreAJourScoreAPVisiteur($pronostiqueur, $match, $score) {
        $ordreSQL =     '   INSERT INTO pronostics      (   Pronostiqueurs_Pronostiqueur,' .
                        '                                   Matches_Match,' .
                        '                                   Pronostics_ScoreAPEquipeVisiteur' .
                        '                               )' .
                        '   VALUES( ' . $pronostiqueur . ',' . $match . ',' . $score . ')' .
                        '   ON DUPLICATE KEY' .
                        '   UPDATE Pronostics_ScoreAPEquipeVisiteur = ' . $score;
        return $ordreSQL;
    }

    function mettreAJourVainqueur($pronostiqueur, $match, $vainqueur) {
        $ordreSQL =     '   INSERT INTO pronostics      (   Pronostiqueurs_Pronostiqueur,' .
                        '                                   Matches_Match,' .
                        '                                   Pronostics_Vainqueur' .
                        '                               )' .
                        '   VALUES( ' . $pronostiqueur . ',' . $match . ',' . $vainqueur . ')' .
                        '   ON DUPLICATE KEY' .
                        '   UPDATE Pronostics_Vainqueur = ' . $vainqueur;
        return $ordreSQL;
    }

    function mettreAJourButeurs($pronostiqueur, $match, $equipe, $contenu) {
        $ordresSQL = array();
        if($contenu == '') {
            // Aucun buteur
            $ordreSQL =     '   DELETE FROM         pronostics_buteurs' .
                            '   WHERE               Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
                            '                       AND     Matches_Match = ' . $match .
                            '                       AND     Equipes_Equipe = ' . $equipe;
            array_push($ordresSQL, $ordreSQL);
        } else {
            // Un ou plusieurs buteurs
            // Il faut toujours supprimer tous les pronostics buteurs avant d'en insérer pour éviter les doublons
            $ordreSQL =     '   DELETE FROM         pronostics_buteurs' .
                            '   WHERE               Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
                            '                       AND     Matches_Match = ' . $match .
                            '                       AND     Equipes_Equipe = ' . $equipe;
            array_push($ordresSQL, $ordreSQL);

            $parseChaine = explode(';', $contenu);
            foreach($parseChaine as $joueur) {
                if($joueur) {
                    $ordreSQL =     '   INSERT INTO     pronostics_buteurs  (   Pronostiqueurs_Pronostiqueur,' .
                                    '                                           Matches_Match,' .
                                    '                                           Joueurs_Joueur,' .
                                    '                                           Equipes_Equipe' .
                                    '                                       )' .
                                    '   VALUES(' . $pronostiqueur . ',' . $match . ',' . $joueur . ',' . $equipe . ')';
                    array_push($ordresSQL, $ordreSQL);
                }
            }
        }
        return $ordresSQL;
    }

    function mettreAJourCanal($pronostiqueur, $journee, $match) {
        $ordreSQL =     '   REPLACE INTO journees_pronostiqueurs_canal      (   Journees_Journee,' .
                        '                                                       Pronostiqueurs_Pronostiqueur,' .
                        '                                                       Matches_Match' .
                        '                                                   )' .
                        '   VALUES( ' . $journee . ',' . $pronostiqueur . ',' . $match . ')';
        return $ordreSQL;
    }

    // Injection des traces de la journée dont le numéro a été passé en paramètre
    $journee = $_POST["journee"] ? $_POST["journee"] : 0;
    $match = $_POST["match"] ? $_POST["match"] : 0;

    // Lecture des matches de la journée si le paramètre journee a été fourni ou du numéro de match dans le cas contraire
    if($journee) {
        $ordreSQL =		'	SELECT		matches.Match'.
                        '	FROM		matches' .
                        '	WHERE		matches.Journees_Journee = ' . $journee;
    } else if($match) {
        $ordreSQL =		'	SELECT		matches.Match'.
                        '	FROM		matches' .
                        '	WHERE		matches.Match = ' . $match;
    } else {
        // Aucun paramètre fourni
        return;
    }
    $req = $bdd->query($ordreSQL);
    $matches = $req->fetchAll();

    // Parcours des traces dont les noms commencent par le numéro des matches de la journée ou du match demandé

    // Pour les scores
    foreach($matches as $unMatch) {
        $masqueFichier = '../traces/scores/' . $unMatch["Match"] . '_*.txt';
        $fichiers = glob($masqueFichier);
        foreach($fichiers as $fichier) {
            // Extraction du nom du fichier seul
            $parseChaine = explode('/', $fichier);
            $nomFichierAvecExtension = end($parseChaine);
            $parseChaine = explode('.', $nomFichierAvecExtension);
            $nomFichier = $parseChaine[0];
            $parseChaine = explode('_', $nomFichier);

            $match = $parseChaine[0];           // Normalement égal à $unMatch["Match"]
            $pronostiqueur = $parseChaine[1];
            $type = $parseChaine[2];
            $contenu = file_get_contents($fichier);

            if($type == 'score' || $type == 'scoreAP') {
                $equipe = $parseChaine[3];
                if($type == 'score' && $equipe == 'D') {
                    $ordreSQL = mettreAJourScoreDomicile($pronostiqueur, $match, $contenu);
                } else if($type == 'score' && $equipe == 'V') {
                    $ordreSQL = mettreAJourScoreVisiteur($pronostiqueur, $match, $contenu);
                } else if($type == 'scoreAP' && $equipe == 'D') {
                    $ordreSQL = mettreAJourScoreAPDomicile($pronostiqueur, $match, $contenu);
                } else if($type == 'scoreAP' && $equipe == 'V') {
                    $ordreSQL = mettreAJourScoreAPVisiteur($pronostiqueur, $match, $contenu);
                }
            } else if($type == 'vainqueur') {
                $ordreSQL = mettreAJourVainqueur($pronostiqueur, $match, $contenu);
            }

            echo $ordreSQL;
            $bdd->exec($ordreSQL);
        }
    }

    // Pour les buteurs
    foreach($matches as $unMatch) {
        $masqueFichier = '../traces/buteurs/' . $unMatch["Match"] . '_*.txt';
        $fichiers = glob($masqueFichier);
        foreach($fichiers as $fichier) {
            // Extraction du nom du fichier seul
            $parseChaine = explode('/', $fichier);
            $nomFichierAvecExtension = end($parseChaine);
            $parseChaine = explode('.', $nomFichierAvecExtension);
            $nomFichier = $parseChaine[0];
            $parseChaine = explode('_', $nomFichier);

            $match = $parseChaine[0];           // Normalement égal à $unMatch["Match"]
            $pronostiqueur = $parseChaine[1];
            $equipe = $parseChaine[2];
            $contenu = file_get_contents($fichier);

            $ordresSQL = mettreAJourButeurs($pronostiqueur, $match, $equipe, $contenu);
            foreach($ordresSQL as $ordreSQL) {
                echo $ordreSQL;
                $bdd->exec($ordreSQL);
            }
        }
    }

    // Pour le match Canal, uniquement en mode réinjection des traces de la journée
    if($journee) {
        $masqueFichier = '../traces/canal/' . $journee . '_*.txt';
        $fichiers = glob($masqueFichier);
        foreach($fichiers as $fichier) {
            // Extraction du nom du fichier seul
            $parseChaine = explode('/', $fichier);
            $nomFichierAvecExtension = end($parseChaine);
            $parseChaine = explode('.', $nomFichierAvecExtension);
            $nomFichier = $parseChaine[0];
            $parseChaine = explode('_', $nomFichier);

            $match = $parseChaine[0];
            $pronostiqueur = $parseChaine[1];
            $contenu = file_get_contents($fichier);

            $ordreSQL = mettreAJourCanal($pronostiqueur, $journee, $contenu);
            $bdd->exec($ordreSQL);
        }
    }
?>
