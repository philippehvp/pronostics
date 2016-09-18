<?php
	include('commun_administrateur.php');

	// Modification du nom ou du nom court de l'équipe
	
	// Lecture des paramètres passés à la page
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
	$valeur = isset($_POST["valeur"]) ? $_POST["valeur"] : 0;
	$champ = isset($_POST["champ"]) ? $_POST["champ"] : -1;

	if($champ == -1)
		return;
	
	switch($champ) {
		case 0: $colonne = 'Equipes_Nom'; break;
		case 1: $colonne = 'Equipes_NomCourt'; break;
		case 2: $colonne = 'Equipes_Fanion'; break;
	}
	
	$ordreSQL =		'	UPDATE		equipes' .
					'	SET			' . $colonne . ' = ' . $bdd->quote($valeur) .
					'	WHERE		Equipe = ' . $equipe;

	$bdd->exec($ordreSQL);

?>