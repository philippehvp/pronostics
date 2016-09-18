<?php
    include_once('common.php');

    $postedData = json_decode(file_get_contents("php://input"), true);
    $saison = json_decode($postedData['saison']);
    $championnat = json_decode($postedData['championnat']);

    $sql =      '   SELECT      DISTINCT Journees_Journee AS Journee, classements_prec.Classements_DateReference, Journees_NomCourt' .
                '   FROM        classements_prec' .
                '   JOIN        journees' .
                '               ON      classements_prec.Journees_Journee = journees.Journee' .
                '   WHERE       classements_prec.Saisons_Saison = ' . $saison .
                '               AND     journees.Championnats_Championnat = ' . $championnat .
                '   ORDER BY    classements_prec.Classements_DateReference';

    $query = $db->query($sql);
    $weeks = $query->fetchAll();

    echo json_encode($weeks);

?>