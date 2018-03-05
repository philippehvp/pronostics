<?php
	// Affichage de la liste des joueurs pour lesquels la recherche a été infructueuse
	
	include_once('commun_administrateur.php');
	include_once('creer_match_fonctions.php');
	
	// Lecture des paramètres passés à la page
	$joueurs = isset($_POST["joueurs"]) ? $_POST["joueurs"] : null;
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$origine = isset($_POST["origine"]) ? $_POST["origine"] : 0;
	
	// Si le paramètre origine vaut :
	// - 1 : on modifie la colonne NomCorrespondance
	// - 2 : on modifie la colonne NomCorrespondanceComplementaire
	// - 3 : on modifie la colonne NomCorrespondanceCote

	// Lecture de la liste des joueurs d'une équipe
	function lireListeJoueurs($match, $equipe, $date) {
		$ordreSQL =		'	SELECT		joueurs.Joueur' .
									'						,CASE' .
									'							WHEN	joueurs.Joueurs_Prenom IS NULL' .
									'							THEN	Joueurs_NomFamille' .
									'							ELSE	CONCAT(joueurs.Joueurs_Prenom, \' \', joueurs.Joueurs_NomFamille)' .
									'						END AS Joueurs_NomComplet' .
									'	FROM			joueurs' .
									'	JOIN			joueurs_equipes' .
									'						ON		joueurs.Joueur = joueurs_equipes.Joueurs_Joueur' .
									'	WHERE			joueurs_equipes.Equipes_Equipe = ' . $equipe .
									'						AND		JoueursEquipes_Debut <= \'' . $date . '\'' .
									'						AND		(JoueursEquipes_Fin IS NULL OR JoueursEquipes_Fin > \'' . $date . '\')' .
									'	ORDER BY	IFNULL(joueurs.Joueurs_Prenom, joueurs.Joueurs_NomFamille)';
		return $ordreSQL;
	}
	
	// Affichage de la liste des joueurs d'une équipe
	function afficherListeJoueurs($i, $nomEquipe, $champPrenom, $champNom, $champCorrespondance, $listeJoueurs, $origine) {
		// On affiche le nom de l'équipe dans la première ligne de la liste
		echo '<select style="font-size: 0.9em;" id="selectListeJoueurs_' . $i . '" onchange="creerMatch_lireJoueur(' . $i . ', \'' . $champPrenom . '\', \'' . $champNom . '\', \'' . $champCorrespondance . '\', ' . $origine . ');">';
			echo '<option value="0">' . $nomEquipe . '</option>';
			foreach($listeJoueurs as $unJoueur) {
				echo '<option value="' . $unJoueur["Joueur"] . '">' . $unJoueur["Joueurs_NomComplet"] . '</option>';
			}
		echo '</select>';
	}
	
	// Lecture des équipes du match
	$ordreSQL =		'	SELECT		Matches_Date, Equipes_EquipeDomicile, Equipes_EquipeVisiteur' .
								'						,equipes_domicile.Equipes_NomCourt AS EquipesDomicile_NomCourt, equipes_visiteur.Equipes_NomCourt AS EquipesVisiteur_NomCourt' .
								'	FROM			matches' .
								'	JOIN			equipes equipes_domicile' .
								'						ON		Equipes_EquipeDomicile = equipes_domicile.Equipe' .
								'	JOIN			equipes equipes_visiteur' .
								'						ON		Equipes_EquipeVisiteur = equipes_visiteur.Equipe' .
								'	WHERE			matches.Match = ' . $match;
	$req = $bdd->query($ordreSQL);
	$equipes = $req->fetchAll();
	
	$nomEquipeDomicile = $equipes[0]["EquipesDomicile_NomCourt"];
	$nomEquipeVisiteur = $equipes[0]["EquipesVisiteur_NomCourt"];
	
	// Lecture de la liste des joueurs des équipes
	$req = $bdd->query(lireListeJoueurs($match, $equipes[0]["Equipes_EquipeDomicile"], $equipes[0]["Matches_Date"]));
	$joueursDomicile = $req->fetchAll();
	$req = $bdd->query(lireListeJoueurs($match, $equipes[0]["Equipes_EquipeVisiteur"], $equipes[0]["Matches_Date"]));
	$joueursVisiteur = $req->fetchAll();
	
	if($joueurs != null) {
		echo '<table class="tableau--liste-joueurs">';
			echo '<thead>';
				echo '<tr>';
					echo '<th class="aligne-centre">?</th>';
					echo '<th>Nom extrait</th>';
					echo '<th>Liste des joueurs</th>';
					echo '<th>Prénom</th>';
					echo '<th>Nom</th>';
					echo '<th>Nom de correspondance et actions</th>';
					echo '<th>Création d\'un nouveau joueur</th>';
				echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
				$i = 0;
				foreach($joueurs as $unJoueur) {
					$nomEquipe = $unJoueur["equipe"] == $equipes[0]["Equipes_EquipeDomicile"] ? $nomEquipeDomicile : $nomEquipeVisiteur;
					$listeJoueurs = $unJoueur["equipe"] == $equipes[0]["Equipes_EquipeDomicile"] ? $joueursDomicile : $joueursVisiteur;
					echo '<tr>';
						echo '<td><label class="bouton" onclick="creerMatch_rechercherJoueur(' . $bdd->quote(rawurlencode($unJoueur["joueur"])) . ', ' . $bdd->quote($nomEquipe) . ');">?</label></td>';
						echo '<td>' . $unJoueur["joueur"] . '</td>';
						echo '<td>';
							afficherListeJoueurs($i, $nomEquipe, 'txtPrenom_' . $i, 'txtNom_' . $i, 'txtCorrespondance_' . $i, $listeJoueurs, $origine);
						echo '</td>';
						echo '<td><input style="width: 10em; font-size: 0.9em;" type="text" id="txtPrenom_' . $i . '" onchange="creerMatch_modifierPrenomJoueur(this, ' . $i . ');" /></td>';
						echo '<td><input style="width: 10em; font-size: 0.9em;" type="text" id="txtNom_' . $i . '" onchange="creerMatch_modifierNomJoueur(this, ' . $i . ');" /></td>';
						echo '<td>';
							echo '<input type=""text id="txtCorrespondance_' . $i . '" />';
							echo '<label style="margin-left: 5px;" class="bouton" onclick="creerMatch_copierNomCorrespondance(\'' . rawurlencode($unJoueur["joueur"]) . '\', ' . $i . ', \'txtCorrespondance_' . $i . '\', ' . $origine . ');">Copier</label>';
							echo '<label style="margin-left: 5px;" class="bouton" onclick="creerMatch_supprimerNomCorrespondance(' . $i . ', \'txtCorrespondance_' . $i . '\', ' . $origine . ');">Suppr.</label>';
						echo '</td>';

						echo '<td>';
							echo '<select id="selectPostes_' . $i . '">';
								echo '<option value="1">Gardien</option>';
								echo '<option value="2">Défenseur</option>';
								echo '<option value="3">Milieu</option>';
								echo '<option value="4">Attaquant</option>';
							echo '</select>';
						
							echo '<input style="margin-left: 5px;" class="date" id="dateDebutPresence_' . $i . '" type="text" value="' . date('d/m/Y') . '" />';
							echo '<label style="margin-left: 5px;" class="bouton" onclick="copierPrenomNom(\'' . $unJoueur["joueur"] . '\', \'txtPrenom_' . $i . '\', \'txtNom_' . $i . '\');">Copier</label>';
							echo '<label style="margin-left: 5px;" class="bouton" onclick="creerMatch_creerJoueur(\'txtPrenom_' . $i . '\', \'txtNom_' . $i . '\', \'txtCorrespondance_' . $i . '\', \'selectPostes_' . $i . '\', \'dateDebutPresence_' . $i . '\', ' . $unJoueur["equipe"] . ');">Créer</label>';
						echo '</td>';

							
							
					echo '</tr>';
					
					$i++;
				}
			echo '</tbody>';
		echo '</table>';
	}
?>

<script>
	// Fonction de copie du nom extrait du site externe vers les champs prénom et nom
	function copierPrenomNom(prenomNom, champPrenom, champNom) {
		// On suppose que le nom extrait est de la forme : Prénom Nom
		// On sépare donc le prénom du nom en supposant que la première partie soit le prénom
		// Si le nom extrait ne comporte pas de caractère espace, on suppose que la valeur lue est juste le nom de famille
		var mots = prenomNom.split(' ');
		if(mots.length == 1) {
			// Uniquement le prénom
			$('#' + champPrenom).val('');
			$('#' + champNom).val(mots[0]);
		}
		else if(mots.length >= 2) {
			$('#' + champPrenom).val(mots[0]);
			$('#' + champNom).val(prenomNom.substr(prenomNom.indexOf(' ') + 1));
		}
	}

	$(function() {
		$('.date').datepicker({dateFormat: 'dd/mm/yy'});
	});
	
</script>


