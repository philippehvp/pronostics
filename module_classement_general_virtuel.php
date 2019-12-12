<?php
	// Module d'affichage des classements virtuels

	// Le module peut être appelé de deux manières :
	// - par une inclusion
	// - par un appel Ajax (cas du rafraîchissement)

	include_once('classements_pronostiqueurs_fonctions.php');

	$rafraichissementModule = isset($_POST["rafraichissementModule"]) ? $_POST["rafraichissementModule"] : 0;
	if($rafraichissementModule == 1) {
		// Rafraîchissement automatique du module
		include_once('commun.php');

		// Lecture des paramètres passés à la page
		$championnat = isset($_POST["parametre"]) ? $_POST["parametre"] : 0;
		$modeRival = isset($_POST["modeRival"]) ? $_POST["modeRival"] : 0;
		$modeConcurrentDirect = isset($_POST["modeConcurrentDirect"]) ? $_POST["modeConcurrentDirect"] : 0;
	}
	else {
		$championnat = $parametre;		// Paramètre du module
	}

	echo '<div style="margin-left: 8px;">';
		afficherClassementGeneralVirtuel($bdd, $championnat, $modeRival, $modeConcurrentDirect);
	echo '</div>';
?>


<script>
	$(function() {
		// Se placer sur le pronostiqueur connecté
		var nomConteneurComplet = '<?php if(isset($nomConteneurComplet)) echo '#' . $nomConteneurComplet; else echo (isset($_POST["nomConteneurComplet"]) ? $_POST["nomConteneurComplet"] : '');?>';
		if(nomConteneurComplet != '') {
			var coordonnees = $(nomConteneurComplet).find('td.surbrillance:first').position();
			var hauteurASoustraire = $(nomConteneurComplet).find('.module--contenu table tbody').position();
			if(coordonnees != null)
				$(nomConteneurComplet).find('.module--contenu').animate({scrollTop: (coordonnees.top - hauteurASoustraire.top)}, 500);
		}
	});
</script>
