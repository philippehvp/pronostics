<?php
    include('commun.php');
    include_once('fonctions.php');

    // Affichage du détail d'une confrontation
    
    // Lecture des paramètres passés à la page
    $confrontation = isset($_POST["confrontation"]) ? $_POST["confrontation"] : 0;
    $saison = isset($_POST["saison"]) ? $_POST["saison"] : 0;

    // Bons résultats des matches de la journée de la confrontation
    $ordreSQL = '   SELECT      DISTINCT matches.Match' .
                '               ,IFNULL(equipes_equipedomicile.Equipes_NomCourt, equipes_equipedomicile.Equipes_Nom) AS EquipesDomicile_NomCourt' .
                '               ,IFNULL(equipes_equipevisiteur.Equipes_NomCourt, equipes_equipevisiteur.Equipes_Nom) AS EquipesVisiteur_NomCourt' .
                '               ,equipes_equipedomicile.Equipes_Nom AS EquipesDomicile_Nom' .
                '               ,equipes_equipevisiteur.Equipes_Nom AS EquipesVisiteur_Nom' .
                '               ,matches.Matches_ScoreEquipeDomicile' .
                '               ,matches.Matches_ScoreAPEquipeDomicile' .
                '               ,matches.Matches_ScoreEquipeVisiteur' .
                '               ,matches.Matches_ScoreAPEquipeVisiteur' .
                '               ,matches.Matches_Vainqueur' .
                '               ,(' .
                '                   SELECT      GROUP_CONCAT(   (' .
                '                                                       CASE' .
                '                                                           WHEN    (matches_buteurs.Buteurs_CSC = 0)' .
                '                                                           THEN    CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille), \' (\', fn_calculcotebuteur(matches_buteurs.Buteurs_Cote), \')\')' .
                '                                                           ELSE    CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille), \' (CSC)\')' .
                '                                                       END' .
                '                                                   ) SEPARATOR \', \'' .
                '                                               )' .
                '                   FROM        matches_buteurs_prec matches_buteurs' .
                '                   JOIN        joueurs' .
                '                               ON      matches_buteurs.Joueurs_Joueur = joueurs.Joueur' .
                '                   WHERE       matches_buteurs.Saisons_Saison = matches.Saisons_Saison' .
                '                               AND     matches_buteurs.Matches_Match = matches.Match' .
                '                               AND     (' .
                '                                           (' .
                '                                               matches_buteurs.Equipes_Equipe = matches.Equipes_EquipeDomicile' .
                '                                               AND     matches_buteurs.Buteurs_CSC = 0' .
                '                                           )' .
                '                                           OR' .
                '                                           (' .
                '                                               matches_buteurs.Equipes_Equipe = matches.Equipes_EquipeVisiteur' .
                '                                               AND     matches_buteurs.Buteurs_CSC = 1' .
                '                                           )' .
                '                                       )' .
                '               ) AS EquipesDomicile_Buteurs' .
                '               ,(' .
                '                   SELECT      GROUP_CONCAT(   (' .
                '                                                       CASE' .
                '                                                           WHEN    matches_buteurs.Buteurs_CSC = 0' .
                '                                                           THEN    CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille), \' (\', fn_calculcotebuteur(matches_buteurs.Buteurs_Cote), \')\')' .
                '                                                           ELSE    CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille), \' (CSC)\')' .
                '                                                       END' .
                '                                                   ) SEPARATOR \', \'' .
                '                                               )' .
                '                   FROM        matches_buteurs_prec matches_buteurs' .
                '                   JOIN        joueurs' .
                '                               ON      matches_buteurs.Joueurs_Joueur = joueurs.Joueur' .
                '                   WHERE       matches_buteurs.Saisons_Saison = matches.Saisons_Saison' .
                '                               AND     matches_buteurs.Matches_Match = matches.Match' .
                '                               AND     (' .
                '                                           (' .
                '                                               matches_buteurs.Equipes_Equipe = matches.Equipes_EquipeVisiteur' .
                '                                               AND     matches_buteurs.Buteurs_CSC = 0' .
                '                                           )' .
                '                                           OR' .
                '                                           (' .
                '                                               matches_buteurs.Equipes_Equipe = matches.Equipes_EquipeDomicile' .
                '                                               AND     matches_buteurs.Buteurs_CSC = 1' .
                '                                           )' .
                '                                       )' .
                '               ) AS EquipesVisiteur_Buteurs' .
                '               ,matches.Matches_PointsQualificationEquipeDomicile' .
                '               ,matches.Matches_PointsQualificationEquipeVisiteur' .
                '               ,matches.Matches_Coefficient' .
                '   FROM        matches_prec matches' .
                '   LEFT JOIN   equipes equipes_equipedomicile' .
                '               ON      matches.Equipes_EquipeDomicile = equipes_equipedomicile.Equipe' .
                '   LEFT JOIN   equipes equipes_equipevisiteur' .
                '               ON      matches.Equipes_EquipeVisiteur = equipes_equipevisiteur.Equipe' .
                '   JOIN        confrontations_prec confrontations' .
                '               ON      matches.Journees_Journee = confrontations.Journees_Journee' .
                '                       AND     matches.Saisons_Saison = confrontations.Saisons_Saison' .
                '   WHERE       confrontations.Confrontation = ' . $confrontation .
                '               AND     confrontations.Saisons_Saison = ' . $saison .
                '   ORDER BY    matches.Match';
    $req = $bdd->query($ordreSQL);
    $resultats = $req->fetchAll();
    $nombreMatches = sizeof($resultats);

    
    function lirePronostics($bdd, $pronostiqueur, $pronostiqueurDetail, $saison, $match, &$pronostics) {
        // Tous les pronostics et pronostics de buteurs des pronostiqueurs de la confrontation pour la journée
        $ordreSQL =     '       SELECT DISTINCT     matches.Match' .
                        '                           ,fn_calculprecisionpronostic_prec(' . $saison . ', matches.Match, ' . $pronostiqueurDetail . ') AS Performance' .
                        '                           ,CASE' .
                        '                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
                        '                               THEN    pronostics.Pronostics_ScoreEquipeDomicile' .
                        '                               ELSE    \'?\'' .
                        '                           END AS Pronostics_ScoreEquipeDomicile' .
                        '                           ,CASE' .
                        '                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
                        '                               THEN    pronostics.Pronostics_ScoreAPEquipeDomicile' .
                        '                               ELSE    \'?\'' .
                        '                           END AS Pronostics_ScoreAPEquipeDomicile' .
                        '                           ,CASE' .
                        '                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
                        '                               THEN    pronostics.Pronostics_ScoreEquipeVisiteur' .
                        '                               ELSE    \'?\'' .
                        '                           END AS Pronostics_ScoreEquipeVisiteur' .
                        '                           ,CASE' .
                        '                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
                        '                               THEN    pronostics.Pronostics_ScoreAPEquipeVisiteur' .
                        '                               ELSE    \'?\'' .
                        '                           END AS Pronostics_ScoreAPEquipeVisiteur' .
                        '                           ,CASE' .
                        '                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
                        '                               THEN    Pronostics_Vainqueur' .
                        '                               ELSE    \'?\'' .
                        '                           END AS Pronostics_Vainqueur' .
                        '                           ,CASE' .
                        '                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
                        '                               THEN    Buteurs_Domicile.Buteurs' .
                        '                               ELSE    \'?\'' .
                        '                           END AS EquipesDomicile_Buteurs' .
                        '                           ,CASE' .
                        '                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
                        '                               THEN    Buteurs_Visiteur.Buteurs' .
                        '                               ELSE    \'?\'' .
                        '                           END AS EquipesVisiteur_Buteurs' .
                        '                           ,CASE' .
                        '                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
                        '                               THEN    ButeursInvalides_Domicile.Buteurs' .
                        '                               ELSE    \'?\'' .
                        '                           END AS EquipesDomicile_ButeursInvalides' .
                        '                           ,CASE' .
                        '                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
                        '                               THEN    ButeursInvalides_Visiteur.Buteurs' .
                        '                               ELSE    \'?\'' .
                        '                           END AS EquipesVisiteur_ButeursInvalides' .
                        '                           ,CASE' .
                        '                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
                        '                               THEN    ButeursAbsents_Domicile.Buteurs' .
                        '                               ELSE    \'?\'' .
                        '                           END AS EquipesDomicile_ButeursAbsents' .
                        '                           ,CASE' .
                        '                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
                        '                               THEN    ButeursAbsents_Visiteur.Buteurs' .
                        '                               ELSE    \'?\'' .
                        '                           END AS EquipesVisiteur_ButeursAbsents' .
                        '                           ,CASE' .
                        '                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
                        '                               THEN    IFNULL(scores.Scores_ScoreMatch, 0)' .
                        '                               ELSE    \'?\'' .
                        '                           END AS Scores_ScoreMatch' .
                        '                           ,CASE' .
                        '                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
                        '                               THEN    IFNULL(scores.Scores_ScoreButeur, 0)' .
                        '                               ELSE    \'?\'' .
                        '                           END AS Scores_ScoreButeur' .
                        '                           ,CASE' .
                        '                               WHEN    pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
                        '                               THEN    IFNULL(scores.Scores_ScoreBonus, 0)' .
                        '                               ELSE    \'?\'' .
                        '                           END Scores_ScoreBonus' .
                        '                           ,Matches_Coefficient' .
                        '       FROM                (' .
                        '                               SELECT      Pronostiqueur' .
                        '                               FROM        pronostiqueurs' .
                        '                               UNION ALL' .
                        '                               SELECT      Pronostiqueur' .
                        '                               FROM        pronostiqueurs_anciens' .
                        '                           ) pronostiqueurs' .
                        '       JOIN                matches_prec matches' .
                        '       LEFT JOIN           equipes equipes_equipedomicile' .
                        '                           ON      matches.Equipes_EquipeDomicile = equipes_equipedomicile.Equipe' .
                        '       LEFT JOIN           equipes equipes_equipevisiteur' .
                        '                           ON      matches.Equipes_EquipeVisiteur = equipes_equipevisiteur.Equipe' .
                        '       LEFT JOIN           pronostics_prec pronostics' .
                        '                           ON pronostics.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
                        '                           AND     matches.Match = pronostics.Matches_Match' .
                        '       LEFT JOIN           scores_prec scores' .
                        '                           ON      pronostiqueurs.Pronostiqueur = scores.Pronostiqueurs_Pronostiqueur' .
                        '                           AND     matches.Match = scores.Matches_Match' .
                        '       JOIN                journees_prec journees' .
                        '                           ON      matches.Journees_Journee = journees.Journee' .
                        '       LEFT JOIN           (   SELECT      pronostics_buteurs.Matches_Match' .
                        '                                           ,pronostics_buteurs.Equipes_Equipe' .
                        '                                           ,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
                        '                               FROM        (' .
                        '                                               SELECT      Matches_Match' .
                        '                                                           ,Joueurs_Joueur' .
                        '                                                           ,Equipes_Equipe' .
                        '                                                           ,CASE' .
                        '                                                               WHEN    @match = Matches_Match' .
                        '                                                                       AND     @joueur = Joueurs_Joueur' .
                        '                                                                       AND     @equipe = Equipes_Equipe' .
                        '                                                               THEN    @indicePronostics := @indicePronostics + 1' .
                        '                                                               ELSE    (@indicePronostics := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
                        '                                                           END AS Pronostics_Indice' .
                        '                                               FROM        pronostics_buteurs_prec pronostics_buteurs' .
                        '                                               JOIN        matches_prec matches' .
                        '                                                           ON      pronostics_buteurs.Matches_Match = matches.Match' .
                        '                                               JOIN        (   SELECT      @indicePronostics := 0, @match := NULL, @joueur := NULL, @equipe := NULL    ) r' .
                        '                                               WHERE       pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurDetail .
                        '                                                           AND     matches.Match = ' . $match .
                        '                                                           AND     pronostics_buteurs.Saisons_Saison = ' . $saison .
                        '                                                           AND     matches.Saisons_Saison = ' . $saison .
                        '                                               ORDER BY    pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
                        '                                           ) pronostics_buteurs' .
                        '                               JOIN        (' .
                        '                                               SELECT      Matches_Match' .
                        '                                                           ,Joueurs_Joueur' .
                        '                                                           ,Equipes_Equipe' .
                        '                                                           ,CASE' .
                        '                                                               WHEN    @match = Matches_Match' .
                        '                                                                       AND     @joueur = Joueurs_Joueur' .
                        '                                                                       AND     @equipe = Equipes_Equipe' .
                        '                                                               THEN    @indiceMatches := @indiceMatches + 1' .
                        '                                                               ELSE    (@indiceMatches := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
                        '                                                           END AS Matches_Indice' .
                        '                                               FROM        matches_buteurs_prec matches_buteurs' .
                        '                                               JOIN        matches_prec matches' .
                        '                                                           ON      matches_buteurs.Matches_Match = matches.Match' .
                        '                                               JOIN        (   SELECT      @indiceMatches := 0, @joueur := NULL, @equipe := NULL   ) r' .
                        '                                               WHERE       matches.Match = ' . $match .
                        '                                                           AND     matches_buteurs.Saisons_Saison = ' . $saison .
                        '                                                           AND     matches.Saisons_Saison = ' . $saison .
                        '                                               ORDER BY    Matches_Match, Joueurs_Joueur, Equipes_Equipe' .
                        '                                           ) matches_buteurs' .
                        '                                           ON      pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
                        '                                                   AND     pronostics_buteurs.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
                        '                                                   AND     pronostics_buteurs.Equipes_Equipe = matches_buteurs.Equipes_Equipe' .
                        '                                                   AND     pronostics_buteurs.Pronostics_Indice = matches_buteurs.Matches_Indice' .
                        '                               JOIN        joueurs' .
                        '                                           ON      pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
                        '                               GROUP BY    pronostics_buteurs.Matches_Match, pronostics_buteurs.Equipes_Equipe' .
                        '                           ) AS Buteurs_Domicile' .
                        '                           ON      Buteurs_Domicile.Matches_Match = matches.Match' .
                        '                                   AND     Buteurs_Domicile.Equipes_Equipe = equipes_equipedomicile.Equipe' .
                        '       LEFT JOIN           (   SELECT      pronostics_buteurs.Matches_Match' .
                        '                                           ,pronostics_buteurs.Equipes_Equipe' .
                        '                                           ,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
                        '                               FROM        (' .
                        '                                               SELECT      Matches_Match' .
                        '                                                           ,Joueurs_Joueur' .
                        '                                                           ,Equipes_Equipe' .
                        '                                                           ,CASE' .
                        '                                                               WHEN    @match = Matches_Match' .
                        '                                                                       AND     @joueur = Joueurs_Joueur' .
                        '                                                                       AND     @equipe = Equipes_Equipe' .
                        '                                                               THEN    @indicePronostics := @indicePronostics + 1' .
                        '                                                               ELSE    (@indicePronostics := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
                        '                                                           END AS Pronostics_Indice' .
                        '                                               FROM        pronostics_buteurs_prec pronostics_buteurs' .
                        '                                               JOIN        matches_prec matches' .
                        '                                                           ON      pronostics_buteurs.Matches_Match = matches.Match' .
                        '                                               JOIN        (   SELECT      @indicePronostics := 0, @match := NULL, @joueur := NULL, @equipe := NULL    ) r' .
                        '                                               WHERE       pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurDetail .
                        '                                                           AND     matches.Match = ' . $match .
                        '                                                           AND     pronostics_buteurs.Saisons_Saison = ' . $saison .
                        '                                                           AND     matches.Saisons_Saison = ' . $saison .
                        '                                               ORDER BY    pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
                        '                                           ) pronostics_buteurs' .
                        '                               JOIN        (' .
                        '                                               SELECT      Matches_Match' .
                        '                                                           ,Joueurs_Joueur' .
                        '                                                           ,Equipes_Equipe' .
                        '                                                           ,CASE' .
                        '                                                               WHEN    @match = Matches_Match' .
                        '                                                                       AND     @joueur = Joueurs_Joueur' .
                        '                                                                       AND     @equipe = Equipes_Equipe' .
                        '                                                               THEN    @indiceMatches := @indiceMatches + 1' .
                        '                                                               ELSE    (@indiceMatches := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
                        '                                                           END AS Matches_Indice' .
                        '                                               FROM        matches_buteurs_prec matches_buteurs' .
                        '                                               JOIN        matches_prec matches' .
                        '                                                           ON      matches_buteurs.Matches_Match = matches.Match' .
                        '                                               JOIN        (   SELECT      @indiceMatches := 0, @joueur := NULL, @equipe := NULL   ) r' .
                        '                                               WHERE       matches.Match = ' . $match .
                        '                                                           AND     matches_buteurs.Saisons_Saison = ' . $saison .
                        '                                                           AND     matches.Saisons_Saison = ' . $saison .
                        '                                               ORDER BY    Matches_Match, Joueurs_Joueur, Equipes_Equipe' .
                        '                                           ) matches_buteurs' .
                        '                                           ON      pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
                        '                                                   AND     pronostics_buteurs.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
                        '                                                   AND     pronostics_buteurs.Equipes_Equipe = matches_buteurs.Equipes_Equipe' .
                        '                                                   AND     pronostics_buteurs.Pronostics_Indice = matches_buteurs.Matches_Indice' .
                        '                               JOIN        joueurs' .
                        '                                           ON      pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
                        '                               GROUP BY    pronostics_buteurs.Matches_Match, pronostics_buteurs.Equipes_Equipe' .
                        '                           ) AS Buteurs_Visiteur' .
                        '                           ON      Buteurs_Visiteur.Matches_Match = matches.Match' .
                        '                                   AND     Buteurs_Visiteur.Equipes_Equipe = equipes_equipevisiteur.Equipe' .
                        '       LEFT JOIN           (   SELECT      pronostics_buteurs.Matches_Match' .
                        '                                           ,pronostics_buteurs.Equipes_Equipe' .
                        '                                           ,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
                        '                               FROM        (' .
                        '                                               SELECT      pronostics_buteurs.Matches_Match' .
                        '                                                           ,pronostics_buteurs.Joueurs_Joueur' .
                        '                                                           ,pronostics_buteurs.Equipes_Equipe' .
                        '                                                           ,CASE' .
                        '                                                               WHEN    @match = pronostics_buteurs.Matches_Match' .
                        '                                                                       AND     @joueur = pronostics_buteurs.Joueurs_Joueur' .
                        '                                                                       AND     @equipe = pronostics_buteurs.Equipes_Equipe' .
                        '                                                               THEN    @indicePronostics := @indicePronostics + 1' .
                        '                                                               ELSE    (@indicePronostics := 1) AND (@match := pronostics_buteurs.Matches_Match) AND (@joueur := pronostics_buteurs.Joueurs_Joueur) AND (@equipe := pronostics_buteurs.Equipes_Equipe)' .
                        '                                                           END AS Pronostics_Indice' .
                        '                                               FROM        pronostics_buteurs_prec pronostics_buteurs' .
                        '                                               JOIN        matches_participants_prec matches_participants' .
                        '                                                           ON      pronostics_buteurs.Matches_Match = matches_participants.Matches_Match' .
                        '                                                                   AND     pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
                        '                                                                   AND     pronostics_buteurs.Equipes_Equipe = matches_participants.Equipes_Equipe' .
                        '                                               JOIN        matches_prec matches' .
                        '                                                           ON      pronostics_buteurs.Matches_Match = matches.Match' .
                        '                                               JOIN        (   SELECT      @indicePronostics := 0, @match := NULL, @joueur := NULL, @equipe := NULL    ) r' .
                        '                                               WHERE       pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurDetail .
                        '                                                           AND     matches.Match = ' . $match .
                        '                                                           AND     pronostics_buteurs.Saisons_Saison = ' . $saison .
                        '                                                           AND     matches_participants.Saisons_Saison = ' . $saison .
                        '                                                           AND     matches.Saisons_Saison = ' . $saison .
                        '                                               ORDER BY    pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
                        '                                           ) pronostics_buteurs' .
                        '                               LEFT JOIN   (' .
                        '                                               SELECT      Matches_Match' .
                        '                                                           ,Joueurs_Joueur' .
                        '                                                           ,Equipes_Equipe' .
                        '                                                           ,CASE' .
                        '                                                               WHEN    @match = Matches_Match' .
                        '                                                                       AND     @joueur = Joueurs_Joueur' .
                        '                                                                       AND     @equipe = Equipes_Equipe' .
                        '                                                               THEN    @indiceMatches := @indiceMatches + 1' .
                        '                                                               ELSE    (@indiceMatches := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
                        '                                                           END AS Matches_Indice' .
                        '                                               FROM        matches_buteurs_prec matches_buteurs' .
                        '                                               JOIN        matches_prec matches' .
                        '                                                           ON      matches_buteurs.Matches_Match = matches.Match' .
                        '                                               JOIN        (   SELECT      @indiceMatches := 0, @match := NULL, @joueur :=  NULL, @equipe := NULL  ) r' .
                        '                                               WHERE       matches.Match = ' . $match .
                        '                                                           AND     matches_buteurs.Saisons_Saison = ' . $saison .
                        '                                                           AND     matches.Saisons_Saison = ' . $saison .
                        '                                               ORDER BY    Matches_Match, Joueurs_Joueur, Equipes_Equipe' .
                        '                                           ) matches_buteurs' .
                        '                                           ON      pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
                        '                                                   AND     pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
                        '                                                   AND     pronostics_buteurs.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
                        '                                                   AND     pronostics_buteurs.Pronostics_Indice = matches_buteurs.Matches_Indice' .
                        '                               JOIN        joueurs' .
                        '                                           ON      pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
                        '                               WHERE       matches_buteurs.Joueurs_Joueur IS NULL' .
                        '                               GROUP BY    pronostics_buteurs.Matches_Match, pronostics_buteurs.Equipes_Equipe' .
                        '                           ) AS ButeursInvalides_Domicile' .
                        '                           ON      ButeursInvalides_Domicile.Matches_Match = matches.Match' .
                        '                                   AND     ButeursInvalides_Domicile.Equipes_Equipe = equipes_equipedomicile.Equipe' .
                        '       LEFT JOIN           (   SELECT      pronostics_buteurs.Matches_Match' .
                        '                                           ,pronostics_buteurs.Equipes_Equipe' .
                        '                                           ,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
                        '                               FROM        (' .
                        '                                               SELECT      pronostics_buteurs.Matches_Match' .
                        '                                                           ,pronostics_buteurs.Joueurs_Joueur' .
                        '                                                           ,pronostics_buteurs.Equipes_Equipe' .
                        '                                                           ,CASE' .
                        '                                                               WHEN    @match = pronostics_buteurs.Matches_Match' .
                        '                                                                       AND     @joueur = pronostics_buteurs.Joueurs_Joueur' .
                        '                                                                       AND     @equipe = pronostics_buteurs.Equipes_Equipe' .
                        '                                                               THEN    @indicePronostics := @indicePronostics + 1' .
                        '                                                               ELSE    (@indicePronostics := 1) AND (@match := pronostics_buteurs.Matches_Match) AND (@joueur := pronostics_buteurs.Joueurs_Joueur) AND (@equipe := pronostics_buteurs.Equipes_Equipe)' .
                        '                                                           END AS Pronostics_Indice' .
                        '                                               FROM        pronostics_buteurs_prec pronostics_buteurs' .
                        '                                               JOIN        matches_participants_prec matches_participants' .
                        '                                                           ON      pronostics_buteurs.Matches_Match = matches_participants.Matches_Match' .
                        '                                                                   AND     pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
                        '                                                                   AND     pronostics_buteurs.Equipes_Equipe = matches_participants.Equipes_Equipe' .
                        '                                               JOIN        matches_prec matches' .
                        '                                                           ON      pronostics_buteurs.Matches_Match = matches.Match' .
                        '                                               JOIN        (   SELECT      @indicePronostics := 0, @match := NULL, @joueur := NULL, @equipe := NULL    ) r' .
                        '                                               WHERE       pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurDetail .
                        '                                                           AND     matches.Match = ' . $match .
                        '                                                           AND     pronostics_buteurs.Saisons_Saison = ' . $saison .
                        '                                                           AND     matches_participants.Saisons_Saison = ' . $saison .
                        '                                                           AND     matches.Saisons_Saison = ' . $saison .
                        '                                               ORDER BY    pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
                        '                                           ) pronostics_buteurs' .
                        '                               LEFT JOIN   (' .
                        '                                               SELECT      Matches_Match' .
                        '                                                           ,Joueurs_Joueur' .
                        '                                                           ,Equipes_Equipe' .
                        '                                                           ,CASE' .
                        '                                                               WHEN    @match = Matches_Match' .
                        '                                                                       AND     @joueur = Joueurs_Joueur' .
                        '                                                                       AND     @equipe = Equipes_Equipe' .
                        '                                                               THEN    @indiceMatches := @indiceMatches + 1' .
                        '                                                               ELSE    (@indiceMatches := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
                        '                                                           END AS Matches_Indice' .
                        '                                               FROM        matches_buteurs_prec matches_buteurs' .
                        '                                               JOIN        matches_prec matches' .
                        '                                                           ON      matches_buteurs.Matches_Match = matches.Match' .
                        '                                               JOIN        (   SELECT      @indiceMatches := 0, @match := NULL, @joueur :=  NULL, @equipe := NULL  ) r' .
                        '                                               WHERE       matches.Match = ' . $match .
                        '                                                           AND     matches_buteurs.Saisons_Saison = ' . $saison .
                        '                                                           AND     matches.Saisons_Saison = ' . $saison .
                        '                                               ORDER BY    Matches_Match, Joueurs_Joueur, Equipes_Equipe' .
                        '                                           ) matches_buteurs' .
                        '                                           ON      pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
                        '                                                   AND     pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
                        '                                                   AND     pronostics_buteurs.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
                        '                                                   AND     pronostics_buteurs.Pronostics_Indice = matches_buteurs.Matches_Indice' .
                        '                               JOIN        joueurs' .
                        '                                           ON      pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
                        '                               WHERE       matches_buteurs.Joueurs_Joueur IS NULL' .
                        '                               GROUP BY    pronostics_buteurs.Matches_Match, pronostics_buteurs.Equipes_Equipe' .
                        '                           ) AS ButeursInvalides_Visiteur' .
                        '                           ON      ButeursInvalides_Visiteur.Matches_Match = matches.Match' .
                        '                                   AND     ButeursInvalides_Visiteur.Equipes_Equipe = equipes_equipevisiteur.Equipe' .
                        '       LEFT JOIN           (' .
                        '                               SELECT      pronostics_buteurs.Matches_Match' .
                        '                                           ,Equipes_Equipe' .
                        '                                           ,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
                        '                               FROM        (' .
                        '                                               SELECT      pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
                        '                                               FROM        pronostics_buteurs_prec pronostics_buteurs' .
                        '                                               JOIN        matches_prec matches' .
                        '                                                           ON      pronostics_buteurs.Matches_Match = matches.Match' .
                        '                                               LEFT JOIN   matches_participants_prec matches_participants' .
                        '                                                           ON      pronostics_buteurs.Matches_Match = matches_participants.Matches_Match' .
                        '                                                                   AND     pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
                        '                                                                   AND     pronostics_buteurs.Equipes_Equipe = matches_participants.Equipes_Equipe' .
                        '                                               WHERE       pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurDetail .
                        '                                                           AND     matches.Match = ' . $match .
                        '                                                           AND     matches_participants.Joueurs_Joueur IS NULL' .
                        '                                                           AND     pronostics_buteurs.Saisons_Saison = ' . $saison .
                        '                                                           AND     matches.Saisons_Saison = ' . $saison .
                        '                                                           AND     matches_participants.Saisons_Saison = ' . $saison .
                        '                                           ) pronostics_buteurs' .
                        '                               JOIN        joueurs' .
                        '                                           ON      pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
                        '                               GROUP BY    pronostics_buteurs.Matches_Match, pronostics_buteurs.Equipes_Equipe' .
                        '                           ) AS ButeursAbsents_Domicile' .
                        '                           ON      ButeursAbsents_Domicile.Matches_Match = matches.Match' .
                        '                                   AND     ButeursAbsents_Domicile.Equipes_Equipe = equipes_equipedomicile.Equipe' .
                        '       LEFT JOIN           (' .
                        '                               SELECT      pronostics_buteurs.Matches_Match' .
                        '                                           ,Equipes_Equipe' .
                        '                                           ,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
                        '                               FROM        (' .
                        '                                               SELECT      pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
                        '                                               FROM        pronostics_buteurs_prec pronostics_buteurs' .
                        '                                               JOIN        matches_prec matches' .
                        '                                                           ON      pronostics_buteurs.Matches_Match = matches.Match' .
                        '                                               LEFT JOIN   matches_participants_prec matches_participants' .
                        '                                                           ON      pronostics_buteurs.Matches_Match = matches_participants.Matches_Match' .
                        '                                                                   AND     pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
                        '                                                                   AND     pronostics_buteurs.Equipes_Equipe = matches_participants.Equipes_Equipe' .
                        '                                               WHERE       pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurDetail .
                        '                                                           AND     matches.Match = ' . $match .
                        '                                                           AND     matches_participants.Joueurs_Joueur IS NULL' .
                        '                                                           AND     pronostics_buteurs.Saisons_Saison = ' . $saison .
                        '                                                           AND     matches.Saisons_Saison = ' . $saison .
                        '                                                           AND     matches_participants.Saisons_Saison = ' . $saison .
                        '                                           ) pronostics_buteurs' .
                        '                               JOIN        joueurs' .
                        '                                           ON      pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
                        '                               GROUP BY    pronostics_buteurs.Matches_Match, pronostics_buteurs.Equipes_Equipe' .
                        '                           ) AS ButeursAbsents_Visiteur' .
                        '                           ON      ButeursAbsents_Visiteur.Matches_Match = matches.Match' .
                        '                                   AND     ButeursAbsents_Visiteur.Equipes_Equipe = equipes_equipevisiteur.Equipe' .
                        '       WHERE               pronostiqueurs.Pronostiqueur = ' . $pronostiqueurDetail .
                        '                           AND     matches.Match = ' . $match .
                        '                           AND     matches.Saisons_Saison = ' . $saison .
                        '                           AND     journees.Saisons_Saison = ' . $saison .
                        '                           AND     pronostics.Saisons_Saison = ' . $saison .
                        '                           AND     scores.Saisons_Saison = ' . $saison .
                        '       ORDER BY            matches.Match';
        $req = $bdd->query($ordreSQL);
        $pronostics = $req->fetchAll();
    }
    
    
    // Lecture des pronostiqueurs de la confrontation
    $ordreSQL =     '   SELECT      Pronostiqueurs_PronostiqueurA, Pronostiqueurs_PronostiqueurB' .
                    '               ,IFNULL(pronostiqueursA.Pronostiqueurs_NomUtilisateur, \'-\') AS PronostiqueursA_NomUtilisateur' .
                    '               ,IFNULL(pronostiqueursB.Pronostiqueurs_NomUtilisateur, \'-\') AS PronostiqueursB_NomUtilisateur' .
                    '               ,Journees_Journee' .
                    '   FROM        confrontations_prec confrontations' .
                    '   LEFT JOIN   (' .
                    '                   SELECT      Pronostiqueur, Pronostiqueurs_NomUtilisateur' .
                    '                   FROM        pronostiqueurs' .
                    '                   UNION ALL' .
                    '                   SELECT      Pronostiqueur, Pronostiqueurs_NomUtilisateur' .
                    '                   FROM        pronostiqueurs_anciens' .
                    '               ) pronostiqueursA' .
                    '               ON      confrontations.Pronostiqueurs_PronostiqueurA = pronostiqueursA.Pronostiqueur' .
                    '   LEFT JOIN   (' .
                    '                   SELECT      Pronostiqueur, Pronostiqueurs_NomUtilisateur' .
                    '                   FROM        pronostiqueurs' .
                    '                   UNION ALL' .
                    '                   SELECT      Pronostiqueur, Pronostiqueurs_NomUtilisateur' .
                    '                   FROM        pronostiqueurs_anciens' .
                    '               ) pronostiqueursB' .
                    '               ON      confrontations.Pronostiqueurs_PronostiqueurB = pronostiqueursB.Pronostiqueur' .
                    '   WHERE       Confrontation = ' . $confrontation .
                    '               AND     confrontations.Saisons_Saison = ' . $saison;
    $req = $bdd->query($ordreSQL);
    $pronostiqueurs = $req->fetchAll();
    
    $pronosticsA = $pronosticsB = null;

    // Affichage des bons résultats et des pronostics pour chaque pronostiqueur de la confrontation
    echo '<table class="tableau--resultat" id="tablePronostics">';
        echo '<thead>';
            echo '<tr>';
                echo '<th colspan="2" style="border-right: 1px solid #fff;"></th>';
                echo '<th colspan="4" style="border-right: 1px solid #fff;">' . $pronostiqueurs[0]["PronostiqueursA_NomUtilisateur"] . '</th>';
                echo '<th colspan="4">' . $pronostiqueurs[0]["PronostiqueursB_NomUtilisateur"] . '</th>';
            echo '</tr>';
            echo '<tr>';
                echo '<th>Matches</th>';
                echo '<th style="border-right: 1px solid #fff;">Score final et buteurs</th>';
                echo '<th>Pronostics</th>';
                echo '<th>Ont marqué</th>';
                echo '<th>N\'ont pas marqué</th>';
                echo '<th style="border-right: 1px solid #fff;">N\'ont pas joué</th>';
                echo '<th>Pronostics</th>';
                echo '<th>Ont marqué</th>';
                echo '<th>N\'ont pas marqué</th>';
                echo '<th>N\'ont pas joué</th>';
            echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
            for($i = 0; $i < $nombreMatches; $i++) {
                echo '<tr>';
                    $equipeDomicileNomCourt = $resultats[$i]["EquipesDomicile_NomCourt"];
                    $equipeVisiteurNomCourt = $resultats[$i]["EquipesVisiteur_NomCourt"];
                    $equipeDomicileNom = $resultats[$i]["EquipesDomicile_Nom"];
                    $equipeVisiteurNom = $resultats[$i]["EquipesVisiteur_Nom"];
                    $scoreEquipeDomicile = $resultats[$i]["Matches_ScoreEquipeDomicile"];
                    $scoreAPEquipeDomicile = $resultats[$i]["Matches_ScoreAPEquipeDomicile"];
                    $scoreEquipeVisiteur = $resultats[$i]["Matches_ScoreEquipeVisiteur"];
                    $scoreAPEquipeVisiteur = $resultats[$i]["Matches_ScoreAPEquipeVisiteur"];
                    $vainqueur = $resultats[$i]["Matches_Vainqueur"];
                    $equipeDomicileButeurs = $resultats[$i]["EquipesDomicile_Buteurs"];
                    $equipeVisiteurButeurs = $resultats[$i]["EquipesVisiteur_Buteurs"];
                    $pointsQualificationEquipeDomicile = $resultats[$i]["Matches_PointsQualificationEquipeDomicile"];
                    $pointsQualificationEquipeVisiteur = $resultats[$i]["Matches_PointsQualificationEquipeVisiteur"];
                    $coefficient = $resultats[$i]["Matches_Coefficient"];

                    echo '<td title="' . $equipeDomicileNom . ' - ' . $equipeVisiteurNom . '">';
                        echo '<label>' . $equipeDomicileNomCourt . ' - ' . $equipeVisiteurNomCourt . '</label><br />';
                        echo '<label>Points qualification : ' . $pointsQualificationEquipeDomicile . '-' . $pointsQualificationEquipeVisiteur . '</label>';
                    echo '</td>';
                    echo '<td style="border-right: 1px solid #fff;">';
                        $scoreAffiche = formaterScoreMatch($scoreEquipeDomicile, $scoreAPEquipeDomicile, $scoreEquipeVisiteur, $scoreAPEquipeVisiteur, $vainqueur);
                        echo $scoreAffiche . '<br />' . $equipeDomicileButeurs . '<br />' . $equipeVisiteurButeurs;
                    echo '</td>';

                    if($pronostiqueurs[0]["Pronostiqueurs_PronostiqueurA"] != null)
                        lirePronostics($bdd, $pronostiqueur, $pronostiqueurs[0]["Pronostiqueurs_PronostiqueurA"], $saison, $resultats[$i]["Match"], $pronosticsA);

                    if($pronostiqueurs[0]["Pronostiqueurs_PronostiqueurB"] != null)
                        lirePronostics($bdd, $pronostiqueur, $pronostiqueurs[0]["Pronostiqueurs_PronostiqueurB"], $saison, $resultats[$i]["Match"], $pronosticsB);

                    // Pronostiqueur A
                    if($pronosticsA != null) {
                        $pronosticScoreEquipeDomicile = $pronosticsA[0]["Pronostics_ScoreEquipeDomicile"];
                        $pronosticScoreAPEquipeDomicile = $pronosticsA[0]["Pronostics_ScoreAPEquipeDomicile"];
                        $pronosticScoreEquipeVisiteur = $pronosticsA[0]["Pronostics_ScoreEquipeVisiteur"];
                        $pronosticScoreAPEquipeVisiteur = $pronosticsA[0]["Pronostics_ScoreAPEquipeVisiteur"];
                        $pronosticVainqueur = $pronosticsA[0]["Pronostics_Vainqueur"];
                        $buteursDomicile = $pronosticsA[0]["EquipesDomicile_Buteurs"];
                        $buteursVisiteur = $pronosticsA[0]["EquipesVisiteur_Buteurs"];
                        $buteursInvalidesDomicile = $pronosticsA[0]["EquipesDomicile_ButeursInvalides"];
                        $buteursInvalidesVisiteur = $pronosticsA[0]["EquipesVisiteur_ButeursInvalides"];
                        $buteursAbsentsDomicile = $pronosticsA[0]["EquipesDomicile_ButeursAbsents"];
                        $buteursAbsentsVisiteur = $pronosticsA[0]["EquipesVisiteur_ButeursAbsents"];
                        $scoreMatch = $pronosticsA[0]["Scores_ScoreMatch"];
                        $scoreButeur = $pronosticsA[0]["Scores_ScoreButeur"];
                        $scoreBonus = $pronosticsA[0]["Scores_ScoreBonus"];
                        $performance = $pronosticsA[0]["Performance"];
                    }
                    else {
                        $pronosticScoreEquipeDomicile = '?';
                        $pronosticScoreAPEquipeDomicile = '?';
                        $pronosticScoreEquipeVisiteur = '?';
                        $pronosticScoreAPEquipeVisiteur = '?';
                        $pronosticVainqueur = '?';
                        $buteursDomicile = '?';
                        $buteursVisiteur = '?';
                        $buteursInvalidesDomicile = '?';
                        $buteursInvalidesVisiteur = '?';
                        $buteursAbsentsDomicile = '?';
                        $buteursAbsentsVisiteur = '?';
                        $scoreMatch = '?';
                        $scoreButeur = '?';
                        $scoreBonus = '?';
                        $performance = -1;
                    }
                    
                    if($performance == -1)
                        $style = 'blanc';
                    else if($performance == 0)
                        $style = 'orange';
                    else
                        $style = 'vert';
                    echo '<td class="' . $style . '">';
                        echo '<div>' . $scoreMatch . ' | ' . $scoreButeur . ' | ' . $scoreBonus . '</div>';
                        $scoreAffiche = formaterScoreMatch($pronosticScoreEquipeDomicile, $pronosticScoreAPEquipeDomicile, $pronosticScoreEquipeVisiteur, $pronosticScoreAPEquipeVisiteur, $pronosticVainqueur);
                        echo '<div>' . $scoreAffiche . '</div>';
                    echo '</td>';
                    echo '<td>';
                        echo '<label>' . $buteursDomicile . '<br />' . $buteursVisiteur . '</label>';
                    echo '</td>';
                    echo '<td>';
                        echo '<label>' . $buteursInvalidesDomicile . '<br />' . $buteursInvalidesVisiteur . '</label>';
                    echo '</td>';
                    echo '<td style="border-right: 1px solid #fff;">';
                        echo '<label>' . $buteursAbsentsDomicile . '<br />' . $buteursAbsentsVisiteur . '</label>';
                    echo '</td>';

                    // Pronostiqueur B
                    if($pronosticsB != null) {
                        $pronosticScoreEquipeDomicile = $pronosticsB[0]["Pronostics_ScoreEquipeDomicile"];
                        $pronosticScoreAPEquipeDomicile = $pronosticsB[0]["Pronostics_ScoreAPEquipeDomicile"];
                        $pronosticScoreEquipeVisiteur = $pronosticsB[0]["Pronostics_ScoreEquipeVisiteur"];
                        $pronosticScoreAPEquipeVisiteur = $pronosticsB[0]["Pronostics_ScoreAPEquipeVisiteur"];
                        $pronosticVainqueur = $pronosticsB[0]["Pronostics_Vainqueur"];
                        $buteursDomicile = $pronosticsB[0]["EquipesDomicile_Buteurs"];
                        $buteursVisiteur = $pronosticsB[0]["EquipesVisiteur_Buteurs"];
                        $buteursInvalidesDomicile = $pronosticsB[0]["EquipesDomicile_ButeursInvalides"];
                        $buteursInvalidesVisiteur = $pronosticsB[0]["EquipesVisiteur_ButeursInvalides"];
                        $buteursAbsentsDomicile = $pronosticsB[0]["EquipesDomicile_ButeursAbsents"];
                        $buteursAbsentsVisiteur = $pronosticsB[0]["EquipesVisiteur_ButeursAbsents"];
                        $scoreMatch = $pronosticsB[0]["Scores_ScoreMatch"];
                        $scoreButeur = $pronosticsB[0]["Scores_ScoreButeur"];
                        $scoreBonus = $pronosticsB[0]["Scores_ScoreBonus"];
                        $performance = $pronosticsB[0]["Performance"];
                    }
                    else {
                        $pronosticScoreEquipeDomicile = '?';
                        $pronosticScoreAPEquipeDomicile = '?';
                        $pronosticScoreEquipeVisiteur = '?';
                        $pronosticScoreAPEquipeVisiteur = '?';
                        $pronosticVainqueur = '?';
                        $buteursDomicile = '?';
                        $buteursVisiteur = '?';
                        $buteursInvalidesDomicile = '?';
                        $buteursInvalidesVisiteur = '?';
                        $buteursAbsentsDomicile = '?';
                        $buteursAbsentsVisiteur = '?';
                        $scoreMatch = '?';
                        $scoreButeur = '?';
                        $scoreBonus = '?';
                        $performance = -1;
                    }
                    
                    if($performance == -1)
                        $style = 'blanc';
                    else if($performance == 0)
                        $style = 'orange';
                    else
                        $style = 'vert';
                    echo '<td class="' . $style . '">';
                        echo '<div>' . $scoreMatch . ' | ' . $scoreButeur . ' | ' . $scoreBonus . '</div>';
                        $scoreAffiche = formaterScoreMatch($pronosticScoreEquipeDomicile, $pronosticScoreAPEquipeDomicile, $pronosticScoreEquipeVisiteur, $pronosticScoreAPEquipeVisiteur, $pronosticVainqueur);
                        echo '<div>' . $scoreAffiche . '</div>';
                    echo '</td>';
                    echo '<td>';
                        echo '<label>' . $buteursDomicile . '<br />' . $buteursVisiteur . '</label>';
                    echo '</td>';
                    echo '<td>';
                        echo '<label>' . $buteursInvalidesDomicile . '<br />' . $buteursInvalidesVisiteur . '</label>';
                    echo '</td>';
                    echo '<td>';
                        echo '<label>' . $buteursAbsentsDomicile . '<br />' . $buteursAbsentsVisiteur . '</label>';
                    echo '</td>';
                echo '</tr>';
            }
        echo '</tbody>';
    echo '</table>';
?>
