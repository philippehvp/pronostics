<?php
	// Module d'affichage du classement des poules

	// Le module peut être appelé de deux manières :
	// - par une inclusion
	// - par un appel Ajax (cas du rafraîchissement)

	include_once('module_classement_poule_fonctions.php');

	$rafraichissementModule = isset($_POST["rafraichissementModule"]) ? $_POST["rafraichissementModule"] : 0;
	if($rafraichissementModule == 1) {
		// Rafraîchissement automatique du module
		include_once('commun.php');
		
		// Lecture des paramètres passés à la page
		$championnat = isset($_POST["parametre"]) ? $_POST["parametre"] : 0;
		$modeRival = isset($_POST["modeRival"]) ? $_POST["modeRival"] : 0;
	}
	else {
		$championnat = $parametre;		// Paramètre du module
	}
	
	afficherClassementPoule($bdd, $championnat, $modeRival, $modeConcurrentDirect);
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
