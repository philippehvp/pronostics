<?php
	include_once('commun_administrateur.php');

	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;

	// Demande d'affichage de la page de trophées pour tous les pronostiqueurs à leur prochaine connexion
	$ordreSQL =		'	UPDATE		pronostiqueurs' .
					'	JOIN		inscriptions' .
					'				ON		Pronostiqueur = Pronostiqueurs_Pronostiqueur' .
					'	SET			Pronostiqueurs_AfficherTropheesChampionnat = ' . $championnat .
					'	WHERE		Championnats_Championnat = ' . $championnat;
	$req = $bdd->exec($ordreSQL);

	// Dans le cas où le championnat est la Ligue 1, il est nécessaire de lancer le calcul du classement des équipes
	if($championnat == 1) {
		$ordreSQL =		'	CALL sp_calculclassementequipes()';
		$req = $bdd->exec($ordreSQL);
	}

?>