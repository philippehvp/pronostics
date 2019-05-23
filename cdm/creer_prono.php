<?php
	include_once('commun.php');
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
		$nomPage = 'creer_prono.php';
		include_once('bandeau.php');
		
		include ('creer_prono_affichage_classement.php');
		include ('creer_prono_affichage_tableau.php');
		
		// Affichage du match entre deux équipes
		function afficherMatch($poule, $match, $equipeA, $equipeANom, $equipeAFanion, $equipeB, $equipeBNom, $equipeBFanion, $scoreEquipeA, $scoreEquipeB, $nomDivClassement, $nomDivTableau) {
			echo '<span class="nomEquipeGauche"> ' . $equipeANom . '</span>' . '<span><img src="images/equipes/' . $equipeAFanion . '"/></span>';
			echo '<span class="score">';
				echo '<select onchange="creerProno_changerScorePoule(this, ' . $poule . ', ' . $match . ', ' . $equipeA . ', \'' . $nomDivClassement . '\', \'' . $nomDivTableau . '\', \'lblProblemeEgalites' . $poule . '\');" id="match_' . $match . '_scoreEquipeA">';
					for($i = -1; $i < 16; $i++) {
						$selected = ($i == $scoreEquipeA) ? ' selected' : '';
						echo '<option value="' . $i . '"' . $selected . '>' . ($i == -1 ? 'Score' : $i) . '</option>';
					}
				echo '</select>';
				echo '<select onchange="creerProno_changerScorePoule(this, ' . $poule . ', ' . $match . ', ' . $equipeB . ', \'' . $nomDivClassement . '\', \'' . $nomDivTableau . '\');" id="match_' . $match . '_scoreEquipeB">';
					for($i = -1; $i < 16; $i++) {
						$selected = ($i == $scoreEquipeB) ? ' selected' : '';
						echo '<option value="' . $i . '"' . $selected . '>' . ($i == -1 ? 'Score' : $i) . '</option>';
					}
				echo '</select>';
			echo '</span>';
			echo '<span><img src="images/equipes/' . $equipeBFanion . '"/></span>' . '<span class="nomEquipeDroite">' . $equipeBNom . '</span>';
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
		function afficherMeilleurButeur($bdd, $tableau, $nomDiv) {
			// Deux cas se présentent :
			// - le pronostiqueur connecté est l'administrateur du site
			// - c'est un autre pronostiqueur qui est connecté
			// Dans le premier cas, il faut accéder à la table cdm_meilleur_buteur car c'est elle qui contient le ou les meilleur(s) buteur(s)
			
			// Lecture des données déjà saisies par le pronostiqueur
			if($_SESSION["cdm_pronostiqueur"] == 1) {
				$ordreSQL =		'	SELECT		GROUP_CONCAT(Joueurs_Nom SEPARATOR \', \') AS Joueurs_Nom' .
								'	FROM		cdm_meilleur_buteur' .
								'	JOIN		cdm_joueurs' .
								'				ON		Joueurs_Joueur = Joueur';
								
				$req = $bdd->query($ordreSQL);
				$buteur = $req->fetchAll();

				if($buteur != null)
					$nomMeilleurButeur = $buteur[0]["Joueurs_Nom"] != null ? $buteur[0]["Joueurs_Nom"] : '';
				else
					$nomMeilleurButeur = '';

				echo '<div id="' . $nomDiv . '">';
					echo '<div class="colle-gauche gauche titre">';
						echo '<h2>Choix du meilleur buteur</h2>';
					echo '</div>';
					echo '<div class="gauche saisie">';
						echo '<label id="lblNomMeilleurButeur">Meilleur(s) buteur(s) actuel(s) :<br />' . $nomMeilleurButeur . '</label>';
					echo '</div>';
				echo '</div>';
			}
			else {
					$ordreSQL =		'	SELECT		Joueurs_Joueur, Joueurs_Nom' .
												'	FROM		cdm_pronostics_buteur' .
												'	JOIN		cdm_joueurs' .
												'				ON		cdm_pronostics_buteur.Joueurs_Joueur = cdm_joueurs.Joueur' .
												'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"];

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
							echo '<input style="width: 180px; margin-right: 3px;" type="text" id="nomMeilleurButeur" value="' . $nomMeilleurButeur . '" />';
							echo '<img src="images/loupe.png" alt="Loupe" />';
							echo '<div class="listeJoueurs">';
								echo '<div id="divListeJoueurs"></div>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
			}
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
						'	WHERE		pronostics_pouleA.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"] .
						'				AND		pronostics_pouleB.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"] .
						'	ORDER BY	cdm_matches_poule.Poules_Poule, cdm_matches_poule.Match';

		$req = $bdd->query($ordreSQL);
		$matchesPoule = $req->fetchAll();

		echo '<div id="divReinitialisation">';
			echo '<label id="lblReinitialiser">Réinitialiser les pronostics</label>';
		echo '</div>';
		
		echo '<div id="divPronosticsPoules">';
			echo '<ul>';
				for($i = 0; $i < $nombrePoules; $i++)
					echo '<li><a href="#divPronosticsPoules-' . ($i + 1) . '">' . $matchesPoule[($i * $nombreMatchesParPoule)]["Poules_Nom"] . '</a></li>';
			echo '</ul>';
			for($i = 0; $i < $nombrePoules; $i++) {
				echo '<div id="divPronosticsPoules-' . ($i + 1) . '">';
					echo '<div class="gauche matches">';
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
							
							echo '<div class="gauche matchPoule">';
								afficherMatch($i + 1,$match, $equipeA, $equipeANom, $equipeAFanion, $equipeB, $equipeBNom, $equipeBFanion, $scoreEquipeA, $scoreEquipeB, 'divClassementPoule-' . ($i + 1), 'divTableau');
							echo '</div>';
						}
					echo '</div>';
					echo '<div id="divClassementPoule-' . ($i + 1) . '" class="gauche classementPoule">';
						afficherClassementPoule($i + 1, $bdd);
					echo '</div>';

				echo '</div>';
			}
		echo '</div>';
		
		echo '<div id="divPronosticsPhaseFinale">';
			afficherEntetePhaseFinale();
			echo '<div id="divTableau">';
				afficherTableaux($bdd, 'divTableau');
			echo '</div>';
			
			afficherMeilleurButeur($bdd, $tableau, 'divMeilleurButeur');
		echo '</div>';


?>
	
	<div id="divInfo"></div>
	<div id="divScoreMatch"></div>
	<div id="divPronosticDepasse"></div>
	<div id="divEgalites"></div>
	<div id="divReinitialisationPronostics"></div>
	
	<script>
		$(function() {
			afficherTitrePage('divReinitialisation', 'Saisie des pronostics');
			$('#divPronosticsPoules').tabs();
			
			$('#lblReinitialiser').button().click	(	function(event) {
															creerProno_effacerPronostics();
														}
													);
			<?php
				if($_SESSION["cdm_pronostiqueur"] == 1 || time() < 1528988400) {
			?>
					$('#nomMeilleurButeur').keyup(function(event) {
						// Lecture de la taille de la zone de texte
						if($('#nomMeilleurButeur').val().length >= 3) {
							creerProno_rechercherJoueur($('#nomMeilleurButeur').val(), '#divListeJoueurs', '#nomMeilleurButeur', '#meilleurButeur', '#lblNomMeilleurButeur');
							$('#divListeJoueurs').css({'display': 'block'});
						}
						else {
							$('#divListeJoueurs').css({'display': 'none'});
						}
						
					});
					
					$('#nomMeilleurButeur').blur	(	function() {
						setTimeout(function() {
							$('#divListeJoueurs').css({'display': 'none'});
						}, 500);
					});
												
					$('#nomMeilleurButeur').focus(function() {
						// Lecture de la taille de la zone de texte
						if($('#nomMeilleurButeur').val().length >= 3) {
							creerProno_rechercherJoueur($('#nomMeilleurButeur').val(), '#divListeJoueurs', '#nomMeilleurButeur', '#meilleurButeur', '#lblNomMeilleurButeur');
							$('#divListeJoueurs').css({'display': 'block'});
						}
					});
			<?php
				}
			?>
		});

	</script>
</body>
</html>