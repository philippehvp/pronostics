<?php
	include_once('../commun.php');

	// Affichage des détails d'une équipe du concours
	// Si l'équipe fait partie de la ligue 1 et que l'on est sur le sous-onglet de ligue 1, alors on affiche aussi les résultats de ses précédentes journées de ligue 1

	// Lecture des paramètres passés à la page
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
	$typeEquipe = isset($_POST["typeEquipe"]) ? $_POST["typeEquipe"] : 0;

	$ordreSQL =		'	SELECT		Equipes_Nom, Equipes_NomCourt, Equipes_Fanion' .
					'	FROM		equipes' .
					'	WHERE		Equipe = ' . $equipe;

	$req = $bdd->query($ordreSQL);
	$equipes = $req->fetchAll();
	if(sizeof($equipes) == 1)
		$uneEquipe = $equipes[0];
	else {
		echo '<label class="cc--equipes-detail--nom">Equipe non trouvée</label>';
		return;
	}

	$ordreSQL =		'	SELECT		CONCAT(joueurs.Joueurs_NomFamille, \' \', IFNULL(joueurs.Joueurs_Prenom, \'\')) AS Joueurs_NomComplet' .
					'	FROM		joueurs' .
					'	JOIN		joueurs_equipes' .
					'				ON		joueurs.Joueur = joueurs_equipes.Joueurs_Joueur' .
					'	WHERE		JoueursEquipes_Debut <= NOW()' .
					'				AND		(JoueursEquipes_Fin IS NULL OR JoueursEquipes_Fin > NOW())' .
					'				AND		joueurs_equipes.Equipes_Equipe = ' . $equipe .
					'	ORDER BY	Joueurs_NomComplet';

	$req = $bdd->query($ordreSQL);
	$joueurs = $req->fetchAll();

	// Fanion et nom de l'équipe
	echo '<div class="cc--equipes-entete colle-gauche">';
		echo '<img class="photo cc--vignette--bordure-grise" src="images/equipes/' . $uneEquipe["Equipes_Fanion"] . '" alt="" />';
		// Si le nom court est le même que le nom long, alors il ne faut pas l'afficher
		if($uneEquipe["Equipes_Nom"] == $uneEquipe["Equipes_NomCourt"])
			echo '<label class="cc--equipes-detail--nom">' . $uneEquipe["Equipes_Nom"] . '</label>';
		else
			echo '<label class="cc--equipes-detail--nom">' . $uneEquipe["Equipes_Nom"] . ' (' . $uneEquipe["Equipes_NomCourt"] . ')</label>';
	echo '</div>';

	// Effectif
	echo '<div>';
		// Affichage des joueurs en colonnes
		$NOMBRE_COLONNES = 7;
		$nombreJoueurs = sizeof($joueurs);
		$nombreJoueursParColonne = ceil($nombreJoueurs / $NOMBRE_COLONNES);

		date_default_timezone_set('Europe/Paris');
		echo '<label><b>Effectif au ' . date('d/m/Y') . '</b></label><br />';
		for($i = 0; $i < $NOMBRE_COLONNES; $i++) {
			echo '<div class="gauche">';
				for($j = 0; $j < $nombreJoueursParColonne && $i * $nombreJoueursParColonne + $j < $nombreJoueurs; $j++) {
					$indice = $i * $nombreJoueursParColonne + $j;
					$joueursNom = $joueurs[$indice]["Joueurs_NomComplet"];
					echo '<label class="cc--equipes-detail--effectif">' . $joueursNom . '</label>';
					echo '<br />';
				}
			echo '</div>';
		}

	echo '</div>';

?>