<?php
	include_once('commun_administrateur.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include_once('commun_entete.php');
	echo '<script src="js/jquery/jquery.ui.touch-punch.min.js"></script>';
?>
</head>

<body class="cdf">
	<?php
		$nomPage = 'gerer_cdf.php';
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
		echo '<div class="conteneur">';
			include_once('bandeau.php');
			echo '<div class="confrontations"></div>';
			//include_once('pied.php');
		echo '</div>';
		
	?>
	<script>
		var critereRafraichissement = '';
		
		function rafraichirPage() {
			// On regarde ici s'il est nécessaire de rafraîchir la page (la première fois, c'est évidemment obligatoire)
			$.ajax(	{
						url: 'cdf_verification.php',
						type: 'POST',
						data:	{
									critereRafraichissement: critereRafraichissement
								},
						dataType: 'json'
					}
			).done(function(html) {
				if(html.rafraichir == '1') {
					critereRafraichissement = html.critereRafraichissement;
					$.ajax(	{
						url: 'gerer_cdf_affichage_confrontations.php',
						type: 'POST'
					}).done(function(html) {
						$('.confrontations').empty().append(html);
					}).fail(function(html) { console.log('Fonction d\'affichage des confrontations : fail du deuxième appel Ajax'); });
				}
				
			}).fail(function(html) { console.log('Fonction de vérification des confrontations : fail du premier appel Ajax'); });
		}
	
		$(function() {
			retournerHautPage();
			activitePronostiqueur();
			verificationMessage();

			// Premier affichage des confrontations forcé
			rafraichirPage();

			// Mise en place du timer de rafraîchissement
			var intervalle = setInterval(function() {
							rafraichirPage();
						}, 3000);
		});
	
	</script>
	
</body>
</html>