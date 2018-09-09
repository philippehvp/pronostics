<?php
	// Module d'affichage des classements sans les points buteur
	
	// Le module peut être appelé de deux manières :
	// - par une inclusion
	// - par un appel Ajax (cas du rafraîchissement)
	
	include_once('classements_pronostiqueurs_fonctions.php');
	
	$rafraichissementModule = isset($_POST["rafraichissementModule"]) ? $_POST["rafraichissementModule"] : 0;
	if($rafraichissementModule == 1) {
		// Rafraîchissement automatique du module
		include('commun.php');
		
		// Lecture des paramètres passés à la page
		$championnat = isset($_POST["parametre"]) ? $_POST["parametre"] : 0;
		$modeRival = isset($_POST["modeRival"]) ? $_POST["modeRival"] : 0;
		$modeConcurrentDirect = isset($_POST["modeConcurrentDirect"]) ? $_POST["modeConcurrentDirect"] : 0;
	}
	else {
		$championnat = $parametre;		// Paramètre du module
	}

	// Parcours du championnat
	// Quelle est la dernière journée complète et quelle est la journée en cours ?
	$ordreSQL = lireJournee($championnat);
	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetch();
	$journee = $donnees["Journee"];
	$dateReference = $donnees["Classements_DateReference"];
	$journeeNom = $donnees["Journees_Nom"];
	$dateMAJJournee = $donnees["Journees_DateMAJ"];
	$dtDateMAJ = new DateTime($dateMAJJournee);
	$req->closeCursor();
	
	// On détermine d'abord si la journée suivante est active ou non
	// Si c'est le cas, on affiche alors le nombre de pronostics déjà saisis par rapport au nombre de pronostics théoriques
	// Le nombre de pronostics théorique peut changer d'un pronostiqueur à l'autre (cas de la phase finale de la compétition européenne)
	$ordreSQL =		'	SELECT		Journees_Active' .
								'	FROM		journees' .
								'	WHERE		Journee = fn_recherchejourneesuivante(' . $championnat . ')';
	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetch();
	$journeeSuivanteActive = $donnees["Journees_Active"];
	$req->closeCursor();
	$modeModule = 1;
	$sansButeur = 1;
	
	echo '<div style="margin-left: 8px;">';
		afficherClassementGeneral($bdd, $championnat, $journee, $dateReference, $dtDateMAJ, $journeeNom, $journeeSuivanteActive, $modeModule, $modeRival, $modeConcurrentDirect, $sansButeur);
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
