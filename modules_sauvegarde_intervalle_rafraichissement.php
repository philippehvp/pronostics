<?php
	include_once('commun.php');

	// Mise à jour de l'intervalle de rafraîchissmeent pour un module et un pronostiqueur
	$module = isset($_POST["module"]) ? $_POST["module"] : 0;
	$parametre = isset($_POST["parametre"]) ? $_POST["parametre"] : -1;
	$intervalleRafraichissement = isset($_POST["intervalleRafraichissement"]) ? $_POST["intervalleRafraichissement"] : 0;

	$ordreSQL =		'	UPDATE		modules_pronostiqueurs' .
					'	SET			ModulesPronostiqueurs_IntervalleRafraichissement = ' . $intervalleRafraichissement .
					'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'				AND		Modules_Module = ' . $module .
					'				AND		ModulesPronostiqueurs_Parametre = ' . $parametre;
	$req = $bdd->exec($ordreSQL);
?>