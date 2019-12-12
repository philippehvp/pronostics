<?php
    include_once('commun.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
    include_once('commun_entete.php');
?>
    <script type="text/javascript" src="js/datatables/jquery.dataTables.js"></script>
    <script type="text/javascript" src="js/datatables/extensions/dataTables.fixedColumns.min.js"></script>

</head>

<body>
    <?php
        $nomPage = 'consulter_bonus.php';
        echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';

        // Page d'affichage des bonus pronostiqués par tous les pronostiqueurs

        // Lecture des bonus saisis
        $ordreSQL =     '   SELECT      Pronostiqueurs_NomUtilisateur' .
                        '               ,CASE WHEN  Bonus_Date_Max <= NOW() OR Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' THEN IFNULL(equipesChampionnes.Equipes_NomCourt, \'-\') ELSE \'-\' END AS PronosticsBonus_EquipeChampionne' .
                        '               ,CASE WHEN  Bonus_Date_Max <= NOW() OR Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' THEN IFNULL(equipesLDC1.Equipes_NomCourt, \'-\') ELSE \'-\' END AS PronosticsBonus_EquipeLDC1' .
                        '               ,CASE WHEN  Bonus_Date_Max <= NOW() OR Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' THEN IFNULL(equipesLDC2.Equipes_NomCourt, \'-\') ELSE \'-\' END AS PronosticsBonus_EquipeLDC2' .
                        '               ,CASE WHEN  Bonus_Date_Max <= NOW() OR Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' THEN IFNULL(equipesLDC3.Equipes_NomCourt, \'-\') ELSE \'-\' END AS PronosticsBonus_EquipeLDC3' .
                        '               ,CASE WHEN  Bonus_Date_Max <= NOW() OR Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' THEN IFNULL(equipesReleguees1.Equipes_NomCourt, \'-\') ELSE \'-\' END AS PronosticsBonus_EquipeReleguee1' .
                        '               ,CASE WHEN  Bonus_Date_Max <= NOW() OR Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' THEN IFNULL(equipesReleguees2.Equipes_NomCourt, \'-\') ELSE \'-\' END AS PronosticsBonus_EquipeReleguee2' .
                        '               ,CASE WHEN  Bonus_Date_Max <= NOW() OR Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' THEN IFNULL(equipesReleguees3.Equipes_NomCourt, \'-\') ELSE \'-\' END AS PronosticsBonus_EquipeReleguee3' .
                        '               ,CASE WHEN  Bonus_Date_Max <= NOW() OR Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' THEN IFNULL(buteurs.Joueurs_NomCourt, buteurs.Joueurs_NomFamille) ELSE \'-\' END AS PronosticsBonus_MeilleurButeur' .
                        '               ,CASE WHEN  Bonus_Date_Max <= NOW() OR Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' THEN IFNULL(passeurs.Joueurs_NomCourt, passeurs.Joueurs_NomFamille) ELSE \'-\' END AS PronosticsBonus_MeilleurPasseur' .
                        '               ,CASE WHEN  pronostics_bonus.PronosticsBonus_JoueurMeilleurButeur IS NULL' .
                        '                           OR      pronostics_bonus.PronosticsBonus_JoueurMeilleurPasseur IS NULL' .
                        '                           OR      pronostics_bonus.PronosticsBonus_EquipeChampionne IS NULL' .
                        '                           OR      pronostics_bonus.PronosticsBonus_EquipeLDC1 IS NULL' .
                        '                           OR      pronostics_bonus.PronosticsBonus_EquipeLDC2 IS NULL' .
                        '                           OR      pronostics_bonus.PronosticsBonus_EquipeLDC3 IS NULL' .
                        '                           OR      pronostics_bonus.PronosticsBonus_EquipeReleguee1 IS NULL' .
                        '                           OR      pronostics_bonus.PronosticsBonus_EquipeReleguee2 IS NULL' .
                        '                           OR      pronostics_bonus.PronosticsBonus_EquipeReleguee3 IS NULL' .
                        '                     THEN  0' .
                        '                     ELSE  1' .
                        '               END AS Bonus_Saisis' .
                        '   FROM        pronostiqueurs' .
                        '   LEFT JOIN   pronostics_bonus' .
                        '               ON      pronostiqueurs.Pronostiqueur = pronostics_bonus.Pronostiqueurs_Pronostiqueur' .
                        '   LEFT JOIN   joueurs buteurs' .
                        '               ON      pronostics_bonus.PronosticsBonus_JoueurMeilleurButeur = buteurs.Joueur' .
                        '   LEFT JOIN   joueurs passeurs' .
                        '               ON      pronostics_bonus.PronosticsBonus_JoueurMeilleurPasseur = passeurs.Joueur' .
                        '   LEFT JOIN   equipes equipesChampionnes' .
                        '               ON      pronostics_bonus.PronosticsBonus_EquipeChampionne = equipesChampionnes.Equipe' .
                        '   LEFT JOIN   equipes equipesLDC1' .
                        '               ON      pronostics_bonus.PronosticsBonus_EquipeLDC1 = equipesLDC1.Equipe' .
                        '   LEFT JOIN   equipes equipesLDC2' .
                        '               ON      pronostics_bonus.PronosticsBonus_EquipeLDC2 = equipesLDC2.Equipe' .
                        '   LEFT JOIN   equipes equipesLDC3' .
                        '               ON      pronostics_bonus.PronosticsBonus_EquipeLDC3 = equipesLDC3.Equipe' .
                        '   LEFT JOIN   equipes equipesReleguees1' .
                        '               ON      pronostics_bonus.PronosticsBonus_EquipeReleguee1 = equipesReleguees1.Equipe' .
                        '   LEFT JOIN   equipes equipesReleguees2' .
                        '               ON      pronostics_bonus.PronosticsBonus_EquipeReleguee2 = equipesReleguees2.Equipe' .
                        '   LEFT JOIN   equipes equipesReleguees3' .
                        '               ON      pronostics_bonus.PronosticsBonus_EquipeReleguee3 = equipesReleguees3.Equipe' .
                        '   CROSS JOIN  bonus_date_max';

        $req = $bdd->query($ordreSQL);
        $pronostics_bonus = $req->fetchAll();
        $nombrePronostiqueurs = sizeof($pronostics_bonus);
        echo '<div class="conteneur">';
            include_once('bandeau.php');
            echo '<div id="divBonus" class="contenu-page">';
                if(sizeof($pronostics_bonus) == 0) {
                    echo 'Aucune donnée n\'est disponible';
                    return;
                }
                echo '<table class="tableau--bonus">';
                    echo '<thead>';
                        echo '<tr>';
                            echo '<th>Joueurs</th>';
                            echo '<th>Equipe championne</th>';
                            echo '<th>LDC 1</th>';
                            echo '<th>LDC 2</th>';
                            echo '<th>LDC 3</th>';
                            echo '<th>18<sup>ème</sup></th>';
                            echo '<th>Releguée 2</th>';
                            echo '<th>Releguée 3</th>';
                            echo '<th>Meilleur buteur</th>';
                            echo '<th>Meilleur passeur</th>';
                        echo '</tr>';
                    echo '</thead>';

                    echo '<tbody>';
                        for($i = 0; $i < $nombrePronostiqueurs; $i++) {
                            $classe = $pronostics_bonus[$i]["Bonus_Saisis"] == 1 ? 'vert' : 'rouge';

                            echo '<tr>';
                                echo '<td class="' . $classe . '">' . $pronostics_bonus[$i]["Pronostiqueurs_NomUtilisateur"]. '</td>';
                                echo '<td>' . $pronostics_bonus[$i]["PronosticsBonus_EquipeChampionne"]. '</td>';
                                echo '<td>' . $pronostics_bonus[$i]["PronosticsBonus_EquipeLDC1"]. '</td>';
                                echo '<td>' . $pronostics_bonus[$i]["PronosticsBonus_EquipeLDC2"]. '</td>';
                                echo '<td>' . $pronostics_bonus[$i]["PronosticsBonus_EquipeLDC3"]. '</td>';
                                echo '<td>' . $pronostics_bonus[$i]["PronosticsBonus_EquipeReleguee1"]. '</td>';
                                echo '<td>' . $pronostics_bonus[$i]["PronosticsBonus_EquipeReleguee2"]. '</td>';
                                echo '<td>' . $pronostics_bonus[$i]["PronosticsBonus_EquipeReleguee3"]. '</td>';
                                echo '<td>' . $pronostics_bonus[$i]["PronosticsBonus_MeilleurButeur"]. '</td>';
                                echo '<td>' . $pronostics_bonus[$i]["PronosticsBonus_MeilleurPasseur"]. '</td>';
                            echo '</tr>';
                        }
                    echo '</tbody>';
                echo '</table>';
            echo '</div>';
            //include_once('pied.php');
        echo '</div>';


    ?>

    <script>
        $(function() {
            afficherTitrePage('divBonus', 'Consultation des bonus');

            $('.tableau--bonus').dataTable({"bPaginate": false, "bFilter": false, "bInfo": false});
        });
    </script>
</body>
</html>
</html>