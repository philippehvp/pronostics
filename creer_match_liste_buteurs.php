<?php
	include_once('commun_administrateur.php');

	// Page de création de la liste des buteurs dans un match
	
	// Equipe concernée
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
	$date = isset($_POST["date"]) ? $_POST["date"] : '';
	$dateSQL = date('Y-m-d', strtotime(str_replace('/', '-', $date)));

	// La page contient à gauche la liste des participants
	// et à droite la liste des buteurs
	$ordreSQLParticipants =		'	SELECT			joueurs_equipes.Joueurs_Joueur, CONCAT(joueurs.Joueurs_NomFamille, \' \', IFNULL(joueurs.Joueurs_Prenom, \'\')) AS Joueurs_NomComplet, joueurs.Postes_Poste' .
								'	FROM			joueurs' .
								'	INNER JOIN		joueurs_equipes' .
								'					ON		joueurs.Joueur = joueurs_equipes.Joueurs_Joueur' .
								'	INNER JOIN		matches_participants' .
								'					ON		joueurs_equipes.Joueurs_Joueur = matches_participants.Joueurs_Joueur' .
								'	WHERE			matches_participants.Matches_Match = ' . $match .
								'					AND		JoueursEquipes_Debut <= \'' . $dateSQL . '\'' .
								'					AND		(JoueursEquipes_Fin IS NULL OR JoueursEquipes_Fin > \'' . $dateSQL . '\')' .
								'					AND		joueurs_equipes.Equipes_Equipe = ' . $equipe .
								//'					AND		Postes_Poste <> 1' .
								'	ORDER BY		joueurs.Postes_Poste DESC, joueurs_equipes.Joueurs_Joueur DESC';

	$reqParticipants = $bdd->query($ordreSQLParticipants);

	$ordreSQLButeurs =		'	SELECT			joueurs.Joueur, CONCAT(joueurs.Joueurs_NomFamille, \' \', IFNULL(joueurs.Joueurs_Prenom, \'\')) AS Joueurs_NomComplet, matches_buteurs.Buteurs_Cote, matches_buteurs.Buteurs_CSC, joueurs.Postes_Poste' .
							'	FROM			joueurs' .
							'	INNER JOIN		joueurs_equipes' .
							'					ON		joueurs.Joueur = joueurs_equipes.Joueurs_Joueur' .
							'	INNER JOIN		matches_buteurs' .
							'					ON		joueurs_equipes.Joueurs_Joueur = matches_buteurs.Joueurs_Joueur' .
							'	WHERE			matches_buteurs.Matches_Match = ' . $match .
							'					AND		JoueursEquipes_Debut <= \'' . $dateSQL . '\'' .
							'					AND		(JoueursEquipes_Fin IS NULL OR JoueursEquipes_Fin > \'' . $dateSQL . '\')' .
							'					AND		joueurs_equipes.Equipes_Equipe = ' . $equipe .
							'	ORDER BY		joueurs.Postes_Poste DESC, joueurs_equipes.Joueurs_Joueur DESC';

	$reqButeurs = $bdd->query($ordreSQLButeurs);

	echo '<div>';
		echo '<div class="gauche">';
			echo '<label>Participants</label><br />';
			echo '<ul class="effectif-equipe participants">';
				while($donneesParticipants = $reqParticipants->fetch()) {
					switch($donneesParticipants["Postes_Poste"]) {
						case 1:		$classe = 'poste-gardien'; break;
						case 2:		$classe = 'poste-defenseur'; break;
						case 3:		$classe = 'poste-milieu'; break;
						case 4:		$classe = 'poste-attaquant'; break;
						default:	$classe = '';
					}
					echo '<li class="' . $classe . '" value="' . $donneesParticipants["Joueurs_Joueur"] . '">' . $donneesParticipants["Joueurs_NomComplet"] . '</li>';
				}
			echo '</ul>';
		echo '</div>';
		
		echo '<div class="gauche" style="margin-left: 10px;">';
			echo '<label>Buteurs du match</label><br />';
			echo '<ul class="effectif-equipe buteurs">';
				while($donneesButeurs = $reqButeurs->fetch()) {
					switch($donneesButeurs["Postes_Poste"]) {
						case 1:		$classe = 'poste-gardien'; break;
						case 2:		$classe = 'poste-defenseur'; break;
						case 3:		$classe = 'poste-milieu'; break;
						case 4:		$classe = 'poste-attaquant'; break;
						default:	$classe = '';
					}
					if($donneesButeurs["Buteurs_CSC"] != 0)
						echo '<li class="' . $classe . '" value="' . $donneesButeurs["Joueur"] . '-1.' . $donneesButeurs["Buteurs_Cote"] . '">' . $donneesButeurs["Joueurs_NomComplet"] . '(CSC)</li>';
					else
						echo '<li class="' . $classe . '" value="' . $donneesButeurs["Joueur"] . '-0.' . $donneesButeurs["Buteurs_Cote"] . '">' . $donneesButeurs["Joueurs_NomComplet"] . '</li>';
						
				}
			echo '</ul>';
		echo '</div>';
	echo '</div>';

	$reqParticipants->closeCursor();
	$reqButeurs->closeCursor();
?>
