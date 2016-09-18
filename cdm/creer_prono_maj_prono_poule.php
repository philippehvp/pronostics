<?php
	include('commun.php');

	// Mise � jour d'un pronostic de poule
	
	// Lecture des param�tres pass�s � la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
	$score = isset($_POST["score"]) ? $_POST["score"] : 0;

	// Le param�tre equipe indique s'il s'agit du score de l'�quipe A ou celui de l'�quipe B
	// On v�rifie avant tout qu'il n'est pas trop tard pour faire la modification
	if($_SESSION["pronostiqueur"] != 1) {
		echo 'DEPASSE';
		exit();
	}

	$ordreSQL = 'CALL cdm_sp_calcul_poule(' . $_SESSION["pronostiqueur"] . ', ' . $match . ', ' . $equipe . ', ' . $score . ')';
	$texte = $ordreSQL;
	$bdd->exec($ordreSQL);
	
	// Arriv� ici, on cherche � savoir si des cas d'�galit� ont �t� trouv�s (uniquement si tous les pronostics de la poule ont �t� effectu�s)
	
	// On v�rifie que tous les pronostics ont �t� saisis pour la poule
	$ordreSQL =		'	SELECT		COUNT(*) AS NombreEgalites' .
					'	FROM		cdm_pronostics_poule_egalites' .
					'	JOIN		cdm_matches_poule' .
					'				ON		cdm_pronostics_poule_egalites.Poules_Poule = cdm_matches_poule.Poules_Poule' .
					'	WHERE		cdm_pronostics_poule_egalites.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'				AND		cdm_fn_nombre_matches_incomplets(' . $_SESSION["pronostiqueur"] . ', ' . $match . ') = 0' .
					'				AND		cdm_matches_poule.Match = ' . $match;
	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetchAll();
	
	if(sizeof($donnees)) {
		if($donnees[0]["NombreEgalites"] > 0)
			echo 'EGALITE';
	}
?>
