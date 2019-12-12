<?php
	// Module d'affichage des confrontations de la Coupe de France


	$rafraichissementModule = isset($_POST["rafraichissementModule"]) ? $_POST["rafraichissementModule"] : 0;
	if($rafraichissementModule == 1) {
		// Rafraîchissement automatique du module
		include_once('commun.php');

		// Lecture des paramètres passés à la page
		$championnat = isset($_POST["parametre"]) ? $_POST["parametre"] : 0;
	}
	else {
		$championnat = $parametre;		// Paramètre du module
	}

	// Parcours du championnat
	// Quelle est la journée complète ?
	$ordreSQL =		'	SELECT		fn_recherchejourneeencours(' . $championnat . ') AS Journee_EnCours';
	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetchAll();
	$journeeAffichee = $donnees[0]["Journee_EnCours"];

	// Nom de la journée
	$ordreSQL =		'	SELECT Journees_Nom FROM journees WHERE Journee = ' . $journeeAffichee;
	$req = $bdd->query($ordreSQL);
	$journees = $req->fetchAll();
	$journeeNom = $journees[0]["Journees_Nom"];

	// Affichage de la liste des confrontations de la journée
	$ordreSQL =		'	SELECT		Confrontation' .
					'				,confrontations.Pronostiqueurs_Vainqueur' .
					'				,confrontations.Pronostiqueurs_PronostiqueurA' .
					'				,confrontations.Pronostiqueurs_PronostiqueurB' .
					'				,confrontations.Confrontations_ScorePronostiqueurA' .
					'				,confrontations.Confrontations_ScorePronostiqueurB' .
					'				,confrontations.Confrontations_ScoreButeurPronostiqueurA' .
					'				,confrontations.Confrontations_ScoreButeurPronostiqueurB' .
					'				,pronostiqueursA.Pronostiqueurs_NomUtilisateur AS PronostiqueursA_NomUtilisateur' .
					'				,pronostiqueursB.Pronostiqueurs_NomUtilisateur AS PronostiqueursB_NomUtilisateur' .
					'	FROM		confrontations' .
					'	JOIN		pronostiqueurs pronostiqueursA' .
					'				ON		confrontations.Pronostiqueurs_PronostiqueurA = pronostiqueursA.Pronostiqueur' .
					'	JOIN		pronostiqueurs pronostiqueursB' .
					'				ON		confrontations.Pronostiqueurs_PronostiqueurB = pronostiqueursB.Pronostiqueur' .
					'	WHERE		confrontations.Journees_Journee = ' . $journeeAffichee .
					'				AND		confrontations.Confrontations_ConfrontationReelle = 1';
	$req = $bdd->query($ordreSQL);
	$confrontations = $req->fetchAll();
	$nombreConfrontations = sizeof($confrontations);

	if($nombreConfrontations) {
		echo '<table class="tableau--classement">';
			echo '<thead>';
				echo '<tr>';
					echo '<th colspan="5"><b>' . $journeeNom . '</b></th>';
				echo '</tr>';
				echo '<tr class="tableau--classement-nom-colonnes">';
					echo '<th class="aligne-droite">Joueur A</th>';
					echo '<th>Score</th>';
					echo '<th>VS</th>';
					echo '<th>Score</th>';
					echo '<th>Joueur B</th>';
				echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
				for($i = 0; $i < $nombreConfrontations; $i++) {
					echo '<tr class="curseur-main" onclick="cdf_afficherConfrontation(' . $confrontations[$i]["Confrontation"] . ');">';
						if($confrontations[$i]["Pronostiqueurs_Vainqueur"] == $confrontations[$i]["Pronostiqueurs_PronostiqueurA"])
							$classe = 'vert';
						else
							$classe = '';
						echo '<td class="' . $classe . ' aligne-droite">' . $confrontations[$i]["PronostiqueursA_NomUtilisateur"] . '</td>';
						echo '<td class="' . $classe . '" title="' . $confrontations[$i]["Confrontations_ScorePronostiqueurA"] . ' (' . $confrontations[$i]["Confrontations_ScoreButeurPronostiqueurA"] . ')">' . $confrontations[$i]["Confrontations_ScorePronostiqueurA"] . '</td>';
						echo '<td>-</td>';
						if($confrontations[$i]["Pronostiqueurs_Vainqueur"] == $confrontations[$i]["Pronostiqueurs_PronostiqueurB"])
							$classe = 'vert';
						else
							$classe = '';
						echo '<td class="' . $classe . '" title="' . $confrontations[$i]["Confrontations_ScorePronostiqueurB"] . ' (' . $confrontations[$i]["Confrontations_ScoreButeurPronostiqueurB"] . ')">' . $confrontations[$i]["Confrontations_ScorePronostiqueurB"] . '</td>';
						echo '<td class="' . $classe . ' aligne-gauche">' . $confrontations[$i]["PronostiqueursB_NomUtilisateur"] . '</td>';
					echo '</tr>';
				}
			echo '</tbody>';
		echo '</table>';
	}

?>