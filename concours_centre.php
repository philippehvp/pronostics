<?php
	include_once('commun.php');
?>

	<script type="text/javascript" src="js/datatables/jquery.dataTables.js"></script>
	<script type="text/javascript" src="js/datatables/extensions/dataTables.fixedColumns.min.js"></script>


<?php
	$nomPage = 'concours_centre.php';
	
	echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
	
	echo '<div class="cc--contenu">';
		echo '<div>';
			echo '<label class="cc--titre gauche">CONTEST CENTRE</label>';
			echo '<img class="droite curseur-main" src="images/concours_centre_fleche.png" alt="" onclick="concoursCentre_masquerConcoursCentre();" style="margin-top: 5px; padding-right: 5px;" />';
		echo '</div>';
		
		echo '<div class="colle-gauche gauche cc--onglets">';
			echo '<label class="cc--nom-onglet" onclick="concoursCentre_afficherPronostiqueurs(\'cc--contenu-interieur\', 1, \'cc--pronostiqueurs-entete\', \'cc--pronostiqueurs-detail\', ' . $_SESSION["pronostiqueur"] . ', 1);">PRONOSTIQUEURS</label>';
			echo '<label class="cc--nom-onglet" onclick="concoursCentre_afficherStatistiquesButeur(\'cc--contenu-interieur\', 2);">BUTEURS</label>';
			echo '<label class="cc--nom-onglet" onclick="concoursCentre_afficherPalmares(\'cc--contenu-interieur\', 3);">PALMARES</label>';
			echo '<label class="cc--nom-onglet" onclick="concoursCentre_afficherRepartitionPoints(\'cc--contenu-interieur\', 4);">POINTS</label>';
			echo '<label class="cc--nom-onglet" onclick="concoursCentre_afficherStatistiquesLigue1(\'cc--contenu-interieur\', 5);">LIGUE 1</label>';
			echo '<label class="cc--nom-onglet" onclick="concoursCentre_afficherClassements(\'cc--contenu-interieur\', 6, 1);">CLT GENERAL</label>';
			echo '<label class="cc--nom-onglet" onclick="concoursCentre_afficherClassements(\'cc--contenu-interieur\', 7, 2);">CLT JOURNEE</label>';
			echo '<label class="cc--nom-onglet" onclick="concoursCentre_afficherOngletEquipes(\'cc--contenu-interieur\', 8);">EQUIPES</label>';
		echo '</div>';
		
		echo '<div class="colle-gauche gauche cc--contenu-interieur cc--contenu-interieur-initial"></div>';

	echo '</div>';

?>

<script>
	$(function() {
		activitePronostiqueur();
		
		// Gestion du clic sur un onglet pour que celui-ci apparaisse avec un style de surbrillance / sélection
		$('.cc--nom-onglet').click(function (e) {
		
			// Si cet onglet n'était pas sélectionné, alors effectuer deux tâches :
			// - enlever le style sélectionné à l'ancien onglet s'il existe
			// - mettre le style à l'onglet cliqué
			if(!$(this).hasClass('cc--selectionne')) {
				$('.cc--nom-onglet').removeClass('cc--selectionne');
				$(this).addClass('cc--selectionne');
			}
		});
	});

</script>
	
