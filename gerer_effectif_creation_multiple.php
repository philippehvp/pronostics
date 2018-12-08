<?php
	include_once('commun_administrateur.php');
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
		$nomPage = 'gerer_effectif_creation_multiple.php';
		include_once('bandeau.php');
		
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
		
		// Page de création de multiples joueurs
	
		// Sélection des différents championnats
		$ordreSQL = 'SELECT DISTINCT Equipe, Equipes_Nom FROM equipes ORDER BY Equipes_Nom';
		$req = $bdd->query($ordreSQL);

		echo '<div id="divGererEffectifCreationMultiple" class="contenu-page">';
		
			echo '<div id="divJoueurs">';
				echo '<label>Nom de famille du joueur</label><br />';
				echo '<input type="text" style="width: 30em;" id="joueurNomFamille" /><br />';
				echo '<div class="recherche-joueurs" style="background-color: #666; position: absolute;"></div>';
				echo '<br />';
				
				echo '<label>Prénom du joueur</label><br />';
				echo '<input type="text" style="width: 30em;" id="joueurPrenom" /><br /><br />';

				echo '<div id="divCreationMultipleValider" class="bouton" style="margin-top: 10px;">';
					echo '<label id="labelCreerJoueur">Créer le joueur</label>';
				echo '</div>';
				
				echo '<br /><label>Poste</label><br />';
				echo '<select id="selectPostes" size="4">';
					echo '<option value="1">Gardien</option>';
					echo '<option value="2">Défenseur</option>';
					echo '<option value="3">Milieu</option>';
					echo '<option value="4">Attaquant</option>';
				echo '</select>';
			echo '</div>';

			echo '<br />';
			
			echo '<div>';
				echo '<label>Début de présence du joueur</label><br />';
				echo '<input class="date" id="dateDebutPresence" type="text" value="' . date('d/m/Y') . '" onchange=""/>';
			echo '</div>';
			
			echo '<br />';
			
			echo '<div id="divEquipes">';
				echo '<label class="gauche">Equipes</label><br />';
				echo '<select id="selectEquipes" size="30">';
					echo '<option value="-1" selected="selected">Equipes</option>';
					echo '<option value="0">Hors concours</option>';
					while($donnees = $req->fetch())
						echo '<option value="' . $donnees["Equipe"] . '">' . $donnees["Equipes_Nom"] . '</option>';
					$req->closeCursor();
				echo '</select>';
			echo '</div>';
		echo '</div>';
	?>

	<script>
		$(function() {
			afficherTitrePage('divGererEffectifCreationMultiple', 'CREATION MULTIPLE DE JOUEURS');

			$('.date').datepicker({dateFormat: 'dd/mm/yy'});
			
			$('#joueurNomFamille').keyup(function(event) {
									// Lecture de la taille de la zone de texte
									if($('#joueurNomFamille').val().length >= 3) {
										gererEffectif_rechercherJoueur($('#joueurNomFamille').val(), '.recherche-joueurs', 1);
										$('.recherche-joueurs').css({'display': 'block'});
									}
									else {
										$('.recherche-joueurs').css({'display': 'none'});
									}
									
								}
							);
			
			$('#joueurNomFamille').blur	(	function() {
												setTimeout(function() {
													$('.recherche-joueurs').css({'display': 'none'});
												}, 100);
											}
										);
										
			$('#joueurNomFamille').focus(function() {
												// Lecture de la taille de la zone de texte
												if($('#joueurNomFamille').val().length >= 3) {
													gererEffectif_rechercherJoueur($('#joueurNomFamille').val(), '.recherche-joueurs', 1);
													$('.recherche-joueurs').css({'display': 'block'});
												}
											}
										);
			
			
			$('#labelCreerJoueur').button().click(	function(event) {
				gererEffectif_creerJoueur('joueurNomFamille', 'joueurPrenom', 'selectPostes', 'dateDebutPresence', 'selectEquipes');
			});

		});
	</script>
</body>
</html>