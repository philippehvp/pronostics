<?php
	include_once('commun.php');

	// Mise à jour d'un pronostic de poule
	
	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
	$score = isset($_POST["score"]) ? $_POST["score"] : 0;

	// Le paramètre equipe indique s'il s'agit du score de l'équipe A ou celui de l'équipe B
	// On vérifie avant tout qu'il n'est pas trop tard pour faire la modification
	if($_SESSION["cdm_pronostiqueur"] != 1 && time() > 1668960000) {
		echo 'DEPASSE';
		exit();
	}

	$ordreSQL = 'CALL cdm_sp_calcul_poule(' . $_SESSION["cdm_pronostiqueur"] . ', ' . $match . ', ' . $equipe . ', ' . $score . ')';
	$texte = $ordreSQL;
	$bdd->exec($ordreSQL);
	
	// Arrivé ici, on cherche à savoir si des cas d'égalité ont �t� trouvés (uniquement si tous les pronostics de la poule ont été effectués)
	
	// On vérifie que tous les pronostics ont été saisis pour la poule
	$ordreSQL =		'	SELECT		COUNT(*) AS NombreEgalites' .
								'	FROM		cdm_pronostics_poule_egalites' .
								'	JOIN		cdm_matches_poule' .
								'				ON		cdm_pronostics_poule_egalites.Poules_Poule = cdm_matches_poule.Poules_Poule' .
								'	WHERE		cdm_pronostics_poule_egalites.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"] .
								'				AND		cdm_fn_nombre_matches_incomplets(' . $_SESSION["cdm_pronostiqueur"] . ', ' . $match . ') = 0' .
								'				AND		cdm_matches_poule.Match = ' . $match;
	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetchAll();
	
	if(sizeof($donnees)) {
		if($donnees[0]["NombreEgalites"] > 0)
			echo 'EGALITE';
	}
?>
