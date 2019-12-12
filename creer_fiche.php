<?php
	include_once('commun.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include_once('commun_entete.php');
?>
</head>

<body>
	<?php
		$nomPage = 'creer_fiche.php';
		include_once('bandeau.php');

		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';

		// Fiche d'identité d'un pronostiqueur

		// Lecture des données déjà saisies par le pronostiqueur
		$ordreSQL =		'	SELECT		IFNULL(Pronostiqueurs_Photo, \'_inconnu.png\') AS Pronostiqueurs_Photo, Pronostiqueurs_NomUtilisateur, Pronostiqueurs_Nom, Pronostiqueurs_Prenom, Pronostiqueurs_MEL' .
						'				,DATE_FORMAT(Pronostiqueurs_DateDeNaissance, \'%d/%m/%Y\') AS Pronostiqueurs_DateDeNaissance, Pronostiqueurs_LieuDeResidence' .
						'				,Pronostiqueurs_EquipeFavorite, Pronostiqueurs_Ambitions, Pronostiqueurs_Palmares, Pronostiqueurs_Carriere, Pronostiqueurs_Commentaire' .
						'	FROM		pronostiqueurs' .
						'	WHERE		Pronostiqueur = ' . $_SESSION["pronostiqueur"];

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

		echo '<div id="divFicheIdentite" class="contenu-page">';
			echo '<div class="fiche">';
				echo '<div class="photo gauche">';
					echo '<img src="images/pronostiqueurs/' . $pronostiqueurPhoto . '" alt="" /><br />';
					echo '<label>' . $pronostiqueurNomUtilisateur . '</label>';
				echo '</div>';
				echo '<div class="gauche">';
					echo '<label class="simple">Nom</label><input type="text" id="txtNom" value="' . $pronostiqueurNom . '" /><br />';
					echo '<label class="simple">Prénom</label><input type="text" id="txtPrenom" value="' . $pronostiqueurPrenom . '" /><br />';
					echo '<label class="simple">Adresse mail</label><input type="text" id="txtMEL" value="' . $pronostiqueurMEL . '" /><br />';
					echo '<label class="simple">Date de naissance</label><input type="text" id="txtDateDeNaissance" value="' . $pronostiqueurDateDeNaissance . '" class="date" readonly="true" /><br />';
					echo '<label class="simple">Lieu de résidence</label><input type="text" id="txtLieuDeResidence" value="' . $pronostiqueurLieuDeResidence . '" /><br />';
					echo '<label class="simple">Equipe favorite</label><input type="text" id="txtEquipeFavorite" value="' . $pronostiqueurEquipeFavorite . '" />';

				echo '</div>';
				echo '<div class="gauche">';
					echo '<label class="zone">Ambitions</label><textarea id="taAmbitions">' . $pronostiqueurAmbitions . '</textarea><br />';
					echo '<label class="zone">Commentaire</label><textarea id="taCommentaire">' . $pronostiqueurCommentaire . '</textarea><br />';
					echo '<label class="zone">Palmarès</label><textarea id="taPalmares" class="lecture-seule grandeZone" readonly="true">' . $pronostiqueurPalmares . '</textarea><br />';
					echo '<label class="zone">Carrière</label><textarea id="taCarriere" class="lecture-seule grandeZone" readonly="true">' . $pronostiqueurCarriere . '</textarea>';
				echo '</div>';
			echo '</div>';

			// Bouton de validation
			echo '<div class="fiche-valider">';
				echo '<label id="labelValiderFiche">Valider les modifications</label>';
			echo '</div>';

		echo '</div>';

	?>

	<script>
		$(function() {
			afficherTitrePage('divFicheIdentite', 'Modification de votre fiche d\'identité');

			$('.date').datepicker({dateFormat: 'dd/mm/yy', changeMonth: true, changeYear: true, yearRange: '-100:+0'	});

			<?php
				// A l'ouverture de la page, si le paramètre premiereconnexion est égal à 1, cela signifie que l'utilisateur est censé ouvrir cette page depuis la page de première connexion
				$premiereConnexion = isset($_GET["premiereconnexion"]) ? $_GET["premiereconnexion"] : 0;
				if($premiereConnexion == 1) {
			?>
					$('#labelValiderFiche').button().click(	function(event) {	creerFiche_validerFiche(1);	});
					afficherMessageInformationBandeau('Mot de passe a été modifié avec succès', 2000, '');
			<?php
				}
				else {
			?>
					$('#labelValiderFiche').button().click(	function(event) {	creerFiche_validerFiche(0);	});
			<?php
				}
			?>

		});
	</script>
</body>
</html>