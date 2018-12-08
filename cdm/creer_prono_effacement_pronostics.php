<?php
	include_once('commun.php');
	
	// Effacement de tous les pronostics
	$ordreSQL = 'CALL cdm_sp_effacement_pronostics(' . $_SESSION["cdm_pronostiqueur"] . ')';
	$bdd->exec($ordreSQL);
	
	
?>