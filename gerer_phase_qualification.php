<?php
	include_once('commun.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
<?php
	include_once('commun_entete.php');
?>
<link rel="stylesheet" href="gerer_phase_qualification.css" />

</head>

<body>
	<?php
		$nomPage = 'gerer_phase_qualification.php';
		include_once('bandeau.php');

		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';

		// Lecture des paramètres passés à la page
		$championnat = isset($_GET["championnat"]) ? $_GET["championnat"] : 0;

		// Page de sélection des classements finaux de la phase de qualification (1 à 8, 9 à 24, 25 à 36)

		// Lecture des groupes et des équipes
		$ordreSQL =		'	SELECT		equipes.Equipe, equipes.Equipes_Nom, equipes.Equipes_Fanion,' .
						'				phase.Phase_Qualification' .
						'	FROM		equipes_groupes' .
						'	JOIN		groupes' .
						'				ON		equipes_groupes.Groupes_Groupe = groupes.Groupe' .
						'	JOIN		equipes' .
						'				ON		equipes_groupes.Equipes_Equipe = equipes.Equipe' .
						'	JOIN		engagements' .
						'				ON		groupes.Championnats_Championnat = engagements.Championnats_Championnat' .
						'						AND		equipes.Equipe = engagements.Equipes_Equipe' .
						'	LEFT JOIN	phase' .
						'				ON		equipes.Equipe = phase.Equipes_Equipe' .
						'	WHERE		phase.Championnats_Championnat = ' . $championnat .
						'	ORDER BY	equipes_groupes.EquipesGroupes_Chapeau, equipes.Equipes_Nom';

		$req = $bdd->query($ordreSQL);
		$equipes = $req->fetchAll();

		echo '<div id="divPhaseQualification" class="contenu-page">';
			echo '<table>';
				echo '<thead>';
					echo '<tr>';
						echo '<th class="colonne-fanion">&nbsp;</th>';
						echo '<th>Equipes</th>';
						echo '<th class="phase">Qualifiée 1/8</th>';
						echo '<th class="phase">Barrages</th>';
						echo '<th class="phase">Eliminée</th>';
						echo '<th class="phase">&nbsp;</th>';
					echo '</tr>';
				echo '</thead>';

				echo '<tbody>';
					foreach($equipes as $uneEquipe) {
						$checked1 = $uneEquipe["Phase_Qualification"] == 1 ? 'checked="checked"' : '';
						$checked2 = $uneEquipe["Phase_Qualification"] == 2 ? 'checked="checked"' : '';
						$checked3 = $uneEquipe["Phase_Qualification"] == 3 ? 'checked="checked"' : '';


						echo '<tr>';
							echo '<td><img class="fanion" src="images/equipes/' . $uneEquipe["Equipes_Fanion"] . '" alt="" /></td>';
							echo '<td>' . $uneEquipe["Equipes_Nom"] . '</td>';
							echo '<td><input type="radio" value="1" name="phase' . $uneEquipe["Equipe"] . '"' . $checked1 . ' onclick="gererPhaseQualification_validerEquipe(' . $uneEquipe["Equipe"] . ', ' . $championnat .', 1)"></td>';
							echo '<td><input type="radio" value="2" name="phase' . $uneEquipe["Equipe"] . '"' . $checked2 . ' onclick="gererPhaseQualification_validerEquipe(' . $uneEquipe["Equipe"] . ', ' . $championnat .', 2)"></td>';
							echo '<td><input type="radio" value="3" name="phase' . $uneEquipe["Equipe"] . '"' . $checked3 . ' onclick="gererPhaseQualification_validerEquipe(' . $uneEquipe["Equipe"] . ', ' . $championnat .', 3)"></td>';
							echo '<td onclick="decocher(' . $uneEquipe["Equipe"] . ' ); gererPhaseQualification_effacerPronostic(' . $uneEquipe["Equipe"] . ', ' . $championnat . ')"><img src="images/poubelle.png" alt="" /></td>';
						echo '</tr>';
					}
				echo '</tbody>';
			echo '</table>';
		echo '</div>';

	?>

	<script>
		$(function() {
			afficherTitrePage('divPhaseQualification', 'Gestion de la phase des qualifications');
		});

		function decocher(equipe) {
			$('[name="phase' + equipe + '"]').prop('checked', false);
		}
	</script>
</body>
</html>