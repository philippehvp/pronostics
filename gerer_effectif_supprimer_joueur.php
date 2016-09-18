<?php
	include('commun_administrateur.php');
	
	// Suppression d'un joueur de la base de données
	
	$joueur = isset($_POST["joueur"]) ? $_POST["joueur"] : 0;
	
	$ordreSQL =		'	DELETE FROM joueurs_equipes WHERE Joueurs_Joueur = ' . $joueur;
	$bdd->exec($ordreSQL);
    
    $ordreSQL =		'	DELETE FROM matches_buteurs WHERE Joueurs_Joueur = ' . $joueur;
	$bdd->exec($ordreSQL);
    
    $ordreSQL =		'	DELETE FROM matches_participants WHERE Joueurs_Joueur = ' . $joueur;
	$bdd->exec($ordreSQL);
    
    $ordreSQL =		'	DELETE FROM joueurs WHERE Joueur = ' . $joueur;
	$bdd->exec($ordreSQL);
	
?>