<?php
	include_once('commun.php');
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
		$nomPage = 'creer_qualification.php';
		include_once('bandeau.php');

		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';

		// Page de sélection des équipes qualifiées pour les tours éliminatoires

		// Lecture des groupes et des équipes
		$ordreSQL =		'	SELECT		groupes.Groupe, groupes.Groupes_Nom, equipes.Equipe, equipes.Equipes_Nom, equipes.Equipes_Fanion' .
						'				,pronostics_qualifications.PronosticsQualifications_Classement' .
						'	FROM		equipes_groupes' .
						'	JOIN		groupes' .
						'				ON		equipes_groupes.Groupes_Groupe = groupes.Groupe' .
						'	JOIN		equipes' .
						'				ON		equipes_groupes.Equipes_Equipe = equipes.Equipe' .
						'	LEFT JOIN	(' .
						'					SELECT		Groupes_Groupe, Equipes_Equipe, PronosticsQualifications_Classement' .
						'					FROM		pronostics_qualifications' .
						'					WHERE		pronostics_qualifications.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
						'				) pronostics_qualifications' .
						'				ON		equipes_groupes.Groupes_Groupe = pronostics_qualifications.Groupes_Groupe' .
						'						AND		equipes_groupes.Equipes_Equipe = pronostics_qualifications.Equipes_Equipe' .
						'	JOIN		inscriptions' .
						'				ON		groupes.Championnats_Championnat = inscriptions.Championnats_Championnat' .
						'	WHERE		inscriptions.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'	ORDER BY	equipes_groupes.Groupes_Groupe, IFNULL(pronostics_qualifications.PronosticsQualifications_Classement, EquipesGroupes_Chapeau)';

		$req = $bdd->query($ordreSQL);
		$groupes = $req->fetchAll();

		// Parcours des différents groupes et équipes
		$nombreGroupes = sizeof($groupes) / 4;

		// Lecture du numéro du premier groupe concerné par ce championnat
		$ordreSQL =		'	SELECT		MIN(Groupe) AS Groupe' .
						'	FROM		groupes' .
						'	JOIN		inscriptions' .
						'				ON		groupes.Championnats_Championnat = inscriptions.Championnats_Championnat' .
						'	WHERE		inscriptions.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'				AND		inscriptions.Championnats_Championnat NOT IN (1, 4)';

		$req = $bdd->query($ordreSQL);
		$groupeMinimum = $req->fetchAll();
		$numeroPremierGroupe = $groupeMinimum[0]["Groupe"];

		echo '<div id="divClassementGroupes" class="contenu-page">';
			if($nombreGroupes) {
				echo '<label>Pour déplacer une équipe vers la gauche ou vers la droite, veuillez faire un cliquer-déplacer sur chacune d\'elles</label>';

				$classe = 'pair';
				for($i = 0; $i < $nombreGroupes; $i++) {
					$classe = $classe == 'pair' ? 'impair' : 'pair';
					echo '<div class="tuile classement-groupe ' . $classe . '">';
						echo '<div class="nomGroupe gauche">';
							echo '<label>' . $groupes[$i * 4]["Groupes_Nom"] . '</label>';
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
			}
		echo '</div>';

	?>

	<script>
		$(function() {
			afficherTitrePage('divClassementGroupes', 'Saisie des qualifications');
			$('#labelValiderQualification').button().click(	function(event) {
														creerQualification_validerQualifiees('ulGroupe', '<?php echo $nombreGroupes; ?>', '<?php echo $numeroPremierGroupe; ?>', 4);
													}
			);

			$(function() {
				$('.listeTriee').sortable({axis: 'x'});
				$('.listeTriee').disableSelection();
			});
		});
	</script>
</body>
</html>