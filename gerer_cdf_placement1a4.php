<?php
    // Placement des pronostiqueurs de 1 à 4
    include_once('commun_administrateur.php');

    $ordreSQL =     '   CALL sp_placement1a4()';
    $bdd->exec($ordreSQL);

?>