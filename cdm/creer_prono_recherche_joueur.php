<?php
	include_once('commun.php');
	
	// Recherche d'un joueur dont on a saisi le nom
	
	$critereRecherche = isset($_POST["critereRecherche"]) ? $_POST["critereRecherche"] : '';
	$nomZoneRecherche = isset($_POST["nomZoneRecherche"]) ? $_POST["nomZoneRecherche"] : '';
	$nomZoneId = isset($_POST["nomZoneId"]) ? $_POST["nomZoneId"] : '';
	$nomZoneNomJoueur = isset($_POST["nomZoneNomJoueur"]) ? $_POST["nomZoneNomJoueur"] : '';
	$critereRechercheSQL = str_replace('\'', '\\\'', $critereRecherche);
	
	
	$ordreSQL =		'	SELECT		Joueur, Joueurs_Nom, Equipes_Equipe, Equipes_Nom' .
					'	FROM		cdm_joueurs' .
					'	JOIN		cdm_equipes' .
					'				ON		cdm_joueurs.Equipes_Equipe = cdm_equipes.Equipe' .
					'	WHERE		Joueurs_Nom LIKE \'%' . $critereRechercheSQL . '%\'' .
					'				OR		Equipes_Nom LIKE \'%' . $critereRechercheSQL . '%\'' .
					'	LIMIT 50';

	$req = $bdd->query($ordreSQL);
	
	$premiereExecution = true;
	while($donnees = $req->fetch()) {
		if($premiereExecution == true) {
			$premiereExecution = false;
			echo '<table id="tableResultatRecherche">';
				echo '<tbody>';
		}
		echo '<tr onclick="creerProno_selectionnerJoueur(' . $donnees["Joueur"] . ', \'' . str_replace('\'', '\\\'', $donnees["Joueurs_Nom"]) . '\', \'' . $nomZoneRecherche . '\', ' . '\'' . $nomZoneId . '\', \'' . $nomZoneNomJoueur . '\');">';
			$nomJoueurModifie = str_ireplace($critereRecherche, ('<b>' . $critereRecherche . '</b>'), $donnees["Joueurs_Nom"]);
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