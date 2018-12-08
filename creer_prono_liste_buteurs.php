<?php
	include_once('commun.php');

	// Page de création de la liste des buteurs dans un match
	
	// Equipe concernée
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
	$type = isset($_POST["type"]) ? $_POST["type"] : '';

	// La page contient à gauche la liste des joueurs
	// et à droite la liste des buteurs
	$ordreSQL =		'	SELECT		joueurs_equipes.Joueurs_Joueur' .
							'				,CONCAT(joueurs.Joueurs_NomFamille, \' \', IFNULL(joueurs.Joueurs_Prenom, \'\')) AS Joueurs_NomComplet' .
							'				,joueurs.Postes_Poste' .
							'				,CASE' .
							'					WHEN	joueurs_cotes.JoueursCotes_Cote IS NULL' .
							'					THEN	NULL' .
							'					ELSE	fn_calculcotebuteur(JoueursCotes_Cote)' .
							'				END AS JoueursCotes_Cote' .
							'	FROM		joueurs' .
							'	JOIN		joueurs_equipes' .
							'				ON		joueurs.Joueur = joueurs_equipes.Joueurs_Joueur' .
							'	JOIN		matches' .
							'				ON		joueurs_equipes.Equipes_Equipe = ' . ($type == 'D' ? 'matches.Equipes_EquipeDomicile' : 'matches.Equipes_EquipeVisiteur') .
							'	LEFT JOIN	joueurs_cotes' .
							'				ON		matches.Match = joueurs_cotes.Matches_Match' .
							'						AND		joueurs.Joueur = joueurs_cotes.Joueurs_Joueur' .
							'						AND		joueurs_equipes.Equipes_Equipe = joueurs_cotes.Equipes_Equipe' .
							'	WHERE		JoueursEquipes_Debut <= matches.Matches_Date' .
							'				AND		(JoueursEquipes_Fin IS NULL OR JoueursEquipes_Fin > matches.Matches_Date)' .
							'				AND		joueurs_equipes.Equipes_Equipe = ' . $equipe .
							'				AND		matches.Match = ' . $match .
							'				AND		joueurs.Postes_Poste <> 1' .
							'	ORDER BY	joueurs.Postes_Poste DESC, joueurs_equipes.Joueurs_Joueur DESC';
	$req = $bdd->query($ordreSQL);
	$joueurs = $req->fetchAll();
	
	$ordreSQL =		'	SELECT		joueurs_equipes.Joueurs_Joueur' .
							'				,CONCAT(joueurs.Joueurs_NomFamille, \' \', IFNULL(joueurs.Joueurs_Prenom, \'\')) AS Joueurs_NomComplet' .
							'				,joueurs.Postes_Poste' .
							'				,CASE' .
							'					WHEN	joueurs_cotes.JoueursCotes_Cote IS NULL' .
							'					THEN	NULL' .
							'					ELSE	fn_calculcotebuteur(JoueursCotes_Cote)' .
							'				END AS JoueursCotes_Cote' .
							'	FROM		pronostics_buteurs' .
							'	JOIN		joueurs_equipes' .
							'				ON		pronostics_buteurs.Joueurs_Joueur = joueurs_equipes.Joueurs_Joueur' .
							'	JOIN		joueurs' .
							'				ON		joueurs_equipes.Joueurs_Joueur = joueurs.Joueur' .
							'	JOIN		matches' .
							'				ON		pronostics_buteurs.Matches_Match = matches.Match' .
							'	LEFT JOIN	joueurs_cotes' .
							'				ON		matches.Match = joueurs_cotes.Matches_Match' .
							'						AND		joueurs.Joueur = joueurs_cotes.Joueurs_Joueur' .
							'						AND		joueurs_equipes.Equipes_Equipe = joueurs_cotes.Equipes_Equipe' .
							'	WHERE		pronostics_buteurs.Matches_Match = ' . $match .
							'				AND		joueurs_equipes.Equipes_Equipe = ' . $equipe .
							'				AND		pronostics_buteurs.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
							'				AND		JoueursEquipes_Debut <= matches.Matches_Date' .
							'				AND		(JoueursEquipes_Fin IS NULL OR JoueursEquipes_Fin > matches.Matches_Date)' .
							'	ORDER BY	joueurs.Postes_Poste DESC, joueurs_equipes.Joueurs_Joueur';

	$req = $bdd->query($ordreSQL);
	$buteurs = $req->fetchAll();

	echo '<div>';
		echo '<div class="gauche">';
			//echo '<label>Effectif</label><br />';
			echo '<ul class="effectif-equipe effectif">';
				echo '<li class="titre-liste">Effectif de l\'équipe</li>';
				foreach($joueurs as $unJoueur) {
					switch($unJoueur["Postes_Poste"]) {
						case 1:		$classe = 'poste-gardien'; break;
						case 2:		$classe = 'poste-defenseur'; break;
						case 3:		$classe = 'poste-milieu'; break;
						case 4:		$classe = 'poste-attaquant'; break;
						default:	$classe = '';
					}
					if($unJoueur["JoueursCotes_Cote"] != 0)
						echo '<li class="buteur ' . $classe . '" value="' . $unJoueur["Joueurs_Joueur"] . '">' . $unJoueur["Joueurs_NomComplet"] . ' (' . $unJoueur["JoueursCotes_Cote"] .')</li>';
					else
						echo '<li class="buteur ' . $classe . '" value="' . $unJoueur["Joueurs_Joueur"] . '">' . $unJoueur["Joueurs_NomComplet"] . '</li>';
				}
			echo '</ul>';
		echo '</div>';
		
		echo '<div class="fleches-gauche-droite gauche"></div>';
		
		echo '<div class="gauche">';
			//echo '<label>Buteurs du match</label><br />';
			echo '<ul class="effectif-equipe pronostics-buteurs">';
				echo '<li class="titre-liste">Buteurs du match</li>';
				foreach($buteurs as $unButeur) {
					switch($unButeur["Postes_Poste"]) {
						case 1:		$classe = 'poste-gardien'; break;
						case 2:		$classe = 'poste-defenseur'; break;
						case 3:		$classe = 'poste-milieu'; break;
						case 4:		$classe = 'poste-attaquant'; break;
						default:	$classe = '';
					}
					if($unButeur["JoueursCotes_Cote"] != 0)
						echo '<li class="buteur ' . $classe . '" value="' . $unButeur["Joueurs_Joueur"] . '">' . $unButeur["Joueurs_NomComplet"] . ' (' . $unButeur["JoueursCotes_Cote"] . ')</li>';
					else
						echo '<li class="buteur ' . $classe . '" value="' . $unButeur["Joueurs_Joueur"] . '">' . $unButeur["Joueurs_NomComplet"] . '</li>';
				}
			echo '</ul>';
		echo '</div>';
	echo '</div>';

?>

	<script>
		$(function() {
			$('.fleches-gauche-droite').html('&rsaquo;<br />&lsaquo;');
		});
	</script>