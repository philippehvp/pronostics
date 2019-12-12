<?php
	// Affichage des buteurs d'un match d'une équipe
	include_once('commun.php');

	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;

	$ordreSQL = 'SET @@session.group_concat_max_len=2048';
	$req = $bdd->exec($ordreSQL);

	$ordreSQL =		'	SELECT		IF(Pronostics_Buts IS NULL, 0, COUNT(*)) AS Nombre_Pronostiqueurs' .
					'				,CONCAT(joueurs.Joueurs_NomFamille, \' \', IFNULL(joueurs.Joueurs_Prenom, \'\')) AS Joueurs_NomComplet' .
					'				,Matches_Buts' .
					'				,IFNULL(Pronostics_Buts, 0) AS Pronostics_Buts' .
					'				,IFNULL(GROUP_CONCAT(Pronostiqueurs_NomUtilisateur SEPARATOR \', \'), \'Aucun\') AS Pronostiqueurs_NomUtilisateur' .
					'				,IF(Pronostics_Buts = Matches_Buts, 1, 0) AS Pronostic_Exact' .
					'	FROM		(' .
					'					SELECT		Joueurs_Joueur, COUNT(*) AS Matches_Buts' .
					'					FROM		matches_buteurs' .
					'					WHERE		Matches_Match = ' . $match .
					'								AND		Buteurs_CSC = 0' .
					'								AND		Equipes_Equipe = ' . $equipe .
					'					GROUP BY	Joueurs_Joueur' .
					'				) matches_buteurs' .
					'	LEFT JOIN	(' .
					'					SELECT		Joueurs_Joueur' .
					'								,COUNT(*) AS Pronostics_Buts' .
					'								,Pronostiqueur' .
					'								,Pronostiqueurs_NomUtilisateur' .
					'					FROM		pronostics_buteurs' .
					'					JOIN		pronostiqueurs' .
					'								ON		Pronostiqueurs_Pronostiqueur = Pronostiqueur' .
					'					WHERE		Matches_Match = ' . $match .
					'					GROUP BY	Joueurs_Joueur, Pronostiqueurs_Pronostiqueur' .
					'				) pronostics_buteurs' .
					'				ON		matches_buteurs.Joueurs_Joueur = pronostics_buteurs.Joueurs_Joueur' .
					'	JOIN		joueurs' .
					'				ON		matches_buteurs.Joueurs_Joueur = joueurs.Joueur' .
					'	GROUP BY	matches_buteurs.Joueurs_Joueur, Matches_Buts, Pronostics_Buts' .
					'	ORDER BY	Joueurs_NomComplet DESC, Pronostic_Exact DESC';

	$req = $bdd->query($ordreSQL);
	$buteurs = $req->fetchAll();
	$nombreLignesButeurs = sizeof($buteurs);

	// Affichage des buteurs dans un tableau
	// Le nom du buteur ainsi que le nombre réel de buts marqués apparaît pour chaque ligne
	// Tant qu'on est sur le même buteur, c'est que des pronostiqueurs ont pensé qu'il marquait un nombre différent de buts
	if($nombreLignesButeurs > 0) {
		echo '<table class="tableau--classement tableau--classement-buteurs">';
			echo '<thead>';
				echo '<tr class="tableau--classement-nom-colonnes">';
					echo '<th class="aligne-gauche">Buteurs</th>';
					echo '<th>Marqués</th>';
					echo '<th>Pronostics</th>';
					echo '<th class="aligne-gauche">Pronostiqueurs</th>';
				echo '</tr>';
			echo '</thead>';
			$buteurPrecedent = '';
			foreach($buteurs as $uneLigneButeur) {
				echo '<tr class="aligne-haut">';
					if($buteurPrecedent != $uneLigneButeur["Joueurs_NomComplet"]) {
						echo '<td class="aligne-gauche">' . $uneLigneButeur["Joueurs_NomComplet"] . '</td>';
						echo '<td>' . $uneLigneButeur["Matches_Buts"] . '</td>';
						$buteurPrecedent = $uneLigneButeur["Joueurs_NomComplet"];
					}
					else {
						echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';
					}

					// Pronostics
					echo '<td>' . $uneLigneButeur["Pronostics_Buts"] . '</td>';
					if($uneLigneButeur["Nombre_Pronostiqueurs"] > 0)
						echo '<td class="aligne-gauche retour-ligne tableau--classement-buteurs--colonne-pronostiqueurs">' . $uneLigneButeur["Nombre_Pronostiqueurs"] . ' : ' . $uneLigneButeur["Pronostiqueurs_NomUtilisateur"] . '</td>';
					else
						echo '<td class="aligne-gauche retour-ligne tableau--classement-buteurs--colonne-pronostiqueurs">' . $uneLigneButeur["Pronostiqueurs_NomUtilisateur"] . '</td>';

				echo '</tr>';
			}
		echo '</table>';
	}
	else {
		echo '<label>Aucun buteur non CSC (contre son camp) pour ce match</label>';
	}
?>




