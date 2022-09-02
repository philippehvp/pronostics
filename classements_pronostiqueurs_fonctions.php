<?php

	function lirePronostics($journee) {
		$ordreSQL =		'	    SELECT		Pronostiqueur, Journees_Journee' .
						'					,IFNULL(equipesdomicile.Equipes_NomCourt, equipesdomicile.Equipes_Nom) AS EquipesDomicile_NomCourt' .
						'					,equipesdomicile.Equipes_Nom AS EquipesDomicile_Nom' .
						'					,Pronostics_ScoreEquipeDomicile AS Pronostics_ScoreEquipeDomicile' .
						'					,Pronostics_ScoreAPEquipeDomicile' .
						'					,IFNULL(equipesvisiteur.Equipes_NomCourt, equipesvisiteur.Equipes_Nom) AS EquipesVisiteur_NomCourt' .
						'					,equipesvisiteur.Equipes_Nom AS EquipesVisiteur_Nom' .
						'					,Pronostics_ScoreEquipeVisiteur' .
						'					,Pronostics_ScoreAPEquipeVisiteur' .
						'					,Pronostics_Vainqueur' .
						'					,(	SELECT		GROUP_CONCAT(Joueurs_NomCourt SEPARATOR \', \')' .
						'						FROM		pronostics_buteurs' .
						'						JOIN		joueurs' .
						'									ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
						'						WHERE		pronostics_buteurs.Matches_Match = matches.Match' .
						'									AND		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'									AND		pronostics_buteurs.Equipes_Equipe = equipesdomicile.Equipe' .
						'						GROUP BY	pronostics_buteurs.Matches_Match' .
						'					) AS EquipesDomicile_Buteurs' .
						'					,(	SELECT		GROUP_CONCAT(Joueurs_NomCourt SEPARATOR \', \')' .
						'						FROM		pronostics_buteurs' .
						'						JOIN		joueurs' .
						'									ON		pronostics_buteurs.Joueurs_Joueur = joueurs.Joueur' .
						'						WHERE		pronostics_buteurs.Matches_Match = matches.Match' .
						'									AND		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'									AND		pronostics_buteurs.Equipes_Equipe = equipesvisiteur.Equipe' .
						'						GROUP BY	pronostics_buteurs.Matches_Match' .
						'					) AS EquipesVisiteur_Buteurs' .
						'		FROM		pronostics' .
						'		JOIN		matches' .
						'					ON pronostics.Matches_Match = matches.Match' .
						'		JOIN		equipes equipesdomicile' .
						'					ON		matches.Equipes_EquipeDomicile = equipesdomicile.Equipe' .
						'		JOIN		equipes equipesvisiteur' .
						'					ON		matches.Equipes_EquipeVisiteur = equipesvisiteur.Equipe' .
						'		JOIN		pronostiqueurs' .
						'					ON		pronostics.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'		WHERE		Journees_Journee = ' . $journee .
						'					AND		Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'		ORDER BY	matches.Match';

		return $ordreSQL;
	}

	// Recherche de la journée en cours
	function lireJournee($championnat) {
		$ordreSQL =		'	SELECT		journees.Journee' .
						'				,journees.Journees_Nom' .
						'				,IFNULL(journees.Journees_DateMAJ, \'\') AS Journees_DateMAJ' .
						'				,journees.Journees_DateEvenement' .
						'				,(SELECT MAX(Classements_DateReference) FROM classements WHERE classements.Journees_Journee = journees.Journee GROUP BY classements.Journees_Journee) AS Classements_DateReference' .
						'	FROM		journees' .
						'	WHERE		journees.Journee = fn_recherchejourneeencours(' . $championnat . ')';
		return $ordreSQL;
	}

	// Recherche de la journée dont le classement a déjà été au moins une fois calculé
	function lireDerniereJournee($bdd, $championnat) {
		$ordreSQL =		'	SELECT		IFNULL	(' .
						'							MAX(Journees_Journee)' .
						'							,(SELECT MIN(Journee) FROM journees WHERE Championnats_Championnat = ' . $championnat . ')' .
						'						) AS Journees_Journee' .
						'	FROM		classements' .
						'	JOIN		journees' .
						'				ON		classements.Journees_Journee = journees.Journee' .
						'	WHERE		journees.Championnats_Championnat = ' . $championnat .
						'				AND		classements.Classements_ClassementGeneralMatch IS NOT NULL';
		$req = $bdd->query($ordreSQL);
		$journees = $req->fetchAll();
		if(sizeof($journees) > 0)
			$derniereJournee = $journees[0]["Journees_Journee"];
		else
			$derniereJournee = 0;

		$ordreSQL =		'	SELECT		journees.Journee' .
						'				,journees.Journees_Nom' .
						'				,IFNULL(journees.Journees_DateMAJ, \'\') AS Journees_DateMAJ' .
						'				,journees.Journees_DateEvenement' .
						'				,Classements_DateReference' .
						'	FROM		journees' .
						'	JOIN		(' .
						'					SELECT		Journees_Journee, MAX(Classements_DateReference) AS Classements_DateReference' .
						'					FROM		classements' .
						'					WHERE		Journees_Journee = ' . $derniereJournee .
						'					GROUP BY	Journees_Journee' .
						'				) classements' .
						'				ON		journees.Journee = classements.Journees_Journee' .
						'	WHERE		journees.Journee = ' . $derniereJournee;
		return $ordreSQL;
	}

	// Lecture des informations d'une journée
	function lireUneJournee($championnat, $journee) {
		$ordreSQL =		'	SELECT		DISTINCT CASE' .
						'					WHEN	' . $journee . ' = Derniere_Journee' .
						'					THEN	' . $journee .
						'					ELSE	' . ($journee + 1) .
						'				END AS Journee_En_Cours' .
						'				,journees.Journees_Nom' .
						'				,journees.Journees_DateMAJ' .
						'	FROM		(' .
						'					SELECT		(	SELECT		MAX(Journee) AS Journee' .
						'									FROM		matches' .
						'									JOIN		journees' .
						'												ON		matches.Journees_Journee = journees.Journee' .
						'									WHERE		journees.Championnats_Championnat = ' . $championnat .
						'								) AS Derniere_Journee' .
						'					FROM		matches' .
						'					JOIN		journees' .
						'								ON matches.Journees_Journee = journees.Journee' .
						'					JOIN		championnats' .
						'								ON		journees.Championnats_Championnat = championnats.Championnat' .
						'					WHERE		Matches_Date <= NOW()' .
						'								AND matches.Matches_Report = 0' .
						'				) Journees_Max' .
						'	JOIN		journees' .
						'	WHERE		journees.Journee = ' . $journee;
		return $ordreSQL;
	}

	// Affichage du classement général
	function afficherClassementGeneral($bdd, $championnat, $journee, $dateReference, $dtDateMAJ, $journeeNom, $journeeSuivanteActive, $modeModule, $modeRival, $modeConcurrentDirect, $sansButeur) {
		// Classement général du championnat
		$nombrePlaces = 5;

		// Si le mode concurrent direct est activé, il est nécessaire de lire d'abord le classement du joueur pour ensuite savoir quelles sont les places à afficher
		// Exemple, le joueur est 15ème, on affiche donc les places 10 à 20
		$borneInferieure = 0;
		$borneSuperieure = 1000;
		if($modeConcurrentDirect == 1) {
			$ordreSQL =		'		SELECT		Classements_ClassementGeneralMatch' .
							'		FROM		' . ($sansButeur == 0 ? 'classements' : 'classements_sb classements') .
							'		WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
							'					AND		Journees_Journee = ' . $journee .
							'					AND		Classements_DateReference = \'' . $dateReference . '\'';

			$req = $bdd->query($ordreSQL);
			$donnees = $req->fetchAll();
			$classementActuel = $donnees[0]["Classements_ClassementGeneralMatch"];
			$borneInferieure = $classementActuel - $nombrePlaces;
			$borneSuperieure = $classementActuel + $nombrePlaces;
		}

		// Nom interne de la journée
		// Utilisée notamment pour les championnats européens (la journée 1 de LDC ne doit pas en effet avoir le numéro 39 mais 1)
		// Il n'est pas toujours nécessaire de regarder le nombre de pronostics saisis dans la journée suivante (dans le cas de l'affichage de la page des classements par exemple)
		// Par contre, dans le cas du module d'affichage du classement général, cette information a de l'importance
		$ordreSQL =		'	SELECT		(SELECT IFNULL(Journees_NomCourt, Journee) FROM journees WHERE Journee = ' . $journee . ') AS Journees_NomCourt' .
						'				,(SELECT Journee FROM journees WHERE Journee = fn_recherchejourneesuivante(' . $championnat . ')) AS JourneesSuivantes_Journee' .
						'				,(SELECT IFNULL(Journees_NomCourt, Journee) FROM journees WHERE Journee = fn_recherchejourneesuivante(' . $championnat . ')) AS JourneesSuivantes_NomCourt';

		$req = $bdd->query($ordreSQL);
		$journees = $req->fetchAll();
		$journeeNomCourt = $journees[0]["Journees_NomCourt"];
		if(is_numeric($journeeNomCourt))					$journeeNomCourt = 'J' . $journeeNomCourt;


		$journeeSuivanteNomCourt = $journees[0]["JourneesSuivantes_NomCourt"];
		if($journeeSuivanteNomCourt == null)
			$journeeSuivanteNomCourt = '';

		if(is_numeric($journeeSuivanteNomCourt))			$journeeSuivanteNomCourt = 'J' . $journeeSuivanteNomCourt;
		$journeeSuivante = $journees[0]["JourneesSuivantes_Journee"];
		if($journeeSuivante == null)
			$journeeSuivante = $journee;


		$ordreSQL =		'	SELECT		pronostiqueurs.Pronostiqueur' .
						'				,classements.Classements_ClassementGeneralMatch' .
						'				,CASE' .
						'					WHEN	classements_veille.Pronostiqueurs_Pronostiqueur IS NOT NULL' .
						'					THEN	classements_veille.Classements_ClassementGeneralMatch - classements.Classements_ClassementGeneralMatch' .
						'					ELSE	-1000' .
						'				END AS Classement_Evolution' .
						'				,CASE' .
						'					WHEN	classements_veille.Pronostiqueurs_Pronostiqueur IS NOT NULL' .
						'					THEN	classements.Classements_PointsGeneralMatch - classements_veille.Classements_PointsGeneralMatch' .
						'					ELSE	-1000' .
						'				END AS Classement_EvolutionPoints' .
						'				,pronostiqueurs.Pronostiqueurs_NomUtilisateur' .
						'				,IFNULL(pronostiqueurs.Pronostiqueurs_Photo, \'_inconnu.png\') AS Pronostiqueurs_Photo' .
						'				,classements.Classements_PointsGeneralMatch' .
						($sansButeur == 0 ? '				,classements.Classements_PointsGeneralButeur' : '	,0 AS Classements_PointsGeneralButeur') .
						'				,IFNULL(matches_pronostiques.Nombre_MatchesPronostiques, 0) AS Nombre_MatchesPronostiques' .
						'				,IFNULL(matches_theoriques.NombreMatchesTheoriques, 0) AS NombreMatchesTheoriques' .
						'				,CASE' .
						'					WHEN	matches_theoriques_suivants.Journees_Active = 1' .
						'					THEN	matches_pronostiques_suivants.Nombre_MatchesPronostiques' .
						'					ELSE	NULL' .
						'				END AS Nombre_MatchesPronostiquesSuivants' .
						'				,CASE' .
						'					WHEN	matches_theoriques_suivants.Journees_Active = 1' .
						'					THEN	matches_theoriques_suivants.NombreMatchesTheoriques' .
						'					ELSE	NULL' .
						'				END AS NombreMatchesTheoriquesSuivants' .
						'	FROM';

		if($modeRival == 1 && $modeModule == 1)
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


		$ordreSQL .=	'	LEFT JOIN	' . ($sansButeur == 0 ? 'classements' : 'classements_sb classements') .
						'				ON		pronostiqueurs.Pronostiqueur = classements.Pronostiqueurs_Pronostiqueur' .
						'	LEFT JOIN	(' .

						'					SELECT		Pronostiqueurs_Pronostiqueur, classements.Journees_Journee, Classements_ClassementGeneralMatch, Classements_PointsGeneralMatch' .
						'					FROM		' . ($sansButeur == 0 ? 'classements' : 'classements_sb classements') .
						'					JOIN		(' .
						'									SELECT		MAX(Journees_Journee) AS Journee, journees_reference.Classements_DateReference' .
						'									FROM		' . ($sansButeur == 0 ? 'classements' : 'classements_sb classements') .
						'									JOIN		(' .
						'													SELECT		MAX(Classements_DateReference) AS Classements_DateReference' .
						'													FROM		' . ($sansButeur == 0 ? 'classements' : 'classements_sb classements') .
						'													JOIN		journees' .
						'																ON		classements.Journees_Journee = journees.Journee' .
						'													WHERE		Classements_DateReference < \'' . $dateReference . '\'' .
						'																AND		journees.Championnats_Championnat = ' . $championnat .
						'												) journees_reference' .
						'												ON		classements.Classements_DateReference = journees_reference.Classements_DateReference' .
						'									JOIN        journees' .
						'												ON      classements.Journees_Journee = journees.Journee' .
						'									WHERE       journees.Championnats_Championnat = ' . $championnat .
						'									GROUP BY	journees_reference.Classements_DateReference' .
						'								) journees' .
						'								ON		classements.Journees_Journee = journees.Journee' .
						'										AND		classements.Classements_DateReference = journees.Classements_DateReference' .
						'				) classements_veille' .
						'				ON		classements.Pronostiqueurs_Pronostiqueur = classements_veille.Pronostiqueurs_Pronostiqueur' .
						'	LEFT JOIN	(' .
						'					SELECT		Pronostiqueurs_Pronostiqueur, COUNT(*) AS Nombre_MatchesPronostiques' .
						'					FROM		pronostics' .
						'					JOIN		matches' .
						'								ON			pronostics.Matches_Match = matches.Match' .
						'					WHERE		matches.Journees_Journee = ' . $journee .
						'								AND		pronostics.Pronostics_ScoreEquipeDomicile IS NOT NULL' .
						'								AND		pronostics.Pronostics_ScoreEquipeVisiteur IS NOT NULL' .
						'					GROUP BY	Pronostiqueurs_Pronostiqueur' .
						'				) matches_pronostiques' .
						'				ON		pronostiqueurs.Pronostiqueur = matches_pronostiques.Pronostiqueurs_Pronostiqueur' .
						'	LEFT JOIN	(' .
						'					SELECT		Pronostiqueurs_Pronostiqueur, COUNT(*) AS Nombre_MatchesPronostiques' .
						'					FROM		pronostics' .
						'					JOIN		matches' .
						'								ON		pronostics.Matches_Match = matches.Match' .
						'					JOIN		journees' .
						'								ON		matches.Journees_Journee = journees.Journee' .
						'					WHERE		matches.Journees_Journee = ' . $journeeSuivante .
						'								AND		pronostics.Pronostics_ScoreEquipeDomicile IS NOT NULL' .
						'								AND		pronostics.Pronostics_ScoreEquipeVisiteur IS NOT NULL' .
						'								AND		journees.Championnats_Championnat = ' . $championnat .
						'					GROUP BY	Pronostiqueurs_Pronostiqueur' .
						'				) matches_pronostiques_suivants' .
						'				ON		pronostiqueurs.Pronostiqueur = matches_pronostiques_suivants.Pronostiqueurs_Pronostiqueur' .
						'	LEFT JOIN	(' .
						'					SELECT		Pronostiqueur, COUNT(*) AS NombreMatchesTheoriques' .
						'					FROM		pronostiqueurs' .
						'					FULL JOIN	matches' .
						'					JOIN		inscriptions' .
						'								ON		Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
						'					LEFT JOIN	pronostics_carrefinal' .
						'								ON		`Match` = pronostics_carrefinal.Matches_Match' .
						'										AND		Pronostiqueur = pronostics_carrefinal.Pronostiqueurs_Pronostiqueur' .
						'					WHERE		Journees_Journee = ' . $journee .
						'								AND		(' .
						'											PronosticsCarreFinal_Coefficient IS NULL' .
						'											OR		PronosticsCarreFinal_Coefficient <> 0' .
						'										)' .
						'								AND		inscriptions.Championnats_Championnat = ' . $championnat .
						'					GROUP BY	Pronostiqueur' .
						'				) matches_theoriques' .
						'				ON		pronostiqueurs.Pronostiqueur = matches_theoriques.Pronostiqueur' .
						'	LEFT JOIN	(' .
						'					SELECT		Pronostiqueur, COUNT(*) AS NombreMatchesTheoriques, Journees_Active' .
						'					FROM		pronostiqueurs' .
						'					FULL JOIN	matches' .
						'					JOIN		inscriptions' .
						'								ON		Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
						'					JOIN		journees' .
						'								ON		matches.Journees_Journee = journees.Journee' .
						'								AND		inscriptions.Championnats_Championnat = journees.Championnats_Championnat' .
						'					LEFT JOIN	pronostics_carrefinal' .
						'								ON		`Match` = pronostics_carrefinal.Matches_Match' .
						'								AND		Pronostiqueur = pronostics_carrefinal.Pronostiqueurs_Pronostiqueur' .
						'					WHERE		Journees_Journee = ' . $journeeSuivante .
						'								AND		(' .
						'											PronosticsCarreFinal_Coefficient IS NULL' .
						'											OR		PronosticsCarreFinal_Coefficient <> 0' .
						'										)' .
						'								AND		inscriptions.Championnats_Championnat = ' . $championnat .
						'					GROUP BY	Pronostiqueur' .
						'				) matches_theoriques_suivants' .
						'				ON		pronostiqueurs.Pronostiqueur = matches_theoriques_suivants.Pronostiqueur' .
						'	WHERE		classements.Journees_Journee = ' . $journee .
						'				AND		classements.Classements_ClassementGeneralMatch >= ' . $borneInferieure .
						'				AND		classements.Classements_ClassementGeneralMatch <= ' . $borneSuperieure .
						'				AND		Classements_DateReference = \'' . $dateReference . '\'' .
						'				AND		pronostiqueurs.Pronostiqueurs_DateDebutPresence <= \'' . $dateReference . '\'' .
						'				AND		(pronostiqueurs.Pronostiqueurs_DateFinPresence IS NULL OR pronostiqueurs.Pronostiqueurs_DateFinPresence > \'' . $dateReference . '\')' .
						'	ORDER BY	Classements_PointsGeneralMatch DESC, Classements_PointsGeneralButeur ASC, Pronostiqueurs_NomUtilisateur';

		$req = $bdd->query($ordreSQL);
		$classementGeneral = $req->fetchAll();

		if(sizeof($classementGeneral)) {
			// En mode rival activé, on n'affiche pas de top de classement
			if($modeModule == 0)
				$classe = 'tableau--classement tableau--classement--bordure';
			else
				$classe = 'tableau--classement';

			echo '<table class="' . $classe . '">';
				echo $modeModule == 0 ? '<thead class="tableau--classement--entete">' : '<thead>';
					echo '<tr>';
						if($journeeSuivanteActive == 0)						echo '<th colspan="6">';
						else												echo '<th colspan="7">';

							if($dtDateMAJ != '') {
								if($modeModule == 0)			echo '<b>Général (' . $dtDateMAJ->format('d/m/Y H:i') . ')</b>';
								else							echo 'Mise à jour le ' . $dtDateMAJ->format('d/m/Y H:i');
							}
							else {
								if($modeModule == 0)			echo '<b>Général</b>';
							}
						echo '</th>';
					echo '</tr>';
					echo '<tr class="tableau--classement-nom-colonnes">';
						echo '<th>&nbsp;</th>';
						echo '<th>Rang</th>';
						echo '<th>+/-</th>';
						echo '<th class="aligne-gauche">Joueur</th>';
						echo '<th class="aligne-gauche">Score</th>';
						echo '<th title="Nombre de pronostics saisis">' . $journeeNomCourt . '</th>';
						if($journeeSuivanteActive == 1)			echo '<th title="Nombre de pronostics saisis">' . $journeeSuivanteNomCourt . '</th>';
					echo '</tr>';
				echo '</thead>';
				$classementPrecedent = '';
				echo $modeModule == 0 ? '<tbody class="tableau--classement--corps">' : '<tbody>';
					foreach($classementGeneral as $unClassement) {
						echo '<tr class="curseur-main" onclick="classementsPronostiqueurs_afficherPronostiqueur(' . $unClassement["Pronostiqueur"] . ');">';
							if($unClassement["Classements_ClassementGeneralMatch"] <= 5) {
								echo '<td class="tableau--classement--top-classement--photo"><img src="images/pronostiqueurs/' . $unClassement["Pronostiqueurs_Photo"] . '" /></td>';
							}
							else
								echo '<td>&nbsp;</td>';

							if($classementPrecedent == $unClassement["Classements_ClassementGeneralMatch"])					$classementsAffiche = '-';
							else																							$classementsAffiche = $unClassement["Classements_ClassementGeneralMatch"];

							$classementPrecedent = $unClassement["Classements_ClassementGeneralMatch"];

							if($unClassement["Classements_ClassementGeneralMatch"] <= 5) {
								if($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"])
									echo '<td class="tableau--classement--top-classement surbrillance">' . $classementsAffiche . '</td>';
								else
									echo '<td class="tableau--classement--top-classement">' . $classementsAffiche . '</td>';
							}
							else
								echo '<td ' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="surbrillance"' : '') . '>' . $classementsAffiche . '</td>';

							echo '<td' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="surbrillance"' : '') . '>';
								if($unClassement["Classement_Evolution"] == -1000)
									echo '&nbsp;';
								else {
									if($unClassement["Classement_Evolution"] == 0)										echo '<img src="images/identique.gif" alt="" />';
									else if($unClassement["Classement_Evolution"] > 0)									echo '<img src="images/positif.gif" alt="" />+' . $unClassement["Classement_Evolution"];
									else																				echo '<img src="images/negatif.gif" alt="" />' . $unClassement["Classement_Evolution"];
								}
							echo '</td>';
							echo '<td' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="aligne-gauche surbrillance"' : ' class="aligne-gauche"') . '>' . $unClassement["Pronostiqueurs_NomUtilisateur"] . '</td>';
							echo '<td' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="aligne-gauche surbrillance"' : ' class="aligne-gauche"') . '>';
								if($unClassement["Classements_PointsGeneralMatch"] != null && $unClassement["Classements_PointsGeneralMatch"] != '')
									echo $unClassement["Classements_PointsGeneralMatch"] . ' (' . $unClassement["Classements_PointsGeneralButeur"] . ')';
								else
									echo '-';
							echo '</td>';

							// Affichage de l'état des pronostics saisis et des pronostics théoriques de la journée en cours
							if($unClassement["NombreMatchesTheoriques"]) {
								if($unClassement["Nombre_MatchesPronostiques"] == 0)												echo '<td class="rouge">0</td>';
								else if($unClassement["Nombre_MatchesPronostiques"] < $unClassement["NombreMatchesTheoriques"])		echo '<td class="orange">' . $unClassement["Nombre_MatchesPronostiques"] . '</td>';
								else																								echo '<td class="vert">' . $unClassement["NombreMatchesTheoriques"] . '</td>';
							}
							else {
								echo '<td class="vert">-</td>';
							}

							// Affichage de l'état des pronostics saisis et des pronostics théoriques de la journée suivante
							if($journeeSuivanteActive != null && $journeeSuivanteActive) {
								if($unClassement["NombreMatchesTheoriquesSuivants"]) {
									if($unClassement["Nombre_MatchesPronostiquesSuivants"] == 0)														echo '<td class="rouge">0</td>';
									else if($unClassement["Nombre_MatchesPronostiquesSuivants"] < $unClassement["NombreMatchesTheoriquesSuivants"])		echo '<td class="orange">' . $unClassement["Nombre_MatchesPronostiquesSuivants"] . '</td>';
									else																												echo '<td class="vert">' . $unClassement["NombreMatchesTheoriquesSuivants"] . '</td>';
								}
								else {
									echo '<td class="vert">-</td>';
								}
							}



						echo '</tr>';
					}
				echo '</tbody>';
			echo '</table>';
		}
	}

	// Affichage du classement virtuel général
	function afficherClassementGeneralVirtuel($bdd, $championnat, $modeRival, $modeConcurrentDirect) {
		// Classement virtuel général du championnat

		// Equipe championne
		$ordreSQL =		'	SELECT		Equipes_NomCourt' .
						'				,CASE' .
						'					WHEN		bonus_anticipes_equipe_championne.Equipes_Equipe IS NOT NULL' .
						'					THEN		1' .
						'					ELSE		0' .
						'				END AS Equipe_ChampionneAnticipee' .
						'	FROM		classements_virtuels_equipes' .
						'	JOIN		equipes' .
						'				ON		Equipes_Equipe = Equipe' .
						'	LEFT JOIN	bonus_anticipes_equipe_championne' .
						'				ON		classements_virtuels_equipes.Equipes_Equipe = bonus_anticipes_equipe_championne.Equipes_Equipe' .
						'	WHERE		ClassementsEquipes_Classement = 1';

		$req = $bdd->query($ordreSQL);
		$equipe_championne = $req->fetchAll();

		// Trois premières et trois dernières places du championnat
		$ordreSQL =		'	SELECT		equipes.Equipes_NomCourt' .
						'				,CASE' .
						'					WHEN		bonus_anticipes_equipes_podium.Equipes_Equipe IS NOT NULL' .
						'					THEN		1' .
						'					ELSE		0' .
						'				END AS Equipes_PodiumAnticipe' .
						'	FROM		classements_virtuels_equipes' .
						'	JOIN		equipes' .
						'				ON		Equipes_Equipe = Equipe' .
						'	LEFT JOIN	bonus_anticipes_equipes_podium' .
						'				ON		classements_virtuels_equipes.Equipes_Equipe = bonus_anticipes_equipes_podium.Equipes_Equipe' .
						'	WHERE		ClassementsEquipes_Classement <= 3' .
						'	ORDER BY	ClassementsEquipes_Classement ASC';
		$req = $bdd->query($ordreSQL);
		$equipes_podium = $req->fetchAll();

		$ordreSQL =		'	SELECT		equipes.Equipes_NomCourt' .
						'				,CASE' .
						'					WHEN		bonus_anticipes_equipes_relegation.Equipes_Equipe IS NOT NULL' .
						'					THEN		1' .
						'					ELSE		0' .
						'				END AS Equipes_RelegationAnticipee' .
						'	FROM		classements_virtuels_equipes' .
						'	JOIN		equipes' .
						'				ON		Equipes_Equipe = Equipe' .
						'	LEFT JOIN	bonus_anticipes_equipes_relegation' .
						'				ON		classements_virtuels_equipes.Equipes_Equipe = bonus_anticipes_equipes_relegation.Equipes_Equipe' .
						'	WHERE		ClassementsEquipes_Classement >= 18' .
						'	ORDER BY	ClassementsEquipes_Classement ASC';

		$req = $bdd->query($ordreSQL);
		$equipes_relegation = $req->fetchAll();

		// Meilleur(s) buteur(s)
		$ordreSQL =		'	SELECT		GROUP_CONCAT(joueurs.Joueurs_NomFamille SEPARATOR \', \') AS Joueurs_NomFamille' .
						'	FROM		(' .
						'					SELECT		COUNT(*) AS Nombre_Buts, Joueurs_Joueur' .
						'					FROM		matches_buteurs' .
						'					JOIN		matches' .
						'								ON		matches_buteurs.Matches_Match = matches.Match' .
						'					JOIN		journees' .
						'								ON		matches.Journees_Journee = journees.Journee' .
						'					WHERE		journees.Championnats_Championnat = 1' .
						'								AND		Buteurs_CSC = 0' .
						'								AND		Joueurs_Joueur <> 999999' .
						'					GROUP BY	Joueurs_Joueur' .
						'				) meilleurs_buteurs' .
						'	JOIN		(' .
						'					SELECT		MAX(Nombre_Buts) AS Nombre_Buts' .
						'					FROM		(' .
						'									SELECT		COUNT(*) AS Nombre_Buts' .
						'									FROM		matches_buteurs' .
						'									JOIN		matches' .
						'												ON		matches_buteurs.Matches_Match = matches.Match' .
						'									JOIN		journees' .
						'												ON		matches.Journees_Journee = journees.Journee' .
						'									WHERE		journees.Championnats_Championnat = 1' .
						'												AND		Buteurs_CSC = 0' .
						'												AND		Joueurs_Joueur <> 999999' .
						'									GROUP BY	Joueurs_Joueur' .
						'								) buts' .
						'				) nombre_buts' .
						'				ON		meilleurs_buteurs.Nombre_Buts = nombre_buts.Nombre_Buts' .
						'	JOIN		joueurs' .
						'				ON		meilleurs_buteurs.Joueurs_Joueur = joueurs.Joueur';
		$req = $bdd->query($ordreSQL);
		$meilleur_buteur = $req->fetchAll();

		$ordreSQL =		'	SELECT		GROUP_CONCAT(joueurs.Joueurs_NomFamille SEPARATOR \', \') AS Joueurs_NomFamille' .
						'	FROM		bonus_meilleur_passeur' .
						'	JOIN		joueurs' .
						'				ON		bonus_meilleur_passeur.Joueurs_Joueur = joueurs.Joueur';
		$req = $bdd->query($ordreSQL);
		$meilleur_passeur = $req->fetchAll();

		// Lecture des pronostics de bonus
		$ordreSQL =		'	SELECT		CASE' .
						'					WHEN	NOW() >= bonus_date_max.Bonus_Date_Max OR pronostics_bonus.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'					THEN	equipes_championnes.Equipes_NomCourt' .
						'					ELSE	\'?\''.
						'				END AS Equipe_Championne' .
						'				,CASE' .
						'					WHEN	NOW() >= bonus_date_max.Bonus_Date_Max OR pronostics_bonus.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'					THEN	CONCAT(equipes_1.Equipes_NomCourt, \', \', equipes_2.Equipes_NomCourt, \', \', equipes_3.Equipes_NomCourt)' .
						'					ELSE	\'?\''.
						'				END AS Equipes_LDC' .
						'				,CASE' .
						'					WHEN	NOW() >= bonus_date_max.Bonus_Date_Max OR pronostics_bonus.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'					THEN	CONCAT(equipes_18.Equipes_NomCourt, \', \', equipes_19.Equipes_NomCourt, \', \', equipes_20.Equipes_NomCourt)' .
						'					ELSE	\'?\'' .
						'				END AS Equipes_Releguees' .
						'				,CASE' .
						'					WHEN	NOW() >= bonus_date_max.Bonus_Date_Max OR pronostics_bonus.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'					THEN	meilleurs_buteurs.Joueurs_NomFamille' .
						'					ELSE	\'?\'' .
						'				END AS Meilleur_Buteur' .
						'				,CASE' .
						'					WHEN	NOW() >= bonus_date_max.Bonus_Date_Max OR pronostics_bonus.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'					THEN	meilleurs_passeurs.Joueurs_NomFamille' .
						'					ELSE	\'?\'' .
						'				END AS Meilleur_Passeur' .
						'	FROM		pronostics_bonus' .
						'	JOIN		equipes equipes_championnes' .
						'				ON		PronosticsBonus_EquipeChampionne = equipes_championnes.Equipe' .
						'	JOIN		equipes equipes_1' .
						'				ON		pronostics_bonus.PronosticsBonus_EquipeLDC1 = equipes_1.Equipe' .
						'	JOIN		equipes equipes_2' .
						'				ON		pronostics_bonus.PronosticsBonus_EquipeLDC2 = equipes_2.Equipe' .
						'	JOIN		equipes equipes_3' .
						'				ON		pronostics_bonus.PronosticsBonus_EquipeLDC3 = equipes_3.Equipe' .
						'	JOIN		equipes equipes_17' .
						'				ON		pronostics_bonus.PronosticsBonus_EquipeReleguee1 = equipes_17.Equipe' .
						'	JOIN		equipes equipes_18' .
						'				ON		pronostics_bonus.PronosticsBonus_EquipeReleguee2 = equipes_18.Equipe' .
						'	JOIN		equipes equipes_19' .
						'				ON		pronostics_bonus.PronosticsBonus_EquipeReleguee3 = equipes_19.Equipe' .
						'	JOIN		equipes equipes_20' .
						'				ON		pronostics_bonus.PronosticsBonus_EquipeReleguee4 = equipes_20.Equipe' .
						'	JOIN		joueurs meilleurs_buteurs' .
						'				ON		pronostics_bonus.PronosticsBonus_JoueurMeilleurButeur = meilleurs_buteurs.Joueur' .
						'	JOIN		joueurs meilleurs_passeurs' .
						'				ON		pronostics_bonus.PronosticsBonus_JoueurMeilleurPasseur = meilleurs_passeurs.Joueur' .
						'	JOIN		pronostiqueurs' .
						'				ON		pronostics_bonus.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'	JOIN		classements_virtuels' .
						'				ON		pronostics_bonus.Pronostiqueurs_Pronostiqueur = classements_virtuels.Pronostiqueurs_Pronostiqueur' .
						'	CROSS JOIN	bonus_date_max' .
						'	ORDER BY	ClassementsVirtuels_PointsGeneralMatch DESC, ClassementsVirtuels_PointsGeneralButeur ASC, Pronostiqueurs_NomUtilisateur';
		$req = $bdd->query($ordreSQL);
		$pronostics_bonus = $req->fetchAll();

		// Pour le mode concurrent direct, on limite à 5 places au-dessus et en-dessous du pronostiqueur
		$nombrePlaces = 5;

		// Si le mode concurrent direct est activé, il est nécessaire de lire d'abord le classement du joueur pour ensuite savoir quelles sont les places à afficher
		// Exemple, le joueur est 15ème, on affiche donc les places 10 à 20
		$borneInferieure = 0;
		$borneSuperieure = 1000;
		if($modeConcurrentDirect == 1) {
			$ordreSQL =		'		SELECT		ClassementsVirtuels_ClassementGeneralMatch' .
							'		FROM		classements_virtuels' .
							'		WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"];
			$req = $bdd->query($ordreSQL);
			$donnees = $req->fetchAll();
			$classementActuel = $donnees[0]["ClassementsVirtuels_ClassementGeneralMatch"];
			$borneInferieure = $classementActuel - $nombrePlaces;
			$borneSuperieure = $classementActuel + $nombrePlaces;
		}

		$ordreSQL =		'	SELECT		pronostiqueurs.Pronostiqueur' .
						'				,classements_virtuels.ClassementsVirtuels_ClassementGeneralMatch' .
						'				,pronostiqueurs.Pronostiqueurs_NomUtilisateur' .
						'				,IFNULL(pronostiqueurs.Pronostiqueurs_Photo, \'_inconnu.png\') AS Pronostiqueurs_Photo' .
						'				,classements_virtuels.ClassementsVirtuels_PointsGeneralMatch' .
						'				,classements_virtuels.ClassementsVirtuels_PointsGeneralButeur' .
						'				,IFNULL(pronostics_bonuspoints.PronosticsBonusPoints_PointsEquipeChampionne, 0) AS PronosticsBonusPoints_PointsEquipeChampionne' .
						'				,IFNULL(pronostics_bonuspoints.PronosticsBonusPoints_PointsMeilleurButeur, 0) AS PronosticsBonusPoints_PointsMeilleurButeur' .
						'				,IFNULL(pronostics_bonuspoints.PronosticsBonusPoints_PointsMeilleurPasseur, 0) AS PronosticsBonusPoints_PointsMeilleurPasseur' .
						'				,IFNULL(pronostics_bonuspoints.PronosticsBonusPoints_PointsEquipesPodium, 0) AS PronosticsBonusPoints_PointsEquipesPodium' .
						'				,IFNULL(pronostics_bonuspoints.PronosticsBonusPoints_PointsEquipesRelegation, 0) AS PronosticsBonusPoints_PointsEquipesRelegation' .
						'	FROM';

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


		$ordreSQL .=	'	LEFT JOIN	classements_virtuels' .
						'				ON		pronostiqueurs.Pronostiqueur = classements_virtuels.Pronostiqueurs_Pronostiqueur' .
						'	LEFT JOIN	pronostics_bonuspoints' .
						'				ON		pronostiqueurs.Pronostiqueur = pronostics_bonuspoints.Pronostiqueurs_Pronostiqueur' .
						'	WHERE		classements_virtuels.ClassementsVirtuels_ClassementGeneralMatch >= ' . $borneInferieure .
						'				AND		classements_virtuels.ClassementsVirtuels_ClassementGeneralMatch <= ' . $borneSuperieure .
						'	ORDER BY	ClassementsVirtuels_PointsGeneralMatch DESC, ClassementsVirtuels_PointsGeneralButeur ASC, Pronostiqueurs_NomUtilisateur';

		$req = $bdd->query($ordreSQL);
		$classementGeneral = $req->fetchAll();

		if(sizeof($classementGeneral)) {
			echo '<div class="gauche">';
				echo '<div>';
					// En mode rival activé, on n'affiche pas de top de classement
					echo '<table class="tableau--classement">';
						echo '<thead>';
							echo '<tr>';
								echo '<th colspan="4">&nbsp;</th>';
								echo '<th>Champion</th>';
								echo '<th>Podium</th>';
								echo '<th>Relégation</th>';
								echo '<th>Buteur</th>';
								echo '<th>Passeur</th>';
							echo '</tr>';
							echo '<tr class="tableau--classement-nom-colonnes">';
								echo '<th>&nbsp;</th>';
								echo '<th>Rang</th>';
								echo '<th class="aligne-gauche">Joueur</th>';
								echo '<th class="aligne-gauche">Score</th>';
								echo '<th>';
									/*if($equipe_championne[0]["Equipe_ChampionneAnticipee"] == 1)		echo '<label class="texte-vert texte-souligne">' . $equipe_championne[0]['Equipes_NomCourt'] . '</label>';
									else*/																echo '<label">' . $equipe_championne[0]['Equipes_NomCourt'] . '</label>';
								echo '</th>';

								$nombreEquipesPodium = count($equipes_podium);
								echo '<th>';
									for($i = 0; $i < $nombreEquipesPodium; $i++) {
										/*if($equipes_podium[$i]["Equipes_PodiumAnticipe"] == 1)			echo '<label class="texte-vert texte-souligne">' . $equipes_podium[$i]["Equipes_NomCourt"] . '</label>';
										else*/															echo '<label>' . $equipes_podium[$i]["Equipes_NomCourt"] . '</label>';

										if($i < $nombreEquipesPodium - 1)
											echo '<label>, </label>';
									}
								echo '</th>';

								$nombreEquipesRelegation = count($equipes_relegation);
								echo '<th>';
									for($i = 0; $i < $nombreEquipesRelegation; $i++) {
										/*if($equipes_relegation[$i]["Equipes_RelegationAnticipee"] == 1)			echo '<label class="texte-vert texte-souligne">' . $equipes_relegation[$i]["Equipes_NomCourt"] . '</label>';
										else*/																	echo '<label>' . $equipes_relegation[$i]["Equipes_NomCourt"] . '</label>';

										if($i < $nombreEquipesRelegation - 1)
											echo '<label>, </label>';
									}
								echo '</th>';

								echo '<th>' . $meilleur_buteur[0]['Joueurs_NomFamille'] . '</th>';
								echo '<th>' . $meilleur_passeur[0]['Joueurs_NomFamille'] . '</th>';
							echo '</tr>';
						echo '</thead>';
						$classementPrecedent = '';
						echo '<tbody>';
							$i = 0;
							foreach($classementGeneral as $unClassement) {
								echo '<tr class="curseur-main" onclick="classementsPronostiqueurs_afficherPronostiqueur(' . $unClassement["Pronostiqueur"] . ');">';
									if($unClassement["ClassementsVirtuels_ClassementGeneralMatch"] <= 5) {
										echo '<td class="tableau--classement--top-classement--photo"><img src="images/pronostiqueurs/' . $unClassement["Pronostiqueurs_Photo"] . '" /></td>';
									}
									else
										echo '<td>&nbsp;</td>';

									if($classementPrecedent == $unClassement["ClassementsVirtuels_ClassementGeneralMatch"])					$classementsAffiche = '-';
									else																							$classementsAffiche = $unClassement["ClassementsVirtuels_ClassementGeneralMatch"];

									$classementPrecedent = $unClassement["ClassementsVirtuels_ClassementGeneralMatch"];

									if($unClassement["ClassementsVirtuels_ClassementGeneralMatch"] <= 5) {
										if($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"])
											echo '<td class="tableau--classement--top-classement surbrillance">' . $classementsAffiche . '</td>';
										else
											echo '<td class="tableau--classement--top-classement">' . $classementsAffiche . '</td>';
									}
									else
										echo '<td ' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="surbrillance"' : '') . '>' . $classementsAffiche . '</td>';

									echo '<td' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="aligne-gauche surbrillance"' : ' class="aligne-gauche"') . '>' . $unClassement["Pronostiqueurs_NomUtilisateur"] . '</td>';
									echo '<td' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="aligne-gauche surbrillance"' : ' class="aligne-gauche"') . '>';
										if($unClassement["ClassementsVirtuels_PointsGeneralMatch"] != null && $unClassement["ClassementsVirtuels_PointsGeneralMatch"] != '')
											echo $unClassement["ClassementsVirtuels_PointsGeneralMatch"] . ' (' . $unClassement["ClassementsVirtuels_PointsGeneralButeur"] . ')';
										else
											echo '-';
									echo '</td>';

									echo '<td title="' . $pronostics_bonus[$i]["Equipe_Championne"] . '" ' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="surbrillance"' : '') . '>' . $unClassement["PronosticsBonusPoints_PointsEquipeChampionne"] . '</td>';
									echo '<td title="' . $pronostics_bonus[$i]["Equipes_LDC"] . '"' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="surbrillance"' : '') . '>' . $unClassement["PronosticsBonusPoints_PointsEquipesPodium"] . '</td>';
									echo '<td title="' . $pronostics_bonus[$i]["Equipes_Releguees"] . '"' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="surbrillance"' : '') . '>' . $unClassement["PronosticsBonusPoints_PointsEquipesRelegation"] . '</td>';
									echo '<td title="' . $pronostics_bonus[$i]["Meilleur_Buteur"] . '"' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="surbrillance"' : '') . '>' . $unClassement["PronosticsBonusPoints_PointsMeilleurButeur"] . '</td>';
									echo '<td title="' . $pronostics_bonus[$i]["Meilleur_Passeur"] . '"' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="surbrillance"' : '') . '>' . $unClassement["PronosticsBonusPoints_PointsMeilleurPasseur"] . '</td>';
								echo '</tr>';
								$i++;
							}
						echo '</tbody>';
					echo '</table>';
				echo '</div>';
			echo '</div>';
		}
	}

	// Affichage du classement d'une journée
	function afficherClassementJournee($bdd, $championnat, $journee, $dateReference, $dtDateMAJ, $journeeNom, $modeModule, $modeRival, $modeConcurrentDirect, $sansButeur) {
		// Classement d'une journée du championnat

		// Si le mode concurrent direct est activé, il est nécessaire de lire d'abord le classement du joueur pour ensuite savoir quelles sont les places à afficher
		// Exemple, le joueur est 15ème, on affiche donc les places 10 à 20
		$borneInferieure = 0;
		$borneSuperieure = 1000;
		if($modeConcurrentDirect == 1) {
			$ordreSQL =		'		SELECT		Classements_ClassementJourneeMatch' .
							'		FROM		' . ($sansButeur == 0 ? 'classements' : 'classements_sb classements') .
							'		WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
							'					AND		Journees_Journee = ' . $journee;
			if($dateReference != 0)
				$ordreSQL .=		'	AND		Classements_DateReference = \'' . $dateReference . '\'';
			else
				$ordreSQL .=		'	AND		Classements_DateReference IS NULL';

			$req = $bdd->query($ordreSQL);
			$donnees = $req->fetchAll();
			$classementActuel = $donnees[0]["Classements_ClassementJourneeMatch"];
			$borneInferieure = $classementActuel - 5;
			$borneSuperieure = $classementActuel + 5;
		}

		$ordreSQL =		'	SELECT		Pronostiqueur' .
						'				,classements.Classements_ClassementJourneeMatch' .
						'				,classements.Pronostiqueurs_Pronostiqueur' .
						'				,Pronostiqueurs_NomUtilisateur' .
						'				,IFNULL(Pronostiqueurs_Photo, \'_inconnu.png\') AS Pronostiqueurs_Photo' .
						'				,classements.Classements_PointsJourneeMatch' .
						($sansButeur == 0 ? '				,classements.Classements_PointsJourneeButeur' : '	,0 AS Classements_PointsJourneeButeur') .
						'				,IFNULL(matches_pronostiques.Nombre_MatchesPronostiques, 0) AS Nombre_MatchesPronostiques' .
						'				,journees_rattrapage.JourneesRattrapage_Points' .
						'	FROM';

		if($modeRival == 1 && $modeModule == 1)
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

		$ordreSQL .=	'	LEFT JOIN	' . ($sansButeur == 0 ? 'classements' : 'classements_sb classements') .
						'				ON		pronostiqueurs.Pronostiqueur = classements.Pronostiqueurs_Pronostiqueur' .
						'	LEFT JOIN	(' .
						'					SELECT		Pronostiqueurs_Pronostiqueur, COUNT(*) AS Nombre_MatchesPronostiques' .
						'					FROM		pronostics' .
						'					JOIN		matches' .
						'					ON			pronostics.Matches_Match = matches.Match' .
						'					WHERE		matches.Journees_Journee = ' . $journee .
						'								AND		pronostics.Pronostics_ScoreEquipeDomicile IS NOT NULL' .
						'								AND		pronostics.Pronostics_ScoreEquipeVisiteur IS NOT NULL' .
						'					GROUP BY	Pronostiqueurs_Pronostiqueur' .
						'				) matches_pronostiques' .
						'				ON		pronostiqueurs.Pronostiqueur = matches_pronostiques.Pronostiqueurs_Pronostiqueur' .
						'	LEFT JOIN	journees_rattrapage' .
						'				ON		pronostiqueurs.Pronostiqueur = journees_rattrapage.Pronostiqueurs_Pronostiqueur' .
						'						AND		journees_rattrapage.Journees_Journee = ' . $journee .
						'	WHERE		classements.Journees_Journee = ' . $journee .
						'				AND		classements.Classements_ClassementJourneeMatch >= ' . $borneInferieure .
						'				AND		classements.Classements_ClassementJourneeMatch <= ' . $borneSuperieure .
						'				AND		Classements_DateReference = \'' . $dateReference . '\'' .
						'				AND		pronostiqueurs.Pronostiqueurs_DateDebutPresence <= \'' . $dateReference . '\'' .
						'				AND		(pronostiqueurs.Pronostiqueurs_DateFinPresence IS NULL OR pronostiqueurs.Pronostiqueurs_DateFinPresence > \'' . $dateReference . '\')' .
						'	ORDER BY	Classements_ClassementJourneeMatch ASC, Classements_PointsJourneeButeur ASC, Pronostiqueurs_NomUtilisateur';

		$req = $bdd->query($ordreSQL);
		$classementsJournee = $req->fetchAll();

		if(sizeof($classementsJournee)) {
			if($modeModule == 1)		echo '<div class="gauche">';
			else						echo '<div class="gauche" style="margin-left: 20px;">';
				echo '<div>';
					// En mode rival activé, on n'affiche pas de top de classement
					if($modeModule == 0)
						$classe = 'tableau--classement tableau--classement--bordure';
					else $classe = 'tableau--classement';

					echo '<table class="' . $classe . '">';
						echo $modeModule == 0 ? '<thead class="tableau--classement--entete">' : '<thead>';
							echo '<tr>';
								echo '<th colspan="4">';
									echo '<b>' . $journeeNom . '</b>';
								echo '</th>';
							echo '</tr>';
							echo '<tr class="tableau--classement-nom-colonnes">';
								echo '<th>&nbsp;</th>';
								echo '<th>Rang</th>';
								echo '<th class="aligne-gauche">Joueur</th>';
								echo '<th class="aligne-gauche">Score</th>';
							echo '</tr>';
						echo '</thead>';
						$classementPrecedent = '';
						echo $modeModule == 0 ? '<tbody class="tableau--classement--corps">' : '<tbody>';
							foreach($classementsJournee as $unClassement) {
								echo '<tr class="curseur-main" onclick="consulterResultats_afficherPronostiqueur(' . $unClassement["Pronostiqueur"] . ', \'' . $unClassement["Pronostiqueurs_NomUtilisateur"] . '\', ' . $journee . ');">';
									if($unClassement["Classements_ClassementJourneeMatch"] <= 3) {
										echo '<td class="tableau--classement--top-classement--photo"><img src="images/pronostiqueurs/' . $unClassement["Pronostiqueurs_Photo"] . '" /></td>';
									}
									else
										echo '<td>&nbsp;</td>';

									if($classementPrecedent == $unClassement["Classements_ClassementJourneeMatch"])			$classementsAffiche = '-';
									else																					$classementsAffiche = $unClassement["Classements_ClassementJourneeMatch"];

									$classementPrecedent = $unClassement["Classements_ClassementJourneeMatch"];

									if($unClassement["Classements_ClassementJourneeMatch"] <= 3) {
										if($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"])
											echo '<td class="tableau--classement--top-classement surbrillance">' . $classementsAffiche . '</td>';
										else
											echo '<td class="tableau--classement--top-classement">' . $classementsAffiche . '</td>';
									}
									else
										echo '<td ' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="surbrillance"' : '') . '>' . $classementsAffiche . '</td>';

									echo '<td' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="aligne-gauche surbrillance"' : ' class="aligne-gauche"') . '>' . $unClassement["Pronostiqueurs_NomUtilisateur"] . '</td>';

									// L'affichage des points doit prendre en compte les éventuels points de rattrapage
									$pointsJourneeMatch = $unClassement["JourneesRattrapage_Points"] != null ? (($unClassement["Classements_PointsJourneeMatch"] - $unClassement["JourneesRattrapage_Points"]) . '+<i>' . $unClassement["JourneesRattrapage_Points"] . '</i>') : $unClassement["Classements_PointsJourneeMatch"];
									if($unClassement["Classements_PointsJourneeMatch"] != null && $unClassement["Classements_PointsJourneeMatch"] != '')
										echo '<td' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="aligne-gauche surbrillance"' : ' class="aligne-gauche"') . '>' . $pointsJourneeMatch . ' (' . $unClassement["Classements_PointsJourneeButeur"] . ')</td>';
									else
										echo '<td' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="aligne-gauche surbrillance"' : ' class="aligne-gauche"') . '>-</td>';

								echo '</tr>';
							}
							$req->closeCursor();
						echo '</tbody>';
					echo '</table>';
				echo '</div>';
			echo '</div>';

		}
	}

	// Affichage du classement général buteur
	function afficherClassementGeneralButeur($bdd, $championnat, $journee, $dateReference, $dtDateMAJ, $journeeNom) {
		$ordreSQL =		'	SELECT		Pronostiqueur' .
						'				,classements.Classements_ClassementGeneralButeur' .
						'				,CASE' .
						'					WHEN	classements_veille.Pronostiqueurs_Pronostiqueur IS NOT NULL' .
						'					THEN	classements_veille.Classements_ClassementGeneralButeur - classements.Classements_ClassementGeneralButeur' .
						'					ELSE	-1000' .
						'				END AS Classement_Evolution' .
						'				,CASE' .
						'					WHEN	classements_veille.Pronostiqueurs_Pronostiqueur IS NOT NULL' .
						'					THEN	classements.Classements_PointsGeneralButeur - classements_veille.Classements_PointsGeneralButeur' .
						'					ELSE	-1000' .
						'				END AS Classement_EvolutionPoints' .
						'				,classements.Pronostiqueurs_Pronostiqueur' .
						'				,Pronostiqueurs_NomUtilisateur' .
						'				,IFNULL(Pronostiqueurs_Photo, \'_inconnu.png\') AS Pronostiqueurs_Photo' .
						'				,classements.Classements_PointsGeneralButeur' .
						'	FROM		pronostiqueurs' .
						'	JOIN		classements' .
						'				ON		pronostiqueurs.Pronostiqueur = classements.Pronostiqueurs_Pronostiqueur' .
						'	LEFT JOIN	(' .
						'					SELECT		Pronostiqueurs_Pronostiqueur, classements.Journees_Journee, Classements_ClassementGeneralButeur, Classements_PointsGeneralButeur' .
						'					FROM		classements' .
						'					JOIN		(' .
						'									SELECT		MAX(Journees_Journee) AS Journee, journees_reference.Classements_DateReference' .
						'									FROM		classements' .
						'									JOIN		(' .
						'													SELECT		MAX(Classements_DateReference) AS Classements_DateReference' .
						'													FROM		classements' .
						'													JOIN		journees' .
						'																ON		classements.Journees_Journee = journees.Journee' .
						'													WHERE		Classements_DateReference < \'' . $dateReference . '\'' .
						'																AND		journees.Championnats_Championnat = ' . $championnat .
						'												) journees_reference' .
						'												ON		classements.Classements_DateReference = journees_reference.Classements_DateReference' .
						'									JOIN		journees' .
						'												ON		classements.Journees_Journee = journees.Journee' .
						'									WHERE		journees.Championnats_Championnat = ' . $championnat .
						'								) journees' .
						'								ON		classements.Journees_Journee = journees.Journee' .
						'										AND		classements.Classements_DateReference = journees.Classements_DateReference' .
						'				) classements_veille' .
						'				ON		classements.Pronostiqueurs_Pronostiqueur = classements_veille.Pronostiqueurs_Pronostiqueur' .
						'	WHERE		classements.Journees_Journee = ' . $journee .
						'				AND		pronostiqueurs.Pronostiqueurs_DateDebutPresence <= \'' . $dateReference . '\'' .
						'				AND		(pronostiqueurs.Pronostiqueurs_DateFinPresence IS NULL OR pronostiqueurs.Pronostiqueurs_DateFinPresence > \'' . $dateReference . '\')';

			if($dateReference != 0)
				$ordreSQL .=	'	AND		Classements_DateReference = \'' . $dateReference . '\'';
			else
				$ordreSQL .=	'	AND		Classements_DateReference IS NULL';

			$ordreSQL .=		'	ORDER BY	Classements_ClassementGeneralButeur ASC, Pronostiqueurs_NomUtilisateur';

		$req = $bdd->query($ordreSQL);
		$classementsButeur = $req->fetchAll();

		if(sizeof($classementsButeur)) {
			echo '<div class="gauche" style="margin-left: 20px;">';
				echo '<div>';
					echo '<table class="tableau--classement tableau--classement--bordure">';
						echo '<thead class="tableau--classement--entete">';
							echo '<tr>';
								echo '<th colspan="5">';
									echo '<b>Général buteur</b>';
								echo '</th>';
							echo '</tr>';
							echo '<tr class="tableau--classement-nom-colonnes">';
								echo '<th>Rang</th>';
								echo '<th>+/-</th>';
								echo '<th class="aligne-gauche">Joueur</th>';
								echo '<th class="aligne-gauche">Score</th>';
							echo '</tr>';
						echo '</thead>';
						$classementPrecedent = '';
						echo '<tbody class="tableau--classement--corps">';
							foreach($classementsButeur as $unClassement) {
								echo '<tr class="curseur-main" onclick="classementsPronostiqueurs_afficherPronostiqueur(' . $unClassement["Pronostiqueur"] . ');">';
									if($classementPrecedent == '')
										$classementsAffiche = $unClassement["Classements_ClassementGeneralButeur"];
									else if($classementPrecedent == $unClassement["Classements_ClassementGeneralButeur"])
										$classementsAffiche = '-';
									else
										$classementsAffiche = $unClassement["Classements_ClassementGeneralButeur"];

									$classementPrecedent = $unClassement["Classements_ClassementGeneralButeur"];

									echo '<td' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="surbrillance"' : '') . '>' . $classementsAffiche . '</td>';
									echo '<td' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="surbrillance"' : '') . '>';
										if($unClassement["Classement_Evolution"] == -1000)
											echo '&nbsp;';
										else {
											if($unClassement["Classement_Evolution"] == 0)
												echo '<img src="images/identique.gif" alt="" />';
											else if($unClassement["Classement_Evolution"] > 0)
												echo '<img src="images/positif.gif" alt="" />+' . $unClassement["Classement_Evolution"];
											else
												echo '<img src="images/negatif.gif" alt="" />' . $unClassement["Classement_Evolution"];
										}
									echo '</td>';
									echo '<td' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="aligne-gauche surbrillance"' : ' class="aligne-gauche"') . '>' . $unClassement["Pronostiqueurs_NomUtilisateur"] . '</td>';
									echo '<td' . ($_SESSION["pronostiqueur"] == $unClassement["Pronostiqueur"] ? ' class="aligne-gauche surbrillance"' : ' class="aligne-gauche"') . '>' . $unClassement["Classements_PointsGeneralButeur"] . '</td>';
								echo '</tr>';
							}
							$req->closeCursor();
						echo '</tbody>';
					echo '</table>';
				echo '</div>';
			echo '</div>';
		}
	}

	// Affichage des trois classements pour un championnat donné et une journée donnée
	function afficherClassements($bdd, $championnat, $journee, $dateReference, $dtDateMAJ, $journeeNom, $affichageClassementButeur, $affichageJourneeSuivante, $sansButeur) {
		// Dans le cas où l'on demande à voir le nombre de pronostics remplis de la journée suivante, il est nécessaire de voir si cette journée suivante est active ou non
		// Sinon, on ne fait pas cette lecture
		if($affichageJourneeSuivante == 1) {
			$ordreSQL =		'	SELECT		Journees_Active' .
							'	FROM		journees' .
							'	WHERE		Journee = ' . ($journee + 1) .
							'				AND		Championnats_Championnat = ' . $championnat;
			$req = $bdd->query($ordreSQL);
			$journees = $req->fetchAll();
			$journeeSuivanteActive = $journees[0]["Journees_Active"];
		}
		else
			$journeeSuivanteActive = 0;

		$modeModule = 0;
		$modeRival = 0;
		$modeConcurrentDirect = 0;

		echo '<div class="colle-gauche gauche">';
			afficherClassementGeneral($bdd, $championnat, $journee, $dateReference, $dtDateMAJ, $journeeNom, $journeeSuivanteActive, $modeModule, $modeRival, $modeConcurrentDirect, $sansButeur);
		echo '</div>';
		echo '<div class="gauche">';
			afficherClassementJournee($bdd, $championnat, $journee, $dateReference, $dtDateMAJ, $journeeNom, $modeModule, $modeRival, $modeConcurrentDirect, $sansButeur);
		echo '</div>';

		// Classement général buteur du championnat
		if($affichageClassementButeur == 1 && $sansButeur == 0) {
			echo '<div class="gauche">';
				afficherClassementGeneralButeur($bdd, $championnat, $journee, $dateReference, $dtDateMAJ, $journeeNom);
			echo '</div>';
		}
	}
?>
