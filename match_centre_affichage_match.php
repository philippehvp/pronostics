<?php

	include('commun.php');
	include_once('fonctions.php');
	
	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;

	// Lecture des vainqueurs pronostiqués pour les matches de type 1 et 2
	function lireVainqueurPronostique1Et2($bdd, $match, &$nombrePronosticsVictoireDomicile, &$nombrePronosticsMatchNul, &$nombrePronosticsVictoireVisiteur, &$nombreOublis) {
		$ordreSQL =		'	SELECT		COUNT(*) AS Nombre, Vainqueur' .
						'	FROM		(' .
						'					SELECT		fn_calculvainqueurpronostic	(	NULL' .
						'																,pronostics.Pronostics_ScoreEquipeDomicile' .
						'																,pronostics.Pronostics_ScoreEquipeVisiteur' .
						'																,NULL' .
						'																,NULL' .
						'																,NULL' .
						'																,NULL' .
						'															) AS Vainqueur' .
						'					FROM		matches' .
						'					JOIN		(' .
						'									SELECT		pronostics.Matches_Match' .
						'												,pronostics.Pronostics_ScoreEquipeDomicile' .
						'												,pronostics.Pronostics_ScoreEquipeVisiteur' .
						'									FROM		matches' .
						'									JOIN		pronostics' .
						'												ON		matches.Match = pronostics.Matches_Match' .
						'									WHERE		matches.Match = ' . $match .
						'								) pronostics' .
						'								ON		matches.Match = pronostics.Matches_Match' .
						'					WHERE		matches.Match = ' . $match .
						'				) pronostics_vainqueur' .
						'	GROUP BY	Vainqueur';

		$req = $bdd->query($ordreSQL);
		$vainqueurs = $req->fetchAll();
		
		foreach($vainqueurs as $unVainqueur) {
			switch($unVainqueur["Vainqueur"]) {
				case 0: $nombrePronosticsMatchNul = $unVainqueur["Nombre"];
				break;
				case 1: $nombrePronosticsVictoireDomicile = $unVainqueur["Nombre"];
				break;
				case 2: $nombrePronosticsVictoireVisiteur = $unVainqueur["Nombre"];
				break;
				case -1: $nombreOublis = $unVainqueur["Nombre"];
				break;
			}
		}
		
		if($nombrePronosticsVictoireDomicile == '?')		$nombrePronosticsVictoireDomicile = 0;
		if($nombrePronosticsMatchNul == '?')				$nombrePronosticsMatchNul = 0;
		if($nombrePronosticsVictoireVisiteur == '?')		$nombrePronosticsVictoireVisiteur = 0;
		if($nombreOublis == '?')							$nombreOublis = 0;
	}

	// Lecture des vainqueurs pronostiqués des matches de type 3
	function lireVainqueurPronostique3($bdd, $match, &$nombrePronosticsVictoireDomicile, &$nombrePronosticsMatchNul, &$nombrePronosticsVictoireVisiteur, &$nombreOublis, &$nombrePronosticsQualificationDomicile, &$nombrePronosticsQualificationVisiteur) {
		$ordreSQL =		'	SELECT		COUNT(*) AS Nombre, Vainqueur' .
						'	FROM		(' .
						'					SELECT		fn_calculvainqueurpronostic	(	NULL' .
						'																,pronostics.Pronostics_ScoreEquipeDomicile' .
						'																,pronostics.Pronostics_ScoreEquipeVisiteur' .
						'																,NULL' .
						'																,NULL' .
						'																,pronostics.Pronostics_ScoreAPEquipeDomicile' .
						'																,pronostics.Pronostics_ScoreAPEquipeVisiteur' .
						'															) AS Vainqueur' .
						'					FROM		matches' .
						'					JOIN		(' .
						'									SELECT		pronostics.Matches_Match' .
						'												,pronostics.Pronostics_ScoreEquipeDomicile' .
						'												,pronostics.Pronostics_ScoreEquipeVisiteur' .
						'												,pronostics.Pronostics_ScoreAPEquipeDomicile' .
						'												,pronostics.Pronostics_ScoreAPEquipeVisiteur' .
						'									FROM		matches' .
						'									JOIN		pronostics' .
						'												ON		matches.Match = pronostics.Matches_Match' .
						'									WHERE		matches.Match = ' . $match .
						'								) pronostics' .
						'								ON		matches.Match = pronostics.Matches_Match' .
						'					WHERE		matches.Match = ' . $match .
						'				) pronostics_vainqueur' .
						'	GROUP BY	Vainqueur';

		$req = $bdd->query($ordreSQL);
		$vainqueurs = $req->fetchAll();
		
		foreach($vainqueurs as $unVainqueur) {
			switch($unVainqueur["Vainqueur"]) {
				case 0: $nombrePronosticsMatchNul = $unVainqueur["Nombre"];
				break;
				case 1: $nombrePronosticsVictoireDomicile = $unVainqueur["Nombre"];
				break;
				case 2: $nombrePronosticsVictoireVisiteur = $unVainqueur["Nombre"];
				break;
				case -1: $nombreOublis = $unVainqueur["Nombre"];
				break;
			}
		}
		
		if($nombrePronosticsVictoireDomicile == '?')		$nombrePronosticsVictoireDomicile = 0;
		if($nombrePronosticsMatchNul == '?')				$nombrePronosticsMatchNul = 0;
		if($nombrePronosticsVictoireVisiteur == '?')		$nombrePronosticsVictoireVisiteur = 0;
		if($nombreOublis == '?')							$nombreOublis = 0;
		
		// Lecture du vainqueur qualifié
		$ordreSQL =		'	SELECT		COUNT(*) AS Nombre, Vainqueur' .
						'	FROM		(' .
						'					SELECT		fn_calculvainqueurpronostic	(	pronostics.Pronostics_Vainqueur' .
						'																,pronostics.Pronostics_ScoreEquipeDomicile' .
						'																,pronostics.Pronostics_ScoreEquipeVisiteur' .
						'																,pronostics.PronosticsAller_Pronostics_ScoreEquipeDomicile' .
						'																,pronostics.PronosticsAller_Pronostics_ScoreEquipeVisiteur' .
						'																,pronostics.Pronostics_ScoreAPEquipeDomicile' .
						'																,pronostics.Pronostics_ScoreAPEquipeVisiteur' .
						'															) AS Vainqueur' .
						'					FROM		matches' .
						'					JOIN		(' .
						'									SELECT		pronostics.Matches_Match' .
						'												,pronostics.Pronostics_Vainqueur' .
						'												,pronostics.Pronostics_ScoreEquipeDomicile' .
						'												,pronostics.Pronostics_ScoreEquipeVisiteur' .
						'												,pronosticsaller.Pronostics_ScoreEquipeDomicile AS PronosticsAller_Pronostics_ScoreEquipeDomicile' .
						'												,pronosticsaller.Pronostics_ScoreEquipeVisiteur AS PronosticsAller_Pronostics_ScoreEquipeVisiteur' .
						'												,pronostics.Pronostics_ScoreAPEquipeDomicile' .
						'												,pronostics.Pronostics_ScoreAPEquipeVisiteur' .
						'									FROM		matches' .
						'									JOIN		pronostics' .
						'												ON		matches.Match = pronostics.Matches_Match' .
						'									LEFT JOIN	pronostics pronosticsaller' .
						'												ON		matches.Matches_MatchLie = pronosticsaller.Matches_Match' .
						'												AND		pronostics.Pronostiqueurs_Pronostiqueur = pronosticsaller.Pronostiqueurs_Pronostiqueur' .
						'									WHERE		matches.Match = ' . $match .
						'								) pronostics' .
						'								ON		matches.Match = pronostics.Matches_Match' .
						'					WHERE		matches.Match = ' . $match .
						'				) pronostics_vainqueur' .
						'	GROUP BY	Vainqueur';

		$req = $bdd->query($ordreSQL);
		$vainqueurs = $req->fetchAll();
		
		foreach($vainqueurs as $unVainqueur) {
			switch($unVainqueur["Vainqueur"]) {
				case 1: $nombrePronosticsQualificationDomicile = $unVainqueur["Nombre"];
				break;
				case 2: $nombrePronosticsQualificationVisiteur = $unVainqueur["Nombre"];
				break;
			}
		}
		
		if($nombrePronosticsQualificationDomicile == '?')		$nombrePronosticsQualificationDomicile = 0;
		if($nombrePronosticsQualificationVisiteur == '?')		$nombrePronosticsQualificationVisiteur = 0;

	}
	
	// Lecture des vainqueurs pronostiqués pour les matches de type 4 et 5
	function lireVainqueurPronostique4Et5($bdd, $match, &$nombrePronosticsVictoireDomicile, &$nombrePronosticsVictoireVisiteur, &$nombreOublis) {
		$ordreSQL =		'	SELECT		COUNT(*) AS Nombre, Vainqueur' .
						'	FROM		(' .
						'					SELECT		fn_calculvainqueurpronostic	(	pronostics.Pronostics_Vainqueur' .
						'																,pronostics.Pronostics_ScoreEquipeDomicile' .
						'																,pronostics.Pronostics_ScoreEquipeVisiteur' .
						'																,pronostics.PronosticsAller_Pronostics_ScoreEquipeDomicile' .
						'																,pronostics.PronosticsAller_Pronostics_ScoreEquipeVisiteur' .
						'																,pronostics.Pronostics_ScoreAPEquipeDomicile' .
						'																,pronostics.Pronostics_ScoreAPEquipeVisiteur' .
						'															) AS Vainqueur' .
						'					FROM		matches' .
						'					JOIN		(' .
						'									SELECT		pronostics.Matches_Match' .
						'												,pronostics.Pronostics_Vainqueur' .
						'												,pronostics.Pronostics_ScoreEquipeDomicile' .
						'												,pronostics.Pronostics_ScoreEquipeVisiteur' .
						'												,pronosticsaller.Pronostics_ScoreEquipeDomicile AS PronosticsAller_Pronostics_ScoreEquipeDomicile' .
						'												,pronosticsaller.Pronostics_ScoreEquipeVisiteur AS PronosticsAller_Pronostics_ScoreEquipeVisiteur' .
						'												,pronostics.Pronostics_ScoreAPEquipeDomicile' .
						'												,pronostics.Pronostics_ScoreAPEquipeVisiteur' .
						'									FROM		matches' .
						'									JOIN		pronostics' .
						'												ON		matches.Match = pronostics.Matches_Match' .
						'									LEFT JOIN	pronostics pronosticsaller' .
						'												ON		matches.Matches_MatchLie = pronosticsaller.Matches_Match' .
						'												AND		pronostics.Pronostiqueurs_Pronostiqueur = pronosticsaller.Pronostiqueurs_Pronostiqueur' .
						'									WHERE		matches.Match = ' . $match .
						'								) pronostics' .
						'								ON		matches.Match = pronostics.Matches_Match' .
						'					WHERE		matches.Match = ' . $match .
						'				) pronostics_vainqueur' .
						'	GROUP BY	Vainqueur';

		$req = $bdd->query($ordreSQL);
		$vainqueurs = $req->fetchAll();
		
		foreach($vainqueurs as $unVainqueur) {
			switch($unVainqueur["Vainqueur"]) {
				case 1: $nombrePronosticsVictoireDomicile = $unVainqueur["Nombre"];
				break;
				case 2: $nombrePronosticsVictoireVisiteur = $unVainqueur["Nombre"];
				break;
				case -1: $nombreOublis = $unVainqueur["Nombre"];
				break;
			}
		}
		
		if($nombrePronosticsVictoireDomicile == '?')		$nombrePronosticsVictoireDomicile = 0;
		if($nombrePronosticsVictoireVisiteur == '?')		$nombrePronosticsVictoireVisiteur = 0;
		if($nombreOublis == '?')							$nombreOublis = 0;
	}
	
	// Affichage du nombre de pronostics donnant l'équipe domicile vainqueur, le match nul, l'équipe visiteur vainqueur pour les matches de type 1 et 2
	function afficherVainqueurPronostique1Et2($bdd, $match, $nombrePronosticsVictoireDomicile, $nombrePronosticsMatchNul, $nombrePronosticsVictoireVisiteur, $nombreOublis) {
		if($nombreOublis > 0)			echo '<div title="Victoires | Nuls | Défaites | Oublis">';
		else							echo '<div title="Victoires | Nuls | Défaites">';
			echo '<span onclick="consulterMatch_afficherRepartitionVainqueurPronostiqueMatchRegulier(' . $match . ');" class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsVictoireDomicile . '</label></span>';
			echo '<span onclick="consulterMatch_afficherRepartitionVainqueurPronostiqueMatchRegulier(' . $match . ');" class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsMatchNul . '</label></span>';
			echo '<span onclick="consulterMatch_afficherRepartitionVainqueurPronostiqueMatchRegulier(' . $match . ');" class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsVictoireVisiteur . '</label></span>';
			if($nombreOublis > 0)
				echo '<span onclick="consulterMatch_afficherRepartitionVainqueurPronostiqueMatchRegulier(' . $match . ');" class="stats-match--resultat"><label class="texte-rouge curseur-main">' . $nombreOublis . '</label></span>';
		echo '</div>';
	}
	
	// Affichage du nombre de pronostics donnant l'équipe domicile vainqueur, le match nul, l'équipe visiteur vainqueur pour les matches de type 3
	function afficherVainqueurPronostique3($bdd, $match, $nombrePronosticsVictoireDomicile, $nombrePronosticsMatchNul, $nombrePronosticsVictoireVisiteur, $nombreOublis, $nombrePronosticsQualificationDomicile, $nombrePronosticsQualificationVisiteur) {
		if($nombreOublis > 0)			echo '<div title="Victoires | Nuls | Défaites | Oublis">';
		else							echo '<div title="Victoires | Nuls | Défaites">';
			echo '<span onclick="consulterMatch_afficherResultatMatchRetour(' . $match . ');" class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsVictoireDomicile . '</label></span>';
			echo '<span onclick="consulterMatch_afficherResultatMatchRetour(' . $match . ');" class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsMatchNul . '</label></span>';
			echo '<span onclick="consulterMatch_afficherResultatMatchRetour(' . $match . ');" class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsVictoireVisiteur . '</label></span>';
			if($nombreOublis > 0)
				echo '<span onclick="consulterMatch_afficherResultatMatchRetour(' . $match . ');" class="stats-match--resultat"><label class="texte-rouge curseur-main">' . $nombreOublis . '</label></span>';
		echo '</div>';

		echo '<div title="Qualification pour le tour suivant">';
			echo '<span onclick="consulterMatch_afficherRepartitionVainqueurQualifie(' . $match . ');" class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsQualificationDomicile . '</label></span>';
			echo '<span onclick="consulterMatch_afficherRepartitionVainqueurQualifie(' . $match . ');" class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsQualificationVisiteur . '</label></span>';
		echo '</div>';
	}
	
	// Affichage du nombre de pronostics donnant l'équipe domicile vainqueur, l'équipe visiteur vainqueur pour les matches de type 4 et 5
	function afficherVainqueurPronostique4Et5($bdd, $match, $nombrePronosticsVictoireDomicile, $nombrePronosticsVictoireVisiteur, $nombreOublis) {
		if($nombreOublis > 0)			echo '<div title="Victoires | Nuls | Défaites | Oublis">';
		else							echo '<div title="Victoires | Nuls | Défaites">';
			echo '<span onclick="consulterMatch_afficherRepartitionVainqueurPronostiqueMatchCoupe(' . $match . ');" class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsVictoireDomicile . '</label></span>';
			echo '<span onclick="consulterMatch_afficherRepartitionVainqueurPronostiqueMatchCoupe(' . $match . ');" class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsVictoireVisiteur . '</label></span>';
		
			if($nombreOublis > 0)
				echo '<span onclick="consulterMatch_afficherRepartitionVainqueurPronostiqueMatchCoupe(' . $match . ');" class="stats-match--resultat"><label class="texte-rouge curseur-main">' . $nombreOublis . '</label></span>';
		echo '</div>';
	}

	// Données du match
	$ordreSQL =		'	SELECT		matches.Match, Matches_DateMAJ' .
					'				,matches.Matches_MatchCS, matches.Matches_AvecProlongation, matches.Matches_MatchLie' .
					'				,IFNULL(matches.Matches_CoteEquipeDomicile, 0) AS Matches_CoteEquipeDomicile' .
					'				,IFNULL(matches.Matches_CoteNul, 0) AS Matches_CoteNul' .
					'				,IFNULL(matches.Matches_CoteEquipeVisiteur, 0) AS Matches_CoteEquipeVisiteur' .
					'				,Equipes_EquipeDomicile, Equipes_EquipeVisiteur, equipesDomicile.Equipes_Nom AS EquipesDomicile_Nom, equipesVisiteur.Equipes_Nom AS EquipesVisiteur_Nom' .
					'				,IFNULL(equipesDomicile.Equipes_Fanion, \'_inconnu.png\') AS EquipesDomicile_Fanion, IFNULL(equipesVisiteur.Equipes_Fanion, \'_inconnu.png\') AS EquipesVisiteur_Fanion' .
					'				,fn_matchpronostiquable(' . $match . ', ' . $_SESSION["pronostiqueur"] . ') AS Matches_Pronostiquable' .
					'	FROM		matches' .
					'	JOIN		equipes equipesDomicile' .
					'				ON		matches.Equipes_EquipeDomicile = equipesDomicile.Equipe' .
					'	JOIN		equipes equipesVisiteur' .
					'				ON		matches.Equipes_EquipeVisiteur = equipesVisiteur.Equipe' .
					'	WHERE		matches.Match = ' . $match;
	$req = $bdd->query($ordreSQL);
	$matches = $req->fetchAll();
	$equipeDomicile = $matches[0]["Equipes_EquipeDomicile"];
	$equipeVisiteur = $matches[0]["Equipes_EquipeVisiteur"];
	$equipeDomicile_Nom = $matches[0]["EquipesDomicile_Nom"];
	$equipeVisiteur_Nom = $matches[0]["EquipesVisiteur_Nom"];
	$equipeDomicile_Fanion = $matches[0]["EquipesDomicile_Fanion"];
	$equipeVisiteur_Fanion = $matches[0]["EquipesVisiteur_Fanion"];
	$coteVictoire = calculerCote($matches[0]["Matches_CoteEquipeDomicile"]);
	$coteNul = calculerCote($matches[0]["Matches_CoteNul"]);
	$coteDefaite = calculerCote($matches[0]["Matches_CoteEquipeVisiteur"]);
	$matchCS = $matches[0]["Matches_MatchCS"] != null ? $matches[0]["Matches_MatchCS"] : 0;
	$matchLie = $matches[0]["Matches_MatchLie"] != null ? $matches[0]["Matches_MatchLie"] : 0;
	$matchAP = $matches[0]["Matches_AvecProlongation"] != null ? $matches[0]["Matches_AvecProlongation"] : 0;
	$matchDateMAJ = $matches[0]["Matches_DateMAJ"] != null ? $matches[0]["Matches_DateMAJ"] : 0;
	
	
	// Lors de la lecture de la date de mise à jour du match, il est nécessaire d'indiquer cette date à a page conteneur
	// Pour cela, on va écrire dans une zone temporaire la valeur lue pour qu'elle puisse être ensuite mise à jour dans la page conteneur
	
	echo '<input type="hidden" name="date_maj_match_temporaire" value="' . $matchDateMAJ . '">';
	
	if($matchCS == 1)									$typeMatch = 5;
	else if($matchAP == 0 && $matchLie == 0)			$typeMatch = 1;
	else if($matchAP == 0 && $matchLie != 0)			$typeMatch = 2;
	else if($matchAP == 1 && $matchLie != 0)			$typeMatch = 3;
	else												$typeMatch = 4;

	// L'affichage du pronostic du match sur la victoire, le nul ou la défaite ainsi que de l'équipe qualifiée dépend du type de match :
	// - match de type 1 et 2 : victoire - nul - défaite
	// - match de type 3 : victoire - nul - défaite jusqu'aux TAB + équipe qualifiée
	// - match de type 4 et 5 : victoire - défaite
	if($typeMatch == 1 || $typeMatch == 2) {
		$nombrePronosticsVictoireDomicile = $nombrePronosticsMatchNul = $nombrePronosticsVictoireVisiteur = $nombreOublis = '?';
		if($matches[0]["Matches_Pronostiquable"] == 0)
			lireVainqueurPronostique1Et2($bdd, $match, $nombrePronosticsVictoireDomicile, $nombrePronosticsMatchNul, $nombrePronosticsVictoireVisiteur, $nombreOublis);
	}
	else if($typeMatch == 3) {
		$nombrePronosticsVictoireDomicile = $nombrePronosticsMatchNul = $nombrePronosticsVictoireVisiteur = $nombreOublis = '?';
		$nombrePronosticsQualificationDomicile = $nombrePronosticsQualificationVisiteur = '?';
		if($matches[0]["Matches_Pronostiquable"] == 0)
			lireVainqueurPronostique3($bdd, $match, $nombrePronosticsVictoireDomicile, $nombrePronosticsMatchNul, $nombrePronosticsVictoireVisiteur, $nombreOublis, $nombrePronosticsQualificationDomicile, $nombrePronosticsQualificationVisiteur);
	}
	else if($typeMatch == 4 || $typeMatch = 5) {
		$nombrePronosticsVictoireDomicile = $nombrePronosticsVictoireVisiteur = $nombreOublis = '?';
		if($matches[0]["Matches_Pronostiquable"] == 0)
			lireVainqueurPronostique4Et5($bdd, $match, $nombrePronosticsVictoireDomicile, $nombrePronosticsVictoireVisiteur, $nombreOublis);
	}

	// Effectif sur le terrain de chaque équipe (et liaison avec les buteurs)
	// Equipe domicile
	$ordreSQL =		'	SELECT		IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) AS Joueurs_NomCourt' .
					'				,CASE' .
					'					WHEN	matches_buteurs.Joueurs_Joueur IS NOT NULL' .
					'					THEN	1' .
					'					ELSE	0' .
					'				END AS Buteur' .
					'				,Nombre_Buts' .
					'				,fn_calculcotebuteur(Buteurs_Cote) AS Buteurs_Cote' .
					'	FROM		matches_participants' .
					'	JOIN		matches' .
					'				ON		matches_participants.Matches_Match = matches.Match' .
					'						AND		matches_participants.Equipes_Equipe = matches.Equipes_EquipeDomicile' .
					'	JOIN		joueurs' .
					'				ON		matches_participants.Joueurs_Joueur = joueurs.Joueur' .
					'	LEFT JOIN	(' .
					'					SELECT		Matches_Match, Joueurs_Joueur, Buteurs_Cote, COUNT(*) AS Nombre_Buts' .
					'					FROM		matches_buteurs' .
					'					WHERE		Matches_Match = ' . $match .
					'								AND		Buteurs_CSC = 0' .
					'					GROUP BY	Matches_Match, Joueurs_Joueur, Buteurs_Cote' .
					'				) matches_buteurs' .
					'				ON		matches_participants.Matches_Match = matches_buteurs.Matches_Match' .
					'						AND		matches_participants.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
					'	WHERE		matches.Match = ' . $match .
					'	ORDER BY	Buteur DESC, joueurs.Postes_Poste DESC';
	$req = $bdd->query($ordreSQL);
	$equipeDomicile_participants = $req->fetchAll();

	// Equipe visiteur
	$ordreSQL =		'	SELECT		IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) AS Joueurs_NomCourt' .
					'				,CASE' .
					'					WHEN	matches_buteurs.Joueurs_Joueur IS NOT NULL' .
					'					THEN	1' .
					'					ELSE	0' .
					'				END AS Buteur' .
					'				,Nombre_Buts' .
					'				,fn_calculcotebuteur(Buteurs_Cote) AS Buteurs_Cote' .
					'	FROM		matches_participants' .
					'	JOIN		matches' .
					'				ON		matches_participants.Matches_Match = matches.Match' .
					'						AND		matches_participants.Equipes_Equipe = matches.Equipes_EquipeVisiteur' .
					'	JOIN		joueurs' .
					'				ON		matches_participants.Joueurs_Joueur = joueurs.Joueur' .
					'	LEFT JOIN	(' .
					'					SELECT		Matches_Match, Joueurs_Joueur, Buteurs_Cote, COUNT(*) AS Nombre_Buts' .
					'					FROM		matches_buteurs' .
					'					WHERE		Matches_Match = ' . $match .
					'								AND		Buteurs_CSC = 0' .
					'					GROUP BY	Matches_Match, Joueurs_Joueur, Buteurs_Cote' .
					'				) matches_buteurs' .
					'				ON		matches_participants.Matches_Match = matches_buteurs.Matches_Match' .
					'						AND		matches_participants.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
					'	WHERE		matches.Match = ' . $match .
					'	ORDER BY	Buteur DESC, joueurs.Postes_Poste DESC';
	$req = $bdd->query($ordreSQL);
	$equipeVisiteur_participants = $req->fetchAll();
	

	// On écrit aussi le numéro de match
	echo '<input type="hidden" name="match" value="' . $match . '" />';
	
	echo '<table class="mc--tableau-equipes scroll-pane">';
		echo '<thead>';
			echo '<tr>';
				echo '<td width="45%" class="bordure-basse">';
					echo '<label class="mc--titre-section">' . $equipeDomicile_Nom . '</label>';
					echo '<img class="fanion" src="images/equipes/' . $equipeDomicile_Fanion . '" alt="" />';
				echo '</td>';
				
				echo '<td width="10%" class="bordure-basse">';
					echo '<img class="curseur-main" src="images/oeil.png" title="Tous les pronostics" alt="Pronostics" onclick="consulterResultats_afficherMatch(' . $matches[0]["Match"] . ', \'' . $equipeDomicile_Nom . '\', \'' . $equipeVisiteur_Nom . '\', 0, 0);" />';
					echo '<br /><br />';
					echo '<img class="curseur-main" src="images/oeil_rival.png" title="Pronostics des rivaux" alt="Pronostics des rivaux" onclick="consulterResultats_afficherMatch(' . $matches[0]["Match"] . ', \'' . $equipeDomicile_Nom . '\', \'' . $equipeVisiteur_Nom . '\', 1, 0);" />';
				echo '</td>';
				
				echo '<td width="45%" class="bordure-basse">';
					echo '<label class="mc--titre-section">' . $equipeVisiteur_Nom . '</label>';
					echo '<img class="fanion" src="images/equipes/' . $equipeVisiteur_Fanion . '" alt="" />';
				echo '</td>';
			echo '</tr>';
		echo '</thead>';
		
		echo '<tbody>';
			echo '<tr>';
					echo '<td colspan="3" class="bordure-basse">';
						echo '<table class="mc--tableau-repartition-pronostics">';
							echo '<thead>';
								echo '<tr class="logistique">';
									echo '<th class="bordure-basse-legere bordure-droite-legere">&nbsp;</th>';
									echo '<th class="bordure-basse-legere bordure-droite-legere">Victoire</th>';
									if($typeMatch == 1 || $typeMatch == 2 || $typeMatch == 3)
										echo '<th class="bordure-basse-legere bordure-droite-legere">Nul</th>';
									echo '<th class="bordure-basse-legere bordure-droite-legere">Défaite</th>';
									echo '<th class="bordure-basse-legere">Oubli</th>';
								echo '</tr>';
							echo '</thead>';
							echo '<tbody>';
								echo '<tr class="logistique">';
									echo '<td class="aligne-droite bordure-droite-legere">Cotes</td>';
									echo '<td class="bordure-droite-legere">' . $coteVictoire . '</td>';
									if($typeMatch == 1 || $typeMatch == 2 || $typeMatch == 3)
										echo '<td class="bordure-droite-legere">' . $coteNul . '</td>';
									echo '<td class="bordure-droite-legere">' . $coteDefaite . '</td>';
									echo '<td>-</td>';
								echo '</tr>';
								
								if($typeMatch == 1 || $typeMatch == 2)				echo '<tr class="logistique curseur-main" onclick="consulterMatch_afficherRepartitionVainqueurPronostiqueMatchRegulier(' . $match . ');">';
								else if($typeMatch == 3)							echo '<tr class="logistique curseur-main" onclick="consulterMatch_afficherResultatMatchRetour(' . $match . ');">';
								else if($typeMatch == 4 || $typeMatch == 5)			echo '<tr class="logistique curseur-main" onclick="consulterMatch_afficherResultatMatchCoupe(' . $match . ');">';
									echo '<td class="aligne-droite bordure-droite-legere">Pronostics</td>';
									echo '<td class="bordure-droite-legere">' . $nombrePronosticsVictoireDomicile . '</td>';
									if($typeMatch == 1 || $typeMatch == 2 || $typeMatch == 3)
										echo '<td class="bordure-droite-legere">' . $nombrePronosticsMatchNul . '</td>';
									echo '<td class="bordure-droite-legere">' . $nombrePronosticsVictoireVisiteur . '</td>';
									echo '<td>' . $nombreOublis . '</td>';
								echo '</tr>';
								
								if($typeMatch == 3) {
									echo '<tr class="logistique curseur-main" onclick="consulterMatch_afficherRepartitionVainqueurQualifie(' . $match . ');">';
										echo '<td class="aligne-droite bordure-droite-legere">Qualification</td>';
										echo '<td class="bordure-droite-legere">' . $nombrePronosticsQualificationDomicile . '</td>';
										echo '<td class="bordure-droite-legere">-</td>';
										echo '<td class="bordure-droite-legere">' . $nombrePronosticsQualificationVisiteur . '</td>';
										echo '<td>-</td>';
									echo '</tr>';
								}
								
							echo '</tbody>';
						
						echo '</table>';
			echo '</tr>';

			echo '<tr>';
				echo '<td class="equipe" style="vertical-align: top;">';
					// Effectif de l'équipe domicile
					$nombreParticipants = sizeof($equipeDomicile_participants);
					if($nombreParticipants) {
						echo '<table class="mc--tableau-effectif">';
							echo '<thead>';
								echo '<tr>';
									echo '<th class="bordure-basse">Buts</th>';
									echo '<th class="bordure-basse aligne-gauche">Joueurs et cotes</th>';
								echo '</tr>';
							echo '</thead>';
							echo '<tbody>';
								for($i = 0; $i < $nombreParticipants; $i++) {
									echo '<tr>';
										if($equipeDomicile_participants[$i]["Buteurs_Cote"] != null) {
											echo '<td class="bordure-droite-legere">' . $equipeDomicile_participants[$i]["Nombre_Buts"] . '</td>';
											echo '<td class="curseur-main aligne-gauche buteur" onclick="afficherButeurs(' . $matches[0]["Match"] . ', ' . $matches[0]["Equipes_EquipeDomicile"] .');">' . $equipeDomicile_participants[$i]["Joueurs_NomCourt"] . ' (' . $equipeDomicile_participants[$i]["Buteurs_Cote"] . ')</td>';
										}
										else {
											echo '<td class="bordure-droite-legere">&nbsp;</td>';
											echo '<td class="aligne-gauche">' . $equipeDomicile_participants[$i]["Joueurs_NomCourt"] . '</td>';
										}
									echo '</tr>';
								}
							echo '</tbody>';
						echo '</table>';
					}
				echo '</td>';
				
				echo '<td>&nbsp;</td>';
				
				echo '<td class="equipe" style="vertical-align: top;">';
					// Effectif de l'équipe visiteur
					$nombreParticipants = sizeof($equipeVisiteur_participants);
					if($nombreParticipants) {
						echo '<table class="mc--tableau-effectif">';
							echo '<thead>';
								echo '<tr>';
									echo '<th class="bordure-basse">Buts</th>';
									echo '<th class="bordure-basse aligne-gauche">Joueurs et cotes</th>';
								echo '</tr>';
							echo '</thead>';
							echo '<tbody>';
								for($i = 0; $i < $nombreParticipants; $i++) {
									echo '<tr>';
										if($equipeVisiteur_participants[$i]["Buteurs_Cote"] != null) {
											echo '<td class="bordure-droite-legere">' . $equipeVisiteur_participants[$i]["Nombre_Buts"] . '</td>';
											echo '<td class="curseur-main aligne-gauche buteur" onclick="afficherButeurs(' . $matches[0]["Match"] . ', ' . $matches[0]["Equipes_EquipeVisiteur"] .');">' . $equipeVisiteur_participants[$i]["Joueurs_NomCourt"] . ' (' . $equipeVisiteur_participants[$i]["Buteurs_Cote"] . ')</td>';
										}
										else {
											echo '<td class="bordure-droite-legere">&nbsp;</td>';
											echo '<td class="aligne-gauche">' . $equipeVisiteur_participants[$i]["Joueurs_NomCourt"] . '</td>';
										}
									echo '</tr>';
								}
							echo '</tbody>';
						echo '</table>';
					}
				echo '</td>';
			echo '</tr>';
		
		echo '</tbody>';
	echo '</table>';
?>


<script>
	$(function() {
		// Création d'un timer de rafraîchissement si celui-ci n'existe pas déjà
		var intervalle = $('input[name="minuteur_match"]').val();
		
		if(intervalle == 0) {
			intervalle = setInterval(function() {
				// Vérification des données affichées pour rafraîchissement si nécessaire
				var match = $('input[name="match"]').val();
				var date_maj_match = $('input[name="date_maj_match_temporaire"]').val();
				$('input[name="date_maj_match"]').val(date_maj_match);
				matchCentre_rafrichirMatch(match, date_maj_match, 'mc--detail-match');
			}, 5000);
			
			$('input[name="minuteur_match"]').val(intervalle);
		}

	});

</script>