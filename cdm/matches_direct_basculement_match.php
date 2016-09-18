<?php
	include('commun_administrateur.php');
	
	// Mise à jour d'un match qui passe du mode en direct ou non
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;

	$ordreSQL = 'CALL cdm_sp_matches_direct(' . $match . ')';
	$req = $bdd->exec($ordreSQL);

?>