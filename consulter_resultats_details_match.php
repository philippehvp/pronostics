<?php
	include_once('commun.php');
	include_once('fonctions.php');

	// Affichage des résultats et des pronostics d'un match
	
	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$modeRival = isset($_POST["modeRival"]) ? $_POST["modeRival"] : 0;
	$modeConcurrentDirect = isset($_POST["modeConcurrentDirect"]) ? $_POST["modeConcurrentDirect"] : 0;


	// Bons résultats des matches d'une journée donnée
	$ordreSQL = '	SELECT DISTINCT		vue_resultatsjournees.Match' .
				'						,EquipesDomicile_NomCourt' .
				'						,EquipesVisiteur_NomCourt' .
				'						,EquipesDomicile_Nom' .
				'						,EquipesVisiteur_Nom' .
				'						,Matches_ScoreEquipeDomicile' .
				'						,Matches_ScoreAPEquipeDomicile' .
				'						,Matches_ScoreEquipeVisiteur' .
				'						,Matches_ScoreAPEquipeVisiteur' .
				'						,Matches_Vainqueur' .
				'						,EquipesDomicile_Buteurs' .
				'						,EquipesVisiteur_Buteurs' .
				'						,Matches_CoteEquipeDomicile' .
				'						,Matches_CoteNul' .
				'						,Matches_CoteEquipeVisiteur' .
				'						,Matches_TypeMatch' .
				'	FROM				vue_resultatsjournees' .
				'	WHERE				vue_resultatsjournees.Match = ' . $match .
				'	ORDER BY			vue_resultatsjournees.Match';

	$req = $bdd->query($ordreSQL);
	$resultats = $req->fetchAll();
	
	$nombreMatches = sizeof($resultats);
	
	
	// Si le mode concurrent direct est activé, il est nécessaire de lire d'abord le classement du joueur pour ensuite savoir quelles sont les places à afficher
	// Exemple, le joueur est 15ème, on affiche donc les places 10 à 20
	$borneInferieure = 0;
	$borneSuperieure = 1000;
	if($modeConcurrentDirect == 1) {
		$ordreSQL =		'		SELECT		Classements_ClassementJourneeMatch' .
						'		FROM		classements' .
						'		JOIN		journees' .
						'					ON		classements.Journees_Journee = journees.Journee' .
						'		JOIN		matches' .
						'					ON		journees.Journee = matches.Journees_Journee' .
						'		WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'					AND		matches.Match = ' . $match;
		$req = $bdd->query($ordreSQL);
		$donnees = $req->fetchAll();
		$classementActuel = $donnees[0]["Classements_ClassementJourneeMatch"];
		$borneInferieure = $classementActuel - 5;
		$borneSuperieure = $classementActuel + 5;
	}

	
	
	// Tous les pronostics et pronostics de buteurs d'un match donné
	// Attention toutefois, les modes rival et concurrent direct sont pris en compte
	$ordreSQL =		'	    SELECT DISTINCT		pronostiqueurs.Pronostiqueurs_NomUtilisateur' .
					'							,matches.Match AS `Match`' .
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
					'								THEN	scores.Scores_ScoreMatch' .
					'								ELSE	\'?\'' .
					'							END AS Scores_ScoreMatch' .
					'							,CASE' .
					'								WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
					'								THEN	scores.Scores_ScoreButeur' .
					'								ELSE	\'?\'' .
					'							END AS Scores_ScoreButeur' .
					'							,CASE' .
					'								WHEN	pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' OR matches.Matches_Date <= NOW()' .
					'								THEN	scores.Scores_ScoreBonus' .
					'								ELSE	\'?\'' .
					'							END Scores_ScoreBonus' .
					'							,Matches_Coefficient' .
					'							,CASE' .
					'								WHEN		matches.Matches_DemiFinaleEuropeenne = 1 OR matches.Matches_FinaleEuropeenne = 1' .
					'								THEN		pronostics_carrefinal.PronosticsCarreFinal_Coefficient' .
					'								ELSE		-1' .
					'							END AS PronosticsCarreFinal_Coefficient' .
					'		FROM';
					if($modeRival == 1)
					$ordreSQL .=	'			(' .
					'				SELECT		PronostiqueursRivaux_Pronostiqueur' .
					'				FROM		vue_pronostiqueursrivaux' .
					'				WHERE		vue_pronostiqueursrivaux.Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'				UNION ALL' .
					'				SELECT		' . $_SESSION["pronostiqueur"] . ' AS PronostiqueursRivaux_Pronostiqueur' .
					'			) vue_pronostiqueursrivaux' .
					'	JOIN	pronostiqueurs' .
					'			ON		vue_pronostiqueursrivaux.PronostiqueursRivaux_Pronostiqueur = pronostiqueurs.Pronostiqueur';
					else
					$ordreSQL .=	'			pronostiqueurs';
					
					
					$ordreSQL .=	'		JOIN				(' .
					'								SELECT	*' .
					'								FROM	matches' .
					'								WHERE	matches.Match = ' . $match .
					'							) matches' .
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
					'		JOIN				classements' .
					'							ON		journees.Journee = classements.Journees_Journee' .
					'									AND		pronostiqueurs.Pronostiqueur = classements.Pronostiqueurs_Pronostiqueur' .
					'		LEFT JOIN			(	SELECT		pronostics_buteurs.Pronostiqueurs_Pronostiqueur' .
					'											,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
					'								FROM		(' .
					'												SELECT		Pronostiqueurs_Pronostiqueur' .
					'															,Joueurs_Joueur' .
					'															,Equipes_Equipe' .
					'															,CASE' .
					'																WHEN	@pronostiqueur = Pronostiqueurs_Pronostiqueur' .
					'																		AND		@joueur = Joueurs_Joueur' .
					'																		AND		@equipe = Equipes_Equipe' .
					'																THEN	@bd_indicePronostics := @bd_indicePronostics + 1' .
					'																ELSE	(@bd_indicePronostics := 1) AND (@pronostiqueur := Pronostiqueurs_Pronostiqueur) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
					'															END AS Pronostics_Indice' .
					'												FROM		pronostics_buteurs' .
					'												JOIN		(	SELECT		@bd_indicePronostics := 0, @pronostiqueur := NULL, @joueur := NULL, @equipe := NULL	) r' .
					'												WHERE		pronostics_buteurs.Matches_Match = ' . $match .
					'												ORDER BY	pronostics_buteurs.Pronostiqueurs_Pronostiqueur, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
					'											) pronostics_buteurs' .
					'								JOIN		(' .
					'												SELECT		Joueurs_Joueur' .
					'															,Equipes_Equipe' .
					'															,CASE' .
					'																WHEN	@bd_joueur = Joueurs_Joueur' .
					'																		AND		@bd_equipe = Equipes_Equipe' .
					'																THEN	@bd_indiceMatches := @bd_indiceMatches + 1' .
					'																ELSE	(@bd_indiceMatches := 1) AND (@bd_joueur := Joueurs_Joueur) AND (@bd_equipe := Equipes_Equipe)' .
					'															END AS Matches_Indice' .
					'												FROM		(' .
					'																SELECT		Matches_Match, Joueurs_Joueur, Equipes_Equipe' .
					'																FROM		matches_buteurs' .
					'																WHERE		Matches_Match = ' . $match .
					'																			AND		Buteurs_CSC = 0' .
					'																ORDER BY	Joueurs_Joueur, Equipes_Equipe' .
					'															) matches_buteurs' .
                    '												JOIN		(	SELECT		@bd_indiceMatches := 0, @bd_joueur := NULL, @bd_equipe := NULL	) r' .
                    '                                               JOIN        matches' .
                    '                                                           ON      matches_buteurs.Matches_Match = matches.Match' .
                    '                                                                   AND     matches_buteurs.Equipes_Equipe = matches.Equipes_EquipeDomicile' .
					'											) matches_buteurs' .
					'											ON		pronostics_buteurs.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
					'													AND		pronostics_buteurs.Equipes_Equipe = matches_buteurs.Equipes_Equipe' .
					'													AND		pronostics_buteurs.Pronostics_Indice = matches_buteurs.Matches_Indice' .
					'								JOIN		joueurs' .
					'											ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'								GROUP BY	pronostics_buteurs.Pronostiqueurs_Pronostiqueur' .
					'							) AS Buteurs_Domicile' .
					'							ON		Buteurs_Domicile.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'		LEFT JOIN			(	SELECT		pronostics_buteurs.Pronostiqueurs_Pronostiqueur' .
					'											,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
					'								FROM		(' .
					'												SELECT		Pronostiqueurs_Pronostiqueur' .
					'															,Joueurs_Joueur' .
					'															,Equipes_Equipe' .
					'															,CASE' .
					'																WHEN	@pronostiqueur = Pronostiqueurs_Pronostiqueur' .
					'																		AND		@joueur = Joueurs_Joueur' .
					'																		AND		@equipe = Equipes_Equipe' .
					'																THEN	@bv_indicePronostics := @bv_indicePronostics + 1' .
					'																ELSE	(@bv_indicePronostics := 1) AND (@pronostiqueur := Pronostiqueurs_Pronostiqueur) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
					'															END AS Pronostics_Indice' .
					'												FROM		pronostics_buteurs' .
					'												JOIN		(	SELECT		@bv_indicePronostics := 0, @pronostiqueur := NULL, @joueur := NULL, @equipe := NULL	) r' .
					'												WHERE		pronostics_buteurs.Matches_Match = ' . $match .
					'												ORDER BY	pronostics_buteurs.Pronostiqueurs_Pronostiqueur, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
					'											) pronostics_buteurs' .
					'								JOIN		(' .
					'												SELECT		Joueurs_Joueur' .
					'															,Equipes_Equipe' .
					'															,CASE' .
					'																WHEN	@bv_joueur = Joueurs_Joueur' .
					'																		AND		@bv_equipe = Equipes_Equipe' .
					'																THEN	@bv_indiceMatches := @bv_indiceMatches + 1' .
					'																ELSE	(@bv_indiceMatches := 1) AND (@bv_joueur := Joueurs_Joueur) AND (@bv_equipe := Equipes_Equipe)' .
					'															END AS Matches_Indice' .
					'												FROM		(' .
					'																SELECT		Matches_Match, Joueurs_Joueur, Equipes_Equipe' .
					'																FROM		matches_buteurs' .
					'																WHERE		Matches_Match = ' . $match .
					'																			AND		Buteurs_CSC = 0' .
					'																ORDER BY	Joueurs_Joueur, Equipes_Equipe' .
					'															) matches_buteurs' .
                    '												JOIN		(	SELECT		@bv_indiceMatches := 0, @bv_joueur := NULL, @bv_equipe := NULL	) r' .
                    '                                               JOIN        matches' .
                    '                                                           ON      matches_buteurs.Matches_Match = matches.Match' .
                    '                                                                   AND     matches_buteurs.Equipes_Equipe = matches.Equipes_EquipeVisiteur' .
					'											) matches_buteurs' .
					'											ON		pronostics_buteurs.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
					'													AND		pronostics_buteurs.Equipes_Equipe = matches_buteurs.Equipes_Equipe' .
					'													AND		pronostics_buteurs.Pronostics_Indice = matches_buteurs.Matches_Indice' .
					'								JOIN		joueurs' .
					'											ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'								GROUP BY	pronostics_buteurs.Pronostiqueurs_Pronostiqueur' .
					'							) AS Buteurs_Visiteur' .
					'							ON		Buteurs_Visiteur.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'		LEFT JOIN			(	SELECT		pronostics_buteurs.Pronostiqueurs_Pronostiqueur' .
					'											,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
					'								FROM		(' .
					'												SELECT		pronostics_buteurs.Pronostiqueurs_Pronostiqueur' .
					'															,pronostics_buteurs.Joueurs_Joueur' .
					'															,pronostics_buteurs.Equipes_Equipe' .
					'															,CASE' .
					'																WHEN	@pronostiqueur = pronostics_buteurs.Pronostiqueurs_Pronostiqueur' .
					'																		AND		@joueur = pronostics_buteurs.Joueurs_Joueur' .
					'																		AND		@equipe = pronostics_buteurs.Equipes_Equipe' .
					'																THEN	@bid_indicePronostics := @bid_indicePronostics + 1' .
					'																ELSE	(@bid_indicePronostics := 1) AND (@pronostiqueur := pronostics_buteurs.Pronostiqueurs_Pronostiqueur) AND (@joueur := pronostics_buteurs.Joueurs_Joueur) AND (@equipe := pronostics_buteurs.Equipes_Equipe)' .
					'															END AS Pronostics_Indice' .
					'												FROM		pronostics_buteurs' .
					'												JOIN		matches_participants' .
					'															ON		pronostics_buteurs.Matches_Match = matches_participants.Matches_Match' .
					'																	AND		pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
					'																	AND		pronostics_buteurs.Equipes_Equipe = matches_participants.Equipes_Equipe' .
                    '												JOIN		(	SELECT		@bid_indicePronostics := 0, @pronostiqueur := NULL, @joueur := NULL, @equipe := NULL	) r' .
                    '                                               JOIN        matches' .
                    '                                                           ON       pronostics_buteurs.Matches_Match = matches.Match' .
                    '                                                                    AND      pronostics_buteurs.Equipes_Equipe = matches.Equipes_EquipeDomicile' .
					'												WHERE		pronostics_buteurs.Matches_Match = ' . $match .
					'												ORDER BY	pronostics_buteurs.Pronostiqueurs_Pronostiqueur, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
					'											) pronostics_buteurs' .
					'								LEFT JOIN	(' .
					'												SELECT		Joueurs_Joueur' .
					'															,Equipes_Equipe' .
					'															,CASE' .
					'																WHEN	@bid_joueur = Joueurs_Joueur' .
					'																		AND		@bid_equipe = Equipes_Equipe' .
					'																THEN	@bid_indiceMatches := @bid_indiceMatches + 1' .
					'																ELSE	(@bid_indiceMatches := 1) AND (@bid_joueur := Joueurs_Joueur) AND (@bid_equipe := Equipes_Equipe)' .
					'															END AS Matches_Indice' .
					'												FROM		matches_buteurs' .
                    '												JOIN		(	SELECT		@bid_indiceMatches := 0, @bid_joueur :=  NULL, @bid_equipe := NULL	) r' .
                    '                                               JOIN        matches' .
                    '                                                           ON      matches_buteurs.Matches_Match = matches.Match' .
                    '                                                                   AND     matches_buteurs.Equipes_Equipe = matches.Equipes_EquipeDomicile' .
					'												WHERE		matches_buteurs.Matches_Match = ' . $match .
					'															AND		matches_buteurs.Buteurs_CSC = 0' .
					'												ORDER BY	Joueurs_Joueur, Equipes_Equipe' .
					'											) matches_buteurs' .
					'											ON		pronostics_buteurs.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
					'													AND		pronostics_buteurs.Pronostics_Indice = matches_buteurs.Matches_Indice' .
					'								JOIN		joueurs' .
					'											ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'								WHERE		matches_buteurs.Joueurs_Joueur IS NULL' .
					'								GROUP BY	pronostics_buteurs.Pronostiqueurs_Pronostiqueur' .
					'							) AS ButeursInvalides_Domicile' .
					'							ON		ButeursInvalides_Domicile.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'		LEFT JOIN			(	SELECT		pronostics_buteurs.Pronostiqueurs_Pronostiqueur' .
					'											,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
					'								FROM		(' .
					'												SELECT		pronostics_buteurs.Pronostiqueurs_Pronostiqueur' .
					'															,pronostics_buteurs.Joueurs_Joueur' .
					'															,pronostics_buteurs.Equipes_Equipe' .
					'															,CASE' .
					'																WHEN	@pronostiqueur = pronostics_buteurs.Pronostiqueurs_Pronostiqueur' .
					'																		AND		@joueur = pronostics_buteurs.Joueurs_Joueur' .
					'																		AND		@equipe = pronostics_buteurs.Equipes_Equipe' .
					'																THEN	@biv_indicePronostics := @biv_indicePronostics + 1' .
					'																ELSE	(@biv_indicePronostics := 1) AND (@pronostiqueur := pronostics_buteurs.Pronostiqueurs_Pronostiqueur) AND (@joueur := pronostics_buteurs.Joueurs_Joueur) AND (@equipe := pronostics_buteurs.Equipes_Equipe)' .
					'															END AS Pronostics_Indice' .
					'												FROM		pronostics_buteurs' .
					'												JOIN		matches_participants' .
					'															ON		pronostics_buteurs.Matches_Match = matches_participants.Matches_Match' .
					'																	AND		pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
					'																	AND		pronostics_buteurs.Equipes_Equipe = matches_participants.Equipes_Equipe' .
                    '												JOIN		(	SELECT		@biv_indicePronostics := 0, @pronostiqueur := NULL, @joueur := NULL, @equipe := NULL	) r' .
                    '                                               JOIN        matches' .
                    '                                                           ON       pronostics_buteurs.Matches_Match = matches.Match' .
                    '                                                                    AND      pronostics_buteurs.Equipes_Equipe = matches.Equipes_EquipeVisiteur' .
					'												WHERE		pronostics_buteurs.Matches_Match = ' . $match .
					'												ORDER BY	pronostics_buteurs.Pronostiqueurs_Pronostiqueur, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
					'											) pronostics_buteurs' .
					'								LEFT JOIN	(' .
					'												SELECT		Joueurs_Joueur' .
					'															,Equipes_Equipe' .
					'															,CASE' .
					'																WHEN	@biv_joueur = Joueurs_Joueur' .
					'																		AND		@biv_equipe = Equipes_Equipe' .
					'																THEN	@biv_indiceMatches := @biv_indiceMatches + 1' .
					'																ELSE	(@biv_indiceMatches := 1) AND (@biv_joueur := Joueurs_Joueur) AND (@biv_equipe := Equipes_Equipe)' .
					'															END AS Matches_Indice' .
					'												FROM		matches_buteurs' .
                    '												JOIN		(	SELECT		@biv_indiceMatches := 0, @biv_joueur := NULL, @biv_equipe := NULL	) r' .
                    '                                               JOIN        matches' .
                    '                                                           ON      matches_buteurs.Matches_Match = matches.Match' .
                    '                                                                   AND     matches_buteurs.Equipes_Equipe = matches.Equipes_EquipeVisiteur' .
					'												WHERE		matches_buteurs.Matches_Match = ' . $match .
					'															AND		matches_buteurs.Buteurs_CSC = 0' .
					'												ORDER BY	Joueurs_Joueur, Equipes_Equipe' .
					'											) matches_buteurs' .
					'											ON		pronostics_buteurs.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
					'													AND		pronostics_buteurs.Pronostics_Indice = matches_buteurs.Matches_Indice' .
					'								JOIN		joueurs' .
					'											ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'								WHERE		matches_buteurs.Joueurs_Joueur IS NULL' .
					'								GROUP BY	pronostics_buteurs.Pronostiqueurs_Pronostiqueur' .
					'							) AS ButeursInvalides_Visiteur' .
					'							ON		ButeursInvalides_Visiteur.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'		LEFT JOIN			(' .
					'								SELECT		pronostics_buteurs.Pronostiqueurs_Pronostiqueur' .
					'											,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
					'								FROM		(' .
					'												SELECT		pronostics_buteurs.Pronostiqueurs_Pronostiqueur, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
                    '												FROM		pronostics_buteurs' .
                    '                                               JOIN        matches' .
                    '                                                           ON      pronostics_buteurs.Matches_Match = matches.Match' .
                    '                                                                   AND     pronostics_buteurs.Equipes_Equipe = matches.Equipes_EquipeDomicile' .
					'												LEFT JOIN	matches_participants' .
					'															ON		pronostics_buteurs.Matches_Match = matches_participants.Matches_Match' .
					'																	AND		pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
					'																	AND		pronostics_buteurs.Equipes_Equipe = matches_participants.Equipes_Equipe' .
					'												JOIN		joueurs' .
					'															ON	pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'												WHERE		pronostics_buteurs.Matches_Match = ' . $match .
					'															AND		matches_participants.Joueurs_Joueur IS NULL' .
					'											) pronostics_buteurs' .
					'								JOIN		joueurs' .
					'											ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'								GROUP BY	pronostics_buteurs.Pronostiqueurs_Pronostiqueur' .
					'							) AS ButeursAbsents_Domicile' .
					'							ON		ButeursAbsents_Domicile.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'		LEFT JOIN			(' .
					'								SELECT		pronostics_buteurs.Pronostiqueurs_Pronostiqueur' .
					'											,GROUP_CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) SEPARATOR \', \') AS Buteurs' .
					'								FROM		(' .
					'												SELECT		pronostics_buteurs.Pronostiqueurs_Pronostiqueur, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
                    '												FROM		pronostics_buteurs' .
                    '                                               JOIN        matches' .
                    '                                                           ON      pronostics_buteurs.Matches_Match = matches.Match' .
                    '                                                                   AND     pronostics_buteurs.Equipes_Equipe = matches.Equipes_EquipeVisiteur' .
					'												LEFT JOIN	matches_participants' .
					'															ON		pronostics_buteurs.Matches_Match = matches_participants.Matches_Match' .
					'																	AND		pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
					'																	AND		pronostics_buteurs.Equipes_Equipe = matches_participants.Equipes_Equipe' .
					'												JOIN		joueurs' .
					'															ON	pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'												WHERE		pronostics_buteurs.Matches_Match = ' . $match .
					'															AND		matches_participants.Joueurs_Joueur IS NULL' .
					'											) pronostics_buteurs' .
					'								JOIN		joueurs' .
					'											ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'								GROUP BY	pronostics_buteurs.Pronostiqueurs_Pronostiqueur' .
					'							) AS ButeursAbsents_Visiteur' .
					'							ON		ButeursAbsents_Visiteur.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'		LEFT JOIN			pronostics_carrefinal' .
					'							ON		pronostiqueurs.Pronostiqueur = pronostics_carrefinal.Pronostiqueurs_Pronostiqueur' .
					'									AND		matches.Match = pronostics_carrefinal.Matches_Match' .
					'		WHERE				classements.Classements_ClassementJourneeMatch >= ' . $borneInferieure .
					'							AND		classements.Classements_ClassementJourneeMatch <= ' . $borneSuperieure .
                    '		ORDER BY			pronostiqueurs.Pronostiqueur';

	$req = $bdd->query($ordreSQL);
	$pronostics = $req->fetchAll();
	$nombrePronostiqueurs = sizeof($pronostics) / $nombreMatches;

	// Affichage des bons résultats et de tous les pronostics
	echo '<table class="tableau--resultat" id="tablePronostics">';
		echo '<thead>';
			echo '<tr>';
				echo '<th>Résultats</th>';
				foreach($resultats as $resultat) {
					// Pour chaque ligne de résultat, on affiche le score final, les buteurs
					$equipeDomicileButeurs = $resultat["EquipesDomicile_Buteurs"] != null ? $resultat["EquipesDomicile_Buteurs"] : 'Aucun';
					$equipeVisiteurButeurs = $resultat["EquipesVisiteur_Buteurs"] != null ? $resultat["EquipesVisiteur_Buteurs"] : 'Aucun';
					$coteEquipeDomicile = $resultat["Matches_CoteEquipeDomicile"] != null ? $resultat["Matches_CoteEquipeDomicile"] : '?';
					$coteEquipeNul = $resultat["Matches_CoteNul"] != null ? $resultat["Matches_CoteNul"] : '?';
					$coteEquipeVisiteur = $resultat["Matches_CoteEquipeVisiteur"] != null ? $resultat["Matches_CoteEquipeVisiteur"] : '?';
					echo '<th title="' . $resultat["EquipesDomicile_Nom"] . ' : ' . $equipeDomicileButeurs . '&#13' . $resultat["EquipesVisiteur_Nom"] . ' : ' . $equipeVisiteurButeurs . '">';
						$scoreAffiche = formaterScoreMatch($resultat["Matches_ScoreEquipeDomicile"], $resultat["Matches_ScoreAPEquipeDomicile"], $resultat["Matches_ScoreEquipeVisiteur"], $resultat["Matches_ScoreAPEquipeVisiteur"], $resultat["Matches_Vainqueur"]);
						echo 'Score final ' . $scoreAffiche;
					echo '</th>';
					echo '<th colspan="3">';
						echo $resultat["EquipesDomicile_Nom"] . ' : ' . $equipeDomicileButeurs . '<br />';
						echo $resultat["EquipesVisiteur_Nom"] . ' : ' . $equipeVisiteurButeurs;
					echo '</th>';
				}
			echo '</tr>';
			echo '<tr>';
				echo '<th>&nbsp;</th>';
				echo '<th>Cotes : ' . $coteEquipeDomicile . '-' . $coteEquipeNul . '-' . $coteEquipeVisiteur . '</th>';
				echo '<th>Ont marqué</th>';
				echo '<th>N\'ont pas marqué</th>';
				echo '<th>N\'ont pas joué</th>';
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
			for($i = 0; $i < $nombrePronostiqueurs; $i++) {
				echo '<tr>';
					for($j = 0; $j < $nombreMatches + 1; $j++) {
						if($j == 0) {
							echo '<td>';
							echo $pronostics[($i * $nombreMatches) + $j]["Pronostiqueurs_NomUtilisateur"];
							$pronosticsCarreFinalCoefficient = $pronostics[($i * $nombreMatches) + $j]["PronosticsCarreFinal_Coefficient"];
							if($pronosticsCarreFinalCoefficient != -1)
								echo ' (x' . $pronosticsCarreFinalCoefficient . ')';
						}
						else {
							$indice = ($i * $nombreMatches) + $j - 1;
							$buteursDomicile = $pronostics[$indice]["EquipesDomicile_Buteurs"];
							$buteursVisiteur = $pronostics[$indice]["EquipesVisiteur_Buteurs"];
							$buteursInvalidesDomicile = $pronostics[$indice]["EquipesDomicile_ButeursInvalides"];
							$buteursInvalidesVisiteur = $pronostics[$indice]["EquipesVisiteur_ButeursInvalides"];
							$buteursAbsentsDomicile = $pronostics[$indice]["EquipesDomicile_ButeursAbsents"];
							$buteursAbsentsVisiteur = $pronostics[$indice]["EquipesVisiteur_ButeursAbsents"];
							$scoreMatch = $pronostics[$indice]["Scores_ScoreMatch"] != null ? $pronostics[$indice]["Scores_ScoreMatch"] : '?';
							$scoreMatch == -1 ? '?' : $scoreMatch;
							$scoreButeur = $pronostics[$indice]["Scores_ScoreButeur"] != null ? $pronostics[$indice]["Scores_ScoreButeur"] : '?';
							$scoreButeur == -1 ? '?' : $scoreButeur;
							$scoreBonus = $pronostics[$indice]["Scores_ScoreBonus"] != null ? $pronostics[$indice]["Scores_ScoreBonus"] : '?';
							$scoreBonus == -1 ? '?' : $scoreBonus;
							$pronosticScoreEquipeDomicile = $pronostics[$indice]["Pronostics_ScoreEquipeDomicile"];
							$pronosticScoreAPEquipeDomicile = $pronostics[$indice]["Pronostics_ScoreAPEquipeDomicile"];
							$pronosticScoreEquipeVisiteur = $pronostics[$indice]["Pronostics_ScoreEquipeVisiteur"];
							$pronosticScoreAPEquipeVisiteur = $pronostics[$indice]["Pronostics_ScoreAPEquipeVisiteur"];
							$pronosticVainqueur = $pronostics[$indice]["Pronostics_Vainqueur"];
							
							$coefficient = $pronostics[$indice]["Matches_Coefficient"];
							if($scoreMatch / $coefficient < 5)
								$style = 'blanc';
							else if($scoreMatch / $coefficient >= 5 && $scoreMatch / $coefficient < 10)
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
						}
					}
				echo '</tr>';
			}
		echo '</tbody>';
	echo '</table>';
?>
