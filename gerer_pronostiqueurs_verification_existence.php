<?php
	include('commun_administrateur.php');

	// Vérification de l'existence d'un nom d'utilisateur dans le processus de création d'un nouveau pronostiqueur
	
	// Lecture des paramètres passés à la page
	$nomUtilisateur = isset($_POST["nom_utilisateur"]) ? $_POST["nom_utilisateur"] : '';
	
  $ordreSQL =    ' SELECT    COUNT(*) AS Nombre' .
                 ' FROM      (' .
                 '            SELECT    Pronostiqueurs_NomUtilisateur' .
                 '            FROM      pronostiqueurs' .
                 '            UNION' .
                 '            SELECT    Pronostiqueurs_NomUtilisateur' .
                 '            FROM      pronostiqueurs_anciens' .
                 '          ) pronostiqueurs' .
                 ' WHERE    pronostiqueurs.Pronostiqueurs_NomUtilisateur = ' . $bdd->quote($nomUtilisateur);

	$req = $bdd->query($ordreSQL);
  $donnees = $req->fetchAll();
  
  
  $tableau = array();
	if($donnees[0]["Nombre"] > 0)
    $tableau['existeDeja'] = 1;
  else
    $tableau['existeDeja'] = 0;
	
	echo json_encode($tableau);
?>