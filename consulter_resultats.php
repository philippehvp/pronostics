<?php
	include_once('commun.php');
	include_once('fonctions.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include_once('commun_entete.php');
?>
	<script type="text/javascript" src="js/datatables/jquery.dataTables.js"></script>
	<script type="text/javascript" src="js/datatables/extensions/dataTables.fixedColumns.min.js"></script>

</head>

<body>
	<?php
		$nomPage = 'consulter_resultats.php';
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';

		echo '<div class="conteneur">';
			include_once('bandeau.php');
			// Page d'affichage des pronostics et des résultats de tous les joueurs
			echo '<div id="divResultats" class="marge-haute">';
				// Lecture des paramètres passés à la page
				$championnat = isset($_GET["championnat"]) ? $_GET["championnat"] : 0;
				$journee = isset($_GET["journee"]) ? $_GET["journee"] : 0;
				if($championnat == 0 && $journee == 0) {
					echo '<label>AUCUN PARAMETRE N\'A ETE RENSEIGNE</label>';
					return;
				}

				// Nom du championnat
				// La page peut avoir été appelée avec un numéro de journée ou par un numéro de championnat (le numéro de journée est prioritaire)
				if($journee != 0) {
					$ordreSQL =		'		SELECT			Championnats_Nom, Championnat' .
									'		FROM			championnats' .
									'		JOIN			journees' .
									'						ON		championnats.Championnat = journees.Championnats_Championnat' .
									'		WHERE			Journee = ' . $journee;
					$req = $bdd->query($ordreSQL);
					$unChampionnat = $req->fetch();
					$nomChampionnat = $unChampionnat["Championnats_Nom"];
					$championnat = $unChampionnat["Championnat"];
				}
				else {
					$ordreSQL =		'		SELECT			Championnats_Nom' .
									'		FROM			championnats' .
									'		WHERE			Championnat = ' . $championnat;
					$req = $bdd->query($ordreSQL);
					$unChampionnat = $req->fetch();
					$nomChampionnat = $unChampionnat["Championnats_Nom"];
				}

				// Liste des journées, dans l'ordre décroissant, du championnat demandé
				// On affiche uniquement les journées dont l'heure max de pronostic d'au moins un match est dépassée
				$ordreSQL =		'		SELECT			DISTINCT Journee, Journees_Nom, Championnats_Nom' .
								'		FROM			matches' .
								'		INNER JOIN		journees' .
								'						ON		matches.Journees_Journee = journees.Journee' .
								'		INNER JOIN		championnats' .
								'						ON		journees.Championnats_Championnat = championnats.Championnat' .
								'		WHERE			(	matches.Matches_Date <= NOW()' .
								'							OR journees.Journees_Active = 1' .
								'						)' .
								'						AND		journees.Championnats_Championnat = ' . $championnat .
								'		ORDER BY		Journees_Journee DESC';

				$req = $bdd->query($ordreSQL);
				$championnats = $req->fetchAll();
				$nombreChampionnats = count($championnats);
				if($nombreChampionnats == 0)
					echo '<label>Aucune donnée à afficher</label>';
				else {
					$parcoursJournees = null;

					echo '<label>' . $nomChampionnat . ' - Résultats de la journée :</label>';
					echo '<select id="selectJournee">';
						foreach($championnats as $unChampionnat) {
							if($parcoursJournees == null)
								$parcoursJournees = $unChampionnat["Journee"];

							if($journee == 0)
								$journee = $parcoursJournees;

							$selected = ($unChampionnat["Journee"] == $journee) ? ' selected' : '';
							if($selected != '')
								echo $parcoursJournees;
							echo '<option value="' . $unChampionnat["Journee"] . '"' . $selected . '>' . $unChampionnat["Journees_Nom"] . '</option>';
						}
					echo '</select>';

					echo '<div id="divResultatsPronostics">';
						include_once('consulter_resultats_resultats_pronostics.php');
					echo '</div>';
				}
			echo '</div>';
			//include_once('pied.php');
		echo '</div>';
	?>

	<script>
		$(function() {
			afficherTitrePage('divResultats', 'Consultation des résultats');
			$('#selectJournee').change(	function() {
				consulterResultats_changerJournee();
			});
		});

	</script>

</body>
</html>