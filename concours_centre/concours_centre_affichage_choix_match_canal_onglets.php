<?php
	include_once('../commun.php');

    // Lecture des paramètres passés à la page
    $largeur = isset($_POST["largeur"]) ? $_POST["largeur"] : 0;
	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;

	// Affichage des journées terminées du championnat
    $ordreSQL =		'	SELECT	    journees.Journee, journees.Journees_NumeroInterne' .
                    '   FROM        journees' .
                    '   WHERE       journees.Championnats_Championnat  = ' . $championnat .
					'               AND     journees.Journees_MatchCanalSelectionnable = 1' .
					'				AND		fn_journeeterminee(journees.Journee) = 1';
	$req = $bdd->query($ordreSQL);
    $journees = $req->fetchAll();

    $NOMBRE_JOURNEES_PAR_LIGNE = 23;

	// Affichage des onglets de sélection des journées du choix de match Canal
    echo '<div class="colle-gauche gauche cc--sous-onglets" style="margin-top: 5px;">';
    $indice = 0;
        foreach($journees as $uneJournee) {
            // Retour à la ligne toutes les n journées
            if($indice != 0 && $indice % $NOMBRE_JOURNEES_PAR_LIGNE == 0) {
                echo '<br />';
			}
            echo '<label class="cc--nom-sous-onglet" onclick="concoursCentre_afficherChoixMatchCanal(\'cc--conteneur-canal\', ' . $uneJournee["Journee"] . ');">J' . $uneJournee["Journees_NumeroInterne"] . '</label>';
            $indice++;
        }
	echo '</div>';

	echo '<div class="colle-gauche cc--conteneur-canal"></div>';

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