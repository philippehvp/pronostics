<?php
	
	
	// Cette page est appelée de deux manières différentes :
	// - soit par un include php
	// - soit par un appel Ajax depuis une fonction Javascript
	// Dans le cas de l'appel Ajax, on force l'appel à la fonction afficherClassementPoule
	// Pour déterminer le mode d'appel de la page, on regarde la présence d'un paramètre nommé appalAjax
	$appelAjax = isset($_POST["appelAjax"]) ? 1 : 0;
	$poule = isset($_POST["poule"]) ? $_POST["poule"] : 0;

	if($appelAjax == 1) {
		include('commun.php');
	}
	
	// Fonction d'affichage du classement d'une poule
	function afficherClassementPoule($poule, $bdd) {
		// Lecture des données déjà saisies par le pronostiqueur
		$ordreSQL =		'	SELECT		DISTINCT IFNULL(cdm_pronostics_poule_classements.PronosticsPouleClassements_ClassementTirage, cdm_pronostics_poule_classements.PronosticsPouleClassements_Classement) AS PronosticsPouleClassements_Classement' .
						'				,cdm_equipes.Equipes_Nom' .
						'				,cdm_pronostics_poule_stats.PronosticsPouleStats_Points' .
						'				,cdm_pronostics_poule_stats.PronosticsPouleStats_J' .
						'				,cdm_pronostics_poule_stats.PronosticsPouleStats_G' .
						'				,cdm_pronostics_poule_stats.PronosticsPouleStats_N' .
						'				,cdm_pronostics_poule_stats.PronosticsPouleStats_P' .
						'				,PronosticsPouleStats_BP' .
						'				,PronosticsPouleStats_BC' .
						'				,PronosticsPouleStats_Diff' .
						'	FROM		cdm_pronostics_poule_classements' .
						'	JOIN		cdm_equipes' .
						'				ON		cdm_pronostics_poule_classements.Equipes_Equipe = cdm_equipes.Equipe' .
						'	JOIN		cdm_pronostics_poule_stats' .
						'				ON		cdm_pronostics_poule_classements.Equipes_Equipe = cdm_pronostics_poule_stats.Equipes_Equipe' .
						'						AND		cdm_pronostics_poule_classements.Pronostiqueurs_Pronostiqueur = cdm_pronostics_poule_stats.Pronostiqueurs_Pronostiqueur' .
						'						AND		cdm_pronostics_poule_classements.Poules_Poule = cdm_pronostics_poule_stats.Poules_Poule' .
						'	WHERE		cdm_pronostics_poule_stats.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'				AND		cdm_pronostics_poule_stats.Poules_Poule = ' . $poule .
						'	ORDER BY	IFNULL(PronosticsPouleClassements_ClassementTirage, PronosticsPouleClassements_Classement)';

		$req = $bdd->query($ordreSQL);
		echo '<table class="classementPoule">';
			echo '<thead>';
				echo '<tr>';
					echo '<th>Pos.</th>';
					echo '<th class="equipe">Equipe</th>';
					echo '<th>Pts</th>';
					echo '<th>J</th>';
					echo '<th>G</th>';
					echo '<th>N</th>';
					echo '<th>P</th>';
					echo '<th>BP</th>';
					echo '<th>BC</th>';
					echo '<th>Diff</th>';
				echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
				$i = 1;
				while($donnees = $req->fetch()) {
					$fondVert = ($i++ < 3) ? 'fondVert' : 'fondOrange';
					echo '<tr class="' . $fondVert . '">';
						echo '<td class="bordure-droite">' . $donnees["PronosticsPouleClassements_Classement"] . '</td>';
						echo '<td class="bordure-droite">' . $donnees["Equipes_Nom"] . '</td>';
						echo '<td class="bordure-droite">' . $donnees["PronosticsPouleStats_Points"] . '</td>';
						echo '<td class="bordure-droite">' . $donnees["PronosticsPouleStats_J"] . '</td>';
						echo '<td class="bordure-droite">' . $donnees["PronosticsPouleStats_G"] . '</td>';
						echo '<td class="bordure-droite">' . $donnees["PronosticsPouleStats_N"] . '</td>';
						echo '<td class="bordure-droite">' . $donnees["PronosticsPouleStats_P"] . '</td>';
						echo '<td class="bordure-droite">' . $donnees["PronosticsPouleStats_BP"] . '</td>';
						echo '<td class="bordure-droite">' . $donnees["PronosticsPouleStats_BC"] . '</td>';
						echo '<td>' . ($donnees["PronosticsPouleStats_Diff"] > 0 ? '+' : '') . $donnees["PronosticsPouleStats_Diff"] . '</td>';
					echo '</tr>';
				}
				$req->closeCursor();
				
				// Arrivé ici, on vérifie le nombre d'équipes ajoutées dans le classement
				// et l'on comble si le nombre n'a pas atteint 4
				for($j = $i; $j <= 4; $j++) {
					echo '<tr>';
						echo '<td class="bordure-droite">-</td>';
						echo '<td class="bordure-droite">-</td>';
						echo '<td class="bordure-droite">-</td>';
						echo '<td class="bordure-droite">-</td>';
						echo '<td class="bordure-droite">-</td>';
						echo '<td class="bordure-droite">-</td>';
						echo '<td class="bordure-droite">-</td>';
						echo '<td class="bordure-droite">-</td>';
						echo '<td class="bordure-droite">-</td>';
						echo '<td>-</td>';
					echo '</tr>';
				}
			echo '</tbody>';
		echo '</table>';
		
		echo '<br />';
		
		// Pour déterminer s'il faut afficher ou non le bouton de réglage des égalités, il est nécessaire d'effectuer une requête en base
		$ordreSQL =		'	SELECT		COUNT(*) AS NombreEgalites' .
						'	FROM		cdm_pronostics_poule_egalites' .
						'	WHERE		cdm_pronostics_poule_egalites.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'				AND		cdm_pronostics_poule_egalites.Poules_Poule = ' . $poule .
						'				AND		cdm_fn_nombre_matches_incomplets_poule(' . $_SESSION["pronostiqueur"] . ', ' . $poule . ') = 0';

		$req = $bdd->query($ordreSQL);
		$donnees = $req->fetchAll();
		
		if(sizeof($donnees))
			if($donnees[0]["NombreEgalites"] > 0)
				$style = 'visibility: visible';
			else
				$style = 'visibility: hidden;';
			
			echo '<label id="lblProblemeEgalites' . $poule . '" style="' . $style . '" class="boutonEgalites" onclick="creerProno_gererEgalites(' . $poule . ', \'divClassementPoule-' . $poule . '\', \'divTableau\', \'lblProblemeEgalites' . $poule . '\');">Gérer les égalités</label>';
	}

	function afficherClassementPouleAppelAjax($poule, $bdd) {
		afficherClassementPoule($poule, $bdd);
	}


	if($appelAjax == 1)
		afficherClassementPouleAppelAjax($poule, $bdd);
	
?>

<script>
	$(function() {
		// Transformation des boutons de gestion des égalités
		$('.boutonEgalites').button().click(	function(event) {
													event.preventDefault();
												}
											);
	});
	
</script>
