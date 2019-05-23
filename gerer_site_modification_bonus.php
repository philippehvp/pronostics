<?php
	include_once('commun_administrateur.php');

	// Sauvegarde de la date max de saisie des bonus
	
	// Lecture des paramètres passés à la page
	$date = isset($_POST["date"]) ? $_POST["date"] : 0;
	$heure = isset($_POST["heure"]) ? $_POST["heure"] : 0;
    $minute = isset($_POST["minute"]) ? $_POST["minute"] : 0;
    
    if($date == 0)
        return;

	$ordreSQL =     '   TRUNCATE TABLE bonus_date_max';
    $bdd->exec($ordreSQL);
    
	$ordreSQL =		'	INSERT INTO bonus_date_max' .
					'	SELECT      STR_TO_DATE(\'' . $date . ' ' . $heure . ':' . $minute . '\', \'%d/%m/%Y %H:%i\')';
	$bdd->exec($ordreSQL);
?>