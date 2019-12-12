<?php
	include_once('commun_administrateur.php');


	// Page de sauvegarde du règlement (LDC ou EL)
	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;
	$reglement = isset($_POST["reglement"]) ? $_POST["reglement"] : 'Rien';

//echo $reglement;
	$ordreSQL =		'	UPDATE		reglements' .
					'	SET			Reglements_Texte = ' . $bdd->quote($reglement) .
					'	WHERE		Championnats_Championnat = ' . $championnat;

	$bdd->exec($ordreSQL);

?>
