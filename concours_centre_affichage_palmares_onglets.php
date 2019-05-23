<?php
	include_once('commun.php');
	
	// Affichage des onglets des championnats
	echo '<div class="colle-gauche gauche cc--sous-onglets" style="margin-top: 5px;">';
		echo '<label class="cc--nom-sous-onglet" onclick="concoursCentre_afficherPalmaresChampionnat(1, \'cc--conteneur-palmares\', 1);">LIGUE 1</label>';
		echo '<label class="cc--nom-sous-onglet" onclick="concoursCentre_afficherPalmaresChampionnat(2, \'cc--conteneur-palmares\', 2);">LIGUE DES CHAMPIONS</label>';
		echo '<label class="cc--nom-sous-onglet" onclick="concoursCentre_afficherPalmaresChampionnat(3, \'cc--conteneur-palmares\', 3);">EUROPA LEAGUE</label>';
	echo '</div>';

	echo '<div class="colle-gauche cc--conteneur-palmares"></div>';
	
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