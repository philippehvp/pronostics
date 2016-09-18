<?php
	include('commun_administrateur.php');

	// Remplissage automatique des cotes des joueurs
    // Les cotes existantes ne sont pas effacées

	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
    
    // Lecture des informations du match (date, équipes concernées)
    $ordreSQL =     '   SELECT      Matches_Date' .
                    '   FROM        matches' .
                    '   WHERE       matches.Match = ' . $match .
                    '   LIMIT       1';
    $req = $bdd->query($ordreSQL);
    $matches = $req->fetchAll();
    if(count($matches) == 0)
        return;
    
    $dateMatch = $matches[0]["Matches_Date"];
    
    // Ajout des cotes buteurs non existantes pour les deux équipes
    $ordreSQL =     '   INSERT INTO joueurs_cotes(Joueurs_Joueur, Equipes_Equipe, Matches_Match, JoueursCotes_Cote)' .
                    '   SELECT      joueurs_equipes.Joueurs_Joueur, joueurs_equipes.Equipes_Equipe, ' . $match . ' AS Matches_Match' .
                    '               ,cotes_buteurs_automatiques.CotesButeursAutomatiques_CoteAutomatique' .
                    '   FROM        matches' .
                    '   JOIN        joueurs_equipes' .
                    '               ON      matches.Equipes_EquipeDomicile = joueurs_equipes.Equipes_Equipe' .
                    '   JOIN        joueurs' .
                    '               ON      joueurs_equipes.Joueurs_Joueur = joueurs.Joueur' .
                    '   LEFT JOIN   joueurs_cotes' .
                    '               ON      joueurs_equipes.Joueurs_Joueur = joueurs_cotes.Joueurs_Joueur' .
                    '                       AND     joueurs_equipes.Equipes_Equipe = joueurs_cotes.Equipes_Equipe' .
                    '                       AND     matches.Match = joueurs_cotes.Matches_Match' .
                    '   JOIN        cotes_buteurs_automatiques' .
                    '               ON      cotes_buteurs_automatiques.CotesButeursAutomatiques_CoteEquipe =' .
                    '                           CASE' .
                    '                               WHEN    fn_calculcotematch(matches.Matches_CoteEquipeDomicile) < 5' .
                    '                               THEN    0' .
                    '                               WHEN    fn_calculcotematch(matches.Matches_CoteEquipeDomicile) < 10' .
                    '                               THEN    5' .
                    '                               WHEN    fn_calculcotematch(matches.Matches_CoteEquipeDomicile) < 15' .
                    '                               THEN    10' .
                    '                               ELSE    15' .
                    '                           END' .
                    '                       AND     joueurs.Postes_Poste = cotes_buteurs_automatiques.CotesButeursAutomatiques_Poste' .
                    '   WHERE       matches.Match = ' . $match .
                    '               AND     joueurs.Postes_Poste <> 1' .
                    '               AND     joueurs_equipes.JoueursEquipes_Debut <= matches.Matches_Date' .
                    '               AND     (' .
                    '                           joueurs_equipes.JoueursEquipes_Fin IS NULL' .
                    '                           OR      joueurs_equipes.JoueursEquipes_Fin > matches.Matches_Date' .
                    '                       )' .
                    '               AND     joueurs_cotes.Joueurs_Joueur IS NULL' .
                    '   UNION ALL' .
                    '   SELECT      joueurs_equipes.Joueurs_Joueur, joueurs_equipes.Equipes_Equipe, ' . $match . ' AS Matches_Match' .
                    '               ,cotes_buteurs_automatiques.CotesButeursAutomatiques_CoteAutomatique' .
                    '   FROM        matches' .
                    '   JOIN        joueurs_equipes' .
                    '               ON      matches.Equipes_EquipeVisiteur = joueurs_equipes.Equipes_Equipe' .
                    '   JOIN        joueurs' .
                    '               ON      joueurs_equipes.Joueurs_Joueur = joueurs.Joueur' .
                    '   LEFT JOIN   joueurs_cotes' .
                    '               ON      joueurs_equipes.Joueurs_Joueur = joueurs_cotes.Joueurs_Joueur' .
                    '                       AND     joueurs_equipes.Equipes_Equipe = joueurs_cotes.Equipes_Equipe' .
                    '                       AND     matches.Match = joueurs_cotes.Matches_Match' .
                    '   JOIN        cotes_buteurs_automatiques' .
                    '               ON      cotes_buteurs_automatiques.CotesButeursAutomatiques_CoteEquipe =' .
                    '                           CASE' .
                    '                               WHEN    fn_calculcotematch(matches.Matches_CoteEquipeVisiteur) < 5' .
                    '                               THEN    0' .
                    '                               WHEN    fn_calculcotematch(matches.Matches_CoteEquipeVisiteur) < 10' .
                    '                               THEN    5' .
                    '                               WHEN    fn_calculcotematch(matches.Matches_CoteEquipeVisiteur) < 15' .
                    '                               THEN    10' .
                    '                               ELSE    15' .
                    '                           END' .
                    '                       AND     joueurs.Postes_Poste = cotes_buteurs_automatiques.CotesButeursAutomatiques_Poste' .
                    '   WHERE       matches.Match = ' . $match .
                    '               AND     joueurs.Postes_Poste <> 1' .
                    '               AND     joueurs_equipes.JoueursEquipes_Debut <= matches.Matches_Date' .
                    '               AND     (' .
                    '                           joueurs_equipes.JoueursEquipes_Fin IS NULL' .
                    '                           OR      joueurs_equipes.JoueursEquipes_Fin > matches.Matches_Date' .
                    '                       )' .
                    '               AND     joueurs_cotes.Joueurs_Joueur IS NULL';
    $bdd->exec($ordreSQL);
    
    
    
 
?>