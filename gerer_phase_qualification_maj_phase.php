<?php
	include_once('commun_administrateur.php');

	function sauvegarderPronostic($bdd, $equipe, $championnat, $phase) {
		$ordreSQL =		'	UPDATE		equipes_groupes' .
						'	SET			EquipesGroupes_Phase = ' . $phase .
						'	WHERE		Equipes_Equipe = ' . $equipe;
		$bdd->exec($ordreSQL);
	}

	// Sauvegarde d'une équipe en phase de qualification

	// Lecture des paramètres passés à la page
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;
	$phase = isset($_POST["phase"]) ? $_POST["phase"] : 0;

	// On vérifie auparavant qu'il soit possible de mettre à jour la phase (nombre d'équipes max non atteint)
	// Uniquement dans le cas où la phase vaut 1, 2 ou 3
	// La phase vaut 0 lorsque le pronostiqueur efface son pronostic, ce qu'il a toujours le droit de faire
	if($phase != 0) {
		sauvegarderPronostic($bdd, $equipe, $championnat, $phase);

	} else {
		// Remise à zéro du pronostic de phase de qualification
		sauvegarderPronostic($bdd, $equipe, $championnat, $phase);
	}


?>