<?php
	include_once('commun_administrateur.php');


	// Pour des raisons inconnues, la fonction utf8_decode a des comportements différents en local et sur OVH
	// Une fonction my_utf8_decode a donc été créée pour tester si l'on est en local ou non et faire le comportement adéquat
	function my_utf8_decode($chaine) {
		// $local = isset($_SESSION["local"]) ? $_SESSION["local"] : 0;
		// if($local == 1)
		// 	return $chaine;

		// //return utf8_decode($chaine);
		return $chaine;
	}

	// Ajout d'un événement dans la table des événements d'un match (mouvement de joueur, but, etc.)
	// Si le paramètre unique vaut 1, alors l'ajout ne se fait qu'à partir du moment où l'événément n'existe pas encore
	function ajouterEvenement($bdd, $match, $joueur, $codeEvenement, $datation, $unique) {
		if($unique == 1) {
			$ordreSQL =		'	SELECT		COUNT(*) AS Nombre' .
							'	FROM		matches_evenements' .
							'	WHERE		Matches_Match = ' . $match .
							'				AND		Joueurs_Joueur = ' . $joueur .
							'				AND		MatchesEvenements_Evenement = ' . $codeEvenement .
							'				AND		MatchesEvenements_Datation = ' . $datation;
			$req = $bdd->query($ordreSQL);
			$evenement = $req->fetchAll();

			if($evenement[0]["Nombre"] == 0) {
				// Cas particulier pour l'événement de fin de match : on doit mettre la date à laquelle on détecte cet événement
				// La surveillance ne s'arrêtera que si un certain laps de temps s'est écoulé, évitant ainsi qu'une mise à jour sur le site externe
				// ne soit pas vue (cela arrive assez rarement)
				if($codeEvenement == 9)
					$ordreSQL =		'	INSERT INTO matches_evenements(Matches_Match, Joueurs_Joueur, MatchesEvenements_Evenement, MatchesEvenements_Datation, MatchesEvenements_DateEvenement)' .
									'	VALUES(' . $match . ', ' . $joueur . ', ' . $codeEvenement . ', ' . $datation . ', NOW())';
				else
					$ordreSQL =		'	INSERT INTO matches_evenements(Matches_Match, Joueurs_Joueur, MatchesEvenements_Evenement, MatchesEvenements_Datation)' .
									'	VALUES(' . $match . ', ' . $joueur . ', ' . $codeEvenement . ', ' . $datation . ')';
				
				$bdd->exec($ordreSQL);
				return 1;
			}
		}
		else {
			$ordreSQL =		'	INSERT INTO matches_evenements(Matches_Match, Joueurs_Joueur, MatchesEvenements_Evenement, MatchesEvenements_Datation)' .
							'	VALUES(' . $match . ', ' . $joueur . ', ' . $codeEvenement . ', ' . $datation . ')';
			$bdd->exec($ordreSQL);
			return 1;
		}

		return 0;
	}

	// Initialisation d'un match
	function initialiserMatch($bdd, $match) {
		$ordreSQL =		'	UPDATE		matches' .
						'	SET			Matches_ScoreEquipeDomicile = 0' .
						'				,Matches_ScoreEquipeVisiteur = 0' .
						'				,Matches_Direct = 1' .
						'	WHERE		matches.Match = ' . $match;
		$bdd->exec($ordreSQL);

		mettreAJourJournee($bdd, $match);
	}

	// Ajout d'un participant dans la table des participants de match
	function ajouterParticipant($bdd, $match, $joueur, $equipe) {
		$ordreSQL =		'	INSERT IGNORE INTO matches_participants(Matches_Match, Joueurs_Joueur, Equipes_Equipe)' .
						'	VALUES(' . $match . ', ' . $joueur . ', ' . $equipe . ')';
		$bdd->exec($ordreSQL);
	}

	// Fonction de remplacement de certains caractères d'un nom passé en paramètre
	function remplacerCaracteres($chaine) {
		$tableauCaracteres = array(
			'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'á'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ă'=>'a', 'ą'=>'a', 'ə'=>'a',
			'þ'=>'b', 'Þ'=>'B', 'đ'=>'d',
			'Ç'=>'C', 'Ć'=>'C', 'Č'=>'C', 'ć'=>'c', 'č'=>'c',
			'Đ'=>'D', 'Ď'=>'D',
			'É'=>'E', 'ę'=>'e', 'ě'=>'e',
			'ğ'=>'g',
			'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'İ'=>'I', 'Ï'=>'I', 'ı'=>'i', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',
			'Ł'=>'L', 'ł'=>'l',
			'Ñ'=>'N', 'ñ'=>'n', 'ń'=>'n', 'ň'=>'n',
			'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'ð'=>'o', 'ò'=>'o', 'ó'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ō'=>'o', 'ô'=>'o',
			'Ř'=>'R', 'ř'=>'r',
			'Š'=>'S', 'Ş'=>'S', 'š'=>'s', 'ş'=>'s', 'ș'=>'s',
			'ţ'=>'t', 'ț'=>'t', 'Ț'=>'T',
			'ß'=>'ss',
			'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ů'=>'u',
			'Ý'=>'Y', 'ý'=>'y', 'ÿ'=>'y',
			'Ž'=>'Z', 'ž'=>'z', 'ź'=>'z'
		);
		return strtr($chaine, $tableauCaracteres);
	}

	// Recherche d'un joueur dans une équipe
	// Le paramètre origine permet de savoir quel nom de correspondance utiliser (NomCorrespondance, NomCorrespondanceComplementaire, NomCorrespondanceCote)
	function rechercherJoueur($bdd, $joueurNomComplet, $equipe, $date, $origine) {
		$champ = '';
		switch($origine) {
			case 1: $champ = 'Joueurs_NomCorrespondance'; break;
			case 2: $champ = 'Joueurs_NomCorrespondanceComplementaire'; break;
			case 3: $champ = 'Joueurs_NomCorrespondanceCote'; break;
		}

		if($champ == '')
			return -2;

		$joueurNomModifie = remplacerCaracteres($joueurNomComplet);
		$ordreSQL =		'	SELECT			Joueur' .
						'	FROM			joueurs' .
						'	JOIN			joueurs_equipes' .
						'					ON		joueurs.Joueur = joueurs_equipes.Joueurs_Joueur' .
						'	WHERE			joueurs_equipes.Equipes_Equipe = ' . $equipe .
						'					AND		(	CASE' .
						'											WHEN	joueurs.' . $champ . ' IS NOT NULL' .
						'											THEN	joueurs.' . $champ .
						'											WHEN	joueurs.Joueurs_Prenom IS NOT NULL AND joueurs.Joueurs_Prenom <> \'\'' .
						'											THEN	CONCAT(joueurs.Joueurs_Prenom, \' \', joueurs.Joueurs_NomFamille)' .
						'											ELSE	joueurs.Joueurs_NomFamille' .
						'										END = ' . $bdd->quote($joueurNomModifie) .
						'										OR' .
						'										CASE' .
						'											WHEN	joueurs.' . $champ . ' IS NOT NULL' .
						'											THEN	joueurs.' . $champ .
						'											WHEN	joueurs.Joueurs_Prenom IS NOT NULL AND joueurs.Joueurs_Prenom <> \'\'' .
						'											THEN	CONCAT(joueurs.Joueurs_NomFamille, \' \', joueurs.Joueurs_Prenom)' .
						'											ELSE	joueurs.Joueurs_NomFamille' .
						'										END = ' . $bdd->quote($joueurNomModifie) .
						'									)' .
						'					AND		JoueursEquipes_Debut <= \'' . $date . '\'' .
						'					AND		(JoueursEquipes_Fin IS NULL OR JoueursEquipes_Fin > \'' . $date . '\')' .
						'	LIMIT			1';
		try {
			$req = $bdd->query($ordreSQL);
		} catch(Exception $e) {
			echo $e->getMessage() . PHP_EOL;
			return 0;
		}
		$joueurs = $req->fetchAll();

		if(sizeof($joueurs) == 1)
			return $joueurs[0]["Joueur"];
		else if(sizeof($joueurs) == 0)
			return -1;

		return 0;
	}

	// Recherche d'un joueur dans une équipe à partir de la première lettre de son prénom et de son nom de famille
	function rechercherJoueurInitialePrenom($bdd, $joueurNomComplet, $equipe, $date, $origine) {
		$champ = '';
		switch($origine) {
			case 1: $champ = 'Joueurs_NomCorrespondance'; break;
			case 2: $champ = 'Joueurs_NomCorrespondanceComplementaire'; break;
			case 3: $champ = 'Joueurs_NomCorrespondanceCote'; break;
		}

		if($champ == '')
			return -2;

		$joueurNomModifie = remplacerCaracteres($joueurNomComplet);

		$ordreSQL =		'	SELECT		Joueur' .
									'	FROM			joueurs' .
									'	JOIN			joueurs_equipes' .
									'						ON		joueurs.Joueur = joueurs_equipes.Joueurs_Joueur' .
									'	WHERE			joueurs_equipes.Equipes_Equipe = ' . $equipe .
									'						AND		CASE' .
									'										WHEN	joueurs.' . $champ . ' IS NOT NULL' .
									'										THEN	joueurs.' . $champ .
									'										WHEN	joueurs.Joueurs_Prenom IS NOT NULL AND joueurs.Joueurs_Prenom <> \'\'' .
									'										THEN	CONCAT(LEFT(joueurs.Joueurs_Prenom, 1), \'. \', joueurs.Joueurs_NomFamille)' .
									'										ELSE	joueurs.Joueurs_NomFamille' .
									'									END = ' . $bdd->quote($joueurNomModifie) .
									'						AND		JoueursEquipes_Debut <= \'' . $date . '\'' .
									'						AND		(JoueursEquipes_Fin IS NULL OR JoueursEquipes_Fin > \'' . $date . '\')';
		$req = $bdd->query($ordreSQL);
		$joueurs = $req->fetchAll();

		if(sizeof($joueurs) == 1)
			return $joueurs[0]["Joueur"];
		else if(sizeof($joueurs) == 0)
			return -1;

		return 0;
	}

	// Recherche d'un joueur dans une équipe à partir de la première lettre de son prénom et de son nom de famille
	function rechercherJoueurInitialePrenomSansPoint($bdd, $joueurNomComplet, $equipe, $date, $origine) {
		$champ = '';
		switch($origine) {
			case 1: $champ = 'Joueurs_NomCorrespondance'; break;
			case 2: $champ = 'Joueurs_NomCorrespondanceComplementaire'; break;
			case 3: $champ = 'Joueurs_NomCorrespondanceCote'; break;
		}

		if($champ == '')
			return -2;

		$joueurNomModifie = remplacerCaracteres($joueurNomComplet);
		$ordreSQL =		'	SELECT		Joueur' .
									'	FROM		joueurs' .
									'	JOIN		joueurs_equipes' .
									'				ON		joueurs.Joueur = joueurs_equipes.Joueurs_Joueur' .
									'	WHERE		joueurs_equipes.Equipes_Equipe = ' . $equipe .
									'				AND		CASE' .
									'							WHEN	joueurs.' . $champ . ' IS NOT NULL' .
									'							THEN	joueurs.' . $champ .
									'							WHEN	joueurs.Joueurs_Prenom IS NOT NULL AND joueurs.Joueurs_Prenom <> \'\'' .
									'							THEN	CONCAT(LEFT(joueurs.Joueurs_Prenom, 1), \' \', joueurs.Joueurs_NomFamille)' .
									'							ELSE	joueurs.Joueurs_NomFamille' .
									'						END = ' . $bdd->quote($joueurNomModifie) .
									'				AND		JoueursEquipes_Debut <= \'' . $date . '\'' .
									'				AND		(JoueursEquipes_Fin IS NULL OR JoueursEquipes_Fin > \'' . $date . '\')';
		$req = $bdd->query($ordreSQL);
		$joueurs = $req->fetchAll();

		if(sizeof($joueurs) == 1)
			return $joueurs[0]["Joueur"];
		else if(sizeof($joueurs) == 0)
			return -1;

		return 0;
	}

	// Recherche d'un joueur dans une équipe à partir du nom de famille et de la première lettre de son prénom
	// Utilisée par Scores Pro uniquement, donc désactivée
	function rechercherJoueurInitialePrenomInverse($bdd, $joueurNomComplet, $equipe, $date) {
		$joueurNomModifie = remplacerCaracteres($joueurNomComplet);

		// Attention au cas spécifique des prénoms composés
		$ordreSQL =		'	SELECT		Joueur' .
						'	FROM		joueurs' .
						'	JOIN		joueurs_equipes' .
						'				ON		joueurs.Joueur = joueurs_equipes.Joueurs_Joueur' .
						'	WHERE		joueurs_equipes.Equipes_Equipe = ' . $equipe .
						'				AND		CASE' .
						'							WHEN	TRIM(IFNULL(joueurs.Joueurs_Prenom, \'\')) <> \'\'' .
						'							THEN	CASE' .
						'										WHEN	LOCATE(\'-\', joueurs.Joueurs_Prenom) = 0' .
						'										THEN	CONCAT(REPLACE(joueurs.Joueurs_NomFamille, \'\\\'\', \' \'), \' \', LEFT(joueurs.Joueurs_Prenom, 1))' .
						'										ELSE	CONCAT(REPLACE(joueurs.Joueurs_NomFamille, \'\\\'\', \' \'), \' \', LEFT(joueurs.Joueurs_Prenom, 1), \'-\', SUBSTR(joueurs.Joueurs_Prenom, LOCATE(\'-\', joueurs.Joueurs_Prenom) + 1, 1))' .
						'									END' .
						'							ELSE	joueurs.Joueurs_NomFamille' .
						'						END = ' . $bdd->quote($joueurNomModifie) .
						'				AND		JoueursEquipes_Debut <= \'' . $date . '\'' .
						'				AND		(JoueursEquipes_Fin IS NULL OR JoueursEquipes_Fin > \'' . $date . '\')';

		$req = $bdd->query($ordreSQL);
		$joueurs = $req->fetchAll();

		if(sizeof($joueurs) == 1)
			return $joueurs[0]["Joueur"];
		else if(sizeof($joueurs) == 0)
			return -1;

		return 0;
	}

	// Ajout d'un joueur dans une équipe pour un match
	// Le paramètre origine permet de savoir quel nom de correspondance utiliser (NomCorrespondance, NomCorrespondanceComplementaire, NomCorrespondanceCote)
	function ajouterJoueur($bdd, $joueurNomComplet, $equipe, $match, $date, $origine) {
		$joueurNomModifie = remplacerCaracteres($joueurNomComplet);

		$joueur = rechercherJoueur($bdd, $joueurNomModifie, $equipe, $date, $origine);
		if($joueur <= 0)
			$joueur = rechercherJoueurInitialePrenom($bdd, $joueurNomModifie, $equipe, $date, 1);

		if($joueur > 0) {
			$ordreSQL =		'	REPLACE INTO matches_participants(Matches_Match, Joueurs_Joueur, Equipes_Equipe)' .
							'	SELECT		' . $match . ', ' . $joueur . ', ' . $equipe;
			$bdd->exec($ordreSQL);
		}

		return $joueur;
	}

	// Effacement des événements de but de la table des événements
	function effacerEvenementsScore($bdd, $match) {
		$ordreSQL =		'	DELETE FROM matches_evenements' .
						'	WHERE		Matches_Match = ' . $match .
						'				AND		MatchesEvenements_Evenement >= 31' .
						'				AND		MatchesEvenements_Evenement <= 34';
		$bdd->exec($ordreSQL);
	}

	// Synchronisation des événements de but de la table des événements et de la table des buts du match
	function synchroniserEvenementsScore($bdd, $match, $prolongation) {
		$ordreSQL =		'		SELECT		IFNULL(SUM(Joueurs_Joueur), 0) AS Signature' .
						'		FROM		matches_evenements' .
						'		WHERE		Matches_Match = ' . $match .
						'					AND		MatchesEvenements_Evenement >= 31' .
						'					AND		MatchesEvenements_Evenement <= 34';
		$req = $bdd->query($ordreSQL);
		$donnees = $req->fetchAll();
		$signatureEvenements = $donnees[0]["Signature"];

		$ordreSQL =		'		SELECT		IFNULL(SUM(Joueurs_Joueur), 0) AS Signature' .
						'		FROM		matches_buteurs' .
						'		WHERE		Matches_Match = ' . $match;
		$req = $bdd->query($ordreSQL);
		$donnees = $req->fetchAll();
		$signatureButeurs = $donnees[0]["Signature"];

		if($signatureEvenements != $signatureButeurs) {
			$ordreSQL =		'	CALL	sp_synchronisationevenementsbut(' . $match . ', ' . $prolongation . ')';
			$bdd->exec($ordreSQL);

			return 1;
		}
		return 0;
	}

	// Lancement du calcul d'une journée
	function lancerCalcul($bdd, $journee) {
		$ordreSQL = 'CALL sp_calcultouslesscores(' . $journee . ')';
		$bdd->exec($ordreSQL);
	}

	// Ajout d'un buteur
	function ajouterButeur($bdd, $match, $joueur, $equipe, $csc) {
		$ordreSQL =		'	INSERT INTO matches_buteurs(Matches_Match, Joueurs_Joueur, Equipes_Equipe, Buteurs_Cote, Buteurs_CSC)' .
						'	SELECT		' . $match . ', ' . $joueur . ', ' . $equipe .
						'				,(SELECT IFNULL(JoueursCotes_Cote, 1) FROM joueurs_cotes WHERE Matches_Match = ' . $match . ' AND Joueurs_Joueur = ' . $joueur . '), ' . $csc;
		$bdd->exec($ordreSQL);
	}

	// Ecriture du vainqueur des TAB
	function ecrireVainqueurTAB($bdd, $match, $vainqueur) {
		$ordreSQL =		'	UPDATE		matches' .
						'	SET			Matches_Vainqueur = ' . $vainqueur .
						'	WHERE		matches.Match = ' . $match;
		$bdd->exec($ordreSQL);
	}

	/**
	 * Suppression du match de la liste des matches en direct et mise à jour du statut de match en direct
	 * La suppression de la surveillance n'intervient que 30 minutes après la fin de la détection de la fin du match
	 * Cela permet de détecter des mises à jour effeectuées sur le site externe
	 */
	function supprimerMatchDuDirect($bdd, $match) {
		$ordreSQL =		'	DELETE		matches_direct' .
						'	FROM		matches_direct' .
						'	JOIN		matches_evenements' .
						'				ON		matches_direct.Matches_Match = matches_evenements.Matches_Match' .
						'	WHERE		matches_evenements.MatchesEvenements_Evenement = 9' .
						'				AND		NOW() > DATE_ADD(matches_evenements.MatchesEvenements_DateEvenement, INTERVAL 30 MINUTE)';
		$bdd->exec($ordreSQL);

		$ordreSQL =		'	UPDATE		matches' .
						'	SET			Matches_Direct = 0' .
						'	WHERE		matches.Match = ' . $match;
		$bdd->exec($ordreSQL);

		mettreAJourJournee($bdd, $match);
	}

	/**
	 * Ajout d'un message d'erreur
	 * L'ajout du message d'erreur ne doit se faire qu'une seule fois
	 * Si le message n'existait pas auparavant, il est ajouté et la fonction retourne la valeur 1
	 * Si le message existait déjà, il n'est pas ajouté et la fonction retourne la valeur 0
	 */
	function ajouterErreur($bdd, $match, $message, $datation) {
		$ordreSQL =		'	SELECT		COUNT(*) AS Nombre' .
						'	FROM		matches_erreurs' .
						'	WHERE		Matches_Match = ' . $match .
						'				AND		MatchesErreurs_Message = ' . $bdd->quote($message) .
						'				AND		MatchesErreurs_Datation = ' . $datation;
		$req = $bdd->query($ordreSQL);
		$erreur = $req->fetchAll();

		if($erreur[0]["Nombre"] == 0) {
			$ordreSQL =		'	INSERT INTO matches_erreurs(Matches_Match, MatchesErreurs_Message, MatchesErreurs_Datation)' .
							'	VALUES(' . $match . ', ' . $bdd->quote($message) . ', ' . $datation . ')';
			$bdd->exec($ordreSQL);
			return 1;
		}
		return 0;
	}

	// Finalisation de la composition d'équipes d'un match
	function finaliserCompositionEquipes($bdd, $match) {
		$ordreSQL =		'	UPDATE		matches' .
						'	SET			Matches_CompositionLue = 1' .
						'	WHERE		matches.Match = ' . $match;
		$bdd->exec($ordreSQL);
	}

	// Mise à jour de la journée pour indiquer qu'une modification a eu lieu
	function mettreAJourJournee($bdd, $match) {
		$ordreSQL =		'	UPDATE		journees' .
						'	JOIN		matches' .
						'				ON		journees.Journee = matches.Journees_Journee' .
						'	SET			Journees_DateEvenement = NOW()' .
						'	WHERE		matches.Match = ' . $match;
		$bdd->exec($ordreSQL);
	}

	// Inscription des équipes d'un match
	function inscrireEquipesDansMatch($bdd, $match, $dateMatch, $equipe1, $equipe2) {
		$ordreSQL =		'	UPDATE		matches' .
						'	SET			matches.Equipes_EquipeDomicile = ' . $equipe1 .
						'				,matches.Equipes_EquipeVisiteur = ' . $equipe2 .
						'				,matches.Matches_Date = \'' . $dateMatch->format('Y-m-d H:i:s') . '\'' .
						'	WHERE		matches.Match = ' . $match;
		$bdd->exec($ordreSQL);
	}

	// Recherche d'une équipe depuis son nom de correspondance complémentaire
	function rechercherEquipeDepuisNomCorrespondanceComplementaire($bdd, $nomCorrespondanceComplemtaire) {
		$ordreSQL =		'	SELECT	equipes.Equipe' .
						'	FROM	equipes' .
						'	WHERE	equipes.Equipes_NomCorrespondanceComplementaire = ' . $bdd->quote(remplacerCaracteres(my_utf8_decode($nomCorrespondanceComplemtaire)));
		$req = $bdd->query($ordreSQL);
		$equipes = $req->fetchAll();
		if(sizeof($equipes) == 1) {
			return $equipes[0]["Equipe"];
		} else {
			return 0;
		}
	}

?>
