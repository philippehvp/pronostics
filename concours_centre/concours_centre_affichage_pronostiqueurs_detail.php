<?php
	include_once('../commun.php');
	
	// Affichage des détails d'un pronostiqueur du concours
	// Lecture des paramètres passés à la page
	$pronostiqueurConsulte = isset($_POST["pronostiqueurConsulte"]) ? $_POST["pronostiqueurConsulte"] : 0;
	$sousOnglet = isset($_POST["sousOnglet"]) ? $_POST["sousOnglet"] : 1;
	$zoneDessinLargeur = isset($_POST["zoneDessinLargeur"]) ? $_POST["zoneDessinLargeur"] : 0;
	$zoneDessinHauteur = isset($_POST["zoneDessinHauteur"]) ? $_POST["zoneDessinHauteur"] : 0;

	// Fiche d'identité du pronostiqueur
	function afficherFicheIdentite($bdd, $pronostiqueurConsulte) {
		$administrateur = isset($_SESSION["administrateur"]) ? $_SESSION["administrateur"] : 0;

		$ordreSQL =		'	SELECT		IFNULL(Pronostiqueurs_Photo, \'_inconnu.png\') AS Pronostiqueurs_Photo, Pronostiqueurs_MEL' .
						'				,DATE_FORMAT(Pronostiqueurs_DateDeNaissance, \'%d/%m/%Y\') AS Pronostiqueurs_DateDeNaissance, Pronostiqueurs_LieuDeResidence' .
						'				,Pronostiqueurs_EquipeFavorite, Pronostiqueurs_Ambitions, Pronostiqueurs_Palmares, Pronostiqueurs_Carriere, Pronostiqueurs_Commentaire' .
						'				,CASE' .
						'					WHEN	pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur IS NOT NULL' .
						'					THEN	1' .
						'					ELSE	0' .
						'				END AS Pronostiqueurs_Rival' .
						'	FROM		pronostiqueurs' .
						'	LEFT JOIN	pronostiqueurs_rivaux' .
						'				ON		pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur = ' . $pronostiqueurConsulte .
						'						AND		pronostiqueurs_rivaux.Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'	WHERE		pronostiqueurs.Pronostiqueur = ' . $pronostiqueurConsulte;

		$req = $bdd->query($ordreSQL);
		$fiche = $req->fetchAll();
		
		$pronostiqueurPhoto = $fiche[0]["Pronostiqueurs_Photo"] != null ? $fiche[0]["Pronostiqueurs_Photo"] : '';
		$pronostiqueurMEL = $fiche[0]["Pronostiqueurs_MEL"] != null ? $fiche[0]["Pronostiqueurs_MEL"] : '';
		$pronostiqueurDateDeNaissance = $fiche[0]["Pronostiqueurs_DateDeNaissance"] != null ? $fiche[0]["Pronostiqueurs_DateDeNaissance"] : '';
		$pronostiqueurLieuDeResidence = $fiche[0]["Pronostiqueurs_LieuDeResidence"] != null ? $fiche[0]["Pronostiqueurs_LieuDeResidence"] : '';
		$pronostiqueurEquipeFavorite = $fiche[0]["Pronostiqueurs_EquipeFavorite"] != null ? $fiche[0]["Pronostiqueurs_EquipeFavorite"] : '';
		$pronostiqueurAmbitions = $fiche[0]["Pronostiqueurs_Ambitions"] != null ? $fiche[0]["Pronostiqueurs_Ambitions"] : '';
		$pronostiqueurPalmares = $fiche[0]["Pronostiqueurs_Palmares"] != null ? $fiche[0]["Pronostiqueurs_Palmares"] : '';
		$pronostiqueurCarriere = $fiche[0]["Pronostiqueurs_Carriere"] != null ? $fiche[0]["Pronostiqueurs_Carriere"] : '';
		$pronostiqueurCommentaire = $fiche[0]["Pronostiqueurs_Commentaire"] != null ? $fiche[0]["Pronostiqueurs_Commentaire"] : '';
		$pronostiqueurRival = $fiche[0]["Pronostiqueurs_Rival"] != null ? $fiche[0]["Pronostiqueurs_Rival"] : 0;

		echo '<div class="colle-gauche gauche aligne-centre">';
			echo '<img class="photo cc--vignette--bordure-grise" src="images/pronostiqueurs/' . $pronostiqueurPhoto . '" alt="" /><br />';
			
			if($administrateur)
				echo '<label class="bouton" onclick="consulterFiches_majPronostiqueur(' . $pronostiqueurConsulte . ');">Mettre à jour</label>';
		echo '</div>';

		echo '<div class="gauche" style="margin-left: 20px;">';
			echo '<label>Né(e) le ' . $pronostiqueurDateDeNaissance . ', habite ' .  $pronostiqueurLieuDeResidence . '.</label>';
			echo '<label> Adresse mail : <b>' . $pronostiqueurMEL . '</b></label><br />';
			echo '<label>Equipe favorite : ' . $pronostiqueurEquipeFavorite . '.</label>';
		
			echo '<div class="colle-gauche">';
				echo '<div class="gauche">';
					echo '<label class="cc--pronostiqueurs-detail--section">Ambitions</label><textarea id="taAmbitions" class="grande-zone">' . $pronostiqueurAmbitions . '</textarea><br />';
					echo '<label class="cc--pronostiqueurs-detail--section">Commentaire</label><textarea id="taCommentaire" class="grande-zone">' . $pronostiqueurCommentaire . '</textarea>';
				echo '</div>';
			
				echo '<div class="gauche" style="margin-left: 20px;">';
					if($administrateur) {
						echo '<label class="cc--pronostiqueurs-detail--section">Palmarès</label><textarea id="taPalmares" class="grande-zone">' . $pronostiqueurPalmares . '</textarea><br />';
						echo '<label class="cc--pronostiqueurs-detail--section">Carrière</label><textarea id="taCarriere" class="grande-zone">' . $pronostiqueurCarriere . '</textarea>';
					}
					else {
						echo '<label class="cc--pronostiqueurs-detail--section">Palmarès</label><textarea id="taPalmares" class="grande-zone" readonly="true">' . $pronostiqueurPalmares . '</textarea><br />';
						echo '<label class="cc--pronostiqueurs-detail--section">Carrière</label><textarea id="taCarriere" class="grande-zone" readonly="true">' . $pronostiqueurCarriere . '</textarea>';
					}
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
	
	// Palmarès de la saison par championnat
	function afficherPalmares($bdd, $pronostiqueurConsulte) {
		// Lecture des différents trophées du pronostiqueur pour ses championnats
		$ordreSQL =		'	SELECT		Championnat, Championnats_Nom' .
						'	FROM		inscriptions' .
						'	JOIN		championnats' .
						'				ON		inscriptions.Championnats_Championnat = championnats.Championnat' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'				AND		Championnats_Championnat NOT IN (4, 5)' .
						'	ORDER BY	Championnats_Championnat';
		$req = $bdd->query($ordreSQL);
		$championnats = $req->fetchAll();
		
		// Parcours des championnats du pronostiqueur
		foreach($championnats as $unChampionnat) {
			echo '<div style="margin-bottom: 20px;">';
				// Journées de poulpe d'or
				$ordreSQL =		'	SELECT		GROUP_CONCAT(IFNULL(Journees_NomCourt, Journees_Journee) SEPARATOR \', \') AS Journees_Journees, COUNT(*) AS Nombre' .
								'	FROM		trophees' .
								'	JOIN		journees' .
								'				ON		trophees.Journees_Journee = journees.Journee' .
								'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
								'				AND		journees.Championnats_Championnat = ' . $unChampionnat["Championnat"] .
								'				AND		Trophees_CodeTrophee = 1' .
								'	GROUP BY	Trophees_CodeTrophee';
				$req = $bdd->query($ordreSQL);
				$tropheesPoulpeOr = $req->fetchAll();
				if(sizeof($tropheesPoulpeOr) > 0) {
					$nombrePoulpeOr = $tropheesPoulpeOr[0]["Nombre"];
					$journeesPoulpeOr = $tropheesPoulpeOr[0]["Journees_Journees"];
				}

				// Journées de poulpe d'argent
				$ordreSQL =		'	SELECT		GROUP_CONCAT(IFNULL(Journees_NomCourt, Journees_Journee) SEPARATOR \', \') AS Journees_Journees, COUNT(*) AS Nombre' .
								'	FROM		trophees' .
								'	JOIN		journees' .
								'				ON		trophees.Journees_Journee = journees.Journee' .
								'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
								'				AND		journees.Championnats_Championnat = ' . $unChampionnat["Championnat"] .
								'				AND		Trophees_CodeTrophee = 2' .
								'	GROUP BY	Trophees_CodeTrophee';
				$req = $bdd->query($ordreSQL);
				$tropheesPoulpeArgent = $req->fetchAll();
				if(sizeof($tropheesPoulpeArgent) > 0) {
					$nombrePoulpeArgent = $tropheesPoulpeArgent[0]["Nombre"];
					$journeesPoulpeArgent = $tropheesPoulpeArgent[0]["Journees_Journees"];
				}

				// Journées de poulpe de bronze
				$ordreSQL =		'	SELECT		GROUP_CONCAT(IFNULL(Journees_NomCourt, Journees_Journee) SEPARATOR \', \') AS Journees_Journees, COUNT(*) AS Nombre' .
								'	FROM		trophees' .
								'	JOIN		journees' .
								'				ON		trophees.Journees_Journee = journees.Journee' .
								'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
								'				AND		journees.Championnats_Championnat = ' . $unChampionnat["Championnat"] .
								'				AND		Trophees_CodeTrophee = 3' .
								'	GROUP BY	Trophees_CodeTrophee';
				$req = $bdd->query($ordreSQL);
				$tropheesPoulpeBronze = $req->fetchAll();
				if(sizeof($tropheesPoulpeBronze) > 0) {
					$nombrePoulpeBronze = $tropheesPoulpeBronze[0]["Nombre"];
					$journeesPoulpeBronze = $tropheesPoulpeBronze[0]["Journees_Journees"];
				}
				
				// Journées soulier d'or
				$ordreSQL =		'	SELECT		GROUP_CONCAT(IFNULL(Journees_NomCourt, Journees_Journee) SEPARATOR \', \') AS Journees_Journees, COUNT(*) AS Nombre' .
								'	FROM		trophees' .
								'	JOIN		journees' .
								'				ON		trophees.Journees_Journee = journees.Journee' .
								'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
								'				AND		journees.Championnats_Championnat = ' . $unChampionnat["Championnat"] .
								'				AND		Trophees_CodeTrophee = 4' .
								'	GROUP BY	Trophees_CodeTrophee';
				$req = $bdd->query($ordreSQL);
				$tropheesSoulierOr = $req->fetchAll();
				if(sizeof($tropheesSoulierOr) > 0) {
					$nombreSoulierOr = $tropheesSoulierOr[0]["Nombre"];
					$journeesSoulierOr = $tropheesSoulierOr[0]["Journees_Journees"];
				}

				// Journées Brandao
				$ordreSQL =		'	SELECT		GROUP_CONCAT(IFNULL(Journees_NomCourt, Journees_Journee) SEPARATOR \', \') AS Journees_Journees, COUNT(*) AS Nombre' .
								'	FROM		trophees' .
								'	JOIN		journees' .
								'				ON		trophees.Journees_Journee = journees.Journee' .
								'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
								'				AND		journees.Championnats_Championnat = ' . $unChampionnat["Championnat"] .
								'				AND		Trophees_CodeTrophee = 5' .
								'	GROUP BY	Trophees_CodeTrophee';
				$req = $bdd->query($ordreSQL);
				$tropheesBrandao = $req->fetchAll();
				if(sizeof($tropheesBrandao) > 0) {
					$nombreBrandao = $tropheesBrandao[0]["Nombre"];
					$journeesBrandao = $tropheesBrandao[0]["Journees_Journees"];
				}

				// Journées Dja Djédjé
				$ordreSQL =		'	SELECT		GROUP_CONCAT(IFNULL(Journees_NomCourt, Journees_Journee) SEPARATOR \', \') AS Journees_Journees, COUNT(*) AS Nombre' .
								'	FROM		trophees' .
								'	JOIN		journees' .
								'				ON		trophees.Journees_Journee = journees.Journee' .
								'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
								'				AND		journees.Championnats_Championnat = ' . $unChampionnat["Championnat"] .
								'				AND		Trophees_CodeTrophee = 6' .
								'	GROUP BY	Trophees_CodeTrophee';
				$req = $bdd->query($ordreSQL);
				$tropheesDjaDjedje = $req->fetchAll();
				if(sizeof($tropheesDjaDjedje) > 0) {
					$nombreDjaDjedje = $tropheesDjaDjedje[0]["Nombre"];
					$journeesDjaDjedje = $tropheesDjaDjedje[0]["Journees_Journees"];
				}
				
				// Journées record de points
				$ordreSQL =		'	SELECT		GROUP_CONCAT(IFNULL(Journees_NomCourt, Journees_Journee) SEPARATOR \', \') AS Journees_Journees, COUNT(*) AS Nombre' .
								'	FROM		trophees' .
								'	JOIN		journees' .
								'				ON		trophees.Journees_Journee = journees.Journee' .
								'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
								'				AND		journees.Championnats_Championnat = ' . $unChampionnat["Championnat"] .
								'				AND		Trophees_CodeTrophee = 7' .
								'	GROUP BY	Trophees_CodeTrophee';
				$req = $bdd->query($ordreSQL);
				$tropheesRecordPoints = $req->fetchAll();
				if(sizeof($tropheesRecordPoints) > 0) {
					$nombreRecordPoints = $tropheesRecordPoints[0]["Nombre"];
					$journeesRecordPoints = $tropheesRecordPoints[0]["Journees_Journees"];
				}

				// Journées record de points buteur
				$ordreSQL =		'	SELECT		GROUP_CONCAT(IFNULL(Journees_NomCourt, Journees_Journee) SEPARATOR \', \') AS Journees_Journees, COUNT(*) AS Nombre' .
								'	FROM		trophees' .
								'	JOIN		journees' .
								'				ON		trophees.Journees_Journee = journees.Journee' .
								'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
								'				AND		journees.Championnats_Championnat = ' . $unChampionnat["Championnat"] .
								'				AND		Trophees_CodeTrophee = 8' .
								'	GROUP BY	Trophees_CodeTrophee';
				$req = $bdd->query($ordreSQL);
				$tropheesRecordPointsButeur = $req->fetchAll();
				if(sizeof($tropheesRecordPointsButeur) > 0) {
					$nombreRecordPointsButeur = $tropheesRecordPointsButeur[0]["Nombre"];
					$journeesRecordPointsButeur = $tropheesRecordPointsButeur[0]["Journees_Journees"];
				}

				echo '<label><b>' . $unChampionnat["Championnats_Nom"] . '</b></label><br />';
				
				echo '<table class="cc--pronostiqueurs-detail--legende">';
					echo '<thead>';
						echo '<tr>';
							echo '<th>Trophées</th><th>Nombre de fois et journées</th><th>Trophées</th><th>Nombre de fois et journées</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
						echo '<tr>';
							echo '<td>Poulpe d\'Or</td><td>' . (sizeof($tropheesPoulpeOr) == 0 ? '-' : ($nombrePoulpeOr . ' : ' . $journeesPoulpeOr)) . '</td>';
							echo '<td>Trophée Brandao</td><td>' . (sizeof($tropheesBrandao) == 0 ? '-' : ($nombreBrandao . ' : ' . $journeesBrandao)) . '</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Poulpe d\'Argent</td><td>' . (sizeof($tropheesPoulpeArgent) == 0 ? '-' : ($nombrePoulpeArgent . ' : ' . $journeesPoulpeArgent)) . '</td>';
							echo '<td>Trophée Jérémy Morel</td><td>' . (sizeof($tropheesDjaDjedje) == 0 ? '-' : ($nombreDjaDjedje . ' : ' . $journeesDjaDjedje)) . '</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Poulpe de Bronze</td><td>' . (sizeof($tropheesPoulpeBronze) == 0 ? '-' : ($nombrePoulpeBronze . ' : ' . $journeesPoulpeBronze)) . '</td>';
							echo '<td>Record de points</td><td>' . (sizeof($tropheesRecordPoints) == 0 ? '-' : ($nombreRecordPoints . ' : ' . $journeesRecordPoints)) . '</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Soulier d\'Or</td><td>' . (sizeof($tropheesSoulierOr) == 0 ? '-' : ($nombreSoulierOr . ' : ' . $journeesSoulierOr)) . '</td>';
							echo '<td>Record de points buteur</td><td>' . (sizeof($tropheesRecordPointsButeur) == 0 ? '-' : ($nombreRecordPointsButeur . ' : ' . $journeesRecordPointsButeur)) . '</td>';
						echo '</tr>';
					echo '</tbody>';
				echo '</table>';
			echo '</div>';
		}
	}

	// Statistiques par championnat
	function afficherStatistiques($bdd, $pronostiqueurConsulte) {
		// Lecture des statistiques du pronostiqueur pour ses championnats
		$ordreSQL =		'	SELECT		Championnat, Championnats_Nom' .
						'	FROM		inscriptions' .
						'	JOIN		championnats' .
						'				ON		inscriptions.Championnats_Championnat = championnats.Championnat' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'				AND		Championnats_Championnat NOT IN (4, 5)' .
						'	ORDER BY	Championnats_Championnat';
		$req = $bdd->query($ordreSQL);
		$championnats = $req->fetchAll();
		
		// Légende
		echo '<label><b>Légende</b></label><br />';
		echo '<table class="cc--pronostiqueurs-detail--legende">';
			echo '<tbody>';
				echo '<tr class="aligne-gauche">';
					echo '<td class="titre">10 points</td><td>Score exact</td>';
					echo '<td class="titre">3 points</td><td>Seulement le nombre de buts du vainqueur</td>';
				echo '</tr>';
				echo '<tr class="aligne-gauche">';
					echo '<td class="titre">8 points</td><td>Vainqueur et nombre de buts du vainqueur</td>';
					echo '<td class="titre">2 points</td><td>Nombre de buts d\'une équipe d\'un match nul</td>';
				echo '</tr>';
				echo '<tr class="aligne-gauche">';
					echo '<td class="titre">7 points</td><td>Vainqueur et bon écart ou Match nul sans le score exact</td>';
					echo '<td class="titre">1 point</td><td>Seulement le nombre de buts du perdant</td>';
				echo '</tr>';
				echo '<tr class="aligne-gauche">';
					echo '<td class="titre">6 points</td><td>Vainqueur et nombre de buts du perdant</td>';
					echo '<td class="titre">0 point</td><td>Tout faux</td>';
				echo '</tr>';
				echo '<tr class="aligne-gauche">';
					echo '<td class="titre">5 points</td><td>Seulement le vainqueur</td>';
					echo '<td class="titre">Oublis</td><td>Nombre de pronostics non effectués</td>';
				echo '</tr>';
			echo '</tbody>';
		echo '</table>';
		echo '<br />';

		// Parcours des championnats du pronostiqueur
		foreach($championnats as $unChampionnat) {
			echo '<div style="margin-bottom: 10px;">';
				// Nombre de buteurs pronostiqués
				$ordreSQL =		'	SELECT		COUNT(*) AS Nombre_Pronostics_Buteur' .
								'	FROM		pronostics_buteurs' .
								'	JOIN		matches' .
								'				ON		pronostics_buteurs.Matches_Match = matches.Match' .
								'	JOIN		journees' .
								'				ON		matches.Journees_Journee = journees.Journee' .
								'	JOIN		matches_participants' .
								'				ON		matches.Match = matches_participants.Matches_Match' .
								'						AND		pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
								'	WHERE		journees.Championnats_Championnat = ' . $unChampionnat["Championnat"] .
								'				AND		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte;
				$req = $bdd->query($ordreSQL);
				$pronosticsButeur = $req->fetchAll();
				$nombrePronosticsButeur = $pronosticsButeur[0]["Nombre_Pronostics_Buteur"];
				
				// Nombre de buteurs trouvés
				$ordreSQL =		'	SELECT		COUNT(*) AS Nombre_Buteurs_Trouves' .
								'	FROM		(' .
								'					SELECT		pronostics_buteurs.Matches_Match' .
								'								,pronostics_buteurs.Joueurs_Joueur' .
								'								,pronostics_buteurs.Equipes_Equipe' .
								'								,CASE' .
								'									WHEN	@match = pronostics_buteurs.Matches_Match' .
								'											AND		@joueur = pronostics_buteurs.Joueurs_Joueur' .
								'											AND		@equipe = pronostics_buteurs.Equipes_Equipe' .
								'									THEN	@indicePronostics := @indicePronostics + 1' .
								'									ELSE	(@indicePronostics := 1) AND (@match := pronostics_buteurs.Matches_Match) AND (@joueur := pronostics_buteurs.Joueurs_Joueur) AND (@equipe := pronostics_buteurs.Equipes_Equipe)' .
								'								END AS Pronostics_Indice' .
								'					FROM		pronostics_buteurs' .
								'					JOIN		matches' .
								'								ON		pronostics_buteurs.Matches_Match = matches.Match' .
								'					JOIN		(	SELECT		@indicePronostics := 0, @match := NULL, @joueur := NULL, @equipe := NULL	) r' .
								'					JOIN		matches_participants' .
								'								ON		matches.Match = matches_participants.Matches_Match' .
								'										AND		pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
								'					WHERE		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
								'					ORDER BY	pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
								'				) pronostics_buteurs' .
								'	JOIN		(' .
								'					SELECT		Matches_Match' .
								'								,Joueurs_Joueur' .
								'								,Equipes_Equipe' .
								'								,CASE' .
								'									WHEN	@match = Matches_Match' .
								'											AND		@joueur = Joueurs_Joueur' .
								'											AND		@equipe = Equipes_Equipe' .
								'									THEN	@indiceMatches := @indiceMatches + 1' .
								'									ELSE	(@indiceMatches := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
								'								END AS Matches_Indice' .
								'					FROM		matches_buteurs' .
								'					JOIN		matches' .
								'								ON		matches_buteurs.Matches_Match = matches.Match' .
								'					JOIN		(	SELECT		@indiceMatches := 0, @joueur := NULL, @equipe := NULL	) r' .
								'					WHERE		matches_buteurs.Buteurs_CSC = 0' .
								'					ORDER BY	Matches_Match, Joueurs_Joueur, Equipes_Equipe' .
								'				) matches_buteurs' .
								'				ON		pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
								'						AND		pronostics_buteurs.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
								'						AND		pronostics_buteurs.Equipes_Equipe = matches_buteurs.Equipes_Equipe' .
								'						AND		pronostics_buteurs.Pronostics_Indice = matches_buteurs.Matches_Indice' .
								'	JOIN		matches' .
								'				ON		pronostics_buteurs.Matches_Match = matches.Match' .
								'	JOIN		journees' .
								'				ON		matches.Journees_Journee = journees.Journee' .
								'	WHERE		journees.Championnats_Championnat = ' . $unChampionnat["Championnat"];
				$req = $bdd->query($ordreSQL);
				$buteursTrouves = $req->fetchAll();
				$nombreButeursTrouves = $buteursTrouves[0]["Nombre_Buteurs_Trouves"];
				
				// Répartition des points marqués pour un match
				$ordreSQL =		'	SELECT		fn_calculpointsmatch	(	IFNULL(matches.Matches_ScoreAPEquipeDomicile, matches.Matches_ScoreEquipeDomicile),' .
								'											IFNULL(matches.Matches_ScoreAPEquipeVisiteur, matches.Matches_ScoreEquipeVisiteur),' .
								'											IFNULL(pronostics.Pronostics_ScoreAPEquipeDomicile, pronostics.Pronostics_ScoreEquipeDomicile),' .
								'											IFNULL(pronostics.Pronostics_ScoreAPEquipeVisiteur, pronostics.Pronostics_ScoreEquipeVisiteur)' .
								'										) AS ScoreMatch' .
								'				,COUNT(*) AS Nombre' .
								'	FROM		pronostics' .
								'	JOIN		matches' .
								'				ON		pronostics.Matches_Match = matches.Match' .
								'	JOIN		journees' .
								'				ON		matches.Journees_Journee = journees.Journee' .
								'	WHERE		journees.Championnats_Championnat = ' . $unChampionnat["Championnat"] .
								'				AND		pronostics.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
								'				AND		pronostics.Pronostics_ScoreEquipeDomicile IS NOT NULL' .
								'				AND		pronostics.Pronostics_ScoreEquipeVisiteur IS NOT NULL' .
								'				AND		matches.Matches_ScoreEquipeDomicile IS NOT NULL' .
								'				AND		matches.Matches_ScoreEquipeVisiteur IS NOT NULL' .
								'	GROUP BY	fn_calculpointsmatch	(	IFNULL(matches.Matches_ScoreAPEquipeDomicile, matches.Matches_ScoreEquipeDomicile),' .
								'											IFNULL(matches.Matches_ScoreAPEquipeVisiteur, matches.Matches_ScoreEquipeVisiteur),' .
								'											IFNULL(pronostics.Pronostics_ScoreAPEquipeDomicile, pronostics.Pronostics_ScoreEquipeDomicile),' .
								'											IFNULL(pronostics.Pronostics_ScoreAPEquipeVisiteur, pronostics.Pronostics_ScoreEquipeVisiteur)' .
								'										)' .
								'	ORDER BY	ScoreMatch DESC';

				$req = $bdd->query($ordreSQL);
				
				
				$points10 = $points8 = $points7 = $points6 = $points5 = $points3 = $points2 = $points1 = $points0 = 0;
				if($req != null) {
					$scoresMarques = $req->fetchAll();
					foreach($scoresMarques as $unScoreMarque) {
						switch($unScoreMarque["ScoreMatch"]) {
							case 10: $points10 = $unScoreMarque["Nombre"]; break;
							case 8: $points8 = $unScoreMarque["Nombre"]; break;
							case 7: $points7 = $unScoreMarque["Nombre"]; break;
							case 6: $points6 = $unScoreMarque["Nombre"]; break;
							case 5: $points5 = $unScoreMarque["Nombre"]; break;
							case 3: $points3 = $unScoreMarque["Nombre"]; break;
							case 2: $points2 = $unScoreMarque["Nombre"]; break;
							case 1: $points1 = $unScoreMarque["Nombre"]; break;
							case 0: $points0 = $unScoreMarque["Nombre"]; break;
						}
					}
				}
				
				// Nombre de pronostics oubliés
				$ordreSQL =		'	SELECT		COUNT(*) AS Nombre_Oublis' .
								'	FROM		matches' .
								'	JOIN		journees' .
								'				ON		matches.Journees_Journee = journees.Journee' .
								'	JOIN		pronostiqueurs' .
								'				ON		matches.Matches_Date >= pronostiqueurs.Pronostiqueurs_DateDebutPresence' .
								'						AND		(pronostiqueurs.Pronostiqueurs_DateFinPresence IS NULL OR matches.Matches_Date <= pronostiqueurs.Pronostiqueurs_DateFinPresence)' .
								'	LEFT JOIN	pronostics' .
								'				ON		matches.Match = pronostics.Matches_Match' .
								'						AND		pronostiqueurs.Pronostiqueur = pronostics.Pronostiqueurs_Pronostiqueur' .
								'	WHERE		journees.Championnats_Championnat = ' . $unChampionnat["Championnat"] .
								'				AND		matches.Matches_ScoreEquipeDomicile IS NOT NULL' .
								'				AND		matches.Matches_ScoreEquipeVisiteur IS NOT NULL' .
								'				AND		pronostics.Pronostics_ScoreEquipeDomicile IS NULL' .
								'				AND		pronostics.Pronostics_ScoreEquipeVisiteur IS NULL' .
								'				AND		pronostiqueurs.Pronostiqueur = ' . $pronostiqueurConsulte;
								
								
				$req = $bdd->query($ordreSQL);
				$pronosticsOublies = $req->fetchAll();
				$nombreOublis = $pronosticsOublies[0]["Nombre_Oublis"];
				
				echo '<label><b>' . $unChampionnat["Championnats_Nom"] . '</b></label><br />';
				echo '<table class="cc--tableau">';
					echo '<thead>';
						echo '<tr>';
							echo '<th title="Joueurs présents sur la feuille de match">Buteurs trouvés / Buteurs pronostiqués</th>';
							echo '<th title="Score exact">Score exact</th>';
							echo '<th title="Vainqueur et nombre de buts du vainqueur">8 points</th>';
							echo '<th title="Vainqueur et bon écart ou Match nul sans le score exact">7 points</th>';
							echo '<th title="Vainqueur et nombre de buts du perdant">6 points</th>';
							echo '<th title="Seulement le vainqueur">5 points</th>';
							echo '<th title="Seulement le nombre de buts du vainqueur">3 points</th>';
							echo '<th title="Nombre de buts d\'une équipe d\'un match nul">2 points</th>';
							echo '<th title="Seulement le nombre de buts du perdant">1 point</th>';
							echo '<th>Tout faux</th>';
							echo '<th title="Nombre de pronostics oubliés">Oublis</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
						echo '<tr>';
							if($nombrePronosticsButeur)
								echo '<td title="Joueurs présents sur la feuille de match">' . $nombreButeursTrouves . ' / ' . $nombrePronosticsButeur . ' (' . number_format(($nombreButeursTrouves / $nombrePronosticsButeur * 100), 2) . '%)</td>';
							else
								echo '<td title="Joueurs présents sur la feuille de match">0</td>';
							echo '<td title="Score exact">' . $points10 . '</td>';
							echo '<td title="Vainqueur et nombre de buts du vainqueur">' . $points8 . '</td>';
							echo '<td title="Vainqueur et bon écart ou Match nul sans le score exact">' . $points7 . '</td>';
							echo '<td title="Vainqueur et nombre de buts du perdant">' . $points6 . '</td>';
							echo '<td title="Seulement le vainqueur">' . $points5 . '</td>';
							echo '<td title="Seulement le nombre de buts du vainqueur">' . $points3 . '</td>';
							echo '<td title="Nombre de buts d\'une équipe d\'un match nul">' . $points2 . '</td>';
							echo '<td title="Seulement le nombre de buts du perdant">' . $points1 . '</td>';
							echo '<td>' . $points0 . '</td>';
							echo '<td>' . $nombreOublis . '</td>';
						echo '</tr>';
					echo '</tbody>';
				echo '</table>';

			echo '</div>';
		}
	}
	
	// Evolution du classement par championnat
	function afficherEvolutionClassement($bdd, $pronostiqueurConsulte, $zoneDessinLargeur, $zoneDessinHauteur, &$imagesClassement) {
		// Lecture de l'évolution du classement du pronostiqueur pour ses championnats
		$ordreSQL =		'	SELECT		Championnat, Championnats_Nom' .
						'	FROM		inscriptions' .
						'	JOIN		championnats' .
						'				ON		inscriptions.Championnats_Championnat = championnats.Championnat' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'				AND		Championnats_Championnat NOT IN (4, 5)' .
						'	ORDER BY	Championnats_Championnat';
		$req = $bdd->query($ordreSQL);
		$championnats = $req->fetchAll();
		
		// Parcours des championnats du pronostiqueur
		foreach($championnats as $unChampionnat) {
			// Nombre de pronostiqueurs (et qui donne l'échelle verticale)
			$ordreSQL =		'	SELECT		COUNT(*) AS Nombre_Pronostiqueurs FROM inscriptions WHERE Championnats_Championnat = ' . $unChampionnat["Championnat"];
			$req = $bdd->query($ordreSQL);
			$pronostiqueurs = $req->fetchAll();
			$nombrePronostiqueurs = $pronostiqueurs[0]["Nombre_Pronostiqueurs"];

			// Meilleur et plus mauvais classements
			$ordreSQL =		'	SELECT		MAX(Classements_ClassementGeneralMatch) AS Classement_Max' .
							'				,MIN(Classements_ClassementGeneralMatch) AS Classement_Min' .
							'	FROM		vue_classements_uniques' .
							'	JOIN		journees' .
							'				ON		vue_classements_uniques.Journees_Journee = journees.Journee' .
							'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
							'				AND		journees.Championnats_Championnat = ' . $unChampionnat["Championnat"] .
							'				AND		vue_classements_uniques.Classements_ClassementGeneralMatch IS NOT NULL';
			$req = $bdd->query($ordreSQL);
			$classementsMinEtMax = $req->fetchAll();
			$classementMin = $classementsMinEtMax[0]["Classement_Min"];
			$classementMax = $classementsMinEtMax[0]["Classement_Max"];

			// Classements occupés
			$ordreSQL =		'	SELECT		Classements_ClassementGeneralMatch AS Valeur, vue_classements_uniques.Journees_Journee' .
							'	FROM		vue_classements_uniques' .
							'	JOIN		journees' .
							'				ON		vue_classements_uniques.Journees_Journee = journees.Journee' .
							'	WHERE		journees.Championnats_Championnat = ' . $unChampionnat["Championnat"] .
							'				AND		vue_classements_uniques.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
							'				AND		vue_classements_uniques.Classements_ClassementGeneralMatch IS NOT NULL' .
							'	ORDER BY	vue_classements_uniques.Journees_Journee';

			$req = $bdd->query($ordreSQL);
			$classements = $req->fetchAll();
			$nombrePoints = sizeof($classements);

			if($nombrePoints == 0)
				echo '<label><b>' . $unChampionnat["Championnats_Nom"] . '</b> (' . $nombrePronostiqueurs . ' joueurs) - Aucun match n\'a encore débuté pour ce championnat</label><br />';
			else {
				if($classementMin == 1)					$classementMinAffiche = 'premier';
				else									$classementMinAffiche = $classementMin . '<sup>ème</sup>';
				
				if($classementMax == $nombrePronostiqueurs)					$classementMaxAffiche = 'dernier';
				else														$classementMaxAffiche = $classementMax . '<sup>ème</sup>';
				
				echo '<label><b>' . $unChampionnat["Championnats_Nom"] . '</b> (' . $nombrePronostiqueurs . ' joueurs) - Meilleur classement : ' . $classementMinAffiche . ', performance la moins élevée : ' . $classementMaxAffiche . '</label><br />';
				
				$dossierImages = '../images/evolution/';
				$dossierImagesHTML = 'images/evolution/';
				
				// Effacement d'images qui pourraient exister dans ce dossier pour ce pronostiqueur
				foreach(glob($dossierImagesHTML . $unChampionnat["Championnat"] . '_' . $pronostiqueurConsulte . '_*.png') as $f) {
					unlink($f);
				}
				
				include('concours_centre_affichage_pronostiqueurs_classement.php');
				echo '<img src="' . $nomFichierHTML . '" alt="" />';
			}
		}
	}


	// Classement par journée et par championnat
	function afficherClassementJournee($bdd, $pronostiqueurConsulte, $zoneDessinLargeur, $zoneDessinHauteur, &$imagesClassement) {
		// Lecture du classement par journée du pronostiqueur pour ses championnats
		$ordreSQL =		'	SELECT		Championnat, Championnats_Nom' .
						'	FROM		inscriptions' .
						'	JOIN		championnats' .
						'				ON		inscriptions.Championnats_Championnat = championnats.Championnat' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'				AND		Championnats_Championnat NOT IN (4, 5)' .
						'	ORDER BY	Championnats_Championnat';
		$req = $bdd->query($ordreSQL);
		$championnats = $req->fetchAll();
	
		// Parcours des championnats du pronostiqueur
		foreach($championnats as $unChampionnat) {
			echo '<div style="margin-bottom: 10px;">';
				// Nombre de pronostiqueurs (et qui donne l'échelle verticale)
				$ordreSQL =		'	SELECT		COUNT(*) AS Nombre_Pronostiqueurs FROM inscriptions WHERE Championnats_Championnat = ' . $unChampionnat["Championnat"];
				$req = $bdd->query($ordreSQL);
				$pronostiqueurs = $req->fetchAll();
				$nombrePronostiqueurs = $pronostiqueurs[0]["Nombre_Pronostiqueurs"];
			
				// Meilleur et plus mauvais classements
				$ordreSQL =		'	SELECT		MAX(Classements_ClassementJourneeMatch) AS Classement_Max' .
								'				,MIN(Classements_ClassementJourneeMatch) AS Classement_Min' .
								'	FROM		vue_classements_uniques' .
								'	JOIN		journees' .
								'				ON		vue_classements_uniques.Journees_Journee = journees.Journee' .
								'	WHERE		vue_classements_uniques.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
								'				AND		journees.Championnats_Championnat = ' . $unChampionnat["Championnat"] .
								'				AND		vue_classements_uniques.Classements_ClassementJourneeMatch IS NOT NULL';
				$req = $bdd->query($ordreSQL);
				$classementsMinEtMax = $req->fetchAll();
				$classementMin = $classementsMinEtMax[0]["Classement_Min"];
				$classementMax = $classementsMinEtMax[0]["Classement_Max"];

				// Classements occupés
				$ordreSQL =		'	SELECT		Classements_ClassementJourneeMatch AS Valeur, vue_classements_uniques.Journees_Journee' .
								'	FROM		vue_classements_uniques' .
								'	JOIN		journees' .
								'				ON		vue_classements_uniques.Journees_Journee = journees.Journee' .
								'	WHERE		journees.Championnats_Championnat = ' . $unChampionnat["Championnat"] .
								'				AND		vue_classements_uniques.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
								'				AND		vue_classements_uniques.Classements_ClassementJourneeMatch IS NOT NULL' .
								'	ORDER BY	vue_classements_uniques.Journees_Journee';
								
				$req = $bdd->query($ordreSQL);
				$classements = $req->fetchAll();
				$nombrePoints = sizeof($classements);
				
				if($nombrePoints == 0) {
					echo '<label><b>' . $unChampionnat["Championnats_Nom"] . '</b> (' . $nombrePronostiqueurs . ' joueurs) - Aucun match n\'a encore débuté pour ce championnat</label><br />';
				} else {
					if($classementMin == 1)					$classementMinAffiche = 'premier';
					else									$classementMinAffiche = $classementMin . '<sup>ème</sup>';
					
					if($classementMax == $nombrePronostiqueurs)					$classementMaxAffiche = 'dernier';
					else														$classementMaxAffiche = $classementMax . '<sup>ème</sup>';
					
					echo '<label><b>' . $unChampionnat["Championnats_Nom"] . '</b> (' . $nombrePronostiqueurs . ' joueurs) - Meilleur classement : ' . $classementMinAffiche . ', performance la moins élevée : ' . $classementMaxAffiche . '</label><br />';
					
					$dossierImages = '../images/journee/';
					$dossierImagesHTML = 'images/journee/';

					// Effacement d'images qui pourraient exister dans ce dossier pour ce pronostiqueur
					foreach(glob($dossierImagesHTML . $unChampionnat["Championnat"] . '_' . $pronostiqueurConsulte . '_*.png') as $f) {
						unlink($f);
					}					
					
					include('concours_centre_affichage_pronostiqueurs_classement.php');
					echo '<img src="' . $nomFichierHTML . '" alt="" />';
				}
			
			echo '</div>';
		}
	}
	
	// Le sous-onglet permet de savoir quelle section on doit afficher
	// - 1 : fiche d'identité du pronostiqueur
	// - 2 : palmarès de l'année (trophées gagnés)
	// - 3 : évolution du classement
	switch($sousOnglet) {
		case 1:	afficherFicheIdentite($bdd, $pronostiqueurConsulte);
		break;
		case 2:	afficherPalmares($bdd, $pronostiqueurConsulte);
		break;
		case 3: afficherStatistiques($bdd, $pronostiqueurConsulte);
		break;
		case 4:
			$imagesClassement = array();
			afficherEvolutionClassement($bdd, $pronostiqueurConsulte, $zoneDessinLargeur, $zoneDessinHauteur, $imagesClassement);
		break;
		case 5:
			$imagesClassement = array();
			afficherClassementJournee($bdd, $pronostiqueurConsulte, $zoneDessinLargeur, $zoneDessinHauteur, $imagesClassement);
		break;
	}
	
?>

