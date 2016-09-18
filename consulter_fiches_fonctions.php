<?php

	// Fonction d'affichage de la fiche d'identité d'un pronostiqueur
	function consulterFiche($bdd, $pronostiqueurConsulte, $modeFenetre) {
		// Recherche des pronostiqueurs précédent et suivant
		// Le classement se fait par le nom utilisateur et non par le numéro de pronostiqueur
		
		$administrateur = isset($_SESSION["administrateur"]) ? $_SESSION["administrateur"] : 0;
		$ordreSQL =		'	SELECT		Pronostiqueurs_NomUtilisateur, IFNULL(Pronostiqueurs_Photo, \'_inconnu.png\') AS Pronostiqueurs_Photo, Pronostiqueurs_Nom, Pronostiqueurs_Prenom, Pronostiqueurs_MEL' .
						'				,DATE_FORMAT(Pronostiqueurs_DateDeNaissance, \'%d/%m/%Y\') AS Pronostiqueurs_DateDeNaissance, Pronostiqueurs_LieuDeResidence' .
						'				,Pronostiqueurs_EquipeFavorite, Pronostiqueurs_Ambitions, Pronostiqueurs_Palmares, Pronostiqueurs_Carriere, Pronostiqueurs_Commentaire' .
						'				,CASE' .
						'					WHEN	pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur IS NOT NULL' .
						'					THEN	1' .
						'					ELSE	0' .
						'				END AS Pronostiqueurs_Rival' .
						'	FROM		pronostiqueurs' .
						'	LEFT JOIN	pronostiqueurs_rivaux' .
						'				ON		pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur = ' . $pronostiqueurConsulte .
						'						AND		pronostiqueurs_rivaux.Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'	WHERE		pronostiqueurs.Pronostiqueur = ' . $pronostiqueurConsulte;

		$req = $bdd->query($ordreSQL);
		$fiche = $req->fetchAll();
		
		if(sizeof($fiche)) {
			$pronostiqueurPhoto = $fiche[0]["Pronostiqueurs_Photo"] != null ? $fiche[0]["Pronostiqueurs_Photo"] : '';
			$pronostiqueurNomUtilisateur = $fiche[0]["Pronostiqueurs_NomUtilisateur"] != null ? $fiche[0]["Pronostiqueurs_NomUtilisateur"] : '';
			$pronostiqueurNom = $fiche[0]["Pronostiqueurs_Nom"] != null ? $fiche[0]["Pronostiqueurs_Nom"] : '';
			$pronostiqueurPrenom = $fiche[0]["Pronostiqueurs_Prenom"] != null ? $fiche[0]["Pronostiqueurs_Prenom"] : '';
			$pronostiqueurMEL = $fiche[0]["Pronostiqueurs_MEL"] != null ? $fiche[0]["Pronostiqueurs_MEL"] : '';
			$pronostiqueurDateDeNaissance = $fiche[0]["Pronostiqueurs_DateDeNaissance"] != null ? $fiche[0]["Pronostiqueurs_DateDeNaissance"] : '';
			$pronostiqueurLieuDeResidence = $fiche[0]["Pronostiqueurs_LieuDeResidence"] != null ? $fiche[0]["Pronostiqueurs_LieuDeResidence"] : '';
			$pronostiqueurEquipeFavorite = $fiche[0]["Pronostiqueurs_EquipeFavorite"] != null ? $fiche[0]["Pronostiqueurs_EquipeFavorite"] : '';
			$pronostiqueurAmbitions = $fiche[0]["Pronostiqueurs_Ambitions"] != null ? $fiche[0]["Pronostiqueurs_Ambitions"] : '';
			$pronostiqueurPalmares = $fiche[0]["Pronostiqueurs_Palmares"] != null ? $fiche[0]["Pronostiqueurs_Palmares"] : '';
			$pronostiqueurCarriere = $fiche[0]["Pronostiqueurs_Carriere"] != null ? $fiche[0]["Pronostiqueurs_Carriere"] : '';
			$pronostiqueurCommentaire = $fiche[0]["Pronostiqueurs_Commentaire"] != null ? $fiche[0]["Pronostiqueurs_Commentaire"] : '';
			$pronostiqueurRival = $fiche[0]["Pronostiqueurs_Rival"] != null ? $fiche[0]["Pronostiqueurs_Rival"] : 0;
		}
		else {
			$pronostiqueurPhoto = '';
			$pronostiqueurNomUtilisateur = '';
			$pronostiqueurNom = '';
			$pronostiqueurPrenom = '';
			$pronostiqueurMEL = '';
			$pronostiqueurDateDeNaissance = '';
			$pronostiqueurLieuDeResidence = '';
			$pronostiqueurEquipeFavorite = '';
			$pronostiqueurAmbitions = '';
			$pronostiqueurPalmares = '';
			$pronostiqueurCarriere = '';
			$pronostiqueurCommentaire = '';
		}		

		// Pronostiqueurs précédent et suivant
		$ordreSQL =		'	SELECT		DISTINCT IFNULL(' .
						'					(' .
						'						SELECT		Pronostiqueur' .
						'						FROM		pronostiqueurs' .
						'						JOIN		(' .
						'										SELECT		MAX(Pronostiqueurs_NomUtilisateur) AS Pronostiqueurs_NomUtilisateur' .
						'										FROM		pronostiqueurs' .
						'										WHERE		Pronostiqueurs_NomUtilisateur < \'' . $pronostiqueurNomUtilisateur . '\'' .
						'									) pronostiqueurs_precedents' .
						'									ON		pronostiqueurs.Pronostiqueurs_NomUtilisateur = pronostiqueurs_precedents.Pronostiqueurs_NomUtilisateur' .
						'					)' .
						'					,(' .
						'						SELECT		Pronostiqueur' .
						'						FROM		pronostiqueurs' .
						'						JOIN		(' .
						'										SELECT		MAX(Pronostiqueurs_NomUtilisateur) AS Pronostiqueurs_NomUtilisateur' .
						'										FROM		pronostiqueurs' .
						'									) pronostiqueurs_max' .
						'									ON		pronostiqueurs.Pronostiqueurs_NomUtilisateur = pronostiqueurs_max.Pronostiqueurs_NomUtilisateur' .
						'					)' .
						'				) AS Pronostiqueur_Precedent' .
						'				,IFNULL(' .
						'					(' .
						'						SELECT		Pronostiqueur' .
						'						FROM		pronostiqueurs' .
						'						JOIN		(' .
						'										SELECT		MIN(Pronostiqueurs_NomUtilisateur) AS Pronostiqueurs_NomUtilisateur' .
						'										FROM		pronostiqueurs' .
						'										WHERE		Pronostiqueurs_NomUtilisateur > \'' . $pronostiqueurNomUtilisateur . '\'' .
						'									) pronostiqueurs_suivants' .
						'									ON		pronostiqueurs.Pronostiqueurs_NomUtilisateur = pronostiqueurs_suivants.Pronostiqueurs_NomUtilisateur' .
						'					)' .
						'					,(' .
						'						SELECT		Pronostiqueur' .
						'						FROM		pronostiqueurs' .
						'						JOIN		(' .
						'										SELECT		MIN(Pronostiqueurs_NomUtilisateur) AS Pronostiqueurs_NomUtilisateur' .
						'										FROM		pronostiqueurs' .
						'									) pronostiqueurs_min' .
						'									ON		pronostiqueurs.Pronostiqueurs_NomUtilisateur = pronostiqueurs_min.Pronostiqueurs_NomUtilisateur' .
						'					)' .
						'				) AS Pronostiqueur_Suivant' .
						'	FROM		pronostiqueurs';

		$req = $bdd->query($ordreSQL);
		$precedentSuivant = $req->fetchAll();
		$pronostiqueurPrecedent = $precedentSuivant[0]["Pronostiqueur_Precedent"];
		$pronostiqueurSuivant = $precedentSuivant[0]["Pronostiqueur_Suivant"];
		
		
		// Palmarès de l'année, toutes compétitions confondues
		$ordreSQL =		'	SELECT		GROUP_CONCAT(Trophee SEPARATOR \', \') AS Trophees' .
						'	FROM		(' .
						'					SELECT		CONCAT(Championnats_NomCourt, \' (\', COUNT(*), \')\') AS Trophee' .
						'					FROM		trophees' .
						'					JOIN		journees' .
						'								ON		trophees.Journees_Journee = journees.Journee' .
						'					JOIN		inscriptions' .
						'								ON		trophees.Pronostiqueurs_Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
						'										AND		journees.Championnats_Championnat = inscriptions.Championnats_Championnat' .
						'					JOIN		championnats' .
						'								ON		inscriptions.Championnats_Championnat = championnats.Championnat' .
						'					WHERE		trophees.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'								AND		Trophees_CodeTrophee = 1' .
						'					GROUP BY	inscriptions.Championnats_Championnat, Trophees_CodeTrophee' .
						'					ORDER BY	championnats.Championnat' .
						'				) trophees';
		$req = $bdd->query($ordreSQL);
		$tropheesPoulpeOr = $req->fetchAll();

		$ordreSQL =		'	SELECT		GROUP_CONCAT(Trophee SEPARATOR \', \') AS Trophees' .
						'	FROM		(' .
						'					SELECT		CONCAT(Championnats_NomCourt, \' (\', COUNT(*), \')\') AS Trophee' .
						'					FROM		trophees' .
						'					JOIN		journees' .
						'								ON		trophees.Journees_Journee = journees.Journee' .
						'					JOIN		inscriptions' .
						'								ON		trophees.Pronostiqueurs_Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
						'										AND		journees.Championnats_Championnat = inscriptions.Championnats_Championnat' .
						'					JOIN		championnats' .
						'								ON		inscriptions.Championnats_Championnat = championnats.Championnat' .
						'					WHERE		trophees.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'								AND		Trophees_CodeTrophee = 2' .
						'					GROUP BY	inscriptions.Championnats_Championnat, Trophees_CodeTrophee' .
						'					ORDER BY	championnats.Championnat' .
						'				) trophees';
		$req = $bdd->query($ordreSQL);
		$tropheesPoulpeArgent = $req->fetchAll();
		
		$ordreSQL =		'	SELECT		GROUP_CONCAT(Trophee SEPARATOR \', \') AS Trophees' .
						'	FROM		(' .
						'					SELECT		CONCAT(Championnats_NomCourt, \' (\', COUNT(*), \')\') AS Trophee' .
						'					FROM		trophees' .
						'					JOIN		journees' .
						'								ON		trophees.Journees_Journee = journees.Journee' .
						'					JOIN		inscriptions' .
						'								ON		trophees.Pronostiqueurs_Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
						'										AND		journees.Championnats_Championnat = inscriptions.Championnats_Championnat' .
						'					JOIN		championnats' .
						'								ON		inscriptions.Championnats_Championnat = championnats.Championnat' .
						'					WHERE		trophees.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'								AND		Trophees_CodeTrophee = 3' .
						'					GROUP BY	inscriptions.Championnats_Championnat, Trophees_CodeTrophee' .
						'					ORDER BY	championnats.Championnat' .
						'				) trophees';
		$req = $bdd->query($ordreSQL);
		$tropheesPoulpeBronze = $req->fetchAll();
		
		$ordreSQL =		'	SELECT		GROUP_CONCAT(Trophee SEPARATOR \', \') AS Trophees' .
						'	FROM		(' .
						'					SELECT		CONCAT(Championnats_NomCourt, \' (\', COUNT(*), \')\') AS Trophee' .
						'					FROM		trophees' .
						'					JOIN		journees' .
						'								ON		trophees.Journees_Journee = journees.Journee' .
						'					JOIN		inscriptions' .
						'								ON		trophees.Pronostiqueurs_Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
						'										AND		journees.Championnats_Championnat = inscriptions.Championnats_Championnat' .
						'					JOIN		championnats' .
						'								ON		inscriptions.Championnats_Championnat = championnats.Championnat' .
						'					WHERE		trophees.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'								AND		Trophees_CodeTrophee = 4' .
						'					GROUP BY	inscriptions.Championnats_Championnat, Trophees_CodeTrophee' .
						'					ORDER BY	championnats.Championnat' .
						'				) trophees';
		$req = $bdd->query($ordreSQL);
		$tropheesSoulierOr = $req->fetchAll();

		$ordreSQL =		'	SELECT		GROUP_CONCAT(Trophee SEPARATOR \', \') AS Trophees' .
						'	FROM		(' .
						'					SELECT		CONCAT(Championnats_NomCourt, \' (\', COUNT(*), \')\') AS Trophee' .
						'					FROM		trophees' .
						'					JOIN		journees' .
						'								ON		trophees.Journees_Journee = journees.Journee' .
						'					JOIN		inscriptions' .
						'								ON		trophees.Pronostiqueurs_Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
						'										AND		journees.Championnats_Championnat = inscriptions.Championnats_Championnat' .
						'					JOIN		championnats' .
						'								ON		inscriptions.Championnats_Championnat = championnats.Championnat' .
						'					WHERE		trophees.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'								AND		Trophees_CodeTrophee = 5' .
						'					GROUP BY	inscriptions.Championnats_Championnat, Trophees_CodeTrophee' .
						'					ORDER BY	championnats.Championnat' .
						'				) trophees';
		$req = $bdd->query($ordreSQL);
		$tropheesBrandao = $req->fetchAll();

		$ordreSQL =		'	SELECT		GROUP_CONCAT(Trophee SEPARATOR \', \') AS Trophees' .
						'	FROM		(' .
						'					SELECT		CONCAT(Championnats_NomCourt, \' (\', COUNT(*), \')\') AS Trophee' .
						'					FROM		trophees' .
						'					JOIN		journees' .
						'								ON		trophees.Journees_Journee = journees.Journee' .
						'					JOIN		inscriptions' .
						'								ON		trophees.Pronostiqueurs_Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
						'										AND		journees.Championnats_Championnat = inscriptions.Championnats_Championnat' .
						'					JOIN		championnats' .
						'								ON		inscriptions.Championnats_Championnat = championnats.Championnat' .
						'					WHERE		trophees.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'								AND		Trophees_CodeTrophee = 6' .
						'					GROUP BY	inscriptions.Championnats_Championnat, Trophees_CodeTrophee' .
						'					ORDER BY	championnats.Championnat' .
						'				) trophees';
		$req = $bdd->query($ordreSQL);
		$tropheesDjaDjedje = $req->fetchAll();

		$ordreSQL =		'	SELECT		GROUP_CONCAT(Trophee SEPARATOR \', \') AS Trophees' .
						'	FROM		(' .
						'					SELECT		CONCAT(Championnats_NomCourt, \' (\', COUNT(*), \')\') AS Trophee' .
						'					FROM		trophees' .
						'					JOIN		journees' .
						'								ON		trophees.Journees_Journee = journees.Journee' .
						'					JOIN		inscriptions' .
						'								ON		trophees.Pronostiqueurs_Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
						'										AND		journees.Championnats_Championnat = inscriptions.Championnats_Championnat' .
						'					JOIN		championnats' .
						'								ON		inscriptions.Championnats_Championnat = championnats.Championnat' .
						'					WHERE		trophees.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'								AND		Trophees_CodeTrophee = 7' .
						'					GROUP BY	inscriptions.Championnats_Championnat, Trophees_CodeTrophee' .
						'					ORDER BY	championnats.Championnat' .
						'				) trophees';
		$req = $bdd->query($ordreSQL);
		$tropheesRecordPoints = $req->fetchAll();

		$ordreSQL =		'	SELECT		GROUP_CONCAT(Trophee SEPARATOR \', \') AS Trophees' .
						'	FROM		(' .
						'					SELECT		CONCAT(Championnats_NomCourt, \' (\', COUNT(*), \')\') AS Trophee' .
						'					FROM		trophees' .
						'					JOIN		journees' .
						'								ON		trophees.Journees_Journee = journees.Journee' .
						'					JOIN		inscriptions' .
						'								ON		trophees.Pronostiqueurs_Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
						'										AND		journees.Championnats_Championnat = inscriptions.Championnats_Championnat' .
						'					JOIN		championnats' .
						'								ON		inscriptions.Championnats_Championnat = championnats.Championnat' .
						'					WHERE		trophees.Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte .
						'								AND		Trophees_CodeTrophee = 8' .
						'					GROUP BY	inscriptions.Championnats_Championnat, Trophees_CodeTrophee' .
						'					ORDER BY	championnats.Championnat' .
						'				) trophees';
		$req = $bdd->query($ordreSQL);
		$tropheesRecordPointsButeur = $req->fetchAll();
		
		echo '<div class="photo gauche">';
			echo '<img src="images/pronostiqueurs/' . $pronostiqueurPhoto . '" alt="" /><br />';
			
			if($administrateur)
				echo '<label class="bouton" onclick="consulterFiches_majPronostiqueur(' . $pronostiqueurConsulte . ');">Mettre à jour</label>';
			
			echo '<br /><br />';
			echo '<input type="hidden" name="pronostiqueurConsulte" value="' . $pronostiqueurConsulte . '">';
			echo '<label class="bouton-precedent curseur-main" onclick="consulterFiches_consulterFiche(' . $pronostiqueurPrecedent . ');">&nbsp;</label>';
			echo '&nbsp;';
			echo '<label class="bouton-suivant curseur-main" onclick="consulterFiches_consulterFiche(' . $pronostiqueurSuivant . ');">&nbsp;</label>';
			
			// Zone d'ajout aux rivaux
			echo '<br /><br />';
			if($pronostiqueurConsulte != $_SESSION["pronostiqueur"]) {
				echo '<input type="hidden" name="pronostiqueurConsulte" value="' . $pronostiqueurConsulte . '">';
				if($pronostiqueurRival == 1)
					echo '<input type="checkbox" name="ajoutRival" value="" checked="checked" /><label>Rival</label>';
				else
					echo '<input type="checkbox" name="ajoutRival" value="" /><label>Rival</label>';
			}
			
		echo '</div>';

		echo '<div class="gauche">';
			echo '<div class="colle-gauche gauche">';
				echo '<label class="simple">Nom utilisateur</label><input type="text" id="txtPrenom" value="' . $pronostiqueurNomUtilisateur . '" class="lecture-seule" readonly="true" /><br />';
				echo '<label class="simple">Nom</label><input type="text" id="txtPrenom" value="' . $pronostiqueurNom . '" class="lecture-seule" readonly="true" /><br />';
				echo '<label class="simple">Prénom</label><input type="text" id="txtPrenom" value="' . $pronostiqueurPrenom . '" class="lecture-seule" readonly="true" /><br />';
				echo '<label class="simple">Adresse mail</label><input type="text" id="txtMEL" value="' . $pronostiqueurMEL . '" class="lecture-seule" readonly="true" /><br />';
				echo '<label class="simple">Date de naissance</label><input type="text" id="txtDateDeNaissance" value="' . $pronostiqueurDateDeNaissance . '" class="lecture-seule" readonly="true" /><br />';
				echo '<label class="simple">Lieu de résidence</label><input type="text" id="txtLieuDeResidence" value="' . $pronostiqueurLieuDeResidence . '" class="lecture-seule" readonly="true" /><br />';
				echo '<label class="simple">Equipe favorite</label><input type="text" id="txtEquipeFavorite" value="' . $pronostiqueurEquipeFavorite . '" class="lecture-seule" readonly="true" />';
			echo '</div>';
			echo '<div class="gauche">';
				echo '<label class="zone">Ambitions</label><textarea id="taAmbitions" class="lecture-seule" readonly="true">' . $pronostiqueurAmbitions . '</textarea><br />';
				echo '<label class="zone">Commentaire</label><textarea id="taCommentaire" class="lecture-seule" readonly="true">' . $pronostiqueurCommentaire . '</textarea><br />';
				
				if($administrateur) {
					echo '<label class="zone">Palmarès</label><textarea id="taPalmares" class="grandeZone">' . $pronostiqueurPalmares . '</textarea><br />';
					echo '<label class="zone">Carrière</label><textarea id="taCarriere" class="grandeZone">' . $pronostiqueurCarriere . '</textarea>';
				}
				else {
					echo '<label class="zone">Palmarès</label><textarea id="taPalmares" class="lecture-seule grandeZone" readonly="true">' . $pronostiqueurPalmares . '</textarea><br />';
					echo '<label class="zone">Carrière</label><textarea id="taCarriere" class="lecture-seule grandeZone" readonly="true">' . $pronostiqueurCarriere . '</textarea>';
				}
			echo '</div>';
			
			echo '<div class="colle-gauche">';
				echo '<label class="simple">Poulpe d\'Or</label><input type="text" value="' . (sizeof($tropheesPoulpeOr) == 0 ? '-' : $tropheesPoulpeOr[0]["Trophees"]) . '" class="lecture-seule" readonly="true" />';
				echo '<label class="simple">Poulpe d\'Argent</label><input type="text" value="' . (sizeof($tropheesPoulpeArgent) == 0 ? '-' : $tropheesPoulpeArgent[0]["Trophees"]) . '" class="lecture-seule" readonly="true" /><br />';
				echo '<label class="simple">Poulpe de Bronze</label><input type="text" value="' . (sizeof($tropheesPoulpeBronze) == 0 ? '-' : $tropheesPoulpeBronze[0]["Trophees"]) . '" class="lecture-seule" readonly="true" />';
				echo '<label class="simple">Soulier d\'Or</label><input type="text" value="' . (sizeof($tropheesSoulierOr) == 0 ? '-' : $tropheesSoulierOr[0]["Trophees"]) . '" class="lecture-seule" readonly="true" /><br />';
				echo '<label class="simple">Brandao</label><input type="text" value="' . (sizeof($tropheesBrandao) == 0 ? '-' : $tropheesBrandao[0]["Trophees"]) . '" class="lecture-seule" readonly="true" />';
				echo '<label class="simple">Jérémy Morel</label><input type="text" value="' . (sizeof($tropheesDjaDjedje) == 0 ? '-' : $tropheesDjaDjedje[0]["Trophees"]) . '" class="lecture-seule" readonly="true" /><br />';
				echo '<label class="simple">Record de points</label><input type="text" value="' . (sizeof($tropheesRecordPoints) == 0 ? '-' : $tropheesRecordPoints[0]["Trophees"]) . '" class="lecture-seule" readonly="true" />';
				echo '<label class="simple">Record buteur</label><input type="text" value="' . (sizeof($tropheesRecordPointsButeur) == 0 ? '-' : $tropheesRecordPointsButeur[0]["Trophees"]) . '" class="lecture-seule" readonly="true" />';
			echo '</div>';
		echo '</div>';
	}
?>

<script>
	$(function() {
		$('input[type="checkbox"][name="ajoutRival"]').change(function() {
			var cochee = $(this).prop('checked') == true ? 1 : 0;
			
			consulterFiches_ajoutRival($('input[type="hidden"][name="pronostiqueurConsulte"]').val(), cochee);
		});
	});
</script>
