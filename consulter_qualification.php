<?php
	include_once('commun.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include_once('commun_entete.php');
?>

</head>

<body>
	<?php
		$nomPage = 'consulter_qualification.php';
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
		
		$NOMBRE_EQUIPES = 4;
		
		// Page d'affichage des équipes qualifiées qui sortent de poule
		// Le numéro de championnat dépend du pronostiqueur connecté
		
		echo '<div class="conteneur">';
			include_once('bandeau.php');
			
			echo '<div id="divClassementGroupes" class="contenu-page">';
				// Lecture du nombre de pronostiqueurs
				$ordreSQL =		'	SELECT		COUNT(*) AS NombrePronostiqueurs, inscriptions.Championnats_Championnat' .
								'	FROM		inscriptions' .
								'	JOIN		(' .
								'					SELECT		Championnats_Championnat' .
								'					FROM		inscriptions' .
								'					WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
								'								AND		Championnats_Championnat IN (2, 3)' .
								'				) championnats' .
								'				ON		inscriptions.Championnats_Championnat = championnats.Championnats_Championnat' .
								'	GROUP BY	Championnats_Championnat';

				$req = $bdd->query($ordreSQL);
				$championnats = $req->fetchAll();
				if(count($championnats) == 0)
					echo '<label>Aucune donnée à afficher</label>';
				else {
					$nombrePronostiqueurs = $championnats[0]["NombrePronostiqueurs"] == null ? 0 : $championnats[0]["NombrePronostiqueurs"];
					$championnat = $championnats[0]["Championnats_Championnat"] == null ? 0 : $championnats[0]["Championnats_Championnat"];

					// Lecture des groupes et des équipes
					$ordreSQL =		'	SELECT		Pronostiqueurs_NomUtilisateur, groupes.Groupes_Nom' .
									'				,CASE WHEN Qualifications_Date_Max <= NOW() OR pronostics_qualifications.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' THEN equipes.Equipes_Nom ELSE \'&nbsp;\' END AS Equipes_Nom' .
									'				,CASE WHEN Qualifications_Date_Max <= NOW() OR pronostics_qualifications.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' THEN equipes.Equipes_Fanion ELSE \'_inconnu.png\' END AS Equipes_Fanion' .
									'				,pronostics_qualifications.PronosticsQualifications_Classement' .
									'				,CASE' .
									'					WHEN	Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
									'					THEN	1' .
									'					ELSE	2' .
									'				END AS Ordre' .
									'	FROM		pronostiqueurs' .
									'	JOIN		inscriptions' .
									'				ON		pronostiqueurs.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
									'	JOIN		groupes' .
									'				ON		groupes.Championnats_Championnat = inscriptions.Championnats_Championnat' .
									'						AND		inscriptions.Championnats_Championnat = groupes.Championnats_Championnat' .
									'	JOIN		equipes_groupes' .
									'				ON		groupes.Groupe = equipes_groupes.Groupes_Groupe' .
									'	JOIN		equipes' .
									'				ON		equipes_groupes.Equipes_Equipe = equipes.Equipe' .
									'	LEFT JOIN	(' .
									'					SELECT		Pronostiqueurs_Pronostiqueur, Groupes_Groupe, Equipes_Equipe, PronosticsQualifications_Classement' .
									'					FROM		pronostics_qualifications' .
									'				) pronostics_qualifications' .
									'				ON		equipes_groupes.Groupes_Groupe = pronostics_qualifications.Groupes_Groupe' .
									'						AND		equipes_groupes.Equipes_Equipe = pronostics_qualifications.Equipes_Equipe' .
									'						AND		pronostiqueurs.Pronostiqueur = pronostics_qualifications.Pronostiqueurs_Pronostiqueur' .
									'	JOIN		qualifications_date_max' .
									'				ON		inscriptions.Championnats_Championnat = qualifications_date_max.Championnats_Championnat' .
									'	WHERE		inscriptions.Championnats_Championnat = ' . $championnat .
									'	ORDER BY	Ordre, Pronostiqueurs_NomUtilisateur, equipes_groupes.Groupes_Groupe, IFNULL(pronostics_qualifications.PronosticsQualifications_Classement, EquipesGroupes_Chapeau)';

					$req = $bdd->query($ordreSQL);
					$pronostics = $req->fetchAll();
					
					$nombreGroupes = sizeof($pronostics) / ($NOMBRE_EQUIPES * $nombrePronostiqueurs);
					
					$classe = 'impair';
					for($i = 0; $i < $nombrePronostiqueurs; $i++) {
						$classe = $classe == 'pair' ? 'impair' : 'pair';
						echo '<div class="tuile tuile-consultation ' . $classe . '">';
							echo '<div class="nomPronostiqueur">';
								echo '<label>' . $pronostics[$i * $nombreGroupes * $NOMBRE_EQUIPES]["Pronostiqueurs_NomUtilisateur"] . '</label>';
							echo '</div>';
							echo '<div>';
								for($j = 0; $j < $nombreGroupes; $j++) {
									echo '<div class="equipesConsultation gauche">';
										echo '<label class="gauche">' . $pronostics[($i * $nombreGroupes * $NOMBRE_EQUIPES) + ($j * $NOMBRE_EQUIPES)]["Groupes_Nom"] . '</label>';
										echo '<ul id="ulGroupe' . $j . '" class="listeTrieeConsultation gauche">';
											for($k = 0; $k < $NOMBRE_EQUIPES; $k++)
												echo '<li>' . $pronostics[($i * $nombreGroupes * $NOMBRE_EQUIPES) + ($j * $NOMBRE_EQUIPES) + ($k)]["Equipes_Nom"] . '<br /><img class="consultation" src="images/equipes/' . $pronostics[($i * $nombreGroupes * $NOMBRE_EQUIPES) + ($j * $NOMBRE_EQUIPES) + ($k)]["Equipes_Fanion"] . '" alt="" /></li>';
										echo '</ul>';
									echo '</div>';
								}
							echo '</div>';
						echo '</div>';
					}
				}
			echo '</div>';
			//include_once('pied.php');
		echo '</div>';
	?>

	<script>
		$(function() {
			afficherTitrePage('divClassementGroupes', 'Consultation des qualifications');
		});
	</script>
</body>
</html>