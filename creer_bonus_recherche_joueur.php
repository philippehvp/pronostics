<?php
	include_once('commun.php');
	
	// Recherche d'un joueur dont on a saisi le nom
	
	$critereRecherche = isset($_POST["critereRecherche"]) ? $_POST["critereRecherche"] : '';
	$nomZoneRecherche = isset($_POST["nomZoneRecherche"]) ? $_POST["nomZoneRecherche"] : '';
	$nomZoneId = isset($_POST["nomZoneId"]) ? $_POST["nomZoneId"] : '';
	$nomZoneNomJoueur = isset($_POST["nomZoneNomJoueur"]) ? $_POST["nomZoneNomJoueur"] : '';
	$critereRechercheSQL = str_replace('\'', '\\\'', $critereRecherche);
	
	
	$ordreSQL =		'	SELECT		joueurs.Joueur, CONCAT(joueurs.Joueurs_NomFamille, \' \', IFNULL(joueurs.Joueurs_Prenom, \'\')) AS Joueurs_NomComplet, joueurs_equipes.Equipes_Equipe, equipes.Equipes_Nom' .
					'	FROM		joueurs' .
					'	JOIN		joueurs_equipes' .
					'				ON		joueurs.Joueur = joueurs_equipes.Joueurs_Joueur' .
					'	JOIN		equipes' .
					'				ON		joueurs_equipes.Equipes_Equipe = equipes.Equipe' .
					'	JOIN		engagements' .
					'				ON		equipes.Equipe = engagements.Equipes_Equipe' .
					'	WHERE		(	Joueurs_NomFamille LIKE \'%' . $critereRechercheSQL . '%\'' .
					'					OR		Joueurs_Prenom LIKE \'%' . $critereRechercheSQL . '%\'' .
					'					OR		Joueurs_NomCourt LIKE \'%' . $critereRechercheSQL . '%\'' .
					'					OR		equipes.Equipes_Nom LIKE \'%' . $critereRechercheSQL . '%\'' .
					'				)' .
					'				AND		joueurs_equipes.JoueursEquipes_Fin IS NULL' .
					'				AND		engagements.Championnats_Championnat = 1' .
					'				AND		equipes.Equipes_L1Europe IS NULL' .
					'	LIMIT 50';

	$req = $bdd->query($ordreSQL);
	
	$premiereExecution = true;
	while($donnees = $req->fetch()) {
		if($premiereExecution == true) {
			$premiereExecution = false;
			echo '<table class="recherche">';
				/*echo '<thead>';
					echo '<th>Joueur</th>';
					echo '<th>Equipe</th>';
				echo '</thead>';*/
				echo '<tbody>';
		}
		echo '<tr onclick="creerBonus_selectionnerJoueur(' . $donnees["Joueur"] . ', \'' . str_replace('\'', '\\\'', $donnees["Joueurs_NomComplet"]) . '\', \'' . $nomZoneRecherche . '\', ' . '\'' . $nomZoneId . '\', \'' . $nomZoneNomJoueur . '\');">';
			$nomJoueurModifie = str_ireplace($critereRecherche, ('<b>' . $critereRecherche . '</b>'), $donnees["Joueurs_NomComplet"]);
			$nomEquipeModifie = str_ireplace($critereRecherche, ('<b>' . $critereRecherche . '</b>'), $donnees["Equipes_Nom"]);
			echo '<td>' . $nomJoueurModifie . '</td>';
			echo '<td>' . $nomEquipeModifie . '</td>';
		echo '</tr>';
	}
	$req->closeCursor();
	
	if($premiereExecution == false) {
			echo '</tbody>';
		echo '</table>';
	}
	else {
		echo '<table id="tableResultatRecherche">';
			echo '<tbody>';
				echo '<tr>';
					echo '<td>Aucun résultat pour <b>' . $critereRecherche . '</b></td>';
				echo '</tr>';
			echo '</tbody>';
		echo '</table>';
	}
	
	
	
?>