<?php
	include_once('commun_administrateur.php');

	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;

	// Consultation du match Canal sélectionné par les pronostiqueurs
    $ordreSQL =     '   SELECT          pronostiqueurs.Pronostiqueurs_NomUtilisateur, equipes_domicile.Equipes_Nom AS EquipesDomicile_Nom, equipes_visiteur.Equipes_Nom AS EquipesVisiteur_Nom' .
                    '   FROM            pronostiqueurs' .
                    '   JOIN            journees_pronostiqueurs_canal' .
                    '                   ON      pronostiqueurs.Pronostiqueur = journees_pronostiqueurs_canal.Pronostiqueurs_Pronostiqueur' .
                    '   JOIN            matches' .
                    '                   ON      journees_pronostiqueurs_canal.Matches_Match = matches.Match' .
                    '   JOIN            equipes equipes_domicile' .
                    '                   ON      matches.Equipes_EquipeDomicile = equipes_domicile.Equipe' .
                    '   JOIN            equipes equipes_visiteur' .
                    '                   ON      matches.Equipes_EquipeVisiteur = equipes_visiteur.Equipe' .
                    '   WHERE           journees_pronostiqueurs_canal.Journees_Journee = ' . $journee .
                    '   ORDER BY        journees_pronostiqueurs_canal.Matches_Match';
    $req = $bdd->query($ordreSQL);
    $matchesCanal = $req->fetchAll();

    if(sizeof($matchesCanal) == 0) {
        echo 'Pas de résultat, anomalie';
        return;
    }


    echo '<table class="tableau--classement">';
        echo '<thead>';
            echo '<tr class="tableau--classement-nom-colonnes">';
                echo '<th>Pronostiqueurs</th>';
                echo '<th>Match</th>';
            echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach($matchesCanal as $unMatchCanal) {
            echo '<tr>';
                echo '<td>' . $unMatchCanal["Pronostiqueurs_NomUtilisateur"] . '</td>';
                echo '<td>' . $unMatchCanal["EquipesDomicile_Nom"] . ' - ' . $unMatchCanal["EquipesVisiteur_Nom"] . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
    echo '</table>';
?>
