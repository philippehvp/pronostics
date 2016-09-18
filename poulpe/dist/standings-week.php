<?php
    include_once('common.php');

    $postedData = json_decode(file_get_contents("php://input"), true);
    $saison = json_decode($postedData['saison']);
    $journee = json_decode($postedData['journee']);
    $dateReference = $postedData['date-reference'];

    $sql =      '   SELECT      pronostiqueurs.Pronostiqueur' .
                '               ,pronostiqueurs.Pronostiqueurs_NomUtilisateur' .
                '               ,pronostiqueurs.Pronostiqueurs_Photo' .
                '               ,classements_prec.Classements_ClassementJourneeMatch' .
                '               ,classements_prec.Classements_PointsJourneeMatch' .
                '               ,classements_prec.Classements_PointsJourneeButeur' .
                '   FROM        classements_prec' .
                '   JOIN        (' .
                '                   SELECT      Pronostiqueur, Pronostiqueurs_NomUtilisateur, Pronostiqueurs_Photo' .
                '                   FROM        pronostiqueurs' .
                '                   UNION ALL' .
                '                   SELECT      Pronostiqueur, Pronostiqueurs_NomUtilisateur, NULL AS Pronostiqueurs_Photo' .
                '                   FROM        pronostiqueurs_anciens' .
                '               ) pronostiqueurs' .
                '               ON      classements_prec.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
                '   WHERE       classements_prec.Saisons_Saison = ' . $saison .
                '               AND     classements_prec.Journees_Journee = ' . $journee .
                '               AND     classements_prec.Classements_DateReference = ' . $db->quote($dateReference) .
                '               AND     classements_prec.Classements_ClassementJourneeMatch IS NOT NULL' .
                '   ORDER BY    classements_prec.Classements_ClassementJourneeMatch';

    $query = $db->query($sql);
    $standingsWeek = $query->fetchAll();

    echo json_encode($standingsWeek);

?>