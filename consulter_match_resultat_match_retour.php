<?php
	// Affichage de la répartition des pronostics de vainqueur pour un match retour de confrontation directe (victoire, match nul, défaite)
	include_once('commun.php');
	
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;

	$ordreSQL = 'SET @@session.group_concat_max_len=2048';
	$req = $bdd->exec($ordreSQL);

	// Joueurs ayant pronostiqué la victoire de l'équipe domicile
	$ordreSQL =		'	SELECT		IFNULL(GROUP_CONCAT(Pronostiqueurs_NomUtilisateur SEPARATOR \', \'), \'Aucun\') AS Pronostiqueurs_NomUtilisateur' .
					'	FROM		(' .
					'					SELECT		pronostics.Pronostiqueurs_Pronostiqueur' .
					'								,pronostics.Matches_Match' .
					'								,pronostics.Pronostics_ScoreEquipeDomicile' .
					'								,pronostics.Pronostics_ScoreEquipeVisiteur' .
					'								,pronostics.Pronostics_ScoreAPEquipeDomicile' .
					'								,pronostics.Pronostics_ScoreAPEquipeVisiteur' .
					'					FROM		matches' .
					'					JOIN		pronostics' .
					'								ON		matches.Match = pronostics.Matches_Match' .
					'					WHERE		matches.Match = ' . $match .
					'				) pronostics' .
					'	JOIN		pronostiqueurs' .
					'				ON		pronostics.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'	WHERE		fn_calculvainqueurpronostic	(	NULL' .
					'												,pronostics.Pronostics_ScoreEquipeDomicile' .
					'												,pronostics.Pronostics_ScoreEquipeVisiteur' .
					'												,NULL' .
					'												,NULL' .
					'												,pronostics.Pronostics_ScoreAPEquipeDomicile' .
					'												,pronostics.Pronostics_ScoreAPEquipeVisiteur' .
					'											) = 1';
	$req = $bdd->query($ordreSQL);
	$victoires = $req->fetchAll();
	$pronostiqueursVictoire = $victoires[0]["Pronostiqueurs_NomUtilisateur"];

	// Joueurs ayant pronostiqué le match nul
	$ordreSQL =		'	SELECT		IFNULL(GROUP_CONCAT(Pronostiqueurs_NomUtilisateur SEPARATOR \', \'), \'Aucun\') AS Pronostiqueurs_NomUtilisateur' .
					'	FROM		(' .
					'					SELECT		pronostics.Pronostiqueurs_Pronostiqueur' .
					'								,pronostics.Matches_Match' .
					'								,pronostics.Pronostics_ScoreEquipeDomicile' .
					'								,pronostics.Pronostics_ScoreEquipeVisiteur' .
					'								,pronostics.Pronostics_ScoreAPEquipeDomicile' .
					'								,pronostics.Pronostics_ScoreAPEquipeVisiteur' .
					'					FROM		matches' .
					'					JOIN		pronostics' .
					'								ON		matches.Match = pronostics.Matches_Match' .
					'					WHERE		matches.Match = ' . $match .
					'				) pronostics' .
					'	JOIN		pronostiqueurs' .
					'				ON		pronostics.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'	WHERE		fn_calculvainqueurpronostic	(	NULL' .
					'												,pronostics.Pronostics_ScoreEquipeDomicile' .
					'												,pronostics.Pronostics_ScoreEquipeVisiteur' .
					'												,NULL' .
					'												,NULL' .
					'												,pronostics.Pronostics_ScoreAPEquipeDomicile' .
					'												,pronostics.Pronostics_ScoreAPEquipeVisiteur' .
					'											) = 0';
	$req = $bdd->query($ordreSQL);
	$nuls = $req->fetchAll();
	$pronostiqueursNul = $nuls[0]["Pronostiqueurs_NomUtilisateur"];

	// Joueurs ayant pronostiqué la défaite de l'équipe domicile
	$ordreSQL =		'	SELECT		IFNULL(GROUP_CONCAT(Pronostiqueurs_NomUtilisateur SEPARATOR \', \'), \'Aucun\') AS Pronostiqueurs_NomUtilisateur' .
					'	FROM		(' .
					'					SELECT		pronostics.Pronostiqueurs_Pronostiqueur' .
					'								,pronostics.Matches_Match' .
					'								,pronostics.Pronostics_ScoreEquipeDomicile' .
					'								,pronostics.Pronostics_ScoreEquipeVisiteur' .
					'								,pronostics.Pronostics_ScoreAPEquipeDomicile' .
					'								,pronostics.Pronostics_ScoreAPEquipeVisiteur' .
					'					FROM		matches' .
					'					JOIN		pronostics' .
					'								ON		matches.Match = pronostics.Matches_Match' .
					'					LEFT JOIN	pronostics pronosticsaller' .
					'								ON		matches.Matches_MatchLie = pronosticsaller.Matches_Match' .
					'								AND		pronostics.Pronostiqueurs_Pronostiqueur = pronosticsaller.Pronostiqueurs_Pronostiqueur' .
					'					WHERE		matches.Match = ' . $match .
					'				) pronostics' .
					'	JOIN		pronostiqueurs' .
					'				ON		pronostics.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'	WHERE		fn_calculvainqueurpronostic	(	NULL' .
					'												,pronostics.Pronostics_ScoreEquipeDomicile' .
					'												,pronostics.Pronostics_ScoreEquipeVisiteur' .
					'												,NULL' .
					'												,NULL' .
					'												,pronostics.Pronostics_ScoreAPEquipeDomicile' .
					'												,pronostics.Pronostics_ScoreAPEquipeVisiteur' .
					'											) = 2';
	$req = $bdd->query($ordreSQL);
	$defaites = $req->fetchAll();
	$pronostiqueursDefaite = $defaites[0]["Pronostiqueurs_NomUtilisateur"];
					
	// Joueurs n'ayant pas encore effectué leur pronostic
	$ordreSQL =		'	SELECT		IFNULL(GROUP_CONCAT(Pronostiqueurs_NomUtilisateur SEPARATOR \', \'), \'Aucun\') AS Pronostiqueurs_NomUtilisateur' .
					'	FROM		(' .
					'					SELECT		pronostics.Pronostiqueurs_Pronostiqueur' .
					'								,pronostics.Matches_Match' .
					'								,pronostics.Pronostics_Vainqueur' .
					'								,pronostics.Pronostics_ScoreEquipeDomicile' .
					'								,pronostics.Pronostics_ScoreEquipeVisiteur' .
					'								,pronosticsaller.Pronostics_ScoreEquipeDomicile AS PronosticsAller_Pronostics_ScoreEquipeDomicile' .
					'								,pronosticsaller.Pronostics_ScoreEquipeVisiteur AS PronosticsAller_Pronostics_ScoreEquipeVisiteur' .
					'								,pronostics.Pronostics_ScoreAPEquipeDomicile' .
					'								,pronostics.Pronostics_ScoreAPEquipeVisiteur' .
					'					FROM		matches' .
					'					JOIN		pronostics' .
					'								ON		matches.Match = pronostics.Matches_Match' .
					'					LEFT JOIN	pronostics pronosticsaller' .
					'								ON		matches.Matches_MatchLie = pronosticsaller.Matches_Match' .
					'								AND		pronostics.Pronostiqueurs_Pronostiqueur = pronosticsaller.Pronostiqueurs_Pronostiqueur' .
					'					WHERE		matches.Match = ' . $match .
					'				) pronostics' .
					'	JOIN		pronostiqueurs' .
					'				ON		pronostics.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'	WHERE		pronostics.Pronostics_ScoreEquipeDomicile IS NULL' .
					'				AND		pronostics.Pronostics_ScoreEquipeVisiteur IS NULL';
	$req = $bdd->query($ordreSQL);
	$oublis = $req->fetchAll();
	$pronostiqueursOubli = $oublis[0]["Pronostiqueurs_NomUtilisateur"];

	echo '<table class="tableau--classement tableau--classement-buteurs">';
		echo '<thead>';
			echo '<tr class="tableau--classement-nom-colonnes">';
				echo '<th class="aligne-gauche">Pronostic</th>';
				echo '<th class="aligne-gauche">Joueurs</th>';
			echo '</tr>';
		echo '</thead>';
		
		echo '<tbody>';
			echo '<tr class="aligne-haut">';
				echo '<td class="aligne-gauche">Victoire</td>';
				echo '<td class="aligne-gauche retour-ligne" style="width: 600px;">' . $pronostiqueursVictoire . '</td>';
			echo '</tr>';
			echo '<tr class="aligne-haut">';
				echo '<td class="aligne-gauche">Match nul</td>';
				echo '<td class="aligne-gauche retour-ligne" style="width: 600px;">' . $pronostiqueursNul . '</td>';
			echo '</tr>';
			echo '<tr class="aligne-haut">';
				echo '<td class="aligne-gauche">Défaite</td>';
				echo '<td class="aligne-gauche retour-ligne" style="width: 600px;">' . $pronostiqueursDefaite . '</td>';
			echo '</tr>';
			if(sizeof($oublis)) {
				echo '<tr class="aligne-haut">';
					echo '<td class="aligne-gauche">Oubli</td>';
					echo '<td class="aligne-gauche retour-ligne" style="width: 600px;">' . $pronostiqueursOubli . '</td>';
				echo '</tr>';
			}
			
		echo '</tbody>';
	echo '</table>';
?>

