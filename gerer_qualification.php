<?php
	include('commun_administrateur.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include('commun_entete.php');
	echo '<script src="js/jquery/jquery.ui.touch-punch.min.js"></script>';
?>


</head>


<body>
	<?php
		$nomPage = 'gerer_qualification.php';
		include('bandeau.php');
		
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
		
		// Page de gestion des qualifiés
		
		$championnat = isset($_GET["championnat"]) ? $_GET["championnat"] : 0;
	
		// Lecture des groupes et des équipes
		$ordreSQL =		'	SELECT		groupes.Groupe, groupes.Groupes_Nom, equipes.Equipe, equipes.Equipes_Nom, equipes.Equipes_Fanion' .
						'				,qualifications.Qualifications_Classement' .
						'	FROM		equipes_groupes' .
						'	JOIN		groupes' .
						'				ON		equipes_groupes.Groupes_Groupe = groupes.Groupe' .
						'	JOIN		equipes' .
						'				ON		equipes_groupes.Equipes_Equipe = equipes.Equipe' .
						'	LEFT JOIN	qualifications' .
						'				ON		equipes_groupes.Groupes_Groupe = qualifications.Groupes_Groupe' .
						'						AND		equipes_groupes.Equipes_Equipe = qualifications.Equipes_Equipe' .
						'	WHERE		groupes.Championnats_Championnat = ' . $championnat .
						'	ORDER BY	equipes_groupes.Groupes_Groupe, IFNULL(qualifications.Qualifications_Classement, EquipesGroupes_Chapeau)';

		$req = $bdd->query($ordreSQL);
		$groupes = $req->fetchAll();
		
		// Parcours des différents groupes et équipes
		$nombreGroupes = sizeof($groupes) / 4;
		
		// Lecture du numéro du premier groupe concerné par ce championnat
		$ordreSQL =		'	SELECT		MIN(Groupe) AS Groupe' .
						'	FROM		groupes' .
						'	WHERE		groupes.Championnats_Championnat = ' . $championnat;

		$req = $bdd->query($ordreSQL);
		$groupeMinimum = $req->fetchAll();
		$numeroPremierGroupe = $groupeMinimum[0]["Groupe"];
		
		$classe = 'pair';
		echo '<div id="divClassementGroupes" class="contenu-page">';
			echo '<input type="hidden" id="championnat" value="' . $championnat . '" />';
			$classe = $classe == 'pair' ? 'impair' : 'pair';
			for($i = 0; $i < $nombreGroupes; $i++) {
				$classe = $classe == 'pair' ? 'impair' : 'pair';
				echo '<div class="tuile classement-groupe ' . $classe . '">';
					echo '<div class="nomGroupe gauche">';
						echo '<label>' . $groupes[$i * 4]["Groupes_Nom"] . '</label>';
						echo '<label>&nbsp;-&nbsp;</label>';
						echo '<label class="bouton" onclick="gererQualification_validerQualifieesPoule(' . $championnat . ', ' . $i . ', ' . $numeroPremierGroupe . ', 4);">Valider cette poule</label>';
					echo '</div>';
					echo '<div class="equipes">';
						echo '<ul id="ulGroupe' . $i . '" class="listeTriee">';
							for($j = 0; $j < 4; $j++) {
								echo '<li data-val="' . $groupes[($i * 4) + ($j)]["Equipe"] . '">' . $groupes[($i * 4) + ($j)]["Equipes_Nom"] . '<br /><img src="images/equipes/' . $groupes[($i * 4) + ($j)]["Equipes_Fanion"] . '" alt="" /></li>';
							}
						echo '</ul>';
					echo '</div>';
				echo '</div>';
			}
			
			echo '<div id="divClassementsGroupeValider" class="colle-gauche gauche">';
				echo '<label id="labelValiderQualification">Valider les qualifications</label>';
			echo '</div>';
		echo '</div>';

	?>

	<script>
		$(function() {
			afficherTitrePage('divClassementGroupes', 'Gestion des qualifications');
            $('.listeTriee').sortable({axis: 'x'});
            $('.listeTriee').disableSelection();
            
			$('#labelValiderQualification').button().click(	function(event) {
                gererQualification_validerQualifiees('<?php echo $championnat; ?>', '<?php echo $nombreGroupes; ?>', '<?php echo $numeroPremierGroupe; ?>', 4);
            });
		});
	</script>
</body>
</html>