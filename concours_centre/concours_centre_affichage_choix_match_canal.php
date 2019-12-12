<?php
	include_once('../commun.php');

	// Lecture des paramètres passés à la page
	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;

	// Affichage du choix de match Canal de la journée
	$ordreSQL =     '   SELECT          pronostiqueurs.Pronostiqueur, pronostiqueurs.Pronostiqueurs_NomUtilisateur' .
					'					,equipes_domicile.Equipes_NomCourt AS EquipesDomicile_NomCourt, equipes_visiteur.Equipes_NomCourt AS EquipesVisiteur_NomCourt' .
					'					,IFNULL(scores.Scores_ScoreMatch, 0) +' .
					'					IFNULL(scores.Scores_ScoreButeur, 0) +' .
					'					IFNULL(scores.Scores_ScoreBonus, 0) AS Scores_MatchCanal' .
					'					,CASE' .
					'						WHEN		pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur IS NOT NULL' .
					'						THEN		1' .
					'						ELSE		0' .
					'					END AS Pronostiqueurs_Rival' .
                    '   FROM            pronostiqueurs' .
                    '   JOIN            journees_pronostiqueurs_canal' .
                    '                   ON      pronostiqueurs.Pronostiqueur = journees_pronostiqueurs_canal.Pronostiqueurs_Pronostiqueur' .
                    '   JOIN            matches' .
                    '                   ON      journees_pronostiqueurs_canal.Matches_Match = matches.Match' .
                    '   JOIN            equipes equipes_domicile' .
                    '                   ON      matches.Equipes_EquipeDomicile = equipes_domicile.Equipe' .
                    '   JOIN            equipes equipes_visiteur' .
					'                   ON      matches.Equipes_EquipeVisiteur = equipes_visiteur.Equipe' .
					'	JOIN			scores' .
					'					ON		pronostiqueurs.Pronostiqueur = scores.Pronostiqueurs_Pronostiqueur' .
					'							AND		journees_pronostiqueurs_canal.Matches_Match = scores.Matches_Match' .
					'	LEFT JOIN		pronostiqueurs_rivaux' .
					'					ON		pronostiqueurs_rivaux.Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'							AND		pronostiqueurs.Pronostiqueur = pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur' .
                    '   WHERE           journees_pronostiqueurs_canal.Journees_Journee = ' . $journee .
					'   ORDER BY        Scores_MatchCanal DESC';
    $req = $bdd->query($ordreSQL);
    $matchesCanal = $req->fetchAll();

    if(sizeof($matchesCanal) == 0) {
        echo 'Pas de résultat, anomalie';
        return;
	}

	echo '<div class="cc--points-par-equipe">';
		echo '<table class="cc--tableau">';
			echo '<thead>';
				echo '<tr>';
					echo '<th>Rang (indicatif)</th>';
					echo '<th class="pas-de-bordure-droite">Joueurs</th>';
					echo '<th class="pas-de-bordure-droite">Scores</th>';
					echo '<th>Match</th>';
				echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
				$i = 0;
				foreach($matchesCanal as $unMatchCanal) {
					if($unMatchCanal["Pronostiqueur"] == $_SESSION["pronostiqueur"])			echo '<tr class="surbrillance">';
					else if($unMatchCanal["Pronostiqueurs_Rival"] == 1)						echo '<tr class="rival">';
					else																		echo '<tr>';
						echo '<td></td>';
						echo '<td class="pas-de-bordure-droite">' . $unMatchCanal["Pronostiqueurs_NomUtilisateur"] . '</td>';
						echo '<td class="pas-de-bordure-droite">' . $unMatchCanal["Scores_MatchCanal"] . '</td>';
						echo '<td>' . $unMatchCanal["EquipesDomicile_NomCourt"] . ' - ' . $unMatchCanal["EquipesVisiteur_NomCourt"] . '</td>';
					echo '</tr>';

					$i++;
				}

			echo '</tbody>';
		echo '</table>';
	echo '</div>';
?>

<script>
	$(function() {
		// Gestion du clic sur un sous-onglet pour que celui-ci apparaisse avec un style de surbrillance / sélection
		$('.cc--nom-sous-onglet').click(function (e) {
			// Si cet onglet n'était pas sélectionné, alors effectuer deux tâches :
			// - enlever le style sélectionné à l'ancien onglet s'il existe
			// - mettre le style à l'onglet cliqué
			if(!$(this).hasClass('cc--selectionne')) {
				$('.cc--nom-sous-onglet').removeClass('cc--selectionne');
				$(this).addClass('cc--selectionne');
			}
		});

	});

</script>