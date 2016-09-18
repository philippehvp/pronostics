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
		$nomPage = 'pronostics_phase_finale.php';
		include('bandeau.php');
		
		echo '<div id="divVisuPronostics"></div>';
		echo '<div id="divVisuPronosticsPhaseFinale">';
			include('module_pronostics_phase_finale.php');
			echo '<div id="divLegende" class="colle-gauche"></div>';
		echo '</div>';
		
		
	?>
	
	<script>
		$(function() {
			afficherTitrePage('divVisuPronostics', 'Pronostics de la phase finale');
		});

	</script>
	
</body>
</html>