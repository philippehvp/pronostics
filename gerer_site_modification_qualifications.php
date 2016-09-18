<?php
	include('commun_administrateur.php');

	// Sauvegarde de la date max de saisie des bonus
	
	// Lecture des paramètres passés à la page
    $championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;
	$date = isset($_POST["date"]) ? $_POST["date"] : 0;
	$heure = isset($_POST["heure"]) ? $_POST["heure"] : 0;
    $minute = isset($_POST["minute"]) ? $_POST["minute"] : 0;
    
    if($date == 0)
        return;

	$ordreSQL =		'	REPLACE INTO qualifications_date_max(Championnats_Championnat, Qualifications_Date_Max)' .
					'	SELECT      ' . $championnat . ', STR_TO_DATE(\'' . $date . ' ' . $heure . ':' . $minute . '\', \'%d/%m/%Y %H:%i\')';
	$bdd->exec($ordreSQL);
?>