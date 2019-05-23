<?php
	include_once('commun.php');

	// Mise � jour d'un pronostic

	// Lecture des param�tres pass�s � la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;

	// Le param�tre "type" permet de savoir quelle modification est demand�e :
	// - score : le score de l'une des �quipes a �t� modifi� et dans ce cas, pour savoir quelle �quipe est concern�e, on regarde l'existence du param�tre "equipe"
	//   * D : �quipe domicile
	//   * V : �quipe visiteur
	// - scoreAP : le score AP de l'une des �quipes a �t� modifi� et dans ce cas, le param�tre equipe d�termine laquelle des deux
	// - vainqueur : l'utilisateur a choisi l'�quipe vainqueur du match
	$type = isset($_POST["type"]) ? $_POST["type"] : '';
	
	// On v�rifie avant tout qu'il n'est pas trop tard pour faire la modification
	$ordreSQL =		'	SELECT		fn_matchpronostiquable(matches.Match, ' . $pronostiqueur . ') AS Matches_Pronostiquable' .
						'	FROM		matches' .
						'	JOIN		journees' .
						'				ON		matches.Journees_Journee = journees.Journee' .
						'	LEFT JOIN	pronostics_carrefinal' .
						'				ON		matches.Match = pronostics_carrefinal.Matches_Match' .
						'	WHERE		matches.Match = ' . $match;

	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetch();
	$matchPronostiquable = $donnees["Matches_Pronostiquable"];
	$req->closeCursor();

	if($matchPronostiquable == 0) {
		echo 'DEPASSE';
		exit();
	}

	// V�rification de l'existence du pronostic et cr�ation de celui-ci si n�cessaire
	$ordreSQL =		'	INSERT INTO		pronostics	(	Pronostiqueurs_Pronostiqueur, Matches_Match)' .
					'					SELECT		*' .
					'					FROM		(SELECT ' . $_SESSION["pronostiqueur"] . ' AS Pronostiqueurs_Pronostiqueur, ' . $match . ' AS Matches_Match) AS tmp' .
					'					WHERE		NOT EXISTS	(SELECT * FROM pronostics WHERE Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] . ' AND Matches_Match = ' . $match . ')';

	$bdd->exec($ordreSQL);

	// Dans un premier temps, on �crit les donn�es dans la table
	switch($type) {
		case 'score':
			// Changement du score
			$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : '';
			$score = isset($_POST["score"]) ? $_POST["score"] : 0;
			
			// Cas particulier, si le pronostiqueur indique -1 en score (r�initialisation de son score), il est n�cessaire de nettoyer les scores AP
			if($score == -1) {
				$score = 'NULL';
				$ajoutOrdreSQL = ', Pronostics_ScoreAPEquipeDomicile = NULL, Pronostics_ScoreAPEquipeVisiteur = NULL, Pronostics_Vainqueur = NULL';
				
			}

			if($equipe == 'D') {
				// Mise � jour du score de l'�quipe domicile
				$ordreSQL =		'	UPDATE			pronostics' .
								'	SET				Pronostics_ScoreEquipeDomicile = ' . $score .
								'					,Pronostics_DateMAJ = NOW()' .
								$ajoutOrdreSQL .
								'	WHERE			Matches_Match = ' . $match .
								'					AND		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"];
			}
			else if($equipe == 'V') {
				// Mise � jour du score de l'�quipe visiteur
				$ordreSQL =		'	UPDATE			pronostics' .
								'	SET				Pronostics_ScoreEquipeVisiteur = ' . $score .
								'					,Pronostics_DateMAJ = NOW()' .
								$ajoutOrdreSQL .
								'	WHERE			Matches_Match = ' . $match .
								'					AND		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"];
			}
		break;

		case 'scoreAP':
			// Changement du score AP
			$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : '';
			$score = isset($_POST["score"]) ? $_POST["score"] : 0;
			
			if($score == -1)
				$score = 'NULL';

			if($equipe == 'D') {
				// Mise � jour du score de l'�quipe domicile
				$ordreSQL =		'	UPDATE			pronostics' .
								'	SET				Pronostics_ScoreAPEquipeDomicile = ' . $score .
								'					,Pronostics_DateMAJ = NOW()' .
								'	WHERE			Matches_Match = ' . $match .
								'					AND		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"];
			}
			else if($equipe == 'V') {
				// Mise � jour du score de l'�quipe visiteur
				$ordreSQL =		'	UPDATE			pronostics' .
								'	SET				Pronostics_ScoreAPEquipeVisiteur = ' . $score .
								'					,Pronostics_DateMAJ = NOW()' .
								'	WHERE			Matches_Match = ' . $match .
								'					AND		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"];
			}
		break;

		case 'vainqueur':
			// Changement de vainqueur
			$vainqueur = isset($_POST["vainqueur"]) ? $_POST["vainqueur"] : 0;
			$ordreSQL =		'	UPDATE		pronostics' .
							'	SET			Pronostics_Vainqueur = ' . $vainqueur .
							'				,Pronostics_DateMAJ = NOW()' .
							'	WHERE		Matches_Match = ' . $match .
							'				AND		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"];
		break;
	}
	$bdd->exec($ordreSQL);
	$texte = $ordreSQL;

	/*
		-- Ensuite, on d�termine quel est le type de match :
		-- - 1 : match r�gulier (pas de prolongation, pas de match li�)
		-- - 2 : match aller (pas de prolongation, match li�)
		-- - 3 : match retour (prolongation, match li�)
		-- - 4 : match de coupe (prolongation, pas de match li�)
		-- - 5 : match de la CS (pas de prolongation, pas de match li�) --> TAB en cas d'�galit� � la 90�me
	*/
	$ordreSQL =		'	SELECT		Matches_AvecProlongation, Matches_MatchLie, Matches_MatchCS' .
					'	FROM		matches' .
					'	WHERE		matches.Match = ' . $match;
	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetch();

	$matchCS = $donnees["Matches_MatchCS"] != null ? $donnees["Matches_MatchCS"] : 0;
	$matchAP = $donnees["Matches_AvecProlongation"] != null ? $donnees["Matches_AvecProlongation"] : 0;
	$matchLie = $donnees["Matches_MatchLie"] != null ? $donnees["Matches_MatchLie"] : 0;
	$matchAller = $match < $matchLie ? $match : $matchLie;
	$matchRetour = $match > $matchLie ? $match: $matchLie;

	if($matchCS == 1)
		$typeMatch = 5;
	else if($matchAP == 0 && $matchLie == 0)
		$typeMatch = 1;
	else if($matchAP == 0 && $matchLie != 0)
		$typeMatch = 2;
	else if($matchAP == 1 && $matchLie != 0)
		$typeMatch = 3;
	else
		$typeMatch = 4;

	$retour = '';

	// Maintenant, selon la modification effectu�e et le type de match...
	if($type == 'score') {
		switch($typeMatch) {
			case 2:
			case 3:
				// Lecture des pronostics du match aller
				$ordreSQL =		'	SELECT		IFNULL(Pronostics_ScoreEquipeDomicile, -1) AS Pronostics_ScoreEquipeDomicile, IFNULL(Pronostics_ScoreEquipeVisiteur, -1) AS Pronostics_ScoreEquipeVisiteur' .
								'	FROM		pronostics' .
								'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
								'				AND		Matches_Match = ' . $matchAller;
				$req = $bdd->query($ordreSQL);
				$donnees = $req->fetch();
				$aller_ScoreEquipeDomicile = $donnees["Pronostics_ScoreEquipeDomicile"];
				$aller_ScoreEquipeVisiteur = $donnees["Pronostics_ScoreEquipeVisiteur"];

				// Lecture des pronostics du match retour
				$ordreSQL =		'	SELECT		IFNULL(Pronostics_ScoreEquipeDomicile, -1) AS Pronostics_ScoreEquipeDomicile, IFNULL(Pronostics_ScoreEquipeVisiteur, -1) AS Pronostics_ScoreEquipeVisiteur' .
								'	FROM		pronostics' .
								'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
								'				AND		Matches_Match = ' . $matchRetour;
				$req = $bdd->query($ordreSQL);
				$donnees = $req->fetch();

				$retour_ScoreEquipeDomicile = $donnees["Pronostics_ScoreEquipeDomicile"];
				$retour_ScoreEquipeVisiteur = $donnees["Pronostics_ScoreEquipeVisiteur"];

				if($aller_ScoreEquipeDomicile != -1 && $retour_ScoreEquipeDomicile != -1 && $aller_ScoreEquipeVisiteur != -1 && $retour_ScoreEquipeVisiteur != -1) {
					if($aller_ScoreEquipeDomicile == $retour_ScoreEquipeDomicile && $aller_ScoreEquipeVisiteur == $retour_ScoreEquipeVisiteur) {
						$retour = 'PROLONGATION|TAB|score 2-3';

						// Copie des scores du match retour vers les scores de prolongation
						$ordreSQL =		'	UPDATE		pronostics' .
										'	SET			Pronostics_ScoreAPEquipeDomicile = Pronostics_ScoreEquipeDomicile' .
										'				,Pronostics_ScoreAPEquipeVisiteur = Pronostics_ScoreEquipeVisiteur' .
										'				,Pronostics_DateMAJ = NOW()' .
										'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
										'				AND		Matches_Match = ' . $matchRetour;
										
						$bdd->exec($ordreSQL);
					}
					else {
						// S'il n'y a pas prolongation et TAB, on efface les pronostics AP et le nom du vainqueur car ils ont pu avoir �t� �crits � un moment
						$ordreSQL =		'	UPDATE			pronostics' .
										'	SET				Pronostics_Vainqueur = NULL' .
										'					,Pronostics_ScoreAPEquipeDomicile = NULL' .
										'					,Pronostics_ScoreAPEquipeVisiteur = NULL' .
										'	WHERE			Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
										'					AND		Matches_Match = ' . $matchRetour;
						$bdd->exec($ordreSQL);
						$retour = $ordreSQL;
					}
				}
			break;
			
			case 4:
				// Lecture des pronostics AP du match
				$ordreSQL =		'	SELECT		IFNULL(Pronostics_ScoreEquipeDomicile, -1) AS Pronostics_ScoreEquipeDomicile, IFNULL(Pronostics_ScoreEquipeVisiteur, -1) AS Pronostics_ScoreEquipeVisiteur' .
								'	FROM		pronostics' .
								'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
								'				AND		Matches_Match = ' . $match;
				$req = $bdd->query($ordreSQL);
				$donnees = $req->fetch();

				$match_ScoreEquipeDomicile = $donnees["Pronostics_ScoreEquipeDomicile"];
				$match_ScoreEquipeVisiteur = $donnees["Pronostics_ScoreEquipeVisiteur"];

				if($match_ScoreEquipeDomicile != -1 && $match_ScoreEquipeVisiteur != -1) {
					if($match_ScoreEquipeDomicile == $match_ScoreEquipeVisiteur) {
						$retour = 'PROLONGATION|TAB|score 4';

						// Copie des scores du match retour vers les scores de prolongation
						$ordreSQL =		'	UPDATE		pronostics' .
										'	SET			Pronostics_ScoreAPEquipeDomicile = Pronostics_ScoreEquipeDomicile' .
										'				,Pronostics_ScoreAPEquipeVisiteur = Pronostics_ScoreEquipeVisiteur' .
										'				,Pronostics_DateMAJ = NOW()' .
										'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
										'				AND		Matches_Match = ' . $match;
						$bdd->exec($ordreSQL);
					}
					else {
						// S'il n'y a pas prolongation et TAB, on efface les pronostics AP et le nom du vainqueur car ils ont pu avoir �t� �crits � un moment
						$ordreSQL =		'	UPDATE			pronostics' .
										'	SET				Pronostics_Vainqueur = NULL' .
										'					,Pronostics_ScoreAPEquipeDomicile = NULL' .
										'					,Pronostics_ScoreAPEquipeVisiteur = NULL' .
										'					,Pronostics_DateMAJ = NOW()' .
										'	WHERE			Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
										'					AND		Matches_Match = ' . $match;
										
						$bdd->exec($ordreSQL);
						$retour = $ordreSQL;
					}
				}
			break;
			
			case 5:
				// Lecture des pronostics du match
				$ordreSQL =		'	SELECT		IFNULL(Pronostics_ScoreEquipeDomicile, -1) AS Pronostics_ScoreEquipeDomicile,' .
								'				IFNULL(Pronostics_ScoreEquipeVisiteur, -1) AS Pronostics_ScoreEquipeVisiteur' .
								'	FROM		pronostics' .
								'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
								'				AND		Matches_Match = ' . $match;
				$req = $bdd->query($ordreSQL);
				$donnees = $req->fetch();
				$match_ScoreEquipeDomicile = $donnees["Pronostics_ScoreEquipeDomicile"];
				$match_ScoreEquipeVisiteur = $donnees["Pronostics_ScoreEquipeVisiteur"];

				if($match_ScoreEquipeDomicile == $match_ScoreEquipeVisiteur)
					$retour = 'TAB';
				else {
					// S'il n'y a pas TAB, on efface le nom du vainqueur car il a pu avoir �t� �crit � un moment
					$ordreSQL =		'	UPDATE			pronostics' .
									'	SET				Pronostics_Vainqueur = NULL' .
									'					,Pronostics_DateMAJ = NOW()' .
									'	WHERE			Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
									'					AND		Matches_Match = ' . $match;
					$bdd->exec($ordreSQL);
					$retour = $ordreSQL;
				}
			break;
		
		}
	}
	else if($type == 'scoreAP') {
		// Lecture des scores AP du match retour (type de match 3)
		if($typeMatch == 3) {
			$ordreSQL =		'	SELECT		IFNULL(Pronostics_ScoreEquipeDomicile, -1) AS Pronostics_ScoreEquipeDomicile,' .
							'				IFNULL(Pronostics_ScoreEquipeVisiteur, -1) AS Pronostics_ScoreEquipeVisiteur,' .
							'				IFNULL(Pronostics_ScoreAPEquipeDomicile, IFNULL(Pronostics_ScoreEquipeDomicile, -1)) AS Pronostics_ScoreAPEquipeDomicile,' .
							'				IFNULL(Pronostics_ScoreAPEquipeVisiteur, IFNULL(Pronostics_ScoreEquipeVisiteur, -1)) AS Pronostics_ScoreAPEquipeVisiteur' .
							'	FROM		pronostics' .
							'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
							'				AND		Matches_Match = ' . $matchRetour;
			$req = $bdd->query($ordreSQL);
			$donnees = $req->fetch();

			$retour_ScoreEquipeDomicile = $donnees["Pronostics_ScoreEquipeDomicile"];
			$retour_ScoreEquipeVisiteur = $donnees["Pronostics_ScoreEquipeVisiteur"];
			$retour_ScoreAPEquipeDomicile = $donnees["Pronostics_ScoreAPEquipeDomicile"];
			$retour_ScoreAPEquipeVisiteur = $donnees["Pronostics_ScoreAPEquipeVisiteur"];

			if($retour_ScoreEquipeDomicile != -1 && $retour_ScoreAPEquipeDomicile != -1 && $retour_ScoreEquipeVisiteur != -1 && $retour_ScoreAPEquipeVisiteur != -1) {
				if($retour_ScoreEquipeDomicile == $retour_ScoreAPEquipeDomicile && $retour_ScoreEquipeVisiteur == $retour_ScoreAPEquipeVisiteur) {
					$retour = 'PROLONGATION|TAB|score AP 3';
				}
				else {
					// S'il n'y a pas TAB, on efface le nom du vainqueur car il a pu avoir �t� �crit � un moment
					$ordreSQL =		'	UPDATE			pronostics' .
									'	SET				Pronostics_Vainqueur = NULL' .
									'					,Pronostics_DateMAJ = NOW()' .
									'	WHERE			Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
									'					AND		Matches_Match = ' . $matchRetour;
					$bdd->exec($ordreSQL);
					$retour = $ordreSQL;
				}
			}
		}
		else if($typeMatch == 4) {
			// Lecture des scores AP du match (type de match 4)
			$ordreSQL =		'	SELECT		IFNULL(Pronostics_ScoreAPEquipeDomicile, -1) AS Pronostics_ScoreAPEquipeDomicile, IFNULL(Pronostics_ScoreAPEquipeVisiteur, -1) AS Pronostics_ScoreAPEquipeVisiteur' .
							'	FROM		pronostics' .
							'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
							'				AND		Matches_Match = ' . $match;
			$req = $bdd->query($ordreSQL);
			$donnees = $req->fetch();

			$match_ScoreAPEquipeDomicile = $donnees["Pronostics_ScoreAPEquipeDomicile"];
			$match_ScoreAPEquipeVisiteur = $donnees["Pronostics_ScoreAPEquipeVisiteur"];

			if($match_ScoreAPEquipeDomicile != -1 && $match_ScoreAPEquipeVisiteur != -1) {
				if($match_ScoreAPEquipeDomicile == $match_ScoreAPEquipeVisiteur) {
					$retour = 'PROLONGATION|TAB|scoreAP 4';
				}
				else {
					// S'il n'y a pas TAB, on efface le nom du vainqueur car il a pu avoir �t� �crit � un moment
					$ordreSQL =		'	UPDATE			pronostics' .
									'	SET				Pronostics_Vainqueur = NULL' .
									'					,Pronostics_DateMAJ = NOW()' .
									'	WHERE			Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
									'					AND		Matches_Match = ' . $match;
					$bdd->exec($ordreSQL);
					$retour = $ordreSQL;
				}
			}
		}
	}
	echo $retour;
?>
