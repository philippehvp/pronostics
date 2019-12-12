<?php
	include_once('../commun.php');

	// Affichage des statistiques buteur pour un championnat

	// Lecture des paramètres passés à la page
	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;

	// Nombre de buteurs pronostiqués et trouvés
	$ordreSQL =		'	SELECT		pronostiqueurs.Pronostiqueur, Pronostiqueurs_NomUtilisateur' .
					'				,IFNULL(Nombre_Pronostics_Buteur, 0) AS Nombre_Pronostics_Buteur, IFNULL(Nombre_Buteurs_Trouves, 0) AS Nombre_Buteurs_Trouves, Nombre_Buteurs_Trouves / Nombre_Pronostics_Buteur * 100 AS Ratio_Buteur' .
					'				,CASE' .
					'					WHEN		pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur IS NOT NULL' .
					'					THEN		1' .
					'					ELSE		0' .
					'				END AS Pronostiqueurs_Rival' .
					'	FROM		pronostiqueurs' .
					'	JOIN		inscriptions' .
					'				ON		pronostiqueurs.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
					'	LEFT JOIN	(' .
					'					SELECT		pronostics_buteurs.Pronostiqueurs_Pronostiqueur, COUNT(*) AS Nombre_Pronostics_Buteur' .
					'					FROM		pronostics_buteurs' .
					'					JOIN		matches' .
					'								ON		pronostics_buteurs.Matches_Match = matches.Match' .
					'					JOIN		journees' .
					'								ON		matches.Journees_Journee = journees.Journee' .
					'					JOIN		matches_participants' .
					'								ON		matches.Match = matches_participants.Matches_Match' .
					'										AND		pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
					'					WHERE		journees.Championnats_Championnat = ' . $championnat .
					'					GROUP BY	pronostics_buteurs.Pronostiqueurs_Pronostiqueur' .
					'				) pronostics_buteur' .
					'				ON		pronostiqueurs.Pronostiqueur = pronostics_buteur.Pronostiqueurs_Pronostiqueur' .
					'	LEFT JOIN	(' .
					'					SELECT		pronostics_buteurs.Pronostiqueurs_Pronostiqueur, COUNT(*) AS Nombre_Buteurs_Trouves' .
					'					FROM		(' .
					'									SELECT		pronostics_buteurs.Pronostiqueurs_Pronostiqueur' .
					'												,pronostics_buteurs.Matches_Match' .
					'												,pronostics_buteurs.Joueurs_Joueur' .
					'												,pronostics_buteurs.Equipes_Equipe' .
					'												,CASE' .
					'													WHEN	@match = pronostics_buteurs.Matches_Match' .
					'															AND		@joueur = pronostics_buteurs.Joueurs_Joueur' .
					'															AND		@equipe = pronostics_buteurs.Equipes_Equipe' .
					'													THEN	@indicePronostics := @indicePronostics + 1' .
					'													ELSE	(@indicePronostics := 1) AND (@match := pronostics_buteurs.Matches_Match) AND (@joueur := pronostics_buteurs.Joueurs_Joueur) AND (@equipe := pronostics_buteurs.Equipes_Equipe)' .
					'												END AS Pronostics_Indice' .
					'									FROM		pronostics_buteurs' .
					'									JOIN		matches' .
					'												ON		pronostics_buteurs.Matches_Match = matches.Match' .
					'									JOIN		journees' .
					'												ON		matches.Journees_Journee = journees.Journee' .
					'									JOIN		(	SELECT		@indicePronostics := 0, @match := NULL, @joueur := NULL, @equipe := NULL	) r' .
					'									JOIN		matches_participants' .
					'												ON		matches.Match = matches_participants.Matches_Match' .
					'														AND		pronostics_buteurs.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
					'									WHERE		journees.Championnats_Championnat = ' . $championnat .
					'									ORDER BY	pronostics_buteurs.Pronostiqueurs_Pronostiqueur, pronostics_buteurs.Matches_Match, pronostics_buteurs.Joueurs_Joueur, pronostics_buteurs.Equipes_Equipe' .
					'								) pronostics_buteurs' .
					'					JOIN		(' .
					'									SELECT		Matches_Match' .
					'												,Joueurs_Joueur' .
					'												,Equipes_Equipe' .
					'												,CASE' .
					'													WHEN	@match = Matches_Match' .
					'															AND		@joueur = Joueurs_Joueur' .
					'															AND		@equipe = Equipes_Equipe' .
					'													THEN	@indiceMatches := @indiceMatches + 1' .
					'													ELSE	(@indiceMatches := 1) AND (@match := Matches_Match) AND (@joueur := Joueurs_Joueur) AND (@equipe := Equipes_Equipe)' .
					'												END AS Matches_Indice' .
					'									FROM		matches_buteurs' .
					'									JOIN		matches' .
					'												ON		matches_buteurs.Matches_Match = matches.Match' .
					'									JOIN		journees' .
					'												ON		matches.Journees_Journee = journees.Journee' .
					'									JOIN		(	SELECT		@indiceMatches := 0, @joueur := NULL, @equipe := NULL	) r' .
					'									WHERE		journees.Championnats_Championnat = ' . $championnat .
					'												AND		matches_buteurs.Buteurs_CSC = 0' .
					'									ORDER BY	Matches_Match, Joueurs_Joueur, Equipes_Equipe' .
					'								) matches_buteurs' .
					'								ON		pronostics_buteurs.Matches_Match = matches_buteurs.Matches_Match' .
					'										AND		pronostics_buteurs.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
					'										AND		pronostics_buteurs.Equipes_Equipe = matches_buteurs.Equipes_Equipe' .
					'										AND		pronostics_buteurs.Pronostics_Indice = matches_buteurs.Matches_Indice' .
					'					JOIN		matches' .
					'								ON		pronostics_buteurs.Matches_Match = matches.Match' .
					'					JOIN		journees' .
					'								ON		matches.Journees_Journee = journees.Journee' .
					'					WHERE		journees.Championnats_Championnat = ' . $championnat .
					'					GROUP BY	pronostics_buteurs.Pronostiqueurs_Pronostiqueur' .
					'				) buteurs_trouves' .
					'				ON		pronostics_buteur.Pronostiqueurs_Pronostiqueur = buteurs_trouves.Pronostiqueurs_Pronostiqueur' .
					'	LEFT JOIN	pronostiqueurs_rivaux' .
					'				ON		pronostiqueurs_rivaux.Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'						AND		pronostiqueurs.Pronostiqueur = pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur' .
					'	WHERE		inscriptions.Championnats_Championnat = ' . $championnat .
					'	ORDER BY	Ratio_Buteur DESC';

	$req = $bdd->query($ordreSQL);
	$pronosticsButeur = $req->fetchAll();



	if(sizeof($pronosticsButeur) > 0) {
		echo '<div class="cc--statistiques-buteur">';
			echo '<table class="cc--tableau">';
				echo '<thead>';
					echo '<tr>';
						echo '<th>Rang (indicatif)</th>';
						echo '<th>Joueurs</th>';
						echo '<th>Ratio</th>';
						echo '<th>Réalité</th>';
						echo '<th>Pronostics</th>';
					echo '</tr>';
				echo '<thead>';
				echo '<tbody>';
					foreach($pronosticsButeur as $unPronosticButeur) {
						if($unPronosticButeur["Pronostiqueur"] == $_SESSION["pronostiqueur"])			echo '<tr class="surbrillance">';
						else if($unPronosticButeur["Pronostiqueurs_Rival"] == 1)						echo '<tr class="rival">';
						else																			echo '<tr>';
							echo '<td></td>';
							echo '<td>' . $unPronosticButeur["Pronostiqueurs_NomUtilisateur"] . '</td>';
							echo '<td>' . number_format($unPronosticButeur["Ratio_Buteur"], 2) . '%</td>';
							echo '<td>' . $unPronosticButeur["Nombre_Buteurs_Trouves"] . '</td>';
							echo '<td>' . $unPronosticButeur["Nombre_Pronostics_Buteur"] . '</td>';
						echo '</tr>';

					}
				echo '</tbody>';
			echo '</table>';
		echo '</div>';
	}
	else {
		echo '<label>Aucune donnée à afficher</label>';
	}


?>
