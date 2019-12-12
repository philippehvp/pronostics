<?php
	include_once('commun.php');

	echo '<link rel="stylesheet" href="css/jquery.jscrollpane/jquery.jscrollpane.css" />';


	echo '<script src="js/d3/d3.min.js"></script>';
	echo '<script src="js/d3/d3.tip.js"></script>';


	$nomPage = 'match_centre.php';

	echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';

	// Championnats du pronostiqueur ainsi que l'autre championnat européen
	$ordreSQL =		'	SELECT		DISTINCT Championnat, Championnats_Nom' .
					'	FROM		championnats' .
					'	WHERE		Championnat NOT IN (4, 5)';
	$req = $bdd->query($ordreSQL);
	$championnats = $req->fetchAll();

	echo '<div class="mc--contenu">';
		echo '<div>';
			echo '<label class="mc--titre gauche" style="margin-right: 90px;">MATCH CENTRE</label>';
			echo '<ul><li class="menu--lien rouge" id="mc--bulle-tchat" style="display: none;"><span class="bulle-tchat"></span></li></ul>';
			echo '<img class="droite curseur-main" src="images/match_centre_fleche.png" alt="" onclick="masquerMatchCentre();" style="margin-top: 5px; margin-right: 5px;" />';
		echo '</div>';

		echo '<div class="colle-gauche gauche mc--onglets">';
			foreach($championnats as $unChampionnat) {
				echo '<label class="mc--nom-onglet" onclick="matchCentre_afficherChampionnat(\'mc--contenu-interieur\', ' . $unChampionnat["Championnat"] . ', ' . $_SESSION["pronostiqueur"] . ');">' . $unChampionnat["Championnats_Nom"] . '</label>';
			}
		echo '</div>';

		echo '<div class="colle-gauche gauche mc--contenu-interieur"></div>';

	echo '</div>';

	// Gestion des timers de rafraîchissement
	echo '<input type="hidden" name="minuteur_journee" value="0" />';
	echo '<input type="hidden" name="minuteur_match" value="0" />';

	// Match sélectionné par le joueur (sauvegarde de ce numéro de match pour le remettre en sélectionné lors d'un rafraîchissement)
	echo '<input type="hidden" name="matchSelectionne" value="0" />';

	// Les informations de dernière date de mise à jour du match et de la journée sont sauvegardées dans la page pour être
	// comparées par la suite par la fonction de rafraîchissement
	echo '<input type="hidden" name="date_maj_match" value="0" />';
	echo '<input type="hidden" name="dateEvenementJournee" value="0" />';
	echo '<input type="hidden" name="dateMAJJournee" value="0" />';

?>

<script>
	// Lors de la fermeture du Match centre, il est nécessaire d'arrêter les timers de rafraîchissement s'ils existent
	function masquerMatchCentre() {
		// Suppression d'éventuels timers de rafraîchissement
		var intervalle = $('input[name="minuteur_journee"]').val();
		if(intervalle) {
			clearInterval(intervalle);
		}

		intervalle = $('input[name="minuteur_match"]').val();
		if(intervalle) {
			clearInterval(intervalle);
		}

		matchCentre_masquerMatchCentre();
	}


	$(function() {
		// Gestion du clic sur un onglet pour que celui-ci apparaisse avec un style de surbrillance / sélection
		$('.mc--nom-onglet').click(function (e) {

			// Si cet onglet n'était pas sélectionné, alors effectuer deux tâches :
			// - enlever le style sélectionné à l'ancien onglet s'il existe
			// - mettre le style à l'onglet cliqué
			if(!$(this).hasClass('mc--selectionne')) {
				$('.mc--nom-onglet').removeClass('mc--selectionne');
				$(this).addClass('mc--selectionne');
			}
		});
	});

</script>

