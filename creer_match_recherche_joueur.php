<?php
	// Recherche d'un joueur sur Google
	include_once('commun_administrateur.php');
	
	
	// Lecture de paramètres passés à la page
	$joueur = isset($_POST["joueur"]) ? $_POST["joueur"] : '';
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : '';

	$url = 'https://www.googleapis.com/customsearch/v1?key=AIzaSyAoKmlo6XgO4rWvmE5DS9swTvX1zZ8PHR0&cx=011890591360819296102:txy7tpqz-hq&q=' . $joueur . '%20' . $equipe;

	$resultat = file_get_contents($url, FILE_USE_INCLUDE_PATH);

	$json = json_decode($resultat);
	

	for($i = 0; $i < count($json->items) && $i <= 3; $i++) {
		echo '<b><a target="_blank" href="' . $json->items[$i]->link . '">' . $json->items[$i]->link . '</a></b>';
		echo '<br />Titre : ' . $json->items[$i]->title;
		echo '<br />' . $json->items[$i]->snippet;
		echo '<br /><br />';
	}

?>