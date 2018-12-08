<?php
	include_once('commun.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	//include_once('commun_entete.php');
	echo '<link rel="stylesheet" href="css/cdm/cdm.css" />';
?>
</head>

<body>
	<?php
		echo '<div id="divFenetreMenu">';
			echo '<div id="divMenuW8">';
				echo '<div class="tuileFermer"><label class="fermer">X</label></div>';
				echo '<div class="tuileProfil">';
					echo '<div class="zoneTexte">';
						echo '<label class="nomPronostiqueur">' . $_SESSION["cdm_nom_pronostiqueur"] . '</label><br />';
						echo '<label class="deconnecter" onclick="window.open(\'deconnexion.php\', \'_self\');">Déconnecter</label>';
					echo '</div>';
					// echo '<img src="images/pronostiqueurs/' . $_SESSION["cdm_photo_pronostiqueur"] . '" />';
				echo '</div>';
				echo '<div id="divTuilePronostics" class="colle-gauche tuileRectangulaire" onclick="window.open(\'creer_prono.php\', \'_self\');"><label>Pronostics</label></div>';
				echo '<div id="divTuileQualifications" class="tuileRectangulaire" onclick="window.open(\'creer_qualification.php\', \'_self\');"><label>Qualifications</label></div>';
				echo '<div id="divTuileBonus" class="tuileRectangulaire" onclick="window.open(\'creer_bonus.php\', \'_self\');"><label>Bonus</label></div>';
				echo '<div id="divTuileLigue1" class="colle-gauche tuileRectangulaire" onclick="window.open(\'consulter_resultats.php?championnat=1\', \'_self\');"><label>Résultats Ligue 1</label></div>';
				echo '<div id="divTuileLDC" class="tuileRectangulaire" onclick="window.open(\'consulter_resultats.php?championnat=2\', \'_self\');"><label>Résultats Ligue des Champions</label></div>';
				echo '<div id="divTuileEL" class="tuileRectangulaire" onclick="window.open(\'consulter_resultats.php?championnat=3\', \'_self\');"><label>Résultats Europa League</label></div>';
			
				if($administrateur == 1) {
					echo '<div class="colle-gauche tuileRectangulaire" onclick="window.open(\'creer_match.php\', \'_self\');"><label>Matches</label></div>';
					echo '<div id="divTuileGererQualifications" class="tuileCarree" onclick="window.open(\'gerer_qualification.php\', \'_self\');"><label>Qualifications</label></div>';
					echo '<div id="divTuileGererBonus" class="tuileCarree" onclick="window.open(\'gerer_bonus.php\', \'_self\');"><label>Bonus</label></div>';
					echo '<div id="divTuileGererEffectif" class="tuileRectangulaire" onclick="window.open(\'gerer_effectif.php\', \'_self\');"><label>Gérer l\'effectif</label></div>';
					/*echo '<div class="tuileCarree" onclick="window.open(\'historique.php\', \'_self\');"><label>Historique</label></div>';*/
				}
			echo '</div>';
		echo '</div>';
?>

	<script>


	</script>
	
</body>
</html>