<?php
    include_once('common.php');

    $postedData = json_decode(file_get_contents("php://input"), true);
    $saison = json_decode($postedData['saison']);
    $championnat = json_decode($postedData['championnat']);

    $sql =      '       SELECT      Statut, Pronostiqueurs_NomUtilisateur, Poulpe_Or, Poulpe_Argent, Poulpe_Bronze, Soulier_Or, Choupo, Jeremy_Morel, Record_Points, Record_Buteur' .
                '       FROM        (' .
                '                       SELECT      0 AS Statut, pronostiqueurs.Pronostiqueurs_NomUtilisateur' .
                '                                   ,(' .
                '                                       SELECT      GROUP_CONCAT(journees.Journees_NomCourt SEPARATOR \', \')' .
                '                                       FROM        trophees_prec' .
                '                                       JOIN        journees' .
                '                                                   ON      trophees_prec.Journees_Journee = journees.Journee' .
                '                                       WHERE       trophees_prec.Saisons_Saison = ' . $saison .
                '                                                   AND     journees.Championnats_Championnat = ' . $championnat .
                '                                                   AND     trophees_prec.Trophees_CodeTrophee = 1' .
                '                                                   AND     trophees_prec.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
                '                                       GROUP BY    trophees_prec.Pronostiqueurs_Pronostiqueur' .
                '                                   ) AS Poulpe_Or' .
                '                                   ,(' .
                '                                       SELECT      GROUP_CONCAT(journees.Journees_NomCourt SEPARATOR \', \')' .
                '                                       FROM        trophees_prec' .
                '                                       JOIN        journees' .
                '                                                   ON      trophees_prec.Journees_Journee = journees.Journee' .
                '                                       WHERE       trophees_prec.Saisons_Saison = ' . $saison .
                '                                                   AND     journees.Championnats_Championnat = ' . $championnat .
                '                                                   AND     trophees_prec.Trophees_CodeTrophee = 2' .
                '                                                   AND     trophees_prec.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
                '                                       GROUP BY    trophees_prec.Pronostiqueurs_Pronostiqueur' .
                '                                   ) AS Poulpe_Argent' .
                '                                   ,(' .
                '                                       SELECT      GROUP_CONCAT(journees.Journees_NomCourt SEPARATOR \', \')' .
                '                                       FROM        trophees_prec' .
                '                                       JOIN        journees' .
                '                                                   ON      trophees_prec.Journees_Journee = journees.Journee' .
                '                                       WHERE       trophees_prec.Saisons_Saison = ' . $saison .
                '                                                   AND     journees.Championnats_Championnat = ' . $championnat .
                '                                                   AND     trophees_prec.Trophees_CodeTrophee = 3' .
                '                                                   AND     trophees_prec.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
                '                                       GROUP BY    trophees_prec.Pronostiqueurs_Pronostiqueur' .
                '                                   ) AS Poulpe_Bronze' .
                '                                   ,(' .
                '                                       SELECT      GROUP_CONCAT(journees.Journees_NomCourt SEPARATOR \', \')' .
                '                                       FROM        trophees_prec' .
                '                                       JOIN        journees' .
                '                                                   ON      trophees_prec.Journees_Journee = journees.Journee' .
                '                                       WHERE       trophees_prec.Saisons_Saison = ' . $saison .
                '                                                   AND     journees.Championnats_Championnat = ' . $championnat .
                '                                                   AND     trophees_prec.Trophees_CodeTrophee = 4' .
                '                                                   AND     trophees_prec.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
                '                                       GROUP BY    trophees_prec.Pronostiqueurs_Pronostiqueur' .
                '                                   ) AS Soulier_Or' .
                '                                   ,(' .
                '                                       SELECT      GROUP_CONCAT(journees.Journees_NomCourt SEPARATOR \', \')' .
                '                                       FROM        trophees_prec' .
                '                                       JOIN        journees' .
                '                                                   ON      trophees_prec.Journees_Journee = journees.Journee' .
                '                                       WHERE       trophees_prec.Saisons_Saison = ' . $saison .
                '                                                   AND     journees.Championnats_Championnat = ' . $championnat .
                '                                                   AND     trophees_prec.Trophees_CodeTrophee = 5' .
                '                                                   AND     trophees_prec.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
                '                                       GROUP BY    trophees_prec.Pronostiqueurs_Pronostiqueur' .
                '                                   ) AS Choupo' .
                '                                   ,(' .
                '                                       SELECT      GROUP_CONCAT(journees.Journees_NomCourt SEPARATOR \', \')' .
                '                                       FROM        trophees_prec' .
                '                                       JOIN        journees' .
                '                                                   ON      trophees_prec.Journees_Journee = journees.Journee' .
                '                                       WHERE       trophees_prec.Saisons_Saison = ' . $saison .
                '                                                   AND     journees.Championnats_Championnat = ' . $championnat .
                '                                                   AND     trophees_prec.Trophees_CodeTrophee = 6' .
                '                                                   AND     trophees_prec.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
                '                                       GROUP BY    trophees_prec.Pronostiqueurs_Pronostiqueur' .
                '                                   ) AS Jeremy_Morel' .
                '                                   ,(' .
                '                                       SELECT      GROUP_CONCAT(journees.Journees_NomCourt SEPARATOR \', \')' .
                '                                       FROM        trophees_prec' .
                '                                       JOIN        journees' .
                '                                                   ON      trophees_prec.Journees_Journee = journees.Journee' .
                '                                       WHERE       trophees_prec.Saisons_Saison = ' . $saison .
                '                                                   AND     journees.Championnats_Championnat = ' . $championnat .
                '                                                   AND     trophees_prec.Trophees_CodeTrophee = 7' .
                '                                                   AND     trophees_prec.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
                '                                       GROUP BY    trophees_prec.Pronostiqueurs_Pronostiqueur' .
                '                                   ) AS Record_Points' .
                '                                   ,(' .
                '                                       SELECT      GROUP_CONCAT(journees.Journees_NomCourt SEPARATOR \', \')' .
                '                                       FROM        trophees_prec' .
                '                                       JOIN        journees' .
                '                                                   ON      trophees_prec.Journees_Journee = journees.Journee' .
                '                                       WHERE       trophees_prec.Saisons_Saison = ' . $saison .
                '                                                   AND     journees.Championnats_Championnat = ' . $championnat .
                '                                                   AND     trophees_prec.Trophees_CodeTrophee = 8' .
                '                                                   AND     trophees_prec.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
                '                                       GROUP BY    trophees_prec.Pronostiqueurs_Pronostiqueur' .
                '                                   ) AS Record_Buteur' .
                '                       FROM        pronostiqueurs' .
                '                       UNION' .
                '                       SELECT      1 AS Statut, pronostiqueurs_anciens.Pronostiqueurs_NomUtilisateur' .
                '                                   ,(' .
                '                                       SELECT      GROUP_CONCAT(journees.Journees_NomCourt SEPARATOR \', \')' .
                '                                       FROM        trophees_prec' .
                '                                       JOIN        journees' .
                '                                                   ON      trophees_prec.Journees_Journee = journees.Journee' .
                '                                       WHERE       trophees_prec.Saisons_Saison = ' . $saison .
                '                                                   AND     journees.Championnats_Championnat = ' . $championnat .
                '                                                   AND     trophees_prec.Trophees_CodeTrophee = 1' .
                '                                                   AND     trophees_prec.Pronostiqueurs_Pronostiqueur = pronostiqueurs_anciens.Pronostiqueur' .
                '                                       GROUP BY    trophees_prec.Pronostiqueurs_Pronostiqueur' .
                '                                   ) AS Poulpe_Or' .
                '                                   ,(' .
                '                                       SELECT      GROUP_CONCAT(journees.Journees_NomCourt SEPARATOR \', \')' .
                '                                       FROM        trophees_prec' .
                '                                       JOIN        journees' .
                '                                                   ON      trophees_prec.Journees_Journee = journees.Journee' .
                '                                       WHERE       trophees_prec.Saisons_Saison = ' . $saison .
                '                                                   AND     journees.Championnats_Championnat = ' . $championnat .
                '                                                   AND     trophees_prec.Trophees_CodeTrophee = 2' .
                '                                                   AND     trophees_prec.Pronostiqueurs_Pronostiqueur = pronostiqueurs_anciens.Pronostiqueur' .
                '                                       GROUP BY    trophees_prec.Pronostiqueurs_Pronostiqueur' .
                '                                   ) AS Poulpe_Argent' .
                '                                   ,(' .
                '                                       SELECT      GROUP_CONCAT(journees.Journees_NomCourt SEPARATOR \', \')' .
                '                                       FROM        trophees_prec' .
                '                                       JOIN        journees' .
                '                                                   ON      trophees_prec.Journees_Journee = journees.Journee' .
                '                                       WHERE       trophees_prec.Saisons_Saison = ' . $saison .
                '                                                   AND     journees.Championnats_Championnat = ' . $championnat .
                '                                                   AND     trophees_prec.Trophees_CodeTrophee = 3' .
                '                                                   AND     trophees_prec.Pronostiqueurs_Pronostiqueur = pronostiqueurs_anciens.Pronostiqueur' .
                '                                       GROUP BY    trophees_prec.Pronostiqueurs_Pronostiqueur' .
                '                                   ) AS Poulpe_Bronze' .
                '                                   ,(' .
                '                                       SELECT      GROUP_CONCAT(journees.Journees_NomCourt SEPARATOR \', \')' .
                '                                       FROM        trophees_prec' .
                '                                       JOIN        journees' .
                '                                                   ON      trophees_prec.Journees_Journee = journees.Journee' .
                '                                       WHERE       trophees_prec.Saisons_Saison = ' . $saison .
                '                                                   AND     journees.Championnats_Championnat = ' . $championnat .
                '                                                   AND     trophees_prec.Trophees_CodeTrophee = 4' .
                '                                                   AND     trophees_prec.Pronostiqueurs_Pronostiqueur = pronostiqueurs_anciens.Pronostiqueur' .
                '                                       GROUP BY    trophees_prec.Pronostiqueurs_Pronostiqueur' .
                '                                   ) AS Soulier_Or' .
                '                                   ,(' .
                '                                       SELECT      GROUP_CONCAT(journees.Journees_NomCourt SEPARATOR \', \')' .
                '                                       FROM        trophees_prec' .
                '                                       JOIN        journees' .
                '                                                   ON      trophees_prec.Journees_Journee = journees.Journee' .
                '                                       WHERE       trophees_prec.Saisons_Saison = ' . $saison .
                '                                                   AND     journees.Championnats_Championnat = ' . $championnat .
                '                                                   AND     trophees_prec.Trophees_CodeTrophee = 5' .
                '                                                   AND     trophees_prec.Pronostiqueurs_Pronostiqueur = pronostiqueurs_anciens.Pronostiqueur' .
                '                                       GROUP BY    trophees_prec.Pronostiqueurs_Pronostiqueur' .
                '                                   ) AS Choupo' .
                '                                   ,(' .
                '                                       SELECT      GROUP_CONCAT(journees.Journees_NomCourt SEPARATOR \', \')' .
                '                                       FROM        trophees_prec' .
                '                                       JOIN        journees' .
                '                                                   ON      trophees_prec.Journees_Journee = journees.Journee' .
                '                                       WHERE       trophees_prec.Saisons_Saison = ' . $saison .
                '                                                   AND     journees.Championnats_Championnat = ' . $championnat .
                '                                                   AND     trophees_prec.Trophees_CodeTrophee = 6' .
                '                                                   AND     trophees_prec.Pronostiqueurs_Pronostiqueur = pronostiqueurs_anciens.Pronostiqueur' .
                '                                       GROUP BY    trophees_prec.Pronostiqueurs_Pronostiqueur' .
                '                                   ) AS Jeremy_Morel' .
                '                                   ,(' .
                '                                       SELECT      GROUP_CONCAT(journees.Journees_NomCourt SEPARATOR \', \')' .
                '                                       FROM        trophees_prec' .
                '                                       JOIN        journees' .
                '                                                   ON      trophees_prec.Journees_Journee = journees.Journee' .
                '                                       WHERE       trophees_prec.Saisons_Saison = ' . $saison .
                '                                                   AND     journees.Championnats_Championnat = ' . $championnat .
                '                                                   AND     trophees_prec.Trophees_CodeTrophee = 7' .
                '                                                   AND     trophees_prec.Pronostiqueurs_Pronostiqueur = pronostiqueurs_anciens.Pronostiqueur' .
                '                                       GROUP BY    trophees_prec.Pronostiqueurs_Pronostiqueur' .
                '                                   ) AS Record_Points' .
                '                                   ,(' .
                '                                       SELECT      GROUP_CONCAT(journees.Journees_NomCourt SEPARATOR \', \')' .
                '                                       FROM        trophees_prec' .
                '                                       JOIN        journees' .
                '                                                   ON      trophees_prec.Journees_Journee = journees.Journee' .
                '                                       WHERE       trophees_prec.Saisons_Saison = ' . $saison .
                '                                                   AND     journees.Championnats_Championnat = ' . $championnat .
                '                                                   AND     trophees_prec.Trophees_CodeTrophee = 8' .
                '                                                   AND     trophees_prec.Pronostiqueurs_Pronostiqueur = pronostiqueurs_anciens.Pronostiqueur' .
                '                                       GROUP BY    trophees_prec.Pronostiqueurs_Pronostiqueur' .
                '                                   ) AS Record_Buteur' .
                '                       FROM        pronostiqueurs_anciens' .
                '                   ) trophees' .
                '       WHERE       Poulpe_Or IS NOT NULL' .
                '                   OR     Poulpe_Argent IS NOT NULL' .
                '                   OR     Poulpe_Bronze IS NOT NULL' .
                '                   OR     Soulier_Or IS NOT NULL' .
                '                   OR     Choupo IS NOT NULL' .
                '                   OR     Jeremy_Morel IS NOT NULL' .
                '                   OR     Record_Points IS NOT NULL' .
                '                   OR     Record_Buteur IS NOT NULL' .
                '       ORDER BY    Pronostiqueurs_NomUtilisateur';

    $query = $db->query($sql);
    $trophies = $query->fetchAll();

    echo json_encode($trophies);

?>