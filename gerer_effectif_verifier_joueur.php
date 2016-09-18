<?php
	include('commun_administrateur.php');
	
	// Recherche d'un joueur dans la liste des partipants et des buteurs avant suppression
	
	$joueur = isset($_POST["joueur"]) ? $_POST["joueur"] : 0;
	
	$ordreSQL =		'	SELECT		Matches_Match' .
					'	FROM		(' .
                    '                   SELECT      matches_participants.Matches_Match' .
                    '                   FROM        matches_participants' .
                    '                   WHERE       matches_participants.Joueurs_Joueur = ' . $joueur .
                    '                   UNION ALL' .
                    '                   SELECT      matches_buteurs.Matches_Match' .
                    '                   FROM        matches_buteurs' .
                    '                   WHERE       matches_buteurs.Joueurs_Joueur = ' . $joueur .
                    '               ) matches';

	$req = $bdd->query($ordreSQL);
	$matches = $req->fetchAll();
    
    $tableau = array();
    $tableau["joueurAParticipeOuMarque"] = count($matches);
    
    echo json_encode($tableau);
	
?>