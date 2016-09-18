<?php
	// Fonction d'affichage du tableau des 1/8, 1/4 et 1/2 finale
	function afficherTableau($bdd, $nomDivTableau, $numeroMatchMin, $numeroMatchMax, $nomDiv, $classeEquipe, $classeEspacement, $classeEspacementVertical, $finale, $pronostiqueurConsulte) {
		$ordreSQL =		'	SELECT		cdm_equipes.Matches_Match, Equipe, Equipes_Nom, Pronostics_Score, Pronostics_ScoreAP, Pronostics_Vainqueur, Equipes_Fanion, Ordre' .
						'				,CASE' .
						'					WHEN	cdm_pronostics_sequencement.Equipes_EquipeA IS NOT NULL AND cdm_pronostics_sequencement.Equipes_EquipeB IS NOT NULL' .
						'					THEN	1' .
						'					ELSE	0' .
						'				END AS Pronostiquable' .
						'	FROM		(' .
						'					SELECT		cdm_pronostics_sequencement.Matches_Match, Equipe, Equipes_NomCourt AS Equipes_Nom, Equipes_Fanion, PronosticsSequencement_EquipeAB, 1 AS Ordre' .
						'								,Pronostics_ScoreEquipeA AS Pronostics_Score, Pronostics_ScoreAPEquipeA AS Pronostics_ScoreAP, Pronostics_Vainqueur' .
						'					FROM		cdm_pronostics_sequencement' .
						'					LEFT JOIN	cdm_equipes' .
						'								ON		cdm_pronostics_sequencement.Equipes_EquipeA = cdm_equipes.Equipe' .
						'					LEFT JOIN	cdm_pronostics_phase_finale' .
						'								ON		cdm_pronostics_sequencement.Pronostiqueurs_Pronostiqueur = cdm_pronostics_phase_finale.Pronostiqueurs_Pronostiqueur' .
						'										AND		cdm_pronostics_sequencement.Matches_Match = cdm_pronostics_phase_finale.Matches_Match' .
						'					WHERE		cdm_pronostics_sequencement.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'								AND		cdm_pronostics_sequencement.Matches_Match >= ' . $numeroMatchMin .
						'								AND		cdm_pronostics_sequencement.Matches_Match <= ' . $numeroMatchMax .
						'					UNION ALL' .
						'					SELECT		cdm_pronostics_sequencement.Matches_Match, Equipe, Equipes_NomCourt AS Equipes_Nom, Equipes_Fanion, PronosticsSequencement_EquipeAB, 2 AS Ordre' .
						'								,Pronostics_ScoreEquipeB AS Pronostics_Score, Pronostics_ScoreAPEquipeB AS Pronostics_ScoreAP, Pronostics_Vainqueur' .
						'					FROM		cdm_pronostics_sequencement' .
						'					LEFT JOIN	cdm_equipes' .
						'								ON		cdm_pronostics_sequencement.Equipes_EquipeB = cdm_equipes.Equipe' .
						'					LEFT JOIN	cdm_pronostics_phase_finale' .
						'								ON		cdm_pronostics_sequencement.Pronostiqueurs_Pronostiqueur = cdm_pronostics_phase_finale.Pronostiqueurs_Pronostiqueur' .
						'										AND		cdm_pronostics_sequencement.Matches_Match = cdm_pronostics_phase_finale.Matches_Match' .
						'					WHERE		cdm_pronostics_sequencement.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'								AND		cdm_pronostics_sequencement.Matches_Match >= ' . $numeroMatchMin .
						'								AND		cdm_pronostics_sequencement.Matches_Match <= ' . $numeroMatchMax .
						'				) cdm_equipes' .
						'	LEFT JOIN	cdm_pronostics_sequencement' .
						'				ON		cdm_pronostics_sequencement.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'						AND		cdm_equipes.Matches_Match = cdm_pronostics_sequencement.Matches_Match' .
						'	ORDER BY	Matches_Match, Ordre';

		$req = $bdd->query($ordreSQL);
		$tableau = $req->fetchAll();
		
		// L'affichage des équipes subit un décalage
		// En effet, la requête retourne :
		// - le premier d'une poule (A)
		// - le deuxième de la poule suivante (B)
		// - le premier de la poule B
		// - le deuxième de la poule A
		echo '<div id="' . $nomDiv . '">';
			for($i = 0; $i < ($numeroMatchMax - $numeroMatchMin + 1); $i++) {
				for($j = 0; $j < 2; $j++) {
					$indiceBrut = $i * 2 + $j;
					$modulo = $indiceBrut % 4;
					
					// Dans les matches différents de la finale, l'affichage des équipes est sur la même ligne (par deux)
					// Pour la finale et la petite finale, chaque équipe est sur une ligne seule ligne
					if($finale == 0)
						$alignement = ($indiceBrut % 2 == 0) ? 'colle-gauche gauche' : 'gauche';
					else
						$alignement = '';
					
					
					if($finale == 0)
						switch($modulo) {
							case 0:
							case 3:
								$indiceRecalcule = $indiceBrut;
							break;
							case 1:
								$indiceRecalcule = $indiceBrut + 1;
							break;
							case 2:
								$indiceRecalcule = $indiceBrut - 1;
							break;
						}
					else
						$indiceRecalcule = $indiceBrut;
					
					$score = $tableau[$indiceRecalcule]["Pronostics_Score"];
					$scoreAP = $tableau[$indiceRecalcule]["Pronostics_ScoreAP"];
					$vainqueur = $tableau[$indiceRecalcule]["Pronostics_Vainqueur"];
					
					/*	En priorité, on affiche :
						- le vainqueur des TAB (mention TAB à côté du score)
						- le perdant des TAB
						- le score AP
						- le score
					*/
					if($vainqueur) {
						if($vainqueur == $tableau[$indiceRecalcule]["Equipe"])
							$scoreAffiche = $scoreAP . ' TAB';
						else
							$scoreAffiche = $scoreAP;
					}
					else {
						if($scoreAP != null)
							$scoreAffiche = $scoreAP . ' AP';
						else
							$scoreAffiche = $score;
					}
					
					$nomEquipe = $tableau[$indiceRecalcule]["Equipes_Nom"];
					
					// Pour les matches différents de la finale, l'affichage des équipes nécessite un espace vertical pour séparer les groupes de matches
					// On le fait tous les 4 matches
					if($indiceBrut % 4 == 0 && $indiceBrut != 0 && $finale == 0)
						echo '<div class="colle-gauche gauche ' . $classeEspacementVertical . '"></div>';
						
					// Pour la finale et la petite finale, on insère un espacement vertical
					if($indiceBrut % 2 == 0 && $indiceBrut && $finale)
						echo '<div class="' . $classeEspacementVertical . '"></div>';
						
					if($tableau[$indiceRecalcule]["Pronostiquable"] == 1)
						echo '<div class="' . $classeEquipe . ' ' . $alignement . '">';
					else
						echo '<div class="' . $classeEquipe . ' ' . $alignement . '">';
						if($tableau[$indiceRecalcule]["Equipes_Fanion"])
							echo '<span><img class="fanion" src="images/equipes/' .  $tableau[$indiceRecalcule]["Equipes_Fanion"] . '" alt="" /></span>';
						else
							echo '<span><img class="fanion" src="images/equipes/_inconnu.png" alt="" /></span>';
						echo '<span class="nomEquipe">' . $nomEquipe . '</span>';
						
						echo '<span class="score">' . $scoreAffiche . '</span>';
					echo '</div>';

					if($indiceBrut % 2 == 0 && $classeEspacement)
						echo '<div class="gauche ' . $classeEspacement . '"></div>';
				}
			}
		echo '</div>';
	}
	
	// Fonction d'affichage du podium
	function afficherPodium($bdd, $tableau, $numeroMatchMin, $numeroMatchMax, $nomDiv, $pronostiqueurConsulte) {
		$ordreSQL =		'	SELECT		equipe_vainqueur_finale.Equipes_Nom AS Vainqueur_Finale' .
						'				,equipe_perdant_finale.Equipes_Nom AS Perdant_Finale' .
						'				,equipe_vainqueur_petite_finale.Equipes_Nom AS Vainqueur_Petite_Finale' .
						'	FROM		(' .
						'					SELECT		DISTINCT cdm_fn_vainqueur(' . $pronostiqueurConsulte . ', ' . $numeroMatchMin . ') AS Vainqueur_Finale' .
						'								,cdm_fn_perdant(' . $pronostiqueurConsulte . ', ' . $numeroMatchMin . ') AS Perdant_Finale' .
						'								,cdm_fn_vainqueur(' . $pronostiqueurConsulte . ', ' . $numeroMatchMax . ') AS Vainqueur_Petite_Finale' .
						'				) podium' .
						'	LEFT JOIN	cdm_equipes equipe_vainqueur_finale' .
						'				ON		podium.Vainqueur_Finale = equipe_vainqueur_finale.Equipe' .
						'	LEFT JOIN	cdm_equipes equipe_perdant_finale' .
						'				ON		podium.Perdant_Finale = equipe_perdant_finale.Equipe' .
						'	LEFT JOIN	cdm_equipes equipe_vainqueur_petite_finale' .
						'				ON		podium.Vainqueur_Petite_Finale = equipe_vainqueur_petite_finale.Equipe';
		$req = $bdd->query($ordreSQL);
		$podium = $req->fetchAll();
		
		$nomVainqueurFinale = $podium[0]["Vainqueur_Finale"];
		$nomPerdantFinale = $podium[0]["Perdant_Finale"];
		$nomVainqueurPetiteFinale = $podium[0]["Vainqueur_Petite_Finale"];
		
		echo '<div id="' . $nomDiv . '">';
			echo '<label>' . $nomVainqueurFinale . '</label><br />';
			echo '<label>' . $nomPerdantFinale . '</label><br />';
			echo '<label>' . $nomVainqueurPetiteFinale . '</label>';
		echo '</div>';

	}
	
	// Fonction d'affichage de tous les tableaux (1/8 à la finale) ainsi que le podium et le choix du meilleur buteur
	function afficherTableaux($bdd, $tableau, $pronostiqueurConsulte) {
		afficherTableau($bdd, $tableau, 1, 8, 'divTableau1_8', 'equipe1_8', 'espacement1_8', 'espacementVertical1_8', 0, $pronostiqueurConsulte);
		afficherTableau($bdd, $tableau, 9, 12, 'divTableau1_4', 'equipe1_4', 'espacement1_4', 'espacementVertical1_4', 0, $pronostiqueurConsulte);
		afficherTableau($bdd, $tableau, 13, 14, 'divTableau1_2', 'equipe1_2', 'espacement1_2', 'espacementVertical1_2', 0, $pronostiqueurConsulte);
		afficherTableau($bdd, $tableau, 15, 16, 'divTableau1_1', 'equipe1_1', '', 'espacementVertical1_1', 1, $pronostiqueurConsulte);
		afficherPodium($bdd, $tableau, 15, 16, 'divPodium', $pronostiqueurConsulte);
	}
	
?>
