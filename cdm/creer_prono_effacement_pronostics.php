<?php
	include('commun.php');
	
	// Effacement de tous les pronostics
	$ordreSQL = 'CALL cdm_sp_effacement_pronostics(' . $_SESSION["pronostiqueur"] . ')';
	$bdd->exec($ordreSQL);
	
	
?>