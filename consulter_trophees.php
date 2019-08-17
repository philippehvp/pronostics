<?php
	include_once('commun.php');
	include_once('classements_pronostiqueurs_fonctions.php');
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
	// Consultation des trophées
	$nomPage = 'consulter_trophees.php';
	echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
	
	// Si l'affichage de la page des trophées a été demandé par l'administrateur, c'est qu'une journée vient de se terminer et que le pronostiqueur, dès sa connexion, doit être dirigé
	// vers cette. Une fois cette page consultée, il n'est plus nécessaire à la prochaine connexion d'afficher cette page (jusqu'à la prochaine demande de l'administrateur)
	$afficherTrophees = isset($_GET["affichertrophees"]) ? $_GET["affichertrophees"] : 0;

	if($afficherTrophees == 1) {
		$ordreSQL = 'UPDATE pronostiqueurs SET Pronostiqueurs_AfficherTropheesChampionnat = 0 WHERE Pronostiqueur = ' . $_SESSION["pronostiqueur"];
		$req = $bdd->exec($ordreSQL);
	}

	// Championnat à afficher
	$championnat = isset($_GET["championnat"]) ? $_GET["championnat"] : 0;
	include_once('consulter_trophees_fonctions.php');

	echo '<div class="conteneur">';
		include_once('bandeau.php');
		// Les championnats sont mis dans des onglets différents pour une meilleure visibilité
		echo '<div class="contenu-page">';
			// Affichage des trophées du championnat demandé
			$ordreSQL = lireJournee($championnat);
			$req = $bdd->query($ordreSQL);
			$donnees = $req->fetchAll();
			$nombreJournees = count($donnees);
			if($nombreJournees == 0)
				echo '<label>Aucune donnée à afficher</label>';
			else {
				$journee = $donnees[0]["Journee"] == null ? 0 : $donnees[0]["Journee"];
				$journeeNom = $donnees[0]["Journees_Nom"] == null ? '' : $donnees[0]["Journees_Nom"];
				$dateMAJJournee = $donnees[0]["Journees_DateMAJ"] == null ? '' : $donnees[0]["Journees_DateMAJ"];
				$dtDateMAJ = new DateTime($dateMAJJournee);
				$req->closeCursor();

				// Liste des journées du championnat
				$ordreSQL =		'	SELECT		Journee, IFNULL(Journees_NomCourt, Journees_Nom) AS Journees_Nom' .
								'	FROM		journees' .
								'	WHERE		Championnats_Championnat = ' . $championnat .
								'				AND		Journee <= ' . $journee .
								'	ORDER BY	Journee DESC';

				$req = $bdd->query($ordreSQL);
				$journees = $req->fetchAll();
				$nombreJournees = count($journees);
				if($nombreJournees) {
					echo '<div>';
						echo '<label class="texteJournee">Journées :&nbsp;</label>';
						$indiceJournee = 0;
						foreach($journees as $uneJournee) {
							if($indiceJournee++ < $nombreJournees - 1)
								echo '<label class="lienJournee" onclick="consulterTrophees_afficherJournee(' . $championnat . ', ' . $uneJournee["Journee"] . ', \'divConsulterTropheesTableaux\');">' . $uneJournee["Journees_Nom"] . '</label><label class="texteJournee"> - </label>';
							else
								echo '<label class="lienJournee" onclick="consulterTrophees_afficherJournee(' . $championnat . ', ' . $uneJournee["Journee"] . ', \'divConsulterTropheesTableaux\');">' . $uneJournee["Journees_Nom"] . '</label>';
						}
					echo '</div>';
					
					echo '<div id="divConsulterTropheesTableaux" class="unChampionnat">';
						afficherTrophees($bdd, $championnat, $journee, $dtDateMAJ, $journeeNom);
					echo '</div>';
				}
			}
		echo '</div>';
		//include_once('pied.php');
	echo '</div>';
?>

	<script>
		$(function() {
			afficherTitrePage('.contenu-page', 'Trophées');
		});
	</script>


</body>
</html>