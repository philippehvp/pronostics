<?php
	include_once('commun_administrateur.php');
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
		$nomPage = 'creer_match.php';
		include_once('bandeau.php');

		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';

		// Page de création d'un match

		// Sélection des différents championnats
		$ordreSQL = 'SELECT DISTINCT Championnat, Championnats_Nom FROM championnats ORDER BY Championnat';
		$req = $bdd->query($ordreSQL);

		echo '<div id="divCreerMatch" class="contenu-page">';
			// Lecture des paramètres passés à la page
			$championnat = isset($_GET["championnat"]) ? $_GET["championnat"] : 0;
			$journee = isset($_GET["journee"]) ? $_GET["journee"] : 0;

			// Nom du championnat
			// La page peut avoir été appelée :
			// - sans aucune information
			// - avec un numéro de championnat
			// - avec un numéro de journée (prioritaire)

			// Liste des championnats
			$ordreSQL =		'		SELECT			Championnat, Championnats_Nom' .
							'		FROM			championnats' .
							'		ORDER BY		Championnat';
			$req = $bdd->query($ordreSQL);
			$championnats = $req->fetchAll();

			// Si un numéro de journée a été fourni, il faut savoir de quel championnat il s'agit et le présélectionner
			// Si un numéro de championnat a été fourni, il faut savoir quelles sont les journées qui le composent
			if($journee != 0) {
				$ordreSQL =		'		SELECT		Championnat, Championnats_Nom' .
								'		FROM		championnats' .
								'		JOIN		journees' .
								'					ON		championnats.Championnat = journees.Championnats_Championnat' .
								'		WHERE		Journee = ' . $journee .
								'		ORDER BY	Championnat';
				$req = $bdd->query($ordreSQL);
				$championnatsDeLaJournee = $req->fetchAll();
				$championnat = $championnatsDeLaJournee[0]["Championnat"];

				$ordreSQL =		'		SELECT		Journee, Journees_Nom' .
								'		FROM		journees' .
								'		WHERE		Championnats_Championnat = ' . $championnat .
								'		ORDER BY	Journee';
				$req = $bdd->query($ordreSQL);
				$journees = $req->fetchAll();

			}
			else  if($championnat != 0) {
				$ordreSQL =		'		SELECT		Journee, Journees_Nom' .
								'		FROM		journees' .
								'		WHERE		Championnats_Championnat = ' . $championnat .
								'		ORDER BY	Journee';
				$req = $bdd->query($ordreSQL);
				$journees = $req->fetchAll();
			}

			echo '<label>Championnat :</label>';
			echo '<select id="selectChampionnat" onchange="creerMatch_changerChampionnat();">';
				if($championnat != 0)			echo '<option value="0">Championnats</option>';
				else							echo '<option value="0" selected="selected">Championnats</option>';
				foreach($championnats as $unChampionnat) {
					if($championnat == $unChampionnat["Championnat"])
						echo '<option value="' . $unChampionnat["Championnat"] . '" selected="selected">' . $unChampionnat["Championnats_Nom"] . '</option>';
					else
						echo '<option value="' . $unChampionnat["Championnat"] . '">' . $unChampionnat["Championnats_Nom"] . '</option>';
				}
			echo '</select>';

			echo '<span id="spanListeJournees">';
				if($championnat != 0 || $journee != 0) {
					echo '<label>Journée :</label>';
					echo '<select id="selectJournee" onchange="creerMatch_changerJournee();">';
						echo '<option value="0">Journées</option>';
						foreach($journees as $uneJournee) {
							if($journee == $uneJournee["Journee"])
								echo '<option value="' . $uneJournee["Journee"] . '" selected="selected">' . $uneJournee["Journees_Nom"] . '</option>';
							else
								echo '<option value="' . $uneJournee["Journee"] . '">' . $uneJournee["Journees_Nom"] . '</option>';
						}
					echo '</select>';
				}
			echo '</span>';
			echo '<div id="divListeMatches">';
				if($journee != 0)
					include_once('creer_match_liste_matches.php');
			echo '</div>';
		echo '</div>';


		$req->closeCursor();

	?>

	<script>
		$(function() {
			afficherTitrePage('divCreerMatch', 'Gestion des matches');

		});
	</script>

</body>
</html>