<?php
    // Placement des pronostiqueurs de 1 à 4
    include_once('commun_administrateur.php');
    
    $ordreSQL =     '   UPDATE      confrontations' .
                    '   SET         Pronostiqueurs_PronostiqueurA = NULL' .
                    '               ,Pronostiqueurs_PronostiqueurB = NULL' .
                    '               ,Confrontations_ScorePronostiqueurA = NULL' .
                    '               ,Confrontations_ScorePronostiqueurB = NULL' .
                    '               ,Confrontations_ScoreButeurPronostiqueurA = NULL' .
                    '               ,Confrontations_ScoreButeurPronostiqueurB = NULL' .
                    '               ,Pronostiqueurs_Vainqueur = NULL';
    $bdd->exec($ordreSQL);

    $ordreSQL =     '   UPDATE      journees' .
                    '   SET         Journees_DateEvenement = NOW()' .
                    '   WHERE       Journee = 61';
    $bdd->exec($ordreSQL);
?>