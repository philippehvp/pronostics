<?php
    // Modification de l'adresse de la vidéo en direct
    include_once('commun_administrateur.php');

    // Lecture des paramètres passés à la page
    $adresse = isset($_POST["adresse"]) ? $_POST["adresse"] : '';
    if($adresse == '')
        $ordreSQL =     '   TRUNCATE TABLE cdf_adresse_video';
    else
        $ordreSQL =     '   REPLACE INTO    cdf_adresse_video(CDF_AdresseVideo)' .
                        '   SELECT          ' . $bdd->quote($adresse);
    $bdd->exec($ordreSQL);

    $ordreSQL =     '   UPDATE      journees' .
                    '   SET         Journees_DateEvenement = NOW()' .
                    '   WHERE       Journee = 61';
    $bdd->exec($ordreSQL);
?>