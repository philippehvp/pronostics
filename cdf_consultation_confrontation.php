<?php
	include_once('commun.php');
	include_once('fonctions.php');

	// Affichage du détail d'une confrontation

	// Lecture des paramètres passés à la page
	$confrontation = isset($_POST["confrontation"]) ? $_POST["confrontation"] : 0;
	$journee = 61;

	// Bons résultats des matches de la journée de la confrontation
	$ordreSQL = '	SELECT		DISTINCT vue_resultatsjournees.Match' .
				'				,EquipesDomicile_NomCourt' .
				'				,EquipesVisiteur_NomCourt' .
				'				,EquipesDomicile_Nom' .
				'				,EquipesVisiteur_Nom' .
				'				,Matches_ScoreEquipeDomicile' .
				'				,Matches_ScoreAPEquipeDomicile' .
				'				,Matches_ScoreEquipeVisiteur' .
				'				,Matches_ScoreAPEquipeVisiteur' .
				'				,Matches_Vainqueur' .
				'				,EquipesDomicile_Buteurs' .
				'				,EquipesVisiteur_Buteurs' .
				'				,Matches_PointsQualificationEquipeDomicile' .
				'				,Matches_PointsQualificationEquipeVisiteur' .
				'				,Matches_Coefficient' .
				'	FROM		vue_resultatsjournees' .
				'	JOIN		confrontations' .
				'				ON		vue_resultatsjournees.Journees_Journee = confrontations.Journees_Journee' .
				'	WHERE		confrontations.Confrontation = ' . $confrontation .
				'	ORDER BY	vue_resultatsjournees.Match';

	$req = $bdd->query($ordreSQL);
	$resultats = $req->fetchAll();
	$nombreMatches = sizeof($resultats);


	function lirePronostics($bdd, $pronostiqueur, $pronostiqueurDetail, $journee, &$pronostics) {
		// Tous les pronostics et pronostics de buteurs des pronostiqueurs de la confrontation pour la journée
		$ordreSQL =		'	    SELECT DISTINCT		matches.Match' .
						'							,fn_calculprecisionpronostic(matches.Match, ' . $pronostiqueurDetail . ') AS Performance' .
						'							,CASE' .
						'								WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
						'								THEN	pronostics.Pronostics_ScoreEquipeDomicile' .
						'								ELSE	\'?\'' .
						'							END AS Pronostics_ScoreEquipeDomicile' .
						'							,CASE' .
						'								WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
						'								THEN	pronostics.Pronostics_ScoreAPEquipeDomicile' .
						'								ELSE	\'?\'' .
						'							END AS Pronostics_ScoreAPEquipeDomicile' .
						'							,CASE' .
						'								WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
						'								THEN	pronostics.Pronostics_ScoreEquipeVisiteur' .
						'								ELSE	\'?\'' .
						'							END AS Pronostics_ScoreEquipeVisiteur' .
						'							,CASE' .
						'								WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
						'								THEN	pronostics.Pronostics_ScoreAPEquipeVisiteur' .
						'								ELSE	\'?\'' .
						'							END AS Pronostics_ScoreAPEquipeVisiteur' .
						'							,CASE' .
						'								WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
						'								THEN	Pronostics_Vainqueur' .
						'								ELSE	\'?\'' .
						'							END AS Pronostics_Vainqueur' .
						'							,CASE' .
						'								WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
						'								THEN	Buteurs_Domicile.Buteurs' .
						'								ELSE	\'?\'' .
						'							END AS EquipesDomicile_Buteurs' .
						'							,CASE' .
						'								WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
						'								THEN	Buteurs_Visiteur.Buteurs' .
						'								ELSE	\'?\'' .
						'							END AS EquipesVisiteur_Buteurs' .
						'							,CASE' .
						'								WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
						'								THEN	ButeursInvalides_Domicile.Buteurs' .
						'								ELSE	\'?\'' .
						'							END AS EquipesDomicile_ButeursInvalides' .
						'							,CASE' .
						'								WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
						'								THEN	ButeursInvalides_Visiteur.Buteurs' .
						'								ELSE	\'?\'' .
						'							END AS EquipesVisiteur_ButeursInvalides' .
						'							,CASE' .
						'								WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
						'								THEN	ButeursAbsents_Domicile.Buteurs' .
						'								ELSE	\'?\'' .
						'							END AS EquipesDomicile_ButeursAbsents' .
						'							,CASE' .
						'								WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
						'								THEN	ButeursAbsents_Visiteur.Buteurs' .
						'								ELSE	\'?\'' .
						'							END AS EquipesVisiteur_ButeursAbsents' .
						'							,CASE' .
						'								WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
						'								THEN	IFNULL(scores.Scores_ScoreMatch, 0)' .
						'								ELSE	\'?\'' .
						'							END AS Scores_ScoreMatch' .
						'							,CASE' .
						'								WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
						'								THEN	IFNULL(scores.Scores_ScoreButeur, 0)' .
						'								ELSE	\'?\'' .
						'							END AS Scores_ScoreButeur' .
						'							,CASE' .
						'								WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
						'								THEN	IFNULL(scores.Scores_ScoreBonus, 0)' .
						'								ELSE	\'?\'' .
						'							END Scores_ScoreBonus' .
						'							,Matches_Coefficient' .
						'		FROM				pronostiqueurs' .
						'		JOIN				matches' .
						'		LEFT JOIN			equipes equipes_equipedomicile' .
						'							ON		matches.Equipes_EquipeDomicile = equipes_equipedomicile.Equipe' .
						'		LEFT JOIN			equipes equipes_equipevisiteur' .
						'							ON		matches.Equipes_EquipeVisiteur = equipes_equipevisiteur.Equipe' .
						'		LEFT JOIN			pronostics' .
						'							ON pronostics.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'							AND		matches.Match = pronostics.Matches_Match' .
						'		LEFT JOIN			scores' .
						'							ON		pronostiqueurs.Pronostiqueur = scores.Pronostiqueurs_Pronostiqueur' .
						'							AND		matches.Match = scores.Matches_Match' .
						'		JOIN				inscriptions' .
						'							ON		pronostiqueurs.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
						'		JOIN				journees' .
						'							ON		matches.Journees_Journee = journees.Journee' .
						'							AND		inscriptions.Championnats_Championnat = journees.Championnats_Championnat' .
						'		LEFT JOIN			(	SELECT		pronostics_buteurs.Matches_Match' .
						'											,pronostics_buteurs.Equipes_Equipe' .
						'											,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
						'								FROM		(' .
						'												SELECT		Matches_Match' .
						'															,Joueurs_Joueur' .
						'															,Equipes_Equipe' .
						'															,CASE' .
						'																WHEN	@match = Matches_Match' .
						'																		AND		@joueur = Joueurs_Joueur' .
						'																		AND		@equipe = Equipes_Equipe' .
						'																THEN	@indicePronostics := @indicePronostics + 1' .
						'																ELSE	(@indicePronostics := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
						'															END AS Pronostics_Indice' .
						'												FROM		pronostics_buteurs' .
						'												JOIN		matches' .
						'															ON		pronostics_buteurs.Matches_Match = matches.Match' .
						'												JOIN		(	SELECT		@indicePronostics := 0, @match := NULL, @joueur := NULL, @equipe := NULL	) r' .
						'												WHERE		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurDetail .
						'															AND		matches.Journees_Journee = ' . $journee .
						'												ORDER BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
						'											) pronostics_buteurs' .
						'								JOIN		(' .
						'												SELECT		Matches_Match' .
						'															,Joueurs_Joueur' .
						'															,Equipes_Equipe' .
						'															,CASE' .
						'																WHEN	@match = Matches_Match' .
						'																		AND		@joueur = Joueurs_Joueur' .
						'																		AND		@equipe = Equipes_Equipe' .
						'																THEN	@indiceMatches := @indiceMatches + 1' .
						'																ELSE	(@indiceMatches := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
						'															END AS Matches_Indice' .
						'												FROM		matches_buteurs' .
						'												JOIN		matches' .
						'															ON		matches_buteurs.Matches_Match = matches.Match' .
						'												JOIN		(	SELECT		@indiceMatches := 0, @joueur := NULL, @equipe := NULL	) r' .
						'												WHERE		matches.Journees_Journee = ' . $journee .
						'												ORDER BY	Matches_Match, Joueurs_Joueur, Equipes_Equipe' .
						'											) matches_buteurs' .
						'											ON		pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
						'													AND		pronostics_buteurs.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
						'													AND		pronostics_buteurs.Equipes_Equipe = matches_buteurs.Equipes_Equipe' .
						'													AND		pronostics_buteurs.Pronostics_Indice = matches_buteurs.Matches_Indice' .
						'								JOIN		joueurs' .
						'											ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
						'								GROUP BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Equipes_Equipe' .
						'							) AS Buteurs_Domicile' .
						'							ON		Buteurs_Domicile.Matches_Match = matches.Match' .
						'									AND		Buteurs_Domicile.Equipes_Equipe = equipes_equipedomicile.Equipe' .
						'		LEFT JOIN			(	SELECT		pronostics_buteurs.Matches_Match' .
						'											,pronostics_buteurs.Equipes_Equipe' .
						'											,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
						'								FROM		(' .
						'												SELECT		Matches_Match' .
						'															,Joueurs_Joueur' .
						'															,Equipes_Equipe' .
						'															,CASE' .
						'																WHEN	@match = Matches_Match' .
						'																		AND		@joueur = Joueurs_Joueur' .
						'																		AND		@equipe = Equipes_Equipe' .
						'																THEN	@indicePronostics := @indicePronostics + 1' .
						'																ELSE	(@indicePronostics := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
						'															END AS Pronostics_Indice' .
						'												FROM		pronostics_buteurs' .
						'												JOIN		matches' .
						'															ON		pronostics_buteurs.Matches_Match = matches.Match' .
						'												JOIN		(	SELECT		@indicePronostics := 0, @match := NULL, @joueur := NULL, @equipe := NULL	) r' .
						'												WHERE		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurDetail .
						'															AND		matches.Journees_Journee = ' . $journee .
						'												ORDER BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
						'											) pronostics_buteurs' .
						'								JOIN		(' .
						'												SELECT		Matches_Match' .
						'															,Joueurs_Joueur' .
						'															,Equipes_Equipe' .
						'															,CASE' .
						'																WHEN	@match = Matches_Match' .
						'																		AND		@joueur = Joueurs_Joueur' .
						'																		AND		@equipe = Equipes_Equipe' .
						'																THEN	@indiceMatches := @indiceMatches + 1' .
						'																ELSE	(@indiceMatches := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
						'															END AS Matches_Indice' .
						'												FROM		matches_buteurs' .
						'												JOIN		matches' .
						'															ON		matches_buteurs.Matches_Match = matches.Match' .
						'												JOIN		(	SELECT		@indiceMatches := 0, @joueur := NULL, @equipe := NULL	) r' .
						'												WHERE		matches.Journees_Journee = ' . $journee .
						'												ORDER BY	Matches_Match, Joueurs_Joueur, Equipes_Equipe' .
						'											) matches_buteurs' .
						'											ON		pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
						'													AND		pronostics_buteurs.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
						'													AND		pronostics_buteurs.Equipes_Equipe = matches_buteurs.Equipes_Equipe' .
						'													AND		pronostics_buteurs.Pronostics_Indice = matches_buteurs.Matches_Indice' .
						'								JOIN		joueurs' .
						'											ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
						'								GROUP BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Equipes_Equipe' .
						'							) AS Buteurs_Visiteur' .
						'							ON		Buteurs_Visiteur.Matches_Match = matches.Match' .
						'									AND		Buteurs_Visiteur.Equipes_Equipe = equipes_equipevisiteur.Equipe' .
						'		LEFT JOIN			(	SELECT		pronostics_buteurs.Matches_Match' .
						'											,pronostics_buteurs.Equipes_Equipe' .
						'											,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
						'								FROM		(' .
						'												SELECT		pronostics_buteurs.Matches_Match' .
						'															,pronostics_buteurs.Joueurs_Joueur' .
						'															,pronostics_buteurs.Equipes_Equipe' .
						'															,CASE' .
						'																WHEN	@match = pronostics_buteurs.Matches_Match' .
						'																		AND		@joueur = pronostics_buteurs.Joueurs_Joueur' .
						'																		AND		@equipe = pronostics_buteurs.Equipes_Equipe' .
						'																THEN	@indicePronostics := @indicePronostics + 1' .
						'																ELSE	(@indicePronostics := 1) AND (@match := pronostics_buteurs.Matches_Match) AND (@joueur := pronostics_buteurs.Joueurs_Joueur) AND (@equipe := pronostics_buteurs.Equipes_Equipe)' .
						'															END AS Pronostics_Indice' .
						'												FROM		pronostics_buteurs' .
						'												JOIN		matches_participants' .
						'															ON		pronostics_buteurs.Matches_Match = matches_participants.Matches_Match' .
						'																	AND		pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
						'																	AND		pronostics_buteurs.Equipes_Equipe = matches_participants.Equipes_Equipe' .
						'												JOIN		matches' .
						'															ON		pronostics_buteurs.Matches_Match = matches.Match' .
						'												JOIN		(	SELECT		@indicePronostics := 0, @match := NULL, @joueur := NULL, @equipe := NULL	) r' .
						'												WHERE		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurDetail .
						'															AND		matches.Journees_Journee = ' . $journee .
						'												ORDER BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
						'											) pronostics_buteurs' .
						'								LEFT JOIN	(' .
						'												SELECT		Matches_Match' .
						'															,Joueurs_Joueur' .
						'															,Equipes_Equipe' .
						'															,CASE' .
						'																WHEN	@match = Matches_Match' .
						'																		AND		@joueur = Joueurs_Joueur' .
						'																		AND		@equipe = Equipes_Equipe' .
						'																THEN	@indiceMatches := @indiceMatches + 1' .
						'																ELSE	(@indiceMatches := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
						'															END AS Matches_Indice' .
						'												FROM		matches_buteurs' .
						'												JOIN		matches' .
						'															ON		matches_buteurs.Matches_Match = matches.Match' .
						'												JOIN		(	SELECT		@indiceMatches := 0, @match := NULL, @joueur :=  NULL, @equipe := NULL	) r' .
						'												WHERE		matches.Journees_Journee = ' . $journee .
						'												ORDER BY	Matches_Match, Joueurs_Joueur, Equipes_Equipe' .
						'											) matches_buteurs' .
						'											ON		pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
						'													AND		pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
						'													AND		pronostics_buteurs.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
						'													AND		pronostics_buteurs.Pronostics_Indice = matches_buteurs.Matches_Indice' .
						'								JOIN		joueurs' .
						'											ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
						'								WHERE		matches_buteurs.Joueurs_Joueur IS NULL' .
						'								GROUP BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Equipes_Equipe' .
						'							) AS ButeursInvalides_Domicile' .
						'							ON		ButeursInvalides_Domicile.Matches_Match = matches.Match' .
						'									AND		ButeursInvalides_Domicile.Equipes_Equipe = equipes_equipedomicile.Equipe' .
						'		LEFT JOIN			(	SELECT		pronostics_buteurs.Matches_Match' .
						'											,pronostics_buteurs.Equipes_Equipe' .
						'											,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
						'								FROM		(' .
						'												SELECT		pronostics_buteurs.Matches_Match' .
						'															,pronostics_buteurs.Joueurs_Joueur' .
						'															,pronostics_buteurs.Equipes_Equipe' .
						'															,CASE' .
						'																WHEN	@match = pronostics_buteurs.Matches_Match' .
						'																		AND		@joueur = pronostics_buteurs.Joueurs_Joueur' .
						'																		AND		@equipe = pronostics_buteurs.Equipes_Equipe' .
						'																THEN	@indicePronostics := @indicePronostics + 1' .
						'																ELSE	(@indicePronostics := 1) AND (@match := pronostics_buteurs.Matches_Match) AND (@joueur := pronostics_buteurs.Joueurs_Joueur) AND (@equipe := pronostics_buteurs.Equipes_Equipe)' .
						'															END AS Pronostics_Indice' .
						'												FROM		pronostics_buteurs' .
						'												JOIN		matches_participants' .
						'															ON		pronostics_buteurs.Matches_Match = matches_participants.Matches_Match' .
						'																	AND		pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
						'																	AND		pronostics_buteurs.Equipes_Equipe = matches_participants.Equipes_Equipe' .
						'												JOIN		matches' .
						'															ON		pronostics_buteurs.Matches_Match = matches.Match' .
						'												JOIN		(	SELECT		@indicePronostics := 0, @match := NULL, @joueur := NULL, @equipe := NULL	) r' .
						'												WHERE		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurDetail .
						'															AND		matches.Journees_Journee = ' . $journee .
						'												ORDER BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
						'											) pronostics_buteurs' .
						'								LEFT JOIN	(' .
						'												SELECT		Matches_Match' .
						'															,Joueurs_Joueur' .
						'															,Equipes_Equipe' .
						'															,CASE' .
						'																WHEN	@match = Matches_Match' .
						'																		AND		@joueur = Joueurs_Joueur' .
						'																		AND		@equipe = Equipes_Equipe' .
						'																THEN	@indiceMatches := @indiceMatches + 1' .
						'																ELSE	(@indiceMatches := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
						'															END AS Matches_Indice' .
						'												FROM		matches_buteurs' .
						'												JOIN		matches' .
						'															ON		matches_buteurs.Matches_Match = matches.Match' .
						'												JOIN		(	SELECT		@indiceMatches := 0, @match := NULL, @joueur :=  NULL, @equipe := NULL	) r' .
						'												WHERE		matches.Journees_Journee = ' . $journee .
						'												ORDER BY	Matches_Match, Joueurs_Joueur, Equipes_Equipe' .
						'											) matches_buteurs' .
						'											ON		pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
						'													AND		pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
						'													AND		pronostics_buteurs.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
						'													AND		pronostics_buteurs.Pronostics_Indice = matches_buteurs.Matches_Indice' .
						'								JOIN		joueurs' .
						'											ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
						'								WHERE		matches_buteurs.Joueurs_Joueur IS NULL' .
						'								GROUP BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Equipes_Equipe' .
						'							) AS ButeursInvalides_Visiteur' .
						'							ON		ButeursInvalides_Visiteur.Matches_Match = matches.Match' .
						'									AND		ButeursInvalides_Visiteur.Equipes_Equipe = equipes_equipevisiteur.Equipe' .
						'		LEFT JOIN			(' .
						'								SELECT		pronostics_buteurs.Matches_Match' .
						'											,Equipes_Equipe' .
						'											,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
						'								FROM		(' .
						'												SELECT		pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
						'												FROM		pronostics_buteurs' .
						'												JOIN		matches' .
						'															ON		pronostics_buteurs.Matches_Match = matches.Match' .
						'												LEFT JOIN	matches_participants' .
						'															ON		pronostics_buteurs.Matches_Match = matches_participants.Matches_Match' .
						'																	AND		pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
						'																	AND		pronostics_buteurs.Equipes_Equipe = matches_participants.Equipes_Equipe' .
						'												WHERE		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurDetail .
						'															AND		matches.Journees_Journee = ' . $journee .
						'															AND		matches_participants.Joueurs_Joueur IS NULL' .
						'											) pronostics_buteurs' .
						'								JOIN		joueurs' .
						'											ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
						'								GROUP BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Equipes_Equipe' .
						'							) AS ButeursAbsents_Domicile' .
						'							ON		ButeursAbsents_Domicile.Matches_Match = matches.Match' .
						'									AND		ButeursAbsents_Domicile.Equipes_Equipe = equipes_equipedomicile.Equipe' .
						'		LEFT JOIN			(' .
						'								SELECT		pronostics_buteurs.Matches_Match' .
						'											,Equipes_Equipe' .
						'											,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
						'								FROM		(' .
						'												SELECT		pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
						'												FROM		pronostics_buteurs' .
						'												JOIN		matches' .
						'															ON		pronostics_buteurs.Matches_Match = matches.Match' .
						'												LEFT JOIN	matches_participants' .
						'															ON		pronostics_buteurs.Matches_Match = matches_participants.Matches_Match' .
						'																	AND		pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
						'																	AND		pronostics_buteurs.Equipes_Equipe = matches_participants.Equipes_Equipe' .
						'												WHERE		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurDetail .
						'															AND		matches.Journees_Journee = ' . $journee .
						'															AND		matches_participants.Joueurs_Joueur IS NULL' .
						'											) pronostics_buteurs' .
						'								JOIN		joueurs' .
						'											ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
						'								GROUP BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Equipes_Equipe' .
						'							) AS ButeursAbsents_Visiteur' .
						'							ON		ButeursAbsents_Visiteur.Matches_Match = matches.Match' .
						'									AND		ButeursAbsents_Visiteur.Equipes_Equipe = equipes_equipevisiteur.Equipe' .
						'		WHERE				pronostiqueurs.Pronostiqueur = ' . $pronostiqueurDetail .
						'							AND		matches.Journees_Journee = ' . $journee .
						'		ORDER BY			matches.Match';

		$req = $bdd->query($ordreSQL);
		$pronostics = $req->fetchAll();
	}


	// Lecture des pronostiqueurs de la confrontation
	$ordreSQL =		'	SELECT		Pronostiqueurs_PronostiqueurA, Pronostiqueurs_PronostiqueurB' .
					'				,IFNULL(pronostiqueursA.Pronostiqueurs_NomUtilisateur, \'-\') AS PronostiqueursA_NomUtilisateur' .
					'				,IFNULL(pronostiqueursB.Pronostiqueurs_NomUtilisateur, \'-\') AS PronostiqueursB_NomUtilisateur' .
					'				,Journees_Journee' .
					'	FROM		confrontations' .
					'	LEFT JOIN	pronostiqueurs pronostiqueursA' .
					'				ON		confrontations.Pronostiqueurs_PronostiqueurA = pronostiqueursA.Pronostiqueur' .
					'	LEFT JOIN	pronostiqueurs pronostiqueursB' .
					'				ON		confrontations.Pronostiqueurs_PronostiqueurB = pronostiqueursB.Pronostiqueur' .
					'	WHERE		Confrontation = ' . $confrontation;
	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetchAll();

	$pronosticsA = $pronosticsB = null;

	if($donnees[0]["Pronostiqueurs_PronostiqueurA"] != null)
		lirePronostics($bdd, $pronostiqueur, $donnees[0]["Pronostiqueurs_PronostiqueurA"], $donnees[0]["Journees_Journee"], $pronosticsA);

	if($donnees[0]["Pronostiqueurs_PronostiqueurB"] != null)
		lirePronostics($bdd, $pronostiqueur, $donnees[0]["Pronostiqueurs_PronostiqueurB"], $donnees[0]["Journees_Journee"], $pronosticsB);

	// Affichage des bons résultats et des pronostics pour chaque pronostiqueur de la confrontation
	echo '<table class="tableau--resultat" id="tablePronostics">';
		echo '<thead>';
			echo '<tr>';
				echo '<th colspan="2" style="border-right: 1px solid #fff;"></th>';
				echo '<th colspan="4" style="border-right: 1px solid #fff;">' . $donnees[0]["PronostiqueursA_NomUtilisateur"] . '</th>';
				echo '<th colspan="4">' . $donnees[0]["PronostiqueursB_NomUtilisateur"] . '</th>';
			echo '</tr>';
			echo '<tr>';
				echo '<th>Matches</th>';
				echo '<th style="border-right: 1px solid #fff;">Score final et buteurs</th>';
				echo '<th>Pronostics</th>';
				echo '<th>Ont marqué</th>';
				echo '<th>N\'ont pas marqué</th>';
				echo '<th style="border-right: 1px solid #fff;">N\'ont pas joué</th>';
				echo '<th>Pronostics</th>';
				echo '<th>Ont marqué</th>';
				echo '<th>N\'ont pas marqué</th>';
				echo '<th>N\'ont pas joué</th>';
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
			for($i = 0; $i < $nombreMatches; $i++) {
				echo '<tr>';
					$equipeDomicileNomCourt = $resultats[$i]["EquipesDomicile_NomCourt"];
					$equipeVisiteurNomCourt = $resultats[$i]["EquipesVisiteur_NomCourt"];
					$equipeDomicileNom = $resultats[$i]["EquipesDomicile_Nom"];
					$equipeVisiteurNom = $resultats[$i]["EquipesVisiteur_Nom"];
					$scoreEquipeDomicile = $resultats[$i]["Matches_ScoreEquipeDomicile"];
					$scoreAPEquipeDomicile = $resultats[$i]["Matches_ScoreAPEquipeDomicile"];
					$scoreEquipeVisiteur = $resultats[$i]["Matches_ScoreEquipeVisiteur"];
					$scoreAPEquipeVisiteur = $resultats[$i]["Matches_ScoreAPEquipeVisiteur"];
					$vainqueur = $resultats[$i]["Matches_Vainqueur"];
					$equipeDomicileButeurs = $resultats[$i]["EquipesDomicile_Buteurs"];
					$equipeVisiteurButeurs = $resultats[$i]["EquipesVisiteur_Buteurs"];
					$pointsQualificationEquipeDomicile = $resultats[$i]["Matches_PointsQualificationEquipeDomicile"];
					$pointsQualificationEquipeVisiteur = $resultats[$i]["Matches_PointsQualificationEquipeVisiteur"];
					$coefficient = $resultats[$i]["Matches_Coefficient"];

					echo '<td title="' . $equipeDomicileNom . ' - ' . $equipeVisiteurNom . '">';
						echo '<label>' . $equipeDomicileNomCourt . ' - ' . $equipeVisiteurNomCourt . '</label><br />';
						echo '<label>Points qualification : ' . $pointsQualificationEquipeDomicile . '-' . $pointsQualificationEquipeVisiteur . '</label>';
					echo '</td>';
					echo '<td style="border-right: 1px solid #fff;">';
						$scoreAffiche = formaterScoreMatch($scoreEquipeDomicile, $scoreAPEquipeDomicile, $scoreEquipeVisiteur, $scoreAPEquipeVisiteur, $vainqueur);
						echo $scoreAffiche . '<br />' . $equipeDomicileButeurs . '<br />' . $equipeVisiteurButeurs;
					echo '</td>';

					// Pronostiqueur A
					if($pronosticsA != null) {
						$pronosticScoreEquipeDomicile = $pronosticsA[$i]["Pronostics_ScoreEquipeDomicile"];
						$pronosticScoreAPEquipeDomicile = $pronosticsA[$i]["Pronostics_ScoreAPEquipeDomicile"];
						$pronosticScoreEquipeVisiteur = $pronosticsA[$i]["Pronostics_ScoreEquipeVisiteur"];
						$pronosticScoreAPEquipeVisiteur = $pronosticsA[$i]["Pronostics_ScoreAPEquipeVisiteur"];
						$pronosticVainqueur = $pronosticsA[$i]["Pronostics_Vainqueur"];
						$buteursDomicile = $pronosticsA[$i]["EquipesDomicile_Buteurs"];
						$buteursVisiteur = $pronosticsA[$i]["EquipesVisiteur_Buteurs"];
						$buteursInvalidesDomicile = $pronosticsA[$i]["EquipesDomicile_ButeursInvalides"];
						$buteursInvalidesVisiteur = $pronosticsA[$i]["EquipesVisiteur_ButeursInvalides"];
						$buteursAbsentsDomicile = $pronosticsA[$i]["EquipesDomicile_ButeursAbsents"];
						$buteursAbsentsVisiteur = $pronosticsA[$i]["EquipesVisiteur_ButeursAbsents"];
						$scoreMatch = $pronosticsA[$i]["Scores_ScoreMatch"];
						$scoreButeur = $pronosticsA[$i]["Scores_ScoreButeur"];
						$scoreBonus = $pronosticsA[$i]["Scores_ScoreBonus"];
						$performance = $pronosticsA[$i]["Performance"];
					}
					else {
						$pronosticScoreEquipeDomicile = '?';
						$pronosticScoreAPEquipeDomicile = '?';
						$pronosticScoreEquipeVisiteur = '?';
						$pronosticScoreAPEquipeVisiteur = '?';
						$pronosticVainqueur = '?';
						$buteursDomicile = '?';
						$buteursVisiteur = '?';
						$buteursInvalidesDomicile = '?';
						$buteursInvalidesVisiteur = '?';
						$buteursAbsentsDomicile = '?';
						$buteursAbsentsVisiteur = '?';
						$scoreMatch = '?';
						$scoreButeur = '?';
						$scoreBonus = '?';
					}

					if($performance == -1)
						$style = 'blanc';
					else if($performance == 0)
						$style = 'orange';
					else
						$style = 'vert';
					echo '<td class="' . $style . '">';
						echo '<div>' . $scoreMatch . ' | ' . $scoreButeur . ' | ' . $scoreBonus . '</div>';
						$scoreAffiche = formaterScoreMatch($pronosticScoreEquipeDomicile, $pronosticScoreAPEquipeDomicile, $pronosticScoreEquipeVisiteur, $pronosticScoreAPEquipeVisiteur, $pronosticVainqueur);
						echo '<div>' . $scoreAffiche . '</div>';
					echo '</td>';
					echo '<td>';
						echo '<label>' . $buteursDomicile . '<br />' . $buteursVisiteur . '</label>';
					echo '</td>';
					echo '<td>';
						echo '<label>' . $buteursInvalidesDomicile . '<br />' . $buteursInvalidesVisiteur . '</label>';
					echo '</td>';
					echo '<td style="border-right: 1px solid #fff;">';
						echo '<label>' . $buteursAbsentsDomicile . '<br />' . $buteursAbsentsVisiteur . '</label>';
					echo '</td>';

					// Pronostiqueur B
					if($pronosticsB != null) {
						$pronosticScoreEquipeDomicile = $pronosticsB[$i]["Pronostics_ScoreEquipeDomicile"];
						$pronosticScoreAPEquipeDomicile = $pronosticsB[$i]["Pronostics_ScoreAPEquipeDomicile"];
						$pronosticScoreEquipeVisiteur = $pronosticsB[$i]["Pronostics_ScoreEquipeVisiteur"];
						$pronosticScoreAPEquipeVisiteur = $pronosticsB[$i]["Pronostics_ScoreAPEquipeVisiteur"];
						$pronosticVainqueur = $pronosticsB[$i]["Pronostics_Vainqueur"];
						$buteursDomicile = $pronosticsB[$i]["EquipesDomicile_Buteurs"];
						$buteursVisiteur = $pronosticsB[$i]["EquipesVisiteur_Buteurs"];
						$buteursInvalidesDomicile = $pronosticsB[$i]["EquipesDomicile_ButeursInvalides"];
						$buteursInvalidesVisiteur = $pronosticsB[$i]["EquipesVisiteur_ButeursInvalides"];
						$buteursAbsentsDomicile = $pronosticsB[$i]["EquipesDomicile_ButeursAbsents"];
						$buteursAbsentsVisiteur = $pronosticsB[$i]["EquipesVisiteur_ButeursAbsents"];
						$scoreMatch = $pronosticsB[$i]["Scores_ScoreMatch"];
						$scoreButeur = $pronosticsB[$i]["Scores_ScoreButeur"];
						$scoreBonus = $pronosticsB[$i]["Scores_ScoreBonus"];
						$performance = $pronosticsB[$i]["Performance"];
					}
					else {
						$pronosticScoreEquipeDomicile = '?';
						$pronosticScoreAPEquipeDomicile = '?';
						$pronosticScoreEquipeVisiteur = '?';
						$pronosticScoreAPEquipeVisiteur = '?';
						$pronosticVainqueur = '?';
						$buteursDomicile = '?';
						$buteursVisiteur = '?';
						$buteursInvalidesDomicile = '?';
						$buteursInvalidesVisiteur = '?';
						$buteursAbsentsDomicile = '?';
						$buteursAbsentsVisiteur = '?';
						$scoreMatch = '?';
						$scoreButeur = '?';
						$scoreBonus = '?';
					}

					if($performance == -1)
						$style = 'blanc';
					else if($performance == 0)
						$style = 'orange';
					else
						$style = 'vert';
					echo '<td class="' . $style . '">';
						echo '<div>' . $scoreMatch . ' | ' . $scoreButeur . ' | ' . $scoreBonus . '</div>';
						$scoreAffiche = formaterScoreMatch($pronosticScoreEquipeDomicile, $pronosticScoreAPEquipeDomicile, $pronosticScoreEquipeVisiteur, $pronosticScoreAPEquipeVisiteur, $pronosticVainqueur);
						echo '<div>' . $scoreAffiche . '</div>';
					echo '</td>';
					echo '<td>';
						echo '<label>' . $buteursDomicile . '<br />' . $buteursVisiteur . '</label>';
					echo '</td>';
					echo '<td>';
						echo '<label>' . $buteursInvalidesDomicile . '<br />' . $buteursInvalidesVisiteur . '</label>';
					echo '</td>';
					echo '<td>';
						echo '<label>' . $buteursAbsentsDomicile . '<br />' . $buteursAbsentsVisiteur . '</label>';
					echo '</td>';
				echo '</tr>';
			}
		echo '</tbody>';
	echo '</table>';
?>
