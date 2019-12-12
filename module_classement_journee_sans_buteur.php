<?php
	// Module d'affichage du classement d'une journée

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

	$ordreSQL =		'	SELECT		MIN(Journee) AS Journee' .
					'	FROM		journees' .
					'	WHERE		Championnats_Championnat = ' . $championnat;
	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetchAll();
	$journeeMin = $donnees[0]["Journee"];


	// Parcours de la Ligue 1
	// Quelle est la dernière journée complète et quelle est la journée en cours de L1 ?
	$ordreSQL = lireJournee($championnat);
	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetch();
	$journee = $donnees["Journee"];
	$dateReference = $donnees["Classements_DateReference"];
	$journeeNom = $donnees["Journees_Nom"];
	$dateMAJJournee = $donnees["Journees_DateMAJ"];
	$dtDateMAJ = new DateTime($dateMAJJournee);
	$req->closeCursor();

	$modeModule = 1;
	$sansButeur = 1;

	echo '<div style="margin-left: 8px;">';
		afficherClassementJournee($bdd, $championnat, $journee, $dateReference, $dtDateMAJ, $journeeNom, $modeModule, $modeRival, $modeConcurrentDirect, $sansButeur);
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
