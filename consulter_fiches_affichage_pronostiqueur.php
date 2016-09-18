<?php
	// Consultation de la fiche d'identité d'un pronostiqueur
	include('commun.php');
	include('consulter_fiches_fonctions.php');
	
	$pronostiqueurConsulte = isset($_POST["pronostiqueurConsulte"]) ? $_POST["pronostiqueurConsulte"] : 0;
	$modeFenetre = isset($_POST["modeFenetre"]) ? $_POST["modeFenetre"] : 0;
	
	consulterFiche($bdd, $pronostiqueurConsulte, $modeFenetre);

?>
