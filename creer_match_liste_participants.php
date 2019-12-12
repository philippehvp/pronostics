<?php
	include_once('commun_administrateur.php');

	// Page de création de la liste des joueurs ayant joué à un match

	// Equipe concernée
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
	$date = isset($_POST["date"]) ? $_POST["date"] : '';
	$dateSQL = date('Y-m-d', strtotime(str_replace('/', '-', $date)));

	// La page contient à gauche la liste des joueurs de l'équipe n'ayant pas joué
	// et à droite la liste de ceux qui ont joué
	$ordreSQL =	'	SELECT			joueurs_equipes.Joueurs_Joueur, CONCAT(joueurs.Joueurs_NomFamille, \' \', IFNULL(joueurs.Joueurs_Prenom, \'\')) AS Joueurs_NomComplet, joueurs.Postes_Poste' .
							'	FROM			joueurs' .
							'	INNER JOIN		joueurs_equipes' .
							'					ON		joueurs.Joueur = joueurs_equipes.Joueurs_Joueur' .
							'	LEFT JOIN		(SELECT Joueurs_Joueur FROM matches_participants WHERE Matches_Match = ' . $match . ') matches_participants' .
							'					ON		joueurs_equipes.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
							'	WHERE			JoueursEquipes_Debut <= \'' . $dateSQL . '\'' .
							'					AND		(JoueursEquipes_Fin IS NULL OR JoueursEquipes_Fin > \'' . $dateSQL . '\')' .
							'					AND		matches_participants.Joueurs_Joueur IS NULL' .
							'					AND		joueurs_equipes.Equipes_Equipe = ' . $equipe .
							'	ORDER BY		joueurs.Postes_Poste DESC, joueurs_equipes.Joueurs_Joueur DESC';

	$req = $bdd->query($ordreSQL);
	$joueursEffectif = $req->fetchAll();

	$ordreSQL =		'	SELECT			joueurs_equipes.Joueurs_Joueur, CONCAT(joueurs.Joueurs_NomFamille, \' \', IFNULL(joueurs.Joueurs_Prenom, \'\')) AS Joueurs_NomComplet, joueurs.Postes_Poste' .
								'	FROM			joueurs' .
								'	INNER JOIN		joueurs_equipes' .
								'					ON		joueurs.Joueur = joueurs_equipes.Joueurs_Joueur' .
								'	INNER JOIN		matches_participants' .
								'					ON		joueurs_equipes.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
								'	WHERE			matches_participants.Matches_Match = ' . $match .
								'					AND		JoueursEquipes_Debut <= \'' . $dateSQL . '\'' .
								'					AND		(JoueursEquipes_Fin IS NULL OR JoueursEquipes_Fin > \'' . $dateSQL . '\')' .
								'					AND		joueurs_equipes.Equipes_Equipe = ' . $equipe .
								'	ORDER BY		joueurs.Postes_Poste DESC, joueurs_equipes.Joueurs_Joueur DESC';
	$req = $bdd->query($ordreSQL);
	$participants = $req->fetchAll();

	echo '<div>';
		echo '<div class="gauche">';
			echo '<label>Effectif</label><br />';
			echo '<ul class="effectif-equipe effectif">';
				foreach($joueursEffectif as $unJoueur) {
					switch($unJoueur["Postes_Poste"]) {
						case 1:		$classe = 'poste-gardien'; break;
						case 2:		$classe = 'poste-defenseur'; break;
						case 3:		$classe = 'poste-milieu'; break;
						case 4:		$classe = 'poste-attaquant'; break;
						default:	$classe = '';
					}
					echo '<li class="' . $classe . '" value="' . $unJoueur["Joueurs_Joueur"] . '">' . $unJoueur["Joueurs_NomComplet"] . '</li>';
				}
			echo '</ul>';
		echo '</div>';

		echo '<div class="gauche" style="margin-left: 10px;">';
			echo '<label>Joueurs du match (' . count($participants) . ')</label><br />';
			echo '<ul class="effectif-equipe participants">';
				foreach($participants as $unParticipant) {
					switch($unParticipant["Postes_Poste"]) {
						case 1:		$classe = 'poste-gardien'; break;
						case 2:		$classe = 'poste-defenseur'; break;
						case 3:		$classe = 'poste-milieu'; break;
						case 4:		$classe = 'poste-attaquant'; break;
						default:	$classe = '';
					}
					echo '<li class="' . $classe . '" value="' . $unParticipant["Joueurs_Joueur"] . '">' . $unParticipant["Joueurs_NomComplet"] . '</li>';
				}
			echo '</ul>';
		echo '</div>';
	echo '</div>';
?>
