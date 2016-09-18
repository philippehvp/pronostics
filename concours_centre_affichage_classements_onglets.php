<?php
	include('commun.php');
	
	// Lecture des paramètres passés à la page
	$generalJournee = isset($_POST["generalJournee"]) ? $_POST["generalJournee"] : 1;
	
	// Affichage des onglets des championnats
	$ordreSQL =		'	SELECT	Championnat, UPPER(Championnats_Nom) AS Championnats_Nom' .
					'	FROM	championnats' .
					'	JOIN	inscriptions' .
					'			ON		championnats.Championnat = inscriptions.Championnats_Championnat' .
					'	WHERE	inscriptions.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'			AND		inscriptions.Championnats_Championnat NOT IN (4, 5)';
	$req = $bdd->query($ordreSQL);
	$championnats = $req->fetchAll();
	
	echo '<div class="colle-gauche gauche cc--sous-onglets" style="margin-top: 5px;">';
		$sousOnglet = 1;
		foreach($championnats as $unChampionnat) {
			echo '<label class="cc--nom-sous-onglet" onclick="concoursCentre_afficherClassementsChampionnat(' . $unChampionnat["Championnat"] . ', \'cc--conteneur-classements\', ' . $sousOnglet++ . ', ' . $generalJournee . ');">' . $unChampionnat["Championnats_Nom"] . '</label>';
		}
	echo '</div>';

	echo '<div class="colle-gauche cc--conteneur-classements"></div>';
	
?>

<script>
	$(function() {
		// Gestion du clic sur un sous-onglet pour que celui-ci apparaisse avec un style de surbrillance / sélection
		$('.cc--nom-sous-onglet').click(function (e) {
			// Si cet onglet n'était pas sélectionné, alors effectuer deux tâches :
			// - enlever le style sélectionné à l'ancien onglet s'il existe
			// - mettre le style à l'onglet cliqué
			if(!$(this).hasClass('cc--selectionne')) {
				$('.cc--nom-sous-onglet').removeClass('cc--selectionne');
				$(this).addClass('cc--selectionne');
			}
		});
		
	});

</script>