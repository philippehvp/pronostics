<?php
	include_once('commun_administrateur.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include_once('commun_entete.php');
	echo '<script src="js/jquery/jquery.ui.touch-punch.min.js"></script>';
?>


</head>


<body>
	<?php
		$nomPage = 'gerer_poules.php';
		include_once('bandeau.php');
		
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
		
		// Page de gestion des poules
		
		$championnat = isset($_GET["championnat"]) ? $_GET["championnat"] : 0;
	
		// Lecture des groupes et des équipes
		$ordreSQL =		'	SELECT      Groupe, Groupes_Nom, Equipes_Equipe AS Equipe, IFNULL(Equipes_NomCourt, Equipes_Nom) AS Equipes_Nom, Chapeau' .
                        '   FROM        groupes' .
                        '   FULL JOIN   (' .
                        '                   SELECT 1 AS Chapeau UNION SELECT 2 AS Chapeau UNION SELECT 3 AS Chapeau UNION SELECT 4 AS Chapeau' .
                        '               ) chapeaux' .
                        '   LEFT JOIN   equipes_groupes' .
                        '               ON      Groupe = Groupes_Groupe' .
                        '                       AND     Chapeau = EquipesGroupes_Chapeau' .
                        '   LEFT JOIN   equipes' .
                        '               ON      Equipes_Equipe = Equipe' .
                        '   WHERE       Championnats_Championnat = ' . $championnat .
                        '   ORDER BY    Groupe, Chapeau';

		$req = $bdd->query($ordreSQL);
		$groupes = $req->fetchAll();
        
        // Lecture des équipes du championnat
        $ordreSQL =     '   SELECT      Equipe, IFNULL(Equipes_NomCourt, Equipes_Nom) AS Equipes_Nom' .
                        '   FROM        equipes' .
                        '   JOIN        engagements' .
                        '               ON      equipes.Equipe = engagements.Equipes_Equipe' .
                        '   WHERE       engagements.Championnats_Championnat = ' . $championnat .
                        '   ORDER BY    Equipes_Nom';
        $req = $bdd->query($ordreSQL);
        $equipes = $req->fetchAll();
        
		// Parcours des différents groupes et équipes
		$nombreGroupes = count($groupes) / 4;
		
		// Lecture du numéro du premier groupe concerné par ce championnat
		$ordreSQL =		'	SELECT		MIN(Groupe) AS Groupe' .
						'	FROM		groupes' .
						'	WHERE		groupes.Championnats_Championnat = ' . $championnat;

		$req = $bdd->query($ordreSQL);
		$groupeMinimum = $req->fetchAll();
		$numeroPremierGroupe = $groupeMinimum[0]["Groupe"];
        
		echo '<div id="divClassementGroupes" class="contenu-page">';
			echo '<input type="hidden" id="championnat" value="' . $championnat . '" />';
            echo '<table class="tableau--poules">';
                echo '<thead>';
                    echo '<tr>';
                        echo '<th>Groupes</th>';
                        for($j = 0; $j < 4; $j++) {
                            echo '<th>Chapeau ' . ($j + 1) . '</th>';
                        }
                    echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                    for($i = 0; $i < $nombreGroupes; $i++) {
                        echo '<tr>';
                            echo '<td>' . $groupes[$i * 4]["Groupes_Nom"] . '</td>';
                            for($j = 0; $j < 4; $j++) {
                                echo '<td id="tdGroupe' . $i . '">';
                                    echo '<select>';
                                        for($k = 0; $k < count($equipes); $k++) {
                                            $selected = ($equipes[$k]['Equipe'] == $groupes[($i * 4) + ($j)]["Equipe"]) ? ' selected' : '';
                                            echo '<option value="' . $equipes[$k]['Equipe'] . '"' . $selected . '>' . $equipes[$k]['Equipes_Nom'] . '</option>';
                                        }
                                    echo '</select>';
                                echo '</td>';
                            }
                        echo '</tr>';
                    }
                    echo '</tbody>';
                echo '</table>';
			
            echo '<br />';
			echo '<div id="divClassementsGroupeValider" class="colle-gauche gauche">';
				echo '<label id="labelCreerPoules">Créer les poules</label>';
			echo '</div>';
		echo '</div>';

	?>

	<script>
		$(function() {
			afficherTitrePage('divClassementGroupes', 'Gestion des poules');
			$('#labelCreerPoules').button().click(	function(event) {
                gererPoules_creerPoules('<?php echo $championnat; ?>', '<?php echo $nombreGroupes; ?>', '<?php echo $numeroPremierGroupe; ?>', 4);
            });
		
            $('.listeTriee').sortable({axis: 'x'});
            $('.listeTriee').disableSelection();
		});
	</script>
</body>
</html>