<?php
	include_once('commun_administrateur.php');

	// Suppression d'un joueur dans la liste des meilleurs buteurs
	
	// Lecture des paramètres passés à la page
	$joueur = isset($_POST["joueur"]) ? $_POST["joueur"] : 0;
    
    $ordreSQL =     '   DELETE FROM bonus_meilleur_passeur' .
                    '   WHERE       Joueurs_Joueur = ' . $joueur;
    $bdd->exec($ordreSQL);
    
?>