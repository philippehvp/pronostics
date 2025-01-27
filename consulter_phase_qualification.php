<?php
	include_once('commun.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
<?php
	include_once('commun_entete.php');
?>
<link rel="stylesheet" href="consulter_phase_qualification.css" />

</head>

<body>
	<?php
		$nomPage = 'consulter_phase_qualification.php';
		include_once('bandeau.php');

		// Lecture des paramètres passés à la page
		$pronostiqueur = $_SESSION["pronostiqueur"];
		$pronostiqueurConsulte = isset($_GET["pronostiqueur"]) ? $_GET["pronostiqueur"] : $_SESSION["pronostiqueur"];

		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';

		// Page de consultation des classements finaux de la phase de qualification (1 à 8, 9 à 24, 25 à 36)

		// Lecture des pronostiqueurs de la compétition concernée
		$ordreSQL =		'	SELECT		CASE' .
						'					WHEN	pronostiqueurs.Pronostiqueur = ' . $pronostiqueur .
						'					THEN	\'Moi\'' .
						'					ELSE	pronostiqueurs.Pronostiqueurs_NomUtilisateur' .
						'				END AS Pronostiqueurs_NomUtilisateur, pronostiqueurs.Pronostiqueur,' .
						'				CASE' .
						'					WHEN	pronostiqueurs.Pronostiqueur = ' . $pronostiqueurConsulte .
						'					THEN	1' .
						'					ELSE	2' .
						'				END AS Ordre' .
						'	FROM		pronostiqueurs' .
						'	JOIN		inscriptions' .
						'				ON		pronostiqueurs.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
						'	JOIN		(' .
						'					SELECT		inscriptions.Championnats_Championnat AS Championnat' .
						'					FROM		inscriptions' .
						'					WHERE		inscriptions.Championnats_Championnat IN (2, 3)' .
						'								AND		inscriptions.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
						'				) championnats' .
						'				ON		inscriptions.Championnats_Championnat = championnats.Championnat' .
						'	ORDER BY	Ordre';
		
		$req = $bdd->query($ordreSQL);
		$pronostiqueurs = $req->fetchAll();

		// Lecture des groupes et des équipes
		$ordreSQL =		'	SELECT		equipes.Equipe, equipes.Equipes_Nom, equipes.Equipes_Fanion,' .
						'				pronostics_phase.PronosticsPhase_Qualification,' .
						'				equipes_groupes.EquipesGroupes_Chapeau,' .
						'				pronostics_phase.PronosticsPhase_Points' .
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
						'	WHERE		inscriptions.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'	ORDER BY	equipes_groupes.EquipesGroupes_Chapeau, equipes.Equipes_Nom';

		$req = $bdd->query($ordreSQL);
		$equipes = $req->fetchAll();



		echo '<div id="divPhaseQualification" class="contenu-page">';

			echo '<h2>Consulter les pronostics de qualifications de poules de</h2>';
			echo '<select id="selectPronostiqueur" onchange="consulterPhaseQualification_changerPronostiqueur()">';
				echo '<option value="-1" selected="selected">Pronostiqueurs</option>';
				foreach($pronostiqueurs as $unPronostiqueur) {
					$selected = $pronostiqueurConsulte == $unPronostiqueur["Pronostiqueur"] ? ' selected="selected"' : '';
					echo '<option value="' . $unPronostiqueur["Pronostiqueur"] . '"' . $selected . '>' . $unPronostiqueur["Pronostiqueurs_NomUtilisateur"] . '</option>';
				}
			echo '</select>';

			echo '<br /><br />';

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
								echo '<td class="phase">Points (J8 et suivantes)</td>';
							echo '</tr>';

						}

						echo '<tr>';
							echo '<td><img class="fanion" src="images/equipes/' . $uneEquipe["Equipes_Fanion"] . '" alt="" /></td>';
							echo '<td>' . $uneEquipe["Equipes_Nom"] . '</td>';
							echo '<td><input type="radio" value="1" name="phase' . $uneEquipe["Equipe"] . '"' . $checked1 . '"></td>';
							echo '<td><input type="radio" value="2" name="phase' . $uneEquipe["Equipe"] . '"' . $checked2 . '"></td>';
							echo '<td><input type="radio" value="3" name="phase' . $uneEquipe["Equipe"] . '"' . $checked3 . '"></td>';
							echo '<td>' . $uneEquipe["PronosticsPhase_Points"]. '</td>';
						echo '</tr>';
					}
				echo '</tbody>';
			echo '</table>';
		echo '</div>';

	?>

	<script>
		$(function() {
			afficherTitrePage('divPhaseQualification', 'Consultation de la phase des qualifications');
		});
	</script>
</body>
</html>