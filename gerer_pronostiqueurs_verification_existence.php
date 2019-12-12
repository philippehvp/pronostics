<?php
	include_once('commun_administrateur.php');

	// Vérification de l'existence d'un nom d'utilisateur dans le processus de création d'un nouveau pronostiqueur

	// Lecture des paramètres passés à la page
	$nomUtilisateur = isset($_POST["nomUtilisateur"]) ? $_POST["nomUtilisateur"] : '';

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
    $tableau['existe'] = 1;
  else
    $tableau['existe'] = 0;

	echo json_encode($tableau);
?>