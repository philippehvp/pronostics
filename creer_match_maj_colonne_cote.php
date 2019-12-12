<?php
	// Ajout du match dans la table des matches en direct
	include_once('commun_administrateur.php');


	// Lecture des paramètres passés à la page
	$colonneCote = isset($_POST["colonne_cote"]) ? $_POST["colonne_cote"] : 1;

	$ordreSQL =		'	UPDATE		configurations' .
					'	SET			Configurations_ColonneCote = ' . $colonneCote .
					'	WHERE		Configuration = 1';
	$bdd->exec($ordreSQL);

?>