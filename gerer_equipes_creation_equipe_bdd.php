<?php
	include_once('commun_administrateur.php');

	// Création en base de données d'une équipe
	
	// Lecture des paramètres passés à la page
	$nom = isset($_POST["nom"]) ? $_POST["nom"] : '';
	$nomCourt = isset($_POST["nom_court"]) ? $_POST["nom_court"] : 'NULL';
	$fanion = isset($_POST["fanion"]) ? $_POST["fanion"] : 'NULL';
	$l1 = isset($_POST["l1"]) ? $_POST["l1"] : 0;
	$l1Europe = isset($_POST["l1_europe"]) ? $_POST["l1_europe"] : 0;
	$ldc = isset($_POST["ldc"]) ? $_POST["ldc"] : 0;
	$el = isset($_POST["el"]) ? $_POST["el"] : 0;
	$barrages = isset($_POST["barrages"]) ? $_POST["barrages"] : 0;
	$cdf = isset($_POST["cdf"]) ? $_POST["cdf"] : 0;
	
	
	if(strlen($nom) == 0)
		return;

	$ordreSQL =		'	INSERT INTO		equipes(Equipes_Nom, Equipes_NomCourt, Equipes_Fanion)' .
					'	SELECT			' . $bdd->quote($nom) .
					'					,CASE WHEN LENGTH(' . $bdd->quote($nomCourt) . ') = 0 THEN NULL ELSE ' . $bdd->quote($nomCourt) . ' END' .
					'					,CASE WHEN LENGTH(' . $bdd->quote($fanion) . ') = 0 THEN NULL ELSE ' . $bdd->quote($fanion) . ' END';

	$bdd->exec($ordreSQL);
	
	// Numéro de l'équipe qui vient d'être ajoutée
	$equipe = $bdd->lastInsertId();
	
	
	// Ajout dans chacun des championnats cochés
	if($l1 == 1) {
		$ordreSQL =		'	INSERT INTO	engagements(Equipes_Equipe, Championnats_Championnat)' .
						'	SELECT		' . $equipe . ', 1';
		$bdd->exec($ordreSQL);
	}

	if($ldc == 1) {
		$ordreSQL =		'	INSERT INTO	engagements(Equipes_Equipe, Championnats_Championnat)' .
						'	SELECT		' . $equipe . ', 2';
		$bdd->exec($ordreSQL);
	}
	
	if($el == 1) {
		$ordreSQL =		'	INSERT INTO	engagements(Equipes_Equipe, Championnats_Championnat)' .
						'	SELECT		' . $equipe . ', 3';
		$bdd->exec($ordreSQL);
	}
	
	if($barrages == 1) {
		$ordreSQL =		'	INSERT INTO	engagements(Equipes_Equipe, Championnats_Championnat)' .
						'	SELECT		' . $equipe . ', 4';
		$bdd->exec($ordreSQL);
	}

	if($cdf == 1) {
		$ordreSQL =		'	INSERT INTO	engagements(Equipes_Equipe, Championnats_Championnat)' .
						'	SELECT		' . $equipe . ', 5';
		$bdd->exec($ordreSQL);
	}
	
	// L'équipe joue-t-elle le match européen de ligue 1 ?
	if($l1Europe == 1) {
		$ordreSQL =		'	UPDATE		equipes' .
						'	SET			Equipes_L1Europe = 1' .
						'	WHERE		Equipe = ' . $equipe;
		$bdd->exec($ordreSQL);
	}
	
?>