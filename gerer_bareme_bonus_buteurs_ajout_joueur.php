<?php
	include_once('commun_administrateur.php');

	// Ajout d'un joueur dans la liste des meilleurs buteurs

	// Lecture des paramètres passés à la page
	$joueur = isset($_POST["joueur"]) ? $_POST["joueur"] : 0;
	$bonus = isset($_POST["bonus"]) ? $_POST["bonus"] : 0;


    $ordreSQL =     '   REPLACE INTO    bonus_meilleur_buteur(Joueurs_Joueur, Bonus_Points)' .
                    '   VALUES          (' . $joueur . ', ' . $bonus . ')';
    $bdd->exec($ordreSQL);

?>