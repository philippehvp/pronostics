<?php
	// Affichage des données d'un match

	// La page peut être appelée de deux manières :
	// - par une inclusion
	// - par un appel Ajax (cas du rafraîchissement)

	$rafraichissement = isset($_POST["rafraichissement"]) ? $_POST["rafraichissement"] : 0;

	if($rafraichissement == 1) {
		// Rafraîchissement
		include_once('commun_administrateur.php');

		// Lecture des paramètres passés à la page
		$match = isset($_POST["match"]) ? $_POST["match"] : 0;

		$ordreSQL =		'	SELECT		vue_matches.Match' .
									'						,EquipeDomicile, EquipesDomicile_Nom' .
									'						,EquipeVisiteur, EquipesVisiteur_Nom' .
									'						,Matches_CoteEquipeDomicile, Matches_CoteNul, Matches_CoteEquipeVisiteur' .
									'						,Matches_Date' .
									'						,HOUR(Matches_Date) AS Matches_Heure' .
									'						,MINUTE(Matches_Date) AS Matches_Minute' .
									'						,Matches_ScoreEquipeDomicile, Matches_ScoreEquipeVisiteur' .
									'						,Matches_ScoreAPEquipeDomicile, Matches_ScoreAPEquipeVisiteur' .
									'						,Matches_Vainqueur' .
									'						,Matches_MatchCS' .
									'						,Matches_Coefficient' .
									'						,Matches_Report' .
									'						,Matches_AvecProlongation' .
									'						,Matches_L1Europe' .
									'						,Matches_L1EuropeNom' .
									'						,Matches_MatchLie' .
									'						,Matches_PointsQualificationEquipeDomicile, Matches_PointsQualificationEquipeVisiteur' .
									'						,IFNULL(Matches_Direct, 0) AS Matches_Direct' .
									'						,Matches_LienPage' .
									'						,Matches_LienPageComplementaire' .
									'						,Matches_MatchIgnore' .
									'						,Mathches_MatchHorsPronostic' .
									'	FROM			vue_matches' .
									'	WHERE			vue_matches.Match = ' . $match;

		$req = $bdd->query($ordreSQL);
		$matches = $req->fetchAll();

		$numeroMatch = $matches[0]["Match"];
		$matchLie = $matches[0]["Matches_MatchLie"];
		$scoreEquipeDomicile = $matches[0]["Matches_ScoreEquipeDomicile"] != null ? $matches[0]["Matches_ScoreEquipeDomicile"] : -1;
		$scoreAPEquipeDomicile = $matches[0]["Matches_ScoreAPEquipeDomicile"] != null ? $matches[0]["Matches_ScoreAPEquipeDomicile"] : -1;
		$scoreEquipeVisiteur = $matches[0]["Matches_ScoreEquipeVisiteur"] != null ? $matches[0]["Matches_ScoreEquipeVisiteur"] : -1;
		$scoreAPEquipeVisiteur = $matches[0]["Matches_ScoreAPEquipeVisiteur"] != null ? $matches[0]["Matches_ScoreAPEquipeVisiteur"] : -1;
		$matchDirect = $matches[0]["Matches_Direct"] != null ? $matches[0]["Matches_Direct"] : 0;
		$equipeDomicile = $matches[0]["EquipeDomicile"] != null ? $matches[0]["EquipeDomicile"] : 0;
		$equipeVisiteur = $matches[0]["EquipeVisiteur"] != null ? $matches[0]["EquipeVisiteur"] : 0;
		$matchL1Europe = $matches[0]["Matches_L1Europe"] != null ? $matches[0]["Matches_L1Europe"] : 0;
		$coteEquipeDomicile = $matches[0]["Matches_CoteEquipeDomicile"] != null ? $matches[0]["Matches_CoteEquipeDomicile"] : 0;
		$coteNul = $matches[0]["Matches_CoteNul"] != null ? $matches[0]["Matches_CoteNul"] : 0;
		$coteEquipeVisiteur = $matches[0]["Matches_CoteEquipeVisiteur"] != null ? $matches[0]["Matches_CoteEquipeVisiteur"] : 0;
		$matchesAvecProlongation = $matches[0]["Matches_AvecProlongation"] != null ? $matches[0]["Matches_AvecProlongation"] : 0;
		$pointsQualificationEquipeDomicile = $matches[0]["Matches_PointsQualificationEquipeDomicile"] != null ? $matches[0]["Matches_PointsQualificationEquipeDomicile"] : 0;
		$pointsQualificationEquipeVisiteur = $matches[0]["Matches_PointsQualificationEquipeVisiteur"] != null ? $matches[0]["Matches_PointsQualificationEquipeVisiteur"] : 0;
		$matchVainqueur = $matches[0]["Matches_Vainqueur"] != null ? $matches[0]["Matches_Vainqueur"] : 0;
		$matchDate = $matches[0]["Matches_Date"];
		$matchHeure = $matches[0]["Matches_Heure"];
		$matchMinute = $matches[0]["Matches_Minute"];
		$matchCS = $matches[0]["Matches_MatchCS"] != null ? $matches[0]["Matches_MatchCS"] : 0;
		$matchL1EuropeNom = $matches[0]["Matches_L1EuropeNom"] != null ? $matches[0]["Matches_L1EuropeNom"] : '';
		$matchCoefficient = $matches[0]["Matches_Coefficient"] != null ? $matches[0]["Matches_Coefficient"] : 1;
		$matchReport = $matches[0]["Matches_Report"] != null ? $matches[0]["Matches_Report"] : 0;
		$matchLienPage = $matches[0]["Matches_LienPage"] != null ? $matches[0]["Matches_LienPage"] : '';
		$matchLienPageComplementaire = $matches[0]["Matches_LienPageComplementaire"] != null ? $matches[0]["Matches_LienPageComplementaire"] : '';
		$matchIgnore = $matches[0]["Matches_MatchIgnore"] != null ? $matches[0]["Matches_MatchIgnore"] : 0;
		$matchHorsPronostic = $matches[0]["Matches_MatchHorsPronostic"] != null ? $matches[0]["Matches_MatchHorsPronostic"] : 0;
	}

	echo '<div id="divEntete_match_' . $numeroMatch . '" class="zoneEntete">';
		echo '<table class="tableau--administration-match">';
			echo '<tbody>';
				echo '<tr>';
					$dateDebut = $matchDate != null ? date("d/m/Y", strtotime($matchDate)) : date("d/m/Y");
					$heureDebut = $matchHeure != null ? $matchHeure : date("G");
					// Les minutes doivent être un multiple de 5
					$minuteDebut = $matchMinute != null ? $matchMinute : (date("i") + 5 - (date("i") % 5));

					echo '<td class="colonne-theme">Match n° ' . $numeroMatch;
					echo '</td>';

					echo '<td colspan="2" style="text-align: left;">';
						echo '<input class="moyen date" id="dateDebut_match_' . $numeroMatch . '" type="text" value="' . $dateDebut . '" onchange="creerMatch_sauvegarderMatch(0, \'\', ' . $numeroMatch . ');"/> à ';
						echo '<select id="heureDebut_match_' . $numeroMatch . '" onchange="creerMatch_sauvegarderMatch(0, \'\', ' . $numeroMatch . ');">';
							for($j = 0; $j <= 23; $j++) {
								$heures = sprintf('%02u', $j);
								$selected = $heureDebut == $j ? ' selected="selected"' : '';
								echo '<option' . $selected . ' value="' . $j . '">' . $heures . '</option>';
							}
						echo '</select>';

						echo '<select id="minuteDebut_match_' . $numeroMatch . '" onchange="creerMatch_sauvegarderMatch(0, \'\', ' . $numeroMatch . ');">';
							for($j = 0; $j <= 55; $j += 5) {
								$minutes = sprintf('%02u', $j);
								$selected = $minuteDebut == $j ? ' selected="selected"' : '';
								echo '<option' . $selected . ' value="' . $j . '">' . $minutes . '</option>';
							}
						echo '</select>';

						$checked = $matchCoefficient == 2 ? ' checked' : '';
						echo ' - Match Canal : ';
						echo '<input type="checkbox" id="matchCanal_match_' . $numeroMatch . '" onclick="creerMatch_sauvegarderMatch(0, \'\', ' . $numeroMatch . ');"' . $checked . ' />';

						$checked = $matchCS == 1 ? ' checked' : '';
						echo ' - Community Shield : ';
						echo '<input type="checkbox" id="matchCS_match_' . $numeroMatch . '" onclick="creerMatch_sauvegarderMatch(0, \'\', ' . $numeroMatch . ');"' . $checked . ' />';

						$checked = $matchReport == 1 ? ' checked' : '';
						echo ' - Match reporté : ';
						echo '<input type="checkbox" id="report_match_' . $numeroMatch . '" onclick="creerMatch_sauvegarderMatch(0, \'\', ' . $numeroMatch . ');"' . $checked . ' />';
					echo '</td>';
				echo '</tr>';

				echo '<tr>';
					echo '<td class="colonne-theme">Equipes</td>';
					echo '<td>';
						echo '<select id="equipeD_match_' . $numeroMatch . '" onchange="creerMatch_sauvegarderMatch(0, \'\', ' . $numeroMatch . ');">';
							$selected = $equipeDomicile == 0 ? ' selected="selected"' : '';
							echo '<option' . $selected . ' value="0">Equipes</option>';
							foreach($equipes as $equipe) {
								if($matchL1Europe == 1) {
									if($equipe["Equipes_L1Europe"] == 1) {
										$selected = $equipe["Equipe"] == $equipeDomicile ? ' selected="selected"' : '';
										echo '<option' . $selected . ' value="' . $equipe["Equipe"] . '">' . $equipe["Equipes_Nom"] . '</option>';
									}
								}
								else {
									if($equipe["Equipes_L1Europe"] != 1) {
										$selected = $equipe["Equipe"] == $equipeDomicile ? ' selected="selected"' : '';
										echo '<option' . $selected . ' value="' . $equipe["Equipe"] . '">' . $equipe["Equipes_Nom"] . '</option>';
									}
								}
							}
						echo '</select>';
					echo '</td>';
					echo '<td>';
						echo '<select id="equipeV_match_' . $numeroMatch . '" onchange="creerMatch_sauvegarderMatch(0, \'\', ' . $numeroMatch . ');">';
							$selected = $equipeVisiteur == 0 ? ' selected="selected"' : '';
							echo '<option' . $selected . ' value="0">Equipes</option>';
							foreach($equipes as $equipe) {
								if($matchL1Europe == 1) {
									if($equipe["Equipes_L1Europe"] == 1) {
										$selected = $equipe["Equipe"] == $equipeVisiteur ? ' selected="selected"' : '';
										echo '<option' . $selected . ' value="' . $equipe["Equipe"] . '">' . $equipe["Equipes_Nom"] . '</option>';
									}
								}
								else {
									if($equipe["Equipes_L1Europe"] != 1) {
										$selected = $equipe["Equipe"] == $equipeVisiteur ? ' selected="selected"' : '';
										echo '<option' . $selected . ' value="' . $equipe["Equipe"] . '">' . $equipe["Equipes_Nom"] . '</option>';
									}
								}
							}
						echo '</select>';
					echo '</td>';
				echo '</tr>';

				echo '<tr>';
					echo '<td class="colonne-theme">Cotes</td>';
					echo '<td colspan="2">';
						echo '<input type="text" id="coteEquipeD_match_' . $numeroMatch . '"  value="' . $coteEquipeDomicile . '" onchange="creerMatch_sauvegarderMatch(0, \'\', ' . $numeroMatch . ');" />';
						echo '<input type="text" id="coteNul_match_' . $numeroMatch . '"  value="' . $coteNul . '" onchange="creerMatch_sauvegarderMatch(0, \'\', ' . $numeroMatch . ');" />';
						echo '<input type="text" id="coteEquipeV_match_' . $numeroMatch . '"  value="' . $coteEquipeVisiteur . '" onchange="creerMatch_sauvegarderMatch(0, \'\', ' . $numeroMatch . ');" />';
					echo '</td>';
				echo '</tr>';

				echo '<tr>';
					echo '<td class="colonne-theme">Buteurs</td>';
					echo '<td colspan="2">';
						echo '<label class="bouton" onclick="creerMatch_detecterCotesV1(' . $numeroMatch . ')">Cotes auto format L1</label>';
						echo '&nbsp;<label class="bouton" onclick="creerMatch_detecterCotesV2(' . $numeroMatch . ')">Cotes auto format LDC</label>';
						echo '&nbsp;<label class="bouton" onclick="creerMatch_remplirCotes(' . $numeroMatch . ')">Remplissage auto. des cotes</label>';

					echo '</td>';
				echo '</tr>';

				// Dans le cas du match retour d'une confrontation directe, il faut indiquer le nombre de points pour la qualification d'une équipe
				// Il n'y a pas de points de qualification pour le match nul bien entendu
				// C'est le cas également pour les matches de Coupe
				if(($matchLie != null && $matchLie < $numeroMatch) || ($matchesAvecProlongation == 1)) {
					echo '<tr>';
						echo '<td class="colonne-theme">Qualification</td>';
						echo '<td colspan="2">';
								echo '<input type="text" id="pointsQualificationEquipeD_match_' . $numeroMatch . '"  value="' . $pointsQualificationEquipeDomicile . '" onchange="creerMatch_sauvegarderMatch(0, \'\', ' . $numeroMatch . ');" />';
								echo '<input type="text" id="pointsQualificationEquipeV_match_' . $numeroMatch . '"  value="' . $pointsQualificationEquipeVisiteur . '" onchange="creerMatch_sauvegarderMatch(0, \'\', ' . $numeroMatch . ');" />';
						echo '</td>';
				}

				echo '<tr>';
					echo '<td class="colonne-theme">&nbsp;</td>';

					echo '<td>';
						echo '<label class="bouton" onclick="creerMatch_confirmerParticipants(' . $numeroMatch . ', 0)">Joueurs</label>';
						echo '&nbsp;<label class="bouton" onclick="creerMatch_confirmerButeurs(' . $numeroMatch . ', 0)">Buteurs</label>';
						echo '&nbsp;<label class="bouton" onclick="creerMatch_saisirCotes(' . $numeroMatch . ', 0)">Cotes / postes manu</label>';
					echo '</td>';

					echo '<td>';
						echo '<label class="bouton" onclick="creerMatch_confirmerParticipants(' . $numeroMatch . ', 1)">Joueurs</label>';
						echo '&nbsp;<label class="bouton" onclick="creerMatch_confirmerButeurs(' . $numeroMatch . ', 1)">Buteurs</label>';
						echo '&nbsp;<label class="bouton" onclick="creerMatch_saisirCotes(' . $numeroMatch . ', 1)">Cotes / postes manu</label>';
					echo '</td>';

				echo '</tr>';

				echo '<tr>';
					echo '<td class="colonne-theme">Score 90<sup>ème</sup></td>';
					echo '<td colspan="2">';
						echo '<select id="scoreEquipeD_match_' . $numeroMatch . '" onchange="creerMatch_sauvegarderMatch(2, \'scoreEquipeD_match_' . $numeroMatch . '\', ' . $numeroMatch . ');">';
							for($j = -1; $j <= 15; $j++) {
								$selected = $scoreEquipeDomicile == $j ? ' selected="selected"' : '';
								echo '<option value="' . $j . '"' . $selected . '>' . ($j == -1 ? 'Score' : $j) . '</option>';
							}
						echo '</select>';

						echo '<select id="scoreEquipeV_match_' . $numeroMatch . '" onchange="creerMatch_sauvegarderMatch(2, \'scoreEquipeV_match_' . $numeroMatch . '\', ' . $numeroMatch . ');">';
							for($j = -1; $j <= 15; $j++) {
								$selected = $scoreEquipeVisiteur == $j ? ' selected="selected"' : '';
								echo '<option value="' . $j . '"' . $selected . '>' . ($j == -1 ? 'Score' : $j) . '</option>';
							}
						echo '</select>';
					echo '</td>';
				echo '</tr>';

				echo '<tr>';
					echo '<td class="colonne-theme">Score AP</sup></td>';
					echo '<td colspan="2">';
						echo '<select id="scoreAPEquipeD_match_' . $numeroMatch . '" onchange="creerMatch_sauvegarderMatch(2, \'scoreAPEquipeD_match_' . $numeroMatch . '\', ' . $numeroMatch . ');">';
							for($j = -1; $j <= 15; $j++) {
								$selected = $scoreAPEquipeDomicile == $j ? ' selected="selected"' : '';
								echo '<option value="' . $j . '"' . $selected . '>' . ($j == -1 ? 'Score' : $j) . '</option>';
							}
						echo '</select>';
						echo '<select id="scoreAPEquipeV_match_' . $numeroMatch . '" onchange="creerMatch_sauvegarderMatch(2, \'scoreAPEquipeV_match_' . $numeroMatch . '\', ' . $numeroMatch . ');">';
							for($j = -1; $j <= 15; $j++) {
								$selected = $scoreAPEquipeVisiteur == $j ? ' selected="selected"' : '';
								echo '<option value="' . $j . '"' . $selected . '>' . ($j == -1 ? 'Score' : $j) . '</option>';
							}
						echo '</select>';
					echo '</td>';
				echo '</tr>';

				echo '<tr>';
					echo '<td class="colonne-theme">Vainqueur TAB</td>';
					echo '<td colspan="2">';
						echo '<select id="vainqueur_match_' . $numeroMatch . '" onchange="creerMatch_sauvegarderMatch(3, \'vainqueur_match_' . $numeroMatch . '\', ' . $numeroMatch . ');">';
							$selected0 = $matchVainqueur == -1 ? ' selected="selected"' : '';
							$selected1 = $matchVainqueur == 1 ? ' selected="selected"' : '';
							$selected2 = $matchVainqueur == 2 ? ' selected="selected"' : '';
							echo '<option value="-1"' . $selected0 . '>Vainqueur</option>';
							echo '<option value="1"' . $selected1 . '>Equipe 1</option>';
							echo '<option value="2"' . $selected2 . '>Equipe 2</option>';
						echo '</select>';
					echo '</td>';
				echo '</tr>';

				echo '<tr><td colspan="3"><hr /></td></tr>';

				echo '<tr>';
					echo '<td class="colonne-theme">Lien page MeD</td>';
					echo '<td colspan="2">';
						echo '<input type="text" class="lien-page" id="lien_match_' . $numeroMatch . '" value="' . $matchLienPage . '" onchange="creerMatch_sauvegarderMatch(0, \'\', ' . $numeroMatch . ');" />';
						echo '&nbsp;<label class="bouton" onclick="window.open(\'' . $matchLienPage . '\', \'_blank\');">Match</label>';
					echo '</td>';
				echo '</tr>';

				/*echo '<tr>';
					echo '<td class="colonne-theme">Lien page FR</td>';
					echo '<td colspan="2">';
						echo '<input type="text" class="lien-page" id="lien_match_complementaire_' . $numeroMatch . '" value="' . $matchLienPageComplementaire . '" onchange="corrigerLien_FlashResultats(this); creerMatch_sauvegarderMatch(0, \'\', ' . $numeroMatch . ');" />';
						echo '&nbsp;<label class="bouton" onclick="window.open(\'' . $matchLienPageComplementaire . '\', \'_blank\');">Match</label>';
					echo '</td>';
				echo '</tr>';*/

				echo '<tr>';
					if($matchDirect == 1)			echo '<td id="libelleMatchDirect_match_' . $numeroMatch . '" class="colonne-theme vert">Direct</td>';
					else											echo '<td id="libelleMatchDirect_match_' . $numeroMatch . '" class="colonne-theme">Direct</td>';
					$checked = $matchDirect == 1 ? ' checked' : '';
					echo '<td colspan="2">';
						echo '<input type="checkbox" id="matchDirect_match_' . $numeroMatch . '" onclick="matchEnDirect($(this), ' . $numeroMatch . ', \'libelleMatchDirect_match_' . $numeroMatch . '\', \'lien_match_' . $numeroMatch . '\'); creerMatch_sauvegarderMatch(1, \'\', ' . $numeroMatch . ');"' . $checked . ' />';
						echo '<label style="margin-left: 5px;" class="bouton" onclick="creerMatch_lireEffectif(' . $numeroMatch . ', \'lien_match_' . $numeroMatch . '\', 1);">MeD - Effectif</label>';
						echo '<label style="margin-left: 5px;" class="bouton" onclick="creerMatch_lireComposition(' . $numeroMatch . ', \'lien_match_' . $numeroMatch . '\', 1);">MeD - Compo</label>';
						echo '<label style="margin-left: 35px;" class="bouton" onclick="creerMatch_reinitialiserMatch(' . $numeroMatch . ');">Réinitialiser le match</label>';
						$checked = $matchIgnore == 1 ? ' checked' : '';
						echo '<label style="margin-left: 35px;">Non surveillé : </label>';
						echo '<input type="checkbox" id="matchIgnore_match_' . $numeroMatch . '" onclick="creerMatch_sauvegarderMatch(0, \'\', ' . $numeroMatch . ');"' . $checked . ' />';

						$checked = $matchHorsPronostic == 1 ? ' checked' : '';
						echo '<label style="margin-left: 35px;">Hors points pronostics : </label>';
						echo '<input type="checkbox" id="matchHorsPronostic_match_' . $numeroMatch . '" onclick="creerMatch_sauvegarderMatch(0, \'\', ' . $numeroMatch . ');"' . $checked . ' />';

					echo '</td>';
				echo '</tr>';

			echo '</tbody>';
		echo '</table>';
	echo '</div>';
?>


<script>
	$(function() {
		$('.date').datepicker({dateFormat: 'dd/mm/yy'});
	});

	// Passe un match en direct ou non
	function matchEnDirect(elt, match, libelle, lienPage) {
		if(elt.is(':checked') == true) {
			// Ajout du match dans la liste des matches en direct
			creerMatch_passerEnDirect(match);
		}
		else
			// Suppression du match de la liste des matches en direct
			creerMatch_supprimerDuDirect(match);

		if($('#' + libelle).hasClass('vert'))
			$('#' + libelle).removeClass('vert');
		else
			$('#' + libelle).addClass('vert');
	}

	// Le lien pour le site Flash Résultats doit être modifié à la main
	function corrigerLien_FlashResultats(elt) {
			var nouvelleValeur = elt.value;
			nouvelleValeur = nouvelleValeur.replace("#resume-du-match", "#compositions;1");
			elt.value = nouvelleValeur;
	}

</script>
