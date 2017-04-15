<?php
	include('commun_administrateur.php');

	// Mise à jour du bonus anticipé d'une équipe (championne, podium ou relégation)
	
	// Lecture des paramètres passés à la page
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
	$table = isset($_POST["table"]) ? $_POST["table"] : 0;
	$action = isset($_POST["action"]) ? $_POST["action"] : 0;
    
    if($table == 0)
        return;
    
    switch($table) {
        case 1: $nomTable = 'bonus_anticipes_equipe_championne';
        break;
        case 2: $nomTable = 'bonus_anticipes_equipes_podium';
        break;
        case 3: $nomTable = 'bonus_anticipes_equipes_relegation';
        break;
    }
    
    if($action == 1)
        $ordreSQL =     '   REPLACE INTO    ' . $nomTable . '(Equipes_Equipe)' .
                        '   VALUES          (' . $equipe . ')';
    else
        $ordreSQL =     '   DELETE FROM ' . $nomTable . ' WHERE Equipes_Equipe = ' . $equipe;
                        
    $bdd->exec($ordreSQL);

    // Appel automatique de la procédure de calcul des points bonus anticipés
    $ordreSQL =     '   CALL sp_calculpointsbonusanticipes()';
    $bdd->exec($ordreSQL);    
    
?>