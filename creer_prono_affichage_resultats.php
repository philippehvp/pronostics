<?php
	include('commun.php');
	
	// Page d'affichage des derniers résultats des équipes d'une rencontre
	
	// Match concerné
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	
	$ordreSQL =		'	SELECT		Equipes_EquipeDomicile, Equipes_EquipeVisiteur, equipesDomicile.Equipes_Nom AS EquipesDomicile_Nom, equipesVisiteur.Equipes_Nom AS EquipesVisiteur_Nom' .
					'	FROM		matches' .
					'	JOIN		equipes equipesDomicile' .
					'				ON		matches.Equipes_EquipeDomicile = equipesDomicile.Equipe' .
					'	JOIN		equipes equipesVisiteur' .
					'				ON		matches.Equipes_EquipeVisiteur = equipesVisiteur.Equipe' .
					'	WHERE		matches.Match = ' . $match;
	$req = $bdd->query($ordreSQL);
	$equipes = $req->fetchAll();
	$equipeDomicile = $equipes[0]["Equipes_EquipeDomicile"];
	$equipeVisiteur = $equipes[0]["Equipes_EquipeVisiteur"];
	$equipeDomicile_Nom = $equipes[0]["EquipesDomicile_Nom"];
	$equipeVisiteur_Nom = $equipes[0]["EquipesVisiteur_Nom"];
	
	// Lecture des classements de l'équipe domicile
	$ordreSQL =		'	SELECT		(' .
					'					SELECT		ClassementsEquipes_Classement' .
					'					FROM		classements_equipes' .
					'					WHERE		Equipes_Equipe = ' . $equipeDomicile .
					'				) AS Classement_General' .
					'				,(' .
					'					SELECT		ClassementsEquipes_Classement' .
					'					FROM		classements_equipes_domicile' .
					'					WHERE		Equipes_Equipe = ' . $equipeDomicile .
					'				) AS Classement_Domicile' .
					'				,(' .
					'					SELECT		ClassementsEquipes_Classement' .
					'					FROM		classements_equipes_attaque' .
					'					WHERE		Equipes_Equipe = ' . $equipeDomicile .
					'				) AS Classement_Attaque' .
					'				,(' .
					'					SELECT		ClassementsEquipes_Classement' .
					'					FROM		classements_equipes_defense' .
					'					WHERE		Equipes_Equipe = ' . $equipeDomicile .
					'				) AS Classement_Defense' .
					'				,(' .
					'					SELECT		ClassementsEquipes_Classement' .
					'					FROM		classements_equipes_attaque_domicile' .
					'					WHERE		Equipes_Equipe = ' . $equipeDomicile .
					'				) AS Classement_Attaque_Domicile' .
					'				,(' .
					'					SELECT		ClassementsEquipes_Classement' .
					'					FROM		classements_equipes_defense_domicile' .
					'					WHERE		Equipes_Equipe = ' . $equipeDomicile .
					'				) AS Classement_Defense_Domicile' .
					'				,(' .
					'					SELECT		ClassementsEquipes_BP' .
					'					FROM		classements_equipes_attaque_domicile' .
					'					WHERE		Equipes_Equipe = ' . $equipeDomicile .
					'				) AS Classement_Attaque_Domicile_Buts' .
					'				,(' .
					'					SELECT		ClassementsEquipes_BC' .
					'					FROM		classements_equipes_defense_domicile' .
					'					WHERE		Equipes_Equipe = ' . $equipeDomicile .
					'				) AS Classement_Defense_Domicile_Buts';
	$req = $bdd->query($ordreSQL);
	$equipeDomicile_classements = $req->fetchAll();

	// Lecture des classements de l'équipe visiteur
	$ordreSQL =		'	SELECT		(' .
					'					SELECT		ClassementsEquipes_Classement' .
					'					FROM		classements_equipes' .
					'					WHERE		Equipes_Equipe = ' . $equipeVisiteur .
					'				) AS Classement_General' .
					'				,(' .
					'					SELECT		ClassementsEquipes_Classement' .
					'					FROM		classements_equipes_visiteur' .
					'					WHERE		Equipes_Equipe = ' . $equipeVisiteur .
					'				) AS Classement_Visiteur' .
					'				,(' .
					'					SELECT		ClassementsEquipes_Classement' .
					'					FROM		classements_equipes_attaque' .
					'					WHERE		Equipes_Equipe = ' . $equipeVisiteur .
					'				) AS Classement_Attaque' .
					'				,(' .
					'					SELECT		ClassementsEquipes_Classement' .
					'					FROM		classements_equipes_defense' .
					'					WHERE		Equipes_Equipe = ' . $equipeVisiteur .
					'				) AS Classement_Defense' .
					'				,(' .
					'					SELECT		ClassementsEquipes_Classement' .
					'					FROM		classements_equipes_attaque_visiteur' .
					'					WHERE		Equipes_Equipe = ' . $equipeVisiteur .
					'				) AS Classement_Attaque_Visiteur' .
					'				,(' .
					'					SELECT		ClassementsEquipes_Classement' .
					'					FROM		classements_equipes_defense_visiteur' .
					'					WHERE		Equipes_Equipe = ' . $equipeVisiteur .
					'				) AS Classement_Defense_Visiteur' .
					'				,(' .
					'					SELECT		ClassementsEquipes_BP' .
					'					FROM		classements_equipes_attaque_visiteur' .
					'					WHERE		Equipes_Equipe = ' . $equipeVisiteur .
					'				) AS Classement_Attaque_Visiteur_Buts' .
					'				,(' .
					'					SELECT		ClassementsEquipes_BC' .
					'					FROM		classements_equipes_defense_visiteur' .
					'					WHERE		Equipes_Equipe = ' . $equipeVisiteur .
					'				) AS Classement_Defense_Visiteur_Buts';
	$req = $bdd->query($ordreSQL);
	$equipeVisiteur_classements = $req->fetchAll();

	// Nombre de matches de l'équipe domicile
	$ordreSQL =		'	SELECT		fn_nombrevictoires(' . $equipeDomicile . ') AS Nombre_Victoires' .
					'				,fn_nombrenuls(' . $equipeDomicile . ') AS Nombre_Nuls' .
					'				,fn_nombredefaites(' . $equipeDomicile . ') AS Nombre_Defaites';
	$req = $bdd->query($ordreSQL);
	$equipeDomicile_general = $req->fetchAll();
					
	$ordreSQL =		'	SELECT		fn_nombrevictoires_domicile(' . $equipeDomicile . ') AS Nombre_Victoires' .
					'				,fn_nombrenuls_domicile(' . $equipeDomicile . ') AS Nombre_Nuls' .
					'				,fn_nombredefaites_domicile(' . $equipeDomicile . ') AS Nombre_Defaites';
	$req = $bdd->query($ordreSQL);
	$equipeDomicile_domicile = $req->fetchAll();
	
	// Nombre de matches de l'équipe visiteur
	$ordreSQL =		'	SELECT		fn_nombrevictoires(' . $equipeVisiteur . ') AS Nombre_Victoires' .
					'				,fn_nombrenuls(' . $equipeVisiteur . ') AS Nombre_Nuls' .
					'				,fn_nombredefaites(' . $equipeVisiteur . ') AS Nombre_Defaites';
	$req = $bdd->query($ordreSQL);
	$equipeVisiteur_general = $req->fetchAll();
					
	$ordreSQL =		'	SELECT		fn_nombrevictoires_visiteur(' . $equipeVisiteur . ') AS Nombre_Victoires' .
					'				,fn_nombrenuls_visiteur(' . $equipeVisiteur . ') AS Nombre_Nuls' .
					'				,fn_nombredefaites_visiteur(' . $equipeVisiteur . ') AS Nombre_Defaites';
	$req = $bdd->query($ordreSQL);
	$equipeVisiteur_visiteur = $req->fetchAll();
	
	// Meilleurs buteurs de l'équipe domicile
	$ordreSQL =		'	SELECT		GROUP_CONCAT(buteurs.Buteurs SEPARATOR \', \') AS Buteurs' .
					'	FROM		(' .
					'					SELECT		CONCAT(joueurs.Joueurs_NomFamille, \' (\', COUNT(*), \')\') AS Buteurs' .
					'					FROM		matches_buteurs' .
					'					JOIN		(' .
					'									SELECT		Joueurs_Joueur' .
					'									FROM		joueurs_equipes' .
					'									WHERE		Equipes_Equipe = ' . $equipeDomicile .
					'												AND		JoueursEquipes_Fin IS NULL' .
					'								) effectif' .
					'								ON		matches_buteurs.Joueurs_Joueur = effectif.Joueurs_Joueur' .
					'					JOIN		joueurs' .
					'								ON		matches_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'					WHERE		matches_buteurs.Buteurs_CSC = 0' .
					'					GROUP BY	matches_buteurs.Joueurs_Joueur' .
					'					ORDER BY	COUNT(*) DESC' .
					'					LIMIT		3' .
					'				) buteurs';
	$req = $bdd->query($ordreSQL);
	$equipeDomicile_meilleurs_buteurs = $req->fetchAll();
	
	// Tous les buteurs de l'équipe domicile
	$ordreSQL =		'	SELECT		GROUP_CONCAT(buteurs.Buteurs SEPARATOR \', \') AS Buteurs' .
					'	FROM		(' .
					'					SELECT		CONCAT(joueurs.Joueurs_NomFamille, \' (\', COUNT(*), \')\') AS Buteurs' .
					'					FROM		matches_buteurs' .
					'					JOIN		(' .
					'									SELECT		Joueurs_Joueur' .
					'									FROM		joueurs_equipes' .
					'									WHERE		Equipes_Equipe = ' . $equipeDomicile .
					'												AND		JoueursEquipes_Fin IS NULL' .
					'								) effectif' .
					'								ON		matches_buteurs.Joueurs_Joueur = effectif.Joueurs_Joueur' .
					'					JOIN		joueurs' .
					'								ON		matches_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'					WHERE		matches_buteurs.Buteurs_CSC = 0' .
					'					GROUP BY	matches_buteurs.Joueurs_Joueur' .
					'					ORDER BY	COUNT(*) DESC' .
					'				) buteurs';
	$req = $bdd->query($ordreSQL);
	$equipeDomicile_buteurs = $req->fetchAll();
	
	// Meilleurs buteurs de l'équipe visiteur
	$ordreSQL =		'	SELECT		GROUP_CONCAT(buteurs.Buteurs SEPARATOR \', \') AS Buteurs' .
					'	FROM		(' .
					'					SELECT		CONCAT(joueurs.Joueurs_NomFamille, \' (\', COUNT(*), \')\') AS Buteurs' .
					'					FROM		matches_buteurs' .
					'					JOIN		(' .
					'									SELECT		Joueurs_Joueur' .
					'									FROM		joueurs_equipes' .
					'									WHERE		Equipes_Equipe = ' . $equipeVisiteur .
					'												AND		JoueursEquipes_Fin IS NULL' .
					'								) effectif' .
					'								ON		matches_buteurs.Joueurs_Joueur = effectif.Joueurs_Joueur' .
					'					JOIN		joueurs' .
					'								ON		matches_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'					WHERE		matches_buteurs.Buteurs_CSC = 0' .
					'					GROUP BY	matches_buteurs.Joueurs_Joueur' .
					'					ORDER BY	COUNT(*) DESC' .
					'					LIMIT		3' .
					'				) buteurs';
	$req = $bdd->query($ordreSQL);
	$equipeVisiteur_meilleurs_buteurs = $req->fetchAll();
	
	// Tous les buteurs de l'équipe visiteur
	$ordreSQL =		'	SELECT		GROUP_CONCAT(buteurs.Buteurs SEPARATOR \', \') AS Buteurs' .
					'	FROM		(' .
					'					SELECT		CONCAT(joueurs.Joueurs_NomFamille, \' (\', COUNT(*), \')\') AS Buteurs' .
					'					FROM		matches_buteurs' .
					'					JOIN		(' .
					'									SELECT		Joueurs_Joueur' .
					'									FROM		joueurs_equipes' .
					'									WHERE		Equipes_Equipe = ' . $equipeVisiteur .
					'												AND		JoueursEquipes_Fin IS NULL' .
					'								) effectif' .
					'								ON		matches_buteurs.Joueurs_Joueur = effectif.Joueurs_Joueur' .
					'					JOIN		joueurs' .
					'								ON		matches_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'					WHERE		matches_buteurs.Buteurs_CSC = 0' .
					'					GROUP BY	matches_buteurs.Joueurs_Joueur' .
					'					ORDER BY	COUNT(*) DESC' .
					'				) buteurs';
	$req = $bdd->query($ordreSQL);
	$equipeVisiteur_buteurs = $req->fetchAll();	
	
	// Liste des derniers matches de l'équipe domicile
	$ordreSQL =		'	SELECT		matches.Match' .
					'				,CASE' .
					'					WHEN	equipesDomicile.Equipe = ' . $equipeDomicile .
					'					THEN	\'DOM\'' .
					'					ELSE	\'EXT\'' .
					'				END AS Localisation' .
					'				,CASE' .
					'					WHEN	equipesDomicile.Equipe = ' . $equipeDomicile .
					'					THEN	equipesVisiteur.Equipes_Nom' .
					'					ELSE	equipesDomicile.Equipes_Nom' .
					'				END AS Equipe' .
					'				,CASE' .
					'					WHEN	equipesDomicile.Equipe = ' . $equipeDomicile .
					'					THEN	CASE' .
					'								WHEN	Matches_ScoreEquipeDomicile > Matches_ScoreEquipeVisiteur' .
					'								THEN	\'V\'' .
					'								WHEN	Matches_ScoreEquipeDomicile = Matches_ScoreEquipeVisiteur' .
					'								THEN	\'N\'' .
					'								WHEN	Matches_ScoreEquipeDomicile < Matches_ScoreEquipeVisiteur' .
					'								THEN	\'D\'' .
					'							END' .
					'					ELSE	CASE' .
					'								WHEN	Matches_ScoreEquipeDomicile < Matches_ScoreEquipeVisiteur' .
					'								THEN	\'V\'' .
					'								WHEN	Matches_ScoreEquipeDomicile = Matches_ScoreEquipeVisiteur' .
					'								THEN	\'N\'' .
					'								WHEN	Matches_ScoreEquipeDomicile > Matches_ScoreEquipeVisiteur' .
					'								THEN	\'D\'' .
					'							END' .
					'				END AS Resultat' .
					'				,CONCAT(Matches_ScoreEquipeDomicile, \'-\', Matches_ScoreEquipeVisiteur) AS Score' .
					'				,(' .
					'					SELECT		GROUP_CONCAT(' .
					'									CASE' .
					'										WHEN	Buteurs_CSC = 1' .
					'										THEN	CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille), \' (CSC)\')' .
					'										ELSE	IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille)' .
					'									END SEPARATOR \', \')' .
					'					FROM		matches_buteurs' .
					'					JOIN		joueurs' .
					'								ON		matches_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'					WHERE		matches_buteurs.Matches_Match = matches.Match' .
					'				) AS Buteurs' .
					'	FROM		matches' .
					'	JOIN		journees' .
					'				ON		Journees_Journee = Journee' .
					'	JOIN		equipes equipesDomicile' .
					'				ON		matches.Equipes_EquipeDomicile = equipesDomicile.Equipe' .
					'	JOIN		equipes equipesVisiteur' .
					'				ON		matches.Equipes_EquipeVisiteur = equipesVisiteur.Equipe' .
					'	WHERE		Championnats_Championnat = 1' .
					'				AND' .
					'				(' .
					'					equipesDomicile.Equipe = ' . $equipeDomicile .
					'					OR		equipesVisiteur.Equipe = ' . $equipeDomicile .
					'				)' .
					'				AND		matches.Match <> ' . $match .
					'	ORDER BY	matches.Matches_Date DESC' .
					'	LIMIT		10';
	$req = $bdd->query($ordreSQL);
	$equipeDomicile_resultats = $req->fetchAll();

	// Liste des derniers matches de l'équipe visiteur
	$ordreSQL =		'	SELECT		matches.Match' .
					'				,CASE' .
					'					WHEN	equipesDomicile.Equipe = ' . $equipeVisiteur .
					'					THEN	\'DOM\'' .
					'					ELSE	\'EXT\'' .
					'				END AS Localisation' .
					'				,CASE' .
					'					WHEN	equipesDomicile.Equipe = ' . $equipeVisiteur .
					'					THEN	equipesVisiteur.Equipes_Nom' .
					'					ELSE	equipesDomicile.Equipes_Nom' .
					'				END AS Equipe' .
					'				,CASE' .
					'					WHEN	equipesDomicile.Equipe = ' . $equipeVisiteur .
					'					THEN	CASE' .
					'								WHEN	Matches_ScoreEquipeDomicile > Matches_ScoreEquipeVisiteur' .
					'								THEN	\'V\'' .
					'								WHEN	Matches_ScoreEquipeDomicile = Matches_ScoreEquipeVisiteur' .
					'								THEN	\'N\'' .
					'								WHEN	Matches_ScoreEquipeDomicile < Matches_ScoreEquipeVisiteur' .
					'								THEN	\'D\'' .
					'							END' .
					'					ELSE	CASE' .
					'								WHEN	Matches_ScoreEquipeDomicile < Matches_ScoreEquipeVisiteur' .
					'								THEN	\'V\'' .
					'								WHEN	Matches_ScoreEquipeDomicile = Matches_ScoreEquipeVisiteur' .
					'								THEN	\'N\'' .
					'								WHEN	Matches_ScoreEquipeDomicile > Matches_ScoreEquipeVisiteur' .
					'								THEN	\'D\'' .
					'							END' .
					'				END AS Resultat' .
					'				,CONCAT(Matches_ScoreEquipeDomicile, \'-\', Matches_ScoreEquipeVisiteur) AS Score' .
					'				,(' .
					'					SELECT		GROUP_CONCAT(' .
					'									CASE' .
					'										WHEN	Buteurs_CSC = 1' .
					'										THEN	CONCAT(IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille), \' (CSC)\')' .
					'										ELSE	IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille)' .
					'									END SEPARATOR \', \')' .
					'					FROM		matches_buteurs' .
					'					JOIN		joueurs' .
					'								ON		matches_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'					WHERE		matches_buteurs.Matches_Match = matches.Match' .
					'				) AS Buteurs' .

					'	FROM		matches' .
					'	JOIN		journees' .
					'				ON		Journees_Journee = Journee' .
					'	JOIN		equipes equipesDomicile' .
					'				ON		matches.Equipes_EquipeDomicile = equipesDomicile.Equipe' .
					'	JOIN		equipes equipesVisiteur' .
					'				ON		matches.Equipes_EquipeVisiteur = equipesVisiteur.Equipe' .
					'	WHERE		Championnats_Championnat = 1' .
					'				AND' .
					'				(' .
					'					equipesDomicile.Equipe = ' . $equipeVisiteur .
					'					OR		equipesVisiteur.Equipe = ' . $equipeVisiteur .
					'				)' .
					'				AND		matches.Match <> ' . $match .
					'	ORDER BY	matches.Matches_Date DESC' .
					'	LIMIT		10';
	$req = $bdd->query($ordreSQL);
	$equipeVisiteur_resultats = $req->fetchAll();


	// Affichage des résultats
	echo '<table class="tableau--classement tableau--statistique">';
		echo '<thead>';
			echo '<tr class="tableau--classement-nom-colonnes">';
				echo '<th class="bordure-basse" style="width: 50%;">' . $equipeDomicile_Nom . '</th>';
				echo '<th class="bordure-basse" style="width: 50%;">' . $equipeVisiteur_Nom . '</th>';
			echo '</tr>';
		echo '</thead>';
		
		echo '<tbody>';
		
			// Meilleurs buteurs
			echo '<tr class="neutre">';
				echo '<td colspan="2">Meilleurs buteurs</td>';
			echo '</tr>';
		
			echo '<tr class="curseur-main neutre" onclick="afficherMasquerObjet(\'buteurs\');">';
				echo '<td class="bordure-basse" title="' . $equipeDomicile_buteurs[0]["Buteurs"] . '">' . $equipeDomicile_meilleurs_buteurs[0]["Buteurs"];
				echo '<td class="bordure-basse" title="' . $equipeVisiteur_buteurs[0]["Buteurs"] . '">' . $equipeVisiteur_meilleurs_buteurs[0]["Buteurs"];
			echo '</tr>';
			
			echo '<tr id="buteurs" class="curseur-main neutre" style="display: none; vertical-align: top;" onclick="afficherMasquerObjet(\'buteurs\');">';
				echo '<td class="bordure-basse tous-buteurs" style="word-wrap: break-word;">' . $equipeDomicile_buteurs[0]["Buteurs"] . '</td>';
				echo '<td class="bordure-basse tous-buteurs" style="word-wrap: break-word;">' . $equipeVisiteur_buteurs[0]["Buteurs"] . '</td>';
			echo '</tr>';

			echo '<tr class="neutre">';
				echo '<td colspan="2">Matches joués</td>';
			echo '</tr>';
			echo '<tr class="neutre">';
				echo '<td class="bordure-basse">';
					echo '<table style="border: none; margin: 0 auto;">';
						echo '<tr>';
							echo '<td>Clt général</td>';
							echo '<td>Victoires</td>';
							echo '<td>Nuls</td>';
							echo '<td>Défaites</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>' . $equipeDomicile_classements[0]["Classement_General"] . '</td>';
							echo '<td>' . $equipeDomicile_general[0]["Nombre_Victoires"] . '</td>';
							echo '<td>' . $equipeDomicile_general[0]["Nombre_Nuls"] . '</td>';
							echo '<td>' . $equipeDomicile_general[0]["Nombre_Defaites"] . '</td>';
						echo '</tr>';
					echo '</table>';
				echo '</td>';

				echo '<td class="bordure-basse">';
					echo '<table style="border: none; margin: 0 auto;">';
						echo '<tr>';
							echo '<td>Clt général</td>';
							echo '<td>Victoires</td>';
							echo '<td>Nuls</td>';
							echo '<td>Défaites</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>' . $equipeVisiteur_classements[0]["Classement_General"] . '</td>';
							echo '<td>' . $equipeVisiteur_general[0]["Nombre_Victoires"] . '</td>';
							echo '<td>' . $equipeVisiteur_general[0]["Nombre_Nuls"] . '</td>';
							echo '<td>' . $equipeVisiteur_general[0]["Nombre_Defaites"] . '</td>';
						echo '</tr>';
					echo '</table>';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr class="neutre">';
				echo '<td>Matches à domicile</td>';
				echo '<td>Matches à l\'extérieur</td>';
			echo '</tr>';
			
			echo '<tr class="neutre">';
				echo '<td class="bordure-basse">';
					echo '<table style="border: none; margin: 0 auto;">';
						echo '<tr>';
							echo '<td>Clt dom.</td>';
							echo '<td>Victoires</td>';
							echo '<td>Nuls</td>';
							echo '<td>Défaites</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>' . $equipeDomicile_classements[0]["Classement_Domicile"] . '</td>';
							echo '<td>' . $equipeDomicile_domicile[0]["Nombre_Victoires"] . '</td>';
							echo '<td>' . $equipeDomicile_domicile[0]["Nombre_Nuls"] . '</td>';
							echo '<td>' . $equipeDomicile_domicile[0]["Nombre_Defaites"] . '</td>';
						echo '</tr>';
					echo '</table>';
				echo '</td>';
				echo '<td class="bordure-basse">';
					echo '<table style="border: none; margin: 0 auto;">';
						echo '<tr>';
							echo '<td>Clt ext.</td>';
							echo '<td>Victoires</td>';
							echo '<td>Nuls</td>';
							echo '<td>Défaites</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>' . $equipeVisiteur_classements[0]["Classement_Visiteur"] . '</td>';
							echo '<td>' . $equipeVisiteur_visiteur[0]["Nombre_Victoires"] . '</td>';
							echo '<td>' . $equipeVisiteur_visiteur[0]["Nombre_Nuls"] . '</td>';
							echo '<td>' . $equipeVisiteur_visiteur[0]["Nombre_Defaites"] . '</td>';
						echo '</tr>';
					echo '</table>';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr class="neutre">';
				echo '<td colspan="2">Attaque et défense (att. = attaque - déf. = défense - dom. = domicile - ext. = extérieur)</td>';
			echo '</tr>';
			
			echo '<tr class="neutre">';
				echo '<td class="bordure-basse">';
					echo '<table style="border: none; margin: 0 auto;">';
						echo '<tr>';
							echo '<td>Clt att.</td>';
							echo '<td>Clt att. dom.</td>';
							echo '<td>BP dom.</td>';
							echo '<td>Clt déf.</td>';
							echo '<td>Clt déf. dom.</td>';
							echo '<td>BC dom.</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>' . $equipeDomicile_classements[0]["Classement_Attaque"] . '</td>';
							echo '<td>' . $equipeDomicile_classements[0]["Classement_Attaque_Domicile"] . '</td>';
							echo '<td>' . $equipeDomicile_classements[0]["Classement_Attaque_Domicile_Buts"] . '</td>';
							echo '<td>' . $equipeDomicile_classements[0]["Classement_Defense"] . '</td>';
							echo '<td>' . $equipeDomicile_classements[0]["Classement_Defense_Domicile"] . '</td>';
							echo '<td>' . $equipeDomicile_classements[0]["Classement_Defense_Domicile_Buts"] . '</td>';
						echo '</tr>';
					echo '</table>';
				echo '</td>';
				echo '<td class="bordure-basse">';
					echo '<table style="border: none; margin: 0 auto;">';
						echo '<tr>';
							echo '<td>Clt att.</td>';
							echo '<td>Clt att. ext.</td>';
							echo '<td>BP ext.</td>';
							echo '<td>Clt déf.</td>';
							echo '<td>Clt déf. ext.</td>';
							echo '<td>BC ext.</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>' . $equipeVisiteur_classements[0]["Classement_Attaque"] . '</td>';
							echo '<td>' . $equipeVisiteur_classements[0]["Classement_Attaque_Visiteur"] . '</td>';
							echo '<td>' . $equipeVisiteur_classements[0]["Classement_Attaque_Visiteur_Buts"] . '</td>';
							echo '<td>' . $equipeVisiteur_classements[0]["Classement_Defense"] . '</td>';
							echo '<td>' . $equipeVisiteur_classements[0]["Classement_Defense_Visiteur"] . '</td>';
							echo '<td>' . $equipeVisiteur_classements[0]["Classement_Defense_Visiteur_Buts"] . '</td>';
						echo '</tr>';
					echo '</table>';
				echo '</td>';
			echo '</tr>';
		echo '</tbody>';
	echo '</table>';
	
	echo '<table class="tableau--classement tableau--statistique" style="margin: 0 auto;">';
		echo '<tbody>';
			echo '<tr class="neutre">';
				echo '<td style="vertical-align: top;">';
					echo '<table style="table-layout: fixed; border: none; margin: 0 auto;">';
						foreach($equipeDomicile_resultats as $unResultat) {
							if($unResultat["Buteurs"] != '')		echo '<tr class="curseur-main" title="' . $unResultat["Buteurs"] . '" onclick="afficherMasquerObjet(\'match_' . $unResultat["Match"] . '\');">';
							else									echo '<tr>';
								echo '<td>' . $unResultat["Localisation"] . '</td>';
								echo '<td>' . $unResultat["Equipe"] . '</td>';
								echo '<td>' . $unResultat["Score"] . '</td>';
								if($unResultat["Resultat"] == 'V')
									$classe = 'vert';
								else if($unResultat["Resultat"] == 'N')
									$classe = 'orange';
								else if($unResultat["Resultat"] == 'D')
									$classe = 'rouge';
								echo '<td class="' . $classe . '">' . $unResultat["Resultat"] . '</td>';
							echo '</tr>';
							
							if($unResultat["Buteurs"] != '') {
								// Affichage de tous les buteurs dans une ligne cachée
								echo '<tr class="curseur-main" id="match_' . $unResultat["Match"] . '" style="display: none;" onclick="afficherMasquerObjet(\'match_' . $unResultat["Match"] . '\');">';
									echo '<td colspan="4" class="buteurs-match" style="word-wrap: break-word;">' . $unResultat["Buteurs"] . '</td>';
								echo '</tr>';
							}
						}
					echo '</table>';
				echo '</td>';
				
				echo '<td style="vertical-align: top;">';
					echo '<table style="table-layout: fixed; border: none; margin: 0 auto;">';
						foreach($equipeVisiteur_resultats as $unResultat) {
							if($unResultat["Buteurs"] != '')		echo '<tr class="curseur-main" title="' . $unResultat["Buteurs"] . '" onclick="afficherMasquerObjet(\'match_' . $unResultat["Match"] . '\');">';
							else									echo '<tr>';
								if($unResultat["Resultat"] == 'V')
									$classe = 'vert';
								else if($unResultat["Resultat"] == 'N')
									$classe = 'orange';
								else if($unResultat["Resultat"] == 'D')
									$classe = 'rouge';
								echo '<td class="' . $classe . '">' . $unResultat["Resultat"] . '</td>';
								echo '<td>' . $unResultat["Score"] . '</td>';
								echo '<td>' . $unResultat["Equipe"] . '</td>';
								echo '<td>' . $unResultat["Localisation"] . '</td>';
							echo '</tr>';
							
							if($unResultat["Buteurs"] != '') {
								// Affichage de tous les buteurs dans une ligne cachée
								echo '<tr class="curseur-main" id="match_' . $unResultat["Match"] . '" style="display: none;" onclick="afficherMasquerObjet(\'match_' . $unResultat["Match"] . '\');">';
									echo '<td colspan="4" class="buteurs-match" style="word-wrap: break-word;">' . $unResultat["Buteurs"] . '</td>';
								echo '</tr>';
							}
						}
					echo '</table>';
				echo '</td>';
			echo '</tr>';
		echo '</tbody>';
	echo '</table>';
	
?>

<script>
	$(function() {
		var tousButeursLargeurActuelle = $('.tous-buteurs').css('width');
		$('.tous-buteurs').css('max-width', tousButeursLargeurActuelle);
		
		var buteursMatchLargeurActuelle = $('.buteurs-match').css('width');
		$('.buteurs-match').css('max-width', buteursMatchLargeurActuelle);
	});
</script>