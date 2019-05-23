<?php

	// La page peut être appelée de deux manières :
	// - par une inclusion
	// - par un appel Ajax (cas du rafraîchissement)

	$rafraichissementSection = isset($_POST["rafraichissementSection"]) ? $_POST["rafraichissementSection"] : 0;
	if($rafraichissementSection == 1) {
		// Rafraîchissement automatique de la section
		include_once('commun.php');
		
		// Lecture des paramètres passés à la page
		$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;
	}

	$ordreSQL =		'	SELECT		Pronostiqueur, Pronostiqueurs_NomUtilisateur, IFNULL(Pronostiqueurs_Photo, \'_inconnu.png\') AS Pronostiqueurs_Photo' .
					'				,classements.Classements_ClassementJourneeMatch' .
					'				,classements.Classements_PointsJourneeMatch, classements.Classements_PointsJourneeButeur' .
					'	FROM		classements' .
					'	JOIN		pronostiqueurs' .
					'				ON		classements.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'	WHERE		classements.Journees_Journee = ' . $journee .
					'	ORDER BY	classements.Classements_ClassementJourneeMatch';
	$req = $bdd->query($ordreSQL);
	$classements = $req->fetchAll();
	$nombreClassements = sizeof($classements);

	// Affichage des données en ligne
	if($nombreClassements) {
		$LIGNES_PAR_COLONNE = 6;
		$nombreColonnes = floor($nombreClassements / $LIGNES_PAR_COLONNE);
		if($nombreClassements % $LIGNES_PAR_COLONNE > 0)
			$nombreColonnes++;
		
		for($i = 0; $i < $nombreColonnes; $i++) {
			if($i == 0)							echo '<div class="premiere-colonne">';
			else								echo '<div class="colonne-suivante">';

				echo '<table class="mc--tableau-classements">';
					echo '<tbody>';
						for($j = 0; $j < $LIGNES_PAR_COLONNE; $j++) {
							if($i * $LIGNES_PAR_COLONNE + $j >= $nombreClassements)
								echo '<tr><td colspan="4">&nbsp;</td></tr>';
							else {
								if($classements[$i * $LIGNES_PAR_COLONNE + $j]["Pronostiqueur"] == $_SESSION["pronostiqueur"])			echo '<tr class="surbrillance" onclick="consulterResultats_afficherPronostiqueur(' . $classements[$i * $LIGNES_PAR_COLONNE + $j]["Pronostiqueur"] . ', \'' . $classements[$i * $LIGNES_PAR_COLONNE + $j]["Pronostiqueurs_NomUtilisateur"] . '\', ' . $journee . ');">';
								else																									echo '<tr onclick="consulterResultats_afficherPronostiqueur(' . $classements[$i * $LIGNES_PAR_COLONNE + $j]["Pronostiqueur"] . ', \'' . $classements[$i * $LIGNES_PAR_COLONNE + $j]["Pronostiqueurs_NomUtilisateur"] . '\', ' . $journee . ');">';
									echo '<td class="bordure-basse-legere"><img class="photo" src="images/pronostiqueurs/' . $classements[$i * $LIGNES_PAR_COLONNE + $j]["Pronostiqueurs_Photo"] . '" alt="" /></td>';
									echo '<td class="bordure-basse-legere">' . $classements[$i * $LIGNES_PAR_COLONNE + $j]["Classements_ClassementJourneeMatch"] . '</td>';
									echo '<td class="aligne-gauche bordure-basse-legere">' . $classements[$i * $LIGNES_PAR_COLONNE + $j]["Pronostiqueurs_NomUtilisateur"] . '</td>';
									echo '<td class="aligne-gauche bordure-basse-legere">' . $classements[$i * $LIGNES_PAR_COLONNE + $j]["Classements_PointsJourneeMatch"] . ' (' . $classements[$i * $LIGNES_PAR_COLONNE + $j]["Classements_PointsJourneeButeur"] . ')</td>';
								echo '</tr>';
							}
						}
					echo '</tbody>';
				echo '</table>';
			echo '</div>';
		}
	}
?>
