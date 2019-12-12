<?php
	include_once('commun_administrateur.php');

	// Engagement / désengagement d'une équipe à un championnat ou ajout / suppression de l'équipe dans le match européen de ligue 1

	// Lecture des paramètres passés à la page
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;
	$l1Europe = isset($_POST["l1Europe"]) ? $_POST["l1Europe"] : 0;
	$action = isset($_POST["action"]) ? $_POST["action"] : -1;

	if($action == -1)
		return;

	if($action == 1) {
		// Engagement dans un championnat ou au match européen de ligue 1
		if($championnat == 1 && $l1Europe == 1)
			$ordreSQL =		'	UPDATE		equipes' .
							'	SET			Equipes_L1Europe = 1' .
							'	WHERE		Equipe = ' . $equipe;
		else
			$ordreSQL =		'	INSERT' .
							'	INTO		engagements(Championnats_Championnat, Equipes_Equipe)' .
							'	SELECT		' . $championnat . ', ' . $equipe;
	}
	else if($action == 0) {
		// Désengagement d'un championnat ou du match européen de ligue 1
		if($championnat == 1 && $l1Europe == 1)
			$ordreSQL =		'	UPDATE		equipes' .
							'	SET			Equipes_L1Europe = NULL' .
							'	WHERE		Equipe = ' . $equipe;
		else
			$ordreSQL =		'	DELETE' .
							'	FROM		engagements' .
							'	WHERE		Championnats_Championnat = ' . $championnat .
							'				AND		Equipes_Equipe = ' . $equipe;
	}

	$bdd->exec($ordreSQL);

?>