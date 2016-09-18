<?php
	include('commun.php');

	// Mise à jour du thème
	
	// Lecture des paramètres passés à la page
	$theme = isset($_POST["theme"]) ? $_POST["theme"] : 1;
	
	$ordreSQL =		'	UPDATE		pronostiqueurs' .
					'	SET			Themes_Theme = ' . $theme .
					'	WHERE		Pronostiqueur = ' . $_SESSION["pronostiqueur"];
					
	$req = $bdd->exec($ordreSQL);
	
	$ordreSQL =		'	SELECT		Themes_NomCourt' .
					'	FROM		themes' .
					'	WHERE		Theme = ' . $theme;
	$req = $bdd->query($ordreSQL);
	$themes = $req->fetchAll();
	$_SESSION["theme"] = $themes[0]["Themes_NomCourt"];
	
?>	