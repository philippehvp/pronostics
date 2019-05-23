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
		$nomPage = 'pronostics_poule.php';
		include_once('bandeau.php');
		
		echo '<div id="divVisuPronostics"></div>';
		echo '<div id="divVisuPronosticsPoule">';
			include_once('module_pronostics_poule.php');
		echo '</div>';
	?>
	
	<script>
		$(function() {
			afficherTitrePage('divVisuPronostics', 'Pronostics des poules');
		});

	</script>
	
</body>
</html>