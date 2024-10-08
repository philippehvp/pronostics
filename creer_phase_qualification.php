<?php
	include_once('commun.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
<?php
	include_once('commun_entete.php');
?>
<link rel="stylesheet" href="creer_phase_qualification.css" />

</head>

<body>
	<?php
		$nomPage = 'creer_phase_qualification.php';
		include_once('bandeau.php');

		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';

		// Page de sélection des classements finaux de la phase de qualification (1 à 8, 9 à 24, 25 à 36)

		// Lecture des groupes et des équipes
		$ordreSQL =		'	SELECT		equipes.Equipe, equipes.Equipes_Nom, equipes.Equipes_Fanion,' .
						'				pronostics_phase.PronosticsPhase_Qualification, pronostics_phase.Championnats_Championnat,' .
						'				equipes_groupes.EquipesGroupes_Chapeau' .
						'	FROM		equipes_groupes' .
						'	JOIN		groupes' .
						'				ON		equipes_groupes.Groupes_Groupe = groupes.Groupe' .
						'	JOIN		equipes' .
						'				ON		equipes_groupes.Equipes_Equipe = equipes.Equipe' .
						'	JOIN		inscriptions' .
						'				ON		groupes.Championnats_Championnat = inscriptions.Championnats_Championnat' .
						'	LEFT JOIN	pronostics_phase' .
						'				ON		inscriptions.Pronostiqueurs_Pronostiqueur = pronostics_phase.Pronostiqueurs_Pronostiqueur' .
						'						AND		equipes.Equipe = pronostics_phase.Equipes_Equipe' .
						'	WHERE		inscriptions.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'	ORDER BY	equipes_groupes.EquipesGroupes_Chapeau, equipes.Equipes_Nom';

		$req = $bdd->query($ordreSQL);
		$equipes = $req->fetchAll();

		echo '<div id="divPhaseQualification" class="contenu-page">';
			echo '<table>';
				echo '<tbody>';
					$chapeau = 0;
					foreach($equipes as $uneEquipe) {
						$checked1 = $uneEquipe["PronosticsPhase_Qualification"] == 1 ? 'checked="checked"' : '';
						$checked2 = $uneEquipe["PronosticsPhase_Qualification"] == 2 ? 'checked="checked"' : '';
						$checked3 = $uneEquipe["PronosticsPhase_Qualification"] == 3 ? 'checked="checked"' : '';

						if ($chapeau != $uneEquipe["EquipesGroupes_Chapeau"]) {
							$chapeau = $uneEquipe["EquipesGroupes_Chapeau"];
							echo '<tr>';
								echo '<td colspan="6">Chapeau ' . $chapeau . '</>';
							echo '</tr>';

							echo '<tr>';
								echo '<td class="colonne-fanion">&nbsp;</td>';
								echo '<td>&nbsp;</td>';
								echo '<td class="phase">Qualifiée 1/8</td>';
								echo '<td class="phase">Barrages</td>';
								echo '<td class="phase">Eliminée</td>';
								echo '<td class="phase">&nbsp;</td>';
							echo '</tr>';

						}

						echo '<tr>';
							echo '<td><img class="fanion" src="images/equipes/' . $uneEquipe["Equipes_Fanion"] . '" alt="" /></td>';
							echo '<td>' . $uneEquipe["Equipes_Nom"] . '</td>';
							echo '<td><input type="radio" value="1" name="phase' . $uneEquipe["Equipe"] . '"' . $checked1 . ' onclick="creerPhaseQualification_pronostiquerEquipe(' . $uneEquipe["Equipe"] . ', ' . $uneEquipe["Championnats_Championnat"] .', 1)"></td>';
							echo '<td><input type="radio" value="2" name="phase' . $uneEquipe["Equipe"] . '"' . $checked2 . ' onclick="creerPhaseQualification_pronostiquerEquipe(' . $uneEquipe["Equipe"] . ', ' . $uneEquipe["Championnats_Championnat"] .', 2)"></td>';
							echo '<td><input type="radio" value="3" name="phase' . $uneEquipe["Equipe"] . '"' . $checked3 . ' onclick="creerPhaseQualification_pronostiquerEquipe(' . $uneEquipe["Equipe"] . ', ' . $uneEquipe["Championnats_Championnat"] .', 3)"></td>';
							echo '<td onclick="decocher(' . $uneEquipe["Equipe"] . ' ); creerPhaseQualification_effacerPronostic(' . $uneEquipe["Equipe"] . ', ' . $uneEquipe["Championnats_Championnat"] . ')"><img src="images/poubelle.png" alt="" /></td>';
						echo '</tr>';
					}
				echo '</tbody>';
			echo '</table>';
		echo '</div>';

	?>

	<script>
		$(function() {
			afficherTitrePage('divPhaseQualification', 'Saisie de la phase des qualifications');
		});

		function decocher(equipe) {
			$('[name="phase' + equipe + '"]').prop('checked', false);
		}
	</script>
</body>
</html>