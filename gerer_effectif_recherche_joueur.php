<?php
	include('commun_administrateur.php');
	
	// Recherche d'un joueur dont on a saisi le nom
	
	$critereRecherche = isset($_POST["critereRecherche"]) ? $_POST["critereRecherche"] : '';
	$critereRechercheSQL = str_replace('\'', '\\\'', $critereRecherche);
	$modeRechercheSimple = isset($_POST["modeRechercheSimple"]) ? $_POST["modeRechercheSimple"] : 0;
	
	
	$ordreSQL =		'	SELECT		joueurs.Joueur, CONCAT(joueurs.Joueurs_NomFamille, \' \', IFNULL(joueurs.Joueurs_Prenom, \'\')) AS Joueurs_NomComplet, joueurs_equipes.Equipes_Equipe, equipes.Equipes_Nom' .
					'	FROM		joueurs' .
					'	LEFT JOIN	joueurs_equipes' .
					'				ON		joueurs.Joueur = joueurs_equipes.Joueurs_Joueur' .
					'	LEFT JOIN	equipes' .
					'				ON		joueurs_equipes.Equipes_Equipe = equipes.Equipe' .
					'	WHERE		(	(' .
					'						Joueurs_NomFamille LIKE \'%' . $critereRechercheSQL . '%\'' .
					'						OR		Joueurs_Prenom LIKE \'%' . $critereRechercheSQL . '%\'' .
					'						OR		Joueurs_NomCourt LIKE \'%' . $critereRechercheSQL . '%\'' .
					'					)' .
					'					AND		joueurs_equipes.JoueursEquipes_Fin IS NULL' .
					'				)' .
					'				OR	equipes.Equipes_Nom LIKE \'%' . $critereRechercheSQL . '%\'' .
					'	LIMIT 50';

	$req = $bdd->query($ordreSQL);
	$joueurs = $req->fetchAll();
	$nombreJoueurs = sizeof($joueurs);
	
	echo '<table class="classement" id="tableResultatRecherche">';
		if($nombreJoueurs == 0) {
			if($modeRechercheSimple == 0) {
				echo '<tbody>';
					echo '<tr onclick="gererEffectif_transfererJoueur(1, 0, \'' . $critereRechercheSQL . '\');">';
						echo '<td>Aucun résultat pour <b>' . $critereRecherche . '</b>. Voulez-vous créer le joueur ?</td>';
					echo '</tr>';
				echo '</tbody>';
			}
			else {
				echo '<tbody>';
					echo '<tr>';
						echo '<td>Aucun résultat pour <b>' . $critereRecherche . '</b></td>';
					echo '</tr>';
				echo '</tbody>';
			}
		}
		else {
			echo '<thead>';
				echo '<th class="aligne-gauche">Joueur(s)</th>';
				echo '<th class="aligne-gauche">Equipe(s)</th>';
			echo '</thead>';
			
			echo '<tbody>';
				foreach($joueurs as $unJoueur) {
					if($modeRechercheSimple == 0)		echo '<tr class="curseur-main" onclick="gererEffectif_transfererJoueur(0, ' . $unJoueur["Joueur"] . ', \'' . str_replace('\'', '\\\'', $unJoueur["Joueurs_NomComplet"]) . '\');">';
					else								echo '<tr>';
						$nomJoueurModifie = str_ireplace($critereRecherche, ('<b>' . $critereRecherche . '</b>'), $unJoueur["Joueurs_NomComplet"]);
						$nomEquipeModifie = str_ireplace($critereRecherche, ('<b>' . $critereRecherche . '</b>'), $unJoueur["Equipes_Nom"]);
						echo '<td class="aligne-gauche" style="padding-right: 2em;">' . $nomJoueurModifie . '</td>';
						echo '<td class="aligne-gauche" style="padding-right: 2em;">' . $nomEquipeModifie . '</td>';
					echo '</tr>';
				}
			echo '</tbody>';
		}
	echo '</table>';
	
?>