<?php
        // Affichage des résultats et des pronostics d'une journée
        
        // Cette page est appelée de deux manières :
        // - soit directement depuis la page consulter_resultats.php (par un include)
        // - soit par un appel Ajax : dans ce cas, il est nécessaire d'inclure le fichier d'en-tête
        
        // Lecture des paramètres passés à la page
        if(isset($_POST["journee"])) {
                include_once('commun.php');
                $journee = $_POST["journee"];
        }

        if($journee == null) {
                echo '<label>Aucune donnée à afficher</label>';
                return;
        }

        include_once('fonctions.php');
        
        // Faut-il afficher les points de qualification pour la journée ?
        $ordreSQL =     '       SELECT          Journees_PointsQualification' .
                                '       FROM            journees' .
                                '       WHERE           Journee = ' . $journee;
        $req = $bdd->query($ordreSQL);
        $donnees = $req->fetchAll();
        $afficherPointsQualification = $donnees[0]["Journees_PointsQualification"] != null ? $donnees[0]["Journees_PointsQualification"] : 0;
        
        // Bons résultats des matches d'une journée donnée
        $ordreSQL = '   SELECT DISTINCT         vue_resultatsjournees.Match' .
                                '                                               ,EquipesDomicile_NomCourt' .
                                '                                               ,EquipesVisiteur_NomCourt' .
                                '                                               ,EquipesDomicile_Nom' .
                                '                                               ,EquipesVisiteur_Nom' .
                                '                                               ,Matches_ScoreEquipeDomicile' .
                                '                                               ,Matches_ScoreAPEquipeDomicile' .
                                '                                               ,Matches_ScoreEquipeVisiteur' .
                                '                                               ,Matches_ScoreAPEquipeVisiteur' .
                                '                                               ,Matches_Vainqueur' .
                                '                                               ,EquipesDomicile_Buteurs' .
                                '                                               ,EquipesVisiteur_Buteurs' .
                                '                                               ,Matches_TypeMatch' .
                                '       FROM                            vue_resultatsjournees' .
                                '       WHERE                           Journees_Journee = ' . $journee .
                                '       ORDER BY                        vue_resultatsjournees.Match';

        $req = $bdd->query($ordreSQL);
        $resultats = $req->fetchAll();
        
        $nombreMatches = sizeof($resultats);
        if($afficherPointsQualification == 1) {
                // Attention, dans les matches de confrontation directe (en LDC et EL), la finale se joue sur un seul match
                if($nombreMatches % 2 == 1)
                        // Finale : un seul match
                        $nombreConfrontations = $nombreMatches;
                else
                        $nombreConfrontations = $nombreMatches / 2;
        }
        
        // Tous les pronostics et pronostics de buteurs d'une journée donnée
        $ordreSQL =             '           SELECT              pronostiqueurs.Pronostiqueur' .
                                        '                                       ,pronostiqueurs.Pronostiqueurs_NomUtilisateur' .
                                        '                                       ,matches.Match AS `Matches_Match`' .
                                        '                                       ,CASE' .
                                        '                                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
                                        '                                               THEN    IFNULL(pronostics.Pronostics_ScoreAPEquipeDomicile, IFNULL(pronostics.Pronostics_ScoreEquipeDomicile, \'\'))' .
                                        '                                               ELSE    \'?\'' .
                                        '                                       END AS Pronostics_ScoreEquipeDomicile' .
                                        '                                       ,CASE' .
                                        '                                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
                                        '                                               THEN    CASE' .
                                        '                                                                       WHEN    pronostics.Pronostics_ScoreAPEquipeVisiteur IS NOT NULL' .
                                        '                                                                       THEN    CASE' .
                                        '                                                                                               WHEN    Pronostics_Vainqueur IN (1, 2)' .
                                        '                                                                                               THEN    CONCAT(pronostics.Pronostics_ScoreAPEquipeVisiteur, \' TAB\')' .
                                        '                                                                                               ELSE    CONCAT(pronostics.Pronostics_ScoreAPEquipeVisiteur, \' AP\')' .
                                        '                                                                                       END' .
                                        '                                                                       ELSE    IFNULL(pronostics.Pronostics_ScoreEquipeVisiteur, \'\')' .
                                        '                                                               END' .
                                        '                                               ELSE    \'?\'' .
                                        '                                       END AS Pronostics_ScoreEquipeVisiteur' .
                                        '                                       ,CASE' .
                                        '                                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
                                        '                                               THEN    CASE' .
                                        '                                                                       WHEN    Pronostics_Vainqueur = 1' .
                                        '                                                                       THEN    IFNULL(equipes_equipedomicile.Equipes_NomCourt, equipes_equipedomicile.Equipes_Nom)' .
                                        '                                                                       WHEN    Pronostics_Vainqueur = 2' .
                                        '                                                                       THEN    IFNULL(equipes_equipevisiteur.Equipes_NomCourt, equipes_equipevisiteur.Equipes_Nom)' .
                                        '                                                                       ELSE    NULL' .
                                        '                                                               END' .
                                        '                                               ELSE    \'?\'' .
                                        '                                       END AS Pronostics_Vainqueur' .
                                        '                                       ,CASE' .
                                        '                                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
                                        '                                               THEN    IFNULL(Buteurs_Domicile.Buteurs, 0)' .
                                        '                                               ELSE    \'?\'' .
                                        '                                       END AS EquipesDomicile_Buteurs' .
                                        '                                       ,CASE' .
                                        '                                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
                                        '                                               THEN    IFNULL(Buteurs_Visiteur.Buteurs, 0)' .
                                        '                                               ELSE    \'?\'' .
                                        '                                       END AS EquipesVisiteur_Buteurs' .
                                        '                                       ,CASE' .
                                        '                                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
                                        '                                               THEN    EquipesDomicile_NomButeurs.Joueurs_Joueur' .
                                        '                                               ELSE    \'?\'' .
                                        '                                       END AS EquipesDomicile_NomButeurs' .
                                        '                                       ,CASE' .
                                        '                                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
                                        '                                               THEN    EquipesVisiteur_NomButeurs.Joueurs_Joueur' .
                                        '                                               ELSE    \'?\'' .
                                        '                                       END AS EquipesVisiteur_NomButeurs' .
                                        '                                       ,CASE' .
                                        '                                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
                                        '                                               THEN    scores.Scores_ScoreMatch' .
                                        '                                               ELSE    \'?\'' .
                                        '                                       END AS Scores_ScoreMatch' .
                                        '                                       ,CASE' .
                                        '                                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
                                        '                                               THEN    scores.Scores_ScoreButeur' .
                                        '                                               ELSE    \'?\'' .
                                        '                                       END AS Scores_ScoreButeur' .
                                        '                                       ,CASE' .
                                        '                                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
                                        '                                               THEN    scores.Scores_ScoreBonus' .
                                        '                                               ELSE    \'?\'' .
                                        '                                       END Scores_ScoreBonus' .
                                        '                                       ,Matches_Coefficient' .
                                        '                                       ,pronostics_carrefinal.PronosticsCarreFinal_Coefficient' .
                                        '               FROM            pronostiqueurs' .
                                        '               JOIN            matches' .
                                        '               LEFT JOIN       equipes equipes_equipedomicile' .
                                        '                                       ON              matches.Equipes_EquipeDomicile = equipes_equipedomicile.Equipe' .
                                        '               LEFT JOIN       equipes equipes_equipevisiteur' .
                                        '                                       ON              matches.Equipes_EquipeVisiteur = equipes_equipevisiteur.Equipe' .
                                        '               LEFT JOIN       pronostics' .
                                        '                                       ON pronostics.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
                                        '                                                       AND             matches.Match = pronostics.Matches_Match' .
                                        '               LEFT JOIN       scores' .
                                        '                                       ON              pronostiqueurs.Pronostiqueur = scores.Pronostiqueurs_Pronostiqueur' .
                                        '                                                       AND             matches.Match = scores.Matches_Match' .
                                        '               JOIN            inscriptions' .
                                        '                                       ON              pronostiqueurs.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
                                        '               JOIN            journees' .
                                        '                                       ON              matches.Journees_Journee = journees.Journee' .
                                        '                                                       AND             inscriptions.Championnats_Championnat = journees.Championnats_Championnat' .
                                        '               LEFT JOIN       (       SELECT          Matches_Match, Pronostiqueurs_Pronostiqueur, Equipes_Equipe, COUNT(*) AS Buteurs' .
                                        '                                               FROM            pronostics_buteurs' .
                                        '                                               JOIN            joueurs ON Joueurs_Joueur = Joueur' .
                                        '                                               GROUP BY        Matches_Match, Pronostiqueurs_Pronostiqueur, Equipes_Equipe' .
                                        '                                       ) AS Buteurs_Domicile' .
                                        '                                       ON              Buteurs_Domicile.Matches_Match = matches.Match' .
                                        '                                                       AND             Buteurs_Domicile.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
                                        '                                                       AND             Buteurs_Domicile.Equipes_Equipe = equipes_equipedomicile.Equipe' .
                                        '               LEFT JOIN       (       SELECT          Matches_Match, Pronostiqueurs_Pronostiqueur, Equipes_Equipe, COUNT(*) AS Buteurs' .
                                        '                                               FROM            pronostics_buteurs' .
                                        '                                               JOIN            joueurs ON Joueurs_Joueur = Joueur' .
                                        '                                               GROUP BY        Matches_Match, Pronostiqueurs_Pronostiqueur, Equipes_Equipe' .
                                        '                                       ) AS Buteurs_Visiteur' .
                                        '                                       ON              Buteurs_Visiteur.Matches_Match = matches.Match' .
                                        '                                                       AND             Buteurs_Visiteur.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
                                        '                                                       AND             Buteurs_Visiteur.Equipes_Equipe = equipes_equipevisiteur.Equipe' .
                                        '               LEFT JOIN       (' .
                                        '                                               SELECT          Pronostiqueurs_Pronostiqueur, Matches_Match, Equipes_Equipe, GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Joueurs_Joueur' .
                                        '                                               FROM            pronostics_buteurs' .
                                        '                                               JOIN            joueurs' .
                                        '                                                                       ON              pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
                                        '                                               JOIN            matches' .
                                        '                                                                       ON              pronostics_buteurs.Matches_Match = matches.Match' .
                                        '                                               WHERE           matches.Journees_Journee = ' . $journee .
                                        '                                               GROUP BY        Pronostiqueurs_Pronostiqueur, Matches_Match, Equipes_Equipe' .
                                        '                                       ) EquipesDomicile_NomButeurs' .
                                        '                                       ON              pronostics.Pronostiqueurs_Pronostiqueur = EquipesDomicile_NomButeurs.Pronostiqueurs_Pronostiqueur' .
                                        '                                                       AND             pronostics.Matches_Match = EquipesDomicile_NomButeurs.Matches_Match '.
                                        '                                                       AND             equipes_equipedomicile.Equipe = EquipesDomicile_NomButeurs.Equipes_Equipe' .
                                        '               LEFT JOIN       (' .
                                        '                                               SELECT          Pronostiqueurs_Pronostiqueur, Matches_Match, Equipes_Equipe, GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Joueurs_Joueur' .
                                        '                                               FROM            pronostics_buteurs' .
                                        '                                               JOIN            joueurs' .
                                        '                                                                       ON              pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
                                        '                                               JOIN            matches' .
                                        '                                                                       ON              pronostics_buteurs.Matches_Match = matches.Match' .
                                        '                                               WHERE           matches.Journees_Journee = ' . $journee .
                                        '                                               GROUP BY        Pronostiqueurs_Pronostiqueur, Matches_Match, Equipes_Equipe' .
                                        '                                       ) EquipesVisiteur_NomButeurs' .
                                        '                                       ON              pronostics.Pronostiqueurs_Pronostiqueur = EquipesVisiteur_NomButeurs.Pronostiqueurs_Pronostiqueur' .
                                        '                                                       AND             pronostics.Matches_Match = EquipesVisiteur_NomButeurs.Matches_Match '.
                                        '                                                       AND             equipes_equipevisiteur.Equipe = EquipesVisiteur_NomButeurs.Equipes_Equipe' .
                                        '               LEFT JOIN       pronostics_carrefinal' .
                                        '                                       ON              matches.Match = pronostics_carrefinal.Matches_Match' .
                                        '                                                       AND             pronostiqueurs.Pronostiqueur = pronostics_carrefinal.Pronostiqueurs_Pronostiqueur' .
                                        '               WHERE           journees.Journee = ' . $journee .
                                        '               ORDER BY        Pronostiqueur, matches.Match';

        $req = $bdd->query($ordreSQL);
        $pronostics = $req->fetchAll();
        $nombrePronostiqueurs = sizeof($pronostics) / ($nombreMatches);
        // Le nombre total de points, de points buteur et de points qualification
        $ordreSQL =             '               SELECT          CASE' .
                                        '                                               WHEN    vue_pronostiqueursmatches.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
                                        '                                               THEN' .
                                        '                                                               CASE' .
                                        '                                                                       WHEN    journees_rattrapage.JourneesRattrapage_Points IS NULL' .
                                        '                                                                       THEN    SUM(IFNULL(Scores_ScoreMatch, 0)) + SUM(IFNULL(Scores_ScoreButeur, 0)) + SUM(IFNULL(Scores_ScoreBonus, 0)) + SUM(IFNULL(Scores_ScoreQualification, 0))' .
                                        '                                                                       ELSE    CONCAT(SUM(IFNULL(Scores_ScoreMatch, 0)) + SUM(IFNULL(Scores_ScoreButeur, 0)) + SUM(IFNULL(Scores_ScoreBonus, 0)) + SUM(IFNULL(Scores_ScoreQualification, 0)), \' + \', journees_rattrapage.JourneesRattrapage_Points)' .
                                        '                                                               END' .
                                        '                                               ELSE    \'?\'' .
                                        '                                       END AS Scores_Total' .
                                        '                                       ,CASE' .
                                        '                                               WHEN    vue_pronostiqueursmatches.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_matchpronostiquable(matches.Match, ' . $_SESSION["pronostiqueur"] . ') = 0' .
                                        '                                               THEN    CAST(SUM(IFNULL(Scores_ScoreButeur, 0)) AS CHAR(5))' .
                                        '                                               ELSE    \'?\'' .
                                        '                                       END AS Scores_TotalButeur' .
                                        '                                       ,CASE' .
                                        '                                               WHEN    vue_pronostiqueursmatches.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_matchpronostiquable(matches.Match, ' . $_SESSION["pronostiqueur"] . ') = 0' .
                                        '                                               THEN    CAST(SUM(IFNULL(IFNULL(Scores_ScoreQualification , 0) * IFNULL(pronostics_carrefinal.PronosticsCarreFinal_Coefficient, 0), 0)) AS CHAR(5))' .
                                        '                                               ELSE    \'?\'' .
                                        '                                       END AS Scores_Qualification' .
                                        '                                       ,CASE' .
                                        '                                               WHEN    vue_pronostiqueursmatches.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_matchpronostiquable(matches.Match, ' . $_SESSION["pronostiqueur"] . ') = 0' .
                                        '                                               THEN    CAST(SUM(IFNULL(IFNULL(Scores_ScoreBonus , 0) * IFNULL(pronostics_carrefinal.PronosticsCarreFinal_Coefficient, 0), 0)) AS CHAR(5))' .
                                        '                                               ELSE    \'?\'' .
                                        '                                       END AS Scores_Bonus' .
                                        '                                       , pronostics_carrefinal.PronosticsCarreFinal_Coefficient' .
                                        '               FROM            vue_pronostiqueursmatches' .
                                        '               LEFT JOIN       scores' .
                                        '                                       ON              vue_pronostiqueursmatches.Pronostiqueurs_Pronostiqueur = scores.Pronostiqueurs_Pronostiqueur' .
                                        '                                                       AND vue_pronostiqueursmatches.Matches_Match = scores.Matches_Match' .
                                        '               LEFT JOIN       journees_rattrapage' .
                                        '                                       ON              vue_pronostiqueursmatches.Pronostiqueurs_Pronostiqueur = journees_rattrapage.Pronostiqueurs_Pronostiqueur' .
                                        '                                                       AND             vue_pronostiqueursmatches.Journees_Journee = journees_rattrapage.Journees_Journee' .
                                        '               JOIN            matches' .
                                        '                                       ON              vue_pronostiqueursmatches.Matches_Match = matches.Match' .
                                        '               JOIN            journees' .
                                        '                                       ON              vue_pronostiqueursmatches.Journees_Journee = journees.Journee' .
                                        '                                                       AND             vue_pronostiqueursmatches.Championnats_Championnat = journees.Championnats_Championnat' .
                                        '               LEFT JOIN       pronostics_carrefinal' .
                                        '                                       ON              vue_pronostiqueursmatches.Matches_Match = pronostics_carrefinal.Matches_Match' .
                                        '                                                       AND             vue_pronostiqueursmatches.Pronostiqueurs_Pronostiqueur = pronostics_carrefinal.Pronostiqueurs_Pronostiqueur' .
                                        '               WHERE           matches.Journees_Journee = ' . $journee .
                                        '               GROUP BY        vue_pronostiqueursmatches.Pronostiqueurs_Pronostiqueur' .
                                        '               ORDER BY        vue_pronostiqueursmatches.Pronostiqueurs_Pronostiqueur';

        $req = $bdd->query($ordreSQL);
        $totaux = $req->fetchAll();
        
        if($afficherPointsQualification == 1) {
                // Les noms des équipes gagnantes
                $ordreSQL =             '               SELECT          Equipes_NomCourt, Equipes_Nom' .
                                                '               FROM            vue_vainqueursreelsretour' .
                                                '               JOIN            matches' .
                                                '                                       ON              vue_vainqueursreelsretour.Matches_Match = matches.Match' .
                                                '               WHERE           vue_vainqueursreelsretour.Journees_Journee = ' . $journee .
                                                '                                       AND             (' .
                                                '                                                               matches.Match = matches.Matches_MatchLie + 1' .
                                                '                                                               OR              matches.Matches_MatchLie IS NULL' .
                                                '                                                       )';

                $req = $bdd->query($ordreSQL);
                $vainqueursReels = $req->fetchAll();
                
                // Les noms des équipes pronostiquées gagnantes par le pronostiqueur
                $ordreSQL =             '               SELECT          CASE' .
                                                '                                               WHEN    vue_vainqueurspronosticsretouravecpoints.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
                                                '                                               THEN    Equipes_Nom' .
                                                '                                               ELSE    \'?\'' .
                                                '                                       END AS Equipes_Nom' .
                                                '                                       ,Vainqueur_Trouve' .
                                                '                                       ,PronosticsCarreFinal_Coefficient' .
                                                '               FROM            vue_vainqueurspronosticsretouravecpoints' .
                                                '               JOIN            journees' .
                                                '                                       ON              vue_vainqueurspronosticsretouravecpoints.Journees_Journee = journees.Journee' .
                                                '                                                       AND             vue_vainqueurspronosticsretouravecpoints.Championnats_Championnat = journees.Championnats_Championnat' .
                                                '               JOIN            matches' .
                                                '                                       ON              vue_vainqueurspronosticsretouravecpoints.Matches_Match = matches.Match' .
                                                '               WHERE           vue_vainqueurspronosticsretouravecpoints.Journees_Journee = ' . $journee .
                                                '                                       AND             (' .
                                                '                                                               matches.Match = matches.Matches_MatchLie + 1' .
                                                '                                                               OR              matches.Matches_MatchLie IS NULL' .
                                                '                                                       )';

                $req = $bdd->query($ordreSQL);
                $vainqueursPronostics = $req->fetchAll();
        }

        // Affichage des bons résultats et de tous les pronostics
        echo '<table class="tableau--resultat" id="tablePronostics">';
                echo '<thead>';
                        echo '<tr>';
                                echo '<th>Résultats</th>';
                                foreach($resultats as $resultat) {
                                        // Pour chaque ligne de résultat, on affiche le score final, les buteurs
                                        $equipeDomicileButeurs = $resultat["EquipesDomicile_Buteurs"] != null ? $resultat["EquipesDomicile_Buteurs"] : 'Aucun';
                                        $equipeVisiteurButeurs = $resultat["EquipesVisiteur_Buteurs"] != null ? $resultat["EquipesVisiteur_Buteurs"] : 'Aucun';
                                        echo '<th class="curseur-main" title="' . $resultat["EquipesDomicile_Nom"] . ' : ' . $equipeDomicileButeurs . '&#13' . $resultat["EquipesVisiteur_Nom"] . ' : ' . $equipeVisiteurButeurs . '" onclick="consulterResultats_afficherMatch(' . $resultat["Match] . ', \'' . $resultat["EquipesDomicile_Nom"] . '\', \'' . $resultat["EquipesVisiteur_Nom"] . '\', 0, 0);">';
                                                echo $resultat["EquipesDomicile_NomCourt"] . '<br />' . $resultat["EquipesVisiteur_NomCourt"] . '<br />';
                                                
                                                // Appel de la fonction d'affichage du score du match
                                                $scoreAffiche = formaterScoreMatch($resultat["Matches_ScoreEquipeDomicile"], $resultat["Matches_ScoreAPEquipeDomicile"], $resultat["Matches_ScoreEquipeVisiteur"], $resultat["Matches_ScoreAPEquipeVisiteur"], $resultat["Matches_Vainqueur"]);
                                                
                                                echo $scoreAffiche;
                                                
                                        echo '</th>';
                                }
                                echo '<th>Total</th>';
                                echo '<th>Total buteur</th>';
                                if($afficherPointsQualification == 1) {

                                        for($a = 0; $a < $nombreConfrontations; $a++)
                                                echo '<th title="' . $vainqueursReels[$a][1] . '">' . $vainqueursReels[$a][0] . '</th>';

                                        echo '<th>Total qualification</th>';
                                }
                        echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                        for($i = 0; $i < $nombrePronostiqueurs; $i++) {
                                echo '<tr>';
                                        for($j = 0; $j < $nombreMatches + 2; $j++) {
                                                if($j == 1) {

                                                        echo '<td class="curseur-main centre" onclick="consulterResultats_afficherPronostiqueur(' . $pronostics[($i * $nombreMatches)][0] . ', \'' . $pronostics[($i * $nombreMatches)][1] . '\', ' . $journee . ');">' . $pronostics[($i * $nombreMatches)][1] . '</td>';
                                                }
                                                else if($j > 1) {
                                                        $indice = ($i * $nombreMatches) + $j - 2;
                                                        $scoreMatch = $pronostics[$indice][10] != null ? $pronostics[$indice][10] : '?';
                                                        $scoreMatch == -1 ? '?' : $scoreMatch;
                                                        $scoreButeur = $pronostics[$indice][11] != null ? $pronostics[$indice][11] : '?';
                                                        $scoreButeur == -1 ? '?' : $scoreButeur;
                                                        $scoreBonus = $pronostics[$indice][12] != null ? $pronostics[$indice][12] : '?';
                                                        $scoreBonus == -1 ? '?' : $scoreBonus;
                                                        if($scoreMatch != '?' && $scoreButeur != '?' && $scoreBonus != '?')
                                                                $scoreTotal = $scoreMatch + $scoreButeur + $scoreBonus;
                                                        else
                                                                $scoreTotal = '?';
                                                                
                                                        if($pronostics[$indice][6] == '?')
                                                                $nomButeursDomicile = '?';
                                                        else if($pronostics[$indice][6] == 0)
                                                                $nomButeursDomicile = 'Aucun';
                                                        else
                                                                $nomButeursDomicile = $pronostics[$indice][8];
                                                                
                                                        if($pronostics[$indice][7] == '?')
                                                                $nomButeursVisiteur = '?';
                                                        else if($pronostics[$indice][7] == 0)
                                                                $nomButeursVisiteur = 'Aucun';
                                                        else
                                                                $nomButeursVisiteur = $pronostics[$indice][9];
                                                                
                                                        $coefficient = $pronostics[$indice][13];
                                                        if($scoreMatch / $coefficient < 5)
                                                                $style = 'blanc';
                                                        else if($scoreMatch / $coefficient >= 5 && $scoreMatch / $coefficient < 10)
                                                                $style = 'orange';
                                                        else
                                                                $style = 'vert';
                                                                
                                                        $pronosticsScoreEquipeDomicile = $pronostics[$indice][3];
                                                        $pronosticsScoreEquipeVisiteur = $pronostics[$indice][4];
                                                        $pronosticsVainqueur = $pronostics[$indice][5];
                                                        
                                                        if($pronosticsVainqueur == '?')
                                                                $pronosticsAffiches = '?';
                                                        else {
                                                                if($pronosticsVainqueur == null)
                                                                        $pronosticsAffiches = $pronosticsScoreEquipeDomicile . '-' . $pronosticsScoreEquipeVisiteur;
                                                                else {
                                                                        if($resultat["Matches_TypeMatch"] == 4)
                                                                                $pronosticsAffiches = $pronosticsScoreEquipeDomicile . '-' . $pronosticsScoreEquipeVisiteur . '<br />' . $pronosticsVainqueur;
                                                                        else
                                                                                $pronosticsAffiches = $pronosticsScoreEquipeDomicile . '-' . $pronosticsScoreEquipeVisiteur;
                                                                }
                                                        }
                                                        
                                                        $coefficientCarreFinal = $pronostics[$indice][14];
                                                        if($coefficientCarreFinal == 2)
                                                                $coeff = ' coeff2';
                                                        else if($coefficientCarreFinal == 3)
                                                                $coeff = ' coeff3';
                                                        else
                                                                $coeff = '';
                                                                
                                                        echo '<td class="' . $style . $coeff . '">';
                                                                echo '<div title="' . $scoreMatch . ' | ' . $scoreButeur . ' | ' . $scoreBonus . '">' . $scoreTotal . '</div>';
                                                                echo '<div>' . $pronosticsAffiches . '</div>';
                                                                echo '<div title="' . $nomButeursDomicile . '&#13' . $nomButeursVisiteur . '">';
                                                                        if($pronostics[$indice][6] == 0)
                                                                                echo '<label>&Oslash;</label>';
                                                                        else
                                                                                for($iButeur = 0; $iButeur < $pronostics[$indice][6]; $iButeur++)
                                                                                        if($style == 'blanc')
                                                                                                echo '<img src="images/ballon_noir.png" alt="Buteur"/>';
                                                                                        else
                                                                                                echo '<img src="images/ballon.png" alt="Buteur"/>';
                                                                                        
                                                                        echo '<label> - </label>';
                                                                        
                                                                        if($pronostics[$indice][7] == 0)
                                                                                echo '<label>&Oslash;</label>';
                                                                        else
                                                                                for($iButeur = 0; $iButeur < $pronostics[$indice][7]; $iButeur++)
                                                                                        if($style == 'blanc')
                                                                                                echo '<img src="images/ballon_noir.png" alt="Buteur"/>';
                                                                                        else
                                                                                                echo '<img src="images/ballon.png" alt="Buteur"/>';
                                                                echo '</div>';
                                                        echo '</td>';
                                                }

                                        }
                                        echo '<td>' . $totaux[$i]["Scores_Total"] . '</td>';
                                        echo '<td>' . $totaux[$i]["Scores_TotalButeur"] . '</td>';
                                        if($afficherPointsQualification == 1) {
                                                // Parcours de la liste des équipes pronostiquées gagnantes
                                                for($k = 0; $k < $nombreConfrontations; $k++) {
                                                        if($vainqueursPronostics[($i * $nombreConfrontations) + $k][1] == 1)
                                                                $style = 'vert';
                                                        else
                                                                $style = 'blanc';
                                                                
                                                        $coefficientCarreFnalQualifies = $vainqueursPronostics[($i * $nombreConfrontations) + $k][2];
                                                        if($coefficientCarreFnalQualifies == 2)
                                                                $coeffQualifies = ' coeff2';
                                                        else if($coefficientCarreFnalQualifies == 3)
                                                                $coeffQualifies = ' coeff3';
                                                        else
                                                                $coeffQualifies = '';
                                                        
                                                        echo '<td class="' . $style . $coeffQualifies . ' ' . ($i * $nombreConfrontations) . '">';
                                                                echo $vainqueursPronostics[($i * $nombreConfrontations) + $k][0];
                                                        echo '</td>';
                                                }
                                                if($resultat["Matches_TypeMatch"] == 3)
                                                        echo '<td>' . $totaux[$i]["Scores_Qualification"]. '</td>';
                                                else
                                                        echo '<td>' . $totaux[$i]["Scores_Bonus"] . '</td>';
                                        }

                                echo '</tr>';
                        }
                echo '</tbody>';
        echo '</table>';

        ?>
        
        <script>
        
                $(function () {
                        var hauteur = '600px';
                        var oTable = $('#tablePronostics').dataTable({"scrollCollapse": true, "scrollY": hauteur, "scrollX": true, "bPaginate": false, "bFilter": false, "bInfo": false, "bSort": false});
                        var obj = new $.fn.dataTable.FixedColumns(oTable);
                        
                        // Changement de l'adresse URL de la page pour qu'elle reflète le numéro de journée
                        var stateObj = { foo: 'bar' };
                        history.pushState(stateObj, "Le Poulpe d'Or", "consulter_resultats.php?journee=" + $('#selectJournee').val());
                        
                });
        </script>
                
