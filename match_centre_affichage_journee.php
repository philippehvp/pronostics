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
		$pronostiqueurConsulte = isset($_POST["pronostiqueurConsulte"]) ? $_POST["pronostiqueurConsulte"] : 0;
	}

	// Données de la journée
	$ordreSQL =		'	SELECT		Journees_DateMAJ, Journees_DateEvenement' .
					'	FROM		journees' .
					'	WHERE		Journee = ' . $journee;
	$req = $bdd->query($ordreSQL);
	$journees = $req->fetchAll();
	$dateMAJ = $journees[0]["Journees_DateMAJ"];
	$dateEvenement = $journees[0]["Journees_DateEvenement"];

	// Données des matches
	$ordreSQL =		'	SELECT		matches.Match, matches.Matches_Direct, matches.Matches_Coefficient' .
					'				,CASE' .
					'					WHEN	matches.Matches_ScoreEquipeDomicile IS NULL AND matches.Matches_ScoreEquipeVisiteur IS NULL AND matches.Matches_Date < NOW()' .
					'					THEN	\'REP\'' .
					'					WHEN	DATE_FORMAT(matches.Matches_Date, \'%Y-%m-%d\') = CURDATE()' .
					'					THEN	DATE_FORMAT(matches.Matches_Date, \'%H:%i\')' .
					'					ELSE	DATE_FORMAT(matches.Matches_Date, \'%d/%m\')' .
					'				END AS Matches_Date' .
					'				,equipesDomicile.Equipes_NomCourt As EquipesDomicile_NomCourt, equipesVisiteur.Equipes_NomCourt As EquipesVisiteur_NomCourt' .
					'				,matches.Matches_ScoreEquipeDomicile, matches.Matches_ScoreEquipeVisiteur' .
					'				,matches.Matches_ScoreAPEquipeDomicile, matches.Matches_ScoreAPEquipeVisiteur' .
					'				,matches.Matches_Vainqueur' .
					'				,IFNULL(pronostics_carrefinal.PronosticsCarreFinal_Coefficient, 1) AS PronosticsCarreFinal_Coefficient' .
					'				,Journees_PointsQualification' .
					'	FROM		matches' .
					'	JOIN		journees' .
					'				ON		matches.Journees_Journee = journees.Journee' .
					'	JOIN		equipes equipesDomicile' .
					'				ON		matches.Equipes_EquipeDomicile = equipesDomicile.Equipe' .
					'	JOIN		equipes equipesVisiteur' .
					'				ON		matches.Equipes_EquipeVisiteur = equipesVisiteur.Equipe' .
					'	LEFT JOIN	pronostics_carrefinal' .
					'				ON		matches.Match = pronostics_carrefinal.Matches_Match' .
					'						AND		pronostics_carrefinal.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
					'	WHERE		matches.Journees_Journee = ' . $journee .
					'	ORDER BY	matches.Match, matches.Matches_Date';

	$req = $bdd->query($ordreSQL);
	$matches = $req->fetchAll();
	$nombreMatches = sizeof($matches);

	// Buteurs des matches
	$ordreSQL =		'	SELECT		IFNULL(GROUP_CONCAT(' .
					'					CASE' .
					'						WHEN	matches_buteurs.Buteurs_CSC = 1' .
					'						THEN	CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille), \' (CSC)\')' .
					'						ELSE	IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille)' .
					'					END ORDER BY IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \'), \'Aucun\') AS Buteurs' .
					'	FROM		matches' .
					'	LEFT JOIN	matches_buteurs' .
					'				ON		matches.Match = matches_buteurs.Matches_Match' .
					'	LEFT JOIN	joueurs' .
					'				ON		matches_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'	WHERE		matches.Journees_Journee = ' . $journee .
					'	GROUP BY	matches.Match' .
					'	ORDER BY	matches.Match, matches.Matches_Date';
	$req = $bdd->query($ordreSQL);
	$matches_buteurs = $req->fetchAll();

	// Pronostics
	$ordreSQL =		'	SELECT		CASE' .
					'					WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'							OR		fn_pronosticvisible(matches.Match) = 1' .
					'					THEN	pronostics.Pronostics_ScoreEquipeDomicile' .
					'					ELSE	\'?\'' .
					'				END AS Pronostics_ScoreEquipeDomicile' .
					'				,CASE' .
					'					WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'							OR		fn_pronosticvisible(matches.Match) = 1' .
					'					THEN	pronostics.Pronostics_ScoreEquipeVisiteur' .
					'					ELSE	\'?\'' .
					'				END AS Pronostics_ScoreEquipeVisiteur' .
					'				,CASE' .
					'					WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'							OR		fn_pronosticvisible(matches.Match) = 1' .
					'					THEN	pronostics.Pronostics_ScoreAPEquipeDomicile' .
					'					ELSE	\'?\'' .
					'				END AS Pronostics_ScoreAPEquipeDomicile' .
					'				,CASE' .
					'					WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'							OR		fn_pronosticvisible(matches.Match) = 1' .
					'					THEN	pronostics.Pronostics_ScoreAPEquipeVisiteur' .
					'					ELSE	\'?\'' .
					'				END AS Pronostics_ScoreAPEquipeVisiteur' .
					'				,CASE' .
					'					WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'							OR		fn_pronosticvisible(matches.Match) = 1' .
					'					THEN	pronostics.Pronostics_Vainqueur' .
					'					ELSE	\'?\'' .
					'				END AS Pronostics_Vainqueur' .
					'	FROM		matches' .
					'	LEFT JOIN	pronostics' .
					'				ON		matches.Match = pronostics.Matches_Match' .
					'	WHERE		matches.Journees_Journee = ' . $journee .
					'				AND		pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
					'	ORDER BY	matches.Match, matches.Matches_Date';
	$req = $bdd->query($ordreSQL);
	$pronostics = $req->fetchAll();

	// Buteurs pronostiqués
	$ordreSQL =		'	SELECT		matches.Match' .
					'				,CASE' .
					'					WHEN	pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'							OR		matches.Matches_Date <= NOW()' .
					'					THEN	IFNULL(GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) ORDER BY IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \'), \'Aucun\')' .
					'					ELSE	\'?\'' .
					'				END AS Buteurs' .
					'	FROM		matches' .
					'	LEFT JOIN	(' .
					'					SELECT		Pronostiqueurs_Pronostiqueur, Matches_Match, Joueurs_Joueur' .
					'					FROM		pronostics_buteurs' .
					'					WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
					'				) pronostics_buteurs' .
					'				ON		matches.Match = pronostics_buteurs.Matches_Match' .
					'	LEFT JOIN	joueurs' .
					'				ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'	WHERE		matches.Journees_Journee = ' . $journee .
					'	GROUP BY	matches.Match' .
					'	ORDER BY	matches.Match, matches.Matches_Date';

	$req = $bdd->query($ordreSQL);
	$pronostics_buteurs = $req->fetchAll();

	// Points obtenus
	$ordreSQL =		'	SELECT		matches.Match' .
					'				,IFNULL(scores.Scores_ScoreMatch, 0) AS Scores_ScoreMatch, IFNULL(scores.Scores_ScoreButeur, 0) AS Scores_ScoreButeur, IFNULL(scores.Scores_ScoreBonus, 0) AS Scores_ScoreBonus' .
					'				,scores.Scores_ScoreQualification' .
					'	FROM		matches' .
					'	LEFT JOIN	scores' .
					'				ON		matches.Match = scores.Matches_Match' .
					'						AND		scores.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
					'	WHERE		matches.Journees_Journee = ' . $journee .
					'	ORDER BY	matches.Match, matches.Matches_Date';
	$req = $bdd->query($ordreSQL);
	$scores = $req->fetchAll();

	// Affichage des résultats
	echo '<table class="mc--tableau-resultats">';
		echo '<thead>';
			echo '<tr>';
				echo '<th class="bordure-basse"><img src="images/match_direct.png" alt="" /></th>';
				echo '<th class="bordure-basse">Matches</th>';
				echo '<th class="bordure-basse points">&nbsp;</th>';
				echo '<th class="bordure-basse" colspan="2">Résultats et pronostics</th>';

				// Si des points qualification existent, il est nécessaire d'afficher les scores sur 4 colonnes
				if($matches[0]["Journees_PointsQualification"] == 1)
					echo '<th colspan="4" class="bordure-basse aligne-centre">Points</th>';
				else
					echo '<th colspan="3" class="bordure-basse aligne-centre">Points</th>';
			echo '</tr>';
		echo '</thead>';

		echo '<tbody>';
			// L'affichage des données se fait par couple : résultats sur la première ligne et pronostics sur la deuxième ligne
			for($i = 0; $i < $nombreMatches; $i++) {
				// Résultats
				echo '<tr id="match_' . $matches[$i]["Match"] . '" class="mc--match" onclick="matchCentre_afficherDetailMatch(\'mc--detail-match\', ' . $matches[$i]["Match"] . ');">';

					$classe = $matches[$i]["Matches_Direct"] == 1 ? 'direct' : '';

					if($matches[$i]["Matches_Coefficient"] == 2)			echo '<td class="bordure-basse bordure-droite-legere ' . $classe . ' aligne-centre">C+</td>';
					else													echo '<td class="bordure-basse bordure-droite-legere ' . $classe . ' aligne-centre">&nbsp;</td>';

					echo '<td class="selectionnable bordure-basse bordure-droite-legere">' . $matches[$i]["EquipesDomicile_NomCourt"] . ' - ' . $matches[$i]["EquipesVisiteur_NomCourt"] . '</td>';

					$scoreMatch = $scores[$i]["Scores_ScoreMatch"] / ($matches[$i]["Matches_Coefficient"] * $matches[$i]["PronosticsCarreFinal_Coefficient"]);
					if($scoreMatch >= 5 && $scoreMatch < 10)
						$classe = 'orange';
					else if($scoreMatch == 10)
						$classe = 'vert';
					else
						$classe = '';

					echo '<td class="bordure-basse bordure-droite-legere points ' . $classe .'">&nbsp;</td>';

					// Affichage du score réel
					// Si celui-ci n'est pas encore connu, afficher l'heure du match (ou le jour si le match a lieu dans le futur)
					if($matches[$i]["Matches_Vainqueur"] != null) {
						if($matches[$i]["Matches_Vainqueur"] == -1)
							$scoreReelAffiche = $matches[$i]["Matches_ScoreAPEquipeDomicile"] . ' AP - ' . $matches[$i]["Matches_ScoreAPEquipeVisiteur"] . ' AP';
						else if($matches[$i]["Matches_Vainqueur"] == 1)
							$scoreReelAffiche = $matches[$i]["Matches_ScoreAPEquipeDomicile"] . ' TAB - ' . $matches[$i]["Matches_ScoreAPEquipeVisiteur"];
						else if($matches[$i]["Matches_Vainqueur"] == 2)
							$scoreReelAffiche = $matches[$i]["Matches_ScoreAPEquipeDomicile"] . ' - ' . $matches[$i]["Matches_ScoreAPEquipeVisiteur"] . ' TAB';
						else
							$scoreReelAffiche = 'TIRS AU BUT';
					}
					else {
						if($matches[$i]["Matches_ScoreAPEquipeDomicile"] != null && $matches[$i]["Matches_ScoreAPEquipeVisiteur"] != null) {
							if($matches[$i]["Matches_ScoreAPEquipeDomicile"] > $matches[$i]["Matches_ScoreAPEquipeVisiteur"])
								$scoreReelAffiche = $matches[$i]["Matches_ScoreAPEquipeDomicile"] . ' AP - ' . $matches[$i]["Matches_ScoreAPEquipeVisiteur"];
							else if($matches[$i]["Matches_ScoreAPEquipeDomicile"] > $matches[$i]["Matches_ScoreAPEquipeVisiteur"])
								$scoreReelAffiche = $matches[$i]["Matches_ScoreAPEquipeDomicile"] . ' - ' . $matches[$i]["Matches_ScoreAPEquipeVisiteur"] . ' AP';
							else
								$scoreReelAffiche = $matches[$i]["Matches_ScoreAPEquipeDomicile"] . ' AP - ' . $matches[$i]["Matches_ScoreAPEquipeVisiteur"] . ' AP';

						}
						else
							$scoreReelAffiche = $matches[$i]["Matches_ScoreEquipeDomicile"] . ' - ' . $matches[$i]["Matches_ScoreEquipeVisiteur"];
					}

					if($scoreReelAffiche == ' - ')
						$scoreReelAffiche = $matches[$i]["Matches_Date"];

					// Affichage du score pronostiqué
					if($pronostics[$i]["Pronostics_Vainqueur"] != null && $pronostics[$i]["Pronostics_Vainqueur"] != '?') {
						if($pronostics[$i]["Pronostics_Vainqueur"] == -1)
							$scorePronostiqueAffiche = $pronostics[$i]["Pronostics_ScoreAPEquipeDomicile"] . ' AP - ' . $pronostics[$i]["Pronostics_ScoreAPEquipeVisiteur"] . ' AP';
						else if($pronostics[$i]["Pronostics_Vainqueur"] == 1)
							$scorePronostiqueAffiche = $pronostics[$i]["Pronostics_ScoreAPEquipeDomicile"] . ' TAB - ' . $pronostics[$i]["Pronostics_ScoreAPEquipeVisiteur"];
						else if($pronostics[$i]["Pronostics_Vainqueur"] == 2)
							$scorePronostiqueAffiche = $pronostics[$i]["Pronostics_ScoreAPEquipeDomicile"] . ' - ' . $pronostics[$i]["Pronostics_ScoreAPEquipeVisiteur"] . ' TAB';
						else
							$scorePronostiqueAffiche = 'TIRS AU BUT';
					}
					else {
						if($pronostics[$i]["Pronostics_ScoreAPEquipeDomicile"] != null && $pronostics[$i]["Pronostics_ScoreAPEquipeDomicile"] != '?' && $pronostics[$i]["Pronostics_ScoreAPEquipeVisiteur"] != null && $pronostics[$i]["Pronostics_ScoreAPEquipeVisiteur"] != '?') {
							if($pronostics[$i]["Pronostics_ScoreAPEquipeDomicile"] > $pronostics[$i]["Pronostics_ScoreAPEquipeVisiteur"])
								$scorePronostiqueAffiche = $pronostics[$i]["Pronostics_ScoreAPEquipeDomicile"] . ' AP - ' . $pronostics[$i]["Pronostics_ScoreAPEquipeVisiteur"];
							else if($pronostics[$i]["Pronostics_ScoreAPEquipeDomicile"] > $pronostics[$i]["Pronostics_ScoreAPEquipeVisiteur"])
								$scorePronostiqueAffiche = $pronostics[$i]["Pronostics_ScoreAPEquipeDomicile"] . ' - ' . $pronostics[$i]["Pronostics_ScoreAPEquipeVisiteur"] . ' AP';
							else
								$scorePronostiqueAffiche = $pronostics[$i]["Pronostics_ScoreAPEquipeDomicile"] . ' AP - ' . $pronostics[$i]["Pronostics_ScoreAPEquipeVisiteur"] . ' AP';

						}
						else {
							if($pronostics[$i]["Pronostics_ScoreEquipeDomicile"] != null && $pronostics[$i]["Pronostics_ScoreEquipeDomicile"] != '?' && $pronostics[$i]["Pronostics_ScoreEquipeVisiteur"] != null && $pronostics[$i]["Pronostics_ScoreEquipeVisiteur"] != '?')
								$scorePronostiqueAffiche = $pronostics[$i]["Pronostics_ScoreEquipeDomicile"] . ' - ' . $pronostics[$i]["Pronostics_ScoreEquipeVisiteur"];
							else
								$scorePronostiqueAffiche = '? - ?';
						}
					}

					echo '<td class="selectionnable bordure-basse bordure-droite-legere aligne-centre">';
						echo $scoreReelAffiche . '<br />';
						echo $scorePronostiqueAffiche;
					echo '</td>';

					echo '<td class="selectionnable bordure-basse bordure-droite-legere" style="overflow-x: auto; overflow-y: auto; white-space: nowrap; width: auto; max-width: 350px;">';
						echo '<div class="scroll-pane">';
							echo '<label style="display: block;">' . $matches_buteurs[$i]["Buteurs"] . '</label>';
							echo '<label>' . $pronostics_buteurs[$i]["Buteurs"] . '</label>';
						echo '</div>';
					echo '</td>';

					// Points obtenus
					echo '<td class="selectionnable bordure-basse bordure-droite-legere aligne-centre">' . $scores[$i]["Scores_ScoreMatch"] . '</td>';
					echo '<td class="selectionnable bordure-basse bordure-droite-legere aligne-centre">' . $scores[$i]["Scores_ScoreButeur"] . '</td>';

					if($matches[0]["Journees_PointsQualification"] == 1) {
						$scoreQualification = $scores[$i]["Scores_ScoreQualification"] != null ? $scores[$i]["Scores_ScoreQualification"] : 0;
						echo '<td class="selectionnable bordure-basse bordure-droite-legere aligne-centre">' . $scores[$i]["Scores_ScoreBonus"] . '</td>';
						echo '<td class="selectionnable bordure-basse aligne-centre">' . $scoreQualification . '</td>';
					}
					else
						echo '<td class="selectionnable bordure-basse aligne-centre">' . $scores[$i]["Scores_ScoreBonus"] . '</td>';
				echo '</tr>';
			}

		echo '</tbody>';
	echo '</table>';

?>


<script>
	$(function() {
		//$('.scroll-pane').getNiceScroll().remove();
		$('.scroll-pane').niceScroll({cursorcolor: "#0e2c3d", cursorborder: "#0e2c3d"});

		// Mise en surbrillance du match précédemment sélectionné si nécessaire lors du rafraîchissement automatique de la page
		var matchSelectionne = $('input[name="matchSelectionne"]').val();
		if(matchSelectionne) {
			$('#match_' + matchSelectionne).addClass('selectionne');
		}

		$('.mc--tableau-resultats tr').click(
			function() {
				$('.mc--tableau-resultats tr.mc--match.selectionne').removeClass('selectionne');
				$(this).addClass('selectionne');
			}
		);

	});

</script>