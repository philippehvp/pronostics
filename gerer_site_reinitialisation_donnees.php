<?php
	include('commun_administrateur.php');

	// Réinitialisation des données de la saison
	
	$ordreSQL =		'	CALL		sp_reinitialisationdonnees()';

	$bdd->exec($ordreSQL);

?>