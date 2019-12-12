<?php
	include_once('commun_administrateur.php');

	// Affichage de l'effectif d'une équipe

	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;

	$ordreSQL =		'	SELECT		Joueur, IFNULL(Postes_Poste, 0) AS Postes_Poste' .
					'				,CONCAT(joueurs.Joueurs_NomFamille, \' \', IFNULL(joueurs.Joueurs_Prenom, \'\')) AS Joueurs_NomComplet' .
                    '               ,joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille, joueurs.Joueurs_Prenom' .
                    '               ,joueurs.Joueurs_NomCorrespondance, joueurs.Joueurs_NomCorrespondanceCote' .
					'				,DATE_FORMAT(JoueursEquipes_Debut, \'%d/%m/%Y\') AS JoueursEquipes_Debut' .
					'	FROM		joueurs' .
					'	JOIN		joueurs_equipes' .
					'				ON		joueurs.Joueur = joueurs_equipes.Joueurs_Joueur' .
					'	WHERE		Equipes_Equipe = ' . $equipe .
					'				AND		JoueursEquipes_Debut <= NOW()' .
					'				AND		(JoueursEquipes_Fin > NOW() OR JoueursEquipes_Fin IS NULL)' .
					'	ORDER BY	Postes_Poste, Joueurs_NomFamille';

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


	echo '<label>Effectif</label><br />';
	echo '<table class="tableau--classement">';
		echo '<thead>';
			echo '<th>Poste</th>';
			echo '<th>Identifiant</th>';
            echo '<th>Action</th>';
			echo '<th>Nom court</th>';
			echo '<th>Nom famille</th>';
            echo '<th>Prénom</th>';
            echo '<th>Correspondance</th>';
            echo '<th>Correspondance cote</th>';
			echo '<th>Début</th>';
		echo '</thead>';

		echo '<tbody>';
			foreach($joueurs as $unJoueur) {
				echo '<tr>';
					echo '<td>';
						echo '<select class="liste-postes" id="selectPostes_' . $unJoueur["Joueur"] . '" onchange="modifierCouleur(this, 0, \'blanc-fond-rouge\'); creerMatch_sauvegarderPostesJoueurs(' . $unJoueur["Joueur"] . ', \'selectPostes_' . $unJoueur["Joueur"] . '\');">';
							foreach($postes as $unPoste) {
								if($unJoueur["Postes_Poste"] == $unPoste["Poste"])
									echo '<option value="' . $unPoste["Poste"] .'" selected="selected">' . $unPoste["Postes_NomCourt"] . '</option>';
								else
									echo '<option value="' . $unPoste["Poste"] .'">' . $unPoste["Postes_NomCourt"] . '</option>';
							}
						echo '</select>';
					echo '</td>';
					echo '<td>' . $unJoueur["Joueur"] .'</td>';
                    echo '<td>';
                        echo '<label class="curseur-main" onclick="gererEffectif_transfererJoueur(0, ' . $unJoueur["Joueur"] . ', \'' . str_replace('\'', '\\\'', $unJoueur["Joueurs_NomComplet"]) . '\');">Transf.</label>&nbsp;/&nbsp;';
                        echo '<label class="curseur-main" onclick="gererEffectif_supprimerJoueur(' . $unJoueur["Joueur"] . ');">Suppr.</label>';
                    echo '</td>';
					echo '<td><input type="text" value="' . $unJoueur["Joueurs_NomCourt"] .'" onchange="gererEffectif_modifierJoueur(this, ' . $unJoueur["Joueur"] . ', 1);" /></td>';
					echo '<td><input type="text" value="' . $unJoueur["Joueurs_NomFamille"] .'" onchange="gererEffectif_modifierJoueur(this, ' . $unJoueur["Joueur"] . ', 2);" /></td>';
					echo '<td><input type="text" value="' . $unJoueur["Joueurs_Prenom"] .'" onchange="gererEffectif_modifierJoueur(this, ' . $unJoueur["Joueur"] . ', 3);" /></td>';
                    echo '<td><input type="text" value="' . $unJoueur["Joueurs_NomCorrespondance"] .'" onchange="gererEffectif_modifierJoueur(this, ' . $unJoueur["Joueur"] . ', 4);" /></td>';
                    echo '<td><input type="text" value="' . $unJoueur["Joueurs_NomCorrespondanceCote"] .'" onchange="gererEffectif_modifierJoueur(this, ' . $unJoueur["Joueur"] . ', 5);" /></td>';
					echo '<td><input type="text" value="' . $unJoueur["JoueursEquipes_Debut"] .'" /></td>';
				echo '</tr>';
			}
		echo '</tbody>';
	echo '</table>';
	$req->closeCursor();

?>

<script>
	$(function() {
		$('.liste-postes').each(function() {
			if($(this).find('option:selected').val() == 0)
				$(this).addClass('blanc-fond-rouge');
		});

	});


</script>