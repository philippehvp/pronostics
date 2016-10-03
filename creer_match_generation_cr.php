<?php
	include('commun_administrateur.php');

	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;

	// Génération du CR
    // Lecture du modèle de CR
    $ordreSQL =     '   SELECT      CompteRenduModeles_Modele' .
                    '   FROM        compte_rendu_modeles' .
                    '   LIMIT       1';
    $req = $bdd->query($ordreSQL);
    $modele = $req->fetchAll()[0]["CompteRenduModeles_Modele"];
    $texteFinal = $modele;

    // Lecture des différentes requêtes
	$ordreSQL =		'	SELECT      CompteRenduRequete, CompteRenduRequetes_Requete' .
                    '   FROM        compte_rendu_requetes' .
					'	WHERE		CompteRenduRequetes_Actif = 1' .
                    '   ORDER BY    CompteRenduRequete';
	$req = $bdd->query($ordreSQL);
    $requetes = $req->fetchAll();

    foreach($requetes as $uneRequete) {
        // On exécute la requête si et seulement si son code de requête se trouve dans le modèle de compte-rendu
        $identifiant = '%' . $uneRequete["CompteRenduRequete"] . '%';
        if(strpos($texteFinal, $identifiant)) {
            // Exécution de la requête
            // Dans une requête, il est possible qu'un champ p_Journee soit présent
            // Dans ce cas, il est nécessaire de le remplacer par le numéro de la journée avant de lancer l'exécution de la requête
            $ordreSQL = str_replace("p_Journee", $journee, $uneRequete["CompteRenduRequetes_Requete"]);
            $req = $bdd->query($ordreSQL);
            $resultats = $req->fetchAll();
			$resultat = $resultats[0]["Chaine"];

            // Remplacement dans le compte-rendu de l'identifiant par le résultat de la requête
            $texteFinal = str_replace($identifiant, $resultat, $texteFinal);
        }
    }

    echo $texteFinal;

?>
