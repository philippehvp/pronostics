<?php
	include_once('commun.php');
	include_once('fonctions.php');

	// Affichage des résultats et des pronostics d'un pronostiqueur pour une journée
	
	// Lecture des paramètres passés à la page
	$pronostiqueurDetail = isset($_POST["pronostiqueurDetail"]) ? $_POST["pronostiqueurDetail"] : 0;

	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;

	$ordreSQL = 'SELECT DISTINCT Journees_MatchCanalSelectionnable FROM journees WHERE Journee = ' . $journee;
	$req = $bdd->query($ordreSQL);
	$journees = $req->fetchAll();
	if(sizeof($journees) && $journees[0]["Journees_MatchCanalSelectionnable"] == 1)
		$matchCanalSelectionnable = 1;
	else
	$matchCanalSelectionnable = 0;
	

	// Bons résultats des matches d'une journée donnée
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
				'				,IFNULL(EquipesDomicile_Buteurs, \'Aucun\') AS EquipesDomicile_Buteurs' .
				'				,IFNULL(EquipesVisiteur_Buteurs, \'Aucun\') AS EquipesVisiteur_Buteurs' .
				'				,Matches_CoteEquipeDomicile' .
				'				,Matches_CoteNul' .
				'				,Matches_CoteEquipeVisiteur' .
				'				,Matches_TypeMatch' .
				'				,CASE' .
				'					WHEN	Matches_TypeMatch = 3' .
				'					THEN	CASE' .
				'								WHEN	(SELECT Equipes_Vainqueur FROM vue_vainqueursreelsretour WHERE Matches_Match = vue_resultatsjournees.Match) = 1' .
				'								THEN	Matches_PointsQualificationEquipeDomicile' .
				'								WHEN	(SELECT Equipes_Vainqueur FROM vue_vainqueursreelsretour WHERE Matches_Match = vue_resultatsjournees.Match) = 2' .
				'								THEN	Matches_PointsQualificationEquipeVisiteur' .
				'								ELSE	\'?\'' .
				'							END' .
				'					ELSE	NULL' .
				'				END AS Matches_PointsQualification' .
				'				,Matches_PointsQualificationEquipeDomicile' .
				'				,Matches_PointsQualificationEquipeVisiteur' .
				'				,Journees_MatchCanalSelectionnable' .
				'	FROM		vue_resultatsjournees' .
				'	WHERE		Journees_Journee = ' . $journee .
				'	ORDER BY	vue_resultatsjournees.Match';

	$req = $bdd->query($ordreSQL);
	$resultats = $req->fetchAll();
	$nombreMatches = sizeof($resultats);

	// Tous les pronostics et pronostics de buteurs d'un pronostiqueur et d'une journée
	$ordreSQL =		'	   SELECT DISTINCT	matches.Match' .
					'						,CASE' .
					'							WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
					'							THEN	pronostics.Pronostics_ScoreEquipeDomicile' .
					'							ELSE	\'?\'' .
					'						END AS Pronostics_ScoreEquipeDomicile' .
					'						,CASE' .
					'							WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
					'							THEN	pronostics.Pronostics_ScoreAPEquipeDomicile' .
					'							ELSE	\'?\'' .
					'						END AS Pronostics_ScoreAPEquipeDomicile' .
					'						,CASE' .
					'							WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
					'							THEN	pronostics.Pronostics_ScoreEquipeVisiteur' .
					'							ELSE	\'?\'' .
					'						END AS Pronostics_ScoreEquipeVisiteur' .
					'						,CASE' .
					'							WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
					'							THEN	pronostics.Pronostics_ScoreAPEquipeVisiteur' .
					'							ELSE	\'?\'' .
					'						END AS Pronostics_ScoreAPEquipeVisiteur' .
					'						,CASE' .
					'							WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
					'							THEN	Pronostics_Vainqueur' .
					'							ELSE	\'?\'' .
					'						END AS Pronostics_Vainqueur' .
					'						,CASE' .
					'							WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
					'							THEN	Buteurs_Domicile.Buteurs' .
					'							ELSE	\'?\'' .
					'						END AS EquipesDomicile_Buteurs' .
					'						,CASE' .
					'							WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
					'							THEN	Buteurs_Visiteur.Buteurs' .
					'							ELSE	\'?\'' .
					'						END AS EquipesVisiteur_Buteurs' .
					'						,CASE' .
					'							WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
					'							THEN	ButeursInvalides_Domicile.Buteurs' .
					'							ELSE	\'?\'' .
					'						END AS EquipesDomicile_ButeursInvalides' .
					'						,CASE' .
					'							WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
					'							THEN	ButeursInvalides_Visiteur.Buteurs' .
					'							ELSE	\'?\'' .
					'						END AS EquipesVisiteur_ButeursInvalides' .
					'						,CASE' .
					'							WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
					'							THEN	ButeursAbsents_Domicile.Buteurs' .
					'							ELSE	\'?\'' .
					'						END AS EquipesDomicile_ButeursAbsents' .
					'						,CASE' .
					'							WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
					'							THEN	ButeursAbsents_Visiteur.Buteurs' .
					'							ELSE	\'?\'' .
					'						END AS EquipesVisiteur_ButeursAbsents' .
					'						,CASE' .
					'							WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
					'							THEN	scores.Scores_ScoreMatch' .
					'							ELSE	\'?\'' .
					'						END AS Scores_ScoreMatch' .
					'						,CASE' .
					'							WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
					'							THEN	scores.Scores_ScoreButeur' .
					'							ELSE	\'?\'' .
					'						END AS Scores_ScoreButeur' .
					'						,CASE' .
					'							WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
					'							THEN	scores.Scores_ScoreBonus' .
					'							ELSE	\'?\'' .
					'						END Scores_ScoreBonus' .
					'						,CASE' .
					'							WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
					'							THEN	scores.Scores_ScoreQualification' .
					'							ELSE	\'?\'' .
					'						END AS Scores_ScoreQualification' .
					'						,CASE' .
					'							WHEN	journees_pronostiqueurs_canal.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' OR fn_pronosticvisible(matches.Match) = 1' .
					'							THEN	CASE' .
					'										WHEN	matches.Match = journees_pronostiqueurs_canal.Matches_Match' .
					'										THEN	2' .
					'										ELSE	1' .
					'									END' .
					'							ELSE	1' .
					'						END AS Matches_Coefficient' .
					'						,CASE' .
					'							WHEN		matches.Matches_DemiFinaleEuropeenne = 1 OR matches.Matches_FinaleEuropeenne = 1' .
					'							THEN		pronostics_carrefinal.PronosticsCarreFinal_Coefficient' .
					'							ELSE		-1' .
					'						END AS PronosticsCarreFinal_Coefficient' .
					'	FROM				pronostiqueurs' .
					'	JOIN				matches' .
					'	LEFT JOIN			equipes equipes_equipedomicile' .
					'						ON		matches.Equipes_EquipeDomicile = equipes_equipedomicile.Equipe' .
					'	LEFT JOIN			equipes equipes_equipevisiteur' .
					'						ON		matches.Equipes_EquipeVisiteur = equipes_equipevisiteur.Equipe' .
					'	LEFT JOIN			pronostics' .
					'						ON pronostics.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'						AND		matches.Match = pronostics.Matches_Match' .
					'	LEFT JOIN			scores' .
					'						ON		pronostiqueurs.Pronostiqueur = scores.Pronostiqueurs_Pronostiqueur' .
					'						AND		matches.Match = scores.Matches_Match' .
					'	JOIN				inscriptions' .
					'						ON		pronostiqueurs.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
					'	JOIN				journees' .
					'						ON		matches.Journees_Journee = journees.Journee' .
					'						AND		inscriptions.Championnats_Championnat = journees.Championnats_Championnat' .
					'	LEFT JOIN			(	SELECT		pronostics_buteurs.Matches_Match' .
					'										,pronostics_buteurs.Equipes_Equipe' .
					'										,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
					'							FROM		(' .
					'											SELECT		Matches_Match' .
					'														,Joueurs_Joueur' .
					'														,Equipes_Equipe' .
					'														,CASE' .
					'															WHEN	@match = Matches_Match' .
					'																	AND		@joueur = Joueurs_Joueur' .
					'																	AND		@equipe = Equipes_Equipe' .
					'															THEN	@indicePronostics := @indicePronostics + 1' .
					'															ELSE	(@indicePronostics := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
					'														END AS Pronostics_Indice' .
					'											FROM		pronostics_buteurs' .
					'											JOIN		matches' .
					'														ON		pronostics_buteurs.Matches_Match = matches.Match' .
					'											JOIN		(	SELECT		@indicePronostics := 0, @match := NULL, @joueur := NULL, @equipe := NULL	) r' .
					'											WHERE		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurDetail .
					'														AND		matches.Journees_Journee = ' . $journee .
					'											ORDER BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
					'										) pronostics_buteurs' .
					'							JOIN		(' .
					'											SELECT		Matches_Match' .
					'														,Joueurs_Joueur' .
					'														,Equipes_Equipe' .
					'														,CASE' .
					'															WHEN	@match = Matches_Match' .
					'																	AND		@joueur = Joueurs_Joueur' .
					'																	AND		@equipe = Equipes_Equipe' .
					'															THEN	@indiceMatches := @indiceMatches + 1' .
					'															ELSE	(@indiceMatches := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
					'														END AS Matches_Indice' .
					'											FROM		matches_buteurs' .
					'											JOIN		matches' .
					'														ON		matches_buteurs.Matches_Match = matches.Match' .
					'											JOIN		(	SELECT		@indiceMatches := 0, @joueur := NULL, @equipe := NULL	) r' .
					'											WHERE		matches.Journees_Journee = ' . $journee .
					'														AND		matches_buteurs.Buteurs_CSC = 0' .
					'											ORDER BY	Matches_Match, Joueurs_Joueur, Equipes_Equipe' .
					'										) matches_buteurs' .
					'										ON		pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
					'												AND		pronostics_buteurs.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
					'												AND		pronostics_buteurs.Equipes_Equipe = matches_buteurs.Equipes_Equipe' .
					'												AND		pronostics_buteurs.Pronostics_Indice = matches_buteurs.Matches_Indice' .
					'							JOIN		joueurs' .
					'										ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'							GROUP BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Equipes_Equipe' .
					'						) AS Buteurs_Domicile' .
					'						ON		Buteurs_Domicile.Matches_Match = matches.Match' .
					'								AND		Buteurs_Domicile.Equipes_Equipe = equipes_equipedomicile.Equipe' .
					'	LEFT JOIN			(	SELECT		pronostics_buteurs.Matches_Match' .
					'										,pronostics_buteurs.Equipes_Equipe' .
					'										,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
					'							FROM		(' .
					'											SELECT		Matches_Match' .
					'														,Joueurs_Joueur' .
					'														,Equipes_Equipe' .
					'														,CASE' .
					'															WHEN	@match = Matches_Match' .
					'																	AND		@joueur = Joueurs_Joueur' .
					'																	AND		@equipe = Equipes_Equipe' .
					'															THEN	@indicePronostics := @indicePronostics + 1' .
					'															ELSE	(@indicePronostics := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
					'														END AS Pronostics_Indice' .
					'											FROM		pronostics_buteurs' .
					'											JOIN		matches' .
					'														ON		pronostics_buteurs.Matches_Match = matches.Match' .
					'											JOIN		(	SELECT		@indicePronostics := 0, @match := NULL, @joueur := NULL, @equipe := NULL	) r' .
					'											WHERE		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurDetail .
					'														AND		matches.Journees_Journee = ' . $journee .
					'											ORDER BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
					'										) pronostics_buteurs' .
					'							JOIN		(' .
					'											SELECT		Matches_Match' .
					'														,Joueurs_Joueur' .
					'														,Equipes_Equipe' .
					'														,CASE' .
					'															WHEN	@match = Matches_Match' .
					'																	AND		@joueur = Joueurs_Joueur' .
					'																	AND		@equipe = Equipes_Equipe' .
					'															THEN	@indiceMatches := @indiceMatches + 1' .
					'															ELSE	(@indiceMatches := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
					'														END AS Matches_Indice' .
					'											FROM		matches_buteurs' .
					'											JOIN		matches' .
					'														ON		matches_buteurs.Matches_Match = matches.Match' .
					'											JOIN		(	SELECT		@indiceMatches := 0, @joueur := NULL, @equipe := NULL	) r' .
					'											WHERE		matches.Journees_Journee = ' . $journee .
					'														AND		matches_buteurs.Buteurs_CSC = 0' .
					'											ORDER BY	Matches_Match, Joueurs_Joueur, Equipes_Equipe' .
					'										) matches_buteurs' .
					'										ON		pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
					'												AND		pronostics_buteurs.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
					'												AND		pronostics_buteurs.Equipes_Equipe = matches_buteurs.Equipes_Equipe' .
					'												AND		pronostics_buteurs.Pronostics_Indice = matches_buteurs.Matches_Indice' .
					'							JOIN		joueurs' .
					'										ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'							GROUP BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Equipes_Equipe' .
					'						) AS Buteurs_Visiteur' .
					'						ON		Buteurs_Visiteur.Matches_Match = matches.Match' .
					'								AND		Buteurs_Visiteur.Equipes_Equipe = equipes_equipevisiteur.Equipe' .
					'	LEFT JOIN			(	SELECT		pronostics_buteurs.Matches_Match' .
					'										,pronostics_buteurs.Equipes_Equipe' .
					'										,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
					'							FROM		(' .
					'											SELECT		pronostics_buteurs.Matches_Match' .
					'														,pronostics_buteurs.Joueurs_Joueur' .
					'														,pronostics_buteurs.Equipes_Equipe' .
					'														,CASE' .
					'															WHEN	@match = pronostics_buteurs.Matches_Match' .
					'																	AND		@joueur = pronostics_buteurs.Joueurs_Joueur' .
					'																	AND		@equipe = pronostics_buteurs.Equipes_Equipe' .
					'															THEN	@indicePronostics := @indicePronostics + 1' .
					'															ELSE	(@indicePronostics := 1) AND (@match := pronostics_buteurs.Matches_Match) AND (@joueur := pronostics_buteurs.Joueurs_Joueur) AND (@equipe := pronostics_buteurs.Equipes_Equipe)' .
					'														END AS Pronostics_Indice' .
					'											FROM		pronostics_buteurs' .
					'											JOIN		matches_participants' .
					'														ON		pronostics_buteurs.Matches_Match = matches_participants.Matches_Match' .
					'																AND		pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
					'																AND		pronostics_buteurs.Equipes_Equipe = matches_participants.Equipes_Equipe' .
					'											JOIN		matches' .
					'														ON		pronostics_buteurs.Matches_Match = matches.Match' .
					'											JOIN		(	SELECT		@indicePronostics := 0, @match := NULL, @joueur := NULL, @equipe := NULL	) r' .
					'											WHERE		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurDetail .
					'														AND		matches.Journees_Journee = ' . $journee .
					'											ORDER BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
					'										) pronostics_buteurs' .
					'							LEFT JOIN	(' .
					'											SELECT		Matches_Match' .
					'														,Joueurs_Joueur' .
					'														,Equipes_Equipe' .
					'														,CASE' .
					'															WHEN	@match = Matches_Match' .
					'																	AND		@joueur = Joueurs_Joueur' .
					'																	AND		@equipe = Equipes_Equipe' .
					'															THEN	@indiceMatches := @indiceMatches + 1' .
					'															ELSE	(@indiceMatches := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
					'														END AS Matches_Indice' .
					'											FROM		matches_buteurs' .
					'											JOIN		matches' .
					'														ON		matches_buteurs.Matches_Match = matches.Match' .
					'											JOIN		(	SELECT		@indiceMatches := 0, @match := NULL, @joueur :=  NULL, @equipe := NULL	) r' .
					'											WHERE		matches.Journees_Journee = ' . $journee .
					'														AND		matches_buteurs.Buteurs_CSC = 0' .
					'											ORDER BY	Matches_Match, Joueurs_Joueur, Equipes_Equipe' .
					'										) matches_buteurs' .
					'										ON		pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
					'												AND		pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
					'												AND		pronostics_buteurs.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
					'												AND		pronostics_buteurs.Pronostics_Indice = matches_buteurs.Matches_Indice' .
					'							JOIN		joueurs' .
					'										ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'							WHERE		matches_buteurs.Joueurs_Joueur IS NULL' .
					'							GROUP BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Equipes_Equipe' .
					'						) AS ButeursInvalides_Domicile' .
					'						ON		ButeursInvalides_Domicile.Matches_Match = matches.Match' .
					'								AND		ButeursInvalides_Domicile.Equipes_Equipe = equipes_equipedomicile.Equipe' .
					'	LEFT JOIN			(	SELECT		pronostics_buteurs.Matches_Match' .
					'										,pronostics_buteurs.Equipes_Equipe' .
					'										,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
					'							FROM		(' .
					'											SELECT		pronostics_buteurs.Matches_Match' .
					'														,pronostics_buteurs.Joueurs_Joueur' .
					'														,pronostics_buteurs.Equipes_Equipe' .
					'														,CASE' .
					'															WHEN	@match = pronostics_buteurs.Matches_Match' .
					'																	AND		@joueur = pronostics_buteurs.Joueurs_Joueur' .
					'																	AND		@equipe = pronostics_buteurs.Equipes_Equipe' .
					'															THEN	@indicePronostics := @indicePronostics + 1' .
					'															ELSE	(@indicePronostics := 1) AND (@match := pronostics_buteurs.Matches_Match) AND (@joueur := pronostics_buteurs.Joueurs_Joueur) AND (@equipe := pronostics_buteurs.Equipes_Equipe)' .
					'														END AS Pronostics_Indice' .
					'											FROM		pronostics_buteurs' .
					'											JOIN		matches_participants' .
					'														ON		pronostics_buteurs.Matches_Match = matches_participants.Matches_Match' .
					'																AND		pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
					'																AND		pronostics_buteurs.Equipes_Equipe = matches_participants.Equipes_Equipe' .
					'											JOIN		matches' .
					'														ON		pronostics_buteurs.Matches_Match = matches.Match' .
					'											JOIN		(	SELECT		@indicePronostics := 0, @match := NULL, @joueur := NULL, @equipe := NULL	) r' .
					'											WHERE		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurDetail .
					'														AND		matches.Journees_Journee = ' . $journee .
					'											ORDER BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
					'										) pronostics_buteurs' .
					'							LEFT JOIN	(' .
					'											SELECT		Matches_Match' .
					'														,Joueurs_Joueur' .
					'														,Equipes_Equipe' .
					'														,CASE' .
					'															WHEN	@match = Matches_Match' .
					'																	AND		@joueur = Joueurs_Joueur' .
					'																	AND		@equipe = Equipes_Equipe' .
					'															THEN	@indiceMatches := @indiceMatches + 1' .
					'															ELSE	(@indiceMatches := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
					'														END AS Matches_Indice' .
					'											FROM		matches_buteurs' .
					'											JOIN		matches' .
					'														ON		matches_buteurs.Matches_Match = matches.Match' .
					'											JOIN		(	SELECT		@indiceMatches := 0, @match := NULL, @joueur :=  NULL, @equipe := NULL	) r' .
					'											WHERE		matches.Journees_Journee = ' . $journee .
					'														AND		matches_buteurs.Buteurs_CSC = 0' .
					'											ORDER BY	Matches_Match, Joueurs_Joueur, Equipes_Equipe' .
					'										) matches_buteurs' .
					'										ON		pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
					'												AND		pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
					'												AND		pronostics_buteurs.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
					'												AND		pronostics_buteurs.Pronostics_Indice = matches_buteurs.Matches_Indice' .
					'							JOIN		joueurs' .
					'										ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'							WHERE		matches_buteurs.Joueurs_Joueur IS NULL' .
					'							GROUP BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Equipes_Equipe' .
					'						) AS ButeursInvalides_Visiteur' .
					'						ON		ButeursInvalides_Visiteur.Matches_Match = matches.Match' .
					'								AND		ButeursInvalides_Visiteur.Equipes_Equipe = equipes_equipevisiteur.Equipe' .
					'	LEFT JOIN			(' .
					'							SELECT		pronostics_buteurs.Matches_Match' .
					'										,Equipes_Equipe' .
					'										,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
					'							FROM		(' .
					'											SELECT		pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
					'											FROM		pronostics_buteurs' .
					'											JOIN		matches' .
					'														ON		pronostics_buteurs.Matches_Match = matches.Match' .
					'											LEFT JOIN	matches_participants' .
					'														ON		pronostics_buteurs.Matches_Match = matches_participants.Matches_Match' .
					'																AND		pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
					'																AND		pronostics_buteurs.Equipes_Equipe = matches_participants.Equipes_Equipe' .
					'											WHERE		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurDetail .
					'														AND		matches.Journees_Journee = ' . $journee .
					'														AND		matches_participants.Joueurs_Joueur IS NULL' .
					'										) pronostics_buteurs' .
					'							JOIN		joueurs' .
					'										ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'							GROUP BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Equipes_Equipe' .
					'						) AS ButeursAbsents_Domicile' .
					'						ON		ButeursAbsents_Domicile.Matches_Match = matches.Match' .
					'								AND		ButeursAbsents_Domicile.Equipes_Equipe = equipes_equipedomicile.Equipe' .
					'	LEFT JOIN			(' .
					'							SELECT		pronostics_buteurs.Matches_Match' .
					'										,Equipes_Equipe' .
					'										,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
					'							FROM		(' .
					'											SELECT		pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
					'											FROM		pronostics_buteurs' .
					'											JOIN		matches' .
					'														ON		pronostics_buteurs.Matches_Match = matches.Match' .
					'											LEFT JOIN	matches_participants' .
					'														ON		pronostics_buteurs.Matches_Match = matches_participants.Matches_Match' .
					'																AND		pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
					'																AND		pronostics_buteurs.Equipes_Equipe = matches_participants.Equipes_Equipe' .
					'											WHERE		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurDetail .
					'														AND		matches.Journees_Journee = ' . $journee .
					'														AND		matches_participants.Joueurs_Joueur IS NULL' .
					'										) pronostics_buteurs' .
					'							JOIN		joueurs' .
					'										ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'							GROUP BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Equipes_Equipe' .
					'						) AS ButeursAbsents_Visiteur' .
					'						ON		ButeursAbsents_Visiteur.Matches_Match = matches.Match' .
					'								AND		ButeursAbsents_Visiteur.Equipes_Equipe = equipes_equipevisiteur.Equipe' .
					'	LEFT JOIN			pronostics_carrefinal' .
					'						ON		pronostiqueurs.Pronostiqueur = pronostics_carrefinal.Pronostiqueurs_Pronostiqueur' .
					'								AND		matches.Match = pronostics_carrefinal.Matches_Match' .
					'	LEFT JOIN			journees_pronostiqueurs_canal' .
					'						ON		journees_pronostiqueurs_canal.Journees_Journee = ' . $journee .
					'								AND		journees_pronostiqueurs_canal.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurDetail .
					'	WHERE				pronostiqueurs.Pronostiqueur = ' . $pronostiqueurDetail .
					'						AND		matches.Journees_Journee = ' . $journee .
					'	ORDER BY			matches.Match';

	$req = $bdd->query($ordreSQL);
	$pronostics = $req->fetchAll();

	// Affichage des bons résultats et des pronostics pour un pronostiqueur et une journée
	echo '<table class="tableau--resultat" id="tablePronostics">';
		echo '<thead>';
			echo '<tr>';
				if($matchCanalSelectionnable == 1) {
					echo '<th class="colonneMatchCanal">&nbsp;</th>';	
				}
				echo '<th>Matches</th>';
				echo '<th>Score final et buteurs</th>';
				echo '<th>Pronostics</th>';
				echo '<th>Ont marqué</th>';
				echo '<th>N\'ont pas marqué</th>';
				echo '<th>N\'ont pas joué</th>';
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
			for($i = 0; $i < $nombreMatches; $i++) {
				echo '<tr>';
					$typeMatch = $resultats[$i]["Matches_TypeMatch"];
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
					$coteEquipeDomicile = $resultats[$i]["Matches_CoteEquipeDomicile"];
					$coteEquipeNul = $resultats[$i]["Matches_CoteNul"];
					$coteEquipeVisiteur = $resultats[$i]["Matches_CoteEquipeVisiteur"];
					$pointsQualification = $resultats[$i]["Matches_PointsQualification"];
					$pointsQualificationEquipeDomicile = $resultats[$i]["Matches_PointsQualificationEquipeDomicile"];
					$pointsQualificationEquipeVisiteur = $resultats[$i]["Matches_PointsQualificationEquipeVisiteur"];
					$pronosticsCarreFinalCoefficient = $pronostics[$i]["PronosticsCarreFinal_Coefficient"];
					
					if($matchCanalSelectionnable == 1) {
						$matchCanal = $pronostics[$i]["Matches_Coefficient"] == 2 ? 'matchCanal' : '';
						echo '<td class="colonneMatchCanal ' . $matchCanal . '">&nbsp;</td>';
					}

					echo '<td title="' . $equipeDomicileNom . ' - ' . $equipeVisiteurNom . '">';
						echo '<label>' . $equipeDomicileNomCourt . ' - ' . $equipeVisiteurNomCourt;
						if($pronosticsCarreFinalCoefficient != -1)
							echo ' (x' . $pronosticsCarreFinalCoefficient . ')';
						
						echo '</label><br />';
						echo '<label>Cotes : ' . $coteEquipeDomicile . '-' . $coteEquipeNul . '-' . $coteEquipeVisiteur . '</label>';
						if($typeMatch == 3) {
							echo '<br />';
							echo '<label>Points qualification : ' . $pointsQualificationEquipeDomicile . '-' . $pointsQualificationEquipeVisiteur . '</label>';
						}
						else
							
					echo '</td>';
					echo '<td>';
						$scoreAffiche = formaterScoreMatch($scoreEquipeDomicile, $scoreAPEquipeDomicile, $scoreEquipeVisiteur, $scoreAPEquipeVisiteur, $vainqueur);
						echo $scoreAffiche . '<br />' . $equipeDomicileButeurs . '<br />' . $equipeVisiteurButeurs;
					echo '</td>';
					
					$pronosticScoreEquipeDomicile = $pronostics[$i]["Pronostics_ScoreEquipeDomicile"];
					$pronosticScoreEquipeVisiteur = $pronostics[$i]["Pronostics_ScoreEquipeVisiteur"];
					$pronosticVainqueur = $pronostics[$i]["Pronostics_Vainqueur"];
					$buteursDomicile = $pronostics[$i]["EquipesDomicile_Buteurs"];
					$buteursVisiteur = $pronostics[$i]["EquipesVisiteur_Buteurs"];
					$buteursInvalidesDomicile = $pronostics[$i]["EquipesDomicile_ButeursInvalides"];
					$buteursInvalidesVisiteur = $pronostics[$i]["EquipesVisiteur_ButeursInvalides"];
					$buteursAbsentsDomicile = $pronostics[$i]["EquipesDomicile_ButeursAbsents"];
					$buteursAbsentsVisiteur = $pronostics[$i]["EquipesVisiteur_ButeursAbsents"];
					$scoreMatch = $pronostics[$i]["Scores_ScoreMatch"] != null ? $pronostics[$i]["Scores_ScoreMatch"] : '?';
					$scoreButeur = $pronostics[$i]["Scores_ScoreButeur"] != null ? $pronostics[$i]["Scores_ScoreButeur"] : '?';
					$scoreBonus = $pronostics[$i]["Scores_ScoreBonus"] != null ? $pronostics[$i]["Scores_ScoreBonus"] : '?';
					$scoreQualification = $pronostics[$i]["Scores_ScoreQualification"] != null ? $pronostics[$i]["Scores_ScoreQualification"] : '?';
					$coefficient = $pronostics[$i]["Matches_Coefficient"];
					
					$pronosticScoreEquipeDomicile = $pronostics[$i]["Pronostics_ScoreEquipeDomicile"];
					$pronosticScoreAPEquipeDomicile = $pronostics[$i]["Pronostics_ScoreAPEquipeDomicile"];
					$pronosticScoreEquipeVisiteur = $pronostics[$i]["Pronostics_ScoreEquipeVisiteur"];
					$pronosticScoreAPEquipeVisiteur = $pronostics[$i]["Pronostics_ScoreAPEquipeVisiteur"];
					$pronosticVainqueur = $pronostics[$i]["Pronostics_Vainqueur"];
					
					if($scoreMatch / $coefficient < 5)
						$style = 'blanc';
					else if($scoreMatch / $coefficient >= 5 && $scoreMatch / $coefficient < 10)
						$style = 'orange';
					else
						$style = 'vert';
					echo '<td class="' . $style . '">';
						if($typeMatch == 3)
							echo '<div>' . $scoreMatch . ' | ' . $scoreButeur . ' | ' . $scoreBonus . ' | ' . $scoreQualification . '</div>';
						else
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
