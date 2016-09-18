<?php
	// Module d'affichage des pronostics
	
	include_once('classements_pronostiqueurs_fonctions.php');

	// Parcours du championnat
	// Quelle est la journée complète ?
	$championnat = $parametre;
	/*$ordreSQL =		'	SELECT		fn_recherchejourneeencours(' . $championnat . ') AS Journee_EnCours';
	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetchAll();
	$journeeAffichee = $donnees[0]["Journee_EnCours"];*/
	
	// On affiche les pronostics de la journée suivante si les trois conditions suivantes sont remplies :
	// - la journée suivante est active
	// - la date du jour du dernier match est passée (changement de jour)
	// - des pronostics ont été saisis pour la journée suivante
	$ordreSQL =		'		SELECT		CASE' .
					'						WHEN	DATE(NOW()) > (SELECT MAX(matches.Matches_Date) FROM matches WHERE matches.Journees_Journee = journees.Journee_En_Cours)' .
					'								AND		(' .
					'											SELECT		Journees_Active' .
					'											FROM		journees j' .
					'											WHERE		j.Journee = journees.Journee_Suivante' .
					'										) = 1' .
					'								AND		IFNULL(' .
					'												(' .
					'													SELECT		COUNT(*)' .
					'													FROM		pronostics' .
					'													JOIN		matches' .
					'																ON		pronostics.Matches_Match = matches.Match' .
					'													WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'																AND		Journees_Journee = journees.Journee_Suivante' .
					'																AND		Pronostics_ScoreEquipeDomicile IS NOT NULL' .
					'																AND		Pronostics_ScoreEquipeVisiteur IS NOT NULL' .
					'												), 0) > 0' .
					'						THEN	Journee_Suivante' .
					'						ELSE	Journee_En_Cours' .
					'					END AS Journee_Affichee' .
					'		FROM		(' .
					'						SELECT		fn_recherchejourneeencours(' . $championnat . ') AS Journee_En_Cours' .
					'									,fn_recherchejourneesuivante(' . $championnat . ') AS Journee_Suivante' .
					'					) journees';
	$req = $bdd->query($ordreSQL);
	$journees = $req->fetchAll();
	$journeeAffichee = $journees[0]["Journee_Affichee"];
	
	// Lecture des pronostics de la dernière journée du championnat
	$ordreSQL =		'	    SELECT		matches.Match, Journees_Nom' .
					'					,IFNULL(equipesdomicile.Equipes_NomCourt, equipesdomicile.Equipes_Nom) AS EquipesDomicile_NomCourt' .
					'					,equipesdomicile.Equipes_Nom AS EquipesDomicile_Nom' .
					'					,Pronostics_ScoreEquipeDomicile AS Pronostics_ScoreEquipeDomicile' .
					'					,Pronostics_ScoreAPEquipeDomicile' .
					'					,IFNULL(equipesvisiteur.Equipes_NomCourt, equipesvisiteur.Equipes_Nom) AS EquipesVisiteur_NomCourt' .
					'					,equipesvisiteur.Equipes_Nom AS EquipesVisiteur_Nom' .
					'					,Pronostics_ScoreEquipeVisiteur' .
					'					,Pronostics_ScoreAPEquipeVisiteur' .
					'					,Pronostics_Vainqueur' .
					'					,(	SELECT		GROUP_CONCAT(IFNULL(Joueurs_NomCourt, Joueurs_NomFamille) SEPARATOR \', \')' .
					'						FROM		pronostics_buteurs' .
					'						JOIN		joueurs' .
					'									ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'						WHERE		pronostics_buteurs.Matches_Match = matches.Match' .
					'									AND		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'									AND		(' .
					'												pronostics_buteurs.Equipes_Equipe = equipesdomicile.Equipe' .
					'												OR		pronostics_buteurs.Equipes_Equipe = equipesvisiteur.Equipe' .
					'											)' .
					'						GROUP BY	pronostics_buteurs.Matches_Match' .
					'					) AS Buteurs' .
					'		FROM		pronostics' .
					'		JOIN		matches' .
					'					ON pronostics.Matches_Match = matches.Match' .
					'		JOIN		journees' .
					'					ON		matches.Journees_Journee = journees.Journee' .
					'		JOIN		equipes equipesdomicile' .
					'					ON		matches.Equipes_EquipeDomicile = equipesdomicile.Equipe' .
					'		JOIN		equipes equipesvisiteur' .
					'					ON		matches.Equipes_EquipeVisiteur = equipesvisiteur.Equipe' .
					'		JOIN		pronostiqueurs' .
					'					ON		pronostics.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'		WHERE		Journees_Journee = ' . $journeeAffichee .
					'					AND		Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'		ORDER BY	matches.Matches_Date, matches.Match';
					
	$req = $bdd->query($ordreSQL);
	$pronostics = $req->fetchAll();
	$nombrePronostics = sizeof($pronostics);
	if($nombrePronostics) {
		echo '<table class="tableau--classement">';
			echo '<thead>';
				echo '<tr>';
					echo '<th colspan="3">' . $pronostics[0]["Journees_Nom"] . '</th>';
				echo '</tr>';
				echo '<tr class="tableau--classement-nom-colonnes">';
					echo '<th class="aligne-gauche">Match</th>';
					echo '<th>Score</th>';
					echo '<th class="aligne-gauche">Buteurs</th>';
				echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
				for($i = 0; $i < $nombrePronostics; $i++) {
					echo '<tr onclick="window.open(\'creer_prono.php#match' . $pronostics[$i]["Match"] . '\', \'_self\');">';
						// L'affichage des scores doit cumuler les scores 90ème, AP et le vainqueur aux TAB s'ils sont pronostiqués
						if($pronostics[$i]["Pronostics_ScoreAPEquipeDomicile"] != null)
							$scores = $pronostics[$i]["Pronostics_ScoreEquipeDomicile"] . ' - ' . $pronostics[$i]["Pronostics_ScoreEquipeVisiteur"] . ' (' . $pronostics[$i]["Pronostics_ScoreAPEquipeDomicile"] . ' - ' . $pronostics[$i]["Pronostics_ScoreAPEquipeVisiteur"] . ' AP)';
						else
							$scores = $pronostics[$i]["Pronostics_ScoreEquipeDomicile"] . ' - ' . $pronostics[$i]["Pronostics_ScoreEquipeVisiteur"];
						
						if($pronostics[$i]["Pronostics_Vainqueur"] != null && $pronostics[$i]["Pronostics_Vainqueur"] != 0)
							$scores .= ' TAB(' . ($pronostics[$i]["Pronostics_Vainqueur"] == 1 ? $pronostics[$i]["EquipesDomicile_Nom"] : $pronostics[$i]["EquipesVisiteur_Nom"]) . ')';

						echo '<td class="aligne-gauche" title="' . $pronostics[$i]["EquipesDomicile_Nom"] . ' - ' . $pronostics[$i]["EquipesVisiteur_Nom"] . '">' .$pronostics[$i]["EquipesDomicile_NomCourt"] . ' - ' . $pronostics[$i]["EquipesVisiteur_NomCourt"] . '</td>';
						echo '<td>' . $scores . '</td>';
						echo '<td class="aligne-gauche">' .($pronostics[$i]["Buteurs"] != null ? $pronostics[$i]["Buteurs"] : 'Aucun') . '</td>';
					echo '</tr>';
				}
			echo '</tbody>';
		echo '</table>';
	}

?>