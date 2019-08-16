<?php
	include_once('../commun.php');
	
	// Affichage des onglets des championnats
	echo '<div class="colle-gauche gauche cc--sous-onglets" style="margin-top: 5px;">';
		echo '<label class="cc--nom-sous-onglet" onclick="concoursCentre_afficherVictoiresNulsDefaites(\'cc--conteneur-statistiques-l1\', 1);">VICTOIRES A DOMICILE/ NULS / DEFAITES A DOMICILE</label>';
		echo '<label class="cc--nom-sous-onglet" onclick="concoursCentre_afficherPourcentagePoints(\'cc--conteneur-statistiques-l1\', 2);">RATIO DE POINTS</label>';
		echo '<label class="cc--nom-sous-onglet" onclick="concoursCentre_afficherPointsParEquipe(\'cc--conteneur-statistiques-l1\', 3);">POINTS PAR EQUIPE</label>';
		echo '<label class="cc--nom-sous-onglet" onclick="concoursCentre_afficherMeilleuresEquipes(\'cc--conteneur-statistiques-l1\', 4);">MEILLEURES EQUIPES</label>';
		echo '<label class="cc--nom-sous-onglet" onclick="concoursCentre_afficherMatchCanal(\'cc--conteneur-statistiques-l1\', 5);">MATCH CANAL</label>';
	echo '</div>';

	echo '<div class="colle-gauche cc--conteneur-statistiques-l1"></div>';
	
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