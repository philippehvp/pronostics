<?php
	include('commun_administrateur.php');
?>
<!--!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"-->
<html>
<head>
<?php
	include('commun_entete.php');
?>
	<link rel="stylesheet" href="css/cdm/recap.css" />
</head>

<body>
	<?php
		$nomPage = 'envoyer_courrier_prono.php';

		// Nom du pronostiqueur consulté
		$ordreSQL =		'	SELECT		Pronostiqueurs_Nom' .
						'	FROM		cdm_pronostiqueurs' .
						'	WHERE		Pronostiqueur = ' . $_SESSION["pronostiqueur"];
						
		$req = $bdd->query($ordreSQL);
		$donnees = $req->fetch();
		$nomPronostiqueur = $donnees["Pronostiqueurs_Nom"];
		$req->closeCursor();
		
		include ('envoyer_courrier_prono_affichage_tableau.php');
		
		// Affichage du match entre deux équipes
		function afficherMatch($poule, $match, $equipeA, $equipeANom, $equipeAFanion, $equipeB, $equipeBNom, $equipeBFanion, $scoreEquipeA, $scoreEquipeB, $nomDivClassement, $nomDivTableau) {
			echo '<div class="gauche colle-gauche"><img src="images/equipes/' . $equipeAFanion . '"/></div>';
			echo '<div class="gauche scoreRecap">';
				echo '<label>' . $scoreEquipeA . '-' . $scoreEquipeB . '</label>';
			echo '</div>';
			echo '<div class="gauche"><img src="images/equipes/' . $equipeBFanion . '"/></div>';
		}

		// Affichage de l'en-tête de la phase de finale
		function afficherEntetePhaseFinale() {
			echo '<div id="divEntetePhaseFinale">';
				echo '<span class="huitieme">1/8 de finale</span>';
				echo '<span class="quart">1/4 de finale</span>';
				echo '<span class="demi">1/2 finale</span>';
				echo '<span class="finale">Finale et troisième place</span>';
				echo '<span class="demi">1/2 finale</span>';
				echo '<span class="quart">1/4 finale</span>';
				echo '<span class="huitieme">1/8 de finale</span>';
			echo '</div>';
		}

		// Fonction d'affichage de la zone de sélection du meilleur buteur
		function afficherMeilleurButeur($bdd, $nomDiv) {
			// Lecture des données déjà saisies par le pronostiqueur consulté
			$ordreSQL =		'	SELECT		Joueurs_Joueur, Joueurs_Nom' .
							'	FROM		cdm_pronostics_buteur' .
							'	JOIN		cdm_joueurs' .
							'				ON		cdm_pronostics_buteur.Joueurs_Joueur = cdm_joueurs.Joueur' .
							'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"];

			$req = $bdd->query($ordreSQL);
			$buteur = $req->fetchAll();

			if($buteur != null) {
				$meilleurButeur = $buteur[0]["Joueurs_Joueur"] != null ? $buteur[0]["Joueurs_Joueur"] : '';
				$nomMeilleurButeur = $buteur[0]["Joueurs_Nom"] != null ? $buteur[0]["Joueurs_Nom"] : '';
			}
			else {
				$meilleurButeur = 0;
				$nomMeilleurButeur = '';
			}

			echo '<div id="' . $nomDiv . '">';
				echo '<div class="colle-gauche gauche titre">';
					echo '<h2>Choix du meilleur buteur</h2>';
				echo '</div>';
				echo '<div class="gauche saisie">';
					echo '<input type="hidden" id="meilleurButeur" value="' . $meilleurButeur . '" />';
					echo '<label id="lblNomMeilleurButeur">Meilleur buteur actuel : ' . $nomMeilleurButeur . '</label><br />';
					echo '<input style="width: 180px; margin-right: 3px;" type="text" id="nomMeilleurButeur" value="' . $nomMeilleurButeur . '" disabled />';
					echo '<img src="images/loupe.png" alt="Loupe" />';
					echo '<div class="listeJoueurs">';
						echo '<div id="divListeJoueurs"></div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		}
		
		// Page de saisie des pronostics
		
		// Phase de poule
		// Liste des cdm_poules
		$nombrePoules = 8;
		$nombreMatchesParPoule = 6;

		// Lecture des données déjà saisies par le pronostiqueur
		$ordreSQL =		'	SELECT		DISTINCT pronostics_pouleA.Matches_Match, cdm_poules.Poule, cdm_poules.Poules_Nom' .
						'				,equipe_a.Equipe AS EquipeA, equipe_a.Equipes_Nom AS EquipeA_Nom, equipe_a.Equipes_Fanion AS EquipeA_Fanion' .
						'				,equipe_b.Equipe AS EquipeB, equipe_b.Equipes_Nom AS EquipeB_Nom, equipe_b.Equipes_Fanion AS EquipeB_Fanion' .
						'				,IFNULL(pronostics_pouleA.PronosticsPoule_Score, -1) AS PronosticsPoule_ScoreEquipeA' .
						'				,IFNULL(pronostics_pouleB.PronosticsPoule_Score, -1) AS PronosticsPoule_ScoreEquipeB' .
						'	FROM		cdm_matches_poule' .
						'	JOIN		cdm_pronostics_poule pronostics_pouleA' .
						'				ON		cdm_matches_poule.Match = pronostics_pouleA.Matches_Match' .
						'						AND		cdm_matches_poule.Equipes_EquipeA = pronostics_pouleA.Equipes_Equipe' .
						'	JOIN		cdm_pronostics_poule pronostics_pouleB' .
						'				ON		cdm_matches_poule.Match = pronostics_pouleB.Matches_Match' .
						'						AND		cdm_matches_poule.Equipes_EquipeB = pronostics_pouleB.Equipes_Equipe' .
						'	JOIN		cdm_poules' .
						'				ON		cdm_matches_poule.Poules_Poule = cdm_poules.Poule' .
						'	JOIN		cdm_equipes equipe_a' .
						'				ON		cdm_matches_poule.Equipes_EquipeA = equipe_a.Equipe' .
						'						AND		equipe_a.Equipe = pronostics_pouleA.Equipes_Equipe' .
						'	JOIN		cdm_equipes equipe_b' .
						'				ON		cdm_matches_poule.Equipes_EquipeB = equipe_b.Equipe' .
						'						AND		equipe_b.Equipe = pronostics_pouleB.Equipes_Equipe' .
						'	WHERE		pronostics_pouleA.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'				AND		pronostics_pouleB.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'	ORDER BY	cdm_matches_poule.Poules_Poule, cdm_matches_poule.Match';

		$req = $bdd->query($ordreSQL);
		$matchesPoule = $req->fetchAll();
		
		// Classements de poule
		$ordreSQL =		'	SELECT		Equipes_NomCourt, Equipes_Fanion' .
						'	FROM		cdm_pronostics_poule_classements' .
						'	JOIN		cdm_equipes' .
						'				ON		cdm_pronostics_poule_classements.Equipes_Equipe = cdm_equipes.Equipe' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'	ORDER BY	cdm_pronostics_poule_classements.Poules_Poule, IFNULL(PronosticsPouleClassements_ClassementTirage, PronosticsPouleClassements_Classement)';
						
		$req = $bdd->query($ordreSQL);
		$classementsPoule = $req->fetchAll();

		echo '<div id="divReinitialisation"></div>';
		
		echo '<div id="divPronosticsPoulesRecap">';
			echo '<div style="margin-left: 10px;">';
				for($i = 0; $i < $nombrePoules; $i++) {
					echo '<div class="gauche matchesRecap">';
						echo '<div class="nomPoule"><label>' . $matchesPoule[($i * $nombreMatchesParPoule)]["Poules_Nom"] . '</label></div>';
						for($j = 0; $j < $nombreMatchesParPoule; $j++) {
						
							$match = $matchesPoule[($i * $nombreMatchesParPoule) + $j]["Matches_Match"];
							$equipeA = $matchesPoule[($i * $nombreMatchesParPoule) + $j]["EquipeA"];
							$equipeANom = $matchesPoule[($i * $nombreMatchesParPoule) + $j]["EquipeA_Nom"];
							$equipeAFanion = $matchesPoule[($i * $nombreMatchesParPoule) + $j]["EquipeA_Fanion"];
							$equipeB = $matchesPoule[($i * $nombreMatchesParPoule) + $j]["EquipeB"];
							$equipeBNom = $matchesPoule[($i * $nombreMatchesParPoule) + $j]["EquipeB_Nom"];
							$equipeBFanion = $matchesPoule[($i * $nombreMatchesParPoule) + $j]["EquipeB_Fanion"];
							$scoreEquipeA = $matchesPoule[($i * $nombreMatchesParPoule) + $j]["PronosticsPoule_ScoreEquipeA"];
							$scoreEquipeB = $matchesPoule[($i * $nombreMatchesParPoule) + $j]["PronosticsPoule_ScoreEquipeB"];
							
							// Une fois sur deux, le fond est sombre
							if($i % 2 == 0)
								$style = 'pair';
							else
								$style = 'impair';
							echo '<div class="matchPouleRecap ' . $style . '">';
								afficherMatch($i + 1,$match, $equipeA, $equipeANom, $equipeAFanion, $equipeB, $equipeBNom, $equipeBFanion, $scoreEquipeA, $scoreEquipeB, 'divClassementPoule-' . ($i + 1), 'divTableau');
							echo '</div>';
						}
						
						// Affichage du classement de la poule
						echo '<div class="classementPouleRecap ' . $style . '">';
							for($k = 0; $k < 4; $k++) {
								echo ($k + 1) . ' - <img src="images/equipes/' . $classementsPoule[($i * 4) + $k]["Equipes_Fanion"] . '" alt=\'\' /> ' . $classementsPoule[($i * 4) + $k]["Equipes_NomCourt"];
								echo '<br />';
							}
						echo '</div>';
					echo '</div>';
				}
			echo '</div>';
		echo '</div>';
		
		echo '<div id="divPronosticsPhaseFinale">';
			afficherEntetePhaseFinale();
			echo '<div id="divTableau">';
				afficherTableaux($bdd, 'divTableau', $_SESSION["pronostiqueur"]);
			echo '</div>';
			
			afficherMeilleurButeur($bdd, 'divMeilleurButeur');
		echo '</div>';


?>
	
	<script>
		$(function() {
			afficherTitrePage('divReinitialisation', 'Résumé de vos pronostics');
		});

	</script>
</body>
</html>