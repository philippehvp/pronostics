<?php
	// Affichage de la liste des joueurs pour lesquels la recherche a été infructueuse
	// Cela concerne la lecture des cotes
	
	include_once('commun_administrateur.php');
	
	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$joueursInconnusEquipeDomicile = isset($_POST["joueursInconnusEquipeDomicile"]) ? $_POST["joueursInconnusEquipeDomicile"] : null;
	$joueursInconnusEquipeVisiteur = isset($_POST["joueursInconnusEquipeVisiteur"]) ? $_POST["joueursInconnusEquipeVisiteur"] : null;
	
	// Lecture de la liste des joueurs d'une équipe
	function lireListeJoueurs($match, $equipe, $date) {
		$ordreSQL =		'	SELECT		joueurs.Joueur' .
						'				,CASE' .
						'					WHEN	joueurs.Joueurs_Prenom IS NULL OR LENGTH(LTRIM(RTRIM(joueurs.Joueurs_Prenom))) = 0' .
						'					THEN	Joueurs_NomFamille' .
						'					ELSE	CONCAT(joueurs.Joueurs_Prenom, \' \', joueurs.Joueurs_NomFamille)' .
						'				END AS Joueurs_NomComplet' .
						'	FROM		joueurs' .
						'	JOIN		joueurs_equipes' .
						'				ON		joueurs.Joueur = joueurs_equipes.Joueurs_Joueur' .
						'	WHERE		joueurs_equipes.Equipes_Equipe = ' . $equipe .
						'				AND		JoueursEquipes_Debut <= \'' . $date . '\'' .
						'				AND		(JoueursEquipes_Fin IS NULL OR JoueursEquipes_Fin > \'' . $date . '\')' .
						'	ORDER BY	IFNULL(joueurs.Joueurs_Prenom, joueurs.Joueurs_NomFamille)';
		return $ordreSQL;
	}
	
	// Affichage de la liste des joueurs d'une équipe
	function afficherListeJoueurs($i, $equipe, $champPrenom, $champNom, $champCorrespondance, $listeJoueurs) {
		echo '<select id="selectListeJoueurs_' . $i . '_' . $equipe . '" onchange="creerMatch_lireJoueur(\'' . $i . '_' . $equipe . '\', \'' . $champPrenom . '\', \'' . $champNom . '\', \'' . $champCorrespondance . '\', 3);">';
			echo '<option value="0">Liste des joueurs</option>';
			foreach($listeJoueurs as $unJoueur) {
				echo '<option value="' . $unJoueur["Joueur"] . '">' . $unJoueur["Joueurs_NomComplet"] . '</option>';
			}
		echo '</select>';
	}
    
    // Affichage de la liste des joueurs inconnus
    function afficherListeJoueursInconnus($bdd, $match, $matchDate, $joueursInconnus, $equipe, $equipeNom) {
        // Lecture de la liste des joueurs des équipes
        $req = $bdd->query(lireListeJoueurs($match, $equipe, $matchDate));
        $joueursEquipe = $req->fetchAll();

        echo '<label>Joueurs inconnus de ' . $equipeNom . '</label>';
		echo '<table class="tableau--liste-joueurs">';
			echo '<thead>';
				echo '<tr>';
					echo '<th>Nom lu sur PMU</th>';
					echo '<th>Liste des joueurs</th>';
					echo '<th>Prénom</th>';
					echo '<th>Nom</th>';
					echo '<th>Nom de correspondance</th>';
					echo '<th>Actions sur le nom de correspondance</th>';
				echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
				$i = 0;
				foreach($joueursInconnus as $unJoueurInconnu) {
					echo '<tr>';
						echo '<td>' . $unJoueurInconnu["joueur"] . '</td>';
						echo '<td>';
							afficherListeJoueurs($i, $equipe, 'txtPrenom_' . $i . '_' . $equipe, 'txtNom_' . $i . '_' . $equipe, 'txtCorrespondance_' . $i . '_' . $equipe, $joueursEquipe);
						echo '</td>';
						echo '<td><input type="text" id="txtPrenom_' . $i . '_' . $equipe . '" onchange="creerMatch_modifierPrenomJoueur(this, \'' . $i . '_' . $equipe . '\');" /></td>';
						echo '<td><input type="text" id="txtNom_' . $i . '_' . $equipe . '" onchange="creerMatch_modifierNomJoueur(this, \'' . $i . '_' . $equipe . '\');" /></td>';
						echo '<td><input type="text" id="txtCorrespondance_' . $i . '_' . $equipe . '" /></td>';
						echo '<td><label class="bouton" onclick="creerMatch_copierNomCorrespondance(\'' . rawurlencode($unJoueurInconnu["joueur"]) . '\', \'' . $i . '_' . $equipe . '\', \'txtCorrespondance_' . $i . '_' . $equipe . '\', 3);">Copier</label> - <label class="bouton" onclick="creerMatch_supprimerNomCorrespondance(\'' . $i . '_' . $equipe . '\', \'txtCorrespondance_' . $i . '_' . $equipe . '\', 3);">Supprimer</label></td>';
					echo '</tr>';
					$i++;
				}
			echo '</tbody>';
		echo '</table>';

    }
	
	// Lecture des joueurs des deux équipes
	$ordreSQL =		'	SELECT		Matches_Date' .
                    '               ,equipes_domicile.Equipe AS EquipesDomicile_Equipe, equipes_visiteur.Equipe AS EquipesVisiteur_Equipe' .
                    '               ,equipes_domicile.Equipes_Nom AS EquipesDomicile_Nom, equipes_visiteur.Equipes_Nom AS EquipesVisiteur_Nom' .
					'	FROM		matches' .
                    '   JOIN        equipes as equipes_domicile' .
                    '               ON      matches.Equipes_EquipeDomicile = equipes_domicile.Equipe' .
                    '   JOIN        equipes as equipes_visiteur' .
                    '               ON      matches.Equipes_EquipeVisiteur = equipes_visiteur.Equipe' .
					'	WHERE		matches.Match = ' . $match;
	$req = $bdd->query($ordreSQL);
	$equipes = $req->fetchAll();
	
    // Deux cas sont possibles :
    // - dans les deux équipes, il y a au moins un joueur inconnu
    // - dans une seule des deux équipes il y a un ou plusieurs joueurs inconnus
    if(count($joueursInconnusEquipeDomicile) && count($joueursInconnusEquipeVisiteur)) {
        // Dans les deux équipes
        afficherListeJoueursInconnus($bdd, $match, $equipes[0]["Matches_Date"], $joueursInconnusEquipeDomicile, $equipes[0]["EquipesDomicile_Equipe"], $equipes[0]["EquipesDomicile_Nom"]);
        echo '<br /><br />';
        afficherListeJoueursInconnus($bdd, $match, $equipes[0]["Matches_Date"], $joueursInconnusEquipeVisiteur, $equipes[0]["EquipesVisiteur_Equipe"], $equipes[0]["EquipesVisiteur_Nom"]);
    }
    else {
        // Une seule des deux équipes comporte un ou plusieurs joueurs inconnus
        if(count($joueursInconnusEquipeDomicile))
            afficherListeJoueursInconnus($bdd, $match, $equipes[0]["Matches_Date"], $joueursInconnusEquipeDomicile, $equipes[0]["EquipesDomicile_Equipe"], $equipes[0]["EquipesDomicile_Nom"]);
        else
            afficherListeJoueursInconnus($bdd, $match, $equipes[0]["Matches_Date"], $joueursInconnusEquipeVisiteur, $equipes[0]["EquipesVisiteur_Equipe"], $equipes[0]["EquipesVisiteur_Nom"]);
    }
    
?>