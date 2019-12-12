<?php
	include_once('commun_administrateur.php');

	// Lancement de la saison

    $ordreSQL =		'	INSERT INTO     classements' .
                    '   SELECT          pronostiqueurs.Pronostiqueur' .
                    '                   ,(SELECT MIN(journees.Journee) FROM journees WHERE journees.Championnats_Championnat = 1)' .
                    '                   ,NOW()' .
                    '                   ,0, 0, 0, 0, 0, 0, 0, 0' .
                    '   FROM            pronostiqueurs' .
                    '   ORDER BY        pronostiqueurs.Pronostiqueurs_NomUtilisateur' ;
	$bdd->exec($ordreSQL);

?>