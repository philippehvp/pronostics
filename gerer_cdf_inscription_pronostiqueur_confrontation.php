<?php
    include_once('commun_administrateur.php');

    // Inscription d'un pronostiqueur dans une cellule

    // Lecture des paramètres passés à la page
    $numeroCase = isset($_POST["numero_case"]) ? $_POST["numero_case"] : 0;
    $pronostiqueur = isset($_POST["pronostiqueur"]) ? $_POST["pronostiqueur"] : 0;
    $exempte = isset($_POST["exempte"]) ? $_POST["exempte"] : -1;

    if($exempte == 1) {
        $ordreSQL =     '   CALL    sp_placement5a14(\'\', ' . $pronostiqueur . ', ' . $numeroCase . ')';
        $bdd->exec($ordreSQL);
    }
    else if($exempte == 2) {
        $ordreSQL =     '   CALL    sp_placement15a50(\'\', ' . $pronostiqueur . ', ' . $numeroCase . ')';
        $bdd->exec($ordreSQL);
    }
?>