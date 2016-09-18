<?php
	include('commun.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include('commun_entete.php');
	echo '<script src="js/jquery/jquery.ui.touch-punch.min.js"></script>';

	// Lecture des paramètres passés à la page
	$saison = isset($_GET["saison"]) ? $_GET["saison"] : 0;
?>
</head>

<body class="cdf">
	<?php
		$nomPage = 'cdf_prec.php';
		
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
		echo '<div class="conteneur">';
			include('bandeau.php');
			echo '<div class="confrontations"></div>';
			//include('pied.php');
		echo '</div>';
		
	?>
	<script>
		var critereRafraichissement = '';
		
		function rafraichirPage() {
			$.ajax(	{
				url: 'cdf_prec_affichage_confrontations.php',
				type: 'POST',
				data: 	{
							saison: <?php echo $saison; ?>
						}
			}).done(function(html) {
				$('.confrontations').empty().append(html);
			}).fail(function(html) { console.log('Fonction d\'affichage des confrontations : fail du deuxième appel Ajax'); });
		}
	
		$(function() {
			retournerHautPage();
			activitePronostiqueur();
			verificationMessage();

			// Premier affichage des confrontations forcé
			rafraichirPage();
		});
	
	</script>
	
</body>
</html>