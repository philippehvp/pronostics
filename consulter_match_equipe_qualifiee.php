<?php
	// Affichage de la répartition des pronostics d'équipes qualifiées pour le tour suivant en confrontation directe
	include_once('commun.php');

	$match = isset($_POST["match"]) ? $_POST["match"] : 0;

	$ordreSQL = 'SET @@session.group_concat_max_len=2048';
	$req = $bdd->exec($ordreSQL);

	// Joueurs ayant pronostiqué la qualification de l'équipe domicile
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
					'										AND		pronostics.Pronostiqueurs_Pronostiqueur = pronosticsaller.Pronostiqueurs_Pronostiqueur' .
					'					WHERE		matches.Match = ' . $match .
					'				) pronostics' .
					'	JOIN		pronostiqueurs' .
					'				ON		pronostics.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'	WHERE		fn_calculvainqueurpronostic	(	pronostics.Pronostics_Vainqueur' .
					'												,pronostics.Pronostics_ScoreEquipeDomicile' .
					'												,pronostics.Pronostics_ScoreEquipeVisiteur' .
					'												,pronostics.PronosticsAller_Pronostics_ScoreEquipeDomicile' .
					'												,pronostics.PronosticsAller_Pronostics_ScoreEquipeVisiteur' .
					'												,pronostics.Pronostics_ScoreAPEquipeDomicile' .
					'												,pronostics.Pronostics_ScoreAPEquipeVisiteur' .
					'											) = 1';
	$req = $bdd->query($ordreSQL);
	$victoires = $req->fetchAll();
	$pronostiqueursVictoire = $victoires[0]["Pronostiqueurs_NomUtilisateur"];

	// Joueurs ayant pronostiqué la qualification de l'équipe visiteur
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
					'										AND		pronostics.Pronostiqueurs_Pronostiqueur = pronosticsaller.Pronostiqueurs_Pronostiqueur' .
					'					WHERE		matches.Match = ' . $match .
					'				) pronostics' .
					'	JOIN		pronostiqueurs' .
					'				ON		pronostics.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'	WHERE		fn_calculvainqueurpronostic	(	pronostics.Pronostics_Vainqueur' .
					'												,pronostics.Pronostics_ScoreEquipeDomicile' .
					'												,pronostics.Pronostics_ScoreEquipeVisiteur' .
					'												,pronostics.PronosticsAller_Pronostics_ScoreEquipeDomicile' .
					'												,pronostics.PronosticsAller_Pronostics_ScoreEquipeVisiteur' .
					'												,pronostics.Pronostics_ScoreAPEquipeDomicile' .
					'												,pronostics.Pronostics_ScoreAPEquipeVisiteur' .
					'											) = 2';
	$req = $bdd->query($ordreSQL);
	$defaites = $req->fetchAll();
	$pronostiqueursDefaite = $defaites[0]["Pronostiqueurs_NomUtilisateur"];

	// Noms des équipes
	$ordreSQL =		'	SELECT		equipesDomicile.Equipes_Nom AS EquipesDomicile_Nom, equipesVisiteur.Equipes_Nom AS EquipesVisiteur_Nom' .
					'	FROM		matches' .
					'	JOIN		equipes equipesDomicile' .
					'				ON		matches.Equipes_EquipeDomicile = equipesDomicile.Equipe' .
					'	JOIN		equipes equipesVisiteur' .
					'				ON		matches.Equipes_EquipeVisiteur = equipesVisiteur.Equipe' .
					'	WHERE		matches.Match = ' . $match;
	$req = $bdd->query($ordreSQL);
	$equipes = $req->fetchAll();


	echo '<table class="tableau--classement tableau--classement-buteurs">';
		echo '<thead>';
			echo '<tr class="tableau--classement-nom-colonnes">';
				echo '<th class="aligne-gauche">Pronostic</th>';
				echo '<th class="aligne-gauche">Joueurs</th>';
			echo '</tr>';
		echo '</thead>';

		echo '<tbody>';
			echo '<tr class="aligne-haut">';
				echo '<td class="aligne-gauche">' . $equipes[0]["EquipesDomicile_Nom"] . '</td>';
				echo '<td class="aligne-gauche retour-ligne" style="width: 600px;">' . $pronostiqueursVictoire . '</td>';
			echo '</tr>';
			echo '<tr class="aligne-haut">';
				echo '<td class="aligne-gauche">' . $equipes[0]["EquipesVisiteur_Nom"] . '</td>';
				echo '<td class="aligne-gauche retour-ligne" style="width: 600px;">' . $pronostiqueursDefaite . '</td>';
			echo '</tr>';
		echo '</tbody>';
	echo '</table>';
?>

