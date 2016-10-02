<?php
	include('commun_administrateur.php');

	// Page de détection des cotes des joueurs

	// Equipe concernée
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;

	echo '<textarea id="txtCotesJoueurs" rows="20" cols="50" placeholder="Coller ici le code HTML"></textarea>';
?>
