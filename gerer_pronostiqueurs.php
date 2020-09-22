<?php
	include_once('commun_administrateur.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include_once('commun_entete.php');
?>
</head>


<body>
	<?php
		$nomPage = 'gerer_pronostiqueurs.php';
		include_once('bandeau.php');

		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';

		// Page de gestion des pronostiqueurs
		// Seuls la Ligue 1, la Ligue des Champions, l'Europa League et les barrages sont concernés

		echo '<div id="divGestionPronostiqueurs" class="contenu-page">';

      // Création d'un nouveau pronostiqueur
      echo '<h2>Création d\'un nouveau pronostiqueur</h2>';
      echo '<table>';
        echo '<thead>';
          echo '<tr>';
            echo '<th>Nom utilisateur</th>';
            echo '<th>Prénom</th>';
            echo '<th>Nom de famille</th>';
            echo '<th>Mot de passe</th>';
            echo '<th>Création</th>';
          echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
          echo '<tr>';
            echo '<td><input type="text" id="txtNomUtilisateur" placeholder="Nom utilisateur" onblur="gererPronostiqueurs_verifierExistence();"></td>';
            echo '<td><input type="text" id="txtPrenom" placeholder="Prénom"></td>';
            echo '<td><input type="text" id="txtNomFamille" placeholder="Nom de famille"></td>';
            echo '<td><input type="text" id="txtMotDePasse" placeholder="Mot de passe"></td>';
            echo '<td><label class="bouton" onclick="gererPronostiqueur_creerPronostiqueur();">Créer</label></td>';
          echo '</tr>';
        echo '</tbody>';
      echo '</table>';

      echo '<br />';
      echo '<hr />';
      echo '<h2>Pronostiqueurs actuels</h2>';

			// Affichage de tous les pronostiqueurs inscrits dans la saison en cours
			$ordreSQL =		'	SELECT		Pronostiqueur, Pronostiqueurs_NomUtilisateur, Pronostiqueurs_Nom, Pronostiqueurs_Prenom' .
							'	FROM		pronostiqueurs' .
							'	ORDER BY	Pronostiqueurs_NomUtilisateur';
			$req = $bdd->query($ordreSQL);
			$pronostiqueurs = $req->fetchAll();

			if(count($pronostiqueurs) == 0) {
				echo '<label>Aucun pronostiqueur présent</label>';
			}
			else {
				// Lecture de tous les pronostiqueurs et des championnats existants pour savoir ceux auxquels le pronostiqueur est inscrit
				$ordreSQL =		'	SELECT		pronostiqueurs.Pronostiqueur, Championnat' .
								'				,CASE' .
								'					WHEN	inscriptions.Championnats_Championnat IS NOT NULL' .
								'					THEN	1' .
								'					ELSE	0' .
								'				END AS Pronostiqueurs_Championnats' .
								'	FROM		(' .
								'					SELECT		DISTINCT Pronostiqueur, Championnat, Championnats_Nom ' .
								'					FROM		pronostiqueurs' .
								'					CROSS JOIN	championnats' .
								'					WHERE		Championnat <> 5 AND Championnat <> 1' .
								'				) pronostiqueurs_championnats' .
								'	CROSS JOIN	pronostiqueurs' .
								'				ON		pronostiqueurs_championnats.Pronostiqueur = pronostiqueurs.Pronostiqueur' .
								'	LEFT JOIN	inscriptions' .
								'				ON		pronostiqueurs_championnats.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
								'						AND		pronostiqueurs_championnats.Championnat = inscriptions.Championnats_Championnat' .
								'	ORDER BY	Pronostiqueurs_NomUtilisateur, Championnat';

				$req = $bdd->query($ordreSQL);
				$inscriptions = $req->fetchAll();

				echo '<h1 style="color: #f00 !important;">ATTENTION, TOUTE DESINSCRIPTION EFFACE AUSSI LES PRONOSTICS !!!</h1>';
				echo '<table class="tableau--liste">';
					echo '<thead>';
						echo '<tr>';
              				echo '<th>Suppression</th>';
							echo '<th>Identifiant</th>';
							echo '<th>Login</th>';
							echo '<th>Prénom Nom</th>';
							echo '<th>Ligue des Champions</th>';
							echo '<th>Europa League</th>';
							echo '<th>Barrages</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
						$nombreChampionnats = 3;
						$i = 0;
						foreach($pronostiqueurs as $unPronostiqueur) {
							echo '<tr>';
                				echo '<td class="aligne-centre curseur-main" style="color: #f00 !important;" onclick="gererPronostiqueur_effacerPronostiqueur(' . $unPronostiqueur["Pronostiqueur"] . ', 1);">Supprimer !</td>';
								echo '<td class="aligne-centre">' . $unPronostiqueur["Pronostiqueur"] . '</td>';
								echo '<td>' . $unPronostiqueur["Pronostiqueurs_NomUtilisateur"] . '</td>';
								echo '<td>' . $unPronostiqueur["Pronostiqueurs_Prenom"] . ' ' . $unPronostiqueur["Pronostiqueurs_Nom"] . '</td>';
								for($j = 0; $j < $nombreChampionnats; $j++) {
									$estInscrit = $inscriptions[$i * $nombreChampionnats + $j]["Pronostiqueurs_Championnats"] == 1 ? ' checked' : '';
									$championnat = $inscriptions[$i * $nombreChampionnats + $j]["Championnat"];
									echo '<td class="aligne-centre"><input type="checkbox" id="pc_' . $unPronostiqueur["Pronostiqueur"] . '_' . $championnat . '" onclick="gererPronostiqueur_modifierInscription($(this), ' . $unPronostiqueur["Pronostiqueur"] . ', ' . $championnat . ');" ' . $estInscrit . '/></td>';
								}

							echo '</tr>';
							$i++;
						}
					echo '</tbody>';
				echo '</table>';
			}

      echo '<br />';
      echo '<hr />';
      echo '<h2>Pronostiqueurs anciens</h2>';

			// Affichage de tous les pronostiqueurs anciens
			$ordreSQL =		'	SELECT		Pronostiqueur, Pronostiqueurs_NomUtilisateur' .
                    '	FROM		pronostiqueurs_anciens' .
                    '	ORDER BY	Pronostiqueurs_NomUtilisateur';
			$req = $bdd->query($ordreSQL);
			$pronostiqueursAnciens = $req->fetchAll();

			if(count($pronostiqueursAnciens) == 0) {
				echo '<label>Aucun pronostiqueur ancien présent</label>';
			}
			else {
				echo '<table class="tableau--liste">';
					echo '<thead>';
						echo '<tr>';
              echo '<th>Réinscription</th>';
							echo '<th>Identifiant</th>';
							echo '<th>Login</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
						foreach($pronostiqueursAnciens as $unPronostiqueurAncien) {
							echo '<tr>';
                echo '<td class="aligne-centre curseur-main" style="color: #f00 !important;" onclick="gererPronostiqueurs_reinscrirePronostiqueur(\'' . $unPronostiqueurAncien["Pronostiqueurs_NomUtilisateur"] . '\');">Réinscrire</td>';
								echo '<td class="aligne-centre">' . $unPronostiqueurAncien["Pronostiqueur"] . '</td>';
								echo '<td>' . $unPronostiqueurAncien["Pronostiqueurs_NomUtilisateur"] . '</td>';
							echo '</tr>';
						}
					echo '</tbody>';
				echo '</table>';
			}

		echo '</div>';

	?>

	<script>
		$(function() {
			afficherTitrePage('divGestionPronostiqueurs', 'Gestion des pronostiqueurs');
			retournerHautPage();
		});
	</script>

</body>
</html>