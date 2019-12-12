<?php
    include_once('commun_administrateur.php');

    // Sélection d'un pronostiqueur à placer dans une confrontation

    // Lecture des paramètres passés à la page
    $exempte = isset($_POST["exempte"]) ? $_POST["exempte"] : -1;

    // Numéro de journée
    $ordreSQL =     '   SELECT      MAX(Journee) AS Journee' .
                    '   FROM        journees' .
                    '   JOIN        matches' .
                    '               ON      journees.Journee = matches.Journees_Journee' .
                    '   WHERE       journees.Championnats_Championnat = 1' .
                    '               AND     matches.Matches_ScoreEquipeDomicile IS NOT NULL' .
                    '               AND     matches.Matches_ScoreEquipeVisiteur IS NOT NULL';
    $req = $bdd->query($ordreSQL);
    $journees = $req->fetchAll();
    $journee = $journees[0]["Journee"];

    // Lecture des pronostiqueurs ayant un classement en Ligue 1 compris entre la cinquième et la quatorzième place ou la quinzième place jusqu'à la fin
    // C'est le paramètre exempte qui nous indique s'il faut chercher dans les exemptés ou les autres
    if($exempte == 1)
        $ordreSQL =     '   SELECT      DISTINCT pronostiqueurs.Pronostiqueur' .
                        '               ,CASE' .
                        '                   WHEN    confrontations.Confrontation IS NULL' .
                        '                   THEN    pronostiqueurs.Pronostiqueurs_NomUtilisateur' .
                        '                   ELSE    CONCAT(\'Déjà placé : \', pronostiqueurs.Pronostiqueurs_NomUtilisateur)' .
                        '               END AS Pronostiqueurs_NomUtilisateur' .
                        '               ,CASE' .
                        '                   WHEN    confrontations.Confrontation IS NULL' .
                        '                   THEN    0' .
                        '                   ELSE    1' .
                        '               END AS Deja_Place' .
                        '   FROM        pronostiqueurs' .
                        '   JOIN        classements' .
                        '               ON      pronostiqueurs.Pronostiqueur = classements.Pronostiqueurs_Pronostiqueur' .
                        '   JOIN        (' .
                        '                   SELECT      Journees_Journee, MAX(Classements_DateReference) AS Classements_DateReference' .
                        '                   FROM        classements' .
                        '                   WHERE       Journees_Journee = ' . $journee .
                        '               ) classements_max' .
                        '               ON      classements.Journees_Journee = classements_max.Journees_Journee' .
                        '                       AND     classements.Classements_DateReference = classements_max.Classements_DateReference' .
                        '   LEFT JOIN   confrontations' .
                        '               ON      pronostiqueurs.Pronostiqueur = confrontations.Pronostiqueurs_PronostiqueurA' .
                        '                       OR      pronostiqueurs.Pronostiqueur = confrontations.Pronostiqueurs_PronostiqueurB' .
                        '   WHERE       classements.Classements_ClassementGeneralMatch >= 5' .
                        '               AND     classements.Classements_ClassementGeneralMatch <= 14' .
                        '   ORDER BY    Deja_Place, pronostiqueurs.Pronostiqueurs_NomUtilisateur';
    else
        $ordreSQL =     '   SELECT      DISTINCT pronostiqueurs.Pronostiqueur' .
                        '               ,CASE' .
                        '                   WHEN    confrontations.Confrontation IS NULL' .
                        '                   THEN    pronostiqueurs.Pronostiqueurs_NomUtilisateur' .
                        '                   ELSE    CONCAT(\'Déjà placé : \', pronostiqueurs.Pronostiqueurs_NomUtilisateur)' .
                        '               END AS Pronostiqueurs_NomUtilisateur' .
                        '               ,CASE' .
                        '                   WHEN    confrontations.Confrontation IS NULL' .
                        '                   THEN    0' .
                        '                   ELSE    1' .
                        '               END AS Deja_Place' .
                        '   FROM        pronostiqueurs' .
                        '   JOIN        classements' .
                        '               ON      pronostiqueurs.Pronostiqueur = classements.Pronostiqueurs_Pronostiqueur' .
                        '   JOIN        (' .
                        '                   SELECT      Journees_Journee, MAX(Classements_DateReference) AS Classements_DateReference' .
                        '                   FROM        classements' .
                        '                   WHERE       Journees_Journee = ' . $journee .
                        '               ) classements_max' .
                        '               ON      classements.Journees_Journee = classements_max.Journees_Journee' .
                        '                       AND     classements.Classements_DateReference = classements_max.Classements_DateReference' .
                        '   LEFT JOIN   confrontations' .
                        '               ON      pronostiqueurs.Pronostiqueur = confrontations.Pronostiqueurs_PronostiqueurA' .
                        '                       OR      pronostiqueurs.Pronostiqueur = confrontations.Pronostiqueurs_PronostiqueurB' .
                        '   WHERE       classements.Classements_ClassementGeneralMatch > 14' .
                        '   ORDER BY    Deja_Place, pronostiqueurs.Pronostiqueurs_NomUtilisateur';
    $req = $bdd->query($ordreSQL);
    $pronostiqueurs = $req->fetchAll();

    echo '<input id="idPronostiqueurSelectionne" type="hidden" value="0" />';

    // Affichage des pronostiqueurs lus dans une liste
    echo '<div class="gauche">';
        //echo '<label>Effectif</label><br />';
        echo '<select onchange="selectionnerPronostiqueur(this);">';
            echo '<option class="titre-liste">Pronostiqueurs</option>';
            foreach($pronostiqueurs as $unPronostiqueur) {
                echo '<option value="' . $unPronostiqueur["Pronostiqueur"] . '">' . $unPronostiqueur["Pronostiqueurs_NomUtilisateur"] . '</option>';
            }
        echo '</select>';
    echo '</div>';
?>

<script>
    function selectionnerPronostiqueur(elt) {
        $('#idPronostiqueurSelectionne').val($(elt).val());
    }
</script>

