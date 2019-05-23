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
		$nomPage = 'creer_match_surveillance_direct.php';
		include_once('bandeau.php');

		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
		
		echo '<div id="divSurveillanceEntete" class="contenu-page"></div>';

		echo '<div id="divSurveillanceComposition" class="contenu-page">';
			include_once('taches/taches_lecture_liens_matches.php');
		echo '</div>';
		
		echo '<div id="divSurveillanceDirect" class="contenu-page">';
			include_once('taches/taches_lecture_direct.php');
		echo '</div>';
	
	?>

	<script>
		var compteur = 1;
	
		// Rappel de la page de surveillance du direct
		function surveillerDirect() {
			$.ajax(	{
						url: 'taches/taches_lecture_direct.php',
						type: 'POST',
						data:	{
									rafraichissement: 1
								}
			}).done(function(html) {
				var contenuActuel = $('#divSurveillanceDirect').html();
				if(contenuActuel.length > 0)
					$('#divSurveillanceDirect').html(html + '<br />' + contenuActuel);
				
			}).fail(function(html) {
				console.log('surveillerDirect : dans le fail');
			});
			
			if(compteur == 30) {
				compteur = 1;
				
				$.ajax(	{
							url: 'taches/taches_lecture_liens_matches.php',
							type: 'POST',
							data:	{
										rafraichissement: 1
									}
				}).done(function(html) {
				});
			}
			else {
				compteur++;
			}
					
		}
	
		$(function() {
			afficherTitrePage('divSurveillanceEntete', 'Match en direct : compo 5 minutes - direct 10 secondes');
			retournerHautPage();
			setInterval(function() { surveillerDirect(); }, 10000);
		});
	</script>
</body>
</html>