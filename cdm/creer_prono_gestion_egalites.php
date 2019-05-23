<?php
	include_once('commun.php');
	
	// Cette page affiche les différents cas d'égalité détectés dans une poule et permet de les régler en spécifiant les classements des équipes
	// à égalité par un tirage au sort (choix de l'utilisateur)

	// Lecture des paramètres passés à la page
	$poule = isset($_POST["poule"]) ? $_POST["poule"] : 0;
	
	// Nombre de cas d'égalités
	$ordreSQL =		'	SELECT		COUNT(*) AS NombreEgalites' .
					'	FROM		cdm_pronostics_poule_egalites' .
					'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"] .
					'				AND		Poules_Poule = ' . $poule .
					'	GROUP BY	Pronostiqueurs_Pronostiqueur, Poules_Poule';
	$req = $bdd->query($ordreSQL);
	
	$nombreEgalites = 0;
	while($donnees = $req->fetch()) {
		$nombreEgalites = $donnees["NombreEgalites"];
	}
	
	// Si un ou plusieurs cas d'égalité ont été trouvés
	if($nombreEgalites > 0) {
		if($nombreEgalites == 1)
			echo 'Un seul cas d\'égalité trouvé';
		else
			echo 'Deux cas d\'égalité ont été trouvés';
			
		echo '.<br />Cliquez sur une équipe et déplacez-la vers la gauche ou la droite pour choisir le classement final.';
		echo '<br /><br />';
		
		// Lecture des différents cas d'égalité
		$ordreSQL =		'	SELECT		PronosticsPouleEgalites_Points, PronosticsPouleEgalites_Diff, PronosticsPouleEgalites_BP, COUNT(*) AS NombreEquipes' .
						'	FROM		cdm_pronostics_poule_stats' .
						'	JOIN		(' .
						'					SELECT		PronosticsPouleEgalites_Points, PronosticsPouleEgalites_Diff, PronosticsPouleEgalites_BP, @curRow := @curRow + 1 AS CasEgalite' .
						'					FROM		cdm_pronostics_poule_egalites' .
						'					JOIN		(' .
						'									SELECT		@curRow := 0' .
						'								) r' .
						'					WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"] .
						'								AND		Poules_Poule = ' . $poule .
						'					ORDER BY	PronosticsPouleEgalites_Points DESC, PronosticsPouleEgalites_Diff DESC, PronosticsPouleEgalites_BP DESC' .
						'				) cdm_pronostics_poule_egalites' .
						'				ON		cdm_pronostics_poule_stats.PronosticsPouleStats_Points = cdm_pronostics_poule_egalites.PronosticsPouleEgalites_Points' .
						'						AND		cdm_pronostics_poule_stats.PronosticsPouleStats_Diff = cdm_pronostics_poule_egalites.PronosticsPouleEgalites_Diff' .
						'						AND		cdm_pronostics_poule_stats.PronosticsPouleStats_BP = cdm_pronostics_poule_egalites.PronosticsPouleEgalites_BP' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"] .
						'				AND		Poules_Poule = ' . $poule .
						'	GROUP BY	CasEgalite' .
						'	ORDER BY	CasEgalite';

		$req = $bdd->query($ordreSQL);
		$casEgalite = $req->fetchAll();
		
		if(sizeof($casEgalite)) {
			// Parcours des cas d'égalité
			for($i = 0; $i < $nombreEgalites; $i++) {
				// Pour un cas d'égalité donné, lecture des équipes concernées
				$ordreSQL =		'	SELECT		Equipe, Equipes_Nom, Equipes_Fanion' .
								'				,cdm_fn_classement_min	(	cdm_pronostics_poule_stats.Pronostiqueurs_Pronostiqueur' .
								'											,cdm_pronostics_poule_stats.Poules_Poule' .
								'											,PronosticsPouleStats_Points' .
								'											,PronosticsPouleStats_Diff' .
								'											,PronosticsPouleStats_BP' .
								'										) AS PronosticsPouleClassements_ClassementMin' .
								'	FROM		cdm_pronostics_poule_stats' .
								'	JOIN		cdm_equipes' .
								'				ON		cdm_pronostics_poule_stats.Equipes_Equipe = cdm_equipes.Equipe' .
								'	JOIN		cdm_pronostics_poule_classements' .
								'				ON		cdm_pronostics_poule_stats.Equipes_Equipe = cdm_pronostics_poule_classements.Equipes_Equipe' .
								'						AND		cdm_pronostics_poule_stats.Pronostiqueurs_Pronostiqueur = cdm_pronostics_poule_classements.Pronostiqueurs_Pronostiqueur' .
								'	WHERE		cdm_pronostics_poule_stats.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"] .
								'				AND		cdm_pronostics_poule_stats.Poules_Poule = ' . $poule .
								'				AND		PronosticsPouleStats_Points = ' . $casEgalite[$i]["PronosticsPouleEgalites_Points"] .
								'				AND		PronosticsPouleStats_Diff = ' . $casEgalite[$i]["PronosticsPouleEgalites_Diff"] .
								'				AND		PronosticsPouleStats_BP = ' . $casEgalite[$i]["PronosticsPouleEgalites_BP"];

				$req = $bdd->query($ordreSQL);
				
				if($nombreEgalites > 1)
					echo '<div class="colle-gauche gauche"><label>Egalité numéro ' . ($i + 1) . '</label></div>';

				echo '<div class="gauche">';
					echo '<ul id="ulEgalite' . $i . '" class="listeTriee">';
						while($donnees = $req->fetch()) {
							echo '<li data-val="' . $donnees["Equipe"] . '-' . $donnees["PronosticsPouleClassements_ClassementMin"] . '">' . $donnees["Equipes_Nom"] . '<br /><img src="images/equipes/' . $donnees["Equipes_Fanion"] . '" alt="" /></li>';
						}
					echo '</ul>';
					$req->closeCursor();
				echo '</div>';

			}
		}
	}
?>

<script>
		$(function() {
			$(function() {
				$('.listeTriee').sortable({axis: 'x'});
				$('.listeTriee').disableSelection();
			});
		});

	
</script>