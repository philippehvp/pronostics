<?php
	include('commun_administrateur.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include('commun_entete.php');
?>

	<script type="text/javascript" src="js/datatables/jquery.dataTables.js"></script>
	<script type="text/javascript" src="js/datatables/extensions/dataTables.fixedColumns.min.js"></script>
</head>

<body>
	<?php
		$nomPage = 'gerer_bareme_bonus_equipes.php';
		include('bandeau.php');
		
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
		
		// Gestion du barème des bonus des équipes ainsi que des bonus anticipés
		
		// Lecture des équipes et des barèmes
		$ordreSQL =		'	SELECT      equipes.Equipe, IFNULL(equipes.Equipes_NomCourt, equipes.Equipes_Nom) AS Equipes_NomCourt, equipes.Equipes_Fanion' .
                        '               ,IFNULL(bonus_equipe_championne.Bonus_Points, 0) AS Bonus_Championne' .
                        '               ,IFNULL(bonus_equipes_podium.Bonus_Points, 0) AS Bonus_Podium' .
                        '               ,IFNULL(bonus_equipes_relegation.Bonus_Points, 0) AS Bonus_Relegation' .
                        '               ,CASE WHEN bonus_anticipes_equipe_championne.Equipes_Equipe IS NOT NULL THEN 1 ELSE 0 END AS BonusAnticipes_Championne' .
                        '               ,CASE WHEN bonus_anticipes_equipes_podium.Equipes_Equipe IS NOT NULL THEN 1 ELSE 0 END AS BonusAnticipes_Podium' .
                        '               ,CASE WHEN bonus_anticipes_equipes_relegation.Equipes_Equipe IS NOT NULL THEN 1 ELSE 0 END AS BonusAnticipes_Relegation' .
                        '   FROM        equipes' .
                        '   JOIN        engagements' .
                        '               ON      equipes.Equipe = engagements.Equipes_Equipe' .
                        '   LEFT JOIN   bonus_equipe_championne' .
                        '               ON      equipes.Equipe = bonus_equipe_championne.Equipes_Equipe' .
                        '   LEFT JOIN   bonus_equipes_podium' .
                        '               ON      equipes.Equipe = bonus_equipes_podium.Equipes_Equipe' .
                        '   LEFT JOIN   bonus_equipes_relegation' .
                        '               ON      equipes.Equipe = bonus_equipes_relegation.Equipes_Equipe' .
                        '   LEFT JOIN   bonus_anticipes_equipe_championne' .
                        '               ON      equipes.Equipe = bonus_anticipes_equipe_championne.Equipes_Equipe' .
                        '   LEFT JOIN   bonus_anticipes_equipes_podium' .
                        '               ON      equipes.Equipe = bonus_anticipes_equipes_podium.Equipes_Equipe' .
                        '   LEFT JOIN   bonus_anticipes_equipes_relegation' .
                        '               ON      equipes.Equipe = bonus_anticipes_equipes_relegation.Equipes_Equipe' .
                        '   WHERE       engagements.Championnats_Championnat = 1' .
                        '               AND     equipes.Equipes_L1Europe IS NULL' .
                        '   ORDER BY    IFNULL(equipes.Equipes_NomCourt, equipes.Equipes_Nom)';

		$req = $bdd->query($ordreSQL);
		$baremesBonus = $req->fetchAll();
        $nombreEquipes = count($baremesBonus);

		echo '<div id="divBonus" class="contenu-page">';
            // Tableau des équipes
            echo '<table class="tableau--bonus">';
                echo '<thead>';
                    echo '<tr>';
                        echo '<th>&nbsp;</th>';
                        echo '<th>Equipes</th>';
                        echo '<th>Championnes de Ligue 1</th>';
                        echo '<th>Sur le podium</th>';
                        echo '<th>Reléguées en L2</th>';
                    echo '</tr>';
                echo '</thead>';
                
                echo '<tbody>';
                    for($i = 0; $i < $nombreEquipes; $i++) {
                        echo '<tr>';
                            $bonusAnticipeEquipeChampionne = $baremesBonus[$i]["BonusAnticipes_Championne"] == 1 ? ' checked' : '';
                            $bonusAnticipeEquipePodium = $baremesBonus[$i]["BonusAnticipes_Podium"] == 1 ? ' checked' : '';
                            $bonusAnticipeEquipeRelegation = $baremesBonus[$i]["BonusAnticipes_Relegation"] == 1 ? ' checked' : '';
                        
                            echo '<td><img src="images/equipes/' . $baremesBonus[$i]["Equipes_Fanion"] . '" alt="Fanion" class="fanion" />';
                            echo '<td>' . $baremesBonus[$i]["Equipes_NomCourt"]. '</td>';
                            echo '<td>';
                                echo '<input type="text" id="txtBaremeBonusEquipesChampionne_' . $baremesBonus[$i]["Equipe"] . '" value="' . $baremesBonus[$i]["Bonus_Championne"]. '" onchange="gererBaremeBonusEquipes_modifierBonus($(this), ' . $baremesBonus[$i]["Equipe"] . ', 1);" />';
                                echo '&nbsp;<input type="checkbox" value="' . $baremesBonus[$i]["Equipe"] . '" ' . $bonusAnticipeEquipeChampionne . ' onclick="gererBaremeBonusEquipes_modifierBonusAnticipe(this, 1);" />';
                            echo '</td>';
                            echo '<td>';
                                echo '<input type="text" id="txtBaremeBonusEquipesPodium_' . $baremesBonus[$i]["Equipe"] . '" value="' . $baremesBonus[$i]["Bonus_Podium"]. '" onchange="gererBaremeBonusEquipes_modifierBonus($(this), ' . $baremesBonus[$i]["Equipe"] . ', 2);" />';
                                echo '&nbsp;<input type="checkbox" value="' . $baremesBonus[$i]["Equipe"] . '" ' . $bonusAnticipeEquipePodium . ' onclick="gererBaremeBonusEquipes_modifierBonusAnticipe(this, 2);" />';
                            echo '</td>';
                            echo '<td>';
                                echo '<input type="text" id="txtBaremeBonusEquipesRelegation_' . $baremesBonus[$i]["Equipe"] . '" value="' . $baremesBonus[$i]["Bonus_Relegation"]. '" onchange="gererBaremeBonusEquipes_modifierBonus($(this), ' . $baremesBonus[$i]["Equipe"] . ', 3);" />';
                                echo '&nbsp;<input type="checkbox" value="' . $baremesBonus[$i]["Equipe"] . '" ' . $bonusAnticipeEquipeRelegation . ' onclick="gererBaremeBonusEquipes_modifierBonusAnticipe(this, 3);" />';
                            echo '</td>';
                        echo '</tr>';
                    }
                echo '</tbody>';
            echo '</table>';
		echo '</div>';

	?>
	
	<script>
		$(function() {
			afficherTitrePage('divBonus', 'Gérer le barème des bonus équipes');
            
            $('.tableau--bonus').dataTable({"bPaginate": false, "bFilter": false, "bInfo": false});
		});
	</script>
</body>
</html>