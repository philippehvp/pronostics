<?php
	include('commun_administrateur.php');

	// Page de saisie des cotes des joueurs
	// La page affiche l'effectif avec les cotes, connues ou non
	
	// Equipe concernée
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$equipe = isset($_POST["numero_equipe"]) ? $_POST["numero_equipe"] : 0;
	$date = isset($_POST["date_debut_match"]) ? $_POST["date_debut_match"] : 0;
	$dateSQL = date('Y-m-d', strtotime(str_replace('/', '-', $date)));
	
	$ordreSQL =		'	SELECT		joueurs.Joueur, IFNULL(Postes_Poste, 0) AS Postes_Poste, Joueurs_NomFamille, Joueurs_Prenom, JoueursCotes_Cote' .
					'	FROM		joueurs' .
					'	JOIN		joueurs_equipes' .
					'				ON		joueurs.Joueur = joueurs_equipes.Joueurs_Joueur' .
					'						AND		joueurs_equipes.Equipes_Equipe = ' . $equipe .
					'	LEFT JOIN	joueurs_cotes' .
					'				ON		joueurs.Joueur = joueurs_cotes.Joueurs_Joueur' .
					'						AND		joueurs_cotes.Matches_Match = ' . $match .
					'						AND		joueurs_cotes.Equipes_Equipe = ' . $equipe .
					'	WHERE		JoueursEquipes_Debut <= \'' . $dateSQL . '\'' .
					'				AND		(JoueursEquipes_Fin IS NULL OR JoueursEquipes_Fin > \'' . $dateSQL . '\')' .
					'	ORDER BY	Postes_Poste DESC, Joueurs_NomFamille';
	
	$req = $bdd->query($ordreSQL);
	$joueurs = $req->fetchAll();
	
	// Postes différents
	$ordreSQL =		'	SELECT		Poste, Postes_NomCourt' .
					'	FROM		(' .
					'					SELECT		0 AS Poste, \'Non\' AS Postes_NomCourt' .
					'					UNION ALL' .
					'					SELECT		Poste, Postes_NomCourt' .
					'					FROM		postes' .
					'				) postes' .
					'	ORDER BY	Poste';
	$req = $bdd->query($ordreSQL);
	$postes = $req->fetchAll();
	
	// Pour les joueurs qui ne possèdent pas de cote, on fait la moyenne des cotes des joueurs qui en possèdent une
	// La moyenne se fait par poste (somme des cotes des attaquants, des milieux, etc.)
	$ordreSQL =		'	SELECT		GROUP_CONCAT(Cote_Moyenne SEPARATOR \' - \') AS Cote' .
					'	FROM		(' .
					'					SELECT		CONCAT(postes.Postes_NomCourt, \' : \', FLOOR(SUM(joueurs_cotes.JoueursCotes_Cote) / COUNT(*))) AS Cote_Moyenne' .
					'					FROM		joueurs_cotes' .
					'					JOIN		joueurs' .
					'								ON		joueurs_cotes.Joueurs_Joueur = joueurs.Joueur' .
					'					LEFT JOIN	postes' .
					'								ON		joueurs.Postes_Poste = postes.Poste' .
					'					WHERE		joueurs_cotes.JoueursCotes_Cote IS NOT NULL' .
					'								AND		postes.Poste <> 1' .
					'								AND		joueurs_cotes.Matches_Match = ' . $match .
					'								AND		joueurs_cotes.Equipes_Equipe = ' . $equipe .
					'					GROUP BY	joueurs.Postes_Poste' .
					'					ORDER BY	postes.Poste' .
					'				) cotes';
	$req = $bdd->query($ordreSQL);
	$cotesMoyennes = $req->fetchAll();
	
	if(sizeof($joueurs)) {
		echo '<label>Cote moyenne par poste : ' . $cotesMoyennes[0]["Cote"] . '</label>';
		echo '<table>';
			echo '<thead>';
				echo '<tr>';
					echo '<th style="text-align: left;">Postes</th>';
					echo '<th style="text-align: left;">Joueurs</th>';
					echo '<th style="text-align: left;">Cotes</th>';
				echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
				foreach($joueurs as $unJoueur) {
					if($unJoueur["Joueurs_Prenom"] != null)
						$nomAffiche = $unJoueur["Joueurs_NomFamille"] . ' ' . $unJoueur["Joueurs_Prenom"];
					else
						$nomAffiche = $unJoueur["Joueurs_NomFamille"];
					
					echo '<tr>';
						
						echo '<td>';
							echo '<select class="liste-postes" id="selectPostes_' . $unJoueur["Joueur"] . '" style="margin-right: 20px;" onchange="modifierCouleur(this, 0, \'blanc-fond-rouge\'); creerMatch_sauvegarderPostesJoueurs(' . $unJoueur["Joueur"] . ', \'selectPostes_' . $unJoueur["Joueur"] . '\');">';
								foreach($postes as $unPoste) {
									if($unJoueur["Postes_Poste"] == $unPoste["Poste"])
										echo '<option value="' . $unPoste["Poste"] .'" selected="selected">' . $unPoste["Postes_NomCourt"] . '</option>';
									else
										echo '<option value="' . $unPoste["Poste"] .'">' . $unPoste["Postes_NomCourt"] . '</option>';
								}
							echo '</select>';
						echo '</td>';
						echo '<td style="text-align: left;">' . $nomAffiche . '</td>';
						echo '<td style="text-align: left;"><input style="width: 50px;" type="text" id="txtCoteJoueur_' . $unJoueur["Joueur"] . '" value="' . $unJoueur["JoueursCotes_Cote"] . '" onchange="creerMatch_sauvegarderCotesJoueurs(' . $match . ', ' . $equipe . ', ' . $unJoueur["Joueur"] . ', \'txtCoteJoueur_' . $unJoueur["Joueur"] . '\');" /></td>';
					echo '</tr>';
				}
			echo '</tbody>';
		echo '</table>';
	}
	
?>


<script>
	$(function() {
		$('.button').button().click	(	function(event) {
									event.preventDefault();
			}
		);
		
		$('.liste-postes').each(function() {
			if($(this).find('option:selected').val() == 0)
				$(this).addClass('blanc-fond-rouge');
		});
		
	});
</script>
