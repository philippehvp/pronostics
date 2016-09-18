<?php
	include('commun.php');
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
		$nomPage = 'meilleur_buteur.php';
		include('bandeau.php');
		
		echo '<div id="divVisuMeilleurButeur">';
			include('module_meilleur_buteur.php');
		echo '</div>';
	?>
	
	<script>
		$(function() {
			afficherTitrePage('divVisuMeilleurButeur', 'Pronostics de meilleur buteur');
		});

	</script>
	
</body>
</html>