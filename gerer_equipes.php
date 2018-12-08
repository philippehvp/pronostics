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
		$nomPage = 'gerer_equipes.php';
		include_once('bandeau.php');
		
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
		
		// Page de gestion des équipes
	
		echo '<div id="divGererEquipes" class="contenu-page">';
			echo '<div id="divRecherche">';
				echo '<label>Filtre sur l\'équipe : </label>';
				echo '<input type="text" id="critereRecherche" />&nbsp;<label class="bouton" onclick="gererEquipe_creationEquipe();">Créer une nouvelle équipe</label>';
				
			echo '</div>';
			
			echo '<br />';
			
			echo '<div id="divEquipes" class="gauche">';
				include_once('gerer_equipes_liste_equipes.php');
			echo '</div>';

		echo '</div>';

		

	?>

	<script>
		$(function() {
			afficherTitrePage('divGererEquipes', 'GESTION DES EQUIPES');
			
			$('#critereRecherche').keyup(function(event) {
				// Lecture de la taille de la zone de texte
				gererEquipe_rechercherEquipe($('#critereRecherche').val(), 'divEquipes');
			});
		});
	</script>
</body>
</html>