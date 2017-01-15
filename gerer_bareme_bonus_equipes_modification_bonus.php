<?php
    include('commun_administrateur.php');

    // Mise à jour du barème des bonus des équipes
    
    // Lecture des paramètres passés à la page
    $equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
    $bonus = isset($_POST["bonus"]) ? $_POST["bonus"] : 0;
    $table = isset($_POST["table"]) ? $_POST["table"] : 0;
    
    if($table == 0)
        return;
    
    switch($table) {
        case 1: $nomTable = 'bonus_equipe_championne';
        break;
        case 2: $nomTable = 'bonus_equipes_podium';
        break;
        case 3: $nomTable = 'bonus_equipes_relegation';
        break;
        case 4: $nomTable = 'equipes_penalites';
        break;

    }
    
    if($table != 4)
        $ordreSQL =     '   REPLACE INTO    ' . $nomTable . '(Equipes_Equipe, Bonus_Points)' .
                        '   VALUES          (' . $equipe . ', ' . $bonus . ')';
    else
        $ordreSQL =     '   REPLACE INTO    ' . $nomTable . '(Equipes_Equipe, EquipesPenalites_Penalite)' .
                        '   VALUES          (' . $equipe . ', ' . $bonus . ')';
    $bdd->exec($ordreSQL);
    
?>