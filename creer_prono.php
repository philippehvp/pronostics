<?php
	include('commun.php');
	include('fonctions.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include('commun_entete.php');
?>
</head>

<body>
	<?php
		$nomPage = 'creer_prono.php';
		enregistrerConsultationPage($bdd, $nomPage);
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';

		// Affichage des informations de l'équipe (nom, fanion, etc.)
		// Le paramètre domicileVisiteur indique s'il s'agit de l'équipe domicile ou visiteur
		function afficherEquipe($unMatch, $domicileVisiteur) {
			$nomEquipe = $domicileVisiteur == 1 ? $unMatch["EquipesDomicile_Nom"] : $unMatch["EquipesVisiteur_Nom"];
			$fanion = $domicileVisiteur == 1 ? $unMatch["EquipesDomicile_Fanion"] : $unMatch["EquipesVisiteur_Fanion"];
			if($fanion == null)
				$fanion = '_inconnu.png';
			$coteEquipe = $domicileVisiteur == 1 ? calculerCote($unMatch["Matches_CoteEquipeDomicile"]) : calculerCote($unMatch["Matches_CoteEquipeVisiteur"]);
		
			echo '<label>' . $nomEquipe . '</label>';
			echo '<br />';
			echo '<img src="images/equipes/' . $fanion . '" alt="Fanion" />';
		}
		
		// Affichage des informations du match (type de match, horaires, etc.)
		function afficherLogistique($unMatch) {
			setlocale (LC_TIME, 'fr_FR.utf8','fra');
			$dateDebut = $unMatch["Matches_Date"] != null ? date("d/m/Y", strtotime($unMatch["Matches_Date"])) : date("d/m/Y");
			$jourDebut = $unMatch["Matches_Date"] != null ? date("w", strtotime($unMatch["Matches_Date"])) : date("w");
			$jourSemaineDebut = strtolower(jourSemaine($jourDebut));
			$heureDebut = $unMatch["Matches_Heure"] != null ? sprintf('%02u', $unMatch["Matches_Heure"]) : sprintf('%02u', date("G"));
			$minuteDebut = $unMatch["Matches_Minute"] != null ? sprintf('%02u', $unMatch["Matches_Minute"]) : sprintf('%02u', (date("i") + 5 - (date("i") % 5)));
			
			$dateMax = $unMatch["Matches_DateMax"] != null ? date("d/m/Y", strtotime($unMatch["Matches_DateMax"])) : null;
			$jourMax = $unMatch["Matches_DateMax"] != null ? date("w", strtotime($unMatch["Matches_DateMax"])) : date("w");
			$jourSemaineMax = strtolower(jourSemaine($jourMax));
			$heureMax = $unMatch["Matches_HeureMax"] != null ? sprintf('%02u', $unMatch["Matches_HeureMax"]) : null;
			$minuteMax = $unMatch["Matches_MinuteMax"] != null ? sprintf('%02u', $unMatch["Matches_MinuteMax"]) : null;

			if($dateDebut == $dateMax)
				echo '<label>Match et limite de pronostic le ' . $jourSemaineDebut . ' ' . $dateDebut . ' à ' . $heureDebut . 'h' . $minuteDebut . '</label>';
			else {
				echo '<label>Match le ' . $jourSemaineDebut . ' ' . $dateDebut . ' à ' . $heureDebut . 'h' . $minuteDebut . '</label>';
				echo '<label class="texte-rouge"> (pronostic avant le ' . $jourSemaineMax . ' ' . $dateMax . ' à ' . $heureMax . 'h' . $minuteMax . ')</label>';
			}
			
			if($unMatch["Matches_L1EuropeNom"] != null)
				echo '<br /><label class="matchEuropeen">' . $unMatch["Matches_L1EuropeNom"] . '</label>';
		}
		
		// Affichage des cotes de l'équipe (et du nul)
		// On affiche les points de qualification et non les cotes dans les deux cas spécifique suivants :
		// - Coupe de France (championnat numéro 5)
		// - en finale de coupe européenne
		function afficherCote($unMatch, $nulDomicileVisiteur, $finaleEuropeenne) {
			if($unMatch["Championnats_Championnat"] != 5 && $finaleEuropeenne == 0) {
				if($nulDomicileVisiteur == 0)
					$cote = calculerCote($unMatch["Matches_CoteNul"]);
				else if($nulDomicileVisiteur == 1)
					$cote = calculerCote($unMatch["Matches_CoteEquipeDomicile"]);
				else if($nulDomicileVisiteur == 2)
					$cote = calculerCote($unMatch["Matches_CoteEquipeVisiteur"]);
				
				if($nulDomicileVisiteur == 0)
					echo '<label>Cote du nul : ' . $cote . '</label>';
				else
					echo '<label>Cote victoire : ' . $cote . '</label>';
			}
			else {
				// Uniquement pour la Coupe de France
				if($nulDomicileVisiteur == 1)
					$cote = $unMatch["Matches_PointsQualificationEquipeDomicile"];
				else if($nulDomicileVisiteur == 2)
					$cote = $unMatch["Matches_PointsQualificationEquipeVisiteur"];
				
				echo '<label>Points qualification : ' . $cote . '</label>';
			}
		}

		// Affichage des cotes de qualification
		// Uniquement pour les matches retour de confrontation directe
		function afficherCoteQualification($unMatch, $nulDomicileVisiteur) {
			if($nulDomicileVisiteur == 1)
				$coteQualification = $unMatch["Matches_PointsQualificationEquipeDomicile"];
			else if($nulDomicileVisiteur == 2)
				$coteQualification = $unMatch["Matches_PointsQualificationEquipeVisiteur"];
			
			echo '<label>Points qualification : ' . $coteQualification . '</label>';
		}

		
		// Affichage des scores de l'équipe (ainsi que les buteurs)
		function afficherScoreEquipe($unMatch, $domicileVisiteur, $matchLie) {
			$disabled = $unMatch["Matches_Pronostiquable"] == 0 ? ' disabled' : '';
			$typeEquipe = $domicileVisiteur == 1 ? 'D' : 'V';
			
			if($domicileVisiteur == 1)
				$scoreEquipe = $unMatch["Pronostics_ScoreEquipeDomicile"] != null ? $unMatch["Pronostics_ScoreEquipeDomicile"] : -1;
			else
				$scoreEquipe = $unMatch["Pronostics_ScoreEquipeVisiteur"] != null ? $unMatch["Pronostics_ScoreEquipeVisiteur"] : -1;

			echo '<select onchange="creerProno_sauvegarderPronostic(this, \'score\', ' . $unMatch["Match"] . ', \'' . $typeEquipe . '\', ' . $matchLie . ');" id="selectButs' . $typeEquipe . '_match_' . $unMatch["Match"] . '"' . $disabled . '>';
				for($i = -1; $i <= 15; $i++) {
					$selected = $i == $scoreEquipe ? ' selected="selected"' : '';
					echo '<option value="' . $i . '"' . $selected . '>' . ($i == -1 ? 'Score' : $i) . '</option>';
				}
			echo '</select>';
		}
		
		function afficherButeurs($unMatch, $domicileVisiteur, $buteurs) {
			$disabled = ($unMatch["Matches_SansButeur"] == 1 || $unMatch["Buteurs_Pronostiquables"] == 0) ? ' disabled' : '';
			$typeEquipe = $domicileVisiteur == 1 ? 'D' : 'V';
			if($domicileVisiteur == 1)		$equipe = isset($unMatch["EquipesDomicile_Equipe"]) ? $unMatch["EquipesDomicile_Equipe"] : 0;
			else							$equipe = isset($unMatch["EquipesVisiteur_Equipe"]) ? $unMatch["EquipesVisiteur_Equipe"] : 0;

			echo '<button type="button" id="btnButeurs' . $typeEquipe . '_match_' . $unMatch["Match"] . '" onclick="creerProno_pronostiquerButeurs(this, \'' . $typeEquipe . '\', ' . $unMatch["Match"] . ', ' . $equipe . ');"' . $disabled . '>Buteurs</button>';

			// Parcours des buteurs
			$listeButeurs = '';
			foreach($buteurs as $buteur) {
				if($buteur["Matches_Match"] == $unMatch["Match"] && $buteur["Equipes_Equipe"] == $equipe) {
					if($buteur["Buteurs_NombreButs"] == 1)
						$listeButeurs .= $buteur["Joueurs_NomComplet"] . ', ';
					else
						$listeButeurs .= $buteur["Joueurs_NomComplet"] . ' (x' . $buteur["Buteurs_NombreButs"] . '), ';
					
				}
			}
			
			if($listeButeurs != '')
				$listeButeurs = substr($listeButeurs, 0, strlen($listeButeurs) - 2);
			
			echo '<br />';
			echo '<span style="overflow: scroll;">';
				if($listeButeurs != '')
					echo '<label id="labelButeurs' . $typeEquipe .'_match_' . $unMatch["Match"] . '" class="buteurs">' . $listeButeurs . '</label>';
				else
					echo '<label id="labelButeurs' . $typeEquipe .'_match_' . $unMatch["Match"] . '" class="buteurs">Aucun</label>';
			echo '</span>';
		}
		
		// Affichage des scores AP de l'équipe
		function afficherScoreAPEquipe($unMatch, $domicileVisiteur, $typeMatch, $matchRetour, $matchLie, $pronostiqueur, $bdd) {
			// Scores AP
			// On suppose que l'on n'affiche pas la zone de saisie des scores AP
			$style = ' style="visibility: hidden;"';
			$disabled = $unMatch["Matches_Pronostiquable"] == 0 ? ' disabled' : '';
			$typeEquipe = $domicileVisiteur == 1 ? 'D' : 'V';
			
			$pronostics_ScoreEquipeDomicile = $unMatch["Pronostics_ScoreEquipeDomicile"] != null ? $unMatch["Pronostics_ScoreEquipeDomicile"] : -1;
			$pronostics_ScoreEquipeVisiteur = $unMatch["Pronostics_ScoreEquipeVisiteur"] != null ? $unMatch["Pronostics_ScoreEquipeVisiteur"] : -1;
			$pronostics_ScoreAPEquipeDomicile = $unMatch["Pronostics_ScoreAPEquipeDomicile"] != null ? $unMatch["Pronostics_ScoreAPEquipeDomicile"] : -1;
			$pronostics_ScoreAPEquipeVisiteur = $unMatch["Pronostics_ScoreAPEquipeVisiteur"] != null ? $unMatch["Pronostics_ScoreAPEquipeVisiteur"] : -1;
			$pronosticsLies_ScoreEquipeDomicile = $unMatch["PronosticsLies_ScoreEquipeDomicile"] != null ? $unMatch["PronosticsLies_ScoreEquipeDomicile"] : -1;
			$pronosticsLies_ScoreEquipeVisiteur = $unMatch["PronosticsLies_ScoreEquipeVisiteur"] != null ? $unMatch["PronosticsLies_ScoreEquipeVisiteur"] : -1;
			$pronosticsLies_ScoreAPEquipeDomicile = $unMatch["PronosticsLies_ScoreAPEquipeDomicile"] != null ? $unMatch["PronosticsLies_ScoreAPEquipeDomicile"] : -1;
			$pronosticsLies_ScoreAPEquipeVisiteur = $unMatch["PronosticsLies_ScoreAPEquipeVisiteur"] != null ? $unMatch["PronosticsLies_ScoreAPEquipeVisiteur"] : -1;

			// Sauf dans certains cas
			if($typeMatch == 3) {
				// Match retour d'une confrontation aller-retour
				// Si tous les 4 scores (match aller et retour) ont été saisis, on les compare pour voir s'il faut afficher les scores AP ou non
				if($pronostics_ScoreEquipeDomicile != -1 && $pronostics_ScoreEquipeVisiteur != -1 && $pronosticsLies_ScoreEquipeDomicile != -1 && $pronosticsLies_ScoreEquipeVisiteur != -1)
					if($pronostics_ScoreEquipeDomicile == $pronosticsLies_ScoreEquipeDomicile && $pronostics_ScoreEquipeVisiteur == $pronosticsLies_ScoreEquipeVisiteur)
						$style = '';
			}
			else if($typeMatch == 4) {
				// Match seul avec prolongation (match de Coupe de France, finale européenne)
				if($pronostics_ScoreEquipeDomicile != -1 && $pronostics_ScoreEquipeVisiteur != -1)
					if($pronostics_ScoreEquipeDomicile == $pronostics_ScoreEquipeVisiteur)
						$style = '';
			
				// Arrivé ici, on va peut-être afficher les scores AP
				// Si ceux-ci n'ont jamais été saisis, il faut les pré-remplir avec le score du match retour
				// Et faire la mise à jour en base de données
				if($pronostics_ScoreEquipeDomicile != -1 && $pronostics_ScoreEquipeVisiteur != -1 && $pronostics_ScoreEquipeDomicile == $pronostics_ScoreEquipeVisiteur && ($pronostics_ScoreAPEquipeDomicile == -1 || $pronostics_ScoreAPEquipeVisiteur == -1)) {
					$pronostics_ScoreAPEquipeDomicile = $pronostics_ScoreEquipeDomicile;
					$pronostics_ScoreAPEquipeVisiteur = $pronostics_ScoreEquipeVisiteur;
					
					$ordreSQL =		'	UPDATE		pronostics' .
									'	SET			Pronostics_ScoreAPEquipeDomicile = Pronostics_ScoreEquipeDomicile' .
									'				,Pronostics_ScoreAPEquipeVisiteur = Pronostics_ScoreEquipeVisiteur' .
									'				,Pronostics_DateMAJ = NOW()' .
									'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
									'				AND		Matches_Match = ' . $matchRetour;
					$bdd->exec($ordreSQL);
				}
			}
		
			$scoreAPEquipe = $domicileVisiteur == 1 ? $pronostics_ScoreAPEquipeDomicile : $pronostics_ScoreAPEquipeVisiteur;
			$scoreAPMinimumEquipe = $domicileVisiteur == 1 ? $pronostics_ScoreEquipeDomicile : $pronostics_ScoreEquipeVisiteur;
			echo '<span id="spanProlongation' . $typeEquipe . '_match_' . $matchRetour . '"' . $style . '>';
				echo '<select onchange="creerProno_sauvegarderPronostic(this, \'scoreAP\', ' . $unMatch["Match"] . ', \'' . $typeEquipe . '\', ' . $matchLie . ');" id="selectButsAP' . $typeEquipe . '_match_' . $unMatch["Match"] . '"' . $disabled . '>';
					for($i = $scoreAPMinimumEquipe; $i <= 15; $i++) {
						$selected = $i == $scoreAPEquipe ? ' selected="selected"' : '';
						echo '<option value="' . $i . '"' . $selected . '>' . ($i == -1 ? 'Score' : $i) . '</option>';
					}
				echo '</select>';
			echo '</span>';
		}
		
		// Affichage de la zone TAB
		function afficherTAB($unMatch, $typeMatch, $matchLie) {
			// TAB
			$style = ' style="visibility: hidden;"';
			$disabled = $unMatch["Matches_Pronostiquable"] == 0 ? ' disabled' : '';
			
			$pronostics_ScoreEquipeDomicile = $unMatch["Pronostics_ScoreEquipeDomicile"] != null ? $unMatch["Pronostics_ScoreEquipeDomicile"] : -1;
			$pronostics_ScoreEquipeVisiteur = $unMatch["Pronostics_ScoreEquipeVisiteur"] != null ? $unMatch["Pronostics_ScoreEquipeVisiteur"] : -1;
			$pronostics_ScoreAPEquipeDomicile = $unMatch["Pronostics_ScoreAPEquipeDomicile"] != null ? $unMatch["Pronostics_ScoreAPEquipeDomicile"] : -1;
			$pronostics_ScoreAPEquipeVisiteur = $unMatch["Pronostics_ScoreAPEquipeVisiteur"] != null ? $unMatch["Pronostics_ScoreAPEquipeVisiteur"] : -1;
			$pronosticsLies_ScoreEquipeDomicile = $unMatch["PronosticsLies_ScoreEquipeDomicile"] != null ? $unMatch["PronosticsLies_ScoreEquipeDomicile"] : -1;
			$pronosticsLies_ScoreEquipeVisiteur = $unMatch["PronosticsLies_ScoreEquipeVisiteur"] != null ? $unMatch["PronosticsLies_ScoreEquipeVisiteur"] : -1;
			$pronostics_Vainqueur = $unMatch["Pronostics_Vainqueur"];


			if($typeMatch == 3) {
				// Match retour d'une confrontation aller-retour
				// Si tous les 6 scores (aller, retour 90ème et AP) ont été saisis, on les compare pour voir s'il faut afficher les scores AP ou non
				if($pronosticsLies_ScoreEquipeDomicile != 1 && $pronosticsLies_ScoreEquipeVisiteur != -1 && $pronostics_ScoreEquipeDomicile != -1 && $pronostics_ScoreEquipeVisiteur != -1 && $pronostics_ScoreAPEquipeDomicile != -1 && $pronostics_ScoreAPEquipeVisiteur != -1)
					if($pronosticsLies_ScoreEquipeDomicile == $pronostics_ScoreEquipeDomicile && $pronosticsLies_ScoreEquipeVisiteur == $pronostics_ScoreEquipeVisiteur && $pronostics_ScoreEquipeDomicile == $pronostics_ScoreAPEquipeDomicile && $pronostics_ScoreEquipeVisiteur == $pronostics_ScoreAPEquipeVisiteur) {
						$style = '';
					}
			}
			else if($typeMatch == 4) {
				// Match seul avec prolongation (match de Coupe de France, finale de la LDC)
				/*echo '$pronostics_ScoreEquipeDomicile = ' . $pronostics_ScoreEquipeDomicile . '<br />';
				echo '$pronostics_ScoreEquipeVisiteur = ' . $pronostics_ScoreEquipeVisiteur . '<br />';
				echo '$pronostics_ScoreAPEquipeDomicile = ' . $pronostics_ScoreAPEquipeDomicile . '<br />';
				echo '$pronostics_ScoreAPEquipeVisiteur = ' . $pronostics_ScoreAPEquipeVisiteur . '<br />';*/
				
				if($pronostics_ScoreEquipeDomicile != -1 && $pronostics_ScoreEquipeVisiteur != -1)
					if($pronostics_ScoreEquipeDomicile == $pronostics_ScoreEquipeVisiteur)
						if($pronostics_ScoreAPEquipeDomicile != -1 && $pronostics_ScoreAPEquipeVisiteur != -1)
							if($pronostics_ScoreAPEquipeDomicile == $pronostics_ScoreAPEquipeVisiteur)
								$style = '';
			}
			else if($typeMatch == 5) {
				 // Match de la Community Shield
				 if($pronostics_ScoreEquipeDomicile != -1 && $pronostics_ScoreEquipeVisiteur != -1)
					if($pronostics_ScoreEquipeDomicile == $pronostics_ScoreEquipeVisiteur)
						$style = '';
			}

			echo '<span id="spanVainqueur_match_' . $unMatch["Match"] . '"' . $style . '>';
				echo '<select class="liste-vainqueur-tab" onchange="modifierCouleur(this, 0, \'blanc-fond-rouge\'); creerProno_sauvegarderPronostic(this, \'vainqueur\',' . $unMatch["Match"] . ', \'\',' . $matchLie . ');" id="selectVainqueur_match_' . $unMatch["Match"] . '"' . $disabled . '>';
					$selected = $pronostics_Vainqueur != 1 && $pronostics_Vainqueur != 2 ? ' selected="selected"' : '';
					echo '<option value="0"' . $selected . '>Vainqueur</option>';

					$selected = $pronostics_Vainqueur == 1 ? ' selected="selected"' : '';
					echo '<option value="1"' . $selected . '>' . $unMatch["EquipesDomicile_Nom"] . '</option>';
					
					$selected = $pronostics_Vainqueur == 2 ? ' selected="selected"' : '';
					echo '<option value="2"' . $selected . '>' . $unMatch["EquipesVisiteur_Nom"] . '</option>';
				echo '</select>';
			echo '</span>';

		}
		
		// Page de saisie des pronostics
		
		// Liste des journées
		
		
		// Ajout des spécificités de la Coupe de France :
		// - un joueur peut être exempté de faire une journée
		// - un joueur ayant été éliminé de la compétition ne peut plus pronostiquer
		
		$ordreSQL =		'	SELECT			DISTINCT Journee, journees.Championnats_Championnat, Championnats_Nom, Journees_Nom' .
						'	FROM			matches' .
						'	JOIN			journees' .
						'					ON		matches.Journees_Journee = journees.Journee' .
						'	JOIN			championnats' .
						'					ON		journees.Championnats_Championnat = championnats.Championnat' .
						'	JOIN			inscriptions' .
						'					ON		journees.Championnats_Championnat = inscriptions.Championnats_Championnat' .
						'	WHERE			journees.Journees_Active = 1' .
						'					AND		inscriptions.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
						'					AND		championnats.Championnat <> 5' .
						'	UNION ALL' .
						'	SELECT			DISTINCT journees.Journee, journees.Championnats_Championnat, Championnats_Nom, journees.Journees_Nom' .
						'	FROM			confrontations' .
						'	JOIN			(' .
						'						SELECT			Championnats_Championnat, Journee, Journees_Nom' .
						'						FROM			journees' .
						'						WHERE			journees.Journees_Active = 1' .
						'										AND		journees.Championnats_Championnat = 5' .
						'					) journees' .
						'					ON		confrontations.Journees_Journee = journees.Journee' .
						'	JOIN			inscriptions' .
						'					ON		journees.Championnats_Championnat = inscriptions.Championnats_Championnat' .
						'							AND		(	confrontations.Pronostiqueurs_PronostiqueurA = inscriptions.Pronostiqueurs_Pronostiqueur' .
						'										OR		confrontations.Pronostiqueurs_PronostiqueurB = inscriptions.Pronostiqueurs_Pronostiqueur' .
						'									)' .
						'	JOIN			championnats' .
						'					ON		journees.Championnats_Championnat = championnats.Championnat' .
						'	WHERE			confrontations.Confrontations_ConfrontationReelle = 1' .
						'					AND		inscriptions.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur;

		$req = $bdd->query($ordreSQL);
		$journees = $req->fetchAll();
		
		// Liste des matches
		// Spécificité des coupes européennes : dans le cas d'un match retour d'une confrontation directe, la date max de pronostic est celle du match aller
		$ordreSQL =		'	SELECT DISTINCT			journees.Journee, journees.Journees_Nom, journees.Championnats_Championnat, matches.Match, matches.Matches_Nom' .
						'							,matches.Matches_AvecProlongation, matches.Matches_MatchLie' .
						'							,matches.Matches_Date, HOUR(matches.Matches_Date) AS Matches_Heure, MINUTE(matches.Matches_Date) AS Matches_Minute' .
						'							,CASE' .
						'								WHEN	matches.Matches_AvecProlongation = 1 AND matches.Matches_MatchLie <> 0' .
						'								THEN	matches_lies.Matches_Date' .
						'								ELSE	matches.Matches_Date' .
						'							END AS Matches_DateMax' .
						'							,CASE' .
						'								WHEN	matches.Matches_AvecProlongation = 1 AND matches.Matches_MatchLie <> 0' .
						'								THEN	HOUR(matches_lies.Matches_Date)' .
						'								ELSE	HOUR(matches.Matches_Date)' .
						'							END AS Matches_HeureMax' .
						'							,CASE' .
						'								WHEN	matches.Matches_AvecProlongation = 1 AND matches.Matches_MatchLie <> 0' .
						'								THEN	MINUTE(matches_lies.Matches_Date)' .
						'								ELSE	MINUTE(matches.Matches_Date)' .
						'							END AS Matches_MinuteMax' .
						'							,matches.Matches_MatchCS' .
						'							,equipes_domicile.Equipe AS EquipesDomicile_Equipe, equipes_visiteur.Equipe AS EquipesVisiteur_Equipe' .
						'							,equipes_domicile.Equipes_Nom AS EquipesDomicile_Nom, equipes_visiteur.Equipes_Nom AS EquipesVisiteur_Nom' .
						'							,equipes_domicile.Equipes_Fanion AS EquipesDomicile_Fanion, equipes_visiteur.Equipes_Fanion AS EquipesVisiteur_Fanion' .
						'							,matches.Matches_CoteEquipeDomicile, matches.Matches_CoteNul, matches.Matches_CoteEquipeVisiteur' .
						'							,matches.Matches_PointsQualificationEquipeDomicile, matches.Matches_PointsQualificationEquipeVisiteur' .
						'							,pronostics.Pronostics_ScoreEquipeDomicile, pronostics.Pronostics_ScoreEquipeVisiteur' .
						'							,pronostics.Pronostics_ScoreAPEquipeDomicile, pronostics.Pronostics_ScoreAPEquipeVisiteur' .
						'							,pronostics.Pronostics_Vainqueur' .
						'							,(SELECT Pronostics_ScoreEquipeDomicile FROM pronostics WHERE Pronostiqueurs_pronostiqueur = ' . $pronostiqueur . ' AND Matches_Match = matches.Matches_MatchLie) AS PronosticsLies_ScoreEquipeDomicile' .
						'							,(SELECT Pronostics_ScoreEquipeVisiteur FROM pronostics WHERE Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' AND Matches_Match = matches.Matches_MatchLie) AS PronosticsLies_ScoreEquipeVisiteur' .
						'							,(SELECT Pronostics_ScoreAPEquipeDomicile FROM pronostics WHERE Pronostiqueurs_pronostiqueur = ' . $pronostiqueur . ' AND Matches_Match = matches.Matches_MatchLie) AS PronosticsLies_ScoreAPEquipeDomicile' .
						'							,(SELECT Pronostics_ScoreAPEquipeVisiteur FROM pronostics WHERE Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' AND Matches_Match = matches.Matches_MatchLie) AS PronosticsLies_ScoreAPEquipeVisiteur' .
						'							,fn_matchpronostiquable(matches.Match, ' . $pronostiqueur . ') AS Matches_Pronostiquable' .
						'							,CASE' .
						'								WHEN	matches.Matches_Date > NOW() AND (pronostics_carrefinal.PronosticsCarreFinal_Coefficient IS NULL OR pronostics_carrefinal.PronosticsCarreFinal_Coefficient <> 0)' .
						'								THEN	1' .
						'								ELSE	0' .
						'							END AS Buteurs_Pronostiquables' .
						'							,CASE' .
						'								WHEN	IFNULL(matches.Matches_SansButeur, 0) = 1' .
						'								THEN	1' .
						'								ELSE	0' .
						'							END AS Matches_SansButeur' .
						'							,matches.Matches_L1EuropeNom' .
						'							,matches.Matches_L1Europe' .
						'							,matches.Matches_Coefficient' .
						'							,CASE' .
						'								WHEN	matches.Matches_DemiFinaleEuropeenne = 1 OR matches.Matches_FinaleEuropeenne = 1' .
						'								THEN	1' .
						'								ELSE	0' .
						'							END AS Afficher_CoefficientCarreFinal' .
						'							,PronosticsCarreFinal_Coefficient' .
						'							,IFNULL(matches.Matches_FinaleEuropeenne, 0) AS Matches_FinaleEuropeenne' .
						'	FROM					matches' .
						'	JOIN					journees' .
						'							ON		matches.Journees_Journee = journees.Journee' .
						'	JOIN					inscriptions' .
						'							ON		journees.Championnats_Championnat = inscriptions.Championnats_Championnat' .
						'	LEFT JOIN				equipes AS equipes_domicile' .
						'							ON		matches.Equipes_EquipeDomicile = equipes_domicile.Equipe' .
						'	LEFT JOIN				equipes AS equipes_visiteur' .
						'							ON		matches.Equipes_EquipeVisiteur = equipes_visiteur.Equipe' .
						'	LEFT JOIN				pronostics' .
						'							ON		matches.Match = pronostics.Matches_Match' .
						'							AND		inscriptions.Pronostiqueurs_Pronostiqueur = pronostics.Pronostiqueurs_Pronostiqueur' .
						'	LEFT JOIN				matches matches_lies' .
						'							ON		matches.Matches_MatchLie = matches_lies.Match' .
						'	LEFT JOIN				pronostics_carrefinal' .
						'							ON		matches.Match = pronostics_carrefinal.Matches_Match' .
						'									AND		inscriptions.Pronostiqueurs_Pronostiqueur = pronostics_carrefinal.Pronostiqueurs_Pronostiqueur' .
						'	WHERE					journees.Journees_Active = 1' .
						'							AND		inscriptions.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
						'							AND		matches.Equipes_EquipeDomicile IS NOT NULL' .
						'							AND		matches.Equipes_EquipeVisiteur IS NOT NULL' .
						'	ORDER BY				CASE' .
						'								WHEN	journees.Championnats_Championnat IN (1, 5)' .
						'								THEN	matches.Matches_Date' .
						'							END' .
						'							,matches.Match';

		$req = $bdd->query($ordreSQL);
		$matches = $req->fetchAll();
		
		$ordreSQL =		'	SELECT			CONCAT(joueurs.Joueurs_NomFamille, \' \', IFNULL(joueurs.Joueurs_Prenom, \'\')) AS Joueurs_NomComplet, pronostics_buteurs.Matches_Match, joueurs_equipes.Equipes_Equipe, COUNT(*) AS Buteurs_NombreButs' .
						'	FROM			pronostics_buteurs' .
						'	JOIN			joueurs_equipes' .
						'					ON		pronostics_buteurs.Joueurs_Joueur = joueurs_equipes.Joueurs_Joueur' .					
						'	JOIN			joueurs' .
						'					ON		joueurs_equipes.Joueurs_Joueur = joueurs.Joueur' .
						'	JOIN			matches' .
						'					ON		pronostics_buteurs.Matches_Match = matches.Match' .
						'	JOIN			journees' .
						'					ON		matches.Journees_Journee = journees.Journee' .
						'	JOIN			inscriptions' .
						'					ON		journees.Championnats_Championnat = inscriptions.Championnats_Championnat' .
						'					AND		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
						'	WHERE			journees.Journees_Active = 1' .
						'					AND		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
						'					AND		JoueursEquipes_Debut <= matches.Matches_Date' .
						'					AND		(JoueursEquipes_Fin IS NULL OR JoueursEquipes_Fin > matches.Matches_Date)' .
						'					AND		matches.Equipes_EquipeDomicile IS NOT NULL' .
						'					AND		matches.Equipes_EquipeVisiteur IS NOT NULL' .
						'	GROUP BY		joueurs.Joueur, pronostics_buteurs.Matches_Match, joueurs_equipes.Equipes_Equipe';

		$req = $bdd->query($ordreSQL);
		$buteurs = $req->fetchAll();

		// Parcours des championnats et des journées
		echo '<div class="conteneur">';
			include('bandeau.php');
			echo '<div id="divPronostics" class="contenu-page">';
				echo '<ul class="ulNavigation"></ul>';
				
				foreach($journees as $journee) {
					echo '<div id="divPronostics-' . $journee["Journee"] . '" class="journee" title="' . $journee["Championnats_Nom"] . ' - ' . $journee["Journees_Nom"] . '">';
						// Parcours des matches
						$classe = 'pair';
						foreach($matches as $unMatch) {
							if($unMatch["Journee"] != $journee["Journee"])
								continue;
							
							$match = $unMatch["Match"];
							$matchLie = $unMatch["Matches_MatchLie"] != null ? $unMatch["Matches_MatchLie"] : 0;
							$matchCS = $unMatch["Matches_MatchCS"] != null ? $unMatch["Matches_MatchCS"] : 0;
							$matchAP = $unMatch["Matches_AvecProlongation"] != null ? $unMatch["Matches_AvecProlongation"] : 0;
							$matchRetour = $match > $matchLie ? $match: $matchLie;

							$classeMatch = '';
							if($matchCS == 1)									{	$typeMatch = 5; $classeMatch = 'matchCS';		}
							else if($matchAP == 0 && $matchLie == 0)			{	$typeMatch = 1; $classeMatch = 'matchLigue1';	}
							else if($matchAP == 0 && $matchLie != 0)			{	$typeMatch = 2; $classeMatch = 'matchAller';	}
							else if($matchAP == 1 && $matchLie != 0)			{	$typeMatch = 3; $classeMatch = 'matchRetour';	}
							else												{	$typeMatch = 4; $classeMatch = 'matchCoupe';	}
							if($matchLie < $match)
								$matchLie = $match;
							
							$finaleEuropeenne = $unMatch["Matches_FinaleEuropeenne"] != null ? $unMatch["Matches_FinaleEuropeenne"] : 0;
							
							// Pronostic
							// Changement de couleur de fond si l'on passe d'un match à l'autre (sauf dans le cas d'un match retour d'une confrontation directe)
							if($typeMatch != 3)
								$classe = $classe == 'pair' ? 'impair' : 'pair';
							
							if($unMatch["Matches_Coefficient"] == 2)	echo '<div id="match' . $match . '" class="' . $classe . ' tuile match-canal">';
							else										echo '<div id="match' . $match . '" class="' . $classe . ' tuile">';
								// Informations sur le match (nom du match, horaires, etc.)
								echo '<div id="divInformations_match_' . $match . '" class="pronosticLogistique">';
									afficherLogistique($unMatch);
								echo '</div>';
							
								// Informations sur l'équipe domicile
								echo '<div id="divEquipeDomicile_match_' . $match . '" class="pronosticEquipe gauche">';
									afficherEquipe($unMatch, 1);
								echo '</div>';
								
								echo '<div class="pronosticZoneScore gauche">';
									if($unMatch["Afficher_CoefficientCarreFinal"] == 1) {
										if($unMatch["PronosticsCarreFinal_Coefficient"] == 0)
											echo '<label class="non-pronostiquable texte-rouge">Non pronostiquable</label>';
										else
											echo '<label>Score (x' . $unMatch["PronosticsCarreFinal_Coefficient"] . ')</label>';
									}
									else
										echo '<label>Score</label>';
									if($unMatch["Championnats_Championnat"] == 1 && $unMatch["Matches_L1Europe"] != 1) {
										echo '<label>&nbsp;-&nbsp;</label><label class="curseur-main" onclick="creerProno_afficherDerniersResultats(' . $unMatch["Match"] . ');">Statistiques</label>';
									}
									echo '<br />';
								
									// Score de l'équipe domicile
									echo '<div id="divScoreEquipeDomicile_match_' . $match . '" class="pronosticScore gauche">';
										afficherScoreEquipe($unMatch, 1, $matchLie);
									echo '</div>';

									// Score de l'équipe visiteur
									echo '<div id="divScoreEquipeVisiteur_match_' . $match . '" class="pronosticScore gauche">';
										afficherScoreEquipe($unMatch, 2, $matchLie);
									echo '</div>';

									// Les scores AP ne s'affichent que si l'on n'est pas dans les matches de type 1, 2 et 5
									if($typeMatch != 1 && $typeMatch != 2 && $typeMatch != 5) {
										echo '<br />';
										echo '<div id="divScoreAPEquipeDomicile_match_' . $match . '" class="pronosticScoreAP gauche">';
											afficherScoreAPEquipe($unMatch, 1, $typeMatch, $matchRetour, $matchLie, $pronostiqueur, $bdd);
										echo '</div>';
										
										echo '<div id="divScoreAPEquipeVisiteur_match_' . $match . '" class="pronosticScoreAP gauche">';
											afficherScoreAPEquipe($unMatch, 2, $typeMatch, $matchRetour, $matchLie, $pronostiqueur, $bdd);
										echo '</div>';
									}
									
									// Les TAB s'affichent si l'on n'est pas dans les matches de type 1 et 2
									if($typeMatch != 1 && $typeMatch != 2) {
										echo '<br />';
										echo '<div id="divVainqueur_match_' . $match . '" class="pronosticVainqueur gauche">';
											afficherTAB($unMatch, $typeMatch, $matchLie);
										echo '</div>';
									}
								echo '</div>';
								
								// Informations sur l'équipe visiteur
								echo '<div id="divEquipeVisiteur_match_' . $match . '" class="pronosticEquipe gauche">';
									afficherEquipe($unMatch, 2);
								echo '</div>';

								// Cote de l'équipe domicile
								echo '<div id="divCoteEquipeDomicile_match_' . $match . '" class="pronosticCoteEquipe gauche">';
									afficherCote($unMatch, 1, $finaleEuropeenne);
								echo '</div>';

								// Cote du match nul
								echo '<div id="divCoteNul_match_' . $match . '" class="pronosticCoteNul gauche">';
									afficherCote($unMatch, 0, $finaleEuropeenne);
								echo '</div>';

								// Cote de l'équipe visiteur
								echo '<div id="divCoteEquipeVisiteur_match_' . $match . '" class="pronosticCoteEquipe gauche">';
									afficherCote($unMatch, 2, $finaleEuropeenne);
								echo '</div>';
								
								// Cote de qualification pour les match
								if($typeMatch == 3) {
									// Cote de l'équipe domicile
									echo '<div id="divCoteQualificationEquipeDomicile_match_' . $match . '" class="pronosticCoteEquipe gauche">';
										afficherCoteQualification($unMatch, 1);
									echo '</div>';

									// Pas de cote de match nul
									echo '<div class="pronosticCoteNul gauche">';
										echo '&nbsp;';
									echo '</div>';

									// Cote de l'équipe visiteur
									echo '<div id="divCoteQualificationEquipeVisiteur_match_' . $match . '" class="pronosticCoteEquipe gauche">';
										afficherCoteQualification($unMatch, 2);
									echo '</div>';
								}

								// Buteurs de l'équipe domicile
								echo '<div id="divButeursEquipeDomicile_match_' . $match . '" class="pronosticButeurs gauche">';
									afficherButeurs($unMatch, 1, $buteurs);
								echo '</div>';

								// Séparateur vertical score
								echo '<div class="' . $classeMatch . ' gauche"></div>';

								// Buteurs de l'équipe visiteur
								echo '<div id="divButeursEquipeVisiteur_match_' . $match . '" class="pronosticButeurs gauche">';
									afficherButeurs($unMatch, 2, $buteurs);
								echo '</div>';

								//echo '<div class="colle-gauche"></div>';
							echo '</div>';
						} // foreach sur les matches
					echo '</div>';
					
				} // foreach sur les championnats et les journées
				if(count($journees) == 0) {
					echo '<label>Aucun pronostic à effectuer actuellement</label>';
				}
			echo '</div>';

			//include('pied.php');
		echo '</div>';

	?>
	
	<script>
		$(function() {
			afficherTitrePage('divPronostics', 'Saisie de pronostics');
			$('button').button().click	(	function( event ) {
												event.preventDefault();
											}
										);
										
			// Création dynamique des onglets selon le contenu
			$('.journee').each(function() {
				$('.ulNavigation').append('<li><a href="#' + $(this).attr('id') + '">' + $(this).attr('title') + '</a></li>');
			});
			$('#divPronostics').tabs();
			
			$('.ui-tabs-anchor').prepend('<em class="icones icones-grandes">&#10150;</em>');
			
			$('.matchLigue1').each(	function() {	$(this).html("VS");	});
			$('.matchCS').each(		function() {	$(this).html("COMMUNITY SHIELD");	});
			$('.matchAller').each(	function() {	$(this).html("ALLER");	});
			$('.matchRetour').each(	function() {	$(this).html("RETOUR");	});
			$('.matchCoupe').each(	function() {	$(this).html("COUPE");	});
			
			
			if($('.liste-vainqueur-tab').val() == 0)
				$('.liste-vainqueur-tab').addClass('blanc-fond-rouge');

			// Lecture d'une éventuelle ancre (anchor) passée dans l'URL
			var ancre = $.trim(window.location.hash);

			if(ancre) {
				// Recherche de l'ancre dans chacun des onglets (tabs)
				var ongletParent = 0;
				$('.journee').each(function() {
					if($(this).find(ancre).size() > 0) {
						// Lecture de l'onglet (tab) contenant le lien pour l'activer
						ongletParent = $(this).attr('id');
						
						// Sélection du bon onglet
						var index = $('#divPronostics a[href="#' + ongletParent + '"]').parent().index();
						$('#divPronostics').tabs('option', 'active', index);
						
						// Arrêt de la boucle
						return false;
					}
				});
				if(ongletParent == 0) {
					// Lien non trouvé, on active le premier onglet et le premier lien dans la liste de navigation
					$('.journee').hide().first().show();
					$('.ulNavigation li:first').addClass('active');
				}
				else {
					// Décalage vers le haut à cause du bandeau fixe
					var hauteurBandeau = $('.bandeau').height();
					if(hauteurBandeau == null)
						hauteurBandeau = 0;
					
					$('html,body').animate({
						scrollTop: $(ancre).offset().top - hauteurBandeau
					}, 500);
				}
			}
			else {
				// Par défaut, active le premier onglet et le premier lien dans la liste de navigation
				$('.journee').hide().first().show();
				$('.ulNavigation li:first').addClass('active');
			}
		});
		
	</script>
</body>
</html>