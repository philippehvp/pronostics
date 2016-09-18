<?php
    include_once('common.php');

    $sql =      '   SELECT      championnats.Championnat, championnats.Championnats_NomCourt' .
                '   FROM        championnats' .
                '   WHERE       championnats.Championnat <> 5';

    $query = $db->query($sql);
    $championships = $query->fetchAll();

    echo json_encode($championships);

?>