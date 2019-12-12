<?php
	echo '<div class="separation-pied"></div>';

	echo '<div class="pied">';
		echo '<div class="largeur-pied">';

			echo '<div class="colle-gauche gauche sections section1">';
				echo '<div class="logo">';
					echo '<img src="images/logo_poulpe_symbole.png" title="" alt="" />';
				echo '</div>';
			echo '</div>';

			echo '<div class="gauche sections section2">';
				echo '<ul class="pied--navigation">';
					echo '<li>NAVIGATION</li>';
					echo '<li><a href="creer_prono.php">Pronostiquer</a></li>';
					echo '<li><a href="classements_pronostiqueurs.php">Classements</a></li>';
					echo '<li>Résultats de <a href="consulter_resultats.php?championnat=1">Ligue 1</a> - <a href="consulter_resultats.php?championnat=2">LDC</a> - <a href="consulter_resultats.php?championnat=3">EL</a></li>';
					echo '<li>Trophées de <a href="consulter_trophees.php?championnat=1">Ligue 1</a> - <a href="consulter_trophees.php?championnat=2">LDC</a> - <a href="consulter_trophees.php?championnat=3">EL</a></li>';
				echo '</ul>';
			echo '</div>';

			echo '<div class="gauche sections section3">';

			echo '</div>';


			echo '<div class="gauche sections section4">';
				echo '<img src="images/pied_fleche.png" title="Haut de page" alt="Haut de page" onclick="remonterHautPage();" />';
			echo '</div>';


		echo '</div>';
	echo '</div>';
?>

<script>
	$(function() {
		// Cumul de tous les padding gauche et droite des sections
		var totalPadding = 0;
		$('.sections').each(function() {
			totalPadding += parseInt($(this).css('padding-left')) + parseInt($(this).css('padding-right'));
		});

		// Lecture de la largeur de la partie utilisable du pied de page
		var largeur = $('.largeur-pied').width() - totalPadding;

		// Une fois cette largeur connue, on va diviser le pied de page en plusieurs parties (4 actuellement) :
		// - logo 30%
		// - navigation sur le site 30%
		// - mentions légales 30%
		// - retour vers le haut 10%


		var largeurSection1 = largeur * 0.15;
		var largeurSection2 = largeur * 0.4;
		var largeurSection3 = largeur * 0.4;
		var largeurSection4 = largeur * 0.05;

		$('.section1').width(largeurSection1);
		$('.section2').width(largeurSection2);
		$('.section3').width(largeurSection3);
		$('.section4').width(largeurSection4);


	});

	function remonterHautPage() {
		$('html, body').animate({scrollTop : 0}, 500);
	}

</script>

