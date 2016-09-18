<?php
	// Cette page est appelée de deux manières :
	// - soit par une page PHP (paramètre appelAjax inconnu)
	// - soit par un appel Ajax (rafraîchissement de la page demandé par elle-même) (appelAjax est égal à 1)
	// Selon l'appelant, il est nécessaire ou non d'inclure l'en-tête, de créer la balise englobante ou non, etc.
	// La détection de l'appel dépend de la présence du paramètre appelAjax

	$POULE_MIN = 1;
	$POULE_MAX = 8;
	
	$appelAjax = isset($_POST["appelAjax"]) ? $_POST["appelAjax"] : 0;
	if($appelAjax == 1) {
		include('commun.php');
	}

	// Avec un rafraîchissement de la page, on peut recevoir en paramètre le numéro de poule à afficher
	if($appelAjax == 1) {
		$poule = isset($_POST["poule"]) ? $_POST["poule"] : 1;
	}
	else {
		$poule = 1;
	}
	
	// Lecture des vrais résultats
	$ordreSQL =		'	SELECT		Equipes_Fanion, Equipes_NomCourt, IFNULL(PronosticsPouleClassements_ClassementTirage, PronosticsPouleClassements_Classement) AS Classement' .
					'	FROM		(' .
					'					SELECT		Pronostiqueur, Poules_Poule, Equipe' .
					'					FROM		cdm_pronostiqueurs' .
					'					FULL JOIN	cdm_equipes' .
					'					WHERE		Pronostiqueur = 1' .
					'				) pronostiqueurs_equipes' .
					'	LEFT JOIN	cdm_pronostics_poule_classements' .
					'				ON		pronostiqueurs_equipes.Pronostiqueur = cdm_pronostics_poule_classements.Pronostiqueurs_Pronostiqueur' .
					'						AND		pronostiqueurs_equipes.Poules_Poule = cdm_pronostics_poule_classements.Poules_Poule' .
					'						AND		pronostiqueurs_equipes.Equipe = cdm_pronostics_poule_classements.Equipes_Equipe' .
					'	LEFT JOIN	cdm_poules' .
					'				ON		cdm_pronostics_poule_classements.Poules_Poule = cdm_poules.Poule' .
					'	LEFT JOIN	cdm_equipes' .
					'				ON		cdm_pronostics_poule_classements.Equipes_Equipe = cdm_equipes.Equipe' .
					'	WHERE		pronostiqueurs_equipes.Poules_Poule = ' . $poule .
					'	ORDER BY	pronostiqueurs_equipes.Pronostiqueur, pronostiqueurs_equipes.Poules_Poule' .
					'				,IFNULL(PronosticsPouleClassements_ClassementTirage, IFNULL(PronosticsPouleClassements_Classement, 99))' .
					'				,pronostiqueurs_equipes.Equipe';
	$req = $bdd->query($ordreSQL);
	$resultats = $req->fetchAll();
		
	// Tous les pronostics
	$ordreSQL =		'	SELECT		CASE' .
					'					WHEN	pronostiqueurs_equipes.Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'					THEN	1' .
					'					ELSE	2' .
					'				END AS Ordre' .
					'				,CASE' .
					'					WHEN	Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'					THEN	\'Moi\'' .
					'					ELSE	Pronostiqueurs_Nom' .
					'				END AS Pronostiqueurs_Nom' .
					'				,IFNULL(Equipes_Fanion, \'_inconnu.png\') AS Equipes_Fanion, Equipes_NomCourt' .
					'				,IFNULL(PronosticsPouleClassements_ClassementTirage, PronosticsPouleClassements_Classement) AS Classement' .
					'	FROM		(' .
					'					SELECT		Pronostiqueur, Pronostiqueurs_Nom, Poules_Poule, Equipe' .
					'					FROM		cdm_pronostiqueurs' .
					'					FULL JOIN	cdm_equipes' .
					'					WHERE		Pronostiqueur <> 1' .
					'				) pronostiqueurs_equipes' .
					'	LEFT JOIN	cdm_pronostics_poule_classements' .
					'				ON		pronostiqueurs_equipes.Pronostiqueur = cdm_pronostics_poule_classements.Pronostiqueurs_Pronostiqueur' .
					'						AND		pronostiqueurs_equipes.Poules_Poule = cdm_pronostics_poule_classements.Poules_Poule' .
					'						AND		pronostiqueurs_equipes.Equipe = cdm_pronostics_poule_classements.Equipes_Equipe' .
					'	LEFT JOIN	cdm_poules' .
					'				ON		cdm_pronostics_poule_classements.Poules_Poule = cdm_poules.Poule' .
					'	LEFT JOIN	cdm_equipes' .
					'				ON		cdm_pronostics_poule_classements.Equipes_Equipe = cdm_equipes.Equipe' .
					'	WHERE		pronostiqueurs_equipes.Poules_Poule = ' . $poule .
					'	ORDER BY	Ordre, Pronostiqueurs_Nom, pronostiqueurs_equipes.Poules_Poule' .
					'				,IFNULL(PronosticsPouleClassements_ClassementTirage, PronosticsPouleClassements_Classement)' .
					'				,pronostiqueurs_equipes.Equipe';
	$req = $bdd->query($ordreSQL);
	$pronostics = $req->fetchAll();

	$nombrePronostiqueurs = sizeof($pronostics) / 4;
	
	// Points des classements des poules
	$ordreSQL	=	'	SELECT		CASE' .
					'					WHEN	cdm_pronostiqueurs.Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'					THEN	1' .
					'					ELSE	2' .
					'				END AS Ordre' .
					'				,IFNULL(BonusClassements_Points, 0) AS BonusClassements_Points, IFNULL(BonusSorties_Points, 0) AS BonusSorties_Points' .
					'	FROM		cdm_pronostiqueurs' .
					'	LEFT JOIN	cdm_bonus_classements' .
					'				ON		cdm_pronostiqueurs.Pronostiqueur = cdm_bonus_classements.Pronostiqueurs_Pronostiqueur' .
					'	LEFT JOIN	cdm_bonus_sorties' .
					'				ON		cdm_pronostiqueurs.Pronostiqueur = cdm_bonus_sorties.Pronostiqueurs_Pronostiqueur' .
					'						AND		cdm_bonus_classements.Poules_Poule = cdm_bonus_sorties.Poules_Poule' .
					'	WHERE		cdm_bonus_classements.Poules_Poule = ' . $poule .
					'				AND		cdm_bonus_sorties.Poules_Poule = ' . $poule .
					'				AND		cdm_pronostiqueurs.Pronostiqueur <> 1' .
					'	ORDER BY	Ordre, cdm_pronostiqueurs.Pronostiqueurs_Nom, cdm_bonus_sorties.Poules_Poule';

	$req = $bdd->query($ordreSQL);
	$points = $req->fetchAll();

	// Affichage d'une zone de titre du module
	echo '<div id="divEnteteClassements">';
		//echo '<div class="colle-gauche gauche nomModule">Classements de poule</div>';
		if($poule > $POULE_MIN)
			echo '<label class="bouton" onclick="module_classementsPoule_afficherPoule(' . ($poule - 1) . ');">&lt;</label>';
		else
			echo '<label class="bouton" onclick="module_classementsPoule_afficherPoule(' . $POULE_MAX . ');">&lt;</label>';
		echo '<label class="poule">Poule ' . chr($poule + 64) . '</label>';
		if($poule < $POULE_MAX)
			echo '<label class="bouton" onclick="module_classementsPoule_afficherPoule(' . ($poule + 1) . ');">&gt;</label>';
		else
			echo '<label class="bouton" onclick="module_classementsPoule_afficherPoule(' . $POULE_MIN . ');">&gt;</label>';
	echo '</div>';
	
	// Affichage des matches dans l'en-tête de la table
	echo '<table id="tblClassementsPoule">';
		echo '<thead>';
			echo '<tr>';
				echo '<th>&nbsp;</th>';
				for($i = 0; $i < 4; $i++) {
					echo '<th class="bordure-droite">';
						if($resultats[$i]["Equipes_Fanion"] != '')
							echo '<img src="images/equipes/' . $resultats[$i]["Equipes_Fanion"] . '" width="21" height="14" alt="" />' . '&nbsp;' . $resultats[$i]["Equipes_NomCourt"];
						else
							echo '&nbsp;';
					echo '</th>';
				}
				echo '<th>Points classements</th>';
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
			for($i = 0; $i < $nombrePronostiqueurs; $i++) {
				echo '<tr>';
					echo '<td class="nomPronostiqueur bordure-droite">' . $pronostics[$i*4]["Pronostiqueurs_Nom"] . '</td>';
					for($j = 0; $j < 4; $j++) {
						echo '<td class="bordure-droite">';
							echo '<img src="images/equipes/' . $pronostics[($i * 4) + $j]["Equipes_Fanion"] . '" width="21" height="14" alt="" />' . '&nbsp;' . $pronostics[($i * 4) + $j]["Equipes_NomCourt"];
						echo '</td>';
					}
					
					echo '<td class="bordure-droite">(' . $points[$i]["BonusClassements_Points"] . ' + ' . $points[$i]["BonusSorties_Points"] . ')</td>';
				echo '</tr>';
			}
		echo '</tbody>';
	echo '</table>';
?>
