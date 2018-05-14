<?php
	include('commun.php');

	if($_SESSION["cdm_pronostiqueur"] != 1 && time() > 1528977600) {
		exit();
	}
	
	// Vérification de la présence d'une équipe dans la suite de la compétition suite à la MAJ du score d'un match
	$pronostiqueur = $_SESSION["cdm_pronostiqueur"];

	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	
	// Lecture du vainqueur du match
	$ordreSQL = 'CALL cdm_sp_maj_sequencement(' . $pronostiqueur . ', ' . $match . ')';
	$bdd->exec($ordreSQL);

?>
