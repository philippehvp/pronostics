<?php
	include_once('commun_administrateur.php');
	
	// Affichage / masquage d'une option de menu
	$menu = isset($_POST["menu"]) ? $_POST["menu"] : 0;
	
	$ordreSQL =		'	UPDATE		menus' .
					'	SET			Menus_Visible =		CASE' .
					'										WHEN	Menus_Visible = 1' .
					'										THEN	0' .
					'										ELSE	1' .
					'									END' .
					'	WHERE		Menu = ' . $menu;

	$bdd->exec($ordreSQL);
?>