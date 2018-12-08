<?php
	// Affichage des détails d'un match
	include_once('commun.php');
	
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	
	include_once('fonctions.php');
	// Affichage des informations de l'équipe (nom, fanion, etc.)
	// Le paramètre domicileVisiteur indique s'il s'agit de l'équipe domicile ou visiteur
	function afficherEquipe($unMatch, $domicileVisiteur) {
		$nomEquipe = $domicileVisiteur == 1 ? $unMatch["EquipesDomicile_Nom"] : $unMatch["EquipesVisiteur_Nom"];
		$fanion = $domicileVisiteur == 1 ? $unMatch["EquipesDomicile_Fanion"] : $unMatch["EquipesVisiteur_Fanion"];
		if($fanion == null)
			$fanion = '_inconnu.png';
		$coteEquipe = $domicileVisiteur == 1 ? calculerCote($unMatch["Matches_CoteEquipeDomicile"]) : calculerCote($unMatch["Matches_CoteEquipeVisiteur"]);
	
		echo '<label>' . $nomEquipe . '</label>';
		echo '<br />';
		echo '<img src="images/equipes/' . $fanion . '" alt="Fanion" />';
	}
	
	// Affichage des informations du match (type de match, horaires, etc.)
	function afficherLogistique($unMatch) {
		setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
		$dateDebut = $unMatch["Matches_Date"] != null ? date("d/m/Y", strtotime($unMatch["Matches_Date"])) : date("d/m/Y");
		$jour = $unMatch["Matches_Date"] != null ? date("w", strtotime($unMatch["Matches_Date"])) : date("w");
		$jourSemaine = strtolower(jourSemaine($jour));
		$heureDebut = $unMatch["Matches_Heure"] != null ? sprintf('%02u', $unMatch["Matches_Heure"]) : sprintf('%02u', date("G"));
		$minuteDebut = $unMatch["Matches_Minute"] != null ? sprintf('%02u', $unMatch["Matches_Minute"]) : sprintf('%02u', (date("i") + 5 - (date("i") % 5)));
		
		echo '<label>Début du match le ' . $jourSemaine . ' ' . $dateDebut . ' à ' . $heureDebut . 'h' . $minuteDebut . '</label>';
		
		if($unMatch["Matches_L1EuropeNom"] != null)
			echo '<br /><label class="matchEuropeen">' . $unMatch["Matches_L1EuropeNom"] . '</label>';

		
	}
	
	// Affichage des cotes de l'équipe (et du nul)
	function afficherCote($unMatch, $nulDomicileVisiteur) {
		if($nulDomicileVisiteur == 0)
			$cote = calculerCote($unMatch["Matches_CoteNul"]);
		else if($nulDomicileVisiteur == 1)
			$cote = calculerCote($unMatch["Matches_CoteEquipeDomicile"]);
		else if($nulDomicileVisiteur == 2)
			$cote = calculerCote($unMatch["Matches_CoteEquipeVisiteur"]);
		
		if($nulDomicileVisiteur == 0)
			echo '<label>Cote du nul : ' . $cote . '</label>';
		else
			echo '<label>Cote victoire : ' . $cote . '</label>';
	}
	
	// Affichage des scores de l'équipe (ainsi que les buteurs)
	function afficherScoreMatch($unMatch) {
		if($unMatch["Matches_Vainqueur"] != null) {
			if($unMatch["Matches_Vainqueur"] == -1)
				$scoreAffiche = $unMatch["Matches_ScoreAPEquipeDomicile"] . ' AP - ' . $unMatch["Matches_ScoreAPEquipeVisiteur"] . ' AP';
			else if($unMatch["Matches_Vainqueur"] == $unResultat["EquipeDomicile"])
				$scoreAffiche = $unMatch["Matches_ScoreAPEquipeDomicile"] . ' TAB - ' . $unMatch["Matches_ScoreAPEquipeVisiteur"];
			else if($unMatch["Matches_Vainqueur"] == $unMatch["EquipesDomicile_Equipe"])
				$scoreAffiche = $unMatch["Matches_ScoreAPEquipeDomicile"] . ' - ' . $unMatch["Matches_ScoreAPEquipeVisiteur"] . ' TAB';
			else
				$scoreAffiche = 'TIRS AU BUT';
		}
		else {
			if($unMatch["Matches_ScoreAPEquipeDomicile"] != null && $unMatch["Matches_ScoreAPEquipeVisiteur"] != null) {
				if($unMatch["Matches_ScoreAPEquipeDomicile"] > $unMatch["Matches_ScoreAPEquipeVisiteur"])
					$scoreAffiche = $unMatch["Matches_ScoreAPEquipeDomicile"] . ' AP - ' . $unMatch["Matches_ScoreAPEquipeVisiteur"];
				else if($unMatch["Matches_ScoreAPEquipeDomicile"] > $unMatch["Matches_ScoreAPEquipeVisiteur"])
					$scoreAffiche = $unMatch["Matches_ScoreAPEquipeDomicile"] . ' - ' . $unMatch["Matches_ScoreAPEquipeVisiteur"] . ' AP';
				else
					$scoreAffiche = $unMatch["Matches_ScoreAPEquipeDomicile"] . ' AP - ' . $unMatch["Matches_ScoreAPEquipeVisiteur"] . ' AP';
			
			}
			else
				$scoreAffiche = $unMatch["Matches_ScoreEquipeDomicile"] . ' - ' . $unMatch["Matches_ScoreEquipeVisiteur"];
		}

		echo '<label class="score">' . $scoreAffiche . '</label>';
	}
	
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
						'					JOIN		pronostics' .
						'								ON		matches.Match = pronostics.Matches_Match' .
						'					JOIN		pronostiqueurs' .
						'								ON		pronostics.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'					WHERE		matches.Match = ' . $match .
						'								AND		pronostiqueurs.Pronostiqueurs_DateDebutPresence <= matches.Matches_Date' .
						'								AND		(pronostiqueurs.Pronostiqueurs_DateFinPresence IS NULL OR pronostiqueurs.Pronostiqueurs_DateFinPresence > matches.Matches_Date)' .
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
						'					JOIN		pronostics' .
						'								ON		matches.Match = pronostics.Matches_Match' .
						'					JOIN		pronostiqueurs' .
						'								ON		pronostics.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'					WHERE		matches.Match = ' . $match .
						'								AND		pronostiqueurs.Pronostiqueurs_DateDebutPresence <= matches.Matches_Date' .
						'								AND		(pronostiqueurs.Pronostiqueurs_DateFinPresence IS NULL OR pronostiqueurs.Pronostiqueurs_DateFinPresence > matches.Matches_Date)' .
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
						'									JOIN		pronostiqueurs' .
						'												ON		pronostics.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'									LEFT JOIN	pronostics pronosticsaller' .
						'												ON		matches.Matches_MatchLie = pronosticsaller.Matches_Match' .
						'												AND		pronostics.Pronostiqueurs_Pronostiqueur = pronosticsaller.Pronostiqueurs_Pronostiqueur' .
						'									WHERE		matches.Match = ' . $match .
						'												AND		pronostiqueurs.Pronostiqueurs_DateDebutPresence <= matches.Matches_Date' .
						'												AND		(pronostiqueurs.Pronostiqueurs_DateFinPresence IS NULL OR pronostiqueurs.Pronostiqueurs_DateFinPresence > matches.Matches_Date)' .
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
						'									JOIN		pronostiqueurs' .
						'												ON		pronostics.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'									LEFT JOIN	pronostics pronosticsaller' .
						'												ON		matches.Matches_MatchLie = pronosticsaller.Matches_Match' .
						'												AND		pronostics.Pronostiqueurs_Pronostiqueur = pronosticsaller.Pronostiqueurs_Pronostiqueur' .
						'									WHERE		matches.Match = ' . $match .
						'												AND		pronostiqueurs.Pronostiqueurs_DateDebutPresence <= matches.Matches_Date' .
						'												AND		(pronostiqueurs.Pronostiqueurs_DateFinPresence IS NULL OR pronostiqueurs.Pronostiqueurs_DateFinPresence > matches.Matches_Date)' .
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
		
			if($nombrePronosticsVictoireDomicile != '?')
				echo '<span onclick="consulterMatch_afficherRepartitionVainqueurPronostiqueMatchRegulier(' . $match . ');" class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsVictoireDomicile . '</label></span>';
			else
				echo '<span class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsVictoireDomicile . '</label></span>';

			if($nombrePronosticsMatchNul != '?')
				echo '<span onclick="consulterMatch_afficherRepartitionVainqueurPronostiqueMatchRegulier(' . $match . ');" class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsMatchNul . '</label></span>';
			else
				echo '<span class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsMatchNul . '</label></span>';
			
			if($nombrePronosticsVictoireVisiteur != '?')
				echo '<span onclick="consulterMatch_afficherRepartitionVainqueurPronostiqueMatchRegulier(' . $match . ');" class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsVictoireVisiteur . '</label></span>';
			else
				echo '<span class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsVictoireVisiteur . '</label></span>';
			
			if($nombreOublis > 0)
				echo '<span onclick="consulterMatch_afficherRepartitionVainqueurPronostiqueMatchRegulier(' . $match . ');" class="stats-match--resultat"><label class="texte-rouge curseur-main">' . $nombreOublis . '</label></span>';
		echo '</div>';
	}
	
	// Affichage du nombre de pronostics donnant l'équipe domicile vainqueur, le match nul, l'équipe visiteur vainqueur pour les matches de type 3
	function afficherVainqueurPronostique3($bdd, $match, $nombrePronosticsVictoireDomicile, $nombrePronosticsMatchNul, $nombrePronosticsVictoireVisiteur, $nombreOublis, $nombrePronosticsQualificationDomicile, $nombrePronosticsQualificationVisiteur) {
		if($nombreOublis > 0)			echo '<div title="Victoires | Nuls | Défaites | Oublis">';
		else							echo '<div title="Victoires | Nuls | Défaites">';
		
			if($nombrePronosticsVictoireDomicile != '?')
				echo '<span onclick="consulterMatch_afficherResultatMatchRetour(' . $match . ');" class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsVictoireDomicile . '</label></span>';
			else
				echo '<span class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsVictoireDomicile . '</label></span>';
			
			if($nombrePronosticsMatchNul != '?')
				echo '<span onclick="consulterMatch_afficherResultatMatchRetour(' . $match . ');" class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsMatchNul . '</label></span>';
			else
				echo '<span class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsMatchNul . '</label></span>';
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
		
			if( $nombrePronosticsVictoireDomicile != '?')
				echo '<span onclick="consulterMatch_afficherRepartitionVainqueurPronostiqueMatchCoupe(' . $match . ');" class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsVictoireDomicile . '</label></span>';
			else
				echo '<span class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsVictoireDomicile . '</label></span>';
			
			if($nombrePronosticsVictoireVisiteur != '?')
				echo '<span onclick="consulterMatch_afficherRepartitionVainqueurPronostiqueMatchCoupe(' . $match . ');" class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsVictoireVisiteur . '</label></span>';
			else
				echo '<span class="stats-match--resultat"><label class="curseur-main">' . $nombrePronosticsVictoireVisiteur . '</label></span>';
		
			if($nombreOublis > 0)
				echo '<span onclick="consulterMatch_afficherRepartitionVainqueurPronostiqueMatchCoupe(' . $match . ');" class="stats-match--resultat"><label class="texte-rouge curseur-main">' . $nombreOublis . '</label></span>';
		echo '</div>';
	}
	
	function afficherButeurs($domicileVisiteur, $buteurs) {
		if($domicileVisiteur == 1)
			echo '<label class="buteurs">' . str_replace(', ', '<br />', $buteurs[0]["Buteurs_Domicile"]) . '</label>';
		else
			echo '<label class="buteurs">' . str_replace(', ', '<br />', $buteurs[0]["Buteurs_Visiteur"]) . '</label>';

	}

	// Affichage de l'effectif de l'équipe
	function afficherEffectif($bdd, $unMatch, $domicileVisiteur) {
		if($domicileVisiteur == 1)
			$equipe = $unMatch["EquipesDomicile_Equipe"];
		else
			$equipe = $unMatch["EquipesVisiteur_Equipe"];
		
		// Effectif des équipes (uniquement les joueurs ayant participé)
		$ordreSQL =		'	SELECT			joueurs.Joueur, CONCAT(joueurs.Joueurs_NomFamille, \' \', IFNULL(joueurs.Joueurs_Prenom, \'\')) AS Joueurs_NomComplet' .
						'					,IFNULL(postes.Postes_NomCourt, \'&nbsp;\') AS Postes_NomCourt' .
						'	FROM			joueurs' .
						'	JOIN			joueurs_equipes' .
						'					ON		joueurs.Joueur = joueurs_equipes.Joueurs_Joueur' .
						'	JOIN			matches_participants' .
						'					ON		joueurs_equipes.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
						'	LEFT JOIN		postes' .
						'					ON		joueurs.Postes_Poste = postes.Poste' .
						'	WHERE			JoueursEquipes_Debut <= \'' . $unMatch["Matches_Date"] . '\'' .
						'					AND		(JoueursEquipes_Fin IS NULL OR JoueursEquipes_Fin > \'' . $unMatch["Matches_Date"] . '\')' .
						'					AND		joueurs_equipes.Equipes_Equipe = ' . $equipe .
						'					AND		matches_participants.Matches_Match = ' . $unMatch["Match"] .
						'	ORDER BY		joueurs.Postes_Poste DESC, Joueurs_NomComplet ASC';

		$req = $bdd->query($ordreSQL);
		$joueurs = $req->fetchAll();
		$nombreJoueurs = sizeof($joueurs);
		
		echo '<div class="' . ($domicileVisiteur == 1 ? 'gauche' : 'droite') . '">';
			echo '<table>';
				for($i = 0; $i < $nombreJoueurs; $i++) {
					echo '<tr>';
						echo '<td style="width: 4em;">' . $joueurs[$i]["Postes_NomCourt"] . '</td>';
						echo '<td>' . $joueurs[$i]["Joueurs_NomComplet"] . '</td>';
					echo '<tr>';
				}
			echo '</table>';
		echo '</div>';

		
	}
	
	// Liste des matches
	$ordreSQL =		'	SELECT DISTINCT			matches.Match, matches.Matches_Nom' .
					'							,matches.Matches_AvecProlongation, matches.Matches_MatchLie' .
					'							,matches.Matches_Date, HOUR(Matches_Date) AS Matches_Heure, MINUTE(Matches_Date) AS Matches_Minute' .
					'							,matches.Matches_MatchCS' .
					'							,EquipesDomicile.Equipe AS EquipesDomicile_Equipe, EquipesVisiteur.Equipe AS EquipesVisiteur_Equipe, EquipesDomicile.Equipes_Fanion AS EquipesDomicile_Fanion' .
					'							,EquipesDomicile.Equipes_Nom AS EquipesDomicile_Nom, EquipesVisiteur.Equipes_Nom AS EquipesVisiteur_Nom, EquipesVisiteur.Equipes_Fanion AS EquipesVisiteur_Fanion' .
					'							,matches.Matches_CoteEquipeDomicile, matches.Matches_CoteNul, matches.Matches_CoteEquipeVisiteur' .
					'							,matches.Matches_PointsQualificationEquipeDomicile, matches.Matches_PointsQualificationEquipeVisiteur' .
					'							,matches.Matches_ScoreEquipeDomicile, matches.Matches_ScoreEquipeVisiteur' .
					'							,matches.Matches_ScoreAPEquipeDomicile, matches.Matches_ScoreAPEquipeVisiteur' .
					'							,matches.Matches_Vainqueur' .
					'							,(SELECT Matches_ScoreEquipeDomicile FROM matches WHERE matches.Match = matches.Matches_MatchLie) AS MatchesLies_ScoreEquipeDomicile' .
					'							,(SELECT Matches_ScoreEquipeVisiteur FROM matches WHERE matches.Match = matches.Matches_MatchLie) AS MatchesLies_ScoreEquipeVisiteur' .
					'							,(SELECT Matches_ScoreAPEquipeDomicile FROM matches WHERE matches.Match = matches.Matches_MatchLie) AS MatchesLies_ScoreAPEquipeDomicile' .
					'							,(SELECT Matches_ScoreAPEquipeVisiteur FROM matches WHERE matches.Match = matches.Matches_MatchLie) AS MatchesLies_ScoreAPEquipeVisiteur' .
					'							,Matches_L1EuropeNom' .
					'							,Matches_Coefficient' .
					'							,fn_pronosticvisible(' . $match . ') AS Pronostic_Visible' .
					'	FROM					matches' .
					'	JOIN					journees' .
					'							ON	matches.Journees_Journee = journees.Journee' .
					'	LEFT JOIN				equipes AS EquipesDomicile' .
					'							ON		EquipesDomicile.Equipe = matches.Equipes_EquipeDomicile' .
					'	LEFT JOIN				equipes AS EquipesVisiteur' .
					'							ON		EquipesVisiteur.Equipe = matches.Equipes_EquipeVisiteur' .
					'	WHERE					matches.Match = ' . $match;

	$req = $bdd->query($ordreSQL);
	$matches = $req->fetchAll();

	$ordreSQL =		'	SELECT		(' .
					'					SELECT		GROUP_CONCAT(' .
					'												CASE' .
					'													WHEN	matches_buteurs.Buteurs_CSC = 0' .
					'													THEN	CONCAT(IFNULL(Joueurs_NomCourt, Joueurs_NomFamille), \' (\', fn_calculcotebuteur(Buteurs_Cote), \')\')' .
					'													ELSE	CONCAT(IFNULL(Joueurs_NomCourt, Joueurs_NomFamille), \' (CSC)\')' .
					'												END' .
					'												SEPARATOR \', \'' .
					'											)' .
					'					FROM		matches_buteurs' .
					'					JOIN		joueurs' .
					'								ON		matches_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'					WHERE		matches_buteurs.Matches_Match = matches.Match' .
					'								AND		(' .
					'											(' .
					'												matches_buteurs.Equipes_Equipe = equipesdomicile.Equipe' .
					'												AND		matches_buteurs.Buteurs_CSC = 0' .
					'											)' .
					'											OR' .
					'											(' .
					'												matches_buteurs.Equipes_Equipe = equipesvisiteur.Equipe' .
					'												AND		matches_buteurs.Buteurs_CSC = 1' .
					'											)' .
					'										)' .
					'				) AS Buteurs_Domicile' .
					'				,(' .
					'					SELECT		GROUP_CONCAT(' .
					'												CASE' .
					'													WHEN	matches_buteurs.Buteurs_CSC = 0' .
					'													THEN	CONCAT(IFNULL(Joueurs_NomCourt, Joueurs_NomFamille), \' (\', fn_calculcotebuteur(Buteurs_Cote), \')\')' .
					'													ELSE	CONCAT(IFNULL(Joueurs_NomCourt, Joueurs_NomFamille), \' (CSC)\')' .
					'												END' .
					'												SEPARATOR \', \'' .
					'											)' .
					'					FROM		matches_buteurs' .
					'					JOIN		joueurs' .
					'								ON		matches_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'					WHERE		matches_buteurs.Matches_Match = matches.Match' .
					'								AND		(' .
					'											(' .
					'												matches_buteurs.Equipes_Equipe = equipesvisiteur.Equipe' .
					'												AND		matches_buteurs.Buteurs_CSC = 0' .
					'											)' .
					'											OR' .
					'											(' .
					'												matches_buteurs.Equipes_Equipe = equipesdomicile.Equipe' .
					'												AND		matches_buteurs.Buteurs_CSC = 1' .
					'											)' .
					'										)' .
					'				) AS Buteurs_Visiteur' .
					'	FROM		journees' .
					'	JOIN		matches' .
					'				ON		journees.Journee = matches.Journees_Journee' .
					'	JOIN		equipes equipesdomicile' .
					'				ON		matches.Equipes_EquipeDomicile = equipesdomicile.Equipe' .
					'	JOIN		equipes equipesvisiteur' .
					'				ON		matches.Equipes_EquipeVisiteur = equipesvisiteur.Equipe' .
					'	WHERE		matches.Match = ' . $match;
	$req = $bdd->query($ordreSQL);
	$buteurs = $req->fetchAll();

	echo '<div id="divMatch" class="centre-page">';
		// Affichage des données du match

		foreach($matches as $unMatch) {
			$match = $unMatch["Match"];
			$matchLie = $unMatch["Matches_MatchLie"] != null ? $unMatch["Matches_MatchLie"] : 0;
			$matchCS = $unMatch["Matches_MatchCS"] != null ? $unMatch["Matches_MatchCS"] : 0;
			$matchAP = $unMatch["Matches_AvecProlongation"] != null ? $unMatch["Matches_AvecProlongation"] : 0;
			$matchRetour = $match > $matchLie ? $match: $matchLie;
			
			$classeMatch = '';
			if($matchCS == 1)									{	$typeMatch = 5; $classeMatch = 'matchCS';		}
			else if($matchAP == 0 && $matchLie == 0)			{	$typeMatch = 1; $classeMatch = 'matchLigue1';	}
			else if($matchAP == 0 && $matchLie != 0)			{	$typeMatch = 2; $classeMatch = 'matchAller';	}
			else if($matchAP == 1 && $matchLie != 0)			{	$typeMatch = 3; $classeMatch = 'matchRetour';	}
			else												{	$typeMatch = 4; $classeMatch = 'matchCoupe';	}
			
			if($matchLie < $match)
				$matchLie = $match;


			// L'affichage du pronostic du match sur la victoire, le nul ou la défaite ainsi que de l'équipe qualifiée dépend du type de match :
			// - match de type 1 et 2 : victoire - nul - défaite
			// - match de type 3 : victoire - nul - défaite jusqu'aux TAB + équipe qualifiée
			// - match de type 4 et 5 : victoire - défaite
			if($typeMatch == 1 || $typeMatch == 2) {
				$nombrePronosticsVictoireDomicile = $nombrePronosticsMatchNul = $nombrePronosticsVictoireVisiteur = $nombreOublis = '?';
				if($unMatch["Pronostic_Visible"] == 1)
					lireVainqueurPronostique1Et2($bdd, $match, $nombrePronosticsVictoireDomicile, $nombrePronosticsMatchNul, $nombrePronosticsVictoireVisiteur, $nombreOublis);
			}
			else if($typeMatch == 3) {
				$nombrePronosticsVictoireDomicile = $nombrePronosticsMatchNul = $nombrePronosticsVictoireVisiteur = $nombreOublis = '?';
				$nombrePronosticsQualificationDomicile = $nombrePronosticsQualificationVisiteur = '?';
				if($unMatch["Pronostic_Visible"] == 1)
					lireVainqueurPronostique3($bdd, $match, $nombrePronosticsVictoireDomicile, $nombrePronosticsMatchNul, $nombrePronosticsVictoireVisiteur, $nombreOublis, $nombrePronosticsQualificationDomicile, $nombrePronosticsQualificationVisiteur);
			}
			else if($typeMatch == 4 || $typeMatch = 5) {
				$nombrePronosticsVictoireDomicile = $nombrePronosticsVictoireVisiteur = $nombreOublis = '?';
				if($unMatch["Pronostic_Visible"] == 1)
					lireVainqueurPronostique4Et5($bdd, $match, $nombrePronosticsVictoireDomicile, $nombrePronosticsVictoireVisiteur, $nombreOublis);
			}

			echo '<div>';
				// Informations sur le match (nom du match, horaires, etc.)
				echo '<div class="matchLogistique">';
					afficherLogistique($unMatch);
				echo '</div>';
			
				// Informations sur l'équipe domicile
				echo '<div class="matchEquipe gauche">';
					afficherEquipe($unMatch, 1);
				echo '</div>';
				
				echo '<div class="matchZoneScore gauche">';
					echo '<label class="titre-score">Score</label>';
				
					// Score du match
					echo '<div class="matchScore gauche">';
						afficherScoreMatch($unMatch);
						
						if($typeMatch == 1 || $typeMatch == 2)
							afficherVainqueurPronostique1Et2($bdd, $match, $nombrePronosticsVictoireDomicile, $nombrePronosticsMatchNul, $nombrePronosticsVictoireVisiteur, $nombreOublis);
						else if($typeMatch == 3)
							afficherVainqueurPronostique3($bdd, $match, $nombrePronosticsVictoireDomicile, $nombrePronosticsMatchNul, $nombrePronosticsVictoireVisiteur, $nombreOublis, $nombrePronosticsQualificationDomicile, $nombrePronosticsQualificationVisiteur);
						else if($typeMatch == 4 || $typeMatch = 5)
							afficherVainqueurPronostique4Et5($bdd, $match, $nombrePronosticsVictoireDomicile, $nombrePronosticsVictoireVisiteur, $nombreOublis);

					echo '</div>';

				echo '</div>';
				
				// Informations sur l'équipe visiteur
				echo '<div class="matchEquipe gauche">';
					afficherEquipe($unMatch, 2);
				echo '</div>';

				// Cote de l'équipe domicile
				echo '<div class="matchCoteEquipe gauche">';
					afficherCote($unMatch, 1);
				echo '</div>';

				// Cote du match nul
				echo '<div class="matchCoteNul gauche">';
					afficherCote($unMatch, 0);
				echo '</div>';

				// Cote de l'équipe visiteur
				echo '<div class="matchCoteEquipe gauche">';
					afficherCote($unMatch, 2);
				echo '</div>';

				// Buteurs de l'équipe domicile
				echo '<div class="matchButeurs gauche">';
					afficherButeurs(1, $buteurs);
				echo '</div>';

				// Séparateur vertical score
				echo '<div class="' . $classeMatch . ' gauche">';
					if($unMatch["Matches_Coefficient"] == 2)
						echo '<br /><img src="images/canal.png" alt="" />';
				echo '</div>';

				// Buteurs de l'équipe visiteur
				echo '<div class="matchButeurs gauche">';
					afficherButeurs(2, $buteurs);
				echo '</div>';
				
				// Effectif complet des équipes
				echo '<div class="matchSeparateur colle-gauche">Feuille de match</div>';
				
				
				echo '<div class="matchEffectif colle-gauche aligne-gauche gauche">';
					afficherEffectif($bdd, $unMatch, 1);
				echo '</div>';
				
				echo '<div class="matchEffectifSeparateur gauche"></div>';
				
				echo '<div class="matchEffectif aligne-gauche gauche">';
					afficherEffectif($bdd, $unMatch, 2);
				echo '</div>';

				echo '<div class="colle-gauche"></div>';
			echo '</div>';
		}
	echo '</div>';

?>

<script>
	$(function() {
		$('.matchLigue1').each(	function() {	$(this).prepend("VS");	});
		$('.matchCS').each(		function() {	$(this).prepend("COMMUNITY SHIELD");	});
		$('.matchAller').each(	function() {	$(this).prepend("ALLER");	});
		$('.matchRetour').each(	function() {	$(this).prepend("RETOUR");	});
		$('.matchCoupe').each(	function() {	$(this).prepend("COUPE");	});
	});
	
</script>



