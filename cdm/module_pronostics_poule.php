<?php
	// Cette page est appelée de deux manières :
	// - soit par une page PHP (paramètre appelAjax inconnu)
	// - soit par un appel Ajax (rafraîchissement de la page demandé par elle-même) (appelAjax est égal à 1)
	// Selon l'appelant, il est nécessaire ou non d'inclure l'en-tête, de créer la balise englobante ou non, etc.
	// La détection de l'appel dépend de la présence du paramètre appelAjax

	$JOURNEE_MIN = 1;
	$JOURNEE_MAX = 15;
	
	$appelAjax = isset($_POST["appelAjax"]) ? $_POST["appelAjax"] : 0;
	if($appelAjax == 1) {
		include_once('commun.php');
	}
	
	// Avec un rafraîchissement de la page, certains paramètres permettent de savoir quelle journée afficher, s'il s'agit d'une journée de poule ou de phase finale
	if($appelAjax == 1) {
		$journeeEnCours = isset($_POST["journeeEnCours"]) ? $_POST["journeeEnCours"] : 0;
	}
	else {
		// S'il ne s'agit pas d'un rafraîchissement de page, on doit déterminer quelle journée afficher
		// Lecture du numéro de journée en cours et de la phase en cours (phase de poule ou phase finale)
		$ordreSQL =		'	SELECT		cdm_fn_journee_en_cours() AS Journee_En_Cours';
		$req = $bdd->query($ordreSQL);
		while($donnees = $req->fetch()) {
			$journeeEnCours = $donnees["Journee_En_Cours"];
		}
		$req->closeCursor();
	}
	
	if($journeeEnCours < $JOURNEE_MIN)
		$journeeEnCours = $JOURNEE_MIN;
	
	if($journeeEnCours > $JOURNEE_MAX)
		$journeeEnCours = $JOURNEE_MAX;

	// Affichage des données de la journée en cours
	$ordreSQL =		'	SELECT		Matches_DateLocale' .
					'				,equipesA.Equipes_Nom AS EquipesA_Nom' .
					'				,equipesB.Equipes_Nom AS EquipesB_Nom' .
					'				,equipesA.Equipes_NomCourt AS EquipesA_NomCourt' .
					'				,equipesB.Equipes_NomCourt AS EquipesB_NomCourt' .
					'				,pronostics_equipesA.PronosticsPoule_Score AS Score_EquipeA' .
					'				,pronostics_equipesB.PronosticsPoule_Score AS Score_EquipeB' .
					'				,CASE' .
					'					WHEN	cdm_matches_direct.Matches_Match IS NOT NULL' .
					'					THEN	1' .
					'					ELSE	0' .
					'				END AS Matches_EnDirect' .
					'	FROM		cdm_matches_poule' .
					'	LEFT JOIN	cdm_pronostics_poule pronostics_equipesA' .
					'				ON		cdm_matches_poule.Match = pronostics_equipesA.Matches_Match' .
					'						AND		cdm_matches_poule.Equipes_EquipeA = pronostics_equipesA.Equipes_Equipe' .
					'	LEFT JOIN	cdm_pronostics_poule pronostics_equipesB' .
					'				ON		cdm_matches_poule.Match = pronostics_equipesB.Matches_Match' .
					'						AND		cdm_matches_poule.Equipes_EquipeB = pronostics_equipesB.Equipes_Equipe' .
					'	JOIN		cdm_pronostiqueurs' .
					'				ON		pronostics_equipesA.Pronostiqueurs_Pronostiqueur = cdm_pronostiqueurs.Pronostiqueur' .
					'						AND		pronostics_equipesB.Pronostiqueurs_Pronostiqueur = cdm_pronostiqueurs.Pronostiqueur' .
					'	JOIN		cdm_equipes equipesA' .
					'				ON		cdm_matches_poule.Equipes_EquipeA = equipesA.Equipe' .
					'	JOIN		cdm_equipes equipesB' .
					'				ON		cdm_matches_poule.Equipes_EquipeB = equipesB.Equipe' .
					'	LEFT JOIN	cdm_matches_direct' .
					'				ON		cdm_matches_poule.Match = cdm_matches_direct.Matches_Match' .
					'	WHERE		cdm_pronostiqueurs.Pronostiqueur = 1' .
					'				AND		Matches_JourneeEnCours = ' . $journeeEnCours .
					'	ORDER BY	cdm_pronostiqueurs.Pronostiqueur, Matches_DateLocale, cdm_matches_poule.Match';

	$req = $bdd->query($ordreSQL);
	$matches = $req->fetchAll();
	$nombreMatches = sizeof($matches);
		
	// Tous les pronostics
	$ordreSQL =		'	SELECT		CASE' .
					'					WHEN	equipesA.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"] .
					'					THEN	1' .
					'					ELSE	2' .
					'				END AS Ordre' .
					'				,CASE' .
					'					WHEN	Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"] .
					'					THEN	\'Moi\'' .
					'					ELSE	Pronostiqueurs_Nom' .
					'				END AS Pronostiqueurs_Nom' .
					'				,equipesA.PronosticsPoule_Score AS Score_EquipeA, equipesB.PronosticsPoule_Score AS Score_EquipeB' .
					'				,cdm_scores.Scores_ScoreMatch' .
					'	FROM		cdm_matches_poule' .
					'	LEFT JOIN	cdm_pronostics_poule equipesA' .
					'				ON		cdm_matches_poule.Match = equipesA.Matches_Match' .
					'						AND		cdm_matches_poule.Equipes_EquipeA = equipesA.Equipes_Equipe' .
					'	LEFT JOIN	cdm_pronostics_poule equipesB' .
					'				ON		cdm_matches_poule.Match = equipesB.Matches_Match' .
					'						AND		cdm_matches_poule.Equipes_EquipeB = equipesB.Equipes_Equipe' .
					'	JOIN		cdm_pronostiqueurs' .
					'				ON		equipesA.Pronostiqueurs_Pronostiqueur = cdm_pronostiqueurs.Pronostiqueur' .
					'						AND		equipesB.Pronostiqueurs_Pronostiqueur = cdm_pronostiqueurs.Pronostiqueur' .
					'	LEFT JOIN	cdm_scores' .
					'				ON		cdm_pronostiqueurs.Pronostiqueur = cdm_scores.Pronostiqueurs_Pronostiqueur' .
					'						AND		cdm_matches_poule.Match = cdm_scores.Matches_Match' .
					'						AND		cdm_scores.Scores_Phase = 1' .
					'	WHERE		cdm_pronostiqueurs.Pronostiqueur <> 1' .
					'				AND		cdm_matches_poule.Matches_JourneeEnCours = ' . $journeeEnCours .
					'	ORDER BY	Ordre, cdm_pronostiqueurs.Pronostiqueurs_Nom, equipesA.Pronostiqueurs_Pronostiqueur, Matches_DateLocale, cdm_matches_poule.Match';

	$req = $bdd->query($ordreSQL);
	$pronostics = $req->fetchAll();
	
	$nombrePronostics = sizeof($pronostics) / ($nombreMatches == 0 ? 1 : $nombreMatches);
	
	// Affichage d'une zone d'information sur la journée en cours d'affichage
	// Cette zone contient aussi les boutons pour passer aux journées suivante / précédente
	echo '<div id="divEnteteJournees">';
		//echo '<div class="colle-gauche gauche nomModule">Poules</div>';
		if($journeeEnCours > $JOURNEE_MIN)
			echo '<label class="bouton" onclick="module_pronosticsPoule_afficherJournee(' . ($journeeEnCours - 1) . ');">&lt;</label>';
		else
			echo '<label class="bouton" onclick="module_pronosticsPoule_afficherJournee(' . $JOURNEE_MAX . ');">&lt;</label>';
		setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
		echo '<label class="date">Journée du ' . strftime('%#d %B', strtotime($matches[0]["Matches_DateLocale"])) . '</label>';
		if($journeeEnCours < $JOURNEE_MAX)
			echo '<label class="bouton" onclick="module_pronosticsPoule_afficherJournee(' . ($journeeEnCours + 1) . ');">&gt;</label>';
		else
			echo '<label class="bouton" onclick="module_pronosticsPoule_afficherJournee(' . $JOURNEE_MIN . ');">&gt;</label>';

		if($_SESSION["cdm_administrateur"] == 1)
			echo '&nbsp;<label class="bouton" onclick="module_pronostics_lancerCalcul(' . $journeeEnCours . ');">Calculer</label>';
	echo '</div>';
		
	// Affichage des matches dans l'en-tête de la table
	echo '<table id="tblPronosticsPoule">';
		echo '<thead>';
			echo '<tr>';
				echo '<th>&nbsp;</th>';
				for($i = 0; $i < $nombreMatches; $i++) {
					if($i < $nombreMatches - 1)
						$bordure = 'bordure-droite';
					else
						$bordure = '';

					echo '<th class="match" title="' . $matches[$i]["EquipesA_Nom"] . ' vs ' . $matches[$i]["EquipesB_Nom"] . '">';
						// Zone des noms des équipes
						// Match en direct ?
						$matchEnDirect = $matches[$i]["Matches_EnDirect"] == 1 ? 'matchEnDirect' : '';
						echo '<div class="gauche nomEquipes">';
							// Equipe A
							echo '<div>' . $matches[$i]["EquipesA_NomCourt"] . '</div>';
							
							// Equipe B
							echo '<div>' . $matches[$i]["EquipesB_NomCourt"] . '</div>';
						echo '</div>';
						
						// Zone des scores des matches
						echo '<div class="gauche scoreEquipes ' . $matchEnDirect . '">';
							// Score équipes
							echo $matches[$i]["Score_EquipeA"] . ' - ' . $matches[$i]["Score_EquipeB"];
						echo '</div>';
					echo '</th>';
				}
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
			for($j = 0; $j < $nombrePronostics; $j++) {
				echo '<tr>';
					echo '<td class="nomPronostiqueur bordure-droite">' . $pronostics[$j*$nombreMatches]["Pronostiqueurs_Nom"] . '</td>';
					for($k = 0; $k < $nombreMatches; $k++) {
						if($k < $nombreMatches - 1)
							$bordure = 'bordure-droite';
						else
							$bordure = '';
						
						$scoreA = $pronostics[$j*$nombreMatches+$k]["Score_EquipeA"] != null ? $pronostics[$j*$nombreMatches+$k]["Score_EquipeA"] : '&nbsp;';
						$scoreB = $pronostics[$j*$nombreMatches+$k]["Score_EquipeB"] != null ? $pronostics[$j*$nombreMatches+$k]["Score_EquipeB"] : '&nbsp;';
						$scoreMatch = $pronostics[$j*$nombreMatches+$k]["Scores_ScoreMatch"] != null ? $pronostics[$j*$nombreMatches+$k]["Scores_ScoreMatch"] : '&nbsp;';
						
						// Couleur de chaque cellule selon les résultats
						if($scoreMatch < 5)
							$style = '';
						else if($scoreMatch >= 5 && $scoreMatch < 10)
							$style = 'orange';
						else
							$style = 'vert';
							
						echo '<td class="match">';
							// Score des équipes
							echo '<div class="gauche scoreEquipes fondScore">';
								echo $scoreA . ' - ' . $scoreB;
							echo '</div>';
							
							// Zone des points match
							echo '<div class="gauche pointsMatch ' . $style . '">';
								echo $scoreMatch;
							echo '</div>';
						echo '</td>';
						
					}
				echo '</tr>';
			}
		echo '</tbody>';
	echo '</table>';
	
?>
