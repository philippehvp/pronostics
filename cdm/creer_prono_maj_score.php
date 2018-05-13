<?php
	include('commun.php');

	// Mise à jour d'un score de pronostic
	$pronostiqueur = $_SESSION["pronostiqueur"];

	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;

	// Le paramètre "type" permet de savoir quelle modification est demandée :
	// - score : le score de l'une des équipes a été modifié et dans ce cas, pour savoir quelle équipe est concernée, on regarde l'existence du paramètre "equipeAB"
	//   * 1 : équipe A
	//   * 2 : équipe B
	// - scoreAP : le score AP de l'une des équipes a été modifié et dans ce cas, le paramètre "equipeAB" détermine laquelle des deux
	// - vainqueur : l'utilisateur a choisi l'équipe vainqueur du match
	$type = isset($_POST["type"]) ? $_POST["type"] : '';
	
	if($_SESSION["pronostiqueur"] != 1 && time() > 1528977600) {
		echo 'DEPASSE';
		exit();
	}

	// Vérification de l'existence du pronostic et création de celui-ci si nécessaire
	$ordreSQL =		'	INSERT INTO		cdm_pronostics_phase_finale	(	Pronostiqueurs_Pronostiqueur, Matches_Match)' .
					'					SELECT		*' .
					'					FROM		(SELECT ' . $pronostiqueur . ' AS Pronostiqueurs_Pronostiqueur, ' . $match . ' AS Matches_Match) AS tmp' .
					'					WHERE		NOT EXISTS	(SELECT * FROM cdm_pronostics_phase_finale WHERE Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur . ' AND Matches_Match = ' . $match . ')';
	$bdd->exec($ordreSQL);

	// Dans un premier temps, on écrit les données dans la table
	switch($type) {
		case 'score':
			// Changement du score
			$equipeAB = isset($_POST["equipeAB"]) ? $_POST["equipeAB"] : '';
			$score = isset($_POST["score"]) ? $_POST["score"] : 0;
			if($score == -1)
				$score = 'NULL';

			if($equipeAB == 'A') {
				// Mise à jour du score de l'équipe A
				$ordreSQL =		'	UPDATE			cdm_pronostics_phase_finale' .
								'	SET				Pronostics_ScoreEquipeA = ' . $score .
								'					,Pronostics_DateMAJ = NOW()' .
								'	WHERE			Matches_Match = ' . $match .
								'					AND		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur;
			}
			else if($equipeAB == 'B') {
				// Mise à jour du score de l'équipe B
				$ordreSQL =		'	UPDATE			cdm_pronostics_phase_finale' .
								'	SET				Pronostics_ScoreEquipeB = ' . $score .
								'					,Pronostics_DateMAJ = NOW()' .
								'	WHERE			Matches_Match = ' . $match .
								'					AND		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur;
			}
		break;

		case 'scoreAP':
			// Changement du score AP
			$equipeAB = isset($_POST["equipeAB"]) ? $_POST["equipeAB"] : '';
			$score = isset($_POST["score"]) ? $_POST["score"] : 0;
			if($score == -1)
				$score = 'NULL';

			if($equipeAB == 'A') {
				// Mise à jour du score de l'équipe A
				$ordreSQL =		'	UPDATE			cdm_pronostics_phase_finale' .
								'	SET				Pronostics_ScoreAPEquipeA = ' . $score .
								'					,Pronostics_DateMAJ = NOW()' .
								'	WHERE			Matches_Match = ' . $match .
								'					AND		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur;
			}
			else if($equipeAB == 'B') {
				// Mise à jour du score de l'équipe B
				$ordreSQL =		'	UPDATE			cdm_pronostics_phase_finale' .
								'	SET				Pronostics_ScoreAPEquipeB = ' . $score .
								'					,Pronostics_DateMAJ = NOW()' .
								'	WHERE			Matches_Match = ' . $match .
								'					AND		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur;
			}
		break;

		case 'vainqueur':
			// Changement de vainqueur
			$vainqueur = isset($_POST["vainqueur"]) ? $_POST["vainqueur"] : 0;
			$ordreSQL =		'	UPDATE		cdm_pronostics_phase_finale' .
							'	SET			Pronostics_Vainqueur = ' . $vainqueur .
							'				,Pronostics_DateMAJ = NOW()' .
							'	WHERE		Matches_Match = ' . $match .
							'				AND		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur;
		break;
	}
	$bdd->exec($ordreSQL);

	$retour = '';

	// Maintenant, selon la modification effectuée et le type de match...
	if($type == 'score') {
		// Lecture des pronostics AP du match
		$ordreSQL =		'	SELECT		IFNULL(Pronostics_ScoreEquipeA, -1) AS Pronostics_ScoreEquipeA, IFNULL(Pronostics_ScoreEquipeB, -1) AS Pronostics_ScoreEquipeB' .
						'	FROM		cdm_pronostics_phase_finale' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
						'				AND		Matches_Match = ' . $match;

		$req = $bdd->query($ordreSQL);
		$donnees = $req->fetch();

		$match_ScoreEquipeA = $donnees["Pronostics_ScoreEquipeA"];
		$match_ScoreEquipeB = $donnees["Pronostics_ScoreEquipeB"];

		if($match_ScoreEquipeA != -1 && $match_ScoreEquipeB != -1) {
			if($match_ScoreEquipeA == $match_ScoreEquipeB) {
				$retour = 'PROLONGATION|TAB';

				// Copie des scores du match retour vers les scores de prolongation
				$ordreSQL =		'	UPDATE		cdm_pronostics_phase_finale' .
								'	SET			Pronostics_ScoreAPEquipeA = Pronostics_ScoreEquipeA' .
								'				,Pronostics_ScoreAPEquipeB = Pronostics_ScoreEquipeB' .
								'				,Pronostics_DateMAJ = NOW()' .
								'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
								'				AND		Matches_Match = ' . $match;
				$bdd->exec($ordreSQL);
			}
			else {
				// S'il n'y a pas prolongation et TAB, on efface les pronostics AP et le nom du vainqueur car ils ont pu avoir été écrits à un moment
				$ordreSQL =		'	UPDATE			cdm_pronostics_phase_finale' .
								'	SET				Pronostics_Vainqueur = NULL' .
								'					,Pronostics_ScoreAPEquipeA = NULL' .
								'					,Pronostics_ScoreAPEquipeB = NULL' .
								'					,Pronostics_DateMAJ = NOW()' .
								'	WHERE			Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
								'					AND		Matches_Match = ' . $match;
								
				$bdd->exec($ordreSQL);
				$retour = $ordreSQL;
			}
		}
	}
	else if($type == 'scoreAP') {
		// Lecture des scores AP du match (type de match 4)
		$ordreSQL =		'	SELECT		IFNULL(Pronostics_ScoreAPEquipeA, -1) AS Pronostics_ScoreAPEquipeA, IFNULL(Pronostics_ScoreAPEquipeB, -1) AS Pronostics_ScoreAPEquipeB' .
						'	FROM		cdm_pronostics_phase_finale' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
						'				AND		Matches_Match = ' . $match;
		$req = $bdd->query($ordreSQL);
		$donnees = $req->fetch();

		$match_ScoreAPEquipeA = $donnees["Pronostics_ScoreAPEquipeA"];
		$match_ScoreAPEquipeB = $donnees["Pronostics_ScoreAPEquipeB"];

		if($match_ScoreAPEquipeA != -1 && $match_ScoreAPEquipeB != -1) {
			if($match_ScoreAPEquipeA == $match_ScoreAPEquipeB) {
				$retour = 'PROLONGATION|TAB';
			}
			else {
				// S'il n'y a pas TAB, on efface le nom du vainqueur car il a pu avoir été écrit à un moment
				$ordreSQL =		'	UPDATE			cdm_pronostics_phase_finale' .
								'	SET				Pronostics_Vainqueur = NULL' .
								'					,Pronostics_DateMAJ = NOW()' .
								'	WHERE			Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
								'					AND		Matches_Match = ' . $match;
				$bdd->exec($ordreSQL);
				$retour = $ordreSQL;
			}
		}
	}

	echo $retour;

?>
