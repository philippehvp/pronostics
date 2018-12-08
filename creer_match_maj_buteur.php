<?php
	include_once('commun_administrateur.php');

	// Mise à jour des buteurs
	
	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$joueurs = isset($_POST["joueurs"]) ? $_POST["joueurs"] : 0;
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
	
	// Suppression des buteurs déjà existants
	$ordreSQL =		'	DELETE	FROM	matches_buteurs' .
					'	WHERE			Matches_Match = ' . $match .
					'					AND		Equipes_Equipe = ' . $equipe;
	
	$bdd->exec($ordreSQL);

	// Ajout des buteurs
	for($i = 0; $i < $joueurs; $i++) {

		$chainePassee = isset($_POST["joueur" . $i]) ? $_POST["joueur" . $i] : '0-0.0';
		
		// Recherche d'un tiret et du point dans la chaîne
		$positionTiret = strpos($chainePassee, '-');
		$positionPoint = strpos($chainePassee, '.');
		
		if($positionTiret == 0 || $positionPoint == 0)
			continue;
			
		$joueur = substr($chainePassee, 0, $positionTiret);
		$csc = substr($chainePassee, ($positionTiret + 1), 1);
		$cote = substr($chainePassee, ($positionPoint + 1));

		$ordreSQL =		'	INSERT INTO		matches_buteurs(Matches_Match, Joueurs_Joueur, Equipes_Equipe, Buteurs_Cote, Buteurs_CSC)' .
						'	VALUES			(' . $match . ', ' . $joueur . ', ' . $equipe . ', ' . $cote . ', ' . $csc . ')';

		$bdd->exec($ordreSQL);
	}

	// Dans l'interface, on ne demande la cote d'un buteur qu'à partir du moment où celui-ci n'a pas encore été ajouté à la liste des buteurs ou que celui-ci a marqué contre son camp à chaque fois qu'il apparaît dans la liste
	// Une fois cette information demandée, on ne la redemande plus
	// Par conséquent, si un buteur a marqué plusieurs fois, il y aura autant de lignes de buts pour lui mais une seule aura l'information cote remplie (sauf s'il n'y a que des buts CSC)
	// Les autres lignes (si elles existent) auront cette information à 0
	
	$ordreSQL =		'	UPDATE			matches_buteurs mb1' .
					'	INNER JOIN		(	SELECT		Matches_Match, Joueurs_Joueur, Equipes_Equipe, MAX(Buteurs_Cote) AS Buteurs_Cote' .
					'						FROM		matches_buteurs' .
					'						GROUP BY	Matches_Match, Joueurs_Joueur, Equipes_Equipe' .
					'					) mb2' .
					'					ON		mb1.Matches_Match = mb2.Matches_Match' .
					'					AND		mb1.Joueurs_Joueur = mb2.Joueurs_Joueur' .
					'					AND		mb1.Equipes_Equipe = mb2.Equipes_Equipe' .
					'	SET				mb1.Buteurs_Cote = mb2.Buteurs_Cote' .
					'	WHERE			mb1.Buteurs_Cote = 0';
	$bdd->exec($ordreSQL);
	
	// On indique qu'une modification a eu lieu au niveau du match
	$ordreSQL =		'	UPDATE		matches' .
					'	SET			Matches_DateMAJ = NOW()' .
					'	WHERE		matches.Match = ' . $match;
	$bdd->exec($ordreSQL);
	
?>