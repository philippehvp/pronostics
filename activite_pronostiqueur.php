<?php
	include('commun.php');
	
	// Présence du pronostiqueur sur le site
	$ordreSQL = 'REPLACE INTO pronostiqueurs_activite(Pronostiqueurs_Pronostiqueur, PronostiqueursActivite_Date) VALUES (' . $_SESSION["pronostiqueur"] . ', NOW())';
	$req = $bdd->exec($ordreSQL);
?>