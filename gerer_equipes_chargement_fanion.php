<?php
    include_once('commun_administrateur.php');

	// Chargement du fanion d'une équipe

    $fichierSource = $_FILES['fichier']['tmp_name'];
    $dossierDestination = 'images/equipes/';
    $fichierDestination = $_FILES['fichier']['name'];
    $equipe = isset($_POST['equipe']) ? $_POST['equipe'] : 0;


    $tableau = array();
    $tableau['equipe'] = $equipe;

    if(move_uploaded_file($fichierSource, $dossierDestination . $fichierDestination)) {
        $tableau['reussite'] = 1;

        // Mise à jour de la table des équipes pour mettre le nouveau nom du fichier
        $ordreSQL =     '   UPDATE      equipes' .
                        '   SET         Equipes_Fanion = ' . $bdd->quote($fichierDestination) .
                        '   WHERE       Equipe = ' . $equipe;
        $bdd->exec($ordreSQL);
    }
    else
        $tableau['reussite'] = 0;

    echo json_encode($tableau);

?>