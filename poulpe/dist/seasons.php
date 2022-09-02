<?php
    include_once('common.php');

    $sql =      '   SELECT      DISTINCT Saisons_Saison AS Saison' .
                '   FROM        classements_prec';

    $query = $db->query($sql);
    $seasons = $query->fetchAll();

    echo json_encode($seasons);

?>