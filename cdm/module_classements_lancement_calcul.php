<?php
	include('commun_administrateur.php');

	// Lancement du calcul des points de poule et de phase finale
	
	// Lecture des paramètres passés à la page
	$journeeEnCours = isset($_POST["journee"]) ? $_POST["journee"] : 0;

	$ordreSQL = 'CALL cdm_sp_calcul_scores_et_bonus(' . $journeeEnCours . ')';
	$bdd->exec($ordreSQL);
?>