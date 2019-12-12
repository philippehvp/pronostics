<?php
	// Module d'affichage des résultats d'une journée

	// Le module peut être appelé de deux manières :
	// - par une inclusion
	// - par un appel Ajax (cas du rafraîchissement)

	include_once('classements_pronostiqueurs_fonctions.php');

	$rafraichissementModule = isset($_POST["rafraichissementModule"]) ? $_POST["rafraichissementModule"] : 0;
	if($rafraichissementModule == 1) {
		// Rafraîchissement automatique du module
		include_once('commun.php');

		// Lecture des paramètres passés à la page
		$championnat = isset($_POST["parametre"]) ? $_POST["parametre"] : 0;
		$modeRival = isset($_POST["modeRival"]) ? $_POST["modeRival"] : 0;
		$modeConcurrentDirect = isset($_POST["modeConcurrentDirect"]) ? $_POST["modeConcurrentDirect"] : 0;
	}
	else {
		$championnat = $parametre;		// Paramètre du module
	}

	// Parcours du championnat
	// Quelle est la journée en cours ?
	// Spécificité du calcul de la journée à afficher : une heure avant le début d'une journée, on affiche cette journée
	$ordreSQL =		'	SELECT		fn_recherchejourneeprochainmatch(' . $championnat . ') AS Journee_Affichee';
	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetchAll();
	$journeeAffichee = $donnees[0]["Journee_Affichee"];

	// Lecture des résultats des matches de la journée en cours ou, si elle n'existe pas, de la dernière journée complète
	$ordreSQL =		'	SELECT		Journees_Nom, matches.Match, matches.Matches_Direct' .
					'				,matches.Matches_Date, HOUR(Matches_Date) AS Matches_Heure, MINUTE(Matches_Date) AS Matches_Minute' .
					'				,matches.Equipes_EquipeDomicile, matches.Equipes_EquipeVisiteur' .
					'				,IFNULL(equipesdomicile.Equipes_NomCourt, equipesdomicile.Equipes_Nom) AS EquipesDomicile_Nom' .
					'				,IFNULL(equipesvisiteur.Equipes_NomCourt, equipesvisiteur.Equipes_Nom) AS EquipesVisiteur_Nom' .
					'				,IFNULL(equipesdomicile.Equipes_Fanion, \'_inconnu.png\') AS EquipesDomicile_Fanion, IFNULL(equipesvisiteur.Equipes_Fanion, \'_inconnu.png\') AS EquipesVisiteur_Fanion' .
					'				,matches.Matches_ScoreEquipeDomicile, matches.Matches_ScoreEquipeVisiteur' .
					'				,matches.Matches_ScoreAPEquipeDomicile, matches.Matches_ScoreAPEquipeVisiteur' .
					'				,matches.Matches_Vainqueur' .
					'				,(' .
					'					SELECT		GROUP_CONCAT(' .
					'												CASE' .
					'													WHEN	matches_buteurs.Buteurs_CSC = 0' .
					'													THEN	CONCAT(IFNULL(Joueurs_NomCourt, Joueurs_NomFamille), \' (\', fn_calculcotebuteur(Buteurs_Cote), \')\')' .
					'													ELSE	CONCAT(IFNULL(Joueurs_NomCourt, Joueurs_NomFamille), \' (CSC)\')' .
					'												END' .
					'												SEPARATOR \', \'' .
					'											)' .
					'					FROM		matches_buteurs' .
					'					JOIN		joueurs' .
					'								ON		matches_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'					WHERE		matches_buteurs.Matches_Match = matches.Match' .
					'								AND		(' .
					'											(' .
					'												matches_buteurs.Equipes_Equipe = equipesdomicile.Equipe' .
					'												AND		matches_buteurs.Buteurs_CSC = 0' .
					'											)' .
					'											OR' .
					'											(' .
					'												matches_buteurs.Equipes_Equipe = equipesvisiteur.Equipe' .
					'												AND		matches_buteurs.Buteurs_CSC = 1' .
					'											)' .
					'										)' .
					'				) AS Buteurs_Domicile' .
					'				,(' .
					'					SELECT		GROUP_CONCAT(' .
					'												CASE' .
					'													WHEN	matches_buteurs.Buteurs_CSC = 0' .
					'													THEN	CONCAT(IFNULL(Joueurs_NomCourt, Joueurs_NomFamille), \' (\', fn_calculcotebuteur(Buteurs_Cote), \')\')' .
					'													ELSE	CONCAT(IFNULL(Joueurs_NomCourt, Joueurs_NomFamille), \' (CSC)\')' .
					'												END' .
					'												SEPARATOR \', \'' .
					'											)' .
					'					FROM		matches_buteurs' .
					'					JOIN		joueurs' .
					'								ON		matches_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'					WHERE		matches_buteurs.Matches_Match = matches.Match' .
					'								AND		(' .
					'											(' .
					'												matches_buteurs.Equipes_Equipe = equipesvisiteur.Equipe' .
					'												AND		matches_buteurs.Buteurs_CSC = 0' .
					'											)' .
					'											OR' .
					'											(' .
					'												matches_buteurs.Equipes_Equipe = equipesdomicile.Equipe' .
					'												AND		matches_buteurs.Buteurs_CSC = 1' .
					'											)' .
					'										)' .
					'				) AS Buteurs_Visiteur' .
					'	FROM		journees' .
					'	JOIN		matches' .
					'				ON		journees.Journee = matches.Journees_Journee' .
					'	JOIN		equipes equipesdomicile' .
					'				ON		matches.Equipes_EquipeDomicile = equipesdomicile.Equipe' .
					'	JOIN		equipes equipesvisiteur' .
					'				ON		matches.Equipes_EquipeVisiteur = equipesvisiteur.Equipe' .
					'	WHERE		journees.Journee = ' . $journeeAffichee .
					'	ORDER BY	matches.Matches_Date, matches.Match';

	$req = $bdd->query($ordreSQL);
	$resultats = $req->fetchAll();
	$nombreResultats = sizeof($resultats);

	if($nombreResultats) {
		$nomJournee = $resultats[0]["Journees_Nom"];

		echo '<table class="tableau--resultat-journee">';
			echo '<thead>';
				echo '<tr>';
					echo '<th colspan="3">';
						echo '<label>' . $nomJournee . '</label>';
					echo '</th>';
				echo '</tr>';
			echo '</thead>';

			echo '<tbody>';
				foreach($resultats as $unResultat) {

					// Lecture des informations pour utilisation ultérieure
					$dateDebut = $unResultat["Matches_Date"] != null ? date("d/m/Y", strtotime($unResultat["Matches_Date"])) : date("d/m/Y");
					$heureDebut = $unResultat["Matches_Heure"] != null ? sprintf('%02u', $unResultat["Matches_Heure"]) : sprintf('%02u', date("G"));
					$minuteDebut = $unResultat["Matches_Minute"] != null ? sprintf('%02u', $unResultat["Matches_Minute"]) : sprintf('%02u', (date("i") + 5 - (date("i") % 5)));

					$scoreEquipeDomicile = $unResultat["Matches_ScoreEquipeDomicile"];
					$scoreEquipeVisiteur = $unResultat["Matches_ScoreEquipeVisiteur"];
					$scoreAPEquipeDomicile = $unResultat["Matches_ScoreAPEquipeDomicile"];
					$scoreAPEquipeVisiteur = $unResultat["Matches_ScoreAPEquipeVisiteur"];
					$vainqueur = $unResultat["Matches_Vainqueur"];

					/*	En priorité, on affiche :
						- le vainqueur des TAB (mention TAB à côté du score)
						- le perdant des TAB
						- le score AP
						- le score
					*/

					if($vainqueur != null) {
						if($vainqueur == -1)
							$scoreAffiche = $scoreAPEquipeDomicile . ' AP - ' . $scoreAPEquipeVisiteur . ' AP';
						else if($vainqueur == 1)
							$scoreAffiche = $scoreAPEquipeDomicile . ' TAB - ' . $scoreAPEquipeVisiteur;
						else if($vainqueur == 2)
							$scoreAffiche = $scoreAPEquipeDomicile . ' - ' . $scoreAPEquipeVisiteur . ' TAB';
						else
							$scoreAffiche = 'TIRS AU BUT';
					}
					else {
						if($scoreAPEquipeDomicile != null && $scoreAPEquipeVisiteur != null) {
							if($scoreAPEquipeDomicile > $scoreAPEquipeVisiteur)
								$scoreAffiche = $scoreAPEquipeDomicile . ' AP - ' . $scoreAPEquipeVisiteur;
							else if($scoreAPEquipeDomicile > $scoreAPEquipeVisiteur)
								$scoreAffiche = $scoreAPEquipeDomicile . ' - ' . $scoreAPEquipeVisiteur . ' AP';
							else
								$scoreAffiche = $scoreAPEquipeDomicile . ' AP - ' . $scoreAPEquipeVisiteur . ' AP';

						}
						else
							$scoreAffiche = $scoreEquipeDomicile . ' - ' . $scoreEquipeVisiteur;
					}

					echo '<tr>';
						// Equipe domicile
						echo '<td>';
							if($_SESSION["administrateur"] == 1) {
								echo '<span onclick="consulterMatch_modifierMatch(' . $journeeAffichee . ', ' . $unResultat["Match"] . ', \'' . $unResultat["EquipesDomicile_Nom"] . '\', \'' . $unResultat["EquipesVisiteur_Nom"] . '\');">';
							} else {
								echo '<span>';
							}
								echo '<label>' . $unResultat["EquipesDomicile_Nom"] . '</label><br />';
								echo '<img class="tableau--resultat-journee--fanion" src="images/equipes/' . $unResultat["EquipesDomicile_Fanion"] . '" alt="" /><br />';
							echo '</span>';

							if($unResultat["Buteurs_Domicile"] != null)
								echo '<label class="texte-petit curseur-main" onclick="afficherButeurs(' . $unResultat["Match"] . ', ' . $unResultat["Equipes_EquipeDomicile"] . ');">' . str_replace(', ', '<br />', $unResultat["Buteurs_Domicile"]) . '</label>';
							else
								echo '<label class="texte-petit">&nbsp;</label>';
						echo '</td>';

						// Logistique et score
						if($unResultat["Matches_Direct"] == 1)				$classe = 'match-en-direct';
						else												$classe = 'match-non-en-direct';
						echo '<td class="' . $classe . '">';
							echo '<label class="texte-petit">' . $dateDebut . ' à ' . $heureDebut . 'h' . $minuteDebut . '</label><br />';
							echo '<label class="texte-grand">' . $scoreAffiche . '</label><br />';
							echo '<label class="texte-petit curseur-main" onclick="consulterMatch_afficherMatch(' . $unResultat["Match"] . ');">Détails</label><br />';
							echo '<label class="texte-petit curseur-main" onclick="afficherMatch($(this), ' . $unResultat["Match"] . ', \'' . $unResultat["EquipesDomicile_Nom"] . '\', \'' . $unResultat["EquipesVisiteur_Nom"] . '\');">Pronostics</label>';
						echo '</td>';

						// Equipe visiteur
						echo '<td>';
							if($_SESSION["administrateur"] == 1) {
								echo '<span onclick="consulterMatch_modifierMatch(' . $journeeAffichee . ', ' . $unResultat["Match"] . ', \'' . $unResultat["EquipesDomicile_Nom"] . '\', \'' . $unResultat["EquipesVisiteur_Nom"] . '\');">';
							} else {
								echo '<span>';
							}
								echo '<label>' . $unResultat["EquipesVisiteur_Nom"] . '</label><br />';
								echo '<img class="tableau--resultat-journee--fanion" src="images/equipes/' . $unResultat["EquipesVisiteur_Fanion"] . '" alt="" /><br />';
							echo '</span>';

							if($unResultat["Buteurs_Visiteur"] != null)
								echo '<label class="texte-petit curseur-main" onclick="afficherButeurs(' . $unResultat["Match"] . ', ' . $unResultat["Equipes_EquipeVisiteur"] . ');">' . str_replace(', ', '<br />', $unResultat["Buteurs_Visiteur"]) . '</label>';
							else
								echo '<label class="texte-petit">&nbsp;</label>';
						echo '</td>';
					echo '</tr>';
				}
			echo '</tbody>';
		echo '</table>';
	}

?>


<script>
	$(function() {

	});

	// Affichage des détails d'un match
	// Prise en compte du mode rival
	function afficherMatch(element, match, equipeDomicile, equipeVisiteur) {
		var modeRival = element.parents('.module').find('input.moderival').prop('checked');
		if(modeRival == true)
			modeRival = 1;
		else
			modeRival = 0;
		var modeConcurrentDirect = element.parents('.module').find('input.modeconcurrentdirect').prop('checked');
		if(modeConcurrentDirect == true)
			modeConcurrentDirect = 1;
		else
			modeConcurrentDirect = 0;

		consulterResultats_afficherMatch(match, equipeDomicile, equipeVisiteur, modeRival, modeConcurrentDirect);
	}

</script>