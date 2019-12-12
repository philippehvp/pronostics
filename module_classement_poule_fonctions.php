<?php
	// Module d'affichage du classement des poules

	// Le module peut être appelé de deux manières :
	// - par une inclusion
	// - par un appel Ajax (cas du rafraîchissement)

	// Fonction d'affichage du classement des poules
	function afficherClassementPoule($bdd, $championnat, $modeRival, $modeConcurrentDirect) {
		// Affichage de tous les pronostiqueurs et les points récoltés par groupe
		$ordreSQL =		'	SELECT		Pronostiqueur, Pronostiqueurs_NomUtilisateur, IFNULL(PronosticsQualificationsPoints_Points, 0) AS PronosticsQualificationsPoints_Points' .
						'				,(' .
						'					SELECT		IFNULL(SUM(PronosticsQualificationsPoints_Points), 0)' .
						'					FROM		pronostics_qualificationspoints table_locale' .
						'					WHERE		table_locale.Pronostiqueurs_Pronostiqueur = pronostics_qualificationspoints.Pronostiqueurs_Pronostiqueur' .
						'					GROUP BY	table_locale.Pronostiqueurs_Pronostiqueur' .
						'				) AS Total_Points' .
						'	FROM';

		if($modeRival == 1)
			$ordreSQL .=	'				(' .
							'					SELECT		PronostiqueursRivaux_Pronostiqueur' .
							'					FROM		vue_pronostiqueursrivaux' .
							'					WHERE		vue_pronostiqueursrivaux.Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
							'					UNION ALL' .
							'					SELECT		' . $_SESSION["pronostiqueur"] . ' AS PronostiqueursRivaux_Pronostiqueur' .
							'				) vue_pronostiqueursrivaux' .
							'	JOIN		pronostiqueurs' .
							'				ON		vue_pronostiqueursrivaux.PronostiqueursRivaux_Pronostiqueur = pronostiqueurs.Pronostiqueur';
		else
			$ordreSQL .=	'			pronostiqueurs';

		$ordreSQL .=	'	JOIN		pronostics_qualificationspoints' .
						'				ON		pronostiqueurs.Pronostiqueur = pronostics_qualificationspoints.Pronostiqueurs_Pronostiqueur' .
						'	WHERE		pronostics_qualificationspoints.Championnats_Championnat = ' . $championnat .
						'	ORDER BY	Total_Points DESC, pronostiqueurs.Pronostiqueur ASC, pronostics_qualificationspoints.Groupes_Groupe ASC';


		$req = $bdd->query($ordreSQL);
		$classements = $req->fetchAll();

		if(sizeof($classements) > 0) {
			if($championnat == 2)
				$nombrePoules = 8;
			else if($championnat == 3)
				$nombrePoules = 12;
			else
				$nombrePoules = 1;

			$nombrePronostiqueurs = sizeof($classements) / $nombrePoules;

			echo '<table class="tableau--classement">';
				echo '<thead>';
					echo '<tr class="tableau--classement-nom-colonnes">';
						echo '<th>Joueur</th>';
						for($i = 0; $i < $nombrePoules; $i++)
							echo '<th>' . chr($i + 65) . '</th>';
						echo '<th>Total</th>';
					echo '</tr>';
				echo '</thead>';

				echo '<tbody>';
					for($j = 0; $j < $nombrePronostiqueurs; $j++) {
						echo '<tr>';
							if($_SESSION["pronostiqueur"] == $classements[$j * $nombrePoules]["Pronostiqueur"])
								echo '<td class="surbrillance">' . $classements[$j * $nombrePoules]["Pronostiqueurs_NomUtilisateur"] . '</td>';
							else
								echo '<td>' . $classements[$j * $nombrePoules]["Pronostiqueurs_NomUtilisateur"] . '</td>';

							for($i = 0; $i < $nombrePoules; $i++) {
								if($_SESSION["pronostiqueur"] == $classements[$j * $nombrePoules + $i]["Pronostiqueur"])
									echo '<td class="surbrillance">' . $classements[$j * $nombrePoules + $i]["PronosticsQualificationsPoints_Points"] . '</td>';
								else
									echo '<td>' . $classements[$j * $nombrePoules + $i]["PronosticsQualificationsPoints_Points"] . '</td>';
							}

							if($_SESSION["pronostiqueur"] == $classements[$j * $nombrePoules]["Pronostiqueur"])
								echo '<td class="surbrillance">' . $classements[$j * $nombrePoules]["Total_Points"] . '</td>';
							else
								echo '<td>' . $classements[$j * $nombrePoules]["Total_Points"] . '</td>';

						echo '</tr>';
					}
				echo '</tbody>';

			echo '</table>';
		}
	}

