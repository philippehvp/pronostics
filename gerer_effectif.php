<?php
	include('commun_administrateur.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include('commun_entete.php');
?>
</head>


<body>
	<?php
		$nomPage = 'gerer_effectif.php';
		include('bandeau.php');
		
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
		
		// Page de gestion de l'effectif (équipes et joueurs)
	
		// Sélection des différents championnats
		$ordreSQL = 'SELECT DISTINCT Equipe, IFNULL(Equipes_NomCourt, Equipes_Nom) AS Equipes_Nom FROM equipes ORDER BY Equipes_Nom';
		$req = $bdd->query($ordreSQL);
        $equipes = $req->fetchAll();

		echo '<div id="divGererEffectif" class="contenu-page">';
		
			echo '<div id="divJoueurs">';
				echo '<label>Nom du joueur ou de son équipe</label><br />';
				echo '<input type="text" id="critereRecherche" />';
				echo '<div class="recherche-joueurs" style="background-color: #666; position: absolute; z-index: 1000;"></div>';
				
				echo '';
			echo '</div>';
			
			echo '<br />';
			
			echo '<div id="divEquipes">';
				echo '<label>Equipes</label><br />';
				echo '<select id="selectEquipes" size="10">';
					echo '<option value="-1" selected="selected">Equipes</option>';
					echo '<option value="0">Hors concours</option>';
					foreach($equipes as $uneEquipe)
						echo '<option value="' . $uneEquipe["Equipe"] . '">' . $uneEquipe["Equipes_Nom"] . '</option>';
				echo '</select>';
			echo '</div>';
				
            echo '<br />';
			echo '<div id="divEffectif"></div>';
			echo '<div id="divTransfertJoueur"></div>';
		echo '</div>';

		

	?>

	<script>
		$(function() {
			afficherTitrePage('divGererEffectif', 'GESTION DE L\'EFFECTIF');
			$('#selectEquipes').click(	function() {	gererEffectif_afficherEffectif();	});
			
			$('#critereRecherche').keyup(function(event) {
												// Lecture de la taille de la zone de texte
												if($('#critereRecherche').val().length >= 3) {
													gererEffectif_rechercherJoueur($('#critereRecherche').val(), '.recherche-joueurs', 0);
													$('.recherche-joueurs').css({'display': 'block'});
												}
												else {
													$('.recherche-joueurs').css({'display': 'none'});
												}
												
											}
										);
			
			$('#critereRecherche').blur	(	function() {
												setTimeout(function() {
													$('.recherche-joueurs').css({'display': 'none'});
												}, 100);
											}
										);
										
			$('#critereRecherche').focus(function() {
												// Lecture de la taille de la zone de texte
												if($('#critereRecherche').val().length >= 3) {
													gererEffectif_rechercherJoueur($('#critereRecherche').val(), '.recherche-joueurs', 0);
													$('.recherche-joueurs').css({'display': 'block'});
												}
											}
										);
		});
	</script>
</body>
</html>