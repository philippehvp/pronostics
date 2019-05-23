<?php
	include_once('commun_administrateur.php');

	// Modification d'une information d'un joueur
	
	// Lecture des paramètres passés à la page
	$joueur = isset($_POST["joueur"]) ? $_POST["joueur"] : -1;
	$valeur = isset($_POST["valeur"]) ? $_POST["valeur"] : 0;
	$champ = isset($_POST["champ"]) ? $_POST["champ"] : 0;
    
    if(!$champ)
        return;
    
    switch($champ) {
        case 1: $nomColonne = 'Joueurs_NomCourt';
        break;
        case 2: $nomColonne = 'Joueurs_NomFamille';
        break;
        case 3: $nomColonne = 'Joueurs_Prenom';
        break;
        case 4: $nomColonne = 'Joueurs_NomCorrespondance';
        break;
        case 5: $nomColonne = 'Joueurs_NomCorrespondanceCote';
        break;
    }
    
    // Cas particulier : si le champ valeur vaut vide, alors il faut mettre vide dans le champ
    if(strlen($valeur))
        $ordreSQL =	'	UPDATE		joueurs' .
                    '	SET			' . $nomColonne . ' = ' . $bdd->quote($valeur) .
                    '	WHERE		joueurs.Joueur = ' . $joueur;
    else
        $ordreSQL =	'	UPDATE		joueurs' .
                    '	SET			' . $nomColonne . ' = NULL' .
                    '	WHERE		joueurs.Joueur = ' . $joueur;
    $bdd->exec($ordreSQL);
?>