<?php
	// Affichage des données d'un match
    $ordreSQL =		'	SELECT		vue_matches.Match' .
                    '				,EquipeDomicile, EquipesDomicile_Nom' .
                    '				,EquipeVisiteur, EquipesVisiteur_Nom' .
                    '				,Matches_Date' .
                    '				,Matches_ScoreEquipeDomicile, Matches_ScoreEquipeVisiteur' .
                    '				,Matches_ScoreAPEquipeDomicile, Matches_ScoreAPEquipeVisiteur' .
                    '				,Matches_Vainqueur' .
                    '				,Matches_Report' .
                    '				,IFNULL(Matches_Direct, 0) AS Matches_Direct' .
                    '				,Matches_MatchIgnore' .
                    '				,Matches_MatchHorsPronostic' .
                    '	FROM		vue_matches' .
                    '	WHERE		vue_matches.Match = ' . $match;

    $req = $bdd->query($ordreSQL);
    $matches = $req->fetchAll();

    $scoreEquipeDomicile = $matches[0]["Matches_ScoreEquipeDomicile"] != null ? $matches[0]["Matches_ScoreEquipeDomicile"] : -1;
    $scoreAPEquipeDomicile = $matches[0]["Matches_ScoreAPEquipeDomicile"] != null ? $matches[0]["Matches_ScoreAPEquipeDomicile"] : -1;
    $scoreEquipeVisiteur = $matches[0]["Matches_ScoreEquipeVisiteur"] != null ? $matches[0]["Matches_ScoreEquipeVisiteur"] : -1;
    $scoreAPEquipeVisiteur = $matches[0]["Matches_ScoreAPEquipeVisiteur"] != null ? $matches[0]["Matches_ScoreAPEquipeVisiteur"] : -1;
    $matchDirect = $matches[0]["Matches_Direct"] != null ? $matches[0]["Matches_Direct"] : 0;
    $equipeDomicile = $matches[0]["EquipeDomicile"] != null ? $matches[0]["EquipeDomicile"] : 0;
    $equipeVisiteur = $matches[0]["EquipeVisiteur"] != null ? $matches[0]["EquipeVisiteur"] : 0;
    $matchVainqueur = $matches[0]["Matches_Vainqueur"] != null ? $matches[0]["Matches_Vainqueur"] : 0;
    $matchDate = $matches[0]["Matches_Date"];
    $matchIgnore = $matches[0]["Matches_MatchIgnore"] != null ? $matches[0]["Matches_MatchIgnore"] : 0;
    $matchHorsPronostic = $matches[0]["Matches_MatchHorsPronostic"] != null ? $matches[0]["Matches_MatchHorsPronostic"] : 0;

    echo '<input type="hidden" id="dateDebut_match_' . $match . '" value="' . $matchDate . '" />';
	echo '<div id="divEntete_match_' . $match . '" class="zoneEntete">';
		echo '<table>';
			echo '<tbody>';
				echo '<tr>';
					echo '<td class="colonne-theme">Effectif</td>';

					echo '<td>';
						echo '<label class="bouton" onclick="creerMatch_confirmerParticipants(' . $match . ', 0, ' . $equipeDomicile . ')">Joueurs</label>';
						echo '&nbsp;<label class="bouton" onclick="creerMatch_confirmerButeurs(' . $match . ', 0, ' . $equipeDomicile . ')">Buteurs</label>';
						echo '&nbsp;<label class="bouton" onclick="creerMatch_saisirCotes(' . $match . ', 0, ' . $equipeDomicile . ')">Cotes / postes manu</label>';
					echo '</td>';

					echo '<td>';
						echo '<label class="bouton" onclick="creerMatch_confirmerParticipants(' . $match . ', 1, ' . $equipeVisiteur . ')">Joueurs</label>';
						echo '&nbsp;<label class="bouton" onclick="creerMatch_confirmerButeurs(' . $match . ', 1, ' . $equipeVisiteur . ')">Buteurs</label>';
						echo '&nbsp;<label class="bouton" onclick="creerMatch_saisirCotes(' . $match . ', 1, ' . $equipeVisiteur . ')">Cotes / postes manu</label>';
					echo '</td>';

				echo '</tr>';

				echo '<tr>';
					echo '<td class="colonne-theme">Score 90<sup>ème</sup></td>';
					echo '<td>';
						echo '<select id="scoreEquipeD_match_' . $match . '" onchange="consulterMatch_sauvegarderMatch(2, \'scoreEquipeD_match_' . $match . '\', ' . $match . ', 1);">';
							for($j = -1; $j <= 15; $j++) {
								$selected = $scoreEquipeDomicile == $j ? ' selected="selected"' : '';
								echo '<option value="' . $j . '"' . $selected . '>' . ($j == -1 ? 'Score' : $j) . '</option>';
							}
                        echo '</select>';
                    echo '</td>';

                    echo '<td>';
						echo '<select id="scoreEquipeV_match_' . $match . '" onchange="consulterMatch_sauvegarderMatch(2, \'scoreEquipeV_match_' . $match . '\', ' . $match . ', 2);">';
							for($j = -1; $j <= 15; $j++) {
								$selected = $scoreEquipeVisiteur == $j ? ' selected="selected"' : '';
								echo '<option value="' . $j . '"' . $selected . '>' . ($j == -1 ? 'Score' : $j) . '</option>';
							}
						echo '</select>';
					echo '</td>';
				echo '</tr>';

				echo '<tr>';
					echo '<td class="colonne-theme">Score AP</sup></td>';
					echo '<td>';
						echo '<select id="scoreAPEquipeD_match_' . $match . '" onchange="consulterMatch_sauvegarderMatch(2, \'scoreAPEquipeD_match_' . $match . '\', ' . $match . ', 3);">';
							for($j = -1; $j <= 15; $j++) {
								$selected = $scoreAPEquipeDomicile == $j ? ' selected="selected"' : '';
								echo '<option value="' . $j . '"' . $selected . '>' . ($j == -1 ? 'Score' : $j) . '</option>';
							}
                        echo '</select>';
                    echo '</td>';

                    echo '<td>';
						echo '<select id="scoreAPEquipeV_match_' . $match . '" onchange="consulterMatch_sauvegarderMatch(2, \'scoreAPEquipeV_match_' . $match . '\', ' . $match . ', 4);">';
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
						echo '<select id="vainqueur_match_' . $match . '" onchange="consulterMatch_sauvegarderMatch(3, \'vainqueur_match_' . $match . '\', ' . $match . ', 5);">';
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
					if($matchDirect == 1)			echo '<td id="libelleMatchDirect_match_' . $match . '" class="colonne-theme vert">Direct</td>';
					else											echo '<td id="libelleMatchDirect_match_' . $match . '" class="colonne-theme">Direct</td>';
					$checked = $matchDirect == 1 ? ' checked' : '';
					echo '<td colspan="2">';
						echo '<input type="checkbox" id="matchDirect_match_' . $match . '" onclick="matchEnDirect($(this), ' . $match . ', \'libelleMatchDirect_match_' . $match . '\', \'lien_match_' . $match . '\'); consulterMatch_sauvegarderMatch(1, \'\', ' . $match . ', 8);"' . $checked . ' />';
						echo '<label style="margin-left: 5px;" class="bouton" onclick="creerMatch_lireEffectif(' . $match . ', \'lien_match_' . $match . '\', 1);">MeD - Effectif</label>';
						echo '<label style="margin-left: 5px;" class="bouton" onclick="creerMatch_lireComposition(' . $match . ', \'lien_match_' . $match . '\', 1);">MeD - Compo</label>';
						echo '<label style="margin-left: 35px;" class="bouton" onclick="creerMatch_reinitialiserMatch(' . $match . ');">Réinitialiser le match</label>';
						$checked = $matchIgnore == 1 ? ' checked' : '';
						echo '<label style="margin-left: 35px;">Non surveillé : </label>';
						echo '<input type="checkbox" id="matchIgnore_match_' . $match . '" onclick="consulterMatch_sauvegarderMatch(0, \'\', ' . $match . ', 6);"' . $checked . ' />';

						$checked = $matchHorsPronostic == 1 ? ' checked' : '';
						echo '<label style="margin-left: 35px;">Hors points pronostics : </label>';
						echo '<input type="checkbox" id="matchHorsPronostic_match_' . $match . '" onclick="consulterMatch_sauvegarderMatch(0, \'\', ' . $match . ', 7);"' . $checked . ' />';

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
