<?php
	include_once('commun.php');

	// Lecture des paramètres passés à la page
	$choix = isset($_POST["choix"]) ? $_POST["choix"] : null;
	$commentaire = isset($_POST["commentaire"]) ? $_POST["commentaire"] : null;

	$req = $bdd->prepare('REPLACE INTO sondage(Pronostiqueurs_Pronostiqueur, Sondages_Choix, Sondages_Commentaire) values(?, ?, ?)');
	$req->execute(array($_SESSION["pronostiqueur"], $choix, $commentaire));

	$ordreSQL = "UPDATE pronostiqueurs SET Pronostiqueurs_ReponseSondage = 1 WHERE Pronostiqueur = " . $_SESSION["pronostiqueur"];
	$bdd->exec($ordreSQL);
?>
