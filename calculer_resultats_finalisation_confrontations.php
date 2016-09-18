<?php
	include('commun_administrateur.php');

	// Page de finalisation des confrontations

	// Lecture des paramètres passés à la page
	
	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;

	$ordreSQL =		'	CALL sp_deplacementvainqueursconfrontations(' . $journee . ')';
	$bdd->exec($ordreSQL);
	
	echo '<label>Confrontations finalisées pour la journée ' . $journee . '</label>';
?>
